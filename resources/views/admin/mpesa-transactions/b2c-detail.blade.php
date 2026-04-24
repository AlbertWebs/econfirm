@extends('layouts.admin')

@section('title', 'B2C #'.$mpesa_b2c->id)
@section('page_title', 'B2C payout detail')

@php
    $outcome = $mpesa_b2c->adminPayoutOutcome();
    $jsonPretty = function ($value): string {
        if ($value === null) {
            return 'null';
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            } else {
                return $value;
            }
        }

        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    };
@endphp

@section('content')
    <div class="mb-6 flex flex-wrap gap-2">
        <a
            href="{{ route('admin.mpesa-transactions.index', array_merge(request()->only(['date_from', 'date_to', 'status', 'phone', 'transaction_id']), ['tab' => 'b2c'])) }}"
            class="inline-flex rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
        >Back to B2C list</a>
    </div>

    <x-admin.card class="mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Payout status</p>
                <p class="mt-2">
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $outcome['badge_class'] }}">{{ $outcome['label'] }}</span>
                </p>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">{{ $outcome['sublabel'] }}</p>
            </div>
            <dl class="grid gap-2 text-right text-sm sm:text-left">
                <div><dt class="text-xs text-slate-500">Row ID</dt><dd class="font-mono text-slate-900">{{ $mpesa_b2c->id }}</dd></div>
                <div><dt class="text-xs text-slate-500">Amount (KES)</dt><dd class="tabular-nums text-slate-900">{{ number_format($mpesa_b2c->displayAmountKes(), 2) }}</dd></div>
                <div><dt class="text-xs text-slate-500">Recipient</dt><dd class="text-slate-900">{{ $mpesa_b2c->receiver_mobile ?: $mpesa_b2c->party_b ?: '—' }}</dd></div>
            </dl>
        </div>
        <hr class="my-4 border-slate-200">
        <dl class="grid gap-3 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-xs font-medium text-slate-500">Transaction ID (app)</dt>
                <dd class="mt-0.5 font-mono text-xs text-slate-800">{{ $mpesa_b2c->transaction_id ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-slate-500">Row status</dt>
                <dd class="mt-0.5 text-slate-800">{{ $mpesa_b2c->status ?? '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-medium text-slate-500">Originator conversation ID</dt>
                <dd class="mt-0.5 break-all font-mono text-xs text-slate-800">{{ $mpesa_b2c->originator_conversation_id ?: '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-medium text-slate-500">Conversation ID</dt>
                <dd class="mt-0.5 break-all font-mono text-xs text-slate-800">{{ $mpesa_b2c->conversation_id ?: '—' }}</dd>
            </div>
        </dl>
    </x-admin.card>

    <x-admin.card class="mb-6">
        <h2 class="text-sm font-semibold text-slate-900">Daraja initiate response (<code class="rounded bg-slate-100 px-1 font-mono text-xs">mpesa_b2c.raw_response</code>)</h2>
        <pre class="mt-3 max-h-[28rem] overflow-auto rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs text-slate-800">{!! $jsonPretty($mpesa_b2c->raw_response) !!}</pre>
    </x-admin.card>

    <x-admin.card>
        <h2 class="text-sm font-semibold text-slate-900">M-Pesa result callbacks (<code class="rounded bg-slate-100 px-1 font-mono text-xs">mpesa_b2c_callbacks</code>)</h2>
        @if ($callbacks->isEmpty())
            <p class="mt-3 text-sm text-slate-600">No callback rows matched this payout’s conversation IDs yet.</p>
        @else
            <div class="mt-4 space-y-6">
                @foreach ($callbacks as $cb)
                    <div class="rounded-lg border border-slate-200 bg-white p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-mono text-xs text-slate-600">Callback #{{ $cb->id }} · {{ optional($cb->created_at)->format('Y-m-d H:i:s') }}</p>
                            <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">
                                result {{ $cb->result_code ?? '—' }}
                            </span>
                        </div>
                        <dl class="mt-3 grid gap-2 text-xs sm:grid-cols-2">
                            <div><dt class="text-slate-500">Receipt</dt><dd class="font-mono text-slate-900">{{ $cb->transaction_receipt ?? '—' }}</dd></div>
                            <div class="sm:col-span-2"><dt class="text-slate-500">Description</dt><dd class="text-slate-800">{{ $cb->result_desc ?? '—' }}</dd></div>
                        </dl>
                        <p class="mt-3 text-xs font-medium text-slate-500">Raw callback payload</p>
                        <pre class="mt-1 max-h-[24rem] overflow-auto rounded-lg border border-slate-100 bg-slate-50 p-3 font-mono text-xs text-slate-800">{!! $jsonPretty($cb->raw_callback) !!}</pre>
                    </div>
                @endforeach
            </div>
        @endif
    </x-admin.card>
@endsection
