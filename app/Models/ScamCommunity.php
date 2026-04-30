<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ScamCommunity extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $community): void {
            if (! filled($community->slug) && filled($community->name)) {
                $community->slug = Str::slug((string) $community->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function reports()
    {
        return $this->hasMany(ScamReport::class, 'community_id');
    }

    public function admins()
    {
        return $this->hasMany(ScamCommunityAdmin::class, 'scam_community_id');
    }
}
