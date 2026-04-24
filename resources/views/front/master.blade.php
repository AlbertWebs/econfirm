<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#18743c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ site_setting('site_name') }}">
    <link rel="apple-touch-icon" href="{{ asset('uploads/favicon.png') }}">
    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('uploads/favicon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <!-- Font Awesome Free CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteUrl = rtrim(config('app.url', url('/')), '/');
        $defaultOgImage = $siteUrl . '/assets/images/social-share.jpg';
        $seoTitle = trim($__env->yieldContent('seo_title')) ?: site_setting('default_seo_title');
        $seoDescription = trim($__env->yieldContent('seo_description')) ?: site_setting('default_seo_description');
        $canonicalHref = trim($__env->yieldContent('canonical_url')) ?: url()->current();
        $ogImage = trim($__env->yieldContent('og_image')) ?: $defaultOgImage;
        $ogType = trim($__env->yieldContent('og_type')) ?: 'website';
        $defaultSeoKeywords = site_setting('default_seo_keywords');
        $metaAuthor = site_setting('meta_author');
        $orgSameAs = array_values(array_filter([
            site_setting('social_facebook_url'),
            site_setting('social_linkedin_url'),
            site_setting('social_instagram_url'),
            site_setting('social_x_url'),
        ]));
        $orgJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => site_setting('site_name'),
            'url' => $siteUrl,
            'logo' => $siteUrl . '/uploads/logo.png',
            'description' => site_setting('jsonld_organization_description'),
        ];
        if ($orgSameAs !== []) {
            $orgJsonLd['sameAs'] = $orgSameAs;
        }
        if (filled(site_setting('contact_phone_e164'))) {
            $orgJsonLd['contactPoint'] = [
                '@type' => 'ContactPoint',
                'telephone' => site_setting('contact_phone_e164'),
                'contactType' => 'Customer Service',
                'areaServed' => 'KE',
                'availableLanguage' => ['English', 'Swahili'],
            ];
        }
    @endphp
    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="@yield('seo_keywords', e($defaultSeoKeywords))">
    <meta name="author" content="{{ $metaAuthor }}">
    <meta name="robots" content="@yield('seo_robots', 'index, follow')">
    <meta name="language" content="en">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ $canonicalHref }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:site_name" content="{{ site_setting('site_name') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $canonicalHref }}">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $canonicalHref }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ $siteUrl }}/assets/images/favicon.ico" type="image/x-icon">

    <!-- Schema.org JSON-LD Structured Data -->
    <script type="application/ld+json">@json($orgJsonLd)</script>
    @stack('structured_data')

    @stack('head_extra')


   
   
    <style>
        /* Smooth scrolling for anchor links */
        html {
            scroll-behavior: smooth;
        }
        
        /* Preloader styles */
        #preloader {
          position: fixed;
          left: 0; top: 0; right: 0; bottom: 0;
          width: 100vw; height: 100vh;
          background: var(--bs-body-bg, #fff);
          z-index: 9999;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: opacity 0.4s cubic-bezier(0.4,0,0.2,1);
        }
        #preloader .loader {
          border: 6px solid #e0e0e0;
          border-top: 6px solid #18743c;
          border-radius: 50%;
          width: 60px;
          height: 60px;
          animation: spin 1s linear infinite;
        }
        @@keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }

        /* Search Escrow popup animation */
        #searchTransactionPopup {
          opacity: 0;
          pointer-events: none;
          transition: opacity 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        #searchTransactionPopup.active {
          opacity: 1;
          pointer-events: auto;
          display: flex !important;
        }
        #searchTransactionPopup .popup-content-anim {
          animation: popupIn 0.4s cubic-bezier(0.4,0,0.2,1);
        }
        @@keyframes popupIn {
          0% { transform: scale(0.95) translateY(30px); opacity: 0; }
          100% { transform: scale(1) translateY(0); opacity: 1; }
        }
        /* Loader spinner for search button */
        .btn-loading {
          position: relative;
          pointer-events: none;
          opacity: 0.7;
        }
        .btn-loading .spinner {
          width: 18px;
          height: 18px;
          border: 2px solid #fff;
          border-top: 2px solid #18743c;
          border-radius: 50%;
          display: inline-block;
          vertical-align: middle;
          margin-left: 8px;
          animation: spin 0.7s linear infinite;
        }
    </style>
