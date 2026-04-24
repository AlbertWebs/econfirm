@extends('layouts.admin')

@section('title', 'SMS logs')
@section('page_title', 'SMS logs')

@section('content')
    <x-admin.page-title title="SMS logs">
        <x-slot:actions>
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                Back to dashboard
            </a>
        </x-slot:actions>
    </x-admin.page-title>

    <form method="get" class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-5 lg:items-end">
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
            <select name="status" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">All</option>
                <option value="success" @selected(request('status') === 'success')>Success</option>
                <option value="failed" @selected(request('status') === 'failed')>Failed</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="mb-1 block text-xs font-medium text-slate-600">Search</label>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Phone, correlator, provider id..." class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div class="flex gap-2 sm:col-span-3 lg:col-span-2">
            <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Filter</button>
            <a href="{{ route('admin.sms-logs.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Recipient</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Correlator</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Provider ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Message details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($logs as $log)
                        <tr class="align-top hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-800 sm:px-5">{{ $log->recipient }}</td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $log->is_success ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $log->is_success ? 'Success' : 'Failed' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-700 sm:px-5">{{ $log->correlator ?: '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-700 sm:px-5">{{ $log->provider_unique_id ?: '—' }}</td>
                            <td class="min-w-[24rem] px-4 py-3 sm:px-5">
                                <p class="line-clamp-2 text-xs text-slate-700">{{ $log->message }}</p>
                                @if(filled($log->provider_message))
                                    <p class="mt-1 text-xs text-slate-500">Provider: {{ $log->provider_message }}</p>
                                @endif
                                @if($log->http_code)
                                    <p class="mt-1 text-[11px] text-slate-400">HTTP {{ $log->http_code }}</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-admin.empty-state>No SMS logs yet.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $logs->links() }}
        </div>
    </x-admin.card>
@endsection
