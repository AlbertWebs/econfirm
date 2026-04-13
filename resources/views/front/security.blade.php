@extends('front.master')

@section('seo_title', 'Security of funds & escrow protection | eConfirm')
@section('seo_description', 'How eConfirm safeguards M-Pesa escrow: peer-to-peer holding, controlled release, encryption, audit trails, and dispute safeguards for buyers and sellers in Kenya.')

@section('content')
<div class="min-w-0">
<section class="relative py-16 lg:py-20 bg-gradient-to-b from-white to-green-50/40 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-40" aria-hidden="true">
        <div class="absolute -top-10 -right-10 sm:top-20 sm:right-10 w-56 h-56 sm:w-72 sm:h-72 bg-green-200 rounded-full blur-3xl max-w-[100vw]"></div>
        <div class="absolute -bottom-16 -left-10 sm:bottom-10 sm:left-10 w-64 h-64 sm:w-80 sm:h-80 bg-emerald-100 rounded-full blur-3xl max-w-[100vw]"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-2xl mb-6">
            <svg class="w-9 h-9 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <p class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-3">Trust &amp; safety</p>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">Security of funds</h1>
        <p class="text-lg text-gray-600 leading-relaxed max-w-2xl mx-auto mb-10">
            How we protect your money in escrow—from M-Pesa payment to release—with clear rules, technology, and transparent processes.
        </p>
        <nav class="flex flex-wrap justify-center gap-2 sm:gap-3" aria-label="On this page">
            <a href="#overview" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Overview</a>
            <a href="#escrow-model" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Escrow model</a>
            <a href="#process-flow" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Process flow</a>
            <a href="#security-measures" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Security measures</a>
        </nav>
    </div>
