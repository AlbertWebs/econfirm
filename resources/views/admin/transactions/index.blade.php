@extends('layouts.admin')

@section('title', 'Escrow transactions')
@section('page_title', 'Escrow transactions')

@section('content')
    <x-admin.page-title title="Escrow transactions">
        <x-slot:actions>
            <a
                href="{{ route('admin.transactions.export', request()->query()) }}"
                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
                Export CSV
            </a>
        </x-slot:actions>
    </x-admin.page-title>

    <form method="get" class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6 lg:items-end">
        <div class="lg:col-span-1">
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
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Min amount</label>
            <input type="number" step="0.01" name="min_amount" value="{{ request('min_amount') }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-600">Max amount</label>
            <input type="number" step="0.01" name="max_amount" value="{{ request('max_amount') }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div class="lg:col-span-1">
            <label class="mb-1 block text-xs font-medium text-slate-600">Search</label>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Ref or phone" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div class="flex flex-wrap gap-2 lg:col-span-6">
            <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Filter</button>
            <a href="{{ route('admin.transactions.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Ref</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Sender</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Created</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($transactions as $t)
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 sm:px-5">{{ $t->id }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-medium text-emerald-700 sm:px-5">
                                <a href="{{ route('admin.transactions.show', $t) }}" class="hover:underline">{{ $t->transaction_id }}</a>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums sm:px-5">KES {{ number_format((float) $t->transaction_amount, 2) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">{{ $t->status }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 sm:px-5">{{ $t->sender_mobile }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($t->created_at)->format('Y-m-d H:i') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right sm:px-5">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.transactions.show', $t) }}" class="inline-flex rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">View transaction details</a>
                                    <form method="POST" action="{{ route('admin.transactions.destroy', $t) }}" onsubmit="return confirm('Delete transaction {{ $t->transaction_id }}? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex rounded-md border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-700 hover:bg-red-100">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-admin.empty-state>No matching transactions.</x-admin.empty-state>
                            </td>
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
