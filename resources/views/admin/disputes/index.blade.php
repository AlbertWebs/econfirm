@extends('layouts.admin')

@section('title', 'Raised disputes')
@section('page_title', 'Raised disputes')

@php
    $filterLink = fn (?string $status) => route('admin.disputes.index', array_filter(['status' => $status]));
    $escrowStatusBadge = function (?string $raw): string {
        $s = mb_strtolower(trim((string) $raw), 'UTF-8');
        if ($s === '') {
            return 'bg-slate-100 text-slate-500 ring-1 ring-slate-200';
        }
        if (str_contains($s, 'fund') || str_contains($s, 'complet') || str_contains($s, 'success') || str_contains($s, 'paid')) {
            return 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-100';
        }
        if (str_contains($s, 'pending') || str_contains($s, 'stk')) {
            return 'bg-amber-50 text-amber-900 ring-1 ring-amber-100';
        }
        if (str_contains($s, 'dispute') || str_contains($s, 'conflict')) {
            return 'bg-orange-50 text-orange-900 ring-1 ring-orange-100';
        }
        if (str_contains($s, 'fail') || str_contains($s, 'cancel')) {
            return 'bg-rose-50 text-rose-800 ring-1 ring-rose-100';
        }

        return 'bg-slate-100 text-slate-600 ring-1 ring-slate-200';
    };
    $chatStatusBadge = function (?string $raw): string {
        $s = mb_strtolower(trim((string) $raw), 'UTF-8');
        if ($s === '') {
            return 'bg-slate-100 text-slate-500 ring-1 ring-slate-200';
        }
        if (str_contains($s, 'open') || str_contains($s, 'active') || str_contains($s, 'live')) {
            return 'bg-emerald-50 text-emerald-800 ring-1 ring-emerald-100';
        }
        if (str_contains($s, 'close') || str_contains($s, 'end') || str_contains($s, 'resolved')) {
            return 'bg-slate-100 text-slate-600 ring-1 ring-slate-200';
        }

        return 'bg-sky-50 text-sky-900 ring-1 ring-sky-100';
    };
@endphp

