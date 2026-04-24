@extends('layouts.admin')

@section('title', 'Raised disputes')
@section('page_title', 'Raised disputes')

@section('content')
    <x-admin.page-title title="Raised disputes" description="Disputes opened from transaction portals (live chat). Update mediation status as you work the case." />

    <x-admin.card class="mb-6">
        <form method="get" action="{{ route('admin.disputes.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-[12rem]">
                <label for="filter_status" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Dispute status</label>
                <select
                    id="filter_status"
                    name="status"
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                >
                    <option value="">All statuses</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @selected($filterStatus === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-900">
                Apply filter
            </button>
            @if ($filterStatus !== '')
                <a href="{{ route('admin.disputes.index') }}" class="text-sm font-medium text-emerald-700 hover:underline">Clear</a>
            @endif
        </form>
    </x-admin.card>

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Transaction</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Chat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Dispute status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Raised</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Update</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($disputes as $d)
                        @php
                            $badgeClass = match ($d->status) {
                                'Resolved' => 'bg-emerald-100 text-emerald-900',
                                'Ongoing' => 'bg-amber-100 text-amber-900',
                                default => 'bg-slate-100 text-slate-800',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 tabular-nums text-slate-600 sm:px-5">{{ $d->id }}</td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                @if ($d->transaction)
                                    <a href="{{ route('admin.transactions.show', $d->transaction) }}" class="font-medium text-emerald-700 hover:underline">{{ $d->transaction->transaction_id }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                @if ($d->liveChat)
                                    <a href="{{ route('admin.live-chats.show', $d->liveChat) }}" class="font-medium text-slate-700 hover:underline">Open chat</a>
                                    <span class="ml-1 text-xs text-slate-400">({{ $d->liveChat->status }})</span>
                                @else
                                    <span class="text-slate-400">Chat removed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 sm:px-5">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">{{ $d->status }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($d->created_at)->format('M j, Y H:i') }}</td>
                            <td class="px-4 py-3 text-right sm:px-5">
                                <form method="post" action="{{ route('admin.disputes.status', $d) }}" class="inline-flex flex-wrap items-center justify-end gap-2">
                                    @csrf
                                    <select
                                        name="status"
                                        class="max-w-[9.5rem] rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs font-medium text-slate-800 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 sm:max-w-none sm:text-sm"
                                        aria-label="Dispute status for #{{ $d->id }}"
                                    >
                                        @foreach ($statuses as $s)
                                            <option value="{{ $s }}" @selected($d->status === $s)>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="inline-flex shrink-0 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                                        Save
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-admin.empty-state>No disputes recorded yet. They appear when someone starts dispute live chat from a transaction page.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $disputes->links() }}
        </div>
    </x-admin.card>
@endsection
