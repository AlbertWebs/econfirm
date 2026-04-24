<?php

namespace App\Http\Controllers;

use App\Models\MpesaB2c;
use App\Models\MpesaStkPush;
use App\Models\Transaction;
use App\Services\EscrowStkFundingService;
use App\Services\PhoneAccountProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    /**
     * STK CallbackMetadata Item list → [ Name => Value ].
     */
    protected function stkMetadataItemsToMap(array $items): array
    {
        $map = [];
        if (isset($items['Name'])) {
            $items = [$items];
        }
        foreach ($items as $item) {
            if (is_array($item) && isset($item['Name'])) {
                $map[$item['Name']] = $item['Value'] ?? null;
            }
        }

        return $map;
    }

    /**
     * Handle M-Pesa callback from Safaricom API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleCallback(Request $request)
    {
        \Log::info('M-Pesa Callback:', $request->all());

        $body = $request->input('Body.stkCallback');
        if (! is_array($body)) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
        }

        $merchantRequestID = $body['MerchantRequestID'] ?? null;
        $checkoutRequestID = $body['CheckoutRequestID'] ?? null;
        $resultCode = $body['ResultCode'] ?? null;
        $resultDesc = $body['ResultDesc'] ?? null;
        $callbackMetadata = $body['CallbackMetadata']['Item'] ?? [];

        $stkPush = $checkoutRequestID
            ? MpesaStkPush::where('checkout_request_id', $checkoutRequestID)->first()
            : null;

        if ($stkPush) {
            $stkPush->status = ((int) $resultCode === 0) ? 'Success' : 'Failed';
            $stkPush->result_desc = $resultDesc;
            $stkPush->callback_metadata = $callbackMetadata;
            $stkPush->save();

            if ((int) $resultCode === 0) {
                try {
                    EscrowStkFundingService::markFundedIfNotAlready($stkPush);
                } catch (\Throwable $e) {
                    Log::error('Escrow funding from STK callback failed', [
                        'checkout_request_id' => $checkoutRequestID,
                        'error' => $e->getMessage(),
                    ]);
                }

                $metaMap = $this->stkMetadataItemsToMap(is_array($callbackMetadata) ? $callbackMetadata : []);
                $payerPhone = isset($metaMap['PhoneNumber']) ? (string) $metaMap['PhoneNumber'] : (string) $stkPush->phone;
                $stkDisplayName = PhoneAccountProvisioningService::displayNameFromStkMetadata($metaMap);

                PhoneAccountProvisioningService::ensureUser(
                    $payerPhone,
                    $stkDisplayName,
                    $stkDisplayName !== null
                );

                $escrow = Transaction::where('transaction_id', $stkPush->reference)->first();
                if ($escrow) {
                    PhoneAccountProvisioningService::ensureUser($escrow->receiver_mobile);
                }
            }
        } else {
            Log::warning('M-Pesa STK callback: no mpesa_stk_pushes row for CheckoutRequestID', [
                'CheckoutRequestID' => $checkoutRequestID,
                'MerchantRequestID' => $merchantRequestID,
            ]);
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

        if ((int) ($result['ResultCode'] ?? 1) === 0 && ! empty($params['PartyB'])) {
            $name = PhoneAccountProvisioningService::parseReceiverPartyPublicName($params['ReceiverPartyPublicName'] ?? null);
            PhoneAccountProvisioningService::ensureUser($params['PartyB'], $name, $name !== null);
        }

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

        if ((int) ($result['ResultCode'] ?? 1) === 0) {
            $b2cRow = isset($result['ConversationID'])
                ? MpesaB2c::where('conversation_id', $result['ConversationID'])->first()
                : null;
            $receiverPhone = $b2cRow
                ? ($b2cRow->party_b ?? $b2cRow->receiver_mobile ?? null)
                : null;
            $name = PhoneAccountProvisioningService::parseReceiverPartyPublicName($params['ReceiverPartyPublicName'] ?? null);
            if ($receiverPhone) {
                PhoneAccountProvisioningService::ensureUser($receiverPhone, $name, $name !== null);
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

}
