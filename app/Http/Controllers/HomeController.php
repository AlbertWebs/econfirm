<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\MpesaService;
use App\Models\MpesaStkPush;

class HomeController extends Controller
{
    public function index()
    {
        return view('front.welcome');
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

    //generateUniqueTransactionId
    private function generateUniqueTransactionId(): string
    {
        do {
            $transactionId = 'E-' . strtoupper(bin2hex(random_bytes(3))); // Generates a 6-character random string
        } while (Transaction::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
    public function submitTransaction(Request $request): JsonResponse
    {
        // Create code to generate unique transaction ID and check if it exists, if it exists, generate a new one the format is ESCROW-ENTRY-veryrandom5-digitnumber
        $transactionId = $this->generateUniqueTransactionId();

    
        // dd($request->all()); // Debugging line, remove in production
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

    //approveTransaction(id)
    public function approveTransaction($id)
    {
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

    
    public function transactionStatus($id)
    {
        $StkPush = MpesaStkPush::where('checkout_request_id', $id)->first();
        if ($StkPush && $StkPush->status === 'Success') {
            $transaction = Transaction::where('checkout_request_id', $id)->first();
            if ($transaction) {
                $transaction->status = 'Escrow Funded';
                $transaction->save();
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

   
}
