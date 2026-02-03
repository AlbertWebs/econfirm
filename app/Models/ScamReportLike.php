<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScamReportLike extends Model
{
    protected $fillable = [
        'scam_report_id',
        'ip_address',
        'user_agent',
    ];

    public function scamReport()
    {
        return $this->belongsTo(ScamReport::class);
    }
}