</section>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-12 lg:pb-16 space-y-16 lg:space-y-20">
    <section id="overview" class="scroll-mt-28">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Our priority</h2>
        <p class="text-gray-600 leading-relaxed mb-4">
            At eConfirm, the security of user funds is our top priority. Our platform uses a peer-to-peer escrow system designed to reduce fraud, limit transaction risk, and help both parties meet their obligations. The process is structured and built on clear rules, backed by technology you can follow step by step.
        </p>
        <div class="rounded-2xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-950">
            <strong class="font-semibold">Important:</strong> The escrow wallet is not a bank account. It is a temporary holding arrangement with one purpose—to safeguard funds until the transaction is successfully completed.
        </div>
    </section>

    <section id="escrow-model" class="scroll-mt-28">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4">Peer-to-peer escrow model</h2>
        <p class="text-gray-600 leading-relaxed mb-4">
            eConfirm acts as a trusted intermediary between the Sender (payer) and the Receiver (payee). Once the Sender initiates a transaction and pays through <strong class="text-gray-800">M-Pesa</strong>, funds are held in escrow by the platform. They are not available to either party until the agreed conditions are fulfilled.
        </p>
    </section>

    <section id="process-flow" class="scroll-mt-28">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Mapped to our process flow</h2>
        <p class="text-gray-600 mb-8">From payment to release—what happens at each stage.</p>
        <ol class="space-y-4">
            <li class="relative pl-0 sm:pl-4">
                <div class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:border-green-200/80 transition-colors">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-600 text-sm font-bold text-white">1</span>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">Payment holding</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            When the Sender deposits funds via M-Pesa, the money is locked in escrow. Funds cannot be moved, withdrawn, or altered by either party outside the agreed flow.
                        </p>
                    </div>
                </div>
            </li>
            <li class="relative pl-0 sm:pl-4">
                <div class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:border-green-200/80 transition-colors">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-600 text-sm font-bold text-white">2</span>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">Digital transaction terms</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            A record of the transaction is created with the terms both parties agreed to (for example delivery window, item or service description). The Receiver is notified and can review terms; funds remain protected.
                        </p>
                    </div>
                </div>
            </li>
            <li class="relative pl-0 sm:pl-4">
                <div class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:border-green-200/80 transition-colors">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-600 text-sm font-bold text-white">3</span>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">Delivery assurance</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            The Receiver delivers the promised goods or services. The Sender verifies delivery and quality against what was agreed.
                        </p>
                    </div>
                </div>
            </li>
            <li class="relative pl-0 sm:pl-4">
                <div class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:border-green-200/80 transition-colors">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-600 text-sm font-bold text-white">4</span>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">Controlled release by Sender</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Only the Sender can release funds (for example via confirm-and-release after successful delivery). This helps ensure funds move only when the agreed outcome is met.
                        </p>
                    </div>
                </div>
            </li>
            <li class="relative pl-0 sm:pl-4">
                <div class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:border-green-200/80 transition-colors">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-600 text-sm font-bold text-white">5</span>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">Dispute and mediation</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            If delivery fails or is unacceptable, funds stay protected while the issue is reviewed. Mediators may assist; where needed, cases can be escalated in line with platform rules and applicable law. eConfirm does not release escrow funds unilaterally without following the defined process.
                        </p>
                    </div>
                </div>
            </li>
        </ol>
    </section>

    <section id="security-measures" class="scroll-mt-28">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Security measures</h2>
        <p class="text-gray-600 mb-8">Technical and operational safeguards built into the platform.</p>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="rounded-2xl border border-gray-100 bg-gradient-to-br from-green-50/80 to-white p-5">
                <div class="mb-2 text-green-700">
                    <i class="fas fa-wallet text-lg" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Escrow wallet isolation</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Funds are held separately from everyday operating balances, supporting clear accounting and reducing misuse.</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-gradient-to-br from-green-50/80 to-white p-5">
                <div class="mb-2 text-green-700">
                    <i class="fas fa-lock text-lg" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Encryption</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Payment and sensitive data are protected using industry-standard encryption in transit and at rest, consistent with how we operate the service.</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-gradient-to-br from-green-50/80 to-white p-5">
                <div class="mb-2 text-green-700">
                    <i class="fas fa-clipboard-list text-lg" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Audit trails</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Key actions—payments, status changes, disputes, releases—are logged and timestamped to support transparency and evidence if a conflict arises.</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-gradient-to-br from-green-50/80 to-white p-5">
                <div class="mb-2 text-green-700">
                    <i class="fas fa-user-check text-lg" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Release authorization</h3>
                <p class="text-sm text-gray-600 leading-relaxed">Release is driven by the verified Sender’s action within the product rules—not ad-hoc overrides—so expectations stay consistent.</p>
            </div>
        </div>
        <p class="text-gray-600 leading-relaxed mt-8">
            Together, automation, user-controlled release, and neutral mediation help keep transactions fair and fraud-resistant—so Senders and Receivers can transact with greater confidence.
        </p>
    </section>

    <p class="text-center text-sm text-gray-500">
        For legal terms, see our <a href="{{ route('terms.conditions') }}" class="text-green-700 font-medium hover:text-green-800 underline underline-offset-2">Terms &amp; Conditions</a>
        and <a href="{{ route('privacy.policy') }}" class="text-green-700 font-medium hover:text-green-800 underline underline-offset-2">Privacy Policy</a>.
    </p>
</div>

<section class="py-20 lg:py-28 bg-gradient-to-br from-green-600 to-emerald-600 relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden bg-black/10 pointer-events-none" aria-hidden="true"></div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-6 backdrop-blur-sm">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
            Ready to secure your transactions?
        </h2>
        <p class="text-lg sm:text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            Join thousands who use our escrow service for important M-Pesa deals.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}#home"
               class="inline-flex items-center justify-center px-8 py-4 bg-white text-green-600 font-semibold rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                Get started
            </a>
            <a href="{{ route('support') }}"
               class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white font-semibold rounded-lg hover:bg-white/10 transition-all duration-200">
                Contact support
            </a>
        </div>
        <p class="text-white/80 text-sm mt-6">No obligation. Cancel anytime.</p>
    </div>
</section>
</div>
@endsection
