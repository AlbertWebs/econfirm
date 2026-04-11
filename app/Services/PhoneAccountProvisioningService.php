<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PhoneAccountProvisioningService
{
    /** Names we treat as placeholders and allow overwriting from M-Pesa. */
    private const GENERIC_NAMES = [
        'customer',
        'guest',
        'user',
        'm-pesa customer',
        'pending',
        '',
    ];

    public static function normalizeKenyaPhone(string $input): ?string
    {
        $n = SmsService::normalizeKenyaTo254($input);

        return preg_match('/^254\d{9}$/', $n) ? $n : null;
    }

    /**
     * Ensure a user row exists for this Kenya number. Optionally set or refine display name from M-Pesa.
     *
     * @param  bool  $forceNameUpdate  When true, always apply a non-empty display name (e.g. from B2C).
     */
    public static function ensureUser(string $phoneInput, ?string $displayName = null, bool $forceNameUpdate = false): ?User
    {
        $phone = self::normalizeKenyaPhone($phoneInput);
        if (! $phone) {
            return null;
        }

        $cleanName = self::sanitizeDisplayName($displayName);
        $user = User::findByKenyaPhone($phone);

        if ($user) {
            $user->phone = $phone;

            if ($cleanName !== null && ($forceNameUpdate || self::shouldReplaceName($user->name))) {
                $user->name = $cleanName;
            }

            $user->save();

            return $user;
        }

        return User::create([
            'name' => $cleanName ?? __('Customer'),
            'phone' => $phone,
            'email' => self::uniqueEmailForPhone($phone),
            'password' => Hash::make(Str::random(48)),
            'role' => 'user',
            'type' => 0,
        ]);
    }

    /**
     * Build a display name from STK CallbackMetadata map (when Safaricom sends name parts).
     */
    public static function displayNameFromStkMetadata(array $map): ?string
    {
        $parts = [];
        foreach (['FirstName', 'MiddleName', 'LastName', 'FullName'] as $k) {
            if (! empty($map[$k]) && is_string($map[$k])) {
                $parts[] = trim($map[$k]);
            }
        }
        if ($parts !== []) {
            return self::sanitizeDisplayName(implode(' ', $parts));
        }

        return null;
    }

    /**
     * B2C / B2B style: "JOHN DOE - 254712345678" or similar.
     */
    public static function parseReceiverPartyPublicName(?string $raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        $raw = trim($raw);
        if (preg_match('/^(.+?)\s*-\s*254\d{9}\s*$/i', $raw, $m)) {
            return self::sanitizeDisplayName($m[1]);
        }

        if (preg_match('/254\d{9}/', $raw)) {
            $stripped = trim(preg_replace('/\s*-\s*254\d{9}.*$/s', '', $raw));

            return self::sanitizeDisplayName($stripped);
        }

        return self::sanitizeDisplayName($raw);
    }

    protected static function uniqueEmailForPhone(string $normalized254): string
    {
        $base = $normalized254.'@phones.econfirm.local';
        if (! User::query()->where('email', $base)->exists()) {
            return $base;
        }

        return $normalized254.'_'.substr(sha1($normalized254.config('app.key')), 0, 10).'@phones.econfirm.local';
    }

    protected static function sanitizeDisplayName(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        $name = preg_replace('/\s+/', ' ', trim($name));

        return $name === '' ? null : Str::limit($name, 120, '');
    }

    protected static function shouldReplaceName(string $current): bool
    {
        return in_array(strtolower(trim($current)), self::GENERIC_NAMES, true);
    }
}
