@extends('front.master')

@section('seo_title', 'Velinex Labs — parent technology partner behind eConfirm | '.site_setting('site_name'))
@section('seo_description', 'Velinex Labs is the Nairobi-based software studio that engineers, tests, and supports eConfirm. Learn how Velinex powers escrow, payments, and product delivery—and explore Velinex Labs for AI, blockchain, mobile, and web.')
@section('seo_keywords', 'Velinex Labs, eConfirm parent company, Nairobi software studio, M-Pesa escrow technology, Velinex Labs Kenya, custom software Velinex')
@section('canonical_url', route('velinex.labs'))

@push('structured_data')
<script type="application/ld+json">
@json([
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => 'Velinex Labs & eConfirm',
    'description' => 'Velinex Labs is the technology partner behind eConfirm, providing engineering, testing, and product delivery for Kenya escrow and M-Pesa workflows.',
    'url' => route('velinex.labs'),
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => site_setting('site_name'),
        'url' => rtrim(config('app.url', url('/')), '/'),
    ],
    'about' => [
        '@type' => 'Organization',
        'name' => 'Velinex Labs',
        'url' => 'https://www.velinexlabs.com/',
        'description' => 'Custom software studio in Nairobi building AI, blockchain, mobile, and web products for ambitious teams.',
        'areaServed' => 'KE',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
</script>
@endpush

@section('content')
@php
    $velinexHome = 'https://www.velinexlabs.com/';
    $velinexContact = 'https://www.velinexlabs.com/contact';
    $velinexWork = 'https://www.velinexlabs.com/work';
    $velinexBlog = 'https://www.velinexlabs.com/blog';
@endphp

<section class="relative py-16 lg:py-22 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-30 pointer-events-none" aria-hidden="true">
        <div class="absolute top-0 right-0 w-96 h-96 bg-sky-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-emerald-600 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-sm font-semibold text-sky-300 uppercase tracking-widest mb-3">Technology partner</p>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight mb-5">Velinex Labs</h1>
        <p class="text-lg sm:text-xl text-slate-300 leading-relaxed max-w-2xl mx-auto mb-8">
            The <strong class="text-white">parent software studio</strong> that designs, builds, and runs technical programmes for <strong class="text-white">eConfirm</strong>—including engineering work, integration testing, and the kind of product discipline you need when money moves on M-Pesa.
        </p>
        <div class="flex flex-col sm:flex-row flex-wrap items-center justify-center gap-3">
            <a href="{{ $velinexHome }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 shadow-lg hover:bg-slate-100 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900">
                Visit Velinex Labs
                <i class="fas fa-external-link-alt text-xs opacity-70" aria-hidden="true"></i>
            </a>
            <a href="{{ $velinexContact }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/25 bg-white/5 px-5 py-3 text-sm font-semibold text-white hover:bg-white/10 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900">
                Start a project with Velinex
            </a>
        </div>
    </div>
</section>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-18 space-y-14">
    <section class="scroll-mt-24" aria-labelledby="velinex-econfirm-heading">
        <h2 id="velinex-econfirm-heading" class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Velinex Labs and eConfirm</h2>
        <p class="text-gray-600 leading-relaxed mb-4">
            <a href="{{ $velinexHome }}" class="text-green-700 font-semibold hover:text-green-800 underline-offset-2 hover:underline" target="_blank" rel="noopener noreferrer">Velinex Labs</a>
            is the organisation behind much of the <strong class="text-gray-800">architecture, integrations, and quality assurance</strong> that keep eConfirm reliable in production. That includes payment-path testing, API and partner integrations (for example flows that touch M-Pesa and escrow settlement), and iterative product delivery as eConfirm grows.
        </p>
        <p class="text-gray-600 leading-relaxed mb-4">
            When we describe Velinex as the <strong class="text-gray-800">parent company</strong> in this context, we mean the <strong class="text-gray-800">engineering and product home</strong> for the team that ships and hardens eConfirm—not a separate consumer brand you pay at checkout. Your eConfirm experience—escrow creation, STK prompts, dispute tooling, and developer APIs—is built and exercised under Velinex’s delivery standards.
        </p>
        <div class="rounded-2xl border border-green-200 bg-green-50/60 px-5 py-4 text-sm text-gray-800">
            <strong class="font-semibold text-green-900">For buyers and sellers:</strong> eConfirm remains your escrow product. Velinex Labs is the software studio that helps build and stress-test that product so it behaves correctly in the real world.
        </div>
    </section>

    <section class="scroll-mt-24" aria-labelledby="velinex-about-heading">
        <h2 id="velinex-about-heading" class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Who is Velinex Labs?</h2>
        <p class="text-gray-600 leading-relaxed mb-4">
            <a href="{{ $velinexHome }}" target="_blank" rel="noopener noreferrer" class="text-green-700 font-semibold hover:text-green-800 underline-offset-2 hover:underline">Velinex Labs</a>
            is a <strong class="text-gray-800">Nairobi software studio</strong> for founders and operators who need production-grade systems—not only marketing sites. The team works across <strong class="text-gray-800">AI products, blockchain systems, mobile apps, and modern web platforms</strong>, with a process that emphasises clarity before code: positioning, visual identity, UX/UI systems, wireframes, and prototypes before engineering and launch.
        </p>
        <p class="text-gray-600 leading-relaxed mb-6">
            Velinex describes its work as helping teams <strong class="text-gray-800">shape how a product should be positioned, how it should look, how it should flow, and how it should ship</strong>, so the final build stays coherent from first concept to production. That same discipline applies to regulated, high-stakes domains—like escrow—where small mistakes are expensive.
        </p>
        <ul class="space-y-3 text-gray-700">
            <li class="flex gap-3">
                <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-800 text-xs font-bold" aria-hidden="true">1</span>
                <span><strong class="text-gray-900">Strategic positioning</strong> — audience, offer, and product direction before interfaces are locked in.</span>
            </li>
            <li class="flex gap-3">
                <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-800 text-xs font-bold" aria-hidden="true">2</span>
                <span><strong class="text-gray-900">Visual identity &amp; UX/UI</strong> — coherent digital brand language, components, and flows.</span>
            </li>
            <li class="flex gap-3">
                <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-800 text-xs font-bold" aria-hidden="true">3</span>
                <span><strong class="text-gray-900">Build &amp; launch</strong> — engineering across blockchain, mobile, web, and applied AI where it makes sense.</span>
            </li>
        </ul>
    </section>

    <section class="scroll-mt-24" aria-labelledby="velinex-capabilities-heading">
        <h2 id="velinex-capabilities-heading" class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Capabilities relevant to eConfirm</h2>
        <p class="text-gray-600 leading-relaxed mb-6">
            eConfirm sits at the intersection of <strong class="text-gray-800">payments, trust, and workflow software</strong>. Velinex Labs publicly highlights depth in areas that map closely to that stack—see their
            <a href="{{ $velinexWork }}" class="text-green-700 font-semibold hover:text-green-800 underline-offset-2 hover:underline" target="_blank" rel="noopener noreferrer">selected work</a>
            and
            <a href="{{ $velinexBlog }}" class="text-green-700 font-semibold hover:text-green-800 underline-offset-2 hover:underline" target="_blank" rel="noopener noreferrer">insights</a>
            on <a href="{{ $velinexHome }}" target="_blank" rel="noopener noreferrer" class="text-green-700 font-semibold hover:text-green-800 underline-offset-2 hover:underline">velinexlabs.com</a>.
        </p>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-5">
                <h3 class="font-semibold text-gray-900 mb-2">Web platforms &amp; ops tooling</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Dashboards, portals, and internal systems aligned to real business workflows—similar to the operator and user surfaces eConfirm depends on.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-5">
                <h3 class="font-semibold text-gray-900 mb-2">Mobile &amp; payments-aware products</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Mobile-first flows and payment infrastructure (including M-Pesa-class integrations in Velinex’s own shipped products) inform how eConfirm behaves on handsets.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-5">
                <h3 class="font-semibold text-gray-900 mb-2">Applied AI</h3>
                <p class="text-sm text-gray-600 leading-relaxed">LLM features, automation, and retrieval patterns where they improve real operations—not AI for its own sake.</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-5">
                <h3 class="font-semibold text-gray-900 mb-2">Blockchain systems</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Smart contracts, wallets, and platform architecture for products that must survive production—rigour that carries over to any high-stakes financial UX.</p>
            </div>
        </div>
    </section>

    <section class="scroll-mt-24" aria-labelledby="velinex-work-heading">
        <h2 id="velinex-work-heading" class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Selected Velinex Labs projects</h2>
        <p class="text-gray-600 leading-relaxed mb-6">
            These examples appear on Velinex’s own site as representative shipped work. They illustrate the calibre of systems Velinex builds alongside programmes like eConfirm:
        </p>
        <ul class="space-y-4">
            <li class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="font-semibold text-gray-900"><a href="{{ $velinexWork }}" target="_blank" rel="noopener noreferrer" class="text-green-700 hover:text-green-800 underline-offset-2 hover:underline">Bashiri Market</a></h3>
                <p class="text-sm text-gray-600 mt-1">Solana prediction-market style product—wallet flows, trading UX, and settlement discipline.</p>
            </li>
            <li class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="font-semibold text-gray-900"><a href="{{ $velinexWork }}" target="_blank" rel="noopener noreferrer" class="text-green-700 hover:text-green-800 underline-offset-2 hover:underline">Zeya</a></h3>
                <p class="text-sm text-gray-600 mt-1">Crypto-to-M-Pesa product with mobile-first exchange flows and Go-based payment infrastructure—directly relevant to Kenyan money movement.</p>
            </li>
            <li class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="font-semibold text-gray-900"><a href="{{ $velinexWork }}" target="_blank" rel="noopener noreferrer" class="text-green-700 hover:text-green-800 underline-offset-2 hover:underline">Insight Chess</a></h3>
                <p class="text-sm text-gray-600 mt-1">AI coaching product combining engine analysis, voice, and real-time feedback—showing Velinex’s applied AI delivery.</p>
            </li>
        </ul>
    </section>

    <section class="rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white p-8 sm:p-10" aria-labelledby="velinex-cta-heading">
        <h2 id="velinex-cta-heading" class="text-xl sm:text-2xl font-bold mb-3">Explore Velinex Labs</h2>
        <p class="text-slate-300 text-sm sm:text-base leading-relaxed mb-6">
            Official site: <a href="{{ $velinexHome }}" class="text-sky-300 font-medium hover:text-white underline underline-offset-2" target="_blank" rel="noopener noreferrer">https://www.velinexlabs.com/</a> — contact, portfolio, and engineering notes live there.
        </p>
        <div class="flex flex-col sm:flex-row flex-wrap gap-3">
            <a href="{{ $velinexHome }}" target="_blank" rel="noopener noreferrer" class="inline-flex justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-slate-100">Velinex Labs home</a>
            <a href="{{ $velinexWork }}" target="_blank" rel="noopener noreferrer" class="inline-flex justify-center rounded-xl border border-white/30 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/10">Selected work</a>
            <a href="{{ $velinexBlog }}" target="_blank" rel="noopener noreferrer" class="inline-flex justify-center rounded-xl border border-white/30 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/10">Insights / blog</a>
            <a href="{{ $velinexContact }}" target="_blank" rel="noopener noreferrer" class="inline-flex justify-center rounded-xl border border-white/30 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/10">Contact Velinex</a>
        </div>
        <p class="mt-6 text-xs text-slate-400">
            eConfirm is operated as its own product; this page explains the Velinex Labs relationship for transparency and SEO. External links open in a new tab.
        </p>
    </section>

    <nav class="text-center text-sm text-gray-500 pb-4" aria-label="Related on eConfirm">
        <a href="{{ route('home') }}" class="text-green-700 hover:underline">Home</a>
        <span class="mx-2" aria-hidden="true">·</span>
        <a href="{{ route('support') }}" class="text-green-700 hover:underline">Support</a>
        <span class="mx-2" aria-hidden="true">·</span>
        <a href="{{ route('contact') }}" class="text-green-700 hover:underline">Contact eConfirm</a>
    </nav>
</div>
@endsection
