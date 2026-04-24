<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Blog extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'author',
        'status',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Blog $blog) {
            if (empty($blog->slug) && filled($blog->title)) {
                $blog->slug = static::uniqueSlugFromTitle((string) $blog->title);
            }
        });
    }

    public static function uniqueSlugFromTitle(string $title): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'post';
        }
        $slug = $base;
        $i = 0;
        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $i++;
            $slug = $base.'-'.$i;
        }

        return $slug;
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function featuredImageUrl(): ?string
    {
        if (empty($this->featured_image)) {
            return null;
        }

        return asset('storage/'.$this->featured_image);
    }
}
