<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScamReport extends Model
{
    protected $fillable = [
        'report_type',
        'website',
        'phone',
        'reported_email',
        'category',
        'description',
        'email',
        'date_of_incident',
        'status',
        'report_count',
    ];

    protected $casts = [
        'date_of_incident' => 'date',
    ];

    public function getReportedValueAttribute()
    {
        return match($this->report_type) {
            'website' => $this->website,
            'phone' => $this->phone,
            'email' => $this->reported_email,
            default => null,
        };
    }

    public function likes()
    {
        return $this->hasMany(ScamReportLike::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }
}
