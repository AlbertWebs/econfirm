@extends('layouts.admin')

@section('title', 'M-PESA transactions')
@section('page_title', 'M-PESA transactions')

@php
    $tabLink = fn (string $t) => route('admin.mpesa-transactions.index', array_merge(request()->only(['date_from', 'date_to', 'status', 'phone', 'transaction_id']), ['tab' => $t]));
    $maskPhone = function (?string $phone): string {
        if ($phone === null || $phone === '') {
            return '—';
        }
        $digits = preg_replace('/\D/', '', $phone) ?? '';
        if (strlen($digits) < 6) {
            return $phone;
        }

        return substr($digits, 0, 3).str_repeat('•', max(2, strlen($digits) - 5)).substr($digits, -2);
    };
@endphp

@section('content')
    <x-admin.page-title title="M-PESA transactions" />

    <p class="mb-6 max-w-3xl text-sm text-slate-600">
        STK pushes use <code class="rounded bg-slate-100 px-1 font-mono text-xs">mpesa_stk_pushes</code>.
        C2B uses <code class="rounded bg-slate-100 px-1 font-mono text-xs">mpesa_c2b_transactions</code> (populate via your C2B validation callback when ready).
        B2C / B2B use <code class="rounded bg-slate-100 px-1 font-mono text-xs">mpesa_b2c</code> and <code class="rounded bg-slate-100 px-1 font-mono text-xs">mpesa_b2b</code>.
        Pending B2C/B2B rows can be approved or rejected with a full audit trail.
    </p>

    <div class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">STK requests</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-slate-900">{{ number_format($summary['stk_total']) }}</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">C2B records</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-slate-900">{{ number_format($summary['c2b_count']) }}</p>
            <p class="mt-0.5 text-xs text-slate-500">KES {{ number_format($summary['c2b_total_amount'], 2) }} total</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">B2C rows</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-slate-900">{{ number_format($summary['b2c_count']) }}</p>
            <p class="mt-0.5 text-xs text-slate-500">KES {{ number_format($summary['b2c_total_amount'], 2) }} total</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">B2B rows</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-slate-900">{{ number_format($summary['b2b_count']) }}</p>
            <p class="mt-0.5 text-xs text-slate-500">KES {{ number_format($summary['b2b_total_amount'], 2) }} total</p>
        </x-admin.card>
        <x-admin.card class="sm:col-span-2 lg:col-span-1 xl:col-span-2 border-amber-200 bg-amber-50/40">
            <p class="text-xs font-medium uppercase tracking-wide text-amber-900">Pending approvals (B2C + B2B)</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-amber-950">{{ number_format($summary['pending_approvals']) }}</p>
        </x-admin.card>
    </div>

    <div class="mb-4 flex flex-wrap gap-2 border-b border-slate-200 pb-3">
        @foreach (['stk' => 'STK Push', 'c2b' => 'C2B', 'b2c' => 'B2C', 'b2b' => 'B2B'] as $t => $label)
            <a
                href="{{ $tabLink($t) }}"
                class="inline-flex rounded-lg px-3 py-1.5 text-sm font-medium transition {{ $tab === $t ? 'bg-emerald-700 text-white shadow-sm' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}"
            >{{ $label }}</a>
        @endforeach
    </div>

    <x-admin.card class="mb-6">
        <form method="get" action="{{ route('admin.mpesa-transactions.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">From</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">To</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Status</label>
                <input type="text" name="status" value="{{ $filters['status'] }}" placeholder="e.g. Pending" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">Phone contains</label>
                <input type="text" name="phone" value="{{ $filters['phone'] }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div class="sm:col-span-2 lg:col-span-2">
                <label class="mb-1 block text-xs font-medium text-slate-600">Transaction / ref / checkout</label>
                <input type="text" name="transaction_id" value="{{ $filters['transaction_id'] }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div class="flex items-end gap-2 lg:col-span-6">
                <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Apply filters</button>
                <a href="{{ route('admin.mpesa-transactions.index', ['tab' => $tab]) }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            @if ($tab === 'stk')
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">ID</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Ref / account</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Phone</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-slate-600">Amount</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Request date</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Resp. code</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Checkout ID</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($rows as $r)
                            <tr class="hover:bg-slate-50/80">
                                <td class="whitespace-nowrap px-3 py-2 font-mono text-xs text-slate-600">{{ $r->id }}</td>
                                <td class="max-w-[10rem] truncate px-3 py-2 font-mono text-xs text-slate-800" title="{{ $r->reference }}">{{ $r->reference ?: '—' }}</td>
                                <td class="whitespace-nowrap px-3 py-2 text-slate-700">{{ $maskPhone($r->phone) }}</td>
                                <td class="whitespace-nowrap px-3 py-2 text-right tabular-nums text-slate-800">{{ number_format((float) $r->amount, 2) }}</td>
                                <td class="px-3 py-2"><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ $r->status }}</span></td>
                                <td class="whitespace-nowrap px-3 py-2 text-slate-600">{{ optional($r->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="px-3 py-2 font-mono text-xs text-slate-600">{{ $r->response_code ?? '—' }}</td>
                                <td class="max-w-[12rem] truncate px-3 py-2 font-mono text-xs text-slate-600" title="{{ $r->checkout_request_id }}">{{ $r->checkout_request_id }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="p-6"><x-admin.empty-state>No STK push records.</x-admin.empty-state></td></tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif ($tab === 'c2b')
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Trans ID</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Phone</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-slate-600">Amount</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Bill ref</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Trans time</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($rows as $r)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-3 py-2 font-mono text-xs text-slate-800">{{ $r->transaction_id ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $maskPhone($r->phone) }}</td>
                                <td class="px-3 py-2 text-right tabular-nums">{{ number_format((float) $r->amount, 2) }}</td>
                                <td class="max-w-[8rem] truncate px-3 py-2 text-xs" title="{{ $r->bill_reference_number }}">{{ $r->bill_reference_number ?? '—' }}</td>
                                <td class="whitespace-nowrap px-3 py-2 text-slate-600">{{ optional($r->transaction_time)->format('Y-m-d H:i') ?: optional($r->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="px-3 py-2"><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ $r->status }}</span></td>
                                <td class="px-3 py-2 font-mono text-xs">{{ $r->mpesa_receipt_number ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-6"><x-admin.empty-state>No C2B transactions yet.</x-admin.empty-state></td></tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif ($tab === 'b2c')
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Trans ID</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Recipient</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-slate-600">Amount</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Reason</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Requested</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Initiated by</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($rows as $r)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-3 py-2 font-mono text-xs">{{ $r->transaction_id ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $maskPhone($r->receiver_mobile ?: $r->party_b) }}</td>
                                <td class="px-3 py-2 text-right tabular-nums">{{ number_format((float) ($r->amount ?? 0), 2) }}</td>
                                <td class="max-w-[10rem] truncate px-3 py-2 text-xs" title="{{ $r->remarks }}">{{ $r->remarks ?? '—' }}</td>
                                <td class="px-3 py-2"><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ $r->status }}</span></td>
                                <td class="whitespace-nowrap px-3 py-2 text-slate-600">{{ optional($r->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="max-w-[8rem] truncate px-3 py-2 text-xs text-slate-600" title="{{ $r->initiator_name }}">{{ $r->initiator_name ?? '—' }}</td>
                                <td class="px-3 py-2 text-right">
                                    @if ($r->isPending())
                                        <div class="flex flex-col items-end gap-2">
                                            <form method="post" action="{{ route('admin.mpesa-transactions.b2c.approve', $r) }}" class="inline" onsubmit="return confirm('Approve this B2C payout?');">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-emerald-700">Approve</button>
                                            </form>
                                            <form method="post" action="{{ route('admin.mpesa-transactions.b2c.reject', $r) }}" class="flex w-full max-w-[14rem] flex-col items-end gap-1">
                                                @csrf
                                                <input type="text" name="rejection_reason" required placeholder="Rejection reason" class="w-full rounded border border-slate-300 px-2 py-1 text-xs">
                                                <button type="submit" class="rounded-lg border border-red-300 bg-red-50 px-2.5 py-1 text-xs font-medium text-red-800 hover:bg-red-100">Reject</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($r->approved_at || $r->rejected_at)
                                <tr class="bg-slate-50/80 text-xs text-slate-600">
                                    <td colspan="8" class="px-3 py-1.5">
                                        @if ($r->approved_at)
                                            Approved {{ $r->approved_at->format('Y-m-d H:i') }} @if($r->approvedByAdmin) by {{ $r->approvedByAdmin->email }} @endif
                                        @endif
                                        @if ($r->rejected_at)
                                            Rejected {{ $r->rejected_at->format('Y-m-d H:i') }} @if($r->rejectedByAdmin) by {{ $r->rejectedByAdmin->email }} @endif
                                            @if ($r->rejection_reason) — {{ \Illuminate\Support\Str::limit($r->rejection_reason, 120) }} @endif
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="8" class="p-6"><x-admin.empty-state>No B2C records.</x-admin.empty-state></td></tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Trans ID</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Paybill / till (Party B)</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-slate-600">Amount</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Account ref</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Requested</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-slate-600">Initiated by</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($rows as $r)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-3 py-2 font-mono text-xs">{{ $r->transaction_id ?? '—' }}</td>
                                <td class="px-3 py-2 font-mono text-xs">{{ $r->party_b ?? '—' }}</td>
                                <td class="px-3 py-2 text-right tabular-nums">{{ number_format((float) ($r->amount ?? 0), 2) }}</td>
                                <td class="max-w-[10rem] truncate px-3 py-2 text-xs" title="{{ $r->remarks }}">{{ $r->remarks ?? '—' }}</td>
                                <td class="px-3 py-2"><span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ $r->status }}</span></td>
                                <td class="whitespace-nowrap px-3 py-2 text-slate-600">{{ optional($r->created_at)->format('Y-m-d H:i') }}</td>
                                <td class="max-w-[8rem] truncate px-3 py-2 text-xs text-slate-600" title="{{ $r->initiator }}">{{ $r->initiator ?? '—' }}</td>
                                <td class="px-3 py-2 text-right">
                                    @if ($r->isPending())
                                        <div class="flex flex-col items-end gap-2">
                                            <form method="post" action="{{ route('admin.mpesa-transactions.b2b.approve', $r) }}" class="inline" onsubmit="return confirm('Approve this B2B transfer?');">
                                                @csrf
                                                <button type="submit" class="rounded-lg bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-emerald-700">Approve</button>
                                            </form>
                                            <form method="post" action="{{ route('admin.mpesa-transactions.b2b.reject', $r) }}" class="flex w-full max-w-[14rem] flex-col items-end gap-1">
                                                @csrf
                                                <input type="text" name="rejection_reason" required placeholder="Rejection reason" class="w-full rounded border border-slate-300 px-2 py-1 text-xs">
                                                <button type="submit" class="rounded-lg border border-red-300 bg-red-50 px-2.5 py-1 text-xs font-medium text-red-800 hover:bg-red-100">Reject</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($r->approved_at || $r->rejected_at)
                                <tr class="bg-slate-50/80 text-xs text-slate-600">
                                    <td colspan="8" class="px-3 py-1.5">
                                        @if ($r->approved_at)
                                            Approved {{ $r->approved_at->format('Y-m-d H:i') }} @if($r->approvedByAdmin) by {{ $r->approvedByAdmin->email }} @endif
                                        @endif
                                        @if ($r->rejected_at)
                                            Rejected {{ $r->rejected_at->format('Y-m-d H:i') }} @if($r->rejectedByAdmin) by {{ $r->rejectedByAdmin->email }} @endif
                                            @if ($r->rejection_reason) — {{ \Illuminate\Support\Str::limit($r->rejection_reason, 120) }} @endif
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="8" class="p-6"><x-admin.empty-state>No B2B records.</x-admin.empty-state></td></tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3">
            {{ $rows->links() }}
        </div>
    </x-admin.card>
@endsection
