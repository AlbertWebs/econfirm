@extends('layouts.admin')

@section('title', 'Report #'.$report->id)
@section('page_title', 'Scam report detail')

@section('content')
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.scam-reports.index', request()->query()) }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
            Back to list
        </a>
        <a href="{{ route('scam.watch.show', ['report' => $report, 'slug' => $report->seoSlug()]) }}" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 shadow-sm hover:bg-emerald-100">
            Public view
        </a>
    </div>

    @unless ($report->is_verified)
        <div class="mb-6 flex flex-col gap-3 rounded-xl border border-amber-200/90 bg-amber-50/90 p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-amber-950">Pending moderation</p>
                <p class="mt-0.5 text-sm text-amber-900/90">Approve this report to mark it <span class="font-medium">Verified</span> on the public Scam Alert page.</p>
            </div>
            <form method="post" action="{{ route('admin.scam-reports.status', $report) }}" class="shrink-0">
                @csrf
                <input type="hidden" name="status" value="approved">
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 sm:w-auto"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Approve report
                </button>
            </form>
        </div>
    @else
        <div class="mb-6 flex items-center gap-2 rounded-xl border border-emerald-200/90 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-900 shadow-sm">
            <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><span class="font-semibold">Approved</span> — shown as verified to visitors.</span>
        </div>
    @endunless

    <x-admin.card class="mb-6">
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Status</dt>
                <dd class="mt-1">
                    @if ($report->is_verified)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">approved</span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-900">pending</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Category</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ $report->category ?? '—' }}</dd>
            </div>
        </dl>
    </x-admin.card>

    <form method="post" action="{{ route('admin.scam-reports.status', $report) }}" class="mb-6">
        @csrf
        <x-admin.card>
            <h3 class="mb-1 text-sm font-semibold text-slate-900">Set status</h3>
            <p class="mb-3 text-sm text-slate-600">Or choose a status manually. Use <span class="font-mono text-xs">pending</span> to send back to the review queue.</p>
            <div class="flex max-w-md flex-col gap-2 sm:flex-row sm:items-stretch">
                <select
                    id="status"
                    name="status"
                    class="block w-full flex-1 rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="pending" @selected(old('status', $report->status) === 'pending')>Pending review</option>
                    <option value="approved" @selected(old('status', $report->status) === 'approved')>Approved (verified on site)</option>
                </select>
                <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50">Save</button>
            </div>
        </x-admin.card>
    </form>

    <x-admin.card>
        <h3 class="mb-3 text-sm font-semibold text-slate-900">Description</h3>
        <p class="whitespace-pre-wrap break-words text-sm leading-relaxed text-slate-700">{{ $report->description ?? '—' }}</p>
    </x-admin.card>
@endsection
