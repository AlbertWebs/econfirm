@extends('layouts.admin')

@section('title', 'VeliPay communications')
@section('page_title', 'VeliPay communications')

@section('content')
    <x-admin.page-title title="VeliPay communications" />

    <div class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <x-admin.card class="border border-slate-200 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Records</p>
            <p class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format($summary['count']) }}</p>
        </x-admin.card>
        <x-admin.card class="border border-slate-200 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Amount</p>
            <p class="mt-1 text-2xl font-semibold text-slate-900">KES {{ number_format($summary['amount'], 2) }}</p>
        </x-admin.card>
        <x-admin.card class="border border-slate-200 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Paid / Settled</p>
            <p class="mt-1 text-2xl font-semibold text-emerald-700">{{ number_format($summary['paid_count']) }}</p>
        </x-admin.card>
        <x-admin.card class="border border-slate-200 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500">Failed / Cancelled</p>
            <p class="mt-1 text-2xl font-semibold text-rose-700">{{ number_format($summary['failed_count']) }}</p>
        </x-admin.card>
    </div>

    <div class="mb-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <form method="get" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
                <select name="status" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm">
                    <option value="">All</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">From</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">To</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm">
            </div>
            <div class="lg:col-span-2">
                <label class="mb-1 block text-xs font-medium text-slate-600">Search</label>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Payment ID, transaction, phone, receipt" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm">
            </div>
            <div class="sm:col-span-2 lg:col-span-5 flex gap-2">
                <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Apply filters</button>
                <a href="{{ route('admin.velipay-payments.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </div>

    <x-admin.card :flush="true" class="overflow-hidden border border-slate-200 shadow-sm">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Payment ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Transaction</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Receipt</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($payments as $p)
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $p->id }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-emerald-700">
                                <a href="{{ route('admin.velipay-payments.show', $p) }}" class="hover:underline">{{ $p->velipay_payment_id }}</a>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-700">{{ $p->transaction_id ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums">KES {{ number_format((float) $p->amount, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-3"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">{{ $p->status }}</span></td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-700">{{ $p->receipt_number ?: '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ optional($p->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-admin.empty-state>No VeliPay records.</x-admin.empty-state></td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3">
            {{ $payments->links() }}
        </div>
    </x-admin.card>
@endsection
