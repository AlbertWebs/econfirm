@extends('front.master')

@section('seo_title', $pageTitle)
@section('seo_description', $metaDescription)
@section('canonical_url', $canonicalUrl)
@section('og_type', 'article')

@push('structured_data')
@php
    $siteUrl = rtrim(config('app.url', url('/')), '/');
    $graph = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            '@id' => $canonicalUrl.'#webpage',
            'url' => $canonicalUrl,
            'name' => $pageTitle,
            'description' => $metaDescription,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'eConfirm',
                'url' => $siteUrl,
            ],
            'breadcrumb' => ['@id' => $canonicalUrl.'#breadcrumb'],
            'mainEntity' => ['@id' => $canonicalUrl.'#article'],
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            '@id' => $canonicalUrl.'#breadcrumb',
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
                    'name' => $report->category_label,
                    'item' => route('scam.watch.category', ['category' => $report->category]),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 4,
                    'name' => Str::limit((string) $report->reported_value, 60, '…'),
                    'item' => $canonicalUrl,
                ],
            ],
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            '@id' => $canonicalUrl.'#article',
            'headline' => Str::limit((string) $report->reported_value, 110, '…').' — '.$report->category_label,
            'description' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($report->description))), 300, '…'),
            'datePublished' => $report->created_at?->toIso8601String(),
            'dateModified' => $report->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => 'eConfirm community reports',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'eConfirm',
                'url' => $siteUrl,
            ],
            'mainEntityOfPage' => ['@id' => $canonicalUrl.'#webpage'],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
