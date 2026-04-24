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

    public function adminStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_RESOLVED => 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-200',
            self::STATUS_ONGOING => 'bg-amber-100 text-amber-950 ring-1 ring-amber-200',
            self::STATUS_CREATED => 'bg-sky-100 text-sky-950 ring-1 ring-sky-200',
            default => 'bg-slate-100 text-slate-700 ring-1 ring-slate-200',
        };
    }
}
