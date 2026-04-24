<?php

use App\Services\SiteSettingsService;

if (! function_exists('site_setting')) {
    /**
     * Site-wide setting value (merged config defaults + `site_settings` table), or all values when $key is null.
     *
     * @return ($key is null ? array<string, string|null> : string|null)
     */
    function site_setting(?string $key = null, mixed $default = null): mixed
    {
        $svc = app(SiteSettingsService::class);
        if ($key === null) {
            return $svc->all();
        }

        return $svc->get($key, $default);
    }
}
