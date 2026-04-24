@extends('layouts.admin')

@section('title', $page->title)
@section('page_title', 'Preview: '.$page->slug)

@section('content')
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.pages.edit', $page) }}" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Edit</a>
        <a href="{{ route('admin.pages.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">All pages</a>
    </div>

    <x-admin.card>
        <h3 class="text-lg font-semibold text-slate-900">{{ $page->title }}</h3>
        <p class="mt-2 text-sm text-slate-600">Published: <span class="font-medium text-slate-800">{{ $page->is_published ? 'Yes' : 'No' }}</span></p>
        <hr class="my-6 border-slate-200">
        <div class="max-w-none text-sm leading-relaxed text-slate-800 [&_a]:text-emerald-700 [&_a]:underline [&_h1]:mb-3 [&_h1]:text-xl [&_h1]:font-semibold [&_h2]:mb-2 [&_h2]:mt-6 [&_h2]:text-lg [&_h2]:font-semibold [&_p]:mb-3 [&_ul]:mb-3 [&_ul]:list-disc [&_ul]:pl-6">
            {!! $page->body !!}
        </div>
    </x-admin.card>
@endsection
