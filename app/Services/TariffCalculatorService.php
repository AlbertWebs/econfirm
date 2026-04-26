<?php

namespace App\Services;

class TariffCalculatorService
{
    /**
     * @param  array<int, array{min: int|float|string, max: int|float|string, fee: int|float|string}>  $tiers
     */
    public static function tierFee(int $amountKes, array $tiers): ?int
    {
        foreach ($tiers as $t) {
            $lo = (int) ($t['min'] ?? 0);
            $hi = (int) ($t['max'] ?? 0);
            if ($amountKes >= $lo && $amountKes <= $hi) {
                return (int) round((float) ($t['fee'] ?? 0));
            }
        }

        return null;
    }

    /**
     * @return array{principal: int, rail: string, rail_label: string, commission: float, mpesa_fee: int, total: int}
     *
     * @throws \InvalidArgumentException
     */
    public static function compute(int $principalKes, string $rail): array
    {
        $rail = $rail === 'b2b' ? 'b2b' : 'b2c';
        $cfg = config('tariffs', []);
        $rate = (float) ($cfg['commission_rate'] ?? 0.01);
        if ($rate < 0 || $rate > 1) {
            $rate = 0.01;
        }

        $tiers = $rail === 'b2b'
            ? ($cfg['mpesa']['b2b_tiers'] ?? [])
            : ($cfg['mpesa']['b2c_tiers'] ?? []);

        $mpesa = self::tierFee($principalKes, $tiers);
        if ($mpesa === null) {
            throw new \InvalidArgumentException('Amount is outside the published M-PESA tariff bands.');
        }

        $commission = round($principalKes * $rate, 2);
        $total = (int) round($principalKes + $commission + $mpesa);

        return [
            'principal' => $principalKes,
            'rail' => $rail,
            'rail_label' => $rail === 'b2b' ? 'Phone number to till (Paybill Business Bouquet)' : 'Phone number to phone number (Safaricom send)',
            'commission' => $commission,
            'mpesa_fee' => $mpesa,
            'total' => $total,
        ];
    }
}
