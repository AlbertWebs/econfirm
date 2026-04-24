@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Registered users</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($userCount) }}</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">All escrows</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($transactionCount) }}</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Non-completed (approx.)</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-amber-700">{{ number_format($pendingLike) }}</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Open live chats</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-900">{{ number_format($openChats) }}</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Site page views (7 days)</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-sky-700">{{ number_format($trafficViews7d) }}</p>
            <p class="mt-2 text-xs text-slate-500">Public GET/HEAD hits; admin excluded.</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Site page views (30 days)</p>
            <p class="mt-2 text-3xl font-semibold tabular-nums text-sky-700">{{ number_format($trafficViews30d) }}</p>
            <p class="mt-2 text-xs text-slate-500">Common crawlers filtered out.</p>
        </x-admin.card>
    </div>

    @php
        $chartPayload = [
            'escrowsDaily' => $chartEscrowsDaily,
            'usersDaily' => $chartUsersDaily,
            'trafficDaily' => $chartTrafficDaily,
            'statusBreakdown' => $chartStatusBreakdown,
        ];
        $statusChartTotal = array_sum($chartStatusBreakdown['values']);
    @endphp
    <script type="application/json" id="admin-dashboard-charts-data">@json($chartPayload)</script>

    <div class="mb-8 grid gap-6 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-4">
        <x-admin.card>
            <p class="mb-3 text-xs font-medium uppercase tracking-wide text-slate-500">Website traffic</p>
            <div class="relative h-52 w-full md:h-56">
                <canvas id="adminChartTraffic" aria-label="Page views per day chart"></canvas>
            </div>
        </x-admin.card>
        <x-admin.card>
            <p class="mb-3 text-xs font-medium uppercase tracking-wide text-slate-500">Trend</p>
            <div class="relative h-52 w-full md:h-56">
                <canvas id="adminChartEscrows" aria-label="Escrows per day chart"></canvas>
            </div>
        </x-admin.card>
        <x-admin.card>
            <p class="mb-3 text-xs font-medium uppercase tracking-wide text-slate-500">Signups</p>
            <div class="relative h-52 w-full md:h-56">
                <canvas id="adminChartUsers" aria-label="New users per day chart"></canvas>
            </div>
        </x-admin.card>
        <x-admin.card class="lg:col-span-2 2xl:col-span-1">
            <p class="mb-3 text-xs font-medium uppercase tracking-wide text-slate-500">Distribution</p>
            @if ($statusChartTotal > 0)
                <div class="relative mx-auto h-52 w-full max-w-xs md:h-56">
                    <canvas id="adminChartStatus" aria-label="Escrow status chart"></canvas>
                </div>
            @else
                <p class="py-8 text-center text-sm text-slate-500">No escrow data to chart by status yet.</p>
            @endif
        </x-admin.card>
    </div>

    <x-admin.card class="mb-8">
        <x-slot:header>Top paths (14 days)</x-slot:header>
        <p class="mb-4 text-sm text-slate-600">Most-requested URL paths on the public site (same rules as the chart above).</p>
        <x-admin.table-wrap class="rounded-lg border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Path</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Views</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($topPaths14d as $row)
                        @php
                            $pathLabel = ($row->path === '' || $row->path === '/') ? '/' : '/'.$row->path;
                        @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="max-w-xl truncate px-4 py-3 font-mono text-xs text-slate-800 sm:px-5" title="{{ $pathLabel }}">{{ $pathLabel }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-slate-700 sm:px-5">{{ number_format((int) $row->views) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">
                                <x-admin.empty-state>No traffic recorded yet. Browse the public site to seed data.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
    </x-admin.card>

    <x-admin.card class="mb-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Volume this month</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums text-slate-900">KES {{ number_format($volumeMonth, 2) }}</p>
            </div>
            <a
                href="{{ route('admin.transactions.index') }}"
                class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
            >
                View all escrows
            </a>
        </div>
    </x-admin.card>

    <x-admin.card :flush="true">
        <x-slot:header>Recent escrows</x-slot:header>
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Ref</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Amount</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Status</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Sender</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($recentTransactions as $t)
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 font-medium text-emerald-700 sm:px-5">
                                <a href="{{ route('admin.transactions.show', $t) }}" class="hover:underline">{{ $t->transaction_id }}</a>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-slate-700 sm:px-5">
                                {{ number_format((float) $t->transaction_amount, 2) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">{{ $t->status }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 sm:px-5">{{ $t->sender_mobile }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($t->created_at)->format('M j, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-admin.empty-state>No transactions yet.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
    </x-admin.card>
@endsection
