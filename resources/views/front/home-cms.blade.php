@extends('front.master')

@section('seo_title')
    {{ $page->title }}
@endsection

@if (filled($page->meta_description))
    @section('seo_description')
        {{ $page->meta_description }}
    @endsection
@endif

@section('content')
    <div class="cms-home-body">
        {!! $page->body !!}
    </div>

    @if(isset($latestBlogs) && $latestBlogs->isNotEmpty())
        <section id="blogs" class="py-20 lg:py-24 bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-10">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 text-green-700 rounded-full text-sm font-semibold">
                            <i class="fas fa-newspaper text-xs" aria-hidden="true"></i>
                            <span>Latest Blogs</span>
                        </div>
                        <h2 class="mt-4 text-3xl sm:text-4xl font-bold text-gray-900">Insights from eConfirm</h2>
                    </div>
                    <a href="{{ route('insights.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                        View all insights
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($latestBlogs as $blog)
                        <article class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <a href="{{ route('insights.show', $blog->slug) }}" class="block aspect-[16/10] overflow-hidden bg-gray-100">
                                @if($blog->featuredImageUrl())
                                    <img src="{{ $blog->featuredImageUrl() }}" alt="{{ $blog->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy" decoding="async">
                                @else
                                    <div class="h-full w-full flex items-center justify-center bg-gradient-to-br from-green-100 to-emerald-50 text-emerald-600/70">
                                        <i class="fas fa-image text-4xl" aria-hidden="true"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="p-5">
                                <time class="text-xs uppercase tracking-wide text-gray-500" datetime="{{ $blog->published_at?->toAtomString() }}">
                                    {{ $blog->published_at?->format('F j, Y') }}
                                </time>
                                <h3 class="mt-2 text-lg font-semibold text-gray-900 leading-snug">
                                    <a href="{{ route('insights.show', $blog->slug) }}" class="hover:text-emerald-700 transition-colors">{{ $blog->title }}</a>
                                </h3>
                                @if(filled($blog->excerpt))
                                    <p class="mt-2 text-sm text-gray-600 line-clamp-3">{{ $blog->excerpt }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
