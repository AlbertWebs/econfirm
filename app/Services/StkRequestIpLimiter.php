<?php

namespace App\Services;

use App\Models\VelipayPayment;

class StkRequestIpLimiter
{
    public const MESSAGE = 'Too many uncompleted transactions. Please complete or wait for an existing payment prompt to finish before starting another.';

    /**
     * “Uncompleted” = STK record still waiting on customer/callback (Pending).
     */
    public static function uncompletedStkCountForIp(?string $ip): int
    {
        if ($ip === null || $ip === '') {
            return 0;
        }

        return (int) VelipayPayment::query()
            ->where('initiator_ip', $ip)
            ->whereIn('status', ['pending', 'initiated'])
            ->count();
    }

    public static function maxUncompletedPerIp(): int
    {
        $max = (int) config('velipay.stk_max_uncompleted_per_ip', 3);

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
