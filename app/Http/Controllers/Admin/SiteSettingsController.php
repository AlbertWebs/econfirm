<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\AdminActivityLogger;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingsController extends Controller
{
    public function edit(): View
    {
        $definitions = config('site_settings.keys', []);
        $groups = config('site_settings.groups', []);
        $values = app(SiteSettingsService::class)->all();

        return view('admin.site-settings.edit', compact('definitions', 'groups', 'values'));
    }

    public function update(Request $request): RedirectResponse
    {
        $definitions = config('site_settings.keys', []);
        $rules = ['settings' => ['required', 'array']];
        foreach ($definitions as $key => $meta) {
            $rules['settings.'.$key] = is_array($meta) && isset($meta['rules'])
                ? $meta['rules']
                : ['nullable', 'string', 'max:2000'];
        }

        $validated = $request->validate($rules);
        app(SiteSettingsService::class)->syncFromRequest($validated['settings'] ?? []);
        AdminActivityLogger::log('site_settings.updated');

        return redirect()->route('admin.site-settings.edit')->with('status', 'Site settings saved.');
    }

    public function reset(): RedirectResponse
    {
        SiteSetting::query()->delete();
        app(SiteSettingsService::class)->forgetCache();
        AdminActivityLogger::log('site_settings.reset');

        return redirect()->route('admin.site-settings.edit')->with('status', 'Stored settings were removed. Config defaults apply until you save again.');
    }
}
