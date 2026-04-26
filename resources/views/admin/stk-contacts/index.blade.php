@extends('layouts.admin')

@section('title', 'STK contacts')
@section('page_title', 'STK contacts')

@section('content')
    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
    @endif
    <x-admin.page-title title="STK contacts">
        <x-slot:actions>
            <form method="post" action="{{ route('admin.stk-contacts.import') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                @csrf
                <input type="file" name="contacts_file" accept=".csv,.txt,.xlsx,.xls" required class="block w-52 cursor-pointer rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-700 file:mr-2 file:rounded file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:text-xs file:font-medium file:text-slate-700">
                <button type="submit" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Import CSV/Excel
                </button>
            </form>
            <a href="{{ route('admin.stk-contacts.export.csv', ['q' => $q]) }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Export CSV
            </a>
            <a href="{{ route('admin.stk-contacts.export.vcf', ['q' => $q]) }}" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                Export VCF
            </a>
        </x-slot:actions>
    </x-admin.page-title>

    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        Use exported contacts only where you have valid marketing consent and follow privacy/communications laws.
    </div>
    @error('contacts_file')
        <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">{{ $message }}</div>
    @enderror

    <x-admin.card :flush="true">
        <form method="get" class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/80 p-4 sm:flex-row sm:flex-wrap sm:items-end">
            <div class="min-w-0 flex-1 sm:max-w-xs">
                <label for="q" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Search phone</label>
                <input type="search" id="q" name="q" value="{{ $q }}" placeholder="e.g. 2547..." class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div class="text-xs text-slate-600">Unique contacts: <strong>{{ number_format($totalUnique) }}</strong></div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Filter</button>
                <a href="{{ route('admin.stk-contacts.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">STK attempts</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Last attempt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($contacts as $c)
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 sm:px-5">{{ $c->name ?: '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-800 sm:px-5">{{ $c->phone }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 sm:px-5">{{ number_format((int) $c->stk_attempts) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ $c->last_stk_attempt_at?->format('Y-m-d H:i') ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-admin.empty-state>No STK contact numbers found.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $contacts->links() }}
        </div>
    </x-admin.card>
@endsection
