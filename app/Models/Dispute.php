<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispute extends Model
{
    public const STATUS_CREATED = 'Created';

    public const STATUS_ONGOING = 'Ongoing';

    public const STATUS_RESOLVED = 'Resolved';

    /**
     * @var list<string>
     */
    public const STATUSES = [
        self::STATUS_CREATED,
        self::STATUS_ONGOING,
        self::STATUS_RESOLVED,
    ];

    protected $fillable = [
        'transaction_id',
        'live_chat_id',
        'status',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function liveChat(): BelongsTo
    {
        return $this->belongsTo(LiveChat::class);
    }
}
