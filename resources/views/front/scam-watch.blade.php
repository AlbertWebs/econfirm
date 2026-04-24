@extends('front.master')

@section('seo_title', 'Scam Alert: Reported Scams, Fake Sites & Fraud Alerts | eConfirm')
@section('seo_description', 'Browse community-reported scams: fake job ads, romance fraud phone numbers, phishing websites, and suspicious emails. Search, filter by category, and report scams to protect others in Kenya and beyond.')
@section('canonical_url', route('scam.watch'))
@section('seo_keywords', 'scam alert Kenya, reported scam websites, fake job ads, romance scam numbers, phishing email list, fraud alerts, eConfirm scam reports')

@push('structured_data')
@php
    $siteUrl = rtrim(config('app.url', url('/')), '/');
    $swGraph = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            '@id' => route('scam.watch').'#collection',
            'name' => 'Scam Alert — community fraud reports',
            'description' => 'Directory of user-reported scam websites, phone numbers, and emails with categories including job scams and romance scams.',
            'url' => route('scam.watch'),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'eConfirm',
                'url' => $siteUrl,
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($swGraph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<!-- Scam Alert hero section -->
<section class="relative py-16 lg:py-20 bg-gradient-to-br from-red-50 via-white to-orange-50 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-red-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-orange-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-2xl mb-6">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-4">
                Scam Alert
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 mb-8">
                Stay informed about reported scams and fraudulent websites. Help protect others by reporting suspicious activities.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('scam.watch.report') }}" class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Report a Scam
                </a>
                <a href="#scam-list" class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-red-500 hover:text-red-600 transition-all duration-200">
                    View Reported Scams
                </a>
            </div>
        </div>

        <div class="mt-6 pt-5 border-t border-red-100/60 w-full max-w-4xl mx-auto">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 text-center sm:text-left">Browse by type</p>
            <div class="flex flex-wrap justify-center sm:justify-start gap-1.5 sm:gap-2">
                @foreach(\App\Models\ScamReport::CATEGORY_LABELS as $catKey => $catLabel)
                    @php $catCount = (int) ($categoryCounts[$catKey] ?? 0); @endphp
                    @php $shortLabel = \App\Models\ScamReport::CATEGORY_SHORT_LABELS[$catKey] ?? $catLabel; @endphp
                    <a href="{{ route('scam.watch.category', ['category' => $catKey]) }}"
                       title="{{ $catLabel }} — {{ $catCount }} {{ Str::plural('report', $catCount) }}"
                       class="inline-flex items-center gap-0.5 px-2 py-1 rounded-md text-xs font-medium bg-white/90 border border-red-200/80 text-red-800 hover:bg-red-50 hover:border-red-300 transition-colors leading-tight shadow-sm">
                        <span>{{ $shortLabel }}</span><span class="text-red-500 font-semibold tabular-nums">({{ $catCount }})</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Scam List Section -->
