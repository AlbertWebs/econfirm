<footer class="relative bg-gray-900 text-gray-300 pb-16 lg:pb-0 border-t border-white/5" role="contentinfo">
    {{-- Subtle top accent — brand green without changing layout --}}
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-green-600/40 to-transparent pointer-events-none" aria-hidden="true"></div>

    <div class="max-w-[min(100%,84rem)] mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10 lg:gap-12 mb-10">
            <!-- Brand Column -->
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="mb-5">
                    <a href="{{ route('home') }}" class="inline-block rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900 transition-opacity hover:opacity-90">
                        <img src="{{ asset('uploads/logo-hoz.png') }}" alt="{{ site_setting('logo_alt') }}" class="h-12 w-auto" width="180" height="48" loading="lazy" decoding="async">
                    </a>
                </div>
                <div class="text-sm text-gray-400 leading-relaxed max-w-md space-y-3">
                    @if(filled(site_setting('footer_about_blurb')))
                        <div class="prose prose-invert prose-sm max-w-none">{!! site_setting('footer_about_blurb') !!}</div>
                    @endif
                    @php
                        $pubEmail = site_setting('contact_email');
                        $pubPhone = site_setting('contact_phone_display');
                        $pubAddr = site_setting('physical_address');
                    @endphp
                    @if(filled($pubEmail) || filled($pubPhone) || filled($pubAddr))
                        <ul class="list-none space-y-1.5 text-gray-400">
                            @if(filled($pubEmail))
                                <li><a href="mailto:{{ $pubEmail }}" class="text-green-400/90 hover:text-green-300 underline-offset-2 hover:underline">{{ $pubEmail }}</a></li>
                            @endif
                            @if(filled($pubPhone))
                                <li>{{ $pubPhone }}</li>
                            @endif
                            @if(filled($pubAddr))
                                <li class="whitespace-pre-line text-gray-500">{{ $pubAddr }}</li>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Products Column -->
            <nav class="min-w-0 lg:col-span-2" aria-label="Products">
                <h3 class="text-white font-semibold text-base mb-1">Products</h3>
                <p class="text-xs text-gray-500 mb-4">Transaction types we support</p>
                <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 sm:gap-x-6">
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('escrow.product', ['product' => 'real-estate']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Real Estate Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'vehicle']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Vehicle Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'business']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Business Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'ecommerce']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">E-commerce Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'services']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Services Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'freelancer']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Freelancer Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'rental']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Rental Escrow</a></li>
                    </ul>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('escrow.product', ['product' => 'import-export']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Import &amp; Export Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'digital-asset']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Digital Asset Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'construction']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Construction Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'equipment-machinery']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Equipment / Machinery Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'tender-contract']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Tender / Contract Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'education-school-fees']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Education / School Fees Escrow</a></li>
                        <li><a href="{{ route('escrow.product', ['product' => 'marketplace']) }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Marketplace Escrow</a></li>
                    </ul>
                </div>
            </nav>

            <!-- Scam Alert -->
            <nav class="min-w-0" aria-label="Scam Alert">
                <h3 class="text-white font-semibold text-base mb-1">Scam Alert</h3>
                <p class="text-xs text-gray-500 mb-4">Reported threats &amp; categories</p>
                <ul class="space-y-2.5">
                    <li>
                        <a href="{{ route('scam.watch') }}" class="inline-flex text-sm font-medium text-red-400/95 hover:text-red-300 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                            All reported scams
                        </a>
                    </li>
                    @foreach(\App\Models\ScamReport::CATEGORY_LABELS as $categoryKey => $categoryLabel)
                        <li>
                            <a href="{{ route('scam.watch.category', ['category' => $categoryKey]) }}" class="inline-flex text-sm text-gray-400 hover:text-red-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                                {{ $categoryLabel }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <!-- Legal Column -->
            <nav class="min-w-0" aria-label="Legal">
                <h3 class="text-white font-semibold text-base mb-1">Legal</h3>
                <p class="text-xs text-gray-500 mb-4">Policies &amp; compliance</p>
                <ul class="space-y-2.5">
                    <li>
                        <a href="{{ route('terms.conditions') }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Terms of Service</a>
                    </li>
                    <li>
                        <a href="{{ route('privacy.policy') }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Privacy Policy</a>
                    </li>
                    <li>
                        <a href="{{ route('security') }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Security &amp; Assurance</a>
                    </li>
                    <li>
                        <a href="{{ route('complience') }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Compliance</a>
                    </li>
                    <li>
                        <a href="{{ route('scam.watch.terms') }}" class="inline-flex text-sm text-gray-400 hover:text-green-400 transition-colors rounded-md py-0.5 -mx-1 px-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">Scam Alert Terms of Use</a>
                    </li>
                </ul>
            </nav>
        </div>

        @php
            $footerScamReportLinks = \Illuminate\Support\Facades\Cache::remember(
                'footer_scam_report_links_v1',
                now()->addMinutes(10),
                fn () => \App\Models\ScamReport::query()
                    ->publicListed()
                    ->orderByDesc('report_count')
                    ->orderByDesc('created_at')
                    ->limit(24)
                    ->get()
            );
        @endphp
        @if($footerScamReportLinks->isNotEmpty())
        <div class="border-t border-gray-800/80 pt-8 mb-8">
            <h3 class="text-white font-semibold text-base mb-1 text-center sm:text-left">Latest scam reports</h3>
            <p class="text-xs text-gray-500 mb-4 text-center sm:text-left max-w-3xl">Short links to warning pages (cache refreshes every few minutes).</p>
            <ul class="flex flex-wrap justify-center sm:justify-start gap-2 text-sm">
                @foreach($footerScamReportLinks as $report)
                    <li>
                        <a href="{{ route('scam.watch.show', ['report' => $report, 'slug' => $report->seoSlug()]) }}" class="inline-block max-w-[14rem] sm:max-w-none rounded-lg px-2.5 py-1.5 bg-gray-800/50 text-gray-400 hover:text-red-300 hover:bg-gray-800 border border-transparent hover:border-gray-700 transition-colors break-all focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                            {{ \Illuminate\Support\Str::limit((string) $report->reported_value, 42, '…') }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <p class="mt-5 text-center sm:text-left">
                <a href="{{ route('scam.watch') }}" class="inline-flex items-center gap-1 text-sm font-medium text-red-400 hover:text-red-300 transition-colors rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                    Browse all reports
                    <span aria-hidden="true">→</span>
                </a>
            </p>
        </div>
        @endif

        @php
            $fb = site_setting('social_facebook_url');
            $sx = site_setting('social_x_url');
            $ig = site_setting('social_instagram_url');
            $li = site_setting('social_linkedin_url');
            $brand = site_setting('site_name');
        @endphp

        <!-- Support quick links -->
        <div class="border-t border-gray-800/80 pt-8 mb-6">
            <p class="sr-only">Help and resources</p>
            <div class="flex flex-col gap-4 sm:gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap justify-center sm:justify-start items-stretch gap-2 sm:gap-3">
                    <a href="{{ route('support') }}" class="inline-flex items-center justify-center gap-2 text-sm font-medium text-gray-300 bg-gray-800/40 hover:bg-gray-800 border border-gray-700/60 hover:border-gray-600 px-4 py-2.5 rounded-xl transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                        <i class="fas fa-headset text-green-500/90 text-xs" aria-hidden="true"></i>
                        <span>Support</span>
                    </a>
                    <a href="{{ route('help') }}" class="inline-flex items-center justify-center gap-2 text-sm font-medium text-gray-300 bg-gray-800/40 hover:bg-gray-800 border border-gray-700/60 hover:border-gray-600 px-4 py-2.5 rounded-xl transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                        <i class="fas fa-book text-green-500/90 text-xs" aria-hidden="true"></i>
                        <span>Help</span>
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center gap-2 text-sm font-medium text-gray-300 bg-gray-800/40 hover:bg-gray-800 border border-gray-700/60 hover:border-gray-600 px-4 py-2.5 rounded-xl transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                        <i class="fas fa-envelope text-green-500/90 text-xs" aria-hidden="true"></i>
                        <span>Contact</span>
                    </a>
                    <a href="{{ route('api-documentation') }}" class="inline-flex items-center justify-center gap-2 text-sm font-medium text-gray-300 bg-gray-800/40 hover:bg-gray-800 border border-gray-700/60 hover:border-gray-600 px-4 py-2.5 rounded-xl transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                        <i class="fas fa-code text-green-500/90 text-xs" aria-hidden="true"></i>
                        <span>API docs</span>
                    </a>
                    <a href="{{ route('scam.watch') }}" class="inline-flex items-center justify-center gap-2 text-sm font-medium text-gray-300 bg-gray-800/40 hover:bg-gray-800 border border-gray-700/60 hover:border-gray-600 px-4 py-2.5 rounded-xl transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                        <i class="fas fa-shield-alt text-red-400/90 text-xs" aria-hidden="true"></i>
                        <span>Scam Alert</span>
                    </a>
                </div>

                @if(filled($fb) || filled($sx) || filled($ig) || filled($li))
                <div class="flex items-center justify-center lg:justify-end gap-3">
                    <span class="text-xs text-gray-500 mr-1">Follow us</span>
                    @if(filled($fb))
                    <a href="{{ $fb }}"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="{{ $brand }} on Facebook"
                       class="w-11 h-11 flex items-center justify-center bg-gray-800/80 rounded-xl hover:bg-green-600 hover:scale-105 transition-all duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                        </svg>
                    </a>
                    @endif
                    @if(filled($sx))
                    <a href="{{ $sx }}"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="{{ $brand }} on X"
                       class="w-11 h-11 flex items-center justify-center bg-gray-800/80 rounded-xl hover:bg-green-600 hover:scale-105 transition-all duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>
                        </svg>
                    </a>
                    @endif
                    @if(filled($ig))
                    <a href="{{ $ig }}"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="{{ $brand }} on Instagram"
                       class="w-11 h-11 flex items-center justify-center bg-gray-800/80 rounded-xl hover:bg-green-600 hover:scale-105 transition-all duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                        </svg>
                    </a>
                    @endif
                    @if(filled($li))
                    <a href="{{ $li }}"
                       target="_blank" rel="noopener noreferrer"
                       aria-label="{{ $brand }} on LinkedIn"
                       class="w-11 h-11 flex items-center justify-center bg-gray-800/80 rounded-xl hover:bg-green-600 hover:scale-105 transition-all duration-200 group focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-900">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                            <rect width="4" height="12" x="2" y="9"/>
                            <circle cx="4" cy="4" r="2"/>
                        </svg>
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Footer bottom -->
        <div class="border-t border-gray-800/80 pt-8">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">
                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} {{ site_setting('organization_legal_name') }}. All rights reserved.
                </p>
                @if(filled(site_setting('footer_tagline')))
                <div class="flex items-center justify-center gap-2 text-sm text-gray-400 max-w-md sm:max-w-none sm:justify-end">
                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                    </svg>
                    <span>{{ site_setting('footer_tagline') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</footer>
