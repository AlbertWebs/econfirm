@extends('front.master')

@section('seo_title', $pageTitle)
@section('seo_description', $metaDescription)
@section('canonical_url', $canonicalUrl)

@section('content')
<section class="relative py-10 lg:py-14 bg-gradient-to-br from-indigo-50 via-white to-red-50 border-b border-indigo-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm text-gray-600 mb-6" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-red-600">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('scam.watch') }}" class="hover:text-red-600">Scam Alert</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-900 font-medium">{{ $community->name }}</li>
            </ol>
        </nav>
        <a href="{{ route('scam.watch') }}" class="inline-flex items-center gap-2 text-red-600 font-semibold hover:underline text-sm">
            <i class="fas fa-arrow-left"></i> Back to Scam Alert
        </a>
        <h1 class="mt-4 text-3xl sm:text-4xl font-bold text-gray-900">{{ $community->name }}</h1>
        <p class="mt-3 text-gray-600 max-w-3xl">
            Community-specific scam reports and discussions. Share warnings with members so others avoid similar fraud.
        </p>
        @auth
            <div class="mt-4">
                @if(($adminRole?->status ?? null) === 'approved')
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                        <i class="fas fa-check-circle"></i> You are an approved community admin
                    </span>
                @elseif(($adminRole?->status ?? null) === 'pending')
                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-900">
                        <i class="fas fa-clock"></i> Community admin request pending platform approval
                    </span>
                @else
                    <form method="post" action="{{ route('scam.watch.community.admin.request', ['community' => $community]) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-indigo-300 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                            <i class="fas fa-user-shield"></i> Request to become community admin
                        </button>
                    </form>
                @endif
            </div>
        @endauth
    </div>
</section>

<section class="py-12 lg:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-4">
            @forelse($reports as $report)
                <article class="wow-reveal bg-white border-2 border-indigo-100 rounded-xl p-6 hover:shadow-lg transition-all" style="--wow-delay: {{ ($loop->index % 4) * 70 }}ms;">
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
                            <p class="text-gray-700 mb-4">{{ Str::limit($report->description, 260, '…') }}</p>
                            <div class="flex flex-wrap gap-3 text-sm text-gray-500 items-center">
                                <a href="{{ route('scam.watch.category', ['category' => $report->category]) }}" class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 hover:bg-red-200">{{ $report->category_label }}</a>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $report->is_verified ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-900' }}">{{ $report->verification_label }}</span>
                                <span><i class="fas fa-calendar text-xs"></i> {{ $report->created_at->diffForHumans() }}</span>
                                <span class="font-semibold text-red-600">{{ $report->report_count }} {{ Str::plural('report', $report->report_count) }}</span>
                            </div>
                            @if(($adminRole?->status ?? null) === 'approved' && ($report->community_moderation_status ?? null) !== 'approved')
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <form method="post" action="{{ route('scam.watch.community.reports.moderate', ['community' => $community, 'report' => $report]) }}">
                                        @csrf
                                        <input type="hidden" name="decision" value="approved">
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                                            <i class="fas fa-check"></i> Approve report
                                        </button>
                                    </form>
                                    <form method="post" action="{{ route('scam.watch.community.reports.moderate', ['community' => $community, 'report' => $report]) }}">
                                        @csrf
                                        <input type="hidden" name="decision" value="rejected">
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700">
                                            <i class="fas fa-times"></i> Reject report
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                            <a href="{{ route('scam.watch.show', ['report' => $report, 'slug' => $report->seoSlug()]) }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 text-sm">
                                View full report
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="text-center py-16 text-gray-500">
                    <p class="text-lg mb-2">No reports in this community yet.</p>
                    <a href="{{ route('scam.watch.report') }}" class="text-red-600 font-semibold hover:underline">Be the first to report a scam for this community</a>
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