<section id="scam-list" class="py-16 lg:py-20 bg-white" x-data="{
    searchQuery: '',
    matchesFilter(report) {
        if (!this.searchQuery) return true;
        const query = this.searchQuery.toLowerCase();
        const reportedValue = (report.reported_value || '').toLowerCase();
        const description = (report.description || '').toLowerCase();
        const category = (report.category || '').toLowerCase();
        const categoryOther = (report.category_other || '').toLowerCase();
        return reportedValue.includes(query) || description.includes(query) || category.includes(query) || categoryOther.includes(query);
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Reported Scams & Fraudulent Activities</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Browse through reported scams including fraudulent websites, phone numbers, and email addresses. Always verify before making any transactions or sharing personal information.
            </p>
        </div>

        <!-- Search -->
        <div class="mb-8 bg-gray-50 rounded-xl p-6 w-full">
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <div class="flex flex-col sm:flex-row gap-3 w-full">
                <input type="text"
                       x-model.debounce.300ms="searchQuery"
                       placeholder="Search by website, name, or description..."
                       class="w-full min-w-0 flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                <button type="button"
                        @click="searchQuery = ''"
                        class="shrink-0 w-full sm:w-auto px-6 py-2 bg-white border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times text-sm"></i>
                    Clear
                </button>
            </div>
        </div>

        <!-- Scam List -->
        <div class="space-y-4">
            @forelse($reports as $report)
                <div x-show="matchesFilter({
                    reported_value: '{{ $report->reported_value }}',
                    description: '{{ addslashes($report->description) }}',
                    category: '{{ $report->category }}',
                    category_other: '{{ addslashes((string) ($report->category_other ?? '')) }}'
                })"
                     class="bg-white border-2 border-red-200 rounded-xl p-6 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                <h3 class="text-xl font-bold text-gray-900">
                                    <a href="{{ route('scam.watch.show', ['report' => $report, 'slug' => $report->seoSlug()]) }}" class="hover:text-red-600">
                                    @if($report->report_type === 'website')
                                        Reported Scam Website
                                    @elseif($report->report_type === 'phone')
                                        Suspicious Phone Number
                                    @else
                                        Fraudulent Email Address
                                    @endif
                                    </a>
                                </h3>
                                @if($report->report_count > 1)
                                    <span class="px-3 py-1 bg-red-500 text-white text-sm font-bold rounded-full flex items-center gap-1">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $report->report_count }} {{ Str::plural('Report', $report->report_count) }}
                                    </span>
                                @endif
                                <a href="{{ route('scam.watch.category', ['category' => $report->category]) }}" class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full hover:bg-red-200 transition-colors max-w-[14rem] sm:max-w-none truncate inline-block align-bottom" title="{{ $report->category_label }}">{{ $report->category_label }}</a>
                                <span class="px-2 py-1 
                                    @if($report->report_type === 'website') bg-blue-100 text-blue-700
                                    @elseif($report->report_type === 'phone') bg-purple-100 text-purple-700
                                    @else bg-green-100 text-green-700
                                    @endif text-xs font-semibold rounded-full capitalize">
                                    {{ ucfirst($report->report_type) }}
                                </span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $report->is_verified ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-900' }}">
                                    {{ $report->verification_label }}
                                </span>
                            </div>
                            <p class="text-gray-600 mb-2 break-words">
                                <strong>
                                    @if($report->report_type === 'website')
                                        Website:
                                    @elseif($report->report_type === 'phone')
                                        Phone:
                                    @else
                                        Email:
                                    @endif
                                </strong>
                                <span class="text-red-600 font-mono break-all">{{ $report->reported_value }}</span>
                            </p>
                            <p class="text-gray-600 mb-4 break-words">
                                {{ $report->description }}
                            </p>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-calendar text-xs"></i> First reported: {{ $report->created_at->diffForHumans() }}
                                </span>
                                <span class="flex items-center gap-1 font-semibold text-red-600">
                                    <i class="fas fa-users text-xs"></i> Reported {{ $report->report_count }} {{ Str::plural('time', $report->report_count) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-xs"></i> 
                                    @if($report->report_count >= 10)
                                        <span class="text-red-600 font-semibold">High Risk</span>
                                    @elseif($report->report_count >= 5)
                                        <span class="text-orange-600 font-semibold">Medium Risk</span>
                                    @else
                                        <span class="text-yellow-600 font-semibold">Low Risk</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                @if($report->report_type === 'website')
                                    <i class="fas fa-globe text-red-600"></i>
                                @elseif($report->report_type === 'phone')
                                    <i class="fas fa-phone text-red-600"></i>
                                @else
                                    <i class="fas fa-envelope text-red-600"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end mt-4 pt-4 border-t border-gray-100">
                        <button 
                            type="button"
                            x-data="{ 
                                liked: false, 
                                likesCount: {{ $report->likes_count ?? 0 }},
                                loading: false 
                            }"
                            x-init="likesCount = {{ $report->likes_count ?? 0 }}"
                            @click="likeReport({{ $report->id }}, $el)"
                            :class="liked ? 'text-red-600' : 'text-gray-600 hover:text-red-600'"
                            class="text-sm transition-colors duration-200 flex items-center gap-2 disabled:opacity-50"
                            :disabled="loading || liked">
                            <i class="fas fa-thumbs-up text-xs" :class="liked ? 'text-red-600' : ''"></i>
                            <span x-text="liked ? 'Liked!' : 'Like if this was helpful'"></span>
                            <span x-show="likesCount > 0" class="text-xs font-semibold" x-text="'(' + likesCount + ')'"></span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-shield-alt text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg">No scam reports available yet</p>
                    <p class="text-sm mt-2">Help protect others — <a href="{{ route('scam.watch.report') }}" class="text-red-600 font-semibold hover:underline">report a scam</a>.</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($reports->hasPages())
                <div class="mt-8">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

<script>
function likeReport(reportId, buttonElement) {
    const button = buttonElement;
    const xData = Alpine.$data(button);

    if (xData.loading || xData.liked) return;

    xData.loading = true;

    fetch(`/like-scam-report/${reportId}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        xData.loading = false;
        if (data.success) {
            xData.liked = true;
            xData.likesCount = data.likes_count;
        } else {
            alert(data.message || 'Unable to like this report.');
        }
    })
    .catch(() => {
        xData.loading = false;
        alert('An error occurred. Please try again.');
    });
}
</script>
@endsection