@section('content')
    <x-admin.page-title
        title="Raised disputes"
        description="Cases opened from transaction dispute chat. Filter by mediation status, open the live thread, and update the record as the case moves forward."
    />

    <div class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">All disputes</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-slate-900">{{ number_format($summary['total']) }}</p>
        </x-admin.card>
        <x-admin.card class="border-sky-200/80 bg-sky-50/30">
            <p class="text-xs font-medium uppercase tracking-wide text-sky-800">Created</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-sky-950">{{ number_format($summary['created']) }}</p>
            <p class="mt-0.5 text-xs text-sky-800/80">New — not yet in active mediation</p>
        </x-admin.card>
        <x-admin.card class="border-amber-200/80 bg-amber-50/35">
            <p class="text-xs font-medium uppercase tracking-wide text-amber-900">Ongoing</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-amber-950">{{ number_format($summary['ongoing']) }}</p>
            <p class="mt-0.5 text-xs text-amber-900/80">Actively being worked</p>
        </x-admin.card>
        <x-admin.card class="border-emerald-200/80 bg-emerald-50/35">
            <p class="text-xs font-medium uppercase tracking-wide text-emerald-900">Resolved</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-emerald-950">{{ number_format($summary['resolved']) }}</p>
            <p class="mt-0.5 text-xs text-emerald-900/80">Closed outcomes</p>
        </x-admin.card>
    </div>

    <x-admin.card class="mb-4">
        <p class="mb-3 text-xs font-medium uppercase tracking-wide text-slate-500">Quick filter</p>
        <div class="flex flex-wrap gap-2">
            <a
                href="{{ $filterLink(null) }}"
                class="inline-flex rounded-full px-3 py-1.5 text-sm font-medium transition {{ $filterStatus === '' ? 'bg-emerald-700 text-white shadow-sm' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
            >All</a>
            @foreach ($statuses as $s)
                <a
                    href="{{ $filterLink($s) }}"
                    class="inline-flex rounded-full px-3 py-1.5 text-sm font-medium transition {{ $filterStatus === $s ? 'bg-emerald-700 text-white shadow-sm' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
                >{{ $s }}</a>
            @endforeach
        </div>
    </x-admin.card>

    <x-admin.card class="mb-6">
        <form method="get" action="{{ route('admin.disputes.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-[14rem] flex-1">
                <label for="filter_status" class="mb-1 block text-xs font-medium text-slate-600">Dispute status</label>
                <select
                    id="filter_status"
                    name="status"
                    class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="">All statuses</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @selected($filterStatus === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                    Apply
                </button>
                @if ($filterStatus !== '')
                    <a href="{{ route('admin.disputes.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </x-admin.card>

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-4">ID</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-4">Escrow</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-4">Live chat</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-4">Dispute status</th>
                        <th class="px-3 py-2.5 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-4">Timeline</th>
                        <th class="px-3 py-2.5 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($disputes as $d)
                        <tr class="align-top hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-3 py-3 font-mono text-xs text-slate-600 sm:px-4">#{{ $d->id }}</td>
                            <td class="max-w-[14rem] px-3 py-3 sm:max-w-none sm:px-4">
                                @if ($d->transaction)
                                    <a href="{{ route('admin.transactions.show', $d->transaction) }}" class="inline-flex items-center gap-1 font-mono text-sm font-semibold text-emerald-700 hover:text-emerald-800 hover:underline">
                                        {{ $d->transaction->transaction_id }}
                                        <x-admin.icon name="arrow-top-right-on-square" class="h-3.5 w-3.5 shrink-0 opacity-70" />
                                    </a>
                                    <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium leading-tight {{ $escrowStatusBadge($d->transaction->status) }}">{{ $d->transaction->status }}</span>
                                        <span class="text-xs tabular-nums text-slate-600">KES {{ number_format((float) $d->transaction->transaction_amount, 2) }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 sm:px-4">
                                @if ($d->liveChat)
                                    <a
                                        href="{{ route('admin.live-chats.show', $d->liveChat) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-800 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50/50"
                                    >
                                        <x-admin.icon name="chat" class="h-4 w-4 text-emerald-600" />
                                        Open chat
                                    </a>
                                    <div class="mt-1.5">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium leading-tight {{ $chatStatusBadge($d->liveChat->status) }}">{{ $d->liveChat->status }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">Chat removed</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 sm:px-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $d->adminStatusBadgeClass() }}">{{ $d->status }}</span>
                            </td>
                            <td class="px-3 py-3 text-xs text-slate-600 sm:px-4">
                                <div class="space-y-1">
                                    <p><span class="font-medium text-slate-500">Opened</span> {{ optional($d->created_at)->format('M j, Y · H:i') }}</p>
                                    <p><span class="font-medium text-slate-500">Updated</span> {{ optional($d->updated_at)->format('M j, Y · H:i') }}</p>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-right sm:px-4">
                                <form method="post" action="{{ route('admin.disputes.status', $d) }}" class="inline-flex flex-col items-stretch gap-2 sm:items-end">
                                    @csrf
                                    <label class="sr-only" for="dispute-status-{{ $d->id }}">Change dispute status</label>
                                    <select
                                        id="dispute-status-{{ $d->id }}"
                                        name="status"
                                        class="min-w-[10rem] rounded-lg border-slate-300 text-xs shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                                    >
                                        @foreach ($statuses as $s)
                                            <option value="{{ $s }}" @selected($d->status === $s)>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="inline-flex justify-center rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700">
                                        Save status
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6">
                                <x-admin.empty-state>No disputes yet. They appear when someone opens dispute live chat from an escrow transaction.</x-admin.empty-state>
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
