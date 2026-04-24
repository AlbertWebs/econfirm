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

    /**
     * B2C result / queue-timeout posts from Safaricom (ResultURL + QueueTimeOutURL).
     *
     * @return array<string, mixed>
     */
    protected function b2cResultParametersToMap(mixed $raw): array
    {
        $params = [];
        if ($raw === null) {
            return $params;
        }
        if (is_array($raw) && isset($raw['Key'])) {
            $raw = [$raw];
        }
        if (! is_array($raw)) {
            return $params;
        }
        foreach ($raw as $param) {
            if (is_array($param) && isset($param['Key'])) {
                $params[$param['Key']] = $param['Value'] ?? null;
            }
        }

        return $params;
    }

    /**
     * Resolve mpesa_b2c row from async Result (ConversationID often differs from sync accept payload).
     */
    protected function findMpesaB2cRowForB2cResult(array $result): ?MpesaB2c
    {
        $convId = $result['ConversationID'] ?? null;
        $origId = $result['OriginatorConversationID'] ?? null;
        $ids = [];
        foreach ([$convId, $origId] as $v) {
            if ($v === null || $v === '') {
                continue;
            }
            $ids[] = is_string($v) ? trim($v) : $v;
        }
        $ids = array_values(array_unique($ids));
        if ($ids === []) {
            return null;
        }

        return MpesaB2c::query()
            ->where(function ($q) use ($ids) {
                foreach ($ids as $i => $id) {
                    $q->{$i === 0 ? 'where' : 'orWhere'}(function ($w) use ($id) {
                        $w->where('conversation_id', $id)->orWhere('originator_conversation_id', $id);
                    });
                }
            })
            ->orderByDesc('id')
            ->first();
    }

    //handleB2CCallback
    public function handleB2CCallback(Request $request)
    {
        \Log::info('M-Pesa B2C Callback:', $request->all());

        $result = $request->input('Result', []);
        if (! is_array($result)) {
            $result = [];
        }
        $params = $this->b2cResultParametersToMap($result['ResultParameters']['ResultParameter'] ?? null);
        $referenceUrl = null;
        if (isset($result['ReferenceData']['ReferenceItem'])) {
            $refItem = $result['ReferenceData']['ReferenceItem'];
            if (isset($refItem['Key']) && $refItem['Key'] === 'QueueTimeoutURL') {
                $referenceUrl = $refItem['Value'];
            }
        }

        \App\Models\MpesaB2cCallback::create([
            'conversation_id' => $result['ConversationID'] ?? null,
            'originator_conversation_id' => $result['OriginatorConversationID'] ?? null,
            'transaction_id' => $result['TransactionID'] ?? null,
            'result_type' => $result['ResultType'] ?? null,
            'result_code' => $result['ResultCode'] ?? null,
            'result_desc' => $result['ResultDesc'] ?? null,
            'transaction_amount' => $params['TransactionAmount'] ?? null,
            'transaction_receipt' => $params['TransactionReceipt'] ?? null,
            'receiver_party_public_name' => $params['ReceiverPartyPublicName'] ?? null,
            'transaction_completed_datetime' => $params['TransactionCompletedDateTime'] ?? null,
            'b2c_working_account_available_funds' => $params['B2CWorkingAccountAvailableFunds'] ?? null,
            'b2c_utility_account_available_funds' => $params['B2CUtilityAccountAvailableFunds'] ?? null,
            'b2c_charges_paid_account_available_funds' => $params['B2CChargesPaidAccountAvailableFunds'] ?? null,
            'receiver_is_registered_customer' => $params['ReceiverIsRegisteredCustomer'] ?? null,
            'charges_paid' => $params['ChargesPaid'] ?? null,
            'queue_timeout_url' => $referenceUrl,
            'raw_callback' => $request->all(),
        ]);

        $b2cRow = $this->findMpesaB2cRowForB2cResult($result);
        if ($b2cRow) {
            $rc = (int) ($result['ResultCode'] ?? 1);
            $b2cRow->fill([
                'conversation_id' => $result['ConversationID'] ?? $b2cRow->conversation_id,
                'originator_conversation_id' => $result['OriginatorConversationID'] ?? $b2cRow->originator_conversation_id,
                'transaction_id' => $result['TransactionID'] ?? $b2cRow->transaction_id,
                'result_code' => $result['ResultCode'] ?? $b2cRow->result_code,
                'result_desc' => $result['ResultDesc'] ?? $b2cRow->result_desc,
                'status' => $rc === 0 ? 'Completed' : 'Failed',
            ]);
            $b2cRow->save();
        } else {
            Log::warning('M-Pesa B2C callback: no mpesa_b2c row matched ConversationID / OriginatorConversationID', [
                'ConversationID' => $result['ConversationID'] ?? null,
                'OriginatorConversationID' => $result['OriginatorConversationID'] ?? null,
            ]);
        }

        if ((int) ($result['ResultCode'] ?? 1) === 0) {
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
