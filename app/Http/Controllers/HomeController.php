<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ScamReport;
use App\Models\ScamReportLike;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\MpesaService;
use App\Models\MpesaStkPush;
use App\Services\SmsService;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        return view('front.welcome');
    }

    public function indexV2()
    {
        return view('front.welcome-v2');
    }

     //Legalities
    public function termsAndConditions()
    {
        return view('front.terms-and-conditions');
    }

    public function privacyPolicy()
    {
        return view('front.privacy-policy');
    }

    public function complience()
    {
        return view('front.complience');
    }
    public function security()
    {
        return view('front.security');
    }

    public function support()
    {
        return view('front.support');
    }

    public function help()
    {
        return view('front.help');
    }

    public function contact()
    {
        return view('front.contact');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // TODO: Send email or save to database
        // For now, just return success
        
        return response()->json([
            'success' => true,
            'message' => 'Thank you for contacting us! We will get back to you soon.'
        ]);
    }

    public function sitemap()
    {
        $urls = [
            [
                'loc' => url('/'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            [
                'loc' => route('scam.watch'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'daily',
                'priority' => '0.9'
            ],
            [
                'loc' => route('support'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ],
            [
                'loc' => route('help'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ],
            [
                'loc' => route('contact'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'loc' => route('terms.conditions'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'loc' => route('privacy.policy'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'loc' => route('security'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'loc' => route('complience'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
            [
                'loc' => route('api-documentation'),
                'lastmod' => now()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.6'
            ],
        ];

        return response()->view('front.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }

    public function scamWatch()
    {
        $reports = ScamReport::withCount('likes')
            ->whereIn('status', ['approved', 'pending'])
            ->orderBy('report_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('front.scam-watch', compact('reports'));
    }

    public function submitScamReport(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'report_type' => 'required|in:website,phone,email',
            'website' => 'required_if:report_type,website|nullable|string|max:255',
            'phone' => 'required_if:report_type,phone|nullable|string|max:20',
            'reported_email' => 'required_if:report_type,email|nullable|email|max:255',
            'category' => 'required|string',
            'description' => 'required|string|max:5000',
            'email' => 'nullable|email|max:255',
            'date_of_incident' => 'nullable|date',
        ]);

        // Check if this report already exists
        $existingReport = ScamReport::where('report_type', $validated['report_type'])
            ->where(function($query) use ($validated) {
                if ($validated['report_type'] === 'website') {
                    $query->where('website', $validated['website']);
                } elseif ($validated['report_type'] === 'phone') {
                    $query->where('phone', $validated['phone']);
                } elseif ($validated['report_type'] === 'email') {
                    $query->where('reported_email', $validated['reported_email']);
                }
            })
            ->first();

        if ($existingReport) {
            // Increment report count
            $existingReport->increment('report_count');
        } else {
            // Create new report - auto-approve for now (can add admin approval later)
            $validated['status'] = 'approved';
            ScamReport::create($validated);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Your report has been submitted successfully. It will be reviewed before being published.'
        ]);
    }

    public function likeScamReport($id)
    {
        $report = ScamReport::findOrFail($id);
        $ipAddress = request()->ip();
        
        // Check if user already liked this report
        $existingLike = ScamReportLike::where('scam_report_id', $id)
            ->where('ip_address', $ipAddress)
            ->first();
        
        if ($existingLike) {
            return response()->json([
                'success' => false,
                'message' => 'You have already liked this report.',
                'likes_count' => $report->likes()->count()
            ]);
        }
        
        // Create new like
        ScamReportLike::create([
            'scam_report_id' => $id,
            'ip_address' => $ipAddress,
            'user_agent' => request()->userAgent(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!',
            'likes_count' => $report->fresh()->likes()->count()
        ]);
    }

    //generateUniqueTransactionId
    private function generateUniqueTransactionId(): string
    {
        do {
            $transactionId = 'E-' . strtoupper(bin2hex(random_bytes(3))); // Generates a 6-character random string
        } while (Transaction::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
    //calculate tranaction fee calculateTransactionFee
    private function calculateTransactionFee(float $amount): float
    {
        // Example fee calculation: 1% of the transaction amount
        $feePercentage = 0.01; // 1%
        return round($amount * $feePercentage, 2);
    }
    public function submitTransaction(Request $request): JsonResponse
    {
        // Create code to generate unique transaction ID and check if it exists, if it exists, generate a new one the format is ESCROW-ENTRY-veryrandom5-digitnumber
        $transactionId = $this->generateUniqueTransactionId();

        // Validate input
        $validated = $request->validate([
            'transaction-type' => 'required|string',
            'transaction-amount' => 'required|numeric|min:1',
            'sender-mobile' => 'required|string',
            'receiver-mobile' => 'required|string',
            'transaction-details' => 'nullable|string',
            'payment-method' => 'required|string',
        ]);

        // Save transaction to database add transaction_id to the transaction
        $transaction = Transaction::create([
            'transaction_fee' => $this->calculateTransactionFee($validated['transaction-amount']),
            'transaction_id' => $transactionId,
            'payment_method' => $validated['payment-method'],
            'paybill_till_number' => $request['paybill-till-number'],
            'transaction_type' => $validated['transaction-type'],
            'transaction_amount' => $validated['transaction-amount'],
            'sender_mobile' => $validated['sender-mobile'],
            'receiver_mobile' => $validated['receiver-mobile'],
            'transaction_details' => $validated['transaction-details'] ?? null,
            'status' => 'pending',
        ]);

        //Create user to user table with some of this data
        
        // Log the transaction creation
        \Log::info('Transaction created', [
            'transaction_id' => $transaction->transaction_id,
            'transaction_fee' => $transaction->transaction_fee,
            'amount' => $transaction->transaction_amount,
            'sender_mobile' => $transaction->sender_mobile,
            'receiver_mobile' => $transaction->receiver_mobile,
            'status' => $transaction->status,
        ]);
        //i want to save the chackout_request_id and merchant_request_id to the transaction from mpesa service
        $transaction->checkout_request_id = null; // Initialize as null
        $transaction->merchant_request_id = null; // Initialize as null
        $transaction->save();

    
        // Use MpesaService for STK push
        $mpesa = new MpesaService();
        $mpesaResponse = $mpesa->stkPush($transaction);
        // dd($mpesaResponse); // Debugging line, remove in production

        if ($mpesaResponse['success']) {
            $transaction->status = 'stk_initiated';
            $transaction->checkout_request_id = $mpesaResponse['data']['CheckoutRequestID'] ?? null;
            $transaction->merchant_request_id = $mpesaResponse['data']['MerchantRequestID'] ?? null;
            $transaction->save();
            return response()->json([
                'success' => true,
                'message' => 'Transaction submitted and STK push initiated! Check your phone for pin confirmation.',
                'CheckoutRequestID' => $mpesaResponse['data']['CheckoutRequestID'] ?? null,
            ]);
        } else {
            $transaction->status = 'stk_failed';
            $transaction->save();
            return response()->json([
                'success' => false,
                'message' => 'Transaction saved, but STK push failed.'
            ]);
        }
    }

     public function transactionStatus($id)
    {
        $StkPush = MpesaStkPush::where('checkout_request_id', $id)->first();
        if ($StkPush && $StkPush->status === 'Success') {
            $transaction = Transaction::where('checkout_request_id', $id)->first();
            if ($transaction) {
                $transaction->status = 'Escrow Funded';
                $transaction->save();
            }
            //send sms to the sender and receiver
            $smsService = new SmsService();
            $smsService->send($transaction->sender_mobile, "Your transaction with ID {$transaction->transaction_id} has been successfully funded.");
            //if payment_method is mpesa, send sms to the receiver else send to receiver and include that the transaction will be sent to paybill/till paybill_till_number
            if ($transaction->payment_method === 'mpesa') {
                $smsService->send($transaction->receiver_mobile, "You have received a transaction with ID {$transaction->transaction_id}, Amount: {$transaction->transaction_amount} that has been successfully funded.");
            } else {
                $smsService->send($transaction->receiver_mobile, "You have received a transaction with ID {$transaction->transaction_id}, Amount: {$transaction->transaction_amount} that will be sent to Paybill/Till Number: {$transaction->paybill_till_number}.");
            }
            return response()->json([
                'success' => true,
                'message' => 'Transaction successful.',
                'transaction_id' => $transaction->transaction_id ?? null,
                'status' => $StkPush->status,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found or not successful.',
            ]);
        }
    }

    //createOTP
    public function createOTP(Request $request)
    {
        $transaction = Transaction::where('transaction_id', $request->input('transaction_id'))->first();
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ]);
        }

        // Generate a random OTP
        $otp = rand(100000, 999999);

        // Save the OTP to the transaction
        $transaction->otp = $otp;
        $transaction->save();

        // Send the OTP to the user via SMS
        $smsService = new SmsService();
        $smsService->send($transaction->sender_mobile, "Your OTP is: $otp, Do not share this OTP with anyone. It is valid for 3 minutes.");

        return response()->json([
            'success' => true,
            'message' => 'OTP created and sent via SMS.',
        ]);
    }

    //approveTransaction(id)
    public function approveTransaction(Request $request, $id)
    {
        //Check if it maches with OTP 
        $transaction = Transaction::where('transaction_id', $id)->first();
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ]);
        }
        $stkPush = MpesaStkPush::where('checkout_request_id', $transaction->checkout_request_id)->first();
       //Return a view to approve the transaction
        return view('process.approve-transaction', compact('transaction', 'stkPush'));
    }


    public function approveTransactionPost(Request $request, $id)
    {
        //Validate OTP
        $ValidateOTP = Transaction::where('id', $id)->where('otp', $request->otp)->first();
        if (!$ValidateOTP) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ]);
        }else{
            //Check if OTP is expired
            $otpExpiryTime = 3; // in minutes
            $otpCreatedAt = Carbon::parse($ValidateOTP->updated_at);
            $otpExpiryAt = $otpCreatedAt->addMinutes($otpExpiryTime);

            if (now()->greaterThan($otpExpiryAt)) {
                \Log::info('OTP expired', [
                    'transaction_id' => $ValidateOTP->transaction_id,
                    'otp_created_at' => $otpCreatedAt,
                    'current_time' => now(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new OTP.',
                ]);
            }

        }
        //check if its mpesa or paybill
        if($ValidateOTP->payment_method === 'paybill'){
            //mpesa b2b
            $mpesa = new MpesaService();
            $b2bResponse = $mpesa->b2b($ValidateOTP);
            if ($b2bResponse['success']) {
                //Update the transaction status to approved
                $ValidateOTP->status = 'Completed';
                $ValidateOTP->save();
                //send sms to the sender and receiver
                $smsService = new SmsService();
                $smsService->send($ValidateOTP->sender_mobile, "Your transaction with ID {$ValidateOTP->transaction_id} has been approved for settlement.");
                $smsService->send($ValidateOTP->receiver_mobile, "Your Payment with ID {$ValidateOTP->transaction_id}, Amount: {$ValidateOTP->transaction_amount} has been approved. Payment Sent to your Paybill {{$ValidateOTP->paybill_till_number}}.");
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction approved successfully.',
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction approval failed.',
                ]);
            }
        } else {
            $mpesa = new MpesaService();
            $b2cResponse = $mpesa->b2c($ValidateOTP);
            if (!$b2cResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction approval failed.',
                ]);
            }else{
                // Handle Paybill approval
                $ValidateOTP->status = 'Completed';
                $ValidateOTP->save();
                //send sms to the sender and receiver
                $smsService = new SmsService();
                $smsService->send($ValidateOTP->sender_mobile, "Your transaction with ID {$ValidateOTP->transaction_id} has been approved.");
                $smsService->send($ValidateOTP->receiver_mobile, "You have received a transaction with ID {$ValidateOTP->transaction_id}, Amount: {$ValidateOTP->transaction_amount} that has been approved.");
                 return response()->json([
                    'success' => true,
                    'message' => 'Transaction approved successfully.',
                ]);
            }
        }
    }


    //approveTransaction
    public function rejectTransaction($id)
    {
        $transaction = Transaction::where('transaction_id', $id)->first();
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ]);
        }
        //Return a view to reject the transaction
        return view('process.reject-transaction', compact('transaction'));
    }


    // transaction
    public function transaction($id)
    {
        $transaction = Transaction::where('transaction_id', $id)->first();
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ]);
        }
        //Get stk where checkout_request_id is the same as the transaction checkout_request_id
        $stkPush = MpesaStkPush::where('checkout_request_id', $transaction->checkout_request_id)->first();
        return view('process.transaction', compact('transaction', 'stkPush'));

    }

    //searchTransactions
    public function searchTransactions(Request $request): JsonResponse
    {
        $query = $request->input('id');
        $transactions = Transaction::where('transaction_id', $query)->get();
        // check if transactions exist
        if ($transactions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No transactions found for the given Transaction ID.',
            ]);
        }else{
                return response()->json([
                'success' => true,
                'data' => $transactions,
            ]);
        }
    }

    public function getAPIDocumentation(){
        return view('front.api-documentation');
    }

    public function getEContract(){
        return view('front.contracts.escrow-agreement');
    }

    /**
     * Test SMS sending to a specific phone number
     * 
     * @param Request $request
     * @param string|null $phone Phone number (optional, defaults to +254723014032)
     * @return JsonResponse
     */
    public function testSms(Request $request, $phone = null)
    {
        $phoneNumber = $phone ?? $request->input('phone', '+254723014032');
        $smsMessage = $request->input('message', 'Test SMS from eConfirm. This is a test message to verify SMS service integration.');

        try {
            $smsService = new SmsService();
            $result = $smsService->send($phoneNumber, $smsMessage, 'test-' . time());

            if (isset($result['status']) && $result['status']) {
                return response()->json([
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'data' => [
                        'phone' => $phoneNumber,
                        'message' => $smsMessage,
                        'response' => $result,
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to send SMS',
                    'data' => $result
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('SMS Test Error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error sending SMS: ' . $e->getMessage()
            ], 500);
        }
    }
}
