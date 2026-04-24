@extends('layouts.admin')

@php
    $typeLabel = match ($report->report_type ?? '') {
        'website' => 'Website',
        'phone' => 'Phone number',
        'email' => 'Email address',
        default => ucfirst((string) ($report->report_type ?? '—')),
    };
    $subjectLabel = match ($report->report_type ?? '') {
        'website' => 'Reported website / URL',
        'phone' => 'Reported phone',
        'email' => 'Reported email',
        default => 'Reported subject',
    };
    $evidenceCount = is_array($report->evidence) ? count(array_filter($report->evidence, fn ($p) => is_string($p) && $p !== '')) : 0;
@endphp

@section('title', 'Report #'.$report->id)
@section('page_title', 'Scam report #'.$report->id)

@section('content')
    <div class="space-y-6">
        {{-- Page intro + actions --}}
        <div class="flex flex-col gap-4 border-b border-slate-200/80 pb-6 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Scam Alert moderation</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Report #{{ $report->id }}</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-600">
                    <span class="font-medium text-slate-800">{{ $report->category_label }}</span>
                    <span class="text-slate-400"> · </span>
                    Submitted {{ optional($report->created_at)->timezone(config('app.timezone'))->format('M j, Y \a\t g:i a') }}
                </p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-2">
                <a
                    href="{{ route('admin.scam-reports.index', request()->query()) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
                >
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to list
                </a>
                <a
                    href="{{ route('scam.watch.show', ['report' => $report, 'slug' => $report->seoSlug()]) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm font-semibold text-emerald-900 shadow-sm transition hover:bg-emerald-100"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Public page
                </a>
            </div>
        </div>

        @unless ($report->is_verified)
            <div
                class="relative overflow-hidden rounded-2xl border border-amber-200/90 bg-gradient-to-br from-amber-50 via-amber-50/80 to-orange-50/40 p-5 shadow-sm sm:p-6"
                role="region"
                aria-label="Moderation"
            >
                <div class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-amber-200/30 blur-2xl" aria-hidden="true"></div>
                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-800">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-amber-950">Awaiting your review</p>
                            <p class="mt-1 text-sm leading-relaxed text-amber-900/90">
                                Approve to show this report as <span class="font-medium">Verified</span> on the public Scam Alert.
                            </p>
                        </div>
                    </div>
                    <form
                        method="post"
                        action="{{ route('admin.scam-reports.status', $report) }}"
                        class="w-full shrink-0 sm:w-auto sm:min-w-[12rem]"
                        onsubmit="return confirm('Approve this listing? It will be shown as verified on the public Scam Alert page.');"
                    >
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button
                            type="submit"
                            title="Publish as verified listing on the public Scam Alert page"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Approve listing
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="flex items-start gap-3 rounded-2xl border border-emerald-200/90 bg-emerald-50/90 px-4 py-4 shadow-sm sm:items-center sm:px-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm leading-relaxed text-emerald-950">
                    <span class="font-semibold">Approved</span>
                    — visitors see this report as verified on Scam Alert.
                </p>
            </div>
        @endunless

        {{-- Key fields --}}
        <x-admin.card>
            <h2 class="text-base font-semibold text-slate-900">Report details</h2>
            <p class="mt-1 text-sm text-slate-600">What was reported and how to reach the submitter.</p>
            <dl class="mt-6 grid gap-5 text-sm sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Status</dt>
                    <dd class="mt-2">
                        @if ($report->is_verified)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Verified (approved)</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-900">Pending review</span>
                        @endif
                    </dd>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Report type</dt>
                    <dd class="mt-2 font-medium text-slate-900">{{ $typeLabel }}</dd>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4 sm:col-span-2 lg:col-span-1">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $subjectLabel }}</dt>
                    <dd class="mt-2 break-all font-mono text-sm font-medium text-slate-900">{{ $report->reported_value ?? '—' }}</dd>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4 sm:col-span-2">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Category</dt>
                    <dd class="mt-2 text-slate-900">{{ $report->category_label }}</dd>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Date of incident</dt>
                    <dd class="mt-2 text-slate-900">{{ $report->date_of_incident ? $report->date_of_incident->format('M j, Y') : '—' }}</dd>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Reporter email</dt>
                    <dd class="mt-2 break-all text-slate-900">{{ $report->email ?? '—' }}</dd>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Reporter phone</dt>
                    <dd class="mt-2 font-mono text-slate-900">{{ $report->reporter_phone ?? '—' }}</dd>
                </div>
                <div class="rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Report count</dt>
                    <dd class="mt-2 tabular-nums text-slate-900">{{ (int) ($report->report_count ?? 1) }}</dd>
                </div>
            </dl>
        </x-admin.card>

        {{-- Description --}}
        <x-admin.card>
            <h2 class="text-base font-semibold text-slate-900">Narrative</h2>
            <p class="mt-1 text-sm text-slate-600">Description from the submission form.</p>
            <div class="mt-4 rounded-xl border border-slate-200/90 bg-slate-50/50 p-4 sm:p-5">
                <p class="whitespace-pre-wrap break-words text-sm leading-relaxed text-slate-800">{{ $report->description ?? '—' }}</p>
            </div>
        </x-admin.card>

        {{-- Evidence --}}
        @if ($evidenceCount > 0)
            <x-admin.card>
                <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                    <h2 class="text-base font-semibold text-slate-900">Evidence</h2>
                    <span class="text-xs font-medium text-slate-500">{{ $evidenceCount }} file{{ $evidenceCount === 1 ? '' : 's' }} · admin only</span>
                </div>
                <p class="mt-1 text-sm text-slate-600">Images and PDFs preview here; other formats open on download.</p>
                <ul class="mt-5 space-y-4">
                    @foreach ($report->evidence as $i => $path)
                        @if (! is_string($path) || $path === '')
                            @continue
                        @endif
                        @php $kind = $report->evidenceDisplayKindForPath($path); @endphp
                        <li class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-200/50">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-slate-50/80 px-4 py-2.5">
                                <span class="flex min-w-0 items-center gap-2 text-sm font-medium text-slate-800">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-200/80 text-xs font-bold text-slate-600" aria-hidden="true">{{ $i + 1 }}</span>
                                    <span class="truncate" title="{{ basename($path) }}">{{ basename($path) }}</span>
                                </span>
                                <a
                                    href="{{ route('admin.scam-reports.evidence', [$report, $i]) }}"
                                    class="shrink-0 text-sm font-semibold text-emerald-700 hover:text-emerald-800 hover:underline"
                                    target="_blank"
                                    rel="noopener"
                                >Open</a>
                            </div>
                            <div class="p-3 sm:p-4">
                                @if ($kind === 'image')
                                    <img
                                        src="{{ route('admin.scam-reports.evidence', [$report, $i]) }}"
                                        alt="Evidence file {{ $i + 1 }}"
                                        class="max-h-[min(70vh,32rem)] w-full max-w-3xl rounded-lg border border-slate-200/90 bg-white object-contain shadow-sm"
                                        loading="lazy"
                                    >
                                @elseif ($kind === 'pdf')
                                    <iframe
                                        title="PDF: {{ basename($path) }}"
                                        src="{{ route('admin.scam-reports.evidence', [$report, $i]) }}#view=FitH"
                                        class="h-[min(70vh,36rem)] w-full min-h-[16rem] rounded-lg border border-slate-200 bg-slate-50"
                                    ></iframe>
                                @else
                                    <p class="text-sm text-slate-600">
                                        Preview not available for this type.
                                        <a href="{{ route('admin.scam-reports.evidence', [$report, $i]) }}" class="font-semibold text-emerald-700 hover:underline" download>Download file</a>
                                    </p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </x-admin.card>
        @else
            <x-admin.card>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Evidence</h2>
                        <p class="mt-0.5 text-sm text-slate-600">No files were attached to this report.</p>
                    </div>
                </div>
            </x-admin.card>
        @endif

        {{-- Status control --}}
        <form method="post" action="{{ route('admin.scam-reports.status', $report) }}">
            @csrf
            <x-admin.card class="border-dashed border-slate-200 bg-slate-50/40">
                <h2 class="text-sm font-semibold text-slate-900">Update status</h2>
                <p class="mt-0.5 text-sm text-slate-600">Switch between <span class="font-medium">pending</span> and <span class="font-medium">approved</span> if you need to correct a decision.</p>
                <div class="mt-4 flex max-w-lg flex-col gap-3 sm:flex-row sm:items-stretch">
                    <label class="sr-only" for="status">Status</label>
                    <select
                        id="status"
                        name="status"
                        class="block w-full flex-1 rounded-xl border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    >
                        <option value="pending" @selected(old('status', $report->status) === 'pending')>Pending review</option>
                        <option value="approved" @selected(old('status', $report->status) === 'approved')>Approved (verified on site)</option>
                    </select>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:bg-slate-50 sm:shrink-0"
                    >
                        Save status
                    </button>
                </div>
            </x-admin.card>
        </form>
    </div>
@endsection
