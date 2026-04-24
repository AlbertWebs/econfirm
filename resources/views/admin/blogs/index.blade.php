@extends('layouts.admin')

@section('title', 'Insights / Blog')
@section('page_title', 'Insights / Blog')

@section('content')
    <x-admin.page-title title="Insights / Blog">
        <x-slot:actions>
            <a href="{{ route('admin.blogs.create') }}" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                New post
            </a>
        </x-slot:actions>
    </x-admin.page-title>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
    @endif

    <x-admin.card :flush="true">
        <form method="get" action="{{ route('admin.blogs.index') }}" class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/80 p-4 sm:flex-row sm:flex-wrap sm:items-end">
            <div class="min-w-0 flex-1 sm:max-w-xs">
                <label for="q" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Search</label>
                <input type="search" id="q" name="q" value="{{ $q }}" placeholder="Title, excerpt, slug…" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div class="w-full sm:w-44">
                <label for="status" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Status</label>
                <select id="status" name="status" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">All</option>
                    <option value="{{ \App\Models\Blog::STATUS_PUBLISHED }}" @selected($status === \App\Models\Blog::STATUS_PUBLISHED)>Published</option>
                    <option value="{{ \App\Models\Blog::STATUS_DRAFT }}" @selected($status === \App\Models\Blog::STATUS_DRAFT)>Draft</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Filter</button>
                <a href="{{ route('admin.blogs.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Slug</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Published</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Updated</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($blogs as $blog)
                        <tr class="hover:bg-slate-50/80">
                            <td class="max-w-xs truncate px-4 py-3 font-medium text-slate-900 sm:px-5">{{ \Illuminate\Support\Str::limit($blog->title, 64) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-600 sm:px-5">{{ $blog->slug }}</td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                @if ($blog->status === \App\Models\Blog::STATUS_PUBLISHED)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Published</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Draft</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($blog->published_at)->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($blog->updated_at)->format('Y-m-d H:i') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right sm:px-5">
                                <div class="flex flex-wrap justify-end gap-1">
                                    <a href="{{ route('insights.show', $blog->slug) }}" target="_blank" rel="noopener" class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">View</a>
                                    <a href="{{ route('admin.blogs.edit', $blog) }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-800 hover:bg-emerald-100">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-admin.empty-state>No blog posts yet. Create your first insight.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $blogs->links() }}
        </div>
    </x-admin.card>
@endsection
