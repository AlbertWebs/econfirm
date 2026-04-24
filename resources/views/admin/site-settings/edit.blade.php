@extends('layouts.admin')

@section('title', 'Site settings')
@section('page_title', 'Site settings')

@section('content')
    @php
        $keysByGroup = [];
        foreach ($definitions as $key => $meta) {
            $g = is_array($meta) ? ($meta['group'] ?? 'general') : 'general';
            $keysByGroup[$g][] = $key;
        }

        $groupIntro = [
            'general' => 'Brand name, footer copy, and logo text used across the public site.',
            'contact' => 'Shown on the contact page and in structured data where applicable.',
            'social' => 'Full URLs for footer and JSON-LD sameAs. Leave blank to omit a network.',
            'seo' => 'Default meta tags and organization text for search engines.',
        ];

        $groupIcons = [
            'general' => 'cog',
            'contact' => 'inbox',
            'social' => 'arrow-top-right-on-square',
            'seo' => 'document',
        ];

        $anchor = static fn (string $g): string => 'site-settings-group-' . $g;
    @endphp

    <x-admin.page-title
        title="Public website content"
        description="Values are stored in the database and override config defaults. Saving updates the live site after cache refresh."
    >
        <x-slot name="actions">
            <a
                href="{{ route('home') }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-900"
            >
                <x-admin.icon name="arrow-top-right-on-square" class="h-4 w-4 text-emerald-600" />
                Preview site
            </a>
        </x-slot>
    </x-admin.page-title>

    <div class="xl:grid xl:grid-cols-[minmax(0,1fr)_13.5rem] xl:items-start xl:gap-10">
        <div class="min-w-0 space-y-8">
            <nav class="xl:hidden" aria-label="Section shortcuts">
                <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">Jump to</p>
                <div class="-mx-1 flex gap-2 overflow-x-auto pb-1">
                    @foreach ($keysByGroup as $groupKey => $_keys)
                        <a
                            href="#{{ $anchor($groupKey) }}"
                            class="shrink-0 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-900"
                        >
                            {{ $groups[$groupKey] ?? ucfirst($groupKey) }}
                        </a>
                    @endforeach
                </div>
            </nav>

            <form id="site-settings-form" method="post" action="{{ route('admin.site-settings.update') }}" class="space-y-8">
                @csrf
                @method('PUT')

                @foreach ($keysByGroup as $groupKey => $keys)
                    <x-admin.card class="scroll-mt-6" id="{{ $anchor($groupKey) }}">
                        <x-slot name="header">
                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-700 ring-1 ring-emerald-500/15">
                                    <x-admin.icon :name="$groupIcons[$groupKey] ?? 'cog'" class="h-4 w-4" />
                                </span>
                                <div class="min-w-0">
                                    <h2 class="text-sm font-semibold text-slate-900">{{ $groups[$groupKey] ?? ucfirst($groupKey) }}</h2>
                                    <p class="mt-0.5 text-xs font-normal font-medium text-slate-500">{{ $groupIntro[$groupKey] ?? '' }}</p>
                                </div>
                            </div>
                        </x-slot>

                        <div class="grid gap-5 sm:grid-cols-2">
                            @foreach ($keys as $settingKey)
                                @php
                                    $meta = $definitions[$settingKey] ?? [];
                                    $label = is_array($meta) ? ($meta['label'] ?? $settingKey) : $settingKey;
                                    $val = old('settings.'.$settingKey, $values[$settingKey] ?? '');
                                    $defaultVal = is_array($meta) ? ($meta['default'] ?? '') : '';
                                    $isMultiline = ($meta['multiline'] ?? false) === true
                                        || str_contains($settingKey, 'blurb')
                                        || str_contains($settingKey, 'address')
                                        || str_contains($settingKey, 'description')
                                        || str_contains($settingKey, 'keywords')
                                        || str_contains($settingKey, 'jsonld');
                                    $isUrl = str_ends_with($settingKey, '_url');
                                    $inputType = $isUrl ? 'url' : (str_contains($settingKey, 'email') ? 'email' : 'text');
                                    $hintId = 'hint-' . $settingKey;
                                @endphp
                                <div @class(['sm:col-span-2' => $isMultiline])>
                                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                                        <label for="setting-{{ $settingKey }}" class="text-sm font-medium text-slate-800">{{ $label }}</label>
                                        @if (str_contains($settingKey, 'blurb') || str_contains($settingKey, 'footer_about'))
                                            <span class="rounded-md bg-amber-50 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800 ring-1 ring-amber-200/80">HTML allowed</span>
                                        @endif
                                    </div>
                                    @if ($isMultiline)
                                        <textarea
                                            id="setting-{{ $settingKey }}"
                                            name="settings[{{ $settingKey }}]"
                                            rows="{{ str_contains($settingKey, 'jsonld') || str_contains($settingKey, 'blurb') ? 6 : 4 }}"
                                            class="mt-1.5 block w-full rounded-xl border-slate-300 font-mono text-sm shadow-sm transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500 @error('settings.'.$settingKey) border-red-500 focus:border-red-500 focus:ring-red-500/30 @enderror"
                                            aria-describedby="{{ $hintId }}"
                                        >{{ $val }}</textarea>
                                    @else
                                        <input
                                            type="{{ $inputType }}"
                                            id="setting-{{ $settingKey }}"
                                            name="settings[{{ $settingKey }}]"
                                            value="{{ $val }}"
                                            class="mt-1.5 block w-full rounded-xl border-slate-300 text-sm shadow-sm transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500 @error('settings.'.$settingKey) border-red-500 focus:border-red-500 focus:ring-red-500/30 @enderror"
                                            @if ($isUrl)
                                                placeholder="https://"
                                            @endif
                                            inputmode="{{ $isUrl ? 'url' : 'text' }}"
                                            aria-describedby="{{ $hintId }}"
                                        >
                                    @endif
                                    <p id="{{ $hintId }}" class="mt-1.5 text-xs leading-relaxed text-slate-500">
                                        <span class="font-medium text-slate-600">Config default:</span>
                                        @if ($defaultVal === '' || $defaultVal === null)
                                            <span class="text-slate-400">(empty)</span>
                                        @else
                                            <span class="line-clamp-2 break-words" title="{{ $defaultVal }}">{{ Str::limit($defaultVal, 160) }}</span>
                                        @endif
                                    </p>
                                    @error('settings.'.$settingKey)
                                        <p class="mt-1.5 text-sm font-medium text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </x-admin.card>
                @endforeach

                <div class="sticky bottom-0 z-10 -mx-px rounded-2xl border border-slate-200/90 bg-white/90 px-4 py-3 shadow-[0_-8px_30px_rgba(15,23,42,0.08)] backdrop-blur-md supports-[backdrop-filter]:bg-white/75 sm:-mx-1 sm:px-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-slate-500 sm:max-w-md">
                            Changes apply on save. Use <strong class="font-semibold text-slate-700">Reset</strong> below only if you need to discard all overrides and fall back to <code class="rounded bg-slate-100 px-1 py-0.5 font-mono text-[11px] text-slate-700">config/site_settings.php</code>.
                        </p>
                        <button
                            type="submit"
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 active:scale-[0.99]"
                        >
                            Save all settings
                        </button>
                    </div>
                </div>
            </form>

            <x-admin.card class="border-red-200/80 bg-gradient-to-b from-red-50/40 to-white">
                <x-slot name="header">
                    <div class="flex items-center gap-2 text-red-900">
                        <x-admin.icon name="exclamation-triangle" class="h-5 w-5 text-red-600" />
                        <span class="text-sm font-semibold">Danger zone</span>
                    </div>
                </x-slot>

                <form method="post" action="{{ route('admin.site-settings.reset') }}" class="space-y-4" onsubmit="return confirm('Remove every stored site setting from the database? The public site will use config defaults until you save this form again.');">
                    @csrf
                    <p class="text-sm leading-relaxed text-slate-600">
                        This deletes all rows in <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-xs">site_settings</code>. It does not change your config files. Use when troubleshooting or after a bad bulk import.
                    </p>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-800 shadow-sm transition hover:border-red-300 hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 focus-visible:ring-offset-2"
                    >
                        <x-admin.icon name="exclamation-triangle" class="h-4 w-4 text-red-600" />
                        Reset to config defaults
                    </button>
                </form>
            </x-admin.card>
        </div>

        <aside class="mt-10 hidden min-w-0 xl:mt-0 xl:block" aria-label="Section navigation">
            <div class="sticky top-6 space-y-3 rounded-2xl border border-slate-200/90 bg-white p-4 shadow-sm">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">On this page</p>
                <ul class="space-y-1 text-sm">
                    @foreach ($keysByGroup as $groupKey => $_keys)
                        <li>
                            <a
                                href="#{{ $anchor($groupKey) }}"
                                class="group flex items-center gap-2 rounded-lg px-2 py-1.5 text-slate-600 transition hover:bg-emerald-50 hover:text-emerald-900"
                            >
                                <x-admin.icon :name="$groupIcons[$groupKey] ?? 'cog'" class="h-4 w-4 shrink-0 text-slate-400 group-hover:text-emerald-600" />
                                <span class="min-w-0 leading-snug">{{ $groups[$groupKey] ?? ucfirst($groupKey) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="border-t border-slate-100 pt-3">
                    <button
                        type="submit"
                        form="site-settings-form"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2"
                    >
                        Save all settings
                    </button>
                </div>
            </div>
        </aside>
    </div>
@endsection
