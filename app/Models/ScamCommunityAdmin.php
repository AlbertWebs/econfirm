<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScamCommunityAdmin extends Model
{
    protected $fillable = [
        'scam_community_id',
        'user_id',
        'status',
        'approved_by_admin_id',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function community()
    {
        return $this->belongsTo(ScamCommunity::class, 'scam_community_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
