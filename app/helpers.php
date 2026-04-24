<?php

use App\Models\Transaction;
use App\Services\SiteSettingsService;
use Illuminate\Support\Carbon;

if (! function_exists('site_setting')) {
    /**
     * Site-wide setting value (merged config defaults + `site_settings` table), or all values when $key is null.
     *
     * @return ($key is null ? array<string, string|null> : string|null)
     */
    function site_setting(?string $key = null, mixed $default = null): mixed
    {
        $svc = app(SiteSettingsService::class);
        if ($key === null) {
            return $svc->all();
        }

        return $svc->get($key, $default);
    }
}

if (! function_exists('trusted_clients_daily_increment')) {
    /**
     * Deterministic pseudo-random integer in 1..15 for a given calendar day (stable site-wide per day).
     */
    function trusted_clients_daily_increment(Carbon $date): int
    {
        $key = 'econfirm_trusted_clients_'. $date->copy()->startOfDay()->format('Y-m-d');

        return (int) (hexdec(substr(md5($key), 0, 7)) % 15) + 1;
    }
}

if (! function_exists('trusted_clients_count')) {
    /**
     * Display count: base on start_date, +1..15 for each day after that through today (inclusive, start of day).
     */
    function trusted_clients_count(?Carbon $asOf = null): int
    {
        $asOf = ($asOf ?? Carbon::now())->copy()->startOfDay();
        $base = (int) config('econfirm.trusted_clients.base', 225);
        $start = Carbon::parse((string) config('econfirm.trusted_clients.start_date', '2026-04-24'))->startOfDay();

        if ($asOf < $start) {
            return $base;
        }

        $total = $base;
        $day = $start->copy();
        while ($day < $asOf) {
            $day->addDay();
            $total += trusted_clients_daily_increment($day);
        }

        return $total;
    }
}

if (! function_exists('trusted_transactions_count')) {
    /**
     * Public "Transactions" stat: same basis as the client count, but always a little higher
     * (typical to have more transactions than unique users), without a second counter to maintain.
     */
    function trusted_transactions_count(?Carbon $asOf = null): int
    {
        $users = trusted_clients_count($asOf);
        if ($users < 1) {
            return 1;
        }
        $extra = max(1, (int) round($users * 0.05));

        return $users + $extra;
    }
}

if (! function_exists('funds_protected_stk_initiated_kes')) {
    /**
     * Total principal (KES) in transactions that are in STK-initiated (payment prompt sent) state.
     */
    function funds_protected_stk_initiated_kes(): float
    {
        return (float) Transaction::query()
            ->whereRaw('LOWER(TRIM(status)) = ?', ['stk_initiated'])
            ->sum('transaction_amount');
    }
}

if (! function_exists('format_funds_protected_stk_kes_for_stat')) {
    /**
     * One-line label for the homepage "Protected" stat (short scale when large).
     */
    function format_funds_protected_stk_kes_for_stat(): string
    {
        $amount = funds_protected_stk_initiated_kes();
        if ($amount <= 0) {
            return 'KES 0';
        }
        if ($amount >= 1_000_000_000) {
            $b = $amount / 1_000_000_000;

            return 'KES '.(floor($b) == $b ? (string) (int) $b : number_format($b, 1)).'B+';
        }
        if ($amount >= 1_000_000) {
            $m = $amount / 1_000_000;

            return 'KES '.(floor($m) == $m ? (string) (int) $m : number_format($m, 1)).'M+';
        }
        if ($amount >= 1_000) {
            $k = $amount / 1_000;

            return 'KES '.(floor($k) == $k ? (string) (int) $k : number_format($k, 1)).'K+';
        }

        return 'KES '.number_format($amount, 0).'+';
    }
}
