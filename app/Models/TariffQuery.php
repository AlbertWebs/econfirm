<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TariffQuery extends Model
{
    protected $fillable = [
        'amount_kes',
        'rail',
        'commission_kes',
        'mpesa_fee_kes',
        'total_kes',
        'error_message',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'amount_kes' => 'integer',
            'commission_kes' => 'decimal:2',
            'mpesa_fee_kes' => 'integer',
            'total_kes' => 'integer',
        ];
    }
}
