@extends('layouts.admin')

@section('title', 'API & developers')
@section('page_title', 'API & developers')

@section('content')
    <x-admin.page-title
        title="API &amp; developers"
        description="Base URLs, per-user API keys, and a link to the public /api/documentation page. Default list: accounts that already have a key."
    />

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <x-admin.card class="mb-6">
        <h2 class="text-sm font-semibold text-slate-900">Endpoint reference</h2>
        <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-2">
            <div class="rounded-lg border border-slate-100 bg-slate-50/80 p-3">
                <dt class="text-xs font-medium uppercase text-slate-500">API root (health check)</dt>
                <dd class="mt-1 break-all font-mono text-slate-800">{{ $apiRoot }}/ping</dd>
            </div>
            <div class="rounded-lg border border-slate-100 bg-slate-50/80 p-3">
                <dt class="text-xs font-medium uppercase text-slate-500">Escrow v1 base</dt>
                <dd class="mt-1 break-all font-mono text-slate-800">{{ $apiV1 }}</dd>
            </div>
        </dl>
        <p class="mt-4 text-sm text-slate-600">
            <a href="{{ $docsUrl }}" class="font-semibold text-emerald-700 hover:underline" target="_blank" rel="noopener">Public API documentation</a>
            (same as <code class="text-xs">/api/documentation</code>) · {{ $totalApiTx }} API-created escrows total.
        </p>
    </x-admin.card>

    <form method="get" class="mb-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end">
        <div class="min-w-0 flex-1 sm:max-w-md">
            <label class="mb-1 block text-xs font-medium text-slate-600">Search users</label>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Name, email, phone" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="show_all" value="1" @checked(request()->boolean('show_all'))>
            Show all users (including no key)
        </label>
        <div class="flex gap-2">
            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Filter</button>
            <a href="{{ route('admin.api-access.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600 sm:px-5">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600 sm:px-5">Key (masked)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-600 sm:px-5">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($users as $u)
                        @php
                            $k = $u->api_key;
                            $mask = $k && strlen($k) > 12
                                ? substr($k, 0, 7).'…'.substr($k, -4)
                                : ($k ? '••••' : '—');
                        @endphp
                        <tr>
                            <td class="px-4 py-3 sm:px-5">
                                <a href="{{ route('admin.users.show', $u) }}" class="font-medium text-emerald-700 hover:underline">#{{ $u->id }} · {{ $u->name }}</a>
                                <div class="text-xs text-slate-500 break-all">{{ $u->email }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-700 sm:px-5">{{ $mask }}</td>
                            <td class="px-4 py-3 sm:px-5">
                                <form method="post" action="{{ route('admin.api-access.regenerate', $u) }}" onsubmit="return confirm('Set a new key for this user? The old key stops working.');">
                                    @csrf
                                    <button type="submit" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-800 hover:bg-slate-50">
                                        Set / rotate key
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <x-admin.empty-state>No users match. Clear filters or create keys from the <a class="text-emerald-700 underline" href="{{ $docsUrl }}">docs</a> /developer page.</x-admin.empty-state>
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
