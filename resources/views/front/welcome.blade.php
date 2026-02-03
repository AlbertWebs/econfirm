@extends('front.master')

@section('content')
<script>
function transactionFormData() {
    return {
        showCustomType: false,
        showPaybill: false,
        submitting: false,
        mpesaResponse: null,
        checkoutRequestId: null,
        
        async submitForm(event) {
            this.submitting = true;
            this.mpesaResponse = null;
            
            const form = event.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch('/submit-transaction', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.mpesaResponse = { type: 'success', message: 'STK push sent. Waiting for payment confirmation...' };
                    const checkoutId = data.CheckoutRequestID || (data.data && data.data.CheckoutRequestID);
                    if (checkoutId) {
                        form.reset();
                        this.checkoutRequestId = checkoutId;
                        this.pollTransactionStatus();
                    }
                } else {
                    this.mpesaResponse = { type: 'error', message: data.message || 'Submission failed. Please try again.' };
                }
            } catch (error) {
                this.mpesaResponse = { type: 'error', message: 'Submission failed. Please try again.' };
            } finally {
                this.submitting = false;
            }
        },
        
        pollTransactionStatus() {
            let attempts = 0;
            const maxAttempts = 24;
            const pollInterval = 5000;
            const self = this;
            
            const poll = setInterval(() => {
                attempts++;
                fetch('/transaction/status/' + self.checkoutRequestId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'completed' || data.status === 'Success') {
                        clearInterval(poll);
                        self.mpesaResponse = { type: 'success', message: 'Payment received! Redirecting...' };
                        setTimeout(() => {
                            window.location.href = '/get-transaction/' + data.transaction_id;
                        }, 1500);
                    } else if (data.status === 'Failed') {
                        clearInterval(poll);
                        self.mpesaResponse = { type: 'error', message: 'Payment failed. Please try again.' };
                    } else if (attempts >= maxAttempts) {
                        clearInterval(poll);
                        self.mpesaResponse = { type: 'warning', message: 'Payment confirmation timed out. Please check your transaction status later.' };
                    }
                })
                .catch(() => {
                    if (attempts >= maxAttempts) {
                        clearInterval(poll);
                        self.mpesaResponse = { type: 'warning', message: 'Payment confirmation timed out. Please check your transaction status later.' };
                    }
                });
            }, pollInterval);
        }
    };
}
</script>

<div x-data="transactionFormData()">

       <!-- Hero Section -->
