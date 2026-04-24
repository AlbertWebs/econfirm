@extends('layouts.admin')

@section('title', 'Edit '.$page->slug)
@section('page_title', 'Edit page')

@section('content')
    <form method="post" action="{{ route('admin.pages.update', $page) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.pages._form', ['page' => $page])
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Save</button>
            <a href="{{ route('admin.pages.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Back</a>
        </div>
    </form>

    <form method="post" action="{{ route('admin.pages.destroy', $page) }}" class="mt-8 border-t border-slate-200 pt-6" onsubmit="return confirm('Delete this page?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-800 hover:bg-red-100">
            Delete page
        </button>
    </form>
@endsection
