@extends('layouts.admin')

@section('title', 'Activity log')
@section('page_title', 'Activity log')

@section('content')
    <x-admin.page-title title="Activity log" />

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">When</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Admin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Subject</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-600 sm:px-5">{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-700 sm:px-5">{{ $log->admin?->email ?? '—' }}</td>
                            <td class="px-4 py-3 sm:px-5">
                                <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs text-slate-800">{{ $log->action }}</code>
                            </td>
                            <td class="px-4 py-3 text-slate-600 sm:px-5">
                                @if ($log->subject_type)
                                    {{ class_basename($log->subject_type) }}#{{ $log->subject_id }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-admin.empty-state>No log entries yet.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $logs->links() }}
        </div>
    </x-admin.card>
@endsection
