@extends('front.master')

@section('seo_title', 'Insights | '.site_setting('site_name'))
@section('seo_description', 'Articles and updates from '.site_setting('site_name').' — escrow, payments, and product news.')
@section('canonical_url', route('insights.index'))

@section('content')
<section class="relative border-b border-slate-200/80 bg-gradient-to-b from-emerald-50/80 via-white to-white py-14 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-wider text-emerald-700/90">Insights</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Ideas &amp; updates</h1>
            <p class="mt-4 text-lg text-slate-600">Stories, guides, and announcements from our team.</p>
        </div>
    </div>
</section>

<section class="py-12 lg:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if ($blogs->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/80 px-6 py-16 text-center">
                <p class="text-slate-600">No published articles yet. Check back soon.</p>
            </div>
        @else
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($blogs as $blog)
                    <article class="group wow-reveal flex flex-col overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm transition hover:border-emerald-200/80 hover:shadow-md" style="--wow-delay: {{ ($loop->index % 3) * 90 }}ms;">
                        <a href="{{ route('insights.show', $blog->slug) }}" class="block shrink-0 overflow-hidden bg-slate-100 aspect-[16/10]">
                            @if ($blog->featuredImageUrl())
                                <img src="{{ $blog->featuredImageUrl() }}" alt="" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]" width="640" height="400" loading="lazy" decoding="async">
                            @else
                                <div class="flex h-full min-h-[10rem] items-center justify-center bg-gradient-to-br from-emerald-100/80 to-slate-100 text-emerald-700/40">
                                    <i class="fas fa-newspaper text-4xl" aria-hidden="true"></i>
                                </div>
                            @endif
                        </a>
                        <div class="flex flex-1 flex-col p-5">
                            <time class="text-xs font-medium uppercase tracking-wide text-slate-500" datetime="{{ $blog->published_at?->toAtomString() }}">
                                {{ $blog->published_at?->format('F j, Y') }}
                            </time>
                            <h2 class="mt-2 text-lg font-semibold leading-snug text-slate-900">
                                <a href="{{ route('insights.show', $blog->slug) }}" class="hover:text-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">{{ $blog->title }}</a>
                            </h2>
                            @if (filled($blog->excerpt))
                                <p class="mt-2 line-clamp-3 flex-1 text-sm text-slate-600">{{ $blog->excerpt }}</p>
                            @endif
                            <p class="mt-4 text-xs text-slate-500">By {{ $blog->author }}</p>
                            <div class="mt-4">
                                <a href="{{ route('insights.show', $blog->slug) }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                                    Read more
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-12 wow-reveal" style="--wow-delay: 80ms;">
                {{ $blogs->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
