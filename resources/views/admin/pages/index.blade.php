@extends('layouts.admin')

@section('title', 'Pages')
@section('page_title', 'CMS pages')

@section('content')
    <x-admin.page-title title="CMS pages">
        <x-slot:actions>
            <a href="{{ route('admin.pages.create') }}" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                New page
            </a>
        </x-slot:actions>
    </x-admin.page-title>

    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 text-sm text-emerald-950 shadow-sm">
        <p class="font-semibold text-emerald-900">Homepage from CMS</p>
        <p class="mt-2 text-emerald-900/90">
            Create a <strong class="font-mono">published</strong> page with slug <code class="rounded bg-white/80 px-1.5 py-0.5 font-mono text-xs">home</code> to replace the default site at
            <a href="{{ url('/') }}" class="font-medium underline decoration-emerald-600 hover:text-emerald-950" target="_blank" rel="noopener">/</a>.
            Use slug <code class="rounded bg-white/80 px-1.5 py-0.5 font-mono text-xs">home-v2</code> for
            <a href="{{ url('/v2') }}" class="font-medium underline decoration-emerald-600 hover:text-emerald-950" target="_blank" rel="noopener">/v2</a>.
            The page <strong>body</strong> is output as HTML inside the normal site layout (header, footer, nav). Unpublish or delete the page to restore the built-in homepage.
        </p>
    </div>

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Slug</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Published</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Updated</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($pages as $p)
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-800 sm:px-5">
                                {{ $p->slug }}
                                @if (in_array($p->slug, ['home', 'home-v2'], true))
                                    <span class="ml-2 inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-800">Home</span>
                                @endif
                            </td>
                            <td class="max-w-md truncate px-4 py-3 text-slate-700 sm:px-5">{{ \Illuminate\Support\Str::limit($p->title, 60) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                @if ($p->is_published)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Yes</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">No</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 sm:px-5">{{ optional($p->updated_at)->format('Y-m-d H:i') }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right sm:px-5">
                                <div class="flex flex-wrap justify-end gap-1">
                                    <a href="{{ route('admin.pages.show', $p) }}" class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50">View</a>
                                    <a href="{{ route('admin.pages.edit', $p) }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-800 hover:bg-emerald-100">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-admin.empty-state>No pages yet. Create one for terms, privacy, etc.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $pages->links() }}
        </div>
    </x-admin.card>
@endsection
