@php
    $kinds = \App\Models\SupportHelpItem::kindLabels();
@endphp

<div class="space-y-5">
    <div>
        <label for="kind" class="mb-1.5 block text-sm font-medium text-slate-700">Section</label>
        <select id="kind" name="kind" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('kind') border-red-500 @enderror">
            @foreach ($kinds as $value => $label)
                <option value="{{ $value }}" @selected(old('kind', $item->kind) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('kind')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="title" class="mb-1.5 block text-sm font-medium text-slate-700">Title / question</label>
        <input type="text" id="title" name="title" value="{{ old('title', $item->title) }}" required maxlength="500" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('title') border-red-500 @enderror">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="body" class="mb-1.5 block text-sm font-medium text-slate-700">Answer (HTML allowed)</label>
        <textarea id="body" name="body" rows="8" required class="block w-full rounded-lg border-slate-300 font-mono text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('body') border-red-500 @enderror">{{ old('body', $item->body) }}</textarea>
        @error('body')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div data-quick-help-only class="space-y-1">
        <label for="icon" class="mb-1.5 block text-sm font-medium text-slate-700">Icon (Font Awesome class)</label>
        <input type="text" id="icon" name="icon" value="{{ old('icon', $item->icon) }}" maxlength="120" placeholder="fas fa-question-circle" class="block w-full rounded-lg border-slate-300 font-mono text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('icon') border-red-500 @enderror">
        <p class="text-xs text-slate-500">Shown only for Quick Help cards on the Support page. Leave empty for FAQs.</p>
        @error('icon')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="sort_order" class="mb-1.5 block text-sm font-medium text-slate-700">Sort order</label>
            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 0) }}" min="0" max="999999" required class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('sort_order') border-red-500 @enderror">
            @error('sort_order')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-end pb-1">
            <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-700">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" @checked(old('is_published', $item->is_published ?? true))>
                Published
            </label>
        </div>
    </div>
</div>

<script>
    (function () {
        const kind = document.getElementById('kind');
        const wrap = document.querySelector('[data-quick-help-only]');
        if (!kind || !wrap) return;
        function sync() {
            wrap.classList.toggle('hidden', kind.value !== '{{ \App\Models\SupportHelpItem::KIND_QUICK_HELP }}');
        }
        kind.addEventListener('change', sync);
        sync();
    })();
</script>
