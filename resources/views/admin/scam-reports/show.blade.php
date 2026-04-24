@extends('layouts.admin')

@section('title', 'Report #'.$report->id)
@section('page_title', 'Scam report detail')

@section('content')
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.scam-reports.index', request()->query()) }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Back to list
        </a>
        <a href="{{ route('scam.watch.show', ['report' => $report, 'slug' => $report->seoSlug()]) }}" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 hover:bg-emerald-100">
            Public view
        </a>
    </div>

    <x-admin.card class="mb-6">
        <dl class="grid gap-4 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Status</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ $report->status ?? '—' }}</dd>
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
            <label for="status" class="mb-2 block text-sm font-medium text-slate-700">Update status</label>
            <div class="flex max-w-md flex-col gap-2 sm:flex-row sm:items-center">
                <input type="text" id="status" name="status" value="{{ old('status', $report->status) }}" required class="block w-full flex-1 rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <button type="submit" class="inline-flex shrink-0 justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Save</button>
            </div>
        </x-admin.card>
    </form>

    <x-admin.card>
        <h3 class="mb-3 text-sm font-semibold text-slate-900">Description</h3>
        <p class="whitespace-pre-wrap break-words text-sm leading-relaxed text-slate-700">{{ $report->description ?? '—' }}</p>
    </x-admin.card>
@endsection
