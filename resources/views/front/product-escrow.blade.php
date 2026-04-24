@extends('front.master')

@section('seo_title', $seoTitle)
@section('seo_description', $seoDescription)
@section('canonical_url', $canonicalUrl)
@section('seo_keywords', 'eConfirm escrow, M-Pesa escrow, '.$productName.', '.$industryKeyword.', secure escrow Kenya, buyer seller protection')

@section('content')
<section class="bg-gradient-to-b from-emerald-50 to-white py-14 lg:py-20">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <p class="mb-3 text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700">Escrow Solutions</p>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{{ $productName }}</h1>
        <p class="mt-4 max-w-3xl text-base leading-7 text-slate-700">
            Use eConfirm to secure {{ strtolower($productName) }} with trusted M-Pesa escrow controls.
            Ideal for {{ $industryKeyword }} where buyer-seller trust and payment release conditions matter.
        </p>
        <div class="mt-7 flex flex-wrap gap-3">
            <a href="{{ route('home') }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Start with eConfirm</a>
            <a href="{{ route('contact') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Talk to support</a>
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