<section id="home" class="relative min-h-[85vh] flex items-center bg-gradient-to-br from-green-50 via-white to-emerald-50 overflow-hidden pt-8">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <div class="relative max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 w-full">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <!-- Hero Content -->
            <div class="text-center lg:text-left space-y-8" 
                 x-data="{ inView: true }"
                 x-intersect="inView = true">
                <div x-show="inView" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-y-8"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/80 backdrop-blur-sm border border-green-200 text-green-700 rounded-full text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-300">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span>Trusted by over 10,000 clients</span>
                    </div>
                
                <div class="space-y-4">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-extrabold text-gray-900 leading-tight tracking-tight">
                        Secure Your Transactions with 
                        <span class="block mt-2 bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent">
                            e-confirm Escrow
                        </span>
                    </h1>
                    
                    <p class="text-lg sm:text-xl lg:text-2xl text-gray-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Our escrow service ensures safe transactions between parties. We hold and regulate payment until all terms of an agreement are met.
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-2">
                    <a href="#home" class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 hover:scale-105">
                        <span>Get Started Now</span>
                        <svg class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#how-it-works" class="inline-flex items-center justify-center px-8 py-4 bg-white border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:border-green-500 hover:text-green-600 hover:bg-green-50 transition-all duration-300 shadow-sm hover:shadow-md">
                        Learn More
                    </a>
                    </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 pt-6">
                    <div class="flex flex-col sm:flex-row items-center gap-2 p-3 bg-white/60 backdrop-blur-sm rounded-xl border border-gray-100 hover:border-green-200 hover:bg-white/80 transition-all duration-300 group">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-600 transition-colors">
                            <svg class="w-4 h-4 text-green-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm text-gray-700 font-medium text-center sm:text-left">No hidden fees</span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-2 p-3 bg-white/60 backdrop-blur-sm rounded-xl border border-gray-100 hover:border-green-200 hover:bg-white/80 transition-all duration-300 group">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-600 transition-colors">
                            <svg class="w-4 h-4 text-green-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2zm10-10V7a4 4 0 0 0-8 0v4h8z"/>
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm text-gray-700 font-medium text-center sm:text-left">Secure payments</span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-2 p-3 bg-white/60 backdrop-blur-sm rounded-xl border border-gray-100 hover:border-green-200 hover:bg-white/80 transition-all duration-300 group">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-600 transition-colors">
                            <svg class="w-4 h-4 text-green-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0zm-5 0a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm text-gray-700 font-medium text-center sm:text-left">24/7 Support</span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-2 p-3 bg-white/60 backdrop-blur-sm rounded-xl border border-gray-100 hover:border-green-200 hover:bg-white/80 transition-all duration-300 group">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-600 transition-colors">
                            <svg class="w-4 h-4 text-green-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-xs sm:text-sm text-gray-700 font-medium text-center sm:text-left">Instant Transfers</span>
                    </div>
                </div>
            </div>
            
            <!-- Hero Form -->
            <div id="home" class="w-full"
                 x-data="{ inView: true }"
                 x-intersect="inView = true">
                <div x-show="inView"
                     x-cloak
                     x-transition:enter="transition ease-out duration-700 delay-200"
                     x-transition:enter-start="opacity-0 translate-y-8"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-white rounded-3xl shadow-2xl p-6 sm:p-8 lg:p-10 border border-gray-100 relative overflow-hidden">
                    <!-- Decorative gradient overlay -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-green-100/50 to-emerald-100/30 rounded-full blur-3xl -mr-32 -mt-32"></div>
                    <div class="relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl lg:text-3xl font-bold text-gray-900">Start a Secure Transaction</h3>
                </div>
                
                    <form @submit.prevent="submitForm($event)" class="space-y-5">
                        <div>
                            <label for="transaction-type" class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                            <select @change="showCustomType = $event.target.value === 'other'"
                                    id="transaction-type" 
                                    name="transaction-type"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                                    <option value="">Select a transaction type</option>
                                    <option value="ecommerce">E-commerce Marketplace Transactions</option>
                                    <option value="services">Professional Services (Consulting, Legal, Accounting)</option>
                                    <option value="real-estate">Real Estate (Land, Plots, Rentals)</option>
                                    <option value="vehicle">Vehicle Sales (Cars, Motorbikes, Trucks)</option>
                                    <option value="business">Business Transfers & Partnerships</option>
                                    <option value="freelance">Freelance Work & Digital Services</option>
                                    <option value="goods">High-Value Goods (Electronics, Machinery, Furniture)</option>
                                    <option value="construction">Construction & Renovation Projects</option>
                                    <option value="agriculture">Agricultural Produce & Equipment</option>
                                    <option value="legal">Legal Settlements & Compensation</option>
                                    <option value="import-export">Import/Export Transactions</option>
                                    <option value="tenders">Government or Corporate Tender Payments</option>
                                    <option value="education">Education Payments (International Tuition, School Fees)</option>
                                    <option value="personal">Personal Loans & Informal Lending</option>
                                    <option value="crypto">Crypto & Forex Trading Agreements</option>
                                    <option value="rentals">Equipment & Property Rentals</option>   
                                    <option value="charity">Charity Donations & Fundraising</option>
                                    <option value="events">Event Ticket Sales & Bookings</option>
                                    <option value="subscriptions">Subscription Services (Software, Memberships)</option>
                                    <option value="affiliate">Affiliate Marketing Payments</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        
                        <div x-show="showCustomType" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             style="display: none;">
                            <label for="custom-transaction-type" class="block text-sm font-medium text-gray-700 mb-2">Specify Transaction Type</label>
                            <input type="text" 
                                   id="custom-transaction-type" 
                                   name="custom-transaction-type" 
                                   placeholder="Enter custom transaction type"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                            </div>
                            
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="payment-method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                                <select @change="showPaybill = $event.target.value === 'paybill'"
                                        id="payment-method" 
                                        name="payment-method"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                                        <option value="mpesa">M-Pesa Number</option>
                                        <option value="paybill">Paybill/Buy Goods</option>
                                    </select>
                                </div>
                            <div>
                                <label for="transaction-amount" class="block text-sm font-medium text-gray-700 mb-2">Transaction Amount</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">KES</span>
                                    <input type="number" 
                                           id="transaction-amount" 
                                           name="transaction-amount" 
                                           placeholder="Amount"
                                           class="w-full pl-16 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                                </div>
                            </div>
                                </div>
                        
                        <div x-show="showPaybill"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             style="display: none;">
                            <label for="paybill-till-number" class="block text-sm font-medium text-gray-700 mb-2">Buy Goods or Paybill Number</label>
                            <input type="text" 
                                   id="paybill-till-number" 
                                   name="paybill-till-number" 
                                   value="{{ old('paybill-till-number') }}"
                                   placeholder="Buy Goods or Paybill Number"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                            </div> 
                            
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="sender-mobile" class="block text-sm font-medium text-gray-700 mb-2">Your Mobile Number</label>
                                <input type="tel" 
                                       id="sender-mobile" 
                                       name="sender-mobile" 
                                       placeholder="+254723000000"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                                </div>
                            <div>
                                <label for="receiver-mobile" class="block text-sm font-medium text-gray-700 mb-2">Recipient Mobile Number</label>
                                <input type="tel" 
                                       id="receiver-mobile" 
                                       name="receiver-mobile" 
                                       placeholder="+254723000000"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition">
                            </div>
                            </div>
                            
                        <div>
                            <label for="transaction-details" class="block text-sm font-medium text-gray-700 mb-2">Transaction Details</label>
                            <textarea id="transaction-details" 
                                      name="transaction-details" 
                                      rows="3" 
                                      placeholder="Describe your transaction..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition resize-none"></textarea>
                            </div>

                        <button type="submit" 
                                :disabled="submitting"
                                class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 hover:scale-[1.02] flex items-center justify-center gap-2 group">
                            <span x-show="!submitting" class="flex items-center gap-2">
                                Fund Your Escrow
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                            <span x-show="submitting" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                            </button>
                        
                        <div x-show="mpesaResponse"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             :class="{
                                'bg-green-50 text-green-800 border-green-200': mpesaResponse?.type === 'success',
                                'bg-red-50 text-red-800 border-red-200': mpesaResponse?.type === 'error',
                                'bg-yellow-50 text-yellow-800 border-yellow-200': mpesaResponse?.type === 'warning'
                             }"
                             class="p-3 rounded-lg border text-sm text-center"
                             style="display: none;"
                             x-text="mpesaResponse?.message">
                        </div>
                        
                        <p class="text-xs text-center text-gray-500 leading-relaxed">
                            By submitting this form, you agree to our 
                            <a href="{{route('terms.conditions')}}" target="_blank" class="text-green-600 hover:text-green-700 underline font-medium transition-colors">Terms of Service</a> 
                            and 
                            <a href="{{route('privacy.policy')}}" target="_blank" class="text-green-600 hover:text-green-700 underline font-medium transition-colors">Privacy Policy</a>.
                            </p>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
