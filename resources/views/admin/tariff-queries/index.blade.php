@extends('layouts.admin')

@section('title', 'Tarif Queries')
@section('page_title', 'Tarif Queries')

@section('content')
    <x-admin.page-title title="Tarif Queries" description="Submissions from the public tariffs calculator (/tariffs)." />

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Amount (KES)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Rail</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Commission</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">M-PESA est.</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">IP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($queries as $q)
                        <tr class="hover:bg-slate-50/80 align-top">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 sm:px-5">{{ $q->id }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-slate-900 sm:px-5">{{ $q->amount_kes !== null ? number_format((int) $q->amount_kes) : '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 sm:px-5">{{ strtoupper((string) ($q->rail ?? '—')) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 sm:px-5">
                                {{ $q->commission_kes !== null ? number_format((float) $q->commission_kes, 2) : '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 sm:px-5">
                                {{ $q->mpesa_fee_kes !== null ? number_format((int) $q->mpesa_fee_kes) : '—' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-900 sm:px-5">
                                {{ $q->total_kes !== null ? number_format((int) $q->total_kes) : '—' }}
                            </td>
                            <td class="max-w-[10rem] truncate px-4 py-3 text-slate-500 sm:px-5" title="{{ $q->ip_address }}">{{ $q->ip_address ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($q->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                        @if (filled($q->error_message))
                            <tr class="bg-amber-50/50">
                                <td colspan="8" class="px-4 py-2 text-xs text-amber-950 sm:px-5">
                                    <span class="font-semibold">Note:</span> {{ $q->error_message }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-admin.empty-state>No tariff calculator submissions yet.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $queries->links() }}
        </div>
    </x-admin.card>
@endsection
