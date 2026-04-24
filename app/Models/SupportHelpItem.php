<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SupportHelpItem extends Model
{
    public const KIND_QUICK_HELP = 'quick_help';

    public const KIND_HELP_FAQ = 'help_faq';

    protected $fillable = [
        'kind',
        'ref_key',
        'title',
        'body',
        'icon',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeQuickHelp(Builder $query): Builder
    {
        return $query->where('kind', self::KIND_QUICK_HELP);
    }

    public function scopeHelpFaq(Builder $query): Builder
    {
        return $query->where('kind', self::KIND_HELP_FAQ);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @return array<string, string>
     */
    public static function kindLabels(): array
    {
        return [
            self::KIND_QUICK_HELP => 'Quick Help (Support page)',
            self::KIND_HELP_FAQ => 'Help FAQs (Help page)',
        ];
    }
}
