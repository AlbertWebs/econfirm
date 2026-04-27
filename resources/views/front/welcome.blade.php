@extends('front.master')

@section('content')
@php
    $homeTransactionTypes = [
        ['value' => 'ecommerce', 'label' => 'E-commerce Marketplace Transactions'],
        ['value' => 'services', 'label' => 'Professional Services (Consulting, Legal, Accounting)'],
        ['value' => 'real-estate', 'label' => 'Real Estate (Land, Plots, Rentals)'],
        ['value' => 'vehicle', 'label' => 'Vehicle Sales (Cars, Motorbikes, Trucks)'],
        ['value' => 'business', 'label' => 'Business Transfers & Partnerships'],
        ['value' => 'freelance', 'label' => 'Freelance Work & Digital Services'],
        ['value' => 'goods', 'label' => 'High-Value Goods (Electronics, Machinery, Furniture)'],
        ['value' => 'construction', 'label' => 'Construction & Renovation Projects'],
        ['value' => 'agriculture', 'label' => 'Agricultural Produce & Equipment'],
        ['value' => 'legal', 'label' => 'Legal Settlements & Compensation'],
        ['value' => 'import-export', 'label' => 'Import/Export Transactions'],
        ['value' => 'tenders', 'label' => 'Government or Corporate Tender Payments'],
        ['value' => 'education', 'label' => 'Education Payments (International Tuition, School Fees)'],
        ['value' => 'personal', 'label' => 'Personal Loans & Informal Lending'],
        ['value' => 'crypto', 'label' => 'Crypto & Forex Trading Agreements'],
        ['value' => 'rentals', 'label' => 'Equipment & Property Rentals'],
        ['value' => 'charity', 'label' => 'Charity Donations & Fundraising'],
        ['value' => 'events', 'label' => 'Event Ticket Sales & Bookings'],
        ['value' => 'subscriptions', 'label' => 'Subscription Services (Software, Memberships)'],
        ['value' => 'affiliate', 'label' => 'Affiliate Marketing Payments'],
        ['value' => 'other', 'label' => 'Other'],
    ];