</head>
@php
    $navProductConfig = config('nav_products', []);
    $navProducts = $navProductConfig['items'] ?? [];
    $navProductDropdown = $navProductConfig['dropdown'] ?? ['image' => 'uploads/logo-hoz.png', 'image_alt' => 'e-confirm', 'image_title' => ''];
    $navProductLinkColumns = array_chunk($navProducts, max(1, (int) ceil(max(count($navProducts), 1) / 2)));
    $navOnHome = request()->routeIs('home', 'home.v2');
    $navOnFeatures = request()->routeIs('features');
    $navOnEscrowProduct = request()->routeIs('escrow.product');
    $navOnScamAlert = request()->is('scam-watch*');
    $navOnUserDashboard = request()->routeIs('user.dashboard', 'user.dashboard.create', 'home.dashboard');
    $navOnLogin = request()->routeIs('login', 'register');
    $navConfirmBase = 'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 flex items-center gap-1.5 border';
    $navConfirmActive = 'text-red-800 bg-red-50 font-semibold border-red-300 ring-1 ring-red-200/60';
    $navConfirmIdle = 'text-red-600 hover:text-red-700 hover:bg-red-50 border-red-200';
    $navBtnBase = 'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 flex items-center gap-2 border border-gray-300';
    $navBtnActive = 'text-green-800 bg-green-50 font-semibold border-green-300 ring-1 ring-green-200/50';
    $navBtnIdle = 'text-gray-700 hover:bg-gray-50 hover:border-green-300';
@endphp
<body
    class="bg-white antialiased overflow-x-hidden"
    x-data="{
        mobileMenuOpen: false,
        searchPopupOpen: false,
        productsDropdownOpen: false,
        mobileProductsOpen: false,
        isHome: @json($navOnHome),
        isFeaturesPage: @json($navOnFeatures),
        activeHash: '',
        syncHash() { this.activeHash = (typeof window !== 'undefined' && window.location) ? window.location.hash : ''; }
    }"
    x-init="syncHash()"
    @hashchange.window="syncHash()"
    @popstate.window="syncHash()"
    @keydown.escape.window="productsDropdownOpen = false">
<div id="preloader" class="fixed inset-0 z-50 flex items-center justify-center bg-white transition-opacity duration-400">
    <div class="w-16 h-16 border-4 border-gray-200 border-t-green-700 rounded-full animate-spin"></div>
