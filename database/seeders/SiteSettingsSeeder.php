<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('site_settings.keys', []) as $key => $meta) {
            if (! is_array($meta)) {
                continue;
            }
            $default = $meta['default'] ?? null;
            $group = $meta['group'] ?? 'general';
            SiteSetting::query()->firstOrCreate(
                ['key' => $key],
                [
                    'value' => ($default === null || $default === '') ? null : (string) $default,
                    'group' => $group,
                ]
            );
        }
    }
}
