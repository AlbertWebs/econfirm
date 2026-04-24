@extends('layouts.admin')

@section('title', 'Legal pages')
@section('page_title', 'Legal pages')

@section('content')
    <x-admin.page-title title="Legal pages">
        <x-slot:actions>
            <a href="{{ route('admin.pages.index') }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                All CMS pages
            </a>
        </x-slot:actions>
    </x-admin.page-title>

    <p class="mb-6 max-w-3xl text-sm text-slate-600">
        These routes show your CMS page when it is <strong>published</strong>; otherwise visitors see the original Blade file.
        Use <strong>Edit</strong> to change content, or <strong>Create</strong> to add a new CMS page for that URL.
    </p>

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Page</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Slug</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">CMS status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Public</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($rows as $row)
                        @php
                            $def = $row['definition'];
                            $page = $row['page'];
                        @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-medium text-slate-900 sm:px-5">{{ $def['label'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-700 sm:px-5">{{ $def['slug'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                @if (! $page)
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Not in CMS</span>
                                @elseif ($page->is_published)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Published</span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-900">Draft</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                <a href="{{ route($def['route']) }}" target="_blank" rel="noopener noreferrer" class="font-medium text-emerald-700 hover:underline">View live</a>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right sm:px-5">
                                <a href="{{ route('admin.legal-pages.edit', $def['slug']) }}" class="inline-flex rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                                    {{ $page ? 'Edit' : 'Create' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-admin.table-wrap>
    </x-admin.card>
@endsection
