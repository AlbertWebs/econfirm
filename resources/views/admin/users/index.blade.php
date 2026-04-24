@extends('layouts.admin')

@section('title', 'Users')
@section('page_title', 'Users')

@section('content')
    <x-admin.page-title title="Users" />

    <form method="get" class="mb-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
        <div class="min-w-0 flex-1 sm:max-w-md">
            <label class="mb-1 block text-xs font-medium text-slate-600">Search</label>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Name, email, phone" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div class="w-full sm:w-44">
            <label class="mb-1 block text-xs font-medium text-slate-600">Type (DB)</label>
            <select name="type" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">All</option>
                <option value="0" @selected(request('type') === '0' || request('type') === 0)>0 — user</option>
                <option value="1" @selected(request('type') === '1' || request('type') === 1)>1 — admin</option>
                <option value="2" @selected(request('type') === '2' || request('type') === 2)>2 — manager</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Type</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($users as $u)
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 sm:px-5">{{ $u->id }}</td>
                            <td class="px-4 py-3 font-medium text-emerald-700 sm:px-5">
                                <a href="{{ route('admin.users.show', $u) }}" class="hover:underline">{{ $u->name }}</a>
                            </td>
                            <td class="break-all px-4 py-3 text-slate-700 sm:px-5">{{ $u->email }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 sm:px-5">{{ $u->phone ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 sm:px-5">{{ $u->getRawOriginal('type') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-admin.empty-state>No users found.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $users->links() }}
        </div>
    </x-admin.card>
@endsection
