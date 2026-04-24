<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitePageView extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'path',
        'method',
        'status_code',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