<section id="features" class="py-20 lg:py-28 bg-gradient-to-b from-white to-green-50/30 relative overflow-hidden">
    <!-- Background Decoration -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-green-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16"
             x-data="{ inView: true }"
             x-intersect="inView = true">
            <div x-show="inView"
                 x-cloak
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-green-200 text-green-700 rounded-full text-sm font-semibold mb-6 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Why Choose Us</span>
                </div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                    Features That Set Us Apart
                </h2>
                <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Our comprehensive escrow service is designed to provide security, convenience, and peace of mind for all types of transactions.
                </p>
            </div>
        </div>
                
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
            <template x-for="(feature, index) in [
                { icon: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z', title: 'Secure Transactions', desc: 'Our escrow service protects both buyers and sellers with a secure and reliable payment system.', stat: '100%', statLabel: 'Secure' },
                { icon: 'M3 11h18M7 11V7a5 5 0 0 1 10 0v4M12 16v-5M12 16h.01', title: 'Fraud Protection', desc: 'Advanced security measures and verification processes to prevent fraud and scams.', stat: '99.9%', statLabel: 'Protected' },
                { icon: 'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', title: 'Quick Processing', desc: 'Fast transaction processing with real-time updates on the status of your escrow.', stat: '<24hrs', statLabel: 'Processing' },
                { icon: 'M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z', title: 'Dispute Resolution', desc: 'Professional mediation services to resolve disputes between parties fairly and efficiently.', stat: '24/7', statLabel: 'Support' }
            ]" :key="index">
                <div x-data="{ inView: true }"
                     x-intersect="inView = true"
                     class="group">
                    <div x-show="inView"
                         x-cloak
                         x-transition:enter="transition ease-out duration-700"
                         x-transition:enter-start="opacity-0 translate-y-8"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         :style="`transition-delay: ${index * 100}ms`"
                         class="bg-white p-8 rounded-2xl border border-gray-100 hover:border-green-300 hover:shadow-2xl transition-all duration-300 h-full relative overflow-hidden group-hover:-translate-y-2">
                        <!-- Gradient overlay on hover -->
                        <div class="absolute inset-0 bg-gradient-to-br from-green-50/0 to-emerald-50/0 group-hover:from-green-50/50 group-hover:to-emerald-50/30 transition-all duration-300 rounded-2xl"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-emerald-100 rounded-2xl flex items-center justify-center group-hover:from-green-600 group-hover:to-emerald-600 group-hover:scale-110 transition-all duration-300 shadow-lg group-hover:shadow-xl">
                                    <svg class="w-8 h-8 text-green-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon"/>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-green-600 group-hover:text-green-700 transition-colors" x-html="feature.stat"></div>
                                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wide" x-text="feature.statLabel"></div>
                                </div>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-700 transition-colors" x-text="feature.title"></h3>
                            <p class="text-gray-600 leading-relaxed text-sm" x-text="feature.desc"></p>
                            
                            <!-- Learn more link -->
                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <a href="#how-it-works" class="inline-flex items-center gap-2 text-sm font-medium text-green-600 hover:text-green-700 group-hover:gap-3 transition-all duration-200">
                                    <span>Learn more</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Stats Section -->
        <div class="mt-20 grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8"
             x-data="{ inView: true }"
             x-intersect="inView = true">
            <div x-show="inView"
                 x-cloak
                 x-transition:enter="transition ease-out duration-700 delay-100"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="text-center p-6 bg-white/60 backdrop-blur-sm rounded-xl border border-gray-200">
                <div class="text-3xl lg:text-4xl font-bold text-green-600 mb-2">10,000+</div>
                <div class="text-sm font-medium text-gray-600">Active Users</div>
            </div>
            <div x-show="inView"
                 x-cloak
                 x-transition:enter="transition ease-out duration-700 delay-200"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="text-center p-6 bg-white/60 backdrop-blur-sm rounded-xl border border-gray-200">
                <div class="text-3xl lg:text-4xl font-bold text-green-600 mb-2">KES 500M+</div>
                <div class="text-sm font-medium text-gray-600">Protected</div>
            </div>
            <div x-show="inView"
                 x-cloak
                 x-transition:enter="transition ease-out duration-700 delay-300"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="text-center p-6 bg-white/60 backdrop-blur-sm rounded-xl border border-gray-200">
                <div class="text-3xl lg:text-4xl font-bold text-green-600 mb-2">50,000+</div>
                <div class="text-sm font-medium text-gray-600">Transactions</div>
            </div>
            <div x-show="inView"
                 x-cloak
                 x-transition:enter="transition ease-out duration-700 delay-400"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="text-center p-6 bg-white/60 backdrop-blur-sm rounded-xl border border-gray-200">
                <div class="text-3xl lg:text-4xl font-bold text-green-600 mb-2">99.9%</div>
                <div class="text-sm font-medium text-gray-600">Success Rate</div>
            </div>
        </div>
    </div>
