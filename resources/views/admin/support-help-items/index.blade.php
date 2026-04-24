@extends('layouts.admin')

@section('title', 'Support & help')
@section('page_title', 'Support & help')

@section('content')
    @php
        $labels = \App\Models\SupportHelpItem::kindLabels();
    @endphp

    <x-admin.page-title title="Support & help content">
        <x-slot:actions>
            <a href="{{ route('admin.support-help-items.create', ['kind' => \App\Models\SupportHelpItem::KIND_QUICK_HELP]) }}" class="inline-flex rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                New Quick Help
            </a>
            <a href="{{ route('admin.support-help-items.create', ['kind' => \App\Models\SupportHelpItem::KIND_HELP_FAQ]) }}" class="inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                New FAQ
            </a>
        </x-slot:actions>
    </x-admin.page-title>

    <p class="mb-6 max-w-3xl text-sm text-slate-600">
        <strong>Quick Help</strong> appears on the <a href="{{ route('support') }}" class="font-medium text-emerald-700 underline decoration-emerald-400 hover:text-emerald-900" target="_blank" rel="noopener">Support</a> page.
        <strong>Help FAQs</strong> appear on the <a href="{{ route('help') }}" class="font-medium text-emerald-700 underline decoration-emerald-400 hover:text-emerald-900" target="_blank" rel="noopener">Help</a> page (unless those routes are overridden by a published CMS page with the same slug).
    </p>

    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('admin.support-help-items.index') }}" class="inline-flex rounded-lg px-3 py-1.5 text-sm font-medium {{ $kind === '' ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
            All
        </a>
        @foreach ($labels as $value => $label)
            <a href="{{ route('admin.support-help-items.index', ['kind' => $value]) }}" class="inline-flex rounded-lg px-3 py-1.5 text-sm font-medium {{ $kind === $value ? 'bg-emerald-700 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <x-admin.card :flush="true">
        <x-admin.table-wrap class="rounded-none border-0">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Section</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Order</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5">Published</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600 sm:px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($items as $row)
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-4 py-3 text-slate-600 sm:px-5">{{ $labels[$row->kind] ?? $row->kind }}</td>
                            <td class="max-w-md px-4 py-3 text-slate-800 sm:px-5">{{ \Illuminate\Support\Str::limit($row->title, 72) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-xs text-slate-600 sm:px-5">{{ $row->sort_order }}</td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                @if ($row->is_published)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Yes</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">No</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right sm:px-5">
                                <div class="flex flex-wrap justify-end gap-1">
                                    <a href="{{ route('admin.support-help-items.edit', $row) }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-800 hover:bg-emerald-100">Edit</a>
                                    <form method="post" action="{{ route('admin.support-help-items.destroy', $row) }}" class="inline" onsubmit="return confirm('Delete this item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-medium text-red-800 hover:bg-red-100">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-admin.empty-state>No items yet. Run <code class="rounded bg-slate-100 px-1 font-mono text-xs">php artisan db:seed --class=SupportHelpItemSeeder</code> or create one above.</x-admin.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-admin.table-wrap>
        <div class="border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
            {{ $items->links() }}
        </div>
    </x-admin.card>
@endsection
