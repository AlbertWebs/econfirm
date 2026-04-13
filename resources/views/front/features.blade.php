@extends('front.master')

@section('seo_title', 'Platform features — Secure escrow, fraud protection & disputes | eConfirm')
@section('seo_description', 'How eConfirm protects buyers and sellers: secure M-Pesa escrow, fraud safeguards, fast processing, and fair dispute resolution. Jump to any topic on this page.')

@section('content')
<section class="relative py-16 lg:py-20 bg-gradient-to-b from-white to-green-50/40 overflow-hidden">
    <div class="absolute inset-0 pointer-events-none opacity-40">
        <div class="absolute top-20 right-10 w-72 h-72 bg-green-200 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-80 h-80 bg-emerald-100 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-3">Why eConfirm</p>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">Platform features</h1>
        <p class="text-lg text-gray-600 leading-relaxed mb-10">
            Everything on one page—use the links below to jump to how we keep your M-Pesa escrow safe, fast, and fair.
        </p>
        <nav class="flex flex-wrap justify-center gap-2 sm:gap-3" aria-label="On this page">
            <a href="#secure-transactions" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Secure transactions</a>
            <a href="#fraud-protection" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Fraud protection</a>
            <a href="#quick-processing" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Quick processing</a>
            <a href="#dispute-resolution" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Dispute resolution</a>
        </nav>
    </div>
</section>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 lg:pb-32 space-y-20">
    <section id="secure-transactions" class="scroll-mt-28">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-green-100 to-emerald-100 text-green-700">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </span>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Secure transactions</h2>
        </div>
        <p class="text-gray-600 leading-relaxed mb-4">
            Funds move through a controlled escrow flow: money is held until the agreed conditions are met, so neither side has to trust a casual bank transfer or informal receipt alone.
        </p>
        <ul class="list-disc pl-5 space-y-2 text-gray-600 leading-relaxed">
            <li>Clear roles for payer and receiver with transaction records you can refer to later.</li>
            <li>Structured release of funds after delivery or approval, reducing “send first” risk.</li>
            <li>Designed around M-Pesa so everyday Kenyan buyers and sellers get a familiar payment path with an extra safety layer.</li>
        </ul>
    </section>

    <section id="fraud-protection" class="scroll-mt-28">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-green-100 to-emerald-100 text-green-700">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11h18M7 11V7a5 5 0 0 1 10 0v4M12 16v-5M12 16h.01"/>
                </svg>
            </span>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Fraud protection</h2>
        </div>
        <p class="text-gray-600 leading-relaxed mb-4">
            Scams often rely on urgency and untraceable payments. eConfirm adds friction for fraudsters: there is a defined transaction, visible status, and less room for fake “payment sent” stories without verification.
        </p>
        <ul class="list-disc pl-5 space-y-2 text-gray-600 leading-relaxed">
            <li>Verification-oriented flows help ensure payments match real escrow cases.</li>
            <li>Communication and status updates reduce confusion about who paid and what happens next.</li>
            <li>When something looks wrong, you have a path to escalate rather than losing money in a one-off transfer.</li>
        </ul>
    </section>

    <section id="quick-processing" class="scroll-mt-28">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-green-100 to-emerald-100 text-green-700">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                </svg>
            </span>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Quick processing</h2>
        </div>
        <p class="text-gray-600 leading-relaxed mb-4">
            You should not wait days to know if a payment landed or a step completed. The platform is built for timely updates as M-Pesa and escrow events are confirmed.
        </p>
        <ul class="list-disc pl-5 space-y-2 text-gray-600 leading-relaxed">
            <li>Real-time style status visibility as the transaction moves from pending to funded and beyond.</li>
            <li>Less back-and-forth messaging to “check if the money came in.”</li>
            <li>Typical flows aim for same-day progress whenever banks and mobile networks cooperate—exact timing can vary by case and verification needs.</li>
        </ul>
    </section>

    <section id="dispute-resolution" class="scroll-mt-28">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-green-100 to-emerald-100 text-green-700">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                </svg>
            </span>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Dispute resolution</h2>
        </div>
        <p class="text-gray-600 leading-relaxed mb-4">
            If delivery, quality, or terms are contested, escrow means funds can stay protected while the situation is reviewed. The goal is a fair outcome—not a race to withdraw before the other party reacts.
        </p>
        <ul class="list-disc pl-5 space-y-2 text-gray-600 leading-relaxed">
            <li>A structured process to present facts and evidence instead of informal arguments alone.</li>
            <li>Neutral review aligned with platform rules and the transaction you created.</li>
            <li>Support channels when you need human help to move a difficult case forward.</li>
        </ul>
    </section>

    <div class="pt-4 border-t border-gray-200">
        <a href="{{ route('home') }}#features" class="inline-flex items-center gap-2 text-green-700 font-medium hover:text-green-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to homepage features
        </a>
    </div>
</div>
@endsection
