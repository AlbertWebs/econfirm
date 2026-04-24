<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LiveChat extends Model
{
    protected $fillable = [
        'transaction_id',
        'public_token',
        'admin_token',
        'status',
        'opened_by_phone',
        'admin_alerted_at',
    ];

    protected $casts = [
        'admin_alerted_at' => 'datetime',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LiveChatMessage::class)->orderBy('id');
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class);
    }
}
