@extends('front.master')

@section('seo_title', $pageTitle)
@section('seo_description', $metaDescription)
@section('canonical_url', $canonicalUrl)

@push('structured_data')
@php
    $siteUrl = rtrim(config('app.url', url('/')), '/');
    $graph = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            '@id' => $canonicalUrl.'#collection',
            'name' => $label.' — Scam Alert',
            'description' => $metaDescription,
            'url' => $canonicalUrl,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'eConfirm',
                'url' => $siteUrl,
            ],
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => url('/'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Scam Alert',
                    'item' => route('scam.watch'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $label,
                    'item' => $canonicalUrl,
                ],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<section class="relative py-14 lg:py-16 bg-gradient-to-br from-red-50 via-white to-orange-50 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-red-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    </div>
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm text-gray-600 mb-6" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-red-600">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('scam.watch') }}" class="hover:text-red-600">Scam Alert</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-900 font-medium">{{ $label }}</li>
            </ol>
        </nav>
        <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-4">{{ $label }}</h1>
        <p class="text-lg text-gray-600 max-w-3xl mb-6">
            Explore individual reports of {{ strtolower($label) }} submitted by the community—fake job boards, romance fraud numbers, phishing sites, and more. Open a report for full details and identifiers.
        </p>
        <a href="{{ route('scam.watch') }}" class="inline-flex items-center gap-2 text-red-600 font-semibold hover:underline text-sm">
            <i class="fas fa-arrow-left"></i> All scam categories
        </a>
    </div>
</section>

<section class="py-12 lg:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-4">
            @forelse($reports as $report)
                <article class="wow-reveal bg-white border-2 border-red-100 rounded-xl p-6 hover:shadow-lg transition-all" style="--wow-delay: {{ ($loop->index % 4) * 70 }}ms;">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-xl font-bold text-gray-900 mb-2">
                                <a href="{{ route('scam.watch.show', ['report' => $report, 'slug' => $report->seoSlug()]) }}" class="hover:text-red-600">
                                    @if($report->report_type === 'website')
                                        Scam website: {{ $report->reported_value }}
                                    @elseif($report->report_type === 'phone')
                                        Phone number: {{ $report->reported_value }}
                                    @else
                                        Email: {{ $report->reported_value }}
                                    @endif
                                </a>
                            </h2>
                            <p class="text-gray-700 mb-4">{{ Str::limit($report->description, 280, '…') }}</p>
                            <div class="flex flex-wrap gap-3 text-sm text-gray-500 items-center">
                                @if($report->community)
                                    <a href="{{ route('scam.watch.community', ['community' => $report->community]) }}" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700 hover:bg-indigo-200">{{ $report->community->name }}</a>
                                @endif
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $report->is_verified ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-900' }}">{{ $report->verification_label }}</span>
                                <span><i class="fas fa-calendar text-xs"></i> {{ $report->created_at->diffForHumans() }}</span>
                                <span class="font-semibold text-red-600">{{ $report->report_count }} {{ Str::plural('report', $report->report_count) }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                            <a href="{{ route('scam.watch.show', ['report' => $report, 'slug' => $report->seoSlug()]) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 text-sm">
                                Full report
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="text-center py-16 text-gray-500">
                    <p class="text-lg mb-2">No reports in this category yet.</p>
                    <a href="{{ route('scam.watch.report') }}" class="text-red-600 font-semibold hover:underline">Be the first to report a {{ strtolower($label) }}</a>
                </div>
            @endforelse
        </div>

        @if($reports->hasPages())
            <div class="mt-10 wow-reveal" style="--wow-delay: 80ms;">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
