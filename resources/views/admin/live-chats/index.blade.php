@extends('layouts.admin')

@section('title', 'Live chats')
@section('page_title', 'Live chats')

@section('content')
    <x-admin.page-title title="Live chats" />

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Transaction</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Opened by</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Messages</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Updated</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($chats as $c)
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 sm:px-5">{{ $c->id }}</td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                @if ($c->transaction)
                                    <a href="{{ route('admin.transactions.show', $c->transaction) }}" class="font-medium text-emerald-700 hover:underline">{{ $c->transaction->transaction_id }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">{{ $c->status }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 sm:px-5">{{ $c->opened_by_phone ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums sm:px-5">{{ $c->messages_count }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($c->updated_at)->format('Y-m-d H:i') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right sm:px-5">
                                <a href="{{ route('admin.live-chats.show', $c) }}" class="inline-flex rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-admin.empty-state>No live chats yet.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $chats->links() }}
        </div>
    </x-admin.card>
@endsection
