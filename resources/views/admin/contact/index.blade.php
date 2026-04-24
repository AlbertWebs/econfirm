@extends('layouts.admin')

@section('title', 'Contact inbox')
@section('page_title', 'Contact inbox')

@section('content')
    <x-admin.page-title title="Contact inbox" />

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">From</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">At</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Read</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($submissions as $s)
                        <tr @class(['hover:bg-slate-50/80', 'bg-slate-50/60' => ! $s->read_at])>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 sm:px-5">{{ $s->id }}</td>
                            <td class="px-4 py-3 sm:px-5">
                                <a href="{{ route('admin.contact.show', $s) }}" class="font-medium text-emerald-700 hover:underline">{{ $s->name }} ({{ $s->email }})</a>
                            </td>
                            <td class="max-w-xs truncate px-4 py-3 text-slate-700 sm:px-5">{{ \Illuminate\Support\Str::limit($s->subject, 60) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($s->created_at)->format('Y-m-d H:i') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                @if ($s->read_at)
                                    <span class="text-slate-500">Yes</span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-900">New</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-admin.empty-state>No messages yet.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $submissions->links() }}
        </div>
    </x-admin.card>
@endsection
