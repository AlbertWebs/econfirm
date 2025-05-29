<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    /**
     * Handle M-Pesa callback from Safaricom API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  public function handleCallback(Request $request)
{
    // Log the callback for debugging (optional)
    \Log::info('M-Pesa Callback:', $request->all());

    $body = $request->input('Body.stkCallback');

    $merchantRequestID = $body['MerchantRequestID'];
    $checkoutRequestID = $body['CheckoutRequestID'];
    $resultCode = $body['ResultCode'];
    $resultDesc = $body['ResultDesc'];
    $callbackMetadata = $body['CallbackMetadata']['Item'] ?? [];

    // You should have saved CheckoutRequestID in your Transaction when you sent the STK
    $transaction = MpesaStkPush::where('checkout_request_id', $checkoutRequestID)->first();

    if ($transaction) {
        $transaction->status = ($resultCode == 0) ? 'completed' : 'failed';
        $transaction->result_desc = $resultDesc;
        $transaction->callback_metadata = $callbackMetadata;
        $transaction->save();
    }

    return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
}
}
