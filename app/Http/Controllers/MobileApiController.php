<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\MpesaStkPush;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\MpesaService;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class MobileApiController extends Controller
{
    /**
     * Generate unique transaction ID
     */
    private function generateUniqueTransactionId(): string
    {
        do {
            $transactionId = 'E-' . strtoupper(bin2hex(random_bytes(3)));
        } while (Transaction::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    /**
     * Calculate transaction fee
     */
    private function calculateTransactionFee(float $amount): float
    {
        $feePercentage = 0.01; // 1%
        return round($amount * $feePercentage, 2);
    }

    /**
     * Get transaction types for mobile app
     */
    public function getTransactionTypes(): JsonResponse
    {
        $types = [
            ['value' => 'ecommerce', 'label' => 'E-commerce Marketplace'],
            ['value' => 'services', 'label' => 'Professional Services'],
            ['value' => 'real-estate', 'label' => 'Real Estate'],
            ['value' => 'vehicle', 'label' => 'Vehicle Sales'],
            ['value' => 'business', 'label' => 'Business Transfers'],
            ['value' => 'freelance', 'label' => 'Freelance Work'],
            ['value' => 'goods', 'label' => 'High-Value Goods'],
            ['value' => 'construction', 'label' => 'Construction Projects'],
            ['value' => 'agriculture', 'label' => 'Agricultural Produce'],
            ['value' => 'legal', 'label' => 'Legal Settlements'],
            ['value' => 'import-export', 'label' => 'Import/Export'],
            ['value' => 'tenders', 'label' => 'Tender Payments'],
            ['value' => 'education', 'label' => 'Education Payments'],
            ['value' => 'personal', 'label' => 'Personal Loans'],
            ['value' => 'crypto', 'label' => 'Crypto Trading'],
            ['value' => 'rentals', 'label' => 'Equipment Rentals'],
            ['value' => 'charity', 'label' => 'Charity Donations'],
            ['value' => 'events', 'label' => 'Event Tickets'],
            ['value' => 'subscriptions', 'label' => 'Subscriptions'],
            ['value' => 'affiliate', 'label' => 'Affiliate Payments'],
            ['value' => 'other', 'label' => 'Other'],
        ];

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * Create a new transaction (Step 1)
     */
    public function createTransaction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|string',
            'transaction_amount' => 'required|numeric|min:1',
            'sender_mobile' => 'required|string|regex:/^\+?254[0-9]{9}$/',
            'receiver_mobile' => 'required|string|regex:/^\+?254[0-9]{9}$/',
            'transaction_details' => 'nullable|string|max:1000',
            'payment_method' => 'required|string|in:mpesa,paybill',
            'paybill_till_number' => 'required_if:payment_method,paybill|string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transactionId = $this->generateUniqueTransactionId();
            $validated = $validator->validated();

            $transaction = Transaction::create([
                'transaction_id' => $transactionId,
                'transaction_type' => $validated['transaction_type'],
                'transaction_amount' => $validated['transaction_amount'],
                'transaction_fee' => $this->calculateTransactionFee($validated['transaction_amount']),
                'sender_mobile' => $validated['sender_mobile'],
                'receiver_mobile' => $validated['receiver_mobile'],
                'transaction_details' => $validated['transaction_details'] ?? null,
                'payment_method' => $validated['payment_method'],
                'paybill_till_number' => $validated['paybill_till_number'] ?? null,
                'status' => 'pending',
            ]);

            Log::info('Mobile transaction created', [
                'transaction_id' => $transaction->transaction_id,
                'amount' => $transaction->transaction_amount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => [
                    'transaction_id' => $transaction->transaction_id,
                    'transaction_amount' => $transaction->transaction_amount,
                    'transaction_fee' => $transaction->transaction_fee,
                    'total_amount' => $transaction->transaction_amount + $transaction->transaction_fee,
                    'status' => $transaction->status,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Mobile transaction creation failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction. Please try again.'
            ], 500);
        }
    }

    /**
     * Initiate payment (Step 2)
     */
    public function initiatePayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string|exists:transactions,transaction_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid transaction ID',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transaction = Transaction::where('transaction_id', $request->transaction_id)->first();

            if ($transaction->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction is not in pending status'
                ], 400);
            }

            // Initialize M-Pesa STK Push
            $mpesa = new MpesaService();
            $mpesaResponse = $mpesa->stkPush($transaction);

            if ($mpesaResponse['success']) {
                $transaction->status = 'stk_initiated';
                $transaction->checkout_request_id = $mpesaResponse['data']['CheckoutRequestID'] ?? null;
                $transaction->merchant_request_id = $mpesaResponse['data']['MerchantRequestID'] ?? null;
                $transaction->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment request sent. Please check your phone for M-Pesa prompt.',
                    'data' => [
                        'checkout_request_id' => $transaction->checkout_request_id,
                        'status' => $transaction->status,
                    ]
                ]);
            } else {
                $transaction->status = 'stk_failed';
                $transaction->save();

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initiate payment. Please try again.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Mobile payment initiation failed', [
                'transaction_id' => $request->transaction_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Check payment status (Step 3)
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'checkout_request_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid checkout request ID',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $stkPush = MpesaStkPush::where('checkout_request_id', $request->checkout_request_id)->first();

            if (!$stkPush) {
                return response()->json([
                    'success' => false,
                    'status' => 'pending',
                    'message' => 'Payment still processing. Please wait...'
                ]);
            }

            if ($stkPush->status === 'Success') {
                $transaction = Transaction::where('checkout_request_id', $request->checkout_request_id)->first();
                
                if ($transaction) {
                    $transaction->status = 'Escrow Funded';
                    $transaction->save();

                    // Send SMS notifications
                    $smsService = new SmsService();
                    $smsService->send($transaction->sender_mobile, "Your transaction {$transaction->transaction_id} has been successfully funded.");
                    
                    if ($transaction->payment_method === 'mpesa') {
                        $smsService->send($transaction->receiver_mobile, "You have received a transaction {$transaction->transaction_id}, Amount: {$transaction->transaction_amount} that has been successfully funded.");
                    } else {
                        $smsService->send($transaction->receiver_mobile, "You have received a transaction {$transaction->transaction_id}, Amount: {$transaction->transaction_amount} that will be sent to Paybill/Till: {$transaction->paybill_till_number}.");
                    }
                }

                return response()->json([
                    'success' => true,
                    'status' => 'completed',
                    'message' => 'Payment successful! Funds are now in escrow.',
                    'data' => [
                        'transaction_id' => $transaction->transaction_id ?? null,
                        'status' => 'Escrow Funded',
                    ]
                ]);
            } else if ($stkPush->status === 'Failed' || $stkPush->status === 'Cancelled') {
                return response()->json([
                    'success' => false,
                    'status' => 'failed',
                    'message' => 'Payment failed or was cancelled. Please try again.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 'pending',
                    'message' => 'Payment still processing. Please wait...'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Mobile payment status check failed', [
                'checkout_request_id' => $request->checkout_request_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Error checking payment status. Please try again.'
            ], 500);
        }
    }

    /**
     * Get transaction details
     */
    public function getTransaction(Request $request, string $transactionId): JsonResponse
    {
        try {
            $transaction = Transaction::where('transaction_id', $transactionId)->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $transaction->transaction_id,
                    'transaction_type' => $transaction->transaction_type,
                    'transaction_amount' => $transaction->transaction_amount,
                    'transaction_fee' => $transaction->transaction_fee,
                    'total_amount' => $transaction->transaction_amount + $transaction->transaction_fee,
                    'sender_mobile' => $transaction->sender_mobile,
                    'receiver_mobile' => $transaction->receiver_mobile,
                    'transaction_details' => $transaction->transaction_details,
                    'payment_method' => $transaction->payment_method,
                    'paybill_till_number' => $transaction->paybill_till_number,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile get transaction failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transaction'
            ], 500);
        }
    }

    /**
     * Search transactions
     */
    public function searchTransaction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction ID is required',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transaction = Transaction::where('transaction_id', $request->transaction_id)->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $transaction->transaction_id,
                    'transaction_type' => $transaction->transaction_type,
                    'transaction_amount' => $transaction->transaction_amount,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Send OTP to phone number
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|regex:/^\+?254[0-9]{9}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $phoneNumber = $request->phone_number;
            
            // Normalize phone number (remove + if present, ensure 254 format)
            $phoneNumber = preg_replace('/^\+/', '', $phoneNumber);
            if (!str_starts_with($phoneNumber, '254')) {
                if (str_starts_with($phoneNumber, '0')) {
                    $phoneNumber = '254' . substr($phoneNumber, 1);
                } else {
                    $phoneNumber = '254' . $phoneNumber;
                }
            }

            // Create OTP
            Log::info('Creating OTP', ['phone' => $phoneNumber]);
            $otp = Otp::createForPhone($phoneNumber, 10); // 10 minutes expiry
            
            if (!$otp || !$otp->id) {
                Log::error('Failed to create OTP', ['phone' => $phoneNumber]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate OTP. Please try again.'
                ], 500);
            }
            
            Log::info('OTP created successfully', [
                'phone' => $phoneNumber,
                'otp_id' => $otp->id,
                'otp_code' => $otp->otp_code,
                'expires_at' => $otp->expires_at
            ]);

            // Send SMS via SmsService
            $smsService = new SmsService();
            $message = "Your eConfirm verification code is: {$otp->otp_code}. Valid for 10 minutes. Do not share this code.";
            
            $smsResult = $smsService->send($phoneNumber, $message);

            // Verify OTP was saved to database
            $savedOtp = Otp::find($otp->id);
            if (!$savedOtp) {
                Log::error('OTP was not saved to database', [
                    'phone' => $phoneNumber,
                    'otp_id' => $otp->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save OTP to database. Please try again.'
                ], 500);
            }

            if ($smsResult['status'] ?? false) {
                Log::info('OTP sent successfully', [
                    'phone' => $phoneNumber,
                    'otp_id' => $otp->id,
                    'otp_code' => $otp->otp_code
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to your phone number',
                    'data' => [
                        'expires_in' => 600, // 10 minutes in seconds
                        'otp_id' => $otp->id, // For debugging
                    ]
                ]);
            } else {
                Log::error('Failed to send OTP SMS', [
                    'phone' => $phoneNumber,
                    'sms_error' => $smsResult['message'] ?? 'Unknown error',
                    'otp_id' => $otp->id
                ]);

                // Still return success but log the SMS failure
                // In production, you might want to return an error
                return response()->json([
                    'success' => true,
                    'message' => 'OTP generated. Please check your phone for the code.',
                    'data' => [
                        'expires_in' => 600,
                        'otp_id' => $otp->id, // For debugging
                        'otp_code' => $otp->otp_code, // For development/testing only - remove in production
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Send OTP failed', [
                'phone' => $request->phone_number ?? 'unknown',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP and create/update user
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|regex:/^\+?254[0-9]{9}$/',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $phoneNumber = $request->phone_number;
            
            // Normalize phone number
            $phoneNumber = preg_replace('/^\+/', '', $phoneNumber);
            if (!str_starts_with($phoneNumber, '254')) {
                if (str_starts_with($phoneNumber, '0')) {
                    $phoneNumber = '254' . substr($phoneNumber, 1);
                } else {
                    $phoneNumber = '254' . $phoneNumber;
                }
            }

            $otpCode = $request->otp;

            // Verify OTP
            $otp = Otp::verify($phoneNumber, $otpCode);

            if (!$otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP. Please request a new one.'
                ], 400);
            }

            // Find or create user
            $user = User::where('phone', $phoneNumber)->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'phone' => $phoneNumber,
                    'name' => 'User', // Default name, can be updated later
                    'email' => null,
                    'password' => Hash::make(uniqid()), // Random password, user can reset later
                    'role' => 'user',
                    'type' => 0, // 0 = user
                ]);

                Log::info('New user created via OTP verification', [
                    'user_id' => $user->id,
                    'phone' => $phoneNumber
                ]);
            } else {
                Log::info('Existing user verified via OTP', [
                    'user_id' => $user->id,
                    'phone' => $phoneNumber
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Phone verified successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'email' => $user->email,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Verify OTP failed', [
                'phone' => $request->phone_number,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP. Please try again.'
            ], 500);
        }
    }
}