<section class="relative py-10 lg:py-14 bg-gradient-to-br from-red-50 via-white to-orange-50 border-b border-red-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm text-gray-600 mb-6" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-red-600">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('scam.watch') }}" class="hover:text-red-600">Scam Alert</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('scam.watch.category', ['category' => $report->category]) }}" class="hover:text-red-600">{{ $report->category_label }}</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-900 font-medium truncate max-w-[12rem] sm:max-w-none">{{ Str::limit((string) $report->reported_value, 48, '…') }}</li>
            </ol>
        </nav>

        <p class="text-sm font-semibold text-red-600 uppercase tracking-wide mb-2">Community fraud alert</p>
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4 leading-tight">
            @if($report->report_type === 'website')
                Reported scam website: <span class="text-red-700 break-all">{{ $report->reported_value }}</span>
            @elseif($report->report_type === 'phone')
                Reported scam phone number: <span class="text-red-700 font-mono">{{ $report->reported_value }}</span>
            @else
                Reported fraudulent email: <span class="text-red-700 break-all">{{ $report->reported_value }}</span>
            @endif
        </h1>
        <p class="text-lg text-gray-600 mb-6">
            This entry is filed under <a href="{{ route('scam.watch.category', ['category' => $report->category]) }}" class="text-red-600 font-semibold hover:underline">{{ $report->category_label }}</a>.
            Always verify offers and never send money to strangers.
        </p>
        <div class="flex flex-wrap gap-3 text-sm">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-medium
                {{ $report->is_verified ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-900' }}"
                title="{{ $report->is_verified ? 'This report has been reviewed and approved for publication.' : 'This report is pending staff review.' }}">
                <i class="fas {{ $report->is_verified ? 'fa-check-circle' : 'fa-clock' }} text-xs" aria-hidden="true"></i>
                {{ $report->verification_label }}
            </span>
            <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 font-medium">{{ $report->category_label }}</span>
            <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 capitalize">{{ $report->report_type }}</span>
            @if($report->report_count > 1)
                <span class="px-3 py-1 rounded-full bg-orange-100 text-orange-800 font-medium">{{ $report->report_count }} community {{ Str::plural('report', $report->report_count) }}</span>
            @endif
        </div>
    </div>
</section>

<section class="py-12 lg:py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <article class="prose prose-lg max-w-none">
            <h2 class="text-xl font-bold text-gray-900 mb-4">What was reported</h2>
            <div class="text-gray-700 leading-relaxed whitespace-pre-wrap border-l-4 border-red-200 pl-4 py-2 bg-red-50/50 rounded-r-lg">{{ $report->description }}</div>

            <dl class="mt-8 grid sm:grid-cols-2 gap-4 text-sm not-prose">
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-gray-500 font-medium">Verification</dt>
                    <dd class="text-gray-900 font-semibold">{{ $report->verification_label }}</dd>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-gray-500 font-medium">First reported</dt>
                    <dd class="text-gray-900 font-semibold">{{ $report->created_at->format('F j, Y') }} ({{ $report->created_at->diffForHumans() }})</dd>
                </div>
                @if($report->date_of_incident)
                <div class="bg-gray-50 rounded-lg p-4">
                    <dt class="text-gray-500 font-medium">Date of incident</dt>
                    <dd class="text-gray-900 font-semibold">{{ $report->date_of_incident->format('F j, Y') }}</dd>
                </div>
                @endif
                <div class="bg-gray-50 rounded-lg p-4 sm:col-span-2">
                    <dt class="text-gray-500 font-medium">Reported identifier</dt>
                    <dd class="text-red-700 font-mono text-base break-all">{{ $report->reported_value }}</dd>
                </div>
            </dl>

            @if ($report->is_verified && is_array($report->evidence) && count(array_filter($report->evidence, fn ($p) => is_string($p) && $p !== '')) > 0)
                <div class="mt-8 not-prose border-t border-gray-200 pt-8" aria-label="Report evidence">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Evidence</h2>
                    <p class="text-sm text-gray-600 mb-4">Submitted files (download for your records).</p>
                    <ul class="space-y-2">
                        @foreach ($report->evidence as $i => $path)
                            @if (! is_string($path) || $path === '')
                                @continue
                            @endif
                            <li>
                                <a
                                    href="{{ route('scam.watch.evidence', ['report' => $report, 'index' => $i]) }}"
                                    class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50/80 px-4 py-2.5 text-sm font-semibold text-red-800 transition hover:border-red-300 hover:bg-red-100"
                                    download
                                >
                                    <i class="fas fa-download text-xs" aria-hidden="true"></i>
                                    Download: {{ basename($path) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </article>

        <div class="mt-10 flex flex-wrap items-center justify-between gap-4 pt-8 border-t border-gray-200">
            <button
                type="button"
                x-data="{ liked: false, likesCount: {{ $report->likes_count ?? 0 }}, loading: false }"
                x-init="likesCount = {{ $report->likes_count ?? 0 }}"
                @click="likeReportDetail({{ $report->id }}, $el)"
                :class="liked ? 'text-red-600' : 'text-gray-600 hover:text-red-600'"
                class="text-sm transition-colors duration-200 flex items-center gap-2 disabled:opacity-50"
                :disabled="loading || liked">
                <i class="fas fa-thumbs-up text-xs"></i>
                <span x-text="liked ? 'Marked as helpful' : 'Helpful if this warning saved you time'"></span>
                <span x-show="likesCount > 0" class="text-xs font-semibold" x-text="'(' + likesCount + ')'"></span>
            </button>
            <a href="{{ route('scam.watch.report') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors text-sm">
                <i class="fas fa-flag"></i> Report another scam
            </a>
        </div>
    </div>
</section>

<section id="comments" class="py-12 bg-gray-50 border-y border-gray-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Discussion</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Share context that may help others stay safe.
                    @if(($report->comments_count ?? 0) > 0)
                        {{ $report->comments_count }} {{ Str::plural('comment', (int) $report->comments_count) }} so far.
                    @endif
                </p>
            </div>
            <a href="#new-comment" class="text-sm font-semibold text-red-600 hover:underline">Add comment</a>
        </div>

        <div id="new-comment" class="rounded-xl border border-gray-200 bg-white p-5 sm:p-6 mb-8">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Post a comment</h3>
            <form method="POST" action="{{ route('scam.watch.comments.store', ['report' => $report]) }}" class="space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label for="author_name" class="block text-sm font-medium text-gray-700 mb-1">Name (optional)</label>
                        <input id="author_name" name="author_name" type="text" maxlength="80" value="{{ old('author_name') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none" placeholder="Anonymous">
                    </div>
                    <div>
                        <label for="author_email" class="block text-sm font-medium text-gray-700 mb-1">Email (optional, not shown)</label>
                        <input id="author_email" name="author_email" type="email" maxlength="255" value="{{ old('author_email') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none" placeholder="you@example.com">
                    </div>
                </div>
                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                    <textarea id="body" name="body" rows="4" maxlength="2000" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none" placeholder="What happened? Any warning signs others should watch for?">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition-colors">
                    <i class="fas fa-paper-plane text-xs"></i>
                    Post comment
                </button>
            </form>
        </div>

        <div class="space-y-4">
            @forelse($comments as $comment)
                <article class="rounded-xl border border-gray-200 bg-white p-5">
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm">
                        <p class="font-semibold text-gray-900">{{ $comment->author_name ?: 'Anonymous' }}</p>
                        <span class="text-gray-400">•</span>
                        <time class="text-gray-500" datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time>
                    </div>
                    <p class="mt-3 whitespace-pre-wrap text-sm leading-7 text-gray-700">{{ $comment->body }}</p>

                    <details class="mt-3">
                        <summary class="cursor-pointer text-xs font-semibold text-red-600 hover:underline">Reply</summary>
                        <form method="POST" action="{{ route('scam.watch.comments.store', ['report' => $report]) }}" class="mt-3 space-y-3 rounded-lg bg-gray-50 p-3">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <div class="grid sm:grid-cols-2 gap-2">
                                <input name="author_name" type="text" maxlength="80" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none" placeholder="Your name (optional)">
                                <input name="author_email" type="email" maxlength="255" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none" placeholder="Email (optional)">
                            </div>
                            <textarea name="body" rows="3" maxlength="2000" required class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none" placeholder="Add a reply"></textarea>
                            <button type="submit" class="rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 transition-colors">Post reply</button>
                        </form>
                    </details>

                    @if($comment->children->isNotEmpty())
                        <div class="mt-4 space-y-3 border-l-2 border-gray-100 pl-4">
                            @foreach($comment->children as $reply)
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs">
                                        <p class="font-semibold text-gray-900">{{ $reply->author_name ?: 'Anonymous' }}</p>
                                        <span class="text-gray-400">•</span>
                                        <time class="text-gray-500" datetime="{{ $reply->created_at->toIso8601String() }}">{{ $reply->created_at->diffForHumans() }}</time>
                                    </div>
                                    <p class="mt-2 whitespace-pre-wrap text-sm leading-6 text-gray-700">{{ $reply->body }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center">
                    <p class="text-gray-700 font-medium">No comments yet.</p>
                    <p class="mt-1 text-sm text-gray-500">Start the thread and help others with context.</p>
                </div>
            @endforelse
        </div>

        @if($comments->hasPages())
            <div class="mt-6">
                {{ $comments->links() }}
            </div>
        @endif
    </div>
</section>

@if($related->isNotEmpty())
<section class="py-12 lg:py-16 bg-gray-50 border-t border-gray-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">More {{ $report->category_label }}</h2>
        <p class="text-gray-600 mb-8">Related reports in the same category.</p>
        <div class="grid md:grid-cols-2 gap-4">
            @foreach($related as $r)
                <a href="{{ route('scam.watch.show', ['report' => $r, 'slug' => $r->seoSlug()]) }}" class="block bg-white border border-red-100 rounded-xl p-5 hover:shadow-md hover:border-red-200 transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-red-600 uppercase">{{ ucfirst($r->report_type) }}</p>
                            <p class="font-semibold text-gray-900 truncate">{{ Str::limit((string) $r->reported_value, 70, '…') }}</p>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($r->description, 120, '…') }}</p>
                        </div>
                        @if($r->report_count > 1)
                            <span class="shrink-0 text-xs font-bold bg-red-500 text-white px-2 py-1 rounded-full">{{ $r->report_count }}×</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-8 text-center">
            <a href="{{ route('scam.watch.category', ['category' => $report->category]) }}" class="inline-flex items-center gap-2 text-red-600 font-semibold hover:underline">
                View all {{ $report->category_label }} <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</section>
@endif

<script>
function likeReportDetail(reportId, buttonElement) {
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
            alert(data.message || 'Unable to record feedback.');
        }
    })
    .catch(() => {
        xData.loading = false;
        alert('An error occurred. Please try again.');
    });
}
</script>
@endsection
