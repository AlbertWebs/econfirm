<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\MpesaStkPush; // Assuming you have a model for M-Pesa STK Push transactions

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
            $transaction->status = ($resultCode == 0) ? 'Success' : 'Failed';  //datatype is enum ('Pending', 'Success', 'Failed') can you change it to enum
            $transaction->result_desc = $resultDesc;
            $transaction->callback_metadata = $callbackMetadata;
            $transaction->save();
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    //handleB2BCallback
    public function handleB2BCallback(Request $request)
    {
        \Log::info('M-Pesa B2B Callback:', $request->all());

        $result = $request->input('Result', []);
        $params = [];
        if (isset($result['ResultParameters']['ResultParameter'])) {
            foreach ($result['ResultParameters']['ResultParameter'] as $param) {
                if (isset($param['Key']) && isset($param['Value'])) {
                    $params[$param['Key']] = $param['Value'];
                }
            }
        }

        // Save to database
        \App\Models\MpesaB2bCallback::create([
            'conversation_id' => $result['ConversationID'] ,
            'originator_conversation_id' => $result['OriginatorConversationID'] ,
            'transaction_id' => $result['TransactionID'] ,
            'result_type' => $result['ResultType'] ,
            'result_code' => $result['ResultCode'] ,
            'result_desc' => $result['ResultDesc'] ,
            'receiver_party_public_name' => $params['ReceiverPartyPublicName'] ,
            'amount' => $params['Amount'] ,
            'debit_account_balance' => $params['DebitAccountBalance'] ,
            'party_a' => $params['PartyA'] ,
            'party_b' => $params['PartyB'] ,
            'transaction_receipt' => $params['TransactionReceipt'] ,
            'transaction_completed_datetime' => $params['TransactionCompletedDateTime'] ,
            'initiator_account_current_balance' => $params['InitiatorAccountCurrentBalance'] ,
            'charges_paid' => $params['ChargesPaid'] ,
            'currency' => $params['Currency'] ,
            'raw_callback' => $request->all(),
        ]);

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    //handleB2CCallback
    public function handleB2CCallback(Request $request)
    {
        \Log::info('M-Pesa B2C Callback:', $request->all());

        $result = $request->input('Result', []);
        $params = [];
        if (isset($result['ResultParameters']['ResultParameter'])) {
            foreach ($result['ResultParameters']['ResultParameter'] as $param) {
                if (isset($param['Key']) && isset($param['Value'])) {
                    $params[$param['Key']] = $param['Value'];
                }
            }
        }
        $referenceUrl = null;
        if (isset($result['ReferenceData']['ReferenceItem'])) {
            $refItem = $result['ReferenceData']['ReferenceItem'];
            if (isset($refItem['Key']) && $refItem['Key'] === 'QueueTimeoutURL') {
                $referenceUrl = $refItem['Value'];
            }
        }

        \App\Models\MpesaB2cCallback::create([
            'conversation_id' => $result['ConversationID'],
            'originator_conversation_id' => $result['OriginatorConversationID'],
            'transaction_id' => $result['TransactionID'],
            'result_type' => $result['ResultType'],
            'result_code' => $result['ResultCode'],
            'result_desc' => $result['ResultDesc'],
            'transaction_amount' => $params['TransactionAmount'],
            'transaction_receipt' => $params['TransactionReceipt'],
            'receiver_party_public_name' => $params['ReceiverPartyPublicName'],
            'transaction_completed_datetime' => $params['TransactionCompletedDateTime'],
            'b2c_working_account_available_funds' => $params['B2CWorkingAccountAvailableFunds'],
            'b2c_utility_account_available_funds' => $params['B2CUtilityAccountAvailableFunds'],
            'b2c_charges_paid_account_available_funds' => $params['B2CChargesPaidAccountAvailableFunds'],
            'receiver_is_registered_customer' => $params['ReceiverIsRegisteredCustomer'],
            'charges_paid' => $params['ChargesPaid'],
            'queue_timeout_url' => $referenceUrl,
            'raw_callback' => $request->all(),
        ]);

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

}
