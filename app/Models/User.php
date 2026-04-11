<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\SmsService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'street',
        'city',
        'state',
        'company',
        'zip',
        'email',
        'password',
        'role',
        'type',
        'api_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * Interact with the user's type.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => isset(["user", "admin", "manager"][$value]) ? ["user", "admin", "manager"][$value] : "user",
        );
    }

    /**
     * Find a user by Kenya phone, matching common stored formats (254…, 07…, 7…).
     */
    public static function findByKenyaPhone(string $input): ?self
    {
        $normalized = SmsService::normalizeKenyaTo254($input);
        if (! preg_match('/^254\d{9}$/', $normalized)) {
            return null;
        }

        return static::query()
            ->whereNotNull('phone')
            ->where(function ($q) use ($normalized) {
                $q->where('phone', $normalized)
                    ->orWhere('phone', '0' . substr($normalized, 3))
                    ->orWhere('phone', substr($normalized, 3));
            })
            ->first();
    }
}
