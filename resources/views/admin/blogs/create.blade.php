@extends('layouts.admin')

@section('title', 'New blog post')
@section('page_title', 'New insight / blog post')

@section('content')
    <form method="post" action="{{ route('admin.blogs.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @include('admin.blogs._form', ['blog' => $blog])
        <div class="flex flex-wrap gap-2">
            <button type="submit" name="action" value="draft" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50">Save as draft</button>
            <button type="submit" name="action" value="publish" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Publish</button>
            <a href="{{ route('admin.blogs.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection
