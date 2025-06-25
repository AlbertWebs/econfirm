@extends('front.master')

@section('content')
       <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content animate-on-scroll">
                    <div class="badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span>Trusted by over 10,000 clients</span>
                    </div>
                    <h1 class="hero-title">
                        Secure Your Transactions with <span class="gradient-text">e-confirm Escrow</span>
                    </h1>
                    <p class="hero-description">
                        Our escrow service ensures safe transactions between parties. We hold and regulate payment until all terms of an agreement are met.
                    </p>
                    <div class="hero-buttons">
                        <button class="btn btn-primary btn-lg">Get Started Now</button>
                        <button class="btn btn-outline btn-lg">Learn More</button>
                    </div>
                    <div class="hero-features">
                        <div class="feature-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                            <span>No hidden fees</span>
                        </div>
                        <div class="feature-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                            <span>Secure payments</span>
                        </div>
                        <div class="feature-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                            <span>24/7 Support</span>
                        </div>
                        <div class="feature-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                            <span>Instant Transfers</span>
                        </div>
                    </div>
                </div>
                
                <div class="hero-form animate-on-scroll">
                    <div class="form-container">
                        <h3>Start a Secure Transaction</h3>
                        <form class="transaction-form">
                            <div class="form-group">
                                <label for="transaction-type">Transaction Type</label>
                                <select id="transaction-type" name="transaction-type">
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
                            <div class="form-group" id="custom-transaction-type-group" style="display:none;">
                                <label for="custom-transaction-type">Specify Transaction Type</label>
                                <input type="text" id="custom-transaction-type" name="custom-transaction-type" placeholder="Enter custom transaction type" class="w-100">
                            </div>
                            
                            {{-- <div class="form-group">
                                <label for="transaction-amount">Transaction Amount</label>
                                <div class="input-with-prefix">
                                    <span class="prefix">kes &nbsp;</span>
                                    <input type="number" id="transaction-amount" name="transaction-amount" placeholder="Amount" class="w-100">
                                </div>
                            </div> --}}

                            <div class="form-row">
                                
                                <div class="form-group">
                                    <label for="payment-method">Payment Method</label>
                                    <select id="payment-method" name="payment-method">
                                        <option value="mpesa">M-Pesa Number</option>
                                        <option value="paybill">Paybill/Buy Goods</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="transaction-amount">Transaction Amount</label>
                                    <div class="input-with-prefix">
                                        <span class="prefix">kes &nbsp;</span>
                                        <input type="number" id="transaction-amount" name="transaction-amount" placeholder="Amount" class="w-100">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="paybill-till-group" style="display:none;">
                                <label for="paybill-till-number">Buy Goods or Paybill Number</label>
                                <div>
                                    <input type="text" id="paybill-till-number" name="paybill-till-number" value="{{ old('paybill-till-number') }}" placeholder="Buy Goods or Paybill Number" class="w-100">
                                </div>
                            </div> 
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="sender-mobile">Your Mobile Number</label>
                                    <input type="tel" id="sender-mobile" name="sender-mobile" placeholder="+254723000000">
                                </div>
                                <div class="form-group">
                                    <label for="receiver-mobile">Recipient Mobile Number</label>
                                    <input type="tel" id="receiver-mobile" name="receiver-mobile" placeholder="+254723000000">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="transaction-details">Transaction Details</label>
                                <textarea id="transaction-details" name="transaction-details" rows="3" placeholder="Describe your transaction..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-full">
                                Fund Your Escrow
                                <svg style="vertical-align: middle; margin-left: 8px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </button>
                            <p style="text-align:center; margin:0 auto; font-size:10px; display:none;" id="mpesa-response"></p>

                            <p class="form-disclaimer">
                                By submitting this form, you agree to our <a target="new" href="{{route('terms.conditions')}}">Terms of Service</a> and <a target="new" href="{{route('privacy.policy')}}">Privacy Policy</a>.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

   

    <!-- Features Section -->
    <section id="features" class="features text-center">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <div class="section-badge">Why Choose Us</div>
                <h2>Features That Set Us Apart</h2>
                <p>Our comprehensive escrow service is designed to provide security, convenience, and peace of mind for all types of transactions.</p>
            </div>
            
            <div class="features-grid row justify-content-center">
                <div class="feature-card col-md-4 animate-on-scroll">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <h3>Secure Transactions</h3>
                    <p>Our escrow service protects both buyers and sellers with a secure and reliable payment system.</p>
                </div>
                
                <div class="feature-card col-md-4 animate-on-scroll">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <circle cx="12" cy="16" r="1"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <h3>Fraud Protection</h3>
                    <p>Advanced security measures and verification processes to prevent fraud and scams.</p>
                </div>
                
                <div class="feature-card col-md-4 animate-on-scroll">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </svg>
                    </div>
                    <h3>Quick Processing</h3>
                    <p>Fast transaction processing with real-time updates on the status of your escrow.</p>
                </div>

                 <div class="feature-card col-md-4 animate-on-scroll">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <h3>Dispute Resolution</h3>
                    <p>Professional mediation services to resolve disputes between parties fairly and efficiently.</p>
                </div>
                
                {{-- <div class="feature-card col-md-4 animate-on-scroll">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>
                    <h3>Multiple Payment Methods</h3>
                    <p>Support for various payment methods including credit cards, bank transfers, and cryptocurrencies.</p>
                </div>
                
                <div class="feature-card col-md-4 animate-on-scroll">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <h3>Dispute Resolution</h3>
                    <p>Professional mediation services to resolve disputes between parties fairly and efficiently.</p>
                </div>
                
                <div class="feature-card col-md-4 animate-on-scroll">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="M21 21l-4.35-4.35"/>
                        </svg>
                    </div>
                    <h3>Full Transparency</h3>
                    <p>Complete transparency with detailed transaction history and documentation.</p>
                </div> --}}
            </div>
        </div>
    </section>

        <!-- Integration Section -->
    <section class="cta" id="integration">
        <div class="container">
            <div class="cta-content">
                <div class="cta-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 18 22 12 16 6" />
                    <polyline points="8 6 2 12 8 18" />
                </svg>

                </div>
                 <div class="section-header animate-on-scroll mb-4">
                <div class="section-badge">Simple Integration</div>
                    <h2>Easy Integration for Your Website or App</h2>
                    <p>Seamlessly add escrow payments to your project with our flexible API and SDKs.</p>
                </div>
                <div class="cta-buttons">
                    {{-- <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" alt="HTML5" class="integration-logo"></button>
                    <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/nextjs/nextjs-original.svg" alt="Next.js" class="integration-logo"></button> --}}
                    <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/react/react-original.svg" alt="React" class="integration-logo"></button>
                    <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg" alt="Vue" class="integration-logo"></button>
                    <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/angularjs/angularjs-original.svg" alt="Angular" class="integration-logo"></button>
                    {{-- <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/svelte/svelte-original.svg" alt="Svelte" class="integration-logo"></button> --}}
                    {{-- <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/astro/astro-original.svg" alt="Astro" class="integration-logo"></button> --}}
                    <button class="btn btn-outline btn-lg"><img src="{{url('/')}}/uploads/icon/cdnlogo.com_laravel.svg" alt="Laravel" class="integration-logo"></button>
                    <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" alt="JavaScript" class="integration-logo"></button>
                    {{-- <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/python/python-original.svg" alt="Python" class="integration-logo"></button> --}}
                    <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg" alt="Java" class="integration-logo"></button>
                    <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" alt="PHP" class="integration-logo"></button>
                    <button class="btn btn-outline btn-lg"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/ruby/ruby-original.svg" alt="Ruby" class="integration-logo"></button>
                 
                </div>
                <br>
                 <div class="cta-buttons">
                    <a href="{{ route('api-documentation') }}" class="text-center btn btn-primary btn-lg">Documentation</a>
                </div>
            </div>
        </div>
    </section>

    
    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <div class="section-badge">Simple Process</div>
                <h2>How Our Escrow Service Works</h2>
                <p>We've made the escrow process simple and straightforward to ensure a smooth transaction experience.</p>
            </div>
            
            <div class="steps-container">
                <div class="steps-grid">
                    <div class="step-card animate-on-scroll">
                        <div class="step-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 11H5a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7a2 2 0 0 0-2-2h-4"/>
                                <polyline points="6,9 12,15 18,9"/>
                            </svg>
                        </div>
                        <h3>1. Create an Agreement</h3>
                        <p>Define the terms of your transaction, including payment amount, delivery conditions, and timeframe.</p>
                    </div>
                    
                    <div class="step-card animate-on-scroll">
                        <div class="step-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                        </div>
                        <h3>2. Buyer Makes Payment</h3>
                        <p>The buyer deposits funds into our secure escrow account. The seller is notified of the payment.</p>
                    </div>
                    
                    <div class="step-card animate-on-scroll">
                        <div class="step-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="M9 12l2 2 4-4"/>
                            </svg>
                        </div>
                        <h3>3. Verification</h3>
                        <p>We hold the funds securely while the seller delivers the goods or services as agreed upon.</p>
                    </div>
                    
                    <div class="step-card animate-on-scroll">
                        <div class="step-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                        </div>
                        <h3>4. Transaction Complete</h3>
                        <p>Once the buyer approves the delivery, we release the funds to the seller, completing the transaction.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>



 

   

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <div class="cta-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h2>Ready to Secure Your Transactions?</h2>
                <p>Join thousands of satisfied clients who trust our escrow service for their important transactions.</p>
                <div class="cta-buttons">
                    <a href="#home" class="btn btn-primary btn-lg">Get Started Now</a>
                    <a href="mailto:support@econfirm.co.ke" class="btn btn-outline btn-lg">Contact Support</a>
                </div>
                <p class="cta-note">No obligation. Cancel anytime.</p>
            </div>
        </div>
    </section>
@endsection



