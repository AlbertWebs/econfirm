<?php

namespace App\Support;

use Illuminate\Support\Facades\Config;

/**
 * Documents M-Pesa callback URLs: fixed inbound API routes vs values sent on Daraja requests (.env).
 */
final class MpesaCallbackUrls
{
    /**
     * @return array<string, string>
     */
    public static function inboundRoutes(): array
    {
        return [
            'stk' => url('/api/mpesa/callback'),
            'b2b' => url('/api/mpesa/b2b/callback'),
            'b2c_result' => url('/api/mpesa/b2c/callback'),
            'b2c_timeout' => url('/api/mpesa/b2c/timeout'),
        ];
    }

    public static function urlsMatch(string $configured, string $inbound): bool
    {
        if ($configured === '' || $inbound === '') {
            return false;
        }

        return rtrim($configured, '/') === rtrim($inbound, '/');
    }

    /**
     * @return array{inbound: array<string, string>, rows: list<array{key: string, label: string, daraja_field: string, env_hint: string, effective: string|null, inbound_target: string, matches: bool, missing: bool}>}
     */
    public static function adminSummary(): array
    {
        $inbound = self::inboundRoutes();

        $stk = trim((string) Config::get('mpesa.callback_url', ''));
        $b2bResult = trim((string) Config::get('mpesa.b2b_results_url', ''));
        $b2bTimeout = trim((string) Config::get('mpesa.b2b_queue_timeout_url', ''));
        $b2cResultCfg = trim((string) Config::get('mpesa.b2c_result_url', ''));
        $b2cTimeoutCfg = trim((string) Config::get('mpesa.b2c_timeout_url', ''));

        $b2cResultEffective = $b2cResultCfg !== '' ? $b2cResultCfg : $inbound['b2c_result'];
        $b2cTimeoutEffective = $b2cTimeoutCfg !== '' ? $b2cTimeoutCfg : $inbound['b2c_timeout'];

        $rows = [
            [
                'key' => 'stk',
                'label' => 'STK (Lipa na M-Pesa Online)',
                'daraja_field' => 'CallBackURL',
                'env_hint' => 'MPESA_CALLBACK_URL',
                'effective' => $stk !== '' ? $stk : null,
                'inbound_target' => $inbound['stk'],
                'matches' => $stk !== '' && self::urlsMatch($stk, $inbound['stk']),
                'missing' => $stk === '',
            ],
            [
                'key' => 'b2b_result',
                'label' => 'B2B result',
                'daraja_field' => 'ResultURL',
                'env_hint' => 'MPESA_B2B_RESULTS_URL',
                'effective' => $b2bResult !== '' ? $b2bResult : null,
                'inbound_target' => $inbound['b2b'],
                'matches' => $b2bResult !== '' && self::urlsMatch($b2bResult, $inbound['b2b']),
                'missing' => $b2bResult === '',
            ],
            [
                'key' => 'b2b_timeout',
                'label' => 'B2B queue timeout',
                'daraja_field' => 'QueueTimeOutURL',
                'env_hint' => 'MPESA_B2B_QUEUE_TIMEOUT_URL',
                'effective' => $b2bTimeout !== '' ? $b2bTimeout : null,
                'inbound_target' => $inbound['b2b'],
                'matches' => $b2bTimeout !== '' && self::urlsMatch($b2bTimeout, $inbound['b2b']),
                'missing' => $b2bTimeout === '',
            ],
            [
                'key' => 'b2c_result',
                'label' => 'B2C result',
                'daraja_field' => 'ResultURL',
                'env_hint' => 'MPESA_B2C_RESULT_URL (optional; empty uses app default)',
                'effective' => $b2cResultEffective,
                'inbound_target' => $inbound['b2c_result'],
                'matches' => self::urlsMatch($b2cResultEffective, $inbound['b2c_result']),
                'missing' => false,
            ],
            [
                'key' => 'b2c_timeout',
                'label' => 'B2C queue timeout',
                'daraja_field' => 'QueueTimeOutURL',
                'env_hint' => 'MPESA_B2C_TIMEOUT_URL (optional; empty uses app default)',
                'effective' => $b2cTimeoutEffective,
                'inbound_target' => $inbound['b2c_timeout'],
                'matches' => self::urlsMatch($b2cTimeoutEffective, $inbound['b2c_timeout']),
                'missing' => false,
            ],
        ];

        return [
            'inbound' => $inbound,
            'rows' => $rows,
        ];
    }
}
