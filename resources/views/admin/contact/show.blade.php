@extends('layouts.admin')

@section('title', 'Message #'.$submission->id)
@section('page_title', 'Contact message')

@section('content')
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.contact.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Inbox
        </a>
        <form method="post" action="{{ route('admin.contact.unread', $submission) }}">
            @csrf
            <button type="submit" class="inline-flex rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 hover:bg-amber-100">
                Mark unread
            </button>
        </form>
    </div>

    <x-admin.card>
        <p class="text-sm text-slate-700"><span class="font-semibold text-slate-900">{{ $submission->name }}</span> ({{ $submission->email }})</p>
        @if ($submission->phone)
            <p class="mt-2 text-sm text-slate-600">Phone: {{ $submission->phone }}</p>
        @endif
        <p class="mt-3 text-sm text-slate-700">Subject: <span class="font-semibold text-slate-900">{{ $submission->subject }}</span></p>
        <hr class="my-6 border-slate-200">
        <p class="whitespace-pre-wrap break-words text-sm leading-relaxed text-slate-800">{{ $submission->message }}</p>
        <hr class="my-6 border-slate-200">
        <p class="text-xs text-slate-500">IP: {{ $submission->ip_address ?? '—' }} · {{ optional($submission->created_at)->toDateTimeString() }}</p>
    </x-admin.card>
@endsection
