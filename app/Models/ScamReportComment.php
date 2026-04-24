<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScamReportComment extends Model
{
    protected $fillable = [
        'scam_report_id',
        'parent_id',
        'author_name',
        'author_email',
        'body',
        'ip_address',
        'user_agent',
        'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function report()
    {
        return $this->belongsTo(ScamReport::class, 'scam_report_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at', 'asc');
    }
}
