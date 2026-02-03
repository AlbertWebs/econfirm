<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#18743c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="eConfirm">
    <link rel="apple-touch-icon" href="{{ asset('uploads/favicon.png') }}">
     @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('uploads/favicon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <!-- Font Awesome Free CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Trusted Escrow Services for Secure M-Pesa Payments in Kenya |  eConfirm </title>
    <meta name="description" content="eConfirm provides secure, fast, and transparent escrow services for peer-to-peer M-Pesa payments in Kenya. Safeguard your transactions for goods, services, and contracts.">
    <meta name="keywords" content="escrow services Kenya, M-Pesa escrow, secure peer to peer payments, escrow for goods and services, payment protection Kenya, eConfirm escrow platform, online escrow Kenya, buyer seller protection, safe M-Pesa payments, digital escrow solution">
    <meta name="author" content="eConfirm">
    <meta name="robots" content="index, follow">
    <meta name="language" content="en">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://econfirm.co.ke/">
    <meta property="og:title" content="Trusted Escrow Services for Secure M-Pesa Payments in Kenya |  eConfirm ">
    <meta property="og:description" content="Use eConfirm to protect your peer-to-peer transactions with reliable escrow services for M-Pesa payments. Trusted by businesses and individuals across Kenya.">
    <meta property="og:image" content="https://econfirm.co.ke/assets/images/social-share.jpg">
    <meta property="og:site_name" content="eConfirm">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://econfirm.co.ke/">
    <meta name="twitter:title" content="Trusted Escrow Services for Secure M-Pesa Payments in Kenya |  eConfirm ">
    <meta name="twitter:description" content="eConfirm offers secure escrow services to protect your M-Pesa transactions in Kenya. Trusted platform for online payments, goods, and services.">
    <meta name="twitter:image" content="https://econfirm.co.ke/assets/images/social-share.jpg">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://econfirm.co.ke/">

    <!-- Favicon -->
    <link rel="icon" href="https://econfirm.co.ke/assets/images/favicon.ico" type="image/x-icon">

    <!-- Schema.org JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "eConfirm",
    "url": "https://econfirm.co.ke",
    "logo": "https://econfirm.co.ke/uploads/logo.png",
    "description": "eConfirm is Kenya's leading escrow platform for secure peer-to-peer payments via M-Pesa. Ideal for buyers and sellers of goods, services, or contracts.",
    "sameAs": [
        "https://www.facebook.com/econfirmke",
        "https://www.linkedin.com/company/econfirmke",
        "https://www.instagram.com/econfirmke/",
        "https://x.com/econfirmke"
    ],
    "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+254XXXXXXXXX",
        "contactType": "Customer Service",
        "areaServed": "KE",
        "availableLanguage": ["English", "Swahili"]
    }
    }
    </script>


   
   
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
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }

        /* Search Transaction Popup Animation */
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
        @keyframes popupIn {
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
<body class="bg-white antialiased" x-data="{ mobileMenuOpen: false, searchPopupOpen: false }">
<div id="preloader" class="fixed inset-0 z-50 flex items-center justify-center bg-white transition-opacity duration-400">
    <div class="w-16 h-16 border-4 border-gray-200 border-t-green-700 rounded-full animate-spin"></div>
</div>
    
    <!-- Header -->
    <header class="hidden lg:block sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}#home" class="block transition-transform hover:scale-105 duration-200">
                        <img src="{{ asset('uploads/logo-hoz.png') }}" alt="e-confirm Logo" class="h-14 md:h-16">
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-2">
                    <a href="{{ route('home') }}#home" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-green-700 hover:bg-green-50 rounded-lg transition-all duration-200">Get Started</a>
                    <a href="{{ route('home') }}#features" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-green-700 hover:bg-green-50 rounded-lg transition-all duration-200">Features</a>
                    <a href="{{ route('home') }}#how-it-works" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-green-700 hover:bg-green-50 rounded-lg transition-all duration-200">How It Works</a>
                    <a href="{{ route('home') }}#integration" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-green-700 hover:bg-green-50 rounded-lg transition-all duration-200 flex items-center gap-1">
                        API <i class="fas fa-code text-xs"></i>
                    </a>
                    <a href="{{ route('scam.watch') }}" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all duration-200 flex items-center gap-1.5 border border-red-200">
                        <i class="fas fa-shield-alt text-xs"></i> Confirm
                    </a>
                    
                    <div class="h-6 w-px bg-gray-300 mx-2"></div>
                    
                    <button @click="searchPopupOpen = true" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-search text-xs"></i> Search Transaction
                    </button>

                    @if (auth()->check())
                        <button onclick="window.location.href='{{ route('user.dashboard') }}'" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 flex items-center gap-2">
                            <i class="fas fa-tachometer-alt text-xs"></i> Dashboard
                        </button>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all duration-200" title="Logout">
                            <i class="fas fa-sign-out"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @else
                        <button onclick="window.location.href='{{ route('login') }}'" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 flex items-center gap-2">
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
                <a href="{{ route('home') }}#home" @click="mobileMenuOpen = false" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition-all duration-200">Get Started</a>
                <a href="{{ route('home') }}#features" @click="mobileMenuOpen = false" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition-all duration-200">Features</a>
                <a href="{{ route('home') }}#how-it-works" @click="mobileMenuOpen = false" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition-all duration-200">How It Works</a>
                <a href="{{ route('home') }}#integration" @click="mobileMenuOpen = false" class="block px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-code text-xs"></i> API
                </a>
                <a href="{{ route('scam.watch') }}" @click="mobileMenuOpen = false" class="block px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg transition-all duration-200 flex items-center gap-2 border border-red-200">
                    <i class="fas fa-shield-alt text-xs"></i> Confirm
                </a>
                
                <div class="h-px bg-gray-200 my-2"></div>
                
                <button @click="searchPopupOpen = true; mobileMenuOpen = false" class="w-full text-left px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-search text-xs"></i> Search Transaction
                </button>
                @if (auth()->check())
                    <button onclick="window.location.href='{{ route('user.dashboard') }}'" class="w-full text-left px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-tachometer-alt text-xs"></i> Dashboard
                    </button>
                @else
                    <button onclick="window.location.href='{{ route('login') }}'" class="w-full text-left px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-sign-in text-xs"></i> Log In
                    </button>
                @endif
            </nav>
        </div>
    </header>


    @yield('content')
  
    <!-- PWA Install Prompt -->
    <div x-data="{ 
        showInstallPrompt: false,
        deferredPrompt: null,
        isInstalled: false,
        init() {
            // Check if already installed
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                                 window.navigator.standalone;
            
            if (isStandalone) {
                this.isInstalled = true;
                console.log('App already installed');
                return;
            }

            // Check if dismissed in this session
            const dismissed = sessionStorage.getItem('pwa-prompt-dismissed');
            if (dismissed === 'true') {
                console.log('Prompt was dismissed in this session');
                return;
            }

            const showPrompt = () => {
                console.log('Attempting to show prompt. isInstalled:', this.isInstalled, 'dismissed:', sessionStorage.getItem('pwa-prompt-dismissed'));
                if (!this.isInstalled && sessionStorage.getItem('pwa-prompt-dismissed') !== 'true') {
                    console.log('Setting showInstallPrompt to true');
                    this.showInstallPrompt = true;
                    console.log('showInstallPrompt is now:', this.showInstallPrompt);
                }
            };

            // Listen for beforeinstallprompt event
            window.addEventListener('beforeinstallprompt', (e) => {
                console.log('beforeinstallprompt event fired');
                e.preventDefault();
                this.deferredPrompt = e;
                // Show prompt after 2 seconds
                setTimeout(showPrompt, 2000);
            });

            // Listen for app installed event
            window.addEventListener('appinstalled', () => {
                console.log('App installed');
                this.isInstalled = true;
                this.showInstallPrompt = false;
                this.deferredPrompt = null;
            });

            // Fallback: Show prompt after 3 seconds regardless
            setTimeout(() => {
                console.log('Fallback timer fired. Checking conditions...');
                console.log('isInstalled:', this.isInstalled);
                console.log('dismissed:', sessionStorage.getItem('pwa-prompt-dismissed'));
                console.log('showInstallPrompt before:', this.showInstallPrompt);
                
                if (!this.isInstalled && sessionStorage.getItem('pwa-prompt-dismissed') !== 'true') {
                    console.log('Conditions met, showing prompt');
                    this.showInstallPrompt = true;
                    console.log('showInstallPrompt after:', this.showInstallPrompt);
                } else {
                    console.log('Conditions not met, not showing prompt');
                }
            }, 3000);
        },
        async installApp() {
            if (this.deferredPrompt) {
                this.deferredPrompt.prompt();
                const { outcome } = await this.deferredPrompt.userChoice;
                
                if (outcome === 'accepted') {
                    this.isInstalled = true;
                }
                
                this.deferredPrompt = null;
            } else {
                // Fallback: Show browser's native install prompt or instructions
                alert('To install this app:\n\nChrome/Edge: Click the install icon in the address bar\nFirefox: Click the menu button and select "Install"\nSafari: Tap Share button and select "Add to Home Screen"');
            }
            
            this.showInstallPrompt = false;
        },
        dismissPrompt() {
            this.showInstallPrompt = false;
            // Don't show again for this session
            sessionStorage.setItem('pwa-prompt-dismissed', 'true');
        }
    }"
    x-show="showInstallPrompt"
    :style="showInstallPrompt ? 'display: block !important;' : 'display: none !important;'"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-4"
    class="fixed bottom-20 lg:bottom-4 left-4 right-4 lg:left-auto lg:right-4 lg:w-96 z-40 bg-white rounded-xl shadow-2xl border-2 border-green-200 p-6 relative">
        <button @click="dismissPrompt()" 
                class="absolute -top-2 -right-2 w-8 h-8 flex items-center justify-center bg-red-500 hover:bg-red-600 rounded-full transition-colors text-white shadow-lg z-50">
            <i class="fas fa-times text-sm"></i>
        </button>
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <img src="{{ asset('uploads/favicon.png') }}" alt="eConfirm" class="w-16 h-16 rounded-lg">
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 mb-1">Install eConfirm App</h3>
                <p class="text-sm text-gray-600 mb-4">Get a better experience with our app. Install for quick access and offline support.</p>
                <div class="flex gap-2">
                    <button @click="installApp()" 
                            class="flex-1 px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-download text-sm"></i>
                        Install Now
                    </button>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Search Transaction Popup -->
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
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Search Transaction</h3>
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
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('Service Worker registered successfully:', registration.scope);
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