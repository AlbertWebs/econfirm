@extends('layouts.admin')

@section('title', 'Scam reports')
@section('page_title', 'Scam reports')

@section('content')
    <x-admin.page-title
        title="Scam reports"
        description="Review Scam Alert submissions. Approving marks a report as verified for the public site."
    />

    <form method="get" class="mb-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
        <div class="min-w-0 flex-1 sm:max-w-md">
            <label class="mb-1 block text-xs font-medium text-slate-600">Search</label>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Text / email / phone" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div class="w-full sm:max-w-[14rem]">
            <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
            <select name="status" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="" @selected(! request()->filled('status'))>All statuses</option>
                <option value="pending" @selected(request('status') === 'pending')>Pending review</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
            </select>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Filter</button>
            <a href="{{ route('admin.scam-reports.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($reports as $r)
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                <a href="{{ route('admin.scam-reports.show', $r) }}" class="font-medium text-emerald-700 hover:underline">#{{ $r->id }}</a>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 sm:px-5">{{ $r->category ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                @if ($r->is_verified)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Approved</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-900">Pending</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($r->created_at)->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 sm:px-5">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.scam-reports.show', $r) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 shadow-sm transition hover:bg-slate-50">View</a>
                                    @unless ($r->is_verified)
                                        <form method="post" action="{{ route('admin.scam-reports.status', $r) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1"
                                            >
                                                Approve
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-admin.empty-state>No reports.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $reports->links() }}
        </div>
    </x-admin.card>
@endsection