@endphp
<script>
function transactionFormData() {
    return {
        transactionTypes: @json($homeTransactionTypes),
        selectedTransactionValue: '',
        transactionTypeQuery: '',
        transactionTypeOpen: false,
        showCustomType: false,
        showPaybill: false,
        transactionAmount: '',
        amountFeeTooltipDismissed: false,
        submitting: false,
        mpesaResponse: null,
        submittedForm: null,
        checkoutRequestId: null,
        _statusPollTimer: null,

        get filteredTransactionTypes() {
            const q = (this.transactionTypeQuery || '').toLowerCase().trim();
            if (!q) {
                return this.transactionTypes;
            }
            return this.transactionTypes.filter(function (t) {
                return t.label.toLowerCase().includes(q) || t.value.toLowerCase().includes(q);
            });
        },

        get showAmountFeeTooltip() {
            return String(this.transactionAmount ?? '').trim() !== '';
        },

        get showAmountFeeTooltipOpen() {
            return this.showAmountFeeTooltip && !this.amountFeeTooltipDismissed;
        },

        dismissAmountFeeTooltip() {
            this.amountFeeTooltipDismissed = true;
        },

        onTransactionAmountInput($event) {
            const v = ($event && $event.target && $event.target.value !== undefined)
                ? $event.target.value
                : this.transactionAmount;
            if (!String(v ?? '').trim()) {
                this.amountFeeTooltipDismissed = false;
            }
        },

        selectTransactionType(t) {
            this.selectedTransactionValue = t.value;
            this.transactionTypeQuery = t.label;
            this.showCustomType = t.value === 'other';
            this.transactionTypeOpen = false;
        },

        onTransactionTypeFocus() {
            this.transactionTypeOpen = true;
        },

        onTransactionTypeInput() {
            this.transactionTypeOpen = true;
            const match = this.transactionTypes.find(function (ty) {
                return ty.label.toLowerCase() === (this.transactionTypeQuery || '').toLowerCase().trim();
            }.bind(this));
            if (match) {
                this.selectedTransactionValue = match.value;
                this.showCustomType = match.value === 'other';
            } else {
                this.selectedTransactionValue = '';
                this.showCustomType = false;
            }
        },

        closeTransactionTypeDropdown() {
            var self = this;
            setTimeout(function () {
                self.transactionTypeOpen = false;
            }, 200);
        },

        syncTransactionTypeOnBlur() {
            var q = (this.transactionTypeQuery || '').toLowerCase().trim();
            if (!q) {
                this.selectedTransactionValue = '';
                this.showCustomType = false;
                return;
            }
            var exact = this.transactionTypes.find(function (t) {
                return t.label.toLowerCase() === q;
            });
            if (exact) {
                this.selectedTransactionValue = exact.value;
                this.transactionTypeQuery = exact.label;
                this.showCustomType = exact.value === 'other';
            }
        },
        
        async submitForm(event) {
            this.submitting = true;
            this.mpesaResponse = null;
            
            const form = event.target;
            this.submittedForm = form;
            if (!this.selectedTransactionValue) {
                this.mpesaResponse = { type: 'error', message: 'Please select a transaction type from the list (use search to find one).' };
                this.submitting = false;
                return;
            }
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
                    this.mpesaResponse = {
                        type: 'success',
                        message: data.message || 'STK sent. Check your phone and enter your M-PESA PIN.',
                    };
                    const checkoutId = data.CheckoutRequestID || (data.data && data.data.CheckoutRequestID);
                    if (checkoutId) {
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
        
        _clearStatusTimer() {
            if (this._statusPollTimer) {
                clearTimeout(this._statusPollTimer);
                this._statusPollTimer = null;
            }
        },

        resetSubmittedForm() {
            if (this.submittedForm && typeof this.submittedForm.reset === 'function') {
                this.submittedForm.reset();
            }
            this.selectedTransactionValue = '';
            this.transactionTypeQuery = '';
            this.transactionTypeOpen = false;
            this.showCustomType = false;
            this.transactionAmount = '';
            this.amountFeeTooltipDismissed = false;
            this.submittedForm = null;
        },

        pollTransactionStatus() {
            const self = this;
            const maxAttempts = 60;
            const pollInterval = 4000;
            this._clearStatusTimer();
            let attempts = 0;

            const timeoutMessage = 'We have not received final confirmation from M-Pesa yet. If you entered your PIN and the amount left your account, wait a few minutes, check your SMS, or open your dashboard. You can also refresh this page.';

            const scheduleNext = function () {
                self._clearStatusTimer();
                self._statusPollTimer = setTimeout(function () {
                    pollOne();
                }, pollInterval);
            };

            const pollOne = function () {
                if (attempts >= maxAttempts) {
                    self.mpesaResponse = { type: 'warning', message: timeoutMessage };
                    return;
                }
                attempts++;
                if (self._statusPollTimer) {
                    clearTimeout(self._statusPollTimer);
                    self._statusPollTimer = null;
                }
                const cid = encodeURIComponent(self.checkoutRequestId || '');
                if (!cid || cid === 'null' || cid === 'undefined') {
                    self._clearStatusTimer();
                    self.mpesaResponse = { type: 'error', message: 'Missing payment session. Please start again from the form.' };
                    return;
                }
                fetch('/transaction/status/' + cid + '?_=' + Date.now(), {
                    cache: 'no-store',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                .then(async function (res) {
                    let data = {};
                    try {
                        data = await res.json();
                    } catch (e) {
                        data = {};
                    }
                    if (res.status === 404) {
                        self._clearStatusTimer();
                        self.mpesaResponse = { type: 'error', message: data.message || 'Payment session not found. Please retry the transaction.' };
                        return;
                    }
                    if (!res.ok) {
                        self.mpesaResponse = {
                            type: 'warning',
                            message: data.message || ('Checking with M-Pesa… (server returned HTTP ' + res.status + '). Please wait.'),
                        };
                        if (attempts >= maxAttempts) {
                            self._clearStatusTimer();
                            self.mpesaResponse = { type: 'warning', message: timeoutMessage };
                            return;
                        }
                        scheduleNext();
                        return;
                    }
                    const st = (data.status != null ? String(data.status) : '').toLowerCase();
                    if (st === 'success' || st === 'completed') {
                        self._clearStatusTimer();
                        self.resetSubmittedForm();
                        if (!data.transaction_id) {
                            self.mpesaResponse = { type: 'warning', message: data.message || 'Payment received. Contact support if the page does not update.' };
                            self.checkoutRequestId = null;
                            return;
                        }
                        self.mpesaResponse = {
                            type: 'success',
                            message: data.message || 'Your escrow has been funded. Redirecting…',
                        };
                        setTimeout(function () {
                            window.location.href = '/get-transaction/' + encodeURIComponent(data.transaction_id);
                        }, 1500);
                    } else if (st === 'failed') {
                        self._clearStatusTimer();
                        self.mpesaResponse = { type: 'error', message: data.message || 'Payment failed. Please try again.' };
                    } else if (st === 'unknown' || (data.success === false && st === '')) {
                        self._clearStatusTimer();
                        self.mpesaResponse = { type: 'error', message: data.message || 'Payment session not found. Please retry the transaction.' };
                    } else if (st === 'pending') {
                        self.mpesaResponse = {
                            type: 'success',
                            message: data.message || 'Waiting for M-Pesa… (after your PIN, confirmation can take a minute or more.)',
                        };
                        if (attempts >= maxAttempts) {
                            self._clearStatusTimer();
                            self.mpesaResponse = { type: 'warning', message: timeoutMessage };
                            return;
                        }
                        scheduleNext();
                    } else if (attempts >= maxAttempts) {
                        self._clearStatusTimer();
                        self.mpesaResponse = { type: 'warning', message: timeoutMessage };
                    } else {
                        scheduleNext();
                    }
                })
                .catch(function () {
                    if (attempts >= maxAttempts) {
                        self._clearStatusTimer();
                        self.mpesaResponse = { type: 'warning', message: timeoutMessage };
                    } else {
                        scheduleNext();
                    }
                });
            };
            pollOne();
        }
    };
}
</script>

<div x-data="transactionFormData()">

       <!-- Hero: mobile shows form only; headline and CTAs from lg breakpoint up -->
<section id="home" class="relative min-h-0 sm:min-h-0 lg:min-h-[75vh] flex items-start lg:items-center bg-gradient-to-br from-green-50 via-white to-emerald-50 pt-2 pb-4 sm:pt-3 sm:pb-5 lg:pt-5 lg:pb-8 w-full min-w-0 max-w-full">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <div class="relative w-full min-w-0 max-w-[min(100%,90rem)] mx-auto px-4 sm:px-2.5 md:px-4 lg:px-8 py-2 sm:py-3 lg:py-6">
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.12fr)] gap-3 sm:gap-4 lg:gap-8 items-stretch min-w-0 min-h-0">
            <!-- Hero Content (hidden on small screens; form only on mobile) -->
            <div class="hidden lg:flex lg:flex-col lg:min-h-0 lg:min-w-0 lg:h-full lg:justify-start text-center lg:text-left space-y-5 xl:space-y-8" 
                 x-data="{ inView: true }"
                 x-intersect="inView = true">
                <div x-show="inView" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 translate-y-8"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="inline-flex w-fit max-w-full items-center gap-2 px-4 py-2.5 bg-white/80 backdrop-blur-sm border border-green-200 text-green-700 rounded-full text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-300 self-start">
                    <svg class="w-4 h-4 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span>Trusted by over {{ number_format(trusted_clients_count()) }} clients</span>
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
                    <a href="#home" class="group inline-flex h-12 items-center justify-center px-8 text-sm font-semibold bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:-translate-y-1 hover:scale-105">
                        <span>Get Started Now</span>
                        <svg class="w-4 h-4 ml-2 shrink-0 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#how-it-works" class="inline-flex h-12 items-center justify-center px-8 text-sm font-semibold bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-green-500 hover:text-green-600 hover:bg-green-50 transition-all duration-300 shadow-sm hover:shadow-md box-border">
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
            
@php
    $formLabel = 'block text-xs font-semibold text-gray-800 mb-1.5 tracking-tight';
    $formControl = 'w-full min-w-0 px-3 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 border border-gray-200 bg-white rounded-xl shadow-sm hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-[color,box-shadow,border-color] duration-150';
@endphp
            <!-- Hero Form: narrow on small screens; full width of column from lg up -->
            <div class="w-full min-w-0 max-w-full flex flex-col lg:h-full lg:min-h-0 lg:max-w-none lg:mx-0"
                 x-data="{ inView: true }"
                 x-intersect="inView = true">
                <div x-show="inView"
                     x-cloak
                     x-transition:enter="transition ease-out duration-700 delay-200"
                     x-transition:enter-start="opacity-0 translate-y-8"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-white/95 backdrop-blur-sm rounded-2xl p-3.5 sm:p-5 md:p-6 border border-gray-200/90 shadow-[0_12px_40px_-12px_rgba(15,118,110,0.15)] ring-1 ring-gray-100/80 relative w-full min-w-0 max-w-full overflow-visible lg:min-h-full">
                    <!-- Decorative gradient overlay -->
                    <div class="absolute top-0 right-0 w-32 h-32 sm:w-52 sm:h-52 bg-gradient-to-br from-emerald-100/50 to-green-50/30 rounded-full blur-2xl -mr-12 -mt-12 sm:-mr-28 sm:-mt-28 pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-green-100/30 to-transparent rounded-full blur-2xl -ml-8 -mb-8 pointer-events-none"></div>
                    <div class="relative min-w-0">
                        <div class="flex items-center gap-3 mb-1 pb-4 border-b border-gray-100/90">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-md shadow-green-600/20 ring-2 ring-white">
                                <svg class="w-4 h-4 sm:w-[1.1rem] sm:h-[1.1rem] text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <h3 class="min-w-0 text-base sm:text-lg font-bold text-gray-900 leading-snug">Start Escrow</h3>
                        </div>
                
                    <form @submit.prevent="submitForm($event)" class="mt-4 sm:mt-5 space-y-3.5 sm:space-y-4 w-full min-w-0 text-sm">
                        <div class="relative min-w-0">
                            <label for="transaction-type-search" class="{{ $formLabel }}">Transaction Type</label>
                            <input type="hidden" name="transaction-type" id="transaction-type" x-model="selectedTransactionValue">
                            <div class="relative group">
                                <span class="pointer-events-none absolute left-3 top-1/2 z-10 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-600 transition-colors" aria-hidden="true">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text"
                                       id="transaction-type-search"
                                       x-model="transactionTypeQuery"
                                       @focus="onTransactionTypeFocus()"
                                       @input="onTransactionTypeInput()"
                                       @blur="syncTransactionTypeOnBlur(); closeTransactionTypeDropdown()"
                                       autocomplete="off"
                                       placeholder="Search or select transaction type…"
                                       class="{{ $formControl }} pl-10"
                                       role="combobox"
                                       aria-autocomplete="list"
                                       :aria-expanded="transactionTypeOpen">
                                <ul x-show="transactionTypeOpen && filteredTransactionTypes.length > 0"
                                    x-transition
                                    x-cloak
                                    class="absolute z-50 left-0 right-0 top-full mt-1.5 w-full min-w-0 max-h-48 sm:max-h-56 overflow-y-auto overflow-x-hidden [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden rounded-xl border border-gray-200/90 bg-white py-1.5 shadow-xl shadow-gray-200/50 ring-1 ring-black/5"
                                    role="listbox">
                                    <template x-for="t in filteredTransactionTypes" :key="t.value">
                                        <li role="option"
                                            @mousedown.prevent="selectTransactionType(t)"
                                            class="px-3 py-2 mx-0.5 text-xs sm:text-sm text-gray-800 cursor-pointer rounded-lg hover:bg-emerald-50 active:bg-emerald-100/80 break-words"
                                            x-text="t.label"></li>
                                    </template>
                                </ul>
                                <p x-show="transactionTypeOpen && transactionTypeQuery && filteredTransactionTypes.length === 0"
                                   x-cloak
                                   class="absolute z-50 w-full mt-1.5 rounded-lg border border-amber-200/90 bg-amber-50/95 px-3 py-2 text-xs text-amber-900 shadow-sm">
                                    No matching types. Try different keywords.
                                </p>
                            </div>
                        </div>
                        
                        <div x-show="showCustomType" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             style="display: none;">
                            <label for="custom-transaction-type" class="{{ $formLabel }}">Specify Transaction Type</label>
                            <input type="text" 
                                   id="custom-transaction-type" 
                                   name="custom-transaction-type" 
                                   placeholder="Enter custom transaction type"
                                   class="{{ $formControl }}">
                            </div>
                            
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4 min-w-0">
                            <div class="min-w-0">
                                <label for="payment-method" class="{{ $formLabel }}">Payment Method</label>
                                <div class="relative">
                                <select @change="showPaybill = $event.target.value === 'paybill'"
                                        id="payment-method" 
                                        name="payment-method"
                                        class="{{ $formControl }} appearance-none pr-9 cursor-pointer bg-white">
                                        <option value="mpesa">M-Pesa Number</option>
                                        <option value="paybill">Paybill/Buy Goods</option>
                                    </select>
                                <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </span>
                                </div>
                                </div>
                            <div class="min-w-0 self-start">
                                <label for="transaction-amount" class="{{ $formLabel }}">Transaction Amount</label>
                                <!-- Fee hint above the field; positioned relative to input wrapper so grid stretch does not break placement -->
                                <div class="relative z-20">
                                    <span class="absolute left-3 top-1/2 z-10 -translate-y-1/2 text-gray-500 font-semibold text-xs tabular-nums">KES</span>
                                    <input type="number" 
                                           id="transaction-amount" 
                                           name="transaction-amount" 
                                           x-model="transactionAmount"
                                           @input="onTransactionAmountInput()"
                                           :aria-describedby="showAmountFeeTooltipOpen ? 'amount-fee-tooltip' : null"
                                           inputmode="decimal"
                                           placeholder="Amount"
                                           class="{{ $formControl }} pl-14 tabular-nums">
                                    <div id="amount-fee-tooltip"
                                         role="tooltip"
                                         x-show="showAmountFeeTooltipOpen"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-1.5 scale-[0.98]"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 -translate-y-1"
                                         x-cloak
                                         class="absolute left-0 right-0 bottom-full z-30 mb-1.5 rounded-lg border border-emerald-200/90 bg-white pl-3 pr-8 py-2.5 text-[11px] sm:text-xs text-gray-700 leading-snug shadow-lg shadow-emerald-900/10 ring-1 ring-black/5">
                                        <button type="button"
                                                @click.stop="dismissAmountFeeTooltip()"
                                                class="absolute top-1 right-1 z-20 flex h-6 w-6 items-center justify-center rounded-md text-gray-500 hover:text-gray-800 hover:bg-gray-100/90 focus:outline-none focus:ring-2 focus:ring-emerald-500/40"
                                                aria-label="Close fee information">
                                            <i class="fas fa-times text-[10px]" aria-hidden="true"></i>
                                        </button>
                                        <span class="absolute -bottom-1.5 left-4 h-3 w-3 rotate-45 border-b border-r border-emerald-200/90 bg-white" aria-hidden="true"></span>
                                        <span class="relative z-10 flex gap-2 pr-0.5">
                                            <span class="text-emerald-600 shrink-0 mt-0.5" aria-hidden="true">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                            </span>
                                            <span>Escrow principal. A 1% platform fee is added; your M-Pesa prompt shows the total to pay.</span>
                                        </span>
                                    </div>
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
                            <label for="paybill-till-number" class="{{ $formLabel }}">Buy Goods or Paybill Number</label>
                            <input type="text" 
                                   id="paybill-till-number" 
                                   name="paybill-till-number" 
                                   value="{{ old('paybill-till-number') }}"
                                   placeholder="Buy Goods or Paybill Number"
                                   class="{{ $formControl }}">
                            </div> 
                            
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4 min-w-0">
                            <div class="min-w-0">
                                <label for="sender-mobile" class="{{ $formLabel }}">Your Mobile Number</label>
                                <input type="tel" 
                                       id="sender-mobile" 
                                       name="sender-mobile" 
                                       inputmode="tel" autocomplete="tel"
                                       placeholder="07XXXXXXXX or +2547XXXXXXXX"
                                       class="{{ $formControl }}">
                                </div>
                            <div class="min-w-0">
                                <label for="receiver-mobile" class="{{ $formLabel }}">Recipient Mobile Number</label>
                                <input type="tel" 
                                       id="receiver-mobile" 
                                       name="receiver-mobile" 
                                       inputmode="tel" autocomplete="tel"
                                       placeholder="07XXXXXXXX or +2547XXXXXXXX"
                                       class="{{ $formControl }}">
                            </div>
                            </div>
                            
                        <div class="min-w-0">
                            <label for="transaction-details" class="{{ $formLabel }}">Transaction Details</label>
                            <textarea id="transaction-details" 
                                      name="transaction-details" 
                                      rows="3"
                                      placeholder="What are you buying, expected delivery, and any key terms…"
                                      class="{{ $formControl }} resize-y min-h-[4.5rem] sm:min-h-20"></textarea>
                            </div>

                        <button type="submit" 
                                :disabled="submitting"
                                class="w-full mt-0.5 h-12 px-4 text-sm font-semibold bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg shadow-emerald-600/25 hover:shadow-xl hover:shadow-emerald-600/30 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 group shrink-0">
                            <span x-show="!submitting" class="flex items-center gap-1.5 sm:gap-2">
                                Fund Your Escrow
                                <svg class="w-4 h-4 shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </span>
                            <span x-show="submitting" class="flex items-center gap-1.5 text-xs sm:text-sm">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
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
                             class="p-3 sm:p-3.5 rounded-xl border text-xs sm:text-sm text-center leading-relaxed shadow-sm"
                             style="display: none;"
                             x-text="mpesaResponse?.message">
                        </div>
                        
                        <p class="text-[11px] sm:text-xs text-center text-gray-500 leading-relaxed pt-0.5">
                            By submitting, you agree to our
                            <a href="{{route('terms.conditions')}}" target="_blank" rel="noopener noreferrer" class="text-emerald-700 hover:text-emerald-800 font-medium underline decoration-emerald-300 underline-offset-2 hover:decoration-emerald-500 transition-colors">Terms of Service</a>
                            and
                            <a href="{{route('privacy.policy')}}" target="_blank" rel="noopener noreferrer" class="text-emerald-700 hover:text-emerald-800 font-medium underline decoration-emerald-300 underline-offset-2 hover:decoration-emerald-500 transition-colors">Privacy Policy</a>.
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
                { slug: 'secure-transactions', icon: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z', title: 'Secure Transactions', desc: 'Our escrow service protects both buyers and sellers with a secure and reliable payment system.', stat: '100%', statLabel: 'Secure' },
                { slug: 'fraud-protection', icon: 'M3 11h18M7 11V7a5 5 0 0 1 10 0v4M12 16v-5M12 16h.01', title: 'Fraud Protection', desc: 'Advanced security measures and verification processes to prevent fraud and scams.', stat: '99.9%', statLabel: 'Protected' },
                { slug: 'quick-processing', icon: 'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', title: 'Quick Processing', desc: 'Fast transaction processing with real-time updates on the status of your escrow.', stat: '<24hrs', statLabel: 'Processing' },
                { slug: 'dispute-resolution', icon: 'M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z', title: 'Dispute Resolution', desc: 'Professional mediation services to resolve disputes between parties fairly and efficiently.', stat: '24/7', statLabel: 'Support' }
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
                                <a :href="'{{ route('features') }}#' + feature.slug" class="inline-flex items-center gap-2 text-sm font-medium text-green-600 hover:text-green-700 group-hover:gap-3 transition-all duration-200">
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
                <div class="text-3xl lg:text-4xl font-bold text-green-600 mb-2">{{ number_format(trusted_clients_count()) }}+</div>
                <div class="text-sm font-medium text-gray-600">Active Users</div>
            </div>
            <div x-show="inView"
                 x-cloak
                 x-transition:enter="transition ease-out duration-700 delay-200"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="text-center p-6 bg-white/60 backdrop-blur-sm rounded-xl border border-gray-200">
                <div class="text-3xl lg:text-4xl font-bold text-green-600 mb-2">{{ format_funds_protected_stk_kes_for_stat() }}</div>
                <div class="text-sm font-medium text-gray-600">Protected</div>
            </div>
            <div x-show="inView"
                 x-cloak
                 x-transition:enter="transition ease-out duration-700 delay-300"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="text-center p-6 bg-white/60 backdrop-blur-sm rounded-xl border border-gray-200">
                <div class="text-3xl lg:text-4xl font-bold text-green-600 mb-2">{{ number_format(trusted_transactions_count()) }}+</div>
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
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-2xl mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-900">
                Easy integration for your website or app
            </h2>
            <p class="mt-4 max-w-2xl mx-auto text-base sm:text-lg text-gray-600 leading-relaxed">
                Connect e-confirm to your own checkout or back office using our REST API—create escrows, check status, and release funds when you are ready. Use the documentation for endpoints and examples, or the developer area to manage your API key.
            </p>
        </div>

        <div class="text-center flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
            <a href="{{ route('api-documentation') }}"
               class="inline-flex items-center justify-center px-8 py-4 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 w-full sm:w-auto">
                View full documentation
            </a>
            <a href="{{ auth()->check() ? route('api.home') : route('developer.login') }}"
               class="inline-flex items-center justify-center px-8 py-4 border-2 border-green-600 text-green-800 font-semibold rounded-lg hover:bg-green-50 transition-all duration-200 w-full sm:w-auto">
                API developer — keys &amp; URLs
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
            <div class="grid grid-cols-2 gap-2.5 sm:gap-4 sm:flex sm:flex-row sm:justify-center w-full max-w-xl mx-auto items-stretch">
                <a href="#home" 
                   class="inline-flex items-center justify-center text-center text-sm sm:text-base px-3 py-3.5 sm:px-8 sm:py-4 bg-white text-green-600 font-semibold rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 min-w-0">
                    Get Started Now
                </a>
                <a href="mailto:info@econfirm.co.ke" 
                   class="inline-flex items-center justify-center text-center text-sm sm:text-base px-3 py-3.5 sm:px-8 sm:py-4 border-2 border-white text-white font-semibold rounded-lg hover:bg-white/10 transition-all duration-200 min-w-0">
                    Contact Support
                </a>
            </div>
            <p class="text-white/80 text-sm mt-6">No obligation. Cancel anytime.</p>
            </div>
        </div>
    </section>

@if(isset($latestBlogs) && $latestBlogs->isNotEmpty())
<section id="blogs" class="py-20 lg:py-24 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-10">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 border border-green-200 text-green-700 rounded-full text-sm font-semibold">
                    <i class="fas fa-newspaper text-xs" aria-hidden="true"></i>
                    <span>Latest Blogs</span>
                </div>
                <h2 class="mt-4 text-3xl sm:text-4xl font-bold text-gray-900">Insights from eConfirm</h2>
                <p class="mt-3 text-base sm:text-lg text-gray-600 max-w-2xl">
                    Practical guides, product updates, and escrow tips to help you transact safely.
                </p>
            </div>
            <a href="{{ route('insights.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                View all insights
                <span aria-hidden="true">→</span>
            </a>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($latestBlogs as $blog)
                <article class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <a href="{{ route('insights.show', $blog->slug) }}" class="block aspect-[16/10] overflow-hidden bg-gray-100">
                        @if($blog->featuredImageUrl())
                            <img src="{{ $blog->featuredImageUrl() }}"
                                 alt="{{ $blog->title }}"
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                 loading="lazy"
                                 decoding="async">
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gradient-to-br from-green-100 to-emerald-50 text-emerald-600/70">
                                <i class="fas fa-image text-4xl" aria-hidden="true"></i>
                            </div>
                        @endif
                    </a>
                    <div class="p-5">
                        <time class="text-xs uppercase tracking-wide text-gray-500" datetime="{{ $blog->published_at?->toAtomString() }}">
                            {{ $blog->published_at?->format('F j, Y') }}
                        </time>
                        <h3 class="mt-2 text-lg font-semibold text-gray-900 leading-snug">
                            <a href="{{ route('insights.show', $blog->slug) }}" class="hover:text-emerald-700 transition-colors">
                                {{ $blog->title }}
                            </a>
                        </h3>
                        @if(filled($blog->excerpt))
                            <p class="mt-2 text-sm text-gray-600 line-clamp-3">{{ $blog->excerpt }}</p>
                        @endif
                        <div class="mt-4">
                            <a href="{{ route('insights.show', $blog->slug) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                                Read article
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

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
