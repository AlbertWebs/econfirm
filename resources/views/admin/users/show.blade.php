@extends('layouts.admin')

@section('title', 'User #'.$user->id)
@section('page_title', 'User detail')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.users.index', request()->query()) }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Back to users
        </a>
    </div>

    <x-admin.card>
        <dl class="space-y-4 text-sm">
            <div class="flex flex-col gap-1 border-b border-slate-100 pb-3 sm:flex-row sm:justify-between">
                <dt class="font-medium text-slate-500">ID</dt>
                <dd class="text-slate-900">{{ $user->id }}</dd>
            </div>
            <div class="flex flex-col gap-1 border-b border-slate-100 pb-3 sm:flex-row sm:justify-between">
                <dt class="font-medium text-slate-500">Name</dt>
                <dd class="text-slate-900">{{ $user->name }}</dd>
            </div>
            <div class="flex flex-col gap-1 border-b border-slate-100 pb-3 sm:flex-row sm:justify-between">
                <dt class="font-medium text-slate-500">Email</dt>
                <dd class="break-all text-slate-900">{{ $user->email }}</dd>
            </div>
            <div class="flex flex-col gap-1 border-b border-slate-100 pb-3 sm:flex-row sm:justify-between">
                <dt class="font-medium text-slate-500">Phone</dt>
                <dd class="text-slate-900">{{ $user->phone ?? '—' }}</dd>
            </div>
            <div class="flex flex-col gap-1 border-b border-slate-100 pb-3 sm:flex-row sm:justify-between">
                <dt class="font-medium text-slate-500">Type (raw / cast)</dt>
                <dd class="text-slate-900">{{ $user->getRawOriginal('type') ?? '—' }} ({{ $user->type }})</dd>
            </div>
            <div class="flex flex-col gap-1 border-b border-slate-100 pb-3 sm:flex-row sm:justify-between">
                <dt class="font-medium text-slate-500">Role</dt>
                <dd class="text-slate-900">{{ $user->role ?? '—' }}</dd>
            </div>
            <div class="flex flex-col gap-1 sm:flex-row sm:justify-between">
                <dt class="font-medium text-slate-500">Created</dt>
                <dd class="text-slate-900">{{ optional($user->created_at)->toDateTimeString() }}</dd>
            </div>
        </dl>
    </x-admin.card>
@endsection
