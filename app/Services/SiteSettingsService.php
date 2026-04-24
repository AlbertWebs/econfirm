<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SiteSettingsService
{
    protected const CACHE_KEY = 'site_settings_flat_v1';

    protected const CACHE_TTL_SECONDS = 3600;

    /**
     * @var array<string, string|null>|null
     */
    protected ?array $runtime = null;

    /**
     * @return array<string, string|null>
     */
    public function all(): array
    {
        if ($this->runtime !== null) {
            return $this->runtime;
        }

        return $this->runtime = Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function () {
            return $this->buildMerged();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $all = $this->all();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        $this->runtime = null;
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public function syncFromRequest(array $settings): void
    {
        $definitions = config('site_settings.keys', []);
        foreach ($definitions as $key => $_meta) {
            if (! array_key_exists($key, $settings)) {
                continue;
            }
            $value = $settings[$key];
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $value = $value === null ? null : (string) $value;
            $group = is_array($_meta) ? ($_meta['group'] ?? 'general') : 'general';
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value === '' ? null : $value, 'group' => $group]
            );
        }
        $this->forgetCache();
    }

    /**
     * @return array<string, string|null>
     */
    protected function buildMerged(): array
    {
        $definitions = config('site_settings.keys', []);
        $db = SiteSetting::query()->pluck('value', 'key')->all();
        $out = [];
        foreach ($definitions as $key => $meta) {
            $default = is_array($meta) ? ($meta['default'] ?? null) : null;
            $raw = $db[$key] ?? null;
            $out[$key] = ($raw === null || $raw === '') ? $default : $raw;
        }

        return $out;
    }
}
