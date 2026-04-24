@extends('layouts.admin')

@section('title', 'Transaction '.$transaction->transaction_id)
@section('page_title', 'Escrow detail')

@section('content')
    @php
        $status = strtolower((string) $transaction->status);
        $statusClass = match (true) {
            str_contains($status, 'complete'), str_contains($status, 'success'), str_contains($status, 'approved') => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            str_contains($status, 'pending'), str_contains($status, 'processing') => 'bg-amber-100 text-amber-800 ring-amber-200',
            str_contains($status, 'reject'), str_contains($status, 'fail'), str_contains($status, 'cancel'), str_contains($status, 'dispute') => 'bg-rose-100 text-rose-800 ring-rose-200',
            default => 'bg-slate-100 text-slate-700 ring-slate-200',
        };
    @endphp

    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.transactions.index', request()->query()) }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Back to list
        </a>
        <a href="{{ route('transaction.index', ['id' => $transaction->transaction_id]) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800 hover:bg-emerald-100">
            Public page
        </a>
        <form method="POST" action="{{ route('admin.transactions.destroy', $transaction) }}" onsubmit="return confirm('Delete transaction {{ $transaction->transaction_id }}? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-100">
                Delete
            </button>
        </form>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.card class="border border-slate-200">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Reference</p>
            <p class="mt-2 break-all font-mono text-sm text-slate-900">{{ $transaction->transaction_id }}</p>
        </x-admin.card>
        <x-admin.card class="border border-slate-200">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Amount</p>
            <p class="mt-2 text-2xl font-semibold tabular-nums text-slate-900">KES {{ number_format((float) $transaction->transaction_amount, 2) }}</p>
        </x-admin.card>
        <x-admin.card class="border border-slate-200">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Status</p>
            <div class="mt-2">
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                    {{ $transaction->status ?: 'Unknown' }}
                </span>
            </div>
        </x-admin.card>
        <x-admin.card class="border border-slate-200">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Created</p>
            <p class="mt-2 text-sm font-medium text-slate-900">{{ optional($transaction->created_at)->format('Y-m-d H:i') ?? '—' }}</p>
            <p class="mt-1 text-xs text-slate-500">Updated: {{ optional($transaction->updated_at)->format('Y-m-d H:i') ?? '—' }}</p>
        </x-admin.card>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-admin.card>
            <h3 class="mb-4 text-sm font-semibold text-slate-900">Core transaction data</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">DB id</dt>
                    <dd class="font-mono text-slate-900">{{ $transaction->id }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">transaction_id</dt>
                    <dd class="break-all text-right font-mono text-slate-900">{{ $transaction->transaction_id }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Status</dt>
                    <dd class="text-slate-900">{{ $transaction->status }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Type</dt>
                    <dd class="text-slate-900">{{ $transaction->transaction_type }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Amount</dt>
                    <dd class="tabular-nums text-slate-900">KES {{ number_format((float) $transaction->transaction_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Fee</dt>
                    <dd class="text-slate-900">{{ $transaction->transaction_fee }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Payment</dt>
                    <dd class="break-all text-right text-slate-900">{{ $transaction->payment_method }} / {{ $transaction->paybill_till_number ?? '—' }}</dd>
                </div>
            </dl>
        </x-admin.card>

        <x-admin.card>
            <h3 class="mb-4 text-sm font-semibold text-slate-900">Participants & request tracing</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Sender</dt>
                    <dd class="text-slate-900">{{ $transaction->sender_mobile }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Receiver</dt>
                    <dd class="text-slate-900">{{ $transaction->receiver_mobile }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-b border-slate-100 pb-2">
                    <dt class="text-slate-500">Checkout req.</dt>
                    <dd class="break-all text-right font-mono text-xs text-slate-700">{{ $transaction->checkout_request_id ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Merchant req.</dt>
                    <dd class="break-all text-right font-mono text-xs text-slate-700">{{ $transaction->merchant_request_id ?? '—' }}</dd>
                </div>
            </dl>
        </x-admin.card>

        <x-admin.card class="lg:col-span-2">
            <h3 class="mb-2 text-sm font-semibold text-slate-900">Transaction notes/details</h3>
            <div class="rounded-lg border border-slate-200 bg-slate-50/70 p-3">
                <p class="break-words text-sm leading-relaxed text-slate-700">{{ $transaction->transaction_details ?? '—' }}</p>
            </div>
        </x-admin.card>

        <x-admin.card class="lg:col-span-2" :flush="true">
            <x-slot:header>
                <div class="flex items-center justify-between gap-3">
                    <span>M-Pesa STK activity</span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                        {{ $stkRows->count() }} {{ \Illuminate\Support\Str::plural('record', $stkRows->count()) }}
                    </span>
                </div>
            </x-slot:header>
            <x-admin.table-wrap class="rounded-none border-0">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-600">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-600">Phone</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-600">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-600">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase text-slate-600">CheckoutRequestID</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($stkRows as $s)
                            <tr class="hover:bg-slate-50/80">
                                <td class="whitespace-nowrap px-4 py-2">{{ $s->id }}</td>
                                <td class="whitespace-nowrap px-4 py-2">{{ $s->phone }}</td>
                                <td class="whitespace-nowrap px-4 py-2 tabular-nums">KES {{ number_format((float) $s->amount, 2) }}</td>
                                <td class="whitespace-nowrap px-4 py-2">
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ $s->status }}</span>
                                </td>
                                <td class="max-w-xs break-all px-4 py-2 font-mono text-xs">{{ $s->checkout_request_id }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-admin.empty-state>No matching STK rows.</x-admin.empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-admin.table-wrap>
        </x-admin.card>
    </div>
@endsection
