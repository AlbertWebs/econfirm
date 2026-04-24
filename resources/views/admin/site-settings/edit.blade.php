@extends('layouts.admin')

@section('title', 'Site settings')
@section('page_title', 'Site settings')

@section('content')
    <x-admin.page-title title="Site settings" />

    <p class="mb-6 max-w-3xl text-sm text-slate-600">
        These values power the public site footer, meta defaults, social links, and JSON-LD organization data. Leave optional fields empty only when you intend to use the configured default.
    </p>

    <form method="post" action="{{ route('admin.site-settings.update') }}" class="space-y-8">
        @csrf
        @method('PUT')

        @php
            $keysByGroup = [];
            foreach ($definitions as $key => $meta) {
                $g = is_array($meta) ? ($meta['group'] ?? 'general') : 'general';
                $keysByGroup[$g][] = $key;
            }
        @endphp

        @foreach ($keysByGroup as $groupKey => $keys)
            <x-admin.card>
                <h2 class="mb-4 text-base font-semibold text-slate-900">{{ $groups[$groupKey] ?? ucfirst($groupKey) }}</h2>
                <div class="space-y-5">
                    @foreach ($keys as $settingKey)
                        @php
                            $meta = $definitions[$settingKey] ?? [];
                            $label = is_array($meta) ? ($meta['label'] ?? $settingKey) : $settingKey;
                            $val = old('settings.'.$settingKey, $values[$settingKey] ?? '');
                        @endphp
                        <div>
                            <label for="setting-{{ $settingKey }}" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $label }}</label>
                            @if (($meta['multiline'] ?? false) === true || str_contains($settingKey, 'blurb') || str_contains($settingKey, 'address') || str_contains($settingKey, 'description') || str_contains($settingKey, 'keywords') || str_contains($settingKey, 'jsonld'))
                                <textarea
                                    id="setting-{{ $settingKey }}"
                                    name="settings[{{ $settingKey }}]"
                                    rows="4"
                                    class="block w-full rounded-lg border-slate-300 font-mono text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('settings.'.$settingKey) border-red-500 @enderror"
                                >{{ $val }}</textarea>
                            @else
                                <input
                                    type="{{ str_contains($settingKey, 'email') ? 'email' : 'text' }}"
                                    id="setting-{{ $settingKey }}"
                                    name="settings[{{ $settingKey }}]"
                                    value="{{ $val }}"
                                    class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('settings.'.$settingKey) border-red-500 @enderror"
                                >
                            @endif
                            @error('settings.'.$settingKey)
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </x-admin.card>
        @endforeach

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                Save settings
            </button>
        </div>
    </form>

    <form method="post" action="{{ route('admin.site-settings.reset') }}" class="mt-10 border-t border-slate-200 pt-8" onsubmit="return confirm('Remove all stored site settings and fall back to config defaults?');">
        @csrf
        <p class="mb-3 text-sm text-slate-600">Clear every saved value from the database. The public site will use defaults from <code class="rounded bg-slate-100 px-1 py-0.5 text-xs">config/site_settings.php</code> until you save the form again.</p>
        <button type="submit" class="inline-flex rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50">
            Reset to config defaults
        </button>
    </form>
@endsection