</div>
    
    <!-- Header -->
    <header class="hidden lg:block sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-md">
        <div class="w-full min-w-0 max-w-[min(100%,90rem)] mx-auto px-2.5 sm:px-4 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}#home" class="block select-none">
                        <img src="{{ asset('uploads/logo-hoz.png') }}"
                             alt="e-confirm Logo"
                             class="h-14 w-auto object-contain object-left align-middle md:h-16"
                             loading="eager"
                             decoding="async">
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-2">
                    <a href="{{ route('home') }}#home"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                       :class="(isHome && (!activeHash || activeHash === '' || activeHash === '#home')) ? 'text-green-800 bg-green-100 font-semibold ring-1 ring-green-200/80 shadow-sm' : 'text-gray-700 hover:text-green-700 hover:bg-green-50'">Get Started</a>
                    <a href="{{ route('home') }}#features"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                       :class="(isFeaturesPage || (isHome && activeHash === '#features')) ? 'text-green-800 bg-green-100 font-semibold ring-1 ring-green-200/80 shadow-sm' : 'text-gray-700 hover:text-green-700 hover:bg-green-50'">Features</a>
                    <div class="relative" @click.outside="productsDropdownOpen = false">
                        <button type="button"
                                @click="productsDropdownOpen = !productsDropdownOpen"
                                :aria-expanded="productsDropdownOpen"
                                aria-haspopup="true"
                                aria-controls="nav-products-list"
                                @class([
                                    'px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 flex items-center gap-1.5',
                                    $navOnEscrowProduct
                                        ? 'text-green-800 bg-green-100 font-semibold ring-1 ring-green-200/80 shadow-sm'
                                        : 'text-gray-700 hover:text-green-700 hover:bg-green-50',
                                ])>
                            Products
                            <i class="fas fa-chevron-down text-[0.65rem] transition-transform duration-200" :class="productsDropdownOpen && 'rotate-180'"></i>
                        </button>
                        <div id="nav-products-list"
                             x-show="productsDropdownOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 -translate-y-0.5"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-cloak
                             class="absolute left-0 top-full z-50 mt-1 w-[min(100vw-2rem,40rem)] max-h-[min(85vh,28rem)] overflow-hidden rounded-xl border border-gray-200/90 bg-white shadow-lg shadow-gray-200/50">
                            <div class="grid grid-cols-3 min-h-0 min-w-0">
                                @foreach ($navProductLinkColumns as $colIndex => $columnItems)
                                    <ul class="list-none m-0 max-h-[min(85vh,28rem)] overflow-y-auto overscroll-contain py-2 border-gray-100 {{ $colIndex > 0 ? 'border-l' : '' }}" role="list">
                                        @foreach ($columnItems as $item)
                                            <li>
                                                <a href="{{ route('escrow.product', $item['slug']) }}"
                                                   @click="productsDropdownOpen = false"
                                                   @class([
                                                       'block px-3 py-2.5 text-sm transition-colors min-w-0',
                                                       $navOnEscrowProduct && request()->route('product') === $item['slug']
                                                           ? 'font-semibold text-green-900 bg-green-50/90'
                                                           : 'text-gray-700 hover:bg-green-50 hover:text-green-800',
                                                   ])>
                                                    {{ $item['label'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                                <div class="flex flex-col justify-center border-l border-gray-100 bg-gradient-to-br from-emerald-50/90 via-white to-green-50/50 p-3 sm:p-4 min-w-0 min-h-[10rem]">
                                    <div class="flex min-h-0 flex-1 items-center justify-center">
                                        <img src="{{ asset($navProductDropdown['image'] ?? 'uploads/logo-hoz.png') }}"
                                             width="280"
                                             height="180"
                                             alt="{{ $navProductDropdown['image_alt'] ?? 'e-confirm' }}"
                                             class="max-h-32 w-full object-contain object-center drop-shadow-sm">
                                    </div>
                                    @if (!empty($navProductDropdown['image_title']))
                                        <p class="mt-2 text-center text-xs font-medium text-gray-600 leading-snug">
                                            {{ $navProductDropdown['image_title'] }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('home') }}#how-it-works"
                       class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                       :class="(isHome && activeHash === '#how-it-works') ? 'text-green-800 bg-green-100 font-semibold ring-1 ring-green-200/80 shadow-sm' : 'text-gray-700 hover:text-green-700 hover:bg-green-50'">How It Works</a>
                    <a href="{{ route('scam.watch') }}"
                       @class([$navConfirmBase, $navOnScamAlert ? $navConfirmActive : $navConfirmIdle])>
                        <i class="fas fa-shield-alt text-xs"></i> Confirm
                    </a>
                    
                    <div class="h-6 w-px bg-gray-300 mx-2"></div>
                    
                    <button type="button"
                            @click="searchPopupOpen = true"
                            class="px-4 py-2 text-sm font-medium border rounded-lg transition-all duration-200 flex items-center gap-2"
                            :class="searchPopupOpen ? 'text-green-800 bg-green-50 font-semibold border-green-300 ring-1 ring-green-200/50' : 'text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-green-300'">
                        <i class="fas fa-search text-xs"></i> Search Escrow
                    </button>

                    @if (auth()->check())
                        <button type="button"
                                onclick="window.location.href='{{ route('user.dashboard') }}'"
                                @class([$navBtnBase, $navOnUserDashboard ? $navBtnActive : $navBtnIdle])>
                            <i class="fas fa-tachometer-alt text-xs"></i> Dashboard
                        </button>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all duration-200" title="Logout">
                            <i class="fas fa-sign-out"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <button type="button"
                                onclick="window.location.href='{{ route('login') }}'"
                                @class([$navBtnBase, $navOnLogin ? $navBtnActive : $navBtnIdle])>
                            <i class="fas fa-sign-in text-xs"></i> Log In
                        </button>
                    @endif
                </nav>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Navigation -->
            <nav x-show="mobileMenuOpen" x-transition class="lg:hidden pb-4 space-y-2 border-t border-gray-200 mt-2 pt-4" style="display: none;">
                <a href="{{ route('home') }}#home" @click="mobileMenuOpen = false"
                   class="block px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
                   :class="(isHome && (!activeHash || activeHash === '' || activeHash === '#home')) ? 'text-green-800 bg-green-100 font-semibold ring-1 ring-green-200/80' : 'text-gray-700 hover:bg-green-50 hover:text-green-700'">Get Started</a>
                <a href="{{ route('home') }}#features" @click="mobileMenuOpen = false"
                   class="block px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
                   :class="(isFeaturesPage || (isHome && activeHash === '#features')) ? 'text-green-800 bg-green-100 font-semibold ring-1 ring-green-200/80' : 'text-gray-700 hover:bg-green-50 hover:text-green-700'">Features</a>
                <div>
                    <button type="button"
                            @click="mobileProductsOpen = !mobileProductsOpen"
                            :aria-expanded="mobileProductsOpen"
                            @class([
                                'w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200',
                                $navOnEscrowProduct
                                    ? 'text-green-800 bg-green-100 font-semibold ring-1 ring-green-200/80'
                                    : 'text-gray-700 hover:bg-green-50 hover:text-green-700',
                            ])>
                        <span>Products</span>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="mobileProductsOpen && 'rotate-180'"></i>
                    </button>
                    <div x-show="mobileProductsOpen"
                         x-transition
                         x-cloak
                         class="pl-2 pt-1 space-y-0.5 border-l-2 border-green-100 ml-4 my-1">
                        @foreach ($navProducts as $item)
                            <a href="{{ route('escrow.product', $item['slug']) }}"
                               @click="mobileMenuOpen = false; mobileProductsOpen = false"
                               @class([
                                   'block px-3 py-2 text-sm rounded-lg transition-colors',
                                   $navOnEscrowProduct && request()->route('product') === $item['slug']
                                       ? 'font-semibold text-green-900 bg-green-50/90'
                                       : 'text-gray-600 hover:text-green-800 hover:bg-green-50/80',
                               ])>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('home') }}#how-it-works" @click="mobileMenuOpen = false"
                   class="block px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200"
                   :class="(isHome && activeHash === '#how-it-works') ? 'text-green-800 bg-green-100 font-semibold ring-1 ring-green-200/80' : 'text-gray-700 hover:bg-green-50 hover:text-green-700'">How It Works</a>
                <a href="{{ route('scam.watch') }}" @click="mobileMenuOpen = false"
                   @class([
                       'block px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 flex items-center gap-2',
                       $navOnScamAlert ? 'text-red-800 bg-red-50 font-semibold border border-red-300 ring-1 ring-red-200/60' : 'text-red-600 hover:bg-red-50 hover:text-red-700 border border-red-200',
                   ])>
                    <i class="fas fa-shield-alt text-xs"></i> Confirm
                </a>
                
                <div class="h-px bg-gray-200 my-2"></div>
                
                <button type="button" @click="searchPopupOpen = true; mobileMenuOpen = false"
                        class="w-full text-left px-4 py-2.5 text-sm font-medium border rounded-lg transition-all duration-200 flex items-center gap-2"
                        :class="searchPopupOpen ? 'text-green-800 bg-green-50 font-semibold border-green-300 ring-1 ring-green-200/50' : 'text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-green-300'">
                    <i class="fas fa-search text-xs"></i> Search Escrow
                </button>
                @if (auth()->check())
                    <button type="button" onclick="window.location.href='{{ route('user.dashboard') }}'"
                            @class([
                                'w-full text-left px-4 py-2.5 text-sm font-medium border rounded-lg transition-all duration-200 flex items-center gap-2',
                                $navOnUserDashboard
                                    ? 'text-green-800 bg-green-50 font-semibold border-green-300 ring-1 ring-green-200/50'
                                    : 'text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-green-300',
                            ])>
                        <i class="fas fa-tachometer-alt text-xs"></i> Dashboard
                    </button>
                @else
                    <button type="button" onclick="window.location.href='{{ route('login') }}'"
                            @class([
                                'w-full text-left px-4 py-2.5 text-sm font-medium border rounded-lg transition-all duration-200 flex items-center gap-2',
                                $navOnLogin
                                    ? 'text-green-800 bg-green-50 font-semibold border-green-300 ring-1 ring-green-200/50'
                                    : 'text-gray-700 border-gray-300 hover:bg-gray-50 hover:border-green-300',
                            ])>
                        <i class="fas fa-sign-in text-xs"></i> Log In
                    </button>
                @endif
            </nav>
        </div>
    </header>


    @yield('content')

    <!-- Mobile Bottom Navigation -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-lg">
        <div class="grid grid-cols-5 h-16">
            <a href="{{ route('home') }}" class="flex flex-col items-center justify-center text-gray-600 hover:text-blue-600 transition-colors active:bg-gray-50">
                <i class="fas fa-home text-lg mb-1"></i>
                <span class="text-xs font-medium">Home</span>
            </a>
            <a href="{{ route('home') }}#home" class="flex flex-col items-center justify-center text-gray-600 hover:text-green-600 transition-colors active:bg-gray-50">
                <i class="fas fa-shield-alt text-lg mb-1"></i>
                <span class="text-xs font-medium">Escrow</span>
            </a>
            <a href="{{ route('scam.watch') }}" class="flex flex-col items-center justify-center text-gray-600 hover:text-red-600 transition-colors active:bg-gray-50">
                <i class="fas fa-check-circle text-lg mb-1"></i>
                <span class="text-xs font-medium">Confirm</span>
            </a>
            <a href="{{ route('scam.watch') }}#scam-list" class="flex flex-col items-center justify-center text-gray-600 hover:text-orange-600 transition-colors active:bg-gray-50">
                <i class="fas fa-flag text-lg mb-1"></i>
                <span class="text-xs font-medium">Report</span>
            </a>
            @if (auth()->check())
                <a href="{{ route('user.dashboard') }}" class="flex flex-col items-center justify-center text-gray-600 hover:text-green-600 transition-colors active:bg-gray-50">
                    <i class="fas fa-user-circle text-lg mb-1"></i>
                    <span class="text-xs font-medium">Account</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center justify-center text-gray-600 hover:text-green-600 transition-colors active:bg-gray-50">
                    <i class="fas fa-sign-in-alt text-lg mb-1"></i>
                    <span class="text-xs font-medium">Login</span>
                </a>
            @endif
        </div>
    </nav>

    <!-- Footer -->
    @include('front.footer')

   <script>
        // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

        // Preloader
            window.addEventListener('load', function () {
                const preloader = document.getElementById('preloader');
                if (preloader) {
                preloader.style.opacity = '0';
                    setTimeout(() => {
                        preloader.style.display = 'none';
                    }, 400);
                }
        });
    </script>

    <!-- Search Escrow popup -->
    <div x-show="searchPopupOpen" 
         @click.away="searchPopupOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
         style="display: none;"
         x-data="{ 
            loading: false,
            result: null,
            error: null,
            async search() {
                this.loading = true;
                this.result = null;
                this.error = null;
                const id = document.getElementById('basic-search-transaction-id').value.trim();
                if (!id) { this.loading = false; return; }
                
                try {
                    const res = await fetch(`/transaction/search?id=${encodeURIComponent(id)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    const transaction = data.transaction || (Array.isArray(data.data) && data.data[0] ? data.data[0] : null);
                    
                    if (data.success && transaction) {
                        this.result = transaction;
                } else {
                        this.error = data.message || 'Transaction not found.';
                    }
                } catch (e) {
                    this.error = 'Error searching transaction.';
                } finally {
                    this.loading = false;
                }
            }
         }">
        <div @click.stop 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6 relative">
            <button @click="searchPopupOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Search Escrow</h3>
            <form @submit.prevent="search()">
                <div class="mb-4">
                    <label for="basic-search-transaction-id" class="block text-sm font-medium text-gray-700 mb-2">Transaction ID</label>
                    <input type="text" 
                           id="basic-search-transaction-id" 
                           name="transaction_id" 
                           placeholder="Enter Transaction ID" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                </div>
                <button type="submit" 
                        :disabled="loading"
                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium flex items-center justify-center gap-2">
                    <span x-show="!loading">Search</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Searching...
                    </span>
                </button>
            </form>
            <div x-show="result || error" class="mt-4 p-3 rounded-lg" 
                 :class="result ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'"
                 style="display: none;">
                <template x-if="result">
                    <div class="space-y-3">
                        <div>
                            <p class="font-semibold mb-1">Transaction Found:</p>
                            <p class="text-sm">ID: <span x-text="result.transaction_id || result.id"></span></p>
                            <p class="text-sm">Status: <span x-text="result.status"></span></p>
                        </div>
                        <a :href="`/get-transaction/${result.transaction_id || result.id}`" 
                           class="block w-full text-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            View Transaction <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                        <div class="pt-3 border-t border-green-200">
                            <p class="text-xs text-gray-600 mb-2">Help us improve by leaving a review:</p>
                            <a href="https://g.page/r/CXoxpsT3ArcfEAE/review" 
                               target="_blank"
                               class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fab fa-google"></i>
                                Leave a Google Review
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        </div>
                    </div>
                </template>
                <template x-if="error">
                    <p class="text-sm" x-text="error"></p>
                </template>
            </div>
        </div>
    </div>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', { updateViaCache: 'none' })
                    .then((registration) => {
                        console.log('Service Worker registered successfully:', registration.scope);
                        registration.update();
                    })
                    .catch((error) => {
                        console.log('Service Worker registration failed:', error);
                    });
            });
        }
    </script>

    <!-- Smooth Scroll for Anchor Links -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced smooth scroll for anchor links with offset for fixed header
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href && href.startsWith('#')) {
                        const targetId = href.substring(1);
                        const targetElement = document.getElementById(targetId);
                        if (targetElement) {
                            e.preventDefault();
                            const headerOffset = 80;
                            const elementPosition = targetElement.getBoundingClientRect().top;
                            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                            
                            window.scrollTo({
                                top: offsetPosition,
                                behavior: 'smooth'
                            });
                        }
                    }
                });
            });
        });
    </script>

</body>
</html>