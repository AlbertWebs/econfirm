@extends('front.master')

@section('seo_title', $seoTitle)
@section('seo_description', $seoDescription)
@section('canonical_url', $canonicalUrl)
@section('seo_keywords', 'eConfirm escrow, M-Pesa escrow, '.$productName.', '.$industryKeyword.', secure escrow Kenya, buyer seller protection')

@section('content')
<h1 class="sr-only">{{ $productName }}</h1>
<section class="bg-gradient-to-b from-emerald-50 to-white py-10 lg:py-12">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('home') }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Start with eConfirm</a>
            <a href="{{ route('contact') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Talk to support</a>
        </div>
        <div class="mt-6 border-t border-emerald-200/60 pt-6">
            <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-emerald-800/80">More options</p>
            <div class="flex flex-wrap gap-2.5 sm:gap-3">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-emerald-400 hover:bg-white">
                    Create account
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-emerald-400 hover:bg-white">
                    Sign in
                </a>
                <a href="{{ route('help') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white/80 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:border-emerald-200 hover:bg-white">
                    Help &amp; FAQs
                </a>
                <a href="{{ route('terms.conditions') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white/80 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:border-emerald-200 hover:bg-white">
                    Terms of Service
                </a>
                <a href="{{ route('scam.watch') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50/90 px-4 py-2.5 text-sm font-medium text-red-800 transition hover:bg-red-100">
                    Scam Alert
                </a>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-12">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-bold text-slate-900">Why use this escrow page?</h2>
            <ul class="mt-4 space-y-3 text-sm leading-7 text-slate-700">
                @foreach($benefits as $benefit)
                    <li class="flex gap-2">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-emerald-500"></span>
                        <span>{{ $benefit }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6 sm:p-8">
            <h2 class="text-xl font-bold text-slate-900">Frequently asked questions</h2>
            <div class="mt-4 space-y-4">
                @foreach($faqs as $faq)
                    <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-slate-200">
                        <h3 class="text-sm font-semibold text-slate-900">{{ $faq['q'] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-700">{{ $faq['a'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