</section>

<!-- Integration Section -->
<section id="integration" class="py-20 lg:py-28 bg-gradient-to-br from-green-50 to-emerald-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12"
             x-data="{ inView: true }"
             x-intersect="inView = true">
            <div x-show="inView"
                 x-cloak
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
             
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                    Easy Integration for Your Website or App
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Seamlessly add escrow payments to your project with our flexible API and SDKs.
                </p>
            </div>
        </div>
        
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-4 xl:flex xl:flex-wrap xl:justify-center gap-3 sm:gap-4 mb-8 max-w-4xl mx-auto"
             style="display: grid !important;"
             x-data="{ 
                inView: true,
                laravelIcon: '{{ asset('uploads/icon/cdnlogo.com_laravel.svg') }}',
                techs: [
                    { name: 'React', icon: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg' },
                    { name: 'Vue', icon: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg' },
                    { name: 'Angular', icon: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/angularjs/angularjs-original.svg' },
                    { name: 'Laravel', icon: '{{ asset('uploads/icon/cdnlogo.com_laravel.svg') }}' },
                    { name: 'JavaScript', icon: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg' },
                    { name: 'Java', icon: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg' },
                    { name: 'PHP', icon: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg' },
                    { name: 'Ruby', icon: 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/ruby/ruby-original.svg' }
                ]
             }"
             x-intersect="inView = true">
            <template x-for="(tech, index) in techs" :key="index">
                <div x-show="inView"
                     x-cloak
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 scale-90"
                     x-transition:enter-end="opacity-100 scale-100"
                     :style="`transition-delay: ${index * 50}ms`"
                     class="bg-white p-3 sm:p-4 rounded-xl border border-gray-200 hover:border-green-300 hover:shadow-lg transition-all duration-300 flex items-center justify-center"
                     style="width: 100%;">
                    <img :src="tech.icon" :alt="tech.name" class="w-10 h-10 sm:w-12 sm:h-12 object-contain">
                </div>
            </template>
                </div>
        
        <div class="text-center">
            <a href="{{ route('api-documentation') }}" 
               class="inline-flex items-center justify-center px-8 py-4 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                View Documentation
            </a>
        </div>
                </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-20 lg:py-28 bg-gradient-to-b from-white via-green-50/20 to-white relative overflow-hidden">
    <!-- Background Decoration -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-0 w-72 h-72 bg-green-100 rounded-full mix-blend-multiply filter blur-3xl opacity-10"></div>
        <div class="absolute bottom-1/4 right-0 w-72 h-72 bg-emerald-100 rounded-full mix-blend-multiply filter blur-3xl opacity-10"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16"
             x-data="{ inView: true }"
             x-intersect="inView = true">
            <div x-show="inView"
                 x-cloak
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-green-200 text-green-700 rounded-full text-sm font-semibold mb-6 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Simple Process</span>
                </div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                    How Our Escrow Service Works
                </h2>
                <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    We've made the escrow process simple and straightforward to ensure a smooth transaction experience. Follow these four easy steps to get started.
                </p>
            </div>
        </div>
        
        <div class="relative">
            <!-- Connection Line (Desktop) -->
            <div class="hidden lg:block absolute top-24 left-0 right-0 h-0.5 bg-gradient-to-r from-green-200 via-green-300 to-green-200"></div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <template x-for="(step, index) in [
                    { number: '1', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z', title: 'Create an Agreement', desc: 'Define the terms of your transaction, including payment amount, delivery conditions, and timeframe.', color: 'from-blue-500 to-blue-600' },
                    { number: '2', icon: 'M3 10h18M7 10V7a5 5 0 0 1 10 0v3M5 10v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-8', title: 'Buyer Makes Payment', desc: 'The buyer deposits funds into our secure escrow account. The seller is notified of the payment.', color: 'from-green-500 to-emerald-600' },
                    { number: '3', icon: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zM9 12l2 2 4-4', title: 'Verification', desc: 'We hold the funds securely while the seller delivers the goods or services as agreed upon.', color: 'from-yellow-500 to-orange-600' },
                    { number: '4', icon: 'M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', title: 'Transaction Complete', desc: 'Once the buyer approves the delivery, we release the funds to the seller, completing the transaction.', color: 'from-purple-500 to-pink-600' }
                ]" :key="index">
                    <div x-data="{ inView: true }"
                         x-intersect="inView = true"
                         class="relative group">
                        <div x-show="inView"
                             x-cloak
                             x-transition:enter="transition ease-out duration-700"
                             x-transition:enter-start="opacity-0 translate-y-8"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             :style="`transition-delay: ${index * 100}ms`"
                             class="bg-white p-8 rounded-2xl border-2 border-gray-100 hover:border-green-300 hover:shadow-2xl transition-all duration-300 h-full text-center relative overflow-hidden group-hover:-translate-y-2">
                            <!-- Gradient overlay on hover -->
                            <div :class="`absolute inset-0 bg-gradient-to-br ${step.color} opacity-0 group-hover:opacity-5 transition-opacity duration-300 rounded-2xl`"></div>
                            
                            <div class="relative z-10">
                                <!-- Step Number Badge -->
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-full text-xl font-bold mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <span x-text="step.number"></span>
                                </div>
                                
                                <!-- Icon -->
                                <div :class="`w-20 h-20 bg-gradient-to-br ${step.color} rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300`">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="step.icon"/>
                                    </svg>
                                </div>
                                
                                <!-- Content -->
                                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-700 transition-colors" x-text="step.title"></h3>
                                <p class="text-gray-600 leading-relaxed text-sm" x-text="step.desc"></p>
                                
                                <!-- Arrow indicator (mobile) -->
                                <div class="lg:hidden mt-6 flex justify-center">
                                    <template x-if="index < 3">
                                        <svg class="w-6 h-6 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                        </svg>
                                    </template>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Connection Arrow (Desktop) -->
                        <template x-if="index < 3">
                            <div class="hidden lg:block absolute top-24 -right-4 z-20">
                                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg border-2 border-green-200">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- CTA at bottom -->
        <div class="mt-16 text-center"
             x-data="{ inView: true }"
             x-intersect="inView = true">
            <div x-show="inView"
                 x-cloak
                 x-transition:enter="transition ease-out duration-700 delay-500"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="inline-block bg-white rounded-xl p-6 shadow-lg border border-gray-200">
                <p class="text-gray-700 mb-4 font-medium">Ready to get started?</p>
                <a href="#home" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Start Your Transaction
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

    <!-- CTA Section -->
<section class="py-20 lg:py-28 bg-gradient-to-br from-green-600 to-emerald-600 relative overflow-hidden">
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center"
         x-data="{ inView: true }"
         x-intersect="inView = true">
        <div x-show="inView"
             x-cloak
             x-transition:enter="transition ease-out duration-700"
             x-transition:enter-start="opacity-0 translate-y-8"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-6 backdrop-blur-sm">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
                Ready to Secure Your Transactions?
            </h2>
            <p class="text-lg sm:text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                Join thousands of satisfied clients who trust our escrow service for their important transactions.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#home" 
                   class="inline-flex items-center justify-center px-8 py-4 bg-white text-green-600 font-semibold rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Get Started Now
                </a>
                <a href="mailto:support@econfirm.co.ke" 
                   class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white font-semibold rounded-lg hover:bg-white/10 transition-all duration-200">
                    Contact Support
                </a>
                </div>
            <p class="text-white/80 text-sm mt-6">No obligation. Cancel anytime.</p>
            </div>
        </div>
    </section>

<style>
@keyframes blob {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -30px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.animate-blob {
    animation: blob 20s ease-in-out infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>

</div>
@endsection
