@extends('layouts.admin')

@section('title', 'New support / help item')
@section('page_title', 'New item')

@section('content')
    <form method="post" action="{{ route('admin.support-help-items.store') }}" class="space-y-6">
        @csrf
        <x-admin.card>
            @include('admin.support-help-items._form', ['item' => $item])
        </x-admin.card>
        <div class="flex flex-wrap gap-2">
            <button type="submit" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Create</button>
            <a href="{{ route('admin.support-help-items.index', array_filter(['kind' => $item->kind])) }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection
