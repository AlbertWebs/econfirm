@extends('layouts.admin')

@section('title', 'New page')
@section('page_title', 'New CMS page')

@section('content')
    <form method="post" action="{{ route('admin.pages.store') }}" class="space-y-6">
        @csrf
        @include('admin.pages._form', ['page' => $page])
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Create</button>
            <a href="{{ route('admin.pages.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection
