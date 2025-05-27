<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureEscrow - Trusted Escrow Services</title>
    <meta name="description" content="Professional escrow services for secure transactions. We provide safe, reliable escrow solutions for real estate, vehicles, businesses, and more.">
    <link rel="stylesheet" href="{{asset('theme/style.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="{{ asset('uploads/favicon.png') }}">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <a href="#">
                        <img src="{{ asset('uploads/logo.png') }}" alt="SecureEscrow Logo" style="height: 50px; vertical-align: middle;">
                    </a>
                </div>
                
                <nav class="nav-desktop">
                    <a href="#features">Features</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#faq">FAQ</a>
                    <button class="btn btn-outline">Log In</button>
                    <button class="btn btn-primary">Get Started</button>
                </nav>
                
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
            
            <nav class="nav-mobile" id="mobileNav">
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#pricing">Pricing</a>
                <a href="#faq">FAQ</a>
                <button class="btn btn-outline">Log In</button>
                <button class="btn btn-primary">Get Started</button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <div class="badge">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span>Trusted by over 10,000 clients</span>
                    </div>
                    <h1 class="hero-title">
                        Secure Transactions with <span class="gradient-text">Professional Escrow</span>
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
                    </div>
                </div>
                
                <div class="hero-form">
                    <div class="form-container">
                        <h3>Start a Secure Transaction</h3>
                        <form class="transaction-form">
                            <div class="form-group">
                                <label for="transaction-type">Transaction Type</label>
                                <select id="transaction-type" name="transaction-type">
                                    <option value="">Select a transaction type</option>
                                    <option value="real-estate">Real Estate</option>
                                    <option value="vehicle">Vehicle</option>
                                    <option value="business">Business</option>
                                    <option value="personal">Personal</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="transaction-amount">Transaction Amount</label>
                                <div class="input-with-prefix">
                                    <span class="prefix">$</span>
                                    <input type="number" id="transaction-amount" name="transaction-amount" placeholder="Amount">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="sender-email">Your Email</label>
                                    <input type="email" id="sender-email" name="sender-email" placeholder="your@email.com">
                                </div>
                                <div class="form-group">
                                    <label for="receiver-email">Recipient Email</label>
                                    <input type="email" id="receiver-email" name="receiver-email" placeholder="recipient@email.com">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="transaction-details">Transaction Details</label>
                                <textarea id="transaction-details" name="transaction-details" rows="3" placeholder="Describe your transaction..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-full">Start Escrow Process</button>
                            
                            <p class="form-disclaimer">
                                By submitting this form, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <div class="section-badge">Why Choose Us</div>
                <h2>Features That Set Us Apart</h2>
                <p>Our comprehensive escrow service is designed to provide security, convenience, and peace of mind for all types of transactions.</p>
            </div>
            
            <div class="features-grid row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <h3>Secure Transactions</h3>
                        <p>Our escrow service protects both buyers and sellers with a secure and reliable payment system.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card animate-on-scroll">
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
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12,6 12,12 16,14"/>
                            </svg>
                        </div>
                        <h3>Quick Processing</h3>
                        <p>Fast transaction processing with real-time updates on the status of your escrow.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                        </div>
                        <h3>Multiple Payment Methods</h3>
                        <p>Support for various payment methods including credit cards, bank transfers, and cryptocurrencies.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card animate-on-scroll">
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
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="M21 21l-4.35-4.35"/>
                            </svg>
                        </div>
                        <h3>Full Transparency</h3>
                        <p>Complete transparency with detailed transaction history and documentation.</p>
                    </div>
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
                        <h3>3. We Verify Everything</h3>
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
                    <button class="btn btn-primary btn-lg">Get Started Now</button>
                    <button class="btn btn-outline btn-lg">Contact Sales</button>
                </div>
                <p class="cta-note">No obligation. Cancel anytime.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="footer-logo">
                        {{-- logo here --}}
                        <img src="{{ asset('uploads/logo.png') }}" alt="SecureEscrow Logo" style="height: 50px; vertical-align: middle;">
                        {{-- <span class="logo-text">SecureEscrow</span> --}}
                    </div>
                    <p>Providing secure escrow services for all your transaction needs.</p>
                    <div class="social-links">
                        <a href="#">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                            </svg>
                        </a>
                        <a href="#">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>
                            </svg>
                        </a>
                        <a href="#">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                                <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                            </svg>
                        </a>
                        <a href="#">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                                <rect width="4" height="12" x="2" y="9"/>
                                <circle cx="4" cy="4" r="2"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>Products</h3>
                    <ul>
                        <li><a href="#">Standard Escrow</a></li>
                        <li><a href="#">Real Estate Escrow</a></li>
                        <li><a href="#">Vehicle Escrow</a></li>
                        <li><a href="#">Business Escrow</a></li>
                        <li><a href="#">International Escrow</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Company</h3>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Press</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Legal</h3>
                    <ul>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Security</a></li>
                        <li><a href="#">Compliance</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 e-confirm. All rights reserved.</p>
                <div class="footer-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22,4 12,14.01 9,11.01"/>
                    </svg>
                    <span>Licensed and regulated escrow service</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoA6DQD1Cj6U6U5r9mMZ6E6Q5Vb5Q5n5F5D5F5F5F5F5F5F" crossorigin="anonymous"></script>
    <script>
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobileNav');
            mobileNav.classList.toggle('active');
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // On-scroll animation using Intersection Observer
        document.addEventListener('DOMContentLoaded', function () {
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>