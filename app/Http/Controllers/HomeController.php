<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\MpesaService;

class HomeController extends Controller
{
    public function submitTransaction(Request $request): JsonResponse
    {
        // Validate input
        $validated = $request->validate([
            'transaction-type' => 'required|string',
            'transaction-amount' => 'required|numeric|min:1',
            'sender-mobile' => 'required|string',
            'receiver-mobile' => 'required|string',
            'transaction-details' => 'nullable|string',
        ]);

        // Save transaction to database
        $transaction = Transaction::create([
            'transaction_type' => $validated['transaction-type'],
            'transaction_amount' => $validated['transaction-amount'],
            'sender_mobile' => $validated['sender-mobile'],
            'receiver_mobile' => $validated['receiver-mobile'],
            'transaction_details' => $validated['transaction-details'] ?? null,
            'status' => 'pending',
        ]);

        // Use MpesaService for STK push
        $mpesa = new MpesaService();
        $mpesaResponse = $mpesa->stkPush($transaction);
        // dd($mpesaResponse); // Debugging line, remove in production

        if ($mpesaResponse['success']) {
            $transaction->status = 'stk_initiated';
            $transaction->save();
            return response()->json([
                'success' => true,
                'message' => 'Transaction submitted and STK push initiated! Check your phone for pin confirmation.',
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

    //get access token
    public function getAccessToken(): string
    {
        $mpesa = new MpesaService();
        return $mpesa->getAccessToken();
    }
}
