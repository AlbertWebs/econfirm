@extends('layouts.admin')

@section('title', 'Business')
@section('page_title', 'Business — escrow profits')

@section('content')
    <x-admin.page-title title="Business & platform fees">
        <x-slot:actions>
            <a
                href="{{ route('admin.transactions.index', request()->only(['from_date', 'to_date', 'status', 'q'])) }}"
                class="inline-flex rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
                Open escrow list
            </a>
        </x-slot:actions>
    </x-admin.page-title>

    <p class="mb-6 max-w-3xl text-sm text-slate-600">
        <strong>Platform profit</strong> is the sum of <code class="rounded bg-slate-100 px-1 font-mono text-xs">transaction_fee</code> on each escrow (your fee on top of or beside the principal). Filters below apply to the totals, the month breakdown, and the table.
    </p>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Escrows (matching filters)</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($summary['escrow_count']) }}</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Principal volume (KES)</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($summary['volume_kes'], 2) }}</p>
        </x-admin.card>
        <x-admin.card class="border-emerald-200/80 bg-emerald-50/40">
            <p class="text-xs font-medium uppercase tracking-wide text-emerald-900">Platform fees / profit (KES)</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-emerald-950">{{ number_format($summary['platform_fees_kes'], 2) }}</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Avg fee per escrow (KES)</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">
                {{ $summary['escrow_count'] > 0 ? number_format($summary['platform_fees_kes'] / $summary['escrow_count'], 2) : '0.00' }}
            </p>
        </x-admin.card>
    </div>

    <div class="mb-6 grid gap-4 lg:grid-cols-2">
        <x-admin.card :flush="true">
            <x-slot:header>Fees by status</x-slot:header>
            <x-admin.table-wrap class="rounded-none border-0">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-600 sm:px-5">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold uppercase text-slate-600 sm:px-5">Escrows</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold uppercase text-slate-600 sm:px-5">Volume (KES)</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold uppercase text-slate-600 sm:px-5">Fees (KES)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($profitByStatus as $row)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-2 sm:px-5">
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">{{ $row->status ?: '—' }}</span>
                                </td>
                                <td class="px-4 py-2 text-right tabular-nums sm:px-5">{{ number_format((int) $row->escrow_count) }}</td>
                                <td class="px-4 py-2 text-right tabular-nums sm:px-5">{{ number_format((float) $row->amount_total, 2) }}</td>
                                <td class="px-4 py-2 text-right font-medium tabular-nums text-emerald-800 sm:px-5">{{ number_format((float) $row->fee_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6"><x-admin.empty-state>No matching escrows.</x-admin.empty-state></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-admin.table-wrap>
        </x-admin.card>

        <x-admin.card :flush="true">
            <x-slot:header>Fees by month</x-slot:header>
            <x-admin.table-wrap class="rounded-none border-0">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-600 sm:px-5">Month</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold uppercase text-slate-600 sm:px-5">Escrows</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold uppercase text-slate-600 sm:px-5">Fees (KES)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($profitByMonth as $row)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-2 font-mono text-xs text-slate-800 sm:px-5">{{ $row->period }}</td>
                                <td class="px-4 py-2 text-right tabular-nums sm:px-5">{{ number_format((int) $row->escrow_count) }}</td>
                                <td class="px-4 py-2 text-right font-medium tabular-nums text-emerald-800 sm:px-5">{{ number_format((float) $row->fee_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-6"><x-admin.empty-state>No monthly data.</x-admin.empty-state></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-admin.table-wrap>
        </x-admin.card>
    </div>

    <form method="get" class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
            <select name="status" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">All</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">From</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">To</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div class="sm:col-span-2 lg:col-span-2">
            <label class="mb-1 block text-xs font-medium text-slate-600">Search</label>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Ref or phone" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div class="flex flex-wrap gap-2 lg:col-span-5">
            <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Apply</button>
            <a href="{{ route('admin.business.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <x-admin.card :flush="true">
        <x-slot:header>Escrows (fee per row)</x-slot:header>
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600 sm:px-5">Ref</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600 sm:px-5">Type</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-600 sm:px-5">Principal</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-600 sm:px-5">Platform fee</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600 sm:px-5">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600 sm:px-5">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($transactions as $t)
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 font-medium text-emerald-700 sm:px-5">
                                <a href="{{ route('admin.transactions.show', $t) }}" class="hover:underline">{{ $t->transaction_id }}</a>
                            </td>
                            <td class="max-w-[10rem] truncate px-4 py-3 text-slate-600 sm:px-5" title="{{ $t->transaction_type }}">{{ $t->transaction_type }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums sm:px-5">{{ number_format((float) $t->transaction_amount, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-medium tabular-nums text-emerald-800 sm:px-5">{{ number_format((float) ($t->transaction_fee ?? 0), 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">{{ $t->status }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($t->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6"><x-admin.empty-state>No escrows for this filter.</x-admin.empty-state></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $transactions->links() }}
        </div>
    </x-admin.card>
@endsection
