<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingContact extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'stk_attempts',
        'last_stk_attempt_at',
        'last_imported_at',
        'source',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_stk_attempt_at' => 'datetime',
            'last_imported_at' => 'datetime',
        ];
    }
}
