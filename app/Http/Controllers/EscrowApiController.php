<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class EscrowApiController extends Controller
{
    /**
     * Create a new escrow transaction
     * 
     * POST /v1/transactions
     */
    public function createTransaction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'buyer_email' => 'required|email',
            'seller_email' => 'required|email',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|size:3',
            'description' => 'required|string|max:1000',
            'terms' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Generate unique transaction ID
        $transactionId = 'txn_' . strtoupper(Str::random(12));

        // Create transaction
        $transaction = Transaction::create([
            'transaction_id' => $transactionId,
            'transaction_type' => 'escrow',
            'transaction_amount' => $request->amount,
            'currency' => $request->currency ?? 'KES',
            'buyer_email' => $request->buyer_email,
            'seller_email' => $request->seller_email,
            'transaction_details' => $request->description,
            'terms' => $request->terms,
            'sender_mobile' => '', // Will be updated when buyer funds
            'receiver_mobile' => '', // Will be updated when seller confirms
            'payment_method' => 'api',
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Escrow transaction created successfully',
            'data' => [
                'id' => $transaction->transaction_id,
                'status' => $transaction->status,
                'amount' => (float) $transaction->transaction_amount,
                'currency' => $transaction->currency,
                'buyer_email' => $transaction->buyer_email,
                'seller_email' => $transaction->seller_email,
                'description' => $transaction->transaction_details,
                'created_at' => $transaction->created_at->format('c'),
            ],
        ], 201);
    }

    /**
     * Get transaction status
     * 
     * GET /v1/transactions/{transaction_id}
     */
    public function getTransaction(Request $request, string $transactionId): JsonResponse
    {
        // Remove 'txn_' prefix if present
        $id = str_replace('txn_', '', $transactionId);
        
        $transaction = Transaction::where('transaction_id', $transactionId)
            ->orWhere('transaction_id', 'like', '%' . $id . '%')
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        // Get buyer and seller users if they exist
        $buyer = User::where('email', $transaction->buyer_email)->first();
        $seller = User::where('email', $transaction->seller_email)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaction->transaction_id,
                'status' => $transaction->status,
                'amount' => (float) $transaction->transaction_amount,
                'currency' => $transaction->currency ?? 'KES',
                'description' => $transaction->transaction_details,
                'terms' => $transaction->terms,
                'created_at' => $transaction->created_at->format('c'),
                'updated_at' => $transaction->updated_at->format('c'),
                'buyer' => [
                    'email' => $transaction->buyer_email,
                    'verified' => $buyer ? ($buyer->email_verified_at !== null) : false,
                ],
                'seller' => [
                    'email' => $transaction->seller_email,
                    'verified' => $seller ? ($seller->email_verified_at !== null) : false,
                ],
            ],
        ]);
    }

    /**
     * Release funds to seller
     * 
     * POST /v1/transactions/{transaction_id}/release
     */
    public function releaseFunds(Request $request, string $transactionId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'confirmation_code' => 'required|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $transaction = Transaction::where('transaction_id', $transactionId)->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        // Check if transaction is in a valid state for release
        if (!in_array($transaction->status, ['funded', 'in_progress', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction cannot be released. Current status: ' . $transaction->status,
            ], 400);
        }

        // Verify confirmation code matches
        if ($transaction->confirmation_code && $transaction->confirmation_code !== $request->confirmation_code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid confirmation code',
            ], 400);
        }

        // Update transaction status
        $transaction->update([
            'status' => 'complete',
            'confirmation_code' => $request->confirmation_code,
            'transaction_details' => $transaction->transaction_details . 
                ($request->notes ? "\n\nRelease Notes: " . $request->notes : ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Funds released successfully',
            'data' => [
                'id' => $transaction->transaction_id,
                'status' => $transaction->status,
                'amount' => (float) $transaction->transaction_amount,
                'currency' => $transaction->currency ?? 'KES',
                'released_at' => $transaction->updated_at->format('c'),
            ],
        ]);
    }
}
