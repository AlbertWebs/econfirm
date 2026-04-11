<?php

namespace App\Models;

use App\Services\PhoneAccountProvisioningService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (Transaction $transaction) {
            if (! empty($transaction->sender_mobile)) {
                PhoneAccountProvisioningService::ensureUser($transaction->sender_mobile);
            }
            if (! empty($transaction->receiver_mobile)) {
                PhoneAccountProvisioningService::ensureUser($transaction->receiver_mobile);
            }
        });
    }

    protected $fillable = [
        'otp',
        'transaction_fee',
        'paybill_till_number',
        'payment_method',
        'transaction_id',
        'transaction_type',
        'transaction_amount',
        'sender_mobile',
        'receiver_mobile',
        'transaction_details',
        'status',
        'buyer_email',
        'seller_email',
        'currency',
        'terms',
        'confirmation_code',
    ];
}
