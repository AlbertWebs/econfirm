<?php

namespace App\Services;

use App\Models\MpesaStkPush;

class StkRequestIpLimiter
{
    public const MESSAGE = 'Too many uncompleted transactions. Please complete or wait for an existing M-Pesa payment prompt to finish before starting another.';

    /**
     * “Uncompleted” = STK record still waiting on customer/callback (Pending).
     */
    public static function uncompletedStkCountForIp(?string $ip): int
    {
        if ($ip === null || $ip === '') {
            return 0;
        }

        return (int) MpesaStkPush::query()
            ->where('initiator_ip', $ip)
            ->where('status', 'Pending')
            ->count();
    }

    public static function maxUncompletedPerIp(): int
    {
        $max = (int) config('mpesa.stk_max_uncompleted_per_ip', 3);

        return max(0, $max);
    }

    public static function isBlocked(?string $ip): bool
    {
        $max = self::maxUncompletedPerIp();
        if ($max <= 0) {
            return false;
        }

        return self::uncompletedStkCountForIp($ip) >= $max;
    }
}
