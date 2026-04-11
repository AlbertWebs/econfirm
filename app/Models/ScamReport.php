<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ScamReport extends Model
{
    public const CATEGORY_LABELS = [
        'ecommerce' => 'E-commerce scams',
        'services' => 'Service scams',
        'investment' => 'Investment scams',
        'job' => 'Job advertisement scams',
        'romance' => 'Romance scams',
        'other' => 'Other scams',
    ];

    /** Short labels for compact navigation chips (full name in CATEGORY_LABELS). */
    public const CATEGORY_SHORT_LABELS = [
        'ecommerce' => 'E-commerce',
        'services' => 'Services',
        'investment' => 'Investment',
        'job' => 'Job',
        'romance' => 'Romance',
        'other' => 'Other',
    ];

    protected $fillable = [
        'report_type',
        'website',
        'phone',
        'reported_email',
        'category',
        'category_other',
        'description',
        'email',
        'reporter_phone',
        'date_of_incident',
        'status',
        'report_count',
    ];

    protected $casts = [
        'date_of_incident' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('footer_scam_report_links_v1');
        });
        static::deleted(function () {
            Cache::forget('footer_scam_report_links_v1');
        });
    }

    public function getReportedValueAttribute()
    {
        return match($this->report_type) {
            'website' => $this->website,
            'phone' => $this->phone,
            'email' => $this->reported_email,
            default => null,
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        $base = self::CATEGORY_LABELS[$this->category] ?? ucfirst(str_replace('_', ' ', $this->category));
        if ($this->category === 'other' && filled($this->category_other)) {
            return 'Other: '.$this->category_other;
        }

        return $base;
    }

    /**
     * Moderation: approved reports are shown as verified; pending await review.
     */
    public function getIsVerifiedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getVerificationLabelAttribute(): string
    {
        return $this->status === 'approved' ? 'Verified' : 'Not Verified';
    }

    /**
     * URL segment for sharing (slug + id for uniqueness).
     */
    public function seoSlug(): string
    {
        $raw = (string) ($this->reported_value ?? 'scam-report');
        $slug = Str::slug(Str::limit($raw, 60, ''));

        return $slug !== '' ? $slug.'-'.$this->id : 'report-'.$this->id;
    }

    public function scopeVisible($query)
    {
        return $query->whereIn('status', ['approved', 'pending']);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return static::query()
            ->visible()
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->firstOrFail();
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
