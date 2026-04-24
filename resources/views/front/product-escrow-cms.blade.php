@extends('front.master')

@section('seo_title', $seoTitle)
@section('seo_description', $seoDescription)
@section('canonical_url', $canonicalUrl)

@section('content')
<section class="relative overflow-hidden bg-gradient-to-b from-emerald-50 via-white to-slate-50 py-12 sm:py-16">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -top-8 -right-8 h-48 w-48 rounded-full bg-emerald-200/40 blur-3xl"></div>
        <div class="absolute -bottom-16 -left-8 h-64 w-64 rounded-full bg-sky-200/30 blur-3xl"></div>
    </div>
    <div class="relative mx-auto w-full max-w-6xl px-4 sm:px-6">
        <div class="grid gap-6 lg:grid-cols-3 lg:gap-8">
            <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                <h1 class="mb-3 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $page->title }}</h1>
                @if(filled($page->meta_description))
                    <p class="mb-6 text-sm leading-6 text-slate-600">{{ $page->meta_description }}</p>
                @endif
                <div class="prose prose-slate max-w-none prose-headings:font-semibold prose-a:text-emerald-700">
                    {!! $page->body !!}
                </div>
            </div>

            <aside class="space-y-4">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-800">Quick Actions</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-900">Start protected now</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-700">Create a secure escrow workflow and release funds only when project terms are met.</p>
                    <div class="mt-4 space-y-2">
                        <a href="{{ route('home') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Start escrow</a>
                        <a href="{{ route('contact') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-emerald-300 bg-white px-4 py-2.5 text-sm font-semibold text-emerald-800 hover:bg-emerald-100">Talk to our team</a>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-slate-900">Useful links</p>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li><a href="{{ route('security') }}" class="text-emerald-700 hover:underline">Security & assurance</a></li>
                        <li><a href="{{ route('support') }}" class="text-emerald-700 hover:underline">Support center</a></li>
                        <li><a href="{{ route('help') }}" class="text-emerald-700 hover:underline">FAQs & help</a></li>
                        <li><a href="{{ route('scam.watch') }}" class="text-emerald-700 hover:underline">Scam Watch alerts</a></li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

@if(($productKey ?? '') === 'construction')
<section class="bg-white py-12">
    <div class="mx-auto w-full max-w-6xl px-4 sm:px-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-2xl font-bold text-slate-900">Construction escrow workflow</h2>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">Reduce site payment risk by tying release to milestones and verification points.</p>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Step 1</p>
                    <h3 class="mt-1 font-semibold text-slate-900">Fund the milestone</h3>
                    <p class="mt-2 text-sm text-slate-600">Buyer secures funds before materials or labor begin.</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Step 2</p>
                    <h3 class="mt-1 font-semibold text-slate-900">Verify progress</h3>
                    <p class="mt-2 text-sm text-slate-600">Confirm completion through delivery notes, photos, and agreed checks.</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Step 3</p>
                    <h3 class="mt-1 font-semibold text-slate-900">Release payment</h3>
                    <p class="mt-2 text-sm text-slate-600">Funds are released only after both parties agree milestone conditions are met.</p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Create construction escrow</a>
                <a href="{{ route('terms.conditions') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Read terms of service</a>
            </div>
        </div>
    </div>
</section>
@endif
@endsection
