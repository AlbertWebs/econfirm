@php
    $p = $page;
@endphp
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="lg:col-span-1">
        <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-700">Slug</label>
        <input
            type="text"
            id="slug"
            name="slug"
            value="{{ old('slug', $p->slug) }}"
            required
            pattern="[a-z0-9]+(?:-[a-z0-9]+)*"
            placeholder="terms-and-conditions"
            class="block w-full rounded-lg border-slate-300 font-mono text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('slug') border-red-500 ring-red-200 @enderror"
        >
        @error('slug')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-2 text-xs text-slate-500">
            Lowercase, hyphens. Reserved for site routes:
            <code class="rounded bg-slate-100 px-1">home</code> (homepage <code class="rounded bg-slate-100 px-1">/</code>),
            <code class="rounded bg-slate-100 px-1">home-v2</code> (<code class="rounded bg-slate-100 px-1">/v2</code>),
            <code class="rounded bg-slate-100 px-1">terms-and-conditions</code>,
            <code class="rounded bg-slate-100 px-1">privacy-policy</code>,
            <code class="rounded bg-slate-100 px-1">complience</code>, and other legal slugs.
        </p>
    </div>
    <div>
        <label for="type" class="mb-1.5 block text-sm font-medium text-slate-700">Type (optional)</label>
        <input type="text" id="type" name="type" value="{{ old('type', $p->type) }}" placeholder="legal" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <div class="lg:col-span-2">
        <label for="title" class="mb-1.5 block text-sm font-medium text-slate-700">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $p->title) }}" required class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('title') border-red-500 @enderror">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="lg:col-span-2">
        <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-700">Meta description</label>
        <input type="text" id="meta_description" name="meta_description" value="{{ old('meta_description', $p->meta_description) }}" maxlength="500" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <div class="lg:col-span-2">
        <label class="flex items-center gap-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" id="is_published" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" @checked(old('is_published', $p->is_published))>
            <span class="text-sm font-medium text-slate-700">Published (visible on public site)</span>
        </label>
    </div>
    <div class="lg:col-span-2">
        <label for="page_body" class="mb-1.5 block text-sm font-medium text-slate-700">Body (HTML allowed)</label>
        <textarea id="page_body" name="body" rows="18" required class="block w-full rounded-lg border-slate-300 font-mono text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('body') border-red-500 @enderror">{{ old('body', $p->body) }}</textarea>
        @error('body')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#page_body',
    height: 420,
    menubar: false,
    plugins: 'lists link code autoresize',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link | code',
    content_style: 'body { font-family: system-ui, sans-serif; font-size: 14px; }',
  });
  document.querySelector('form')?.addEventListener('submit', function () {
    if (window.tinymce) tinymce.triggerSave();
  });
</script>
@endpush
