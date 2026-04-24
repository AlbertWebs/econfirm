<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="lg:col-span-2">
        <label for="title" class="mb-1.5 block text-sm font-medium text-slate-700">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $blog->title) }}" required class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('title') border-red-500 @enderror">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="slug" class="mb-1.5 block text-sm font-medium text-slate-700">Slug</label>
        <input
            type="text"
            id="slug"
            name="slug"
            value="{{ old('slug', $blog->slug) }}"
            placeholder="Leave blank to auto-generate from title"
            class="block w-full rounded-lg border-slate-300 font-mono text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('slug') border-red-500 @enderror"
        >
        @error('slug')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-slate-500">URL path: <code class="rounded bg-slate-100 px-1">/insights/<span id="slug-preview">{{ old('slug', $blog->slug) ?: 'your-slug' }}</span></code></p>
    </div>
    <div>
        <label for="author" class="mb-1.5 block text-sm font-medium text-slate-700">Author</label>
        <input type="text" id="author" name="author" value="{{ old('author', $blog->author ?: 'Admin') }}" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <div class="lg:col-span-2">
        <label for="excerpt" class="mb-1.5 block text-sm font-medium text-slate-700">Short description / excerpt</label>
        <textarea id="excerpt" name="excerpt" rows="3" maxlength="2000" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('excerpt', $blog->excerpt) }}</textarea>
    </div>
    <div class="lg:col-span-2">
        <label for="featured_image" class="mb-1.5 block text-sm font-medium text-slate-700">Featured image</label>
        <input type="file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-800 hover:file:bg-emerald-100">
        @if ($blog->featured_image)
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <img src="{{ $blog->featuredImageUrl() }}" alt="" class="h-20 w-auto rounded-lg border border-slate-200 object-cover">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="remove_featured_image" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" @checked(old('remove_featured_image'))>
                    Remove current image
                </label>
            </div>
        @endif
        @error('featured_image')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="lg:col-span-2">
        <label for="blog_content" class="mb-1.5 block text-sm font-medium text-slate-700">Content</label>
        <textarea id="blog_content" name="content" rows="16" class="block w-full rounded-lg border-slate-300 font-mono text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('content') border-red-500 @enderror">{{ old('content', $blog->content) }}</textarea>
        @error('content')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-slate-500">TinyMCE (open source, no API key). Uses local <code class="rounded bg-slate-100 px-1">public/vendor/tinymce</code> when present after <code class="rounded bg-slate-100 px-1">npm install</code>; otherwise loads the same build from jsDelivr. Images upload to our server.</p>
    </div>
    <div>
        <label for="meta_title" class="mb-1.5 block text-sm font-medium text-slate-700">SEO meta title</label>
        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $blog->meta_title) }}" maxlength="255" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <div>
        <label for="meta_description" class="mb-1.5 block text-sm font-medium text-slate-700">SEO meta description</label>
        <textarea id="meta_description" name="meta_description" rows="2" maxlength="500" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('meta_description', $blog->meta_description) }}</textarea>
    </div>
</div>

@php
    $tinymceVersion = '6.8.4';
    $tinymceLocalPath = public_path('vendor/tinymce/tinymce.min.js');
    $tinymceHasLocal = is_file($tinymceLocalPath);
    $tinymceScript = $tinymceHasLocal
        ? asset('vendor/tinymce/tinymce.min.js')
        : 'https://cdn.jsdelivr.net/npm/tinymce@'.$tinymceVersion.'/tinymce.min.js';
    $tinymceBase = $tinymceHasLocal
        ? rtrim(asset('vendor/tinymce'), '/')
        : 'https://cdn.jsdelivr.net/npm/tinymce@'.$tinymceVersion;
@endphp
@push('scripts')
<script src="{{ $tinymceScript }}" referrerpolicy="origin"></script>
<script>
(function () {
    var uploadUrl = @json(route('admin.blogs.upload-editor-image'));
    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    var tinymceBase = @json($tinymceBase);

    function initTinyMCE() {
        if (typeof tinymce === 'undefined') {
            console.error('TinyMCE failed to load. Check network tab for script errors, or run npm install and deploy public/vendor/tinymce.');
            return;
        }
        if (!document.getElementById('blog_content')) {
            return;
        }
        tinymce.init({
            selector: '#blog_content',
            base_url: tinymceBase,
            suffix: '.min',
            height: 520,
            menubar: false,
            branding: false,
            promotion: false,
            plugins: 'lists link image table code autoresize wordcount paste',
            toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | code removeformat',
            block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3',
            relative_urls: false,
            convert_urls: true,
            paste_data_images: true,
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function (resolve, reject) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', uploadUrl);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.onload = function () {
                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('HTTP ' + xhr.status);
                            return;
                        }
                        try {
                            var json = JSON.parse(xhr.responseText);
                            if (!json || typeof json.location !== 'string') {
                                reject('Invalid upload response');
                                return;
                            }
                            resolve(json.location);
                        } catch (e) {
                            reject(e);
                        }
                    };
                    xhr.onerror = function () { reject('Network error'); };
                    var formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    xhr.send(formData);
                });
            },
            content_style: 'body { font-family: system-ui, -apple-system, sans-serif; font-size: 16px; line-height: 1.6; }',
        });
    }

    document.querySelector('form')?.addEventListener('submit', function () {
        if (window.tinymce) tinymce.triggerSave();
    });

    var slugInput = document.getElementById('slug');
    var slugPreview = document.getElementById('slug-preview');
    var titleInput = document.getElementById('title');
    function slugify(s) {
        return s.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '') || 'your-slug';
    }
    function refreshSlugPreview() {
        if (!slugPreview) return;
        var manual = slugInput && slugInput.value.trim();
        slugPreview.textContent = manual ? slugify(manual) : (titleInput ? slugify(titleInput.value) : 'your-slug');
    }
    slugInput?.addEventListener('input', refreshSlugPreview);
    titleInput?.addEventListener('input', refreshSlugPreview);
    refreshSlugPreview();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTinyMCE);
    } else {
        initTinyMCE();
    }
})();
</script>
@endpush
