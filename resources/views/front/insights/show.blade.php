@extends('front.master')

@section('seo_title', filled($blog->meta_title) ? $blog->meta_title.' | '.site_setting('site_name') : $blog->title.' | '.site_setting('site_name'))
@section('seo_description', filled($blog->meta_description) ? $blog->meta_description : \Illuminate\Support\Str::limit(strip_tags((string) $blog->excerpt), 160))
@section('canonical_url', route('insights.show', $blog->slug))
@if ($blog->featuredImageUrl())
    @section('og_image', $blog->featuredImageUrl())
@endif
@section('og_type', 'article')

@push('head_extra')
<style>
    /* Ensure article list markers are always visible */
    .blog-content ul,
    .blog-content ol {
        margin-top: 1rem;
        margin-bottom: 1rem;
        padding-left: 1.5rem;
        list-style-position: outside;
    }

    .blog-content ul {
        list-style-type: disc !important;
    }

    .blog-content ol {
        list-style-type: decimal !important;
    }

    .blog-content ul ul {
        list-style-type: circle !important;
    }

    .blog-content ol ol {
        list-style-type: lower-alpha !important;
    }

    .blog-content li {
        display: list-item !important;
        margin: 0.35rem 0;
    }
</style>
@endpush

@section('content')
<article class="border-b border-slate-200/80 bg-white">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
        <nav class="mb-8 text-sm">
            <a href="{{ route('insights.index') }}" class="font-medium text-emerald-700 hover:text-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">← Back to Insights</a>
        </nav>
        <header class="border-b border-slate-100 pb-8">
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700/90">Insights</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{{ $blog->title }}</h1>
            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                <span>By <span class="font-medium text-slate-700">{{ $blog->author }}</span></span>
                <span aria-hidden="true">·</span>
                <time datetime="{{ $blog->published_at?->toAtomString() }}">{{ $blog->published_at?->format('F j, Y') }}</time>
            </div>
        </header>
        @if ($blog->featuredImageUrl())
            <figure class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                <img src="{{ $blog->featuredImageUrl() }}" alt="" class="w-full object-cover" width="1200" height="630" loading="eager" decoding="async">
            </figure>
        @endif
        <div class="blog-content mt-10 max-w-none text-base leading-relaxed text-slate-800 [&_>*+*]:mt-4 [&_h1]:text-3xl [&_h1]:font-bold [&_h2]:mt-8 [&_h2]:text-2xl [&_h2]:font-semibold [&_h3]:mt-6 [&_h3]:text-xl [&_h3]:font-semibold [&_a]:font-medium [&_a]:text-emerald-700 [&_a]:underline [&_ul]:my-4 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:my-4 [&_ol]:list-decimal [&_ol]:pl-6 [&_img]:my-6 [&_img]:max-w-full [&_img]:rounded-xl [&_table]:my-6 [&_table]:w-full [&_table]:border-collapse [&_table]:text-sm [&_th]:border [&_th]:border-slate-300 [&_th]:bg-slate-100 [&_th]:p-2 [&_th]:text-left [&_td]:border [&_td]:border-slate-300 [&_td]:p-2">
            {!! $blog->content !!}
        </div>
        <footer class="mt-12 border-t border-slate-100 pt-8">
            <a href="{{ route('insights.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                ← Back to Insights
            </a>
        </footer>
    </div>
</article>
@endsection
