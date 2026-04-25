<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\VelipayPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VelipayService
{
    private string $baseUrl;

    private string $apiKeyId;

    private string $apiKeySecret;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('velipay.base_url', 'https://api.pay.velinexlabs.com'), '/');
        $this->apiKeyId = (string) config('velipay.api_key_id', '');
        $this->apiKeySecret = (string) config('velipay.api_key_secret', '');
    }

    /**
     * Initiate an STK push through VeliPay and persist a local payment row.
     *
     * @return array{success: bool, message?: string, data?: array<string, mixed>}
     */
    public function stkPush(Transaction $transaction, ?string $initiatorIp = null): array
    {
        if ($this->apiKeyId === '' || $this->apiKeySecret === '') {
            return [
                'success' => false,
                'message' => 'VeliPay API keys are not configured.',
            ];
        }

        $amountKes = (int) round((float) $transaction->transaction_amount + (float) ($transaction->transaction_fee ?? 0), 0);
        $payload = [
            'amount' => $amountKes,
            'phoneNumber' => preg_replace('/[\s+]/', '', (string) $transaction->sender_mobile),
            'merchantReference' => (string) $transaction->transaction_id,
            'description' => (string) ($transaction->transaction_details ?: 'Escrow Payment'),
            'settlementMode' => 'manual',
            'metadata' => [
                'source' => 'econfirm',
                'transactionId' => (string) $transaction->transaction_id,
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKeyId.':'.$this->apiKeySecret,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(20)->post($this->baseUrl.'/api/v1/payments/stk-push', $payload);
        } catch (\Throwable $e) {
            Log::error('VeliPay STK request failed', [
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Could not reach VeliPay.',
            ];
        }

        $body = $response->json();
        if (! is_array($body)) {
            $body = [];
        }

        if ($response->failed()) {
            Log::error('VeliPay STK API failure', [
                'transaction_id' => $transaction->transaction_id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => (string) ($body['message'] ?? 'VeliPay request failed.'),
                'data' => $body,
            ];
        }

        $paymentId = $this->extractPaymentId($body);
        if ($paymentId === null) {
            Log::error('VeliPay STK missing payment id', [
                'transaction_id' => $transaction->transaction_id,
                'body' => $body,
            ]);

            return [
                'success' => false,
                'message' => 'VeliPay did not return a payment ID.',
                'data' => $body,
            ];
        }

        VelipayPayment::updateOrCreate(
            ['velipay_payment_id' => $paymentId],
            [
                'transaction_id' => $transaction->transaction_id,
                'initiator_ip' => $initiatorIp,
                'phone' => $payload['phoneNumber'],
                'amount' => $amountKes,
                'merchant_reference' => $payload['merchantReference'],
                'description' => $payload['description'],
                'status' => 'pending',
                'raw_response' => $body,
            ]
        );

        return [
            'success' => true,
            'message' => (string) ($body['message'] ?? 'Payment request sent.'),
            'data' => array_merge($body, ['paymentId' => $paymentId]),
        ];
    }

    public function extractPaymentId(array $payload): ?string
    {
        $candidate = $payload['paymentId'] ?? $payload['id'] ?? null;
        if (is_string($candidate) && trim($candidate) !== '') {
            return trim($candidate);
        }

        return null;
    }

    /**
     * Release escrow funds from business balance to recipient phone via VeliPay.
     *
     * @return array{success: bool, message?: string, data?: array<string, mixed>}
     */
    public function withdrawToPhone(Transaction $transaction): array
    {
        if ($this->apiKeyId === '' || $this->apiKeySecret === '') {
            return [
                'success' => false,
                'message' => 'VeliPay API keys are not configured.',
            ];
        }

        $destination = preg_replace('/[\s+]/', '', (string) $transaction->receiver_mobile);
        $amount = (int) round((float) $transaction->transaction_amount, 0);
        if ($destination === '' || $amount <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid payout destination or amount.',
            ];
        }

        $payload = [
            'amount' => $amount,
            'releaseReference' => (string) $transaction->transaction_id,
            'destinationType' => 'phone',
            'destination' => $destination,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKeyId.':'.$this->apiKeySecret,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(20)->post($this->baseUrl.'/api/v1/business/withdraw', $payload);
        } catch (\Throwable $e) {
            Log::error('VeliPay withdraw request failed', [
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Could not reach VeliPay withdrawal API.',
            ];
        }

        $body = $response->json();
        if (! is_array($body)) {
            $body = [];
        }

        if ($response->failed()) {
            return [
                'success' => false,
                'message' => (string) ($body['message'] ?? 'Withdrawal request failed.'),
                'data' => $body,
            ];
        }

        return [
            'success' => true,
            'message' => (string) ($body['message'] ?? 'Withdrawal queued.'),
            'data' => $body,
        ];
    }
}
