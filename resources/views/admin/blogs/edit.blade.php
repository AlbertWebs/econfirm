@extends('layouts.admin')

@section('title', 'Edit blog')
@section('page_title', 'Edit insight / blog')

@section('content')
    <form method="post" action="{{ route('admin.blogs.update', $blog) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.blogs._form', ['blog' => $blog])
        <div class="flex flex-wrap gap-2">
            <button type="submit" name="action" value="draft" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm hover:bg-slate-50">Save as draft</button>
            <button type="submit" name="action" value="publish" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Publish</button>
            <a href="{{ route('admin.blogs.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Back to list</a>
        </div>
    </form>

    <div class="mt-10 border-t border-slate-200 pt-8">
        <h2 class="text-sm font-semibold text-slate-700">Danger zone</h2>
        <p class="mt-1 text-sm text-slate-500">Soft-delete this post. It will no longer appear on the public site.</p>
        <form method="post" action="{{ route('admin.blogs.destroy', $blog) }}" class="mt-3" onsubmit="return confirm('Delete this blog post?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-800 hover:bg-rose-100">Delete post</button>
        </form>
    </div>
@endsection
