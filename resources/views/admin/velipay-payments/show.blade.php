@extends('layouts.admin')

@section('title', 'VeliPay #'.$payment->id)
@section('page_title', 'VeliPay communication detail')

@section('content')
    <x-admin.page-title title="VeliPay communication detail">
        <x-slot:actions>
            <a href="{{ route('admin.velipay-payments.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Back</a>
        </x-slot:actions>
    </x-admin.page-title>

    <div class="grid gap-6 lg:grid-cols-2">
        <x-admin.card class="border border-slate-200 shadow-sm">
            <h3 class="mb-3 text-sm font-semibold text-slate-900">Record</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Payment ID</dt><dd class="font-mono text-xs text-slate-800">{{ $payment->velipay_payment_id }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Transaction ID</dt><dd class="font-mono text-xs text-slate-800">{{ $payment->transaction_id ?? '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Status</dt><dd class="text-slate-800">{{ $payment->status }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Amount</dt><dd class="tabular-nums text-slate-800">KES {{ number_format((float) $payment->amount, 2) }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Phone</dt><dd class="text-slate-800">{{ $payment->phone ?: '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Merchant Ref</dt><dd class="font-mono text-xs text-slate-800">{{ $payment->merchant_reference ?: '—' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Receipt</dt><dd class="font-mono text-xs text-slate-800">{{ $payment->receipt_number ?: '—' }}</dd></div>
            </dl>
        </x-admin.card>

        <x-admin.card class="border border-slate-200 shadow-sm">
            <h3 class="mb-3 text-sm font-semibold text-slate-900">Raw API response</h3>
            <pre class="max-h-[28rem] overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs text-slate-800">{{ json_encode($payment->raw_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: 'null' }}</pre>
        </x-admin.card>

        <x-admin.card class="lg:col-span-2 border border-slate-200 shadow-sm">
            <h3 class="mb-3 text-sm font-semibold text-slate-900">Latest webhook payload</h3>
            <pre class="max-h-[32rem] overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs text-slate-800">{{ json_encode($payment->webhook_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: 'null' }}</pre>
        </x-admin.card>
    </div>
@endsection
