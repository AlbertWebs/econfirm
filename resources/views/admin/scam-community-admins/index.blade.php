@extends('layouts.admin')

@section('title', 'Community admins')
@section('page_title', 'Community admin approvals')

@section('content')
    <x-admin.page-title title="Community admin approvals" description="Approve community moderators before they can approve reports in their communities." />

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">User</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Community</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Requested</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($requests as $req)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 sm:px-5">
                                <p class="font-medium text-slate-900">{{ $req->user->name ?? 'Unknown user' }}</p>
                                <p class="text-xs text-slate-500">{{ $req->user->email ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 sm:px-5">
                                <a href="{{ route('scam.watch.community', ['community' => $req->community]) }}" target="_blank" class="text-emerald-700 hover:underline">
                                    {{ $req->community->name ?? '—' }}
                                </a>
                            </td>
                            <td class="px-4 py-3 sm:px-5">
                                @if($req->status === 'approved')
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Approved</span>
                                @elseif($req->status === 'rejected')
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">Rejected</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-900">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500 sm:px-5">{{ optional($req->created_at)->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 sm:px-5">
                                <div class="flex flex-wrap gap-2">
                                    <form method="post" action="{{ route('admin.scam-community-admins.status', $req) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Approve</button>
                                    </form>
                                    <form method="post" action="{{ route('admin.scam-community-admins.status', $req) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-admin.empty-state>No community admin requests.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $requests->links() }}
        </div>
    </x-admin.card>
@endsection
