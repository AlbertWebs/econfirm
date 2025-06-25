<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str; // Optional for slug formatting
use Barryvdh\DomPDF\Facade\Pdf;

class ContractController extends Controller
{
    public function generateEscrowPdf($transactionID)
    {
        $Transaction = \App\Models\Transaction::findOrFail($transactionID);
        $buyerName = $Transaction->sender_mobile ?? 'Buyer';
        $sellerName = $Transaction->receiver_mobile ?? 'Seller';
        $date = now()->format('Ymd'); // Format: 20250623
        if($Transaction->paybill_till_number == null) {
            $sellerName = $Transaction->receiver_mobile ?? 'Seller';
        }else {
            $sellerName = $Transaction->paybill_till_number ?? 'Seller';
        }

        // Optional: Slugify names to avoid spaces or special characters
        $filename = Str::slug($buyerName) . '-to-' . Str::slug($sellerName) . '-' . $date . '.pdf';

        $data = [
            'transaction_id' => $Transaction->transaction_id,
            'payment_method' => $Transaction->payment_method,
            'transaction_type' => $Transaction->transaction_type,
            'sender_mobile' => $Transaction->sender_mobile,
            'receiver_mobile' => $Transaction->receiver_mobile,
            'transaction_details' => $Transaction->transaction_details,
            'transaction_fee' => $Transaction->transaction_fee,
            'otp' => $Transaction->otp,
            'transaction_date' => $Transaction->created_at->format('F d, Y'),
            'transaction_time' => $Transaction->created_at->format('h:i A'),
            'transaction_status' => $Transaction->status,
            'transaction_amount' => $Transaction->transaction_amount,
            'created_at' => $Transaction->created_at->format('F d, Y h:i A'),
            'agreement_date' => now()->format('F d, Y'),
            // other fields...
        ];

        $pdf = Pdf::loadView('front.contracts.escrow-agreement', $data); 
        return $pdf->download($filename);
    }
}


