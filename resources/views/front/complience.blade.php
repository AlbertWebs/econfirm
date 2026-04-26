@extends('front.master')

@section('seo_title', 'Compliance & regulatory commitment | '.site_setting('site_name'))
@section('seo_description', 'How eConfirm meets legal, payment, and data protection expectations in Kenya: laws we follow, M-PESA and AML/KYC practices, records, cooperation with authorities, and your responsibilities as a user.')
@section('canonical_url', route('complience'))

@section('content')
<div class="min-w-0">
    <section class="relative py-16 lg:py-20 bg-gradient-to-b from-white via-emerald-50/30 to-white overflow-hidden">
        <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-50" aria-hidden="true">
            <div class="absolute -top-10 -right-10 sm:top-16 sm:right-8 w-56 h-56 sm:w-72 sm:h-72 bg-emerald-200/60 rounded-full blur-3xl max-w-[100vw]"></div>
            <div class="absolute -bottom-20 -left-10 sm:bottom-8 sm:left-12 w-64 h-64 sm:w-80 sm:h-80 bg-green-100/80 rounded-full blur-3xl max-w-[100vw]"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-2xl mb-6">
                <i class="fas fa-scale-balanced text-3xl text-green-700" aria-hidden="true"></i>
            </div>
            <p class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-3">Legal &amp; operations</p>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">Compliance</h1>
            <p class="text-lg text-gray-600 leading-relaxed max-w-2xl mx-auto mb-10">
                We are committed to operating within applicable laws and industry standards so the platform stays secure, fair, and transparent for everyone who uses it.
            </p>
            <nav class="flex flex-wrap justify-center gap-2 sm:gap-3" aria-label="On this page">
                <a href="#legal" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Legal</a>
                <a href="#payments" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Payments</a>
                <a href="#privacy" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Data</a>
                <a href="#records" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Records</a>
                <a href="#authorities" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Authorities</a>
                <a href="#your-responsibility" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white border border-green-200 text-green-800 hover:bg-green-50 hover:border-green-300 transition-colors shadow-sm">Your role</a>
            </nav>
        </div>
    </section>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-6">
        <p class="text-base text-gray-600 leading-relaxed text-center sm:text-left rounded-2xl border border-gray-100 bg-gray-50/80 px-5 py-4">
            At e-confirm, we operate in line with Kenyan law and common safeguards for payments and personal data. The areas below summarise how we think about compliance—without replacing your own legal advice where you need it.
        </p>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-14 lg:pb-20 space-y-6 sm:space-y-8">
        <article id="legal" class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm hover:border-green-200/60 transition-colors">
            <div class="flex gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-600 text-white" aria-hidden="true">
                    <i class="fas fa-gavel text-lg"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Legal compliance</h2>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">
                        We operate in accordance with the laws of the Republic of Kenya, including financial service rules, consumer protection, and the <strong class="text-gray-800">Data Protection Act, 2019</strong>. Our operations are structured to meet statutory and regulatory requirements that apply to how we run the platform.
                    </p>
                </div>
            </div>
        </article>

        <article id="payments" class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm hover:border-green-200/60 transition-colors">
            <div class="flex gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-600 text-white" aria-hidden="true">
                    <i class="fas fa-mobile-screen-button text-lg"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Payment compliance</h2>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">
                        Payments on the platform are processed through authorised mobile money channels such as <strong class="text-gray-800">M-PESA</strong>. Where they apply, we follow <strong class="text-gray-800">Anti-Money Laundering (AML)</strong> and <strong class="text-gray-800">Know Your Customer (KYC)</strong> practices to reduce fraud and misuse.
                    </p>
                </div>
            </div>
        </article>

        <article id="privacy" class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm hover:border-green-200/60 transition-colors">
            <div class="flex gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-600 text-white" aria-hidden="true">
                    <i class="fas fa-shield-halved text-lg"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Data privacy compliance</h2>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base mb-4">
                        We align with national and international expectations for protecting personal data: encryption, access controls, and internal processes designed to keep information safe. Data is collected and used transparently and with consent where the law requires it.
                    </p>
                    <a href="{{ route('privacy.policy') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-green-700 hover:text-green-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 rounded-md">
                        Read our privacy policy
                        <i class="fas fa-arrow-right text-xs opacity-80" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </article>

        <article id="records" class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm hover:border-green-200/60 transition-colors">
            <div class="flex gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-600 text-white" aria-hidden="true">
                    <i class="fas fa-folder-open text-lg"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Audit and record-keeping</h2>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">
                        We keep accurate records of transactions, disputes, and related communications for auditing, regulatory, and security purposes. Records are stored securely for at least <strong class="text-gray-800">six (6) years</strong> and may be produced when the law or a lawful process requires it.
                    </p>
                </div>
            </div>
        </article>

        <article id="authorities" class="scroll-mt-28 rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 shadow-sm hover:border-green-200/60 transition-colors">
            <div class="flex gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-green-600 text-white" aria-hidden="true">
                    <i class="fas fa-landmark text-lg"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Cooperation with authorities</h2>
                    <p class="text-gray-600 leading-relaxed text-sm sm:text-base">
                        In cases of fraud, theft, or other serious wrongdoing, e-confirm will cooperate with law enforcement when the law requires it. We may share relevant user and transaction information with competent authorities under a lawful basis.
                    </p>
                </div>
            </div>
        </article>

        <article id="your-responsibility" class="scroll-mt-28 rounded-2xl border border-amber-200 bg-amber-50/40 p-6 sm:p-8 shadow-sm">
            <div class="flex gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-white" aria-hidden="true">
                    <i class="fas fa-user-check text-lg"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Your responsibility</h2>
                    <p class="text-gray-700 leading-relaxed text-sm sm:text-base">
                        You are expected to use the platform lawfully. Misuse for money laundering, fraud, cybercrime, or other illegal activity can lead to <strong class="text-gray-900">immediate suspension</strong> and may be referred for legal action.
                    </p>
                </div>
            </div>
        </article>
    </div>

    <section class="border-t border-gray-200 bg-gray-50/80 py-12" aria-labelledby="compliance-related-heading">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 id="compliance-related-heading" class="text-lg font-semibold text-gray-900 mb-4">Related on eConfirm</h2>
            <div class="flex flex-wrap justify-center gap-3 sm:gap-4">
                <a href="{{ route('terms.conditions') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-800 hover:border-green-300 hover:bg-green-50/50 transition-colors">Terms &amp; conditions</a>
                <a href="{{ route('privacy.policy') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-800 hover:border-green-300 hover:bg-green-50/50 transition-colors">Privacy policy</a>
                <a href="{{ route('security') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-800 hover:border-green-300 hover:bg-green-50/50 transition-colors">Security</a>
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-xl bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition-colors shadow-sm">Contact us</a>
            </div>
        </div>
    </section>
</div>
@endsection
