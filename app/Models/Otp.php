<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'otp_code',
        'is_verified',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if OTP is valid (not expired and not verified)
     */
    public function isValid(): bool
    {
        return !$this->is_verified && !$this->isExpired();
    }

    /**
     * Mark OTP as verified
     */
    public function markAsVerified(): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Generate a 6-digit OTP code
     */
    public static function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new OTP for a phone number
     */
    public static function createForPhone(string $phoneNumber, int $expiryMinutes = 10): self
    {
        try {
            // Invalidate any existing unverified OTPs for this phone BEFORE creating new one
            $invalidated = self::where('phone_number', $phoneNumber)
                ->where('is_verified', false)
                ->where('expires_at', '>', now())
                ->update(['is_verified' => true]); // Mark as used

            \Log::info('Invalidated existing OTPs', [
                'phone_number' => $phoneNumber,
                'count' => $invalidated
            ]);

            $otpCode = self::generateCode();
            $expiresAt = now()->addMinutes($expiryMinutes);

            \Log::info('Creating new OTP record', [
                'phone_number' => $phoneNumber,
                'otp_code' => $otpCode,
                'expires_at' => $expiresAt->toDateTimeString()
            ]);

            // Use create method which should work fine
            $otp = self::create([
                'phone_number' => $phoneNumber,
                'otp_code' => $otpCode,
                'expires_at' => $expiresAt,
                'is_verified' => false,
            ]);

            // Verify it was saved
            if (!$otp->id) {
                throw new \Exception('OTP was not saved - no ID returned');
            }

            \Log::info('OTP record created and saved successfully', [
                'otp_id' => $otp->id,
                'phone_number' => $otp->phone_number,
                'otp_code' => $otp->otp_code,
                'expires_at' => $otp->expires_at->toDateTimeString(),
                'is_verified' => $otp->is_verified
            ]);

            // Double-check by querying the database
            $verifyOtp = self::find($otp->id);
            if (!$verifyOtp) {
                throw new \Exception('OTP was not found in database after creation');
            }

            return $otp;
        } catch (\Exception $e) {
            \Log::error('Failed to create OTP', [
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Verify OTP code for a phone number
     */
    public static function verify(string $phoneNumber, string $otpCode): ?self
    {
        $otp = self::where('phone_number', $phoneNumber)
            ->where('otp_code', $otpCode)
            ->where('is_verified', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($otp) {
            $otp->markAsVerified();
            return $otp;
        }

        return null;
    }
}

