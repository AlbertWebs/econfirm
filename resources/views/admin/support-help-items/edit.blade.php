@extends('layouts.admin')

@section('title', 'Edit support / help')
@section('page_title', 'Edit item')

@section('content')
    <form method="post" action="{{ route('admin.support-help-items.update', $item) }}" class="space-y-6">
        @csrf
        @method('PUT')
        <x-admin.card>
            @include('admin.support-help-items._form', ['item' => $item])
        </x-admin.card>
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Save</button>
            <a href="{{ route('admin.support-help-items.index', ['kind' => $item->kind]) }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Back to list</a>
        </div>
    </form>
@endsection
