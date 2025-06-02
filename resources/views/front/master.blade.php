<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <link rel="stylesheet" href="{{asset('theme/style.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="{{ asset('uploads/favicon.png') }}">
    <!-- Font Awesome Free CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-jQygMP4idkU0zCQvLFqGmcNybLZjvPGY0WrgqgT3gh9tXvGXh7MBWYgyE/0uWYxGjPZzqPY7N5H+Gp3lObd6Aw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
        "https://www.linkedin.com/company/econfirm",
        "https://www.instagram.com/econfirmke/",
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


   {{--  --}}
   
   
    <style>
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
<body>
<div id="preloader"><div class="loader"></div></div>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <a href="#">
                        <img src="{{ asset('uploads/logo-hoz.png') }}" alt="e-confirm Logo" style="height: 70px; vertical-align: middle;">
                    </a>
                </div>
                
                <nav class="nav-desktop">
                    <a href="#features">Features</a>
                    <a href="#how-it-works">How It Works</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#faq">FAQ</a>
                    <button class="btn btn-outline">Log In</button>
                    <button class="btn btn-outline" id="search-transaction-btn"><i class="fas fa-search"></i> Search Transaction</button>
                    <button class="btn btn-primary">
                        <i class="fas fa-search text-white"></i>Custom Solutions
                    </button>
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
                
                <button class="btn btn-primary">Custom Solutions</button>
            </nav>
        </div>
    </header>


    @yield('content')
  

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
                        <li><a href="{{ route('terms.conditions') }}">Terms of Service</a></li>
                        <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('security') }}">Security</a></li>
                        <li><a href="{{ route('complience') }}">Compliance</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{date('Y')}} Confirm Diligence Solutions. - All rights reserved.</p>
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

    <!-- Search Transaction Popup (Basic) -->
    <div id="searchTransactionPopup" style="display:none; position:fixed; z-index:1050; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
        <div class="popup-content-anim" style="background:#fff; border-radius:10px; max-width:350px; width:90%; margin:auto; padding:2rem 1.5rem; box-shadow:0 8px 32px rgba(0,0,0,0.18); position:relative;">
            <button id="closeSearchPopup" style="position:absolute; top:10px; right:10px; background:none; border:none; font-size:1.5rem; color:#888; cursor:pointer;">&times;</button>
            <h5 style="margin-bottom:1rem;">Search Transaction</h5>
            <form id="basic-search-transaction-form">
            <div class="form-group mb-3"> 
                <label for="basic-search-transaction-id" class="form-label">Transaction ID</label>
                <input type="text" class="form-control w-100" id="basic-search-transaction-id" name="transaction_id" placeholder="Enter Transaction ID" required>
            </div>
            <br>
            <button type="submit" class="btn btn-outline w-100" id="search-btn">Search</button>
            </form>
            <div style="text-align: center; font-size:10px; line-height:1.2; padding-top: 10px;" id="basic-search-transaction-result" class="mt-3" style="display:none;"></div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>

   <script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle Mobile Menu
    window.toggleMobileMenu = function () {
        const mobileNav = document.getElementById('mobileNav');
        if (mobileNav) {
            mobileNav.classList.toggle('active');
        }
    };

    // Smooth Scrolling for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // On-scroll Animation using Intersection Observer
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));

    // Transaction Type Animation
    const transactionType = document.getElementById('transaction-type');
    const customTypeGroup = document.getElementById('custom-transaction-type-group');
    if (transactionType && customTypeGroup) {
        customTypeGroup.style.display = transactionType.value === 'other' ? 'block' : 'none';

        transactionType.addEventListener('change', function () {
            if (this.value === 'other') {
                customTypeGroup.style.display = 'block';
                customTypeGroup.style.opacity = 0;
                customTypeGroup.style.transition = 'opacity 0.4s cubic-bezier(0.4,0,0.2,1)';
                setTimeout(() => { customTypeGroup.style.opacity = 1; }, 10);
            } else {
                customTypeGroup.style.opacity = 0;
                customTypeGroup.style.transition = 'opacity 0.3s cubic-bezier(0.4,0,0.2,1)';
                setTimeout(() => { customTypeGroup.style.display = 'none'; }, 300);
            }
        });
    }

    // AJAX Submit for Transaction Form
    const form = document.querySelector('.transaction-form');
    if (form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const defaultBtnHTML = 'Fund Your Escrow <svg style="vertical-align: middle; margin-left: 8px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12" /><polyline points="12 5 19 12 12 19" /></svg>';

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Processing...';

            fetch('/submit-transaction', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = defaultBtnHTML;

                const mpesaResponse = document.getElementById('mpesa-response');
                if (mpesaResponse) {
                    mpesaResponse.style.display = 'block';
                    if (data.success) {
                        
                        mpesaResponse.textContent = 'STK push sent. Waiting for payment confirmation...';
                        mpesaResponse.className = 'alert alert-success';
                        // Check for CheckoutRequestID before polling
                        const checkoutRequestId = data.CheckoutRequestID || (data.data && data.data.CheckoutRequestID);
                        if (checkoutRequestId) {
                            form.reset();
                            pollTransactionStatus(checkoutRequestId);
                        }
                    } else {
                        mpesaResponse.textContent = data.message || 'Submission failed. Please try again.';
                        mpesaResponse.className = 'alert alert-warning';
                    }
                }
            })
            .catch(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = defaultBtnHTML;
                alert('Submission failed. Please try again.');
            });
        });
    }

    // Polling function for transaction status
    function pollTransactionStatus(checkoutRequestId) {
        let pollInterval = 5000; // 5 seconds
        let maxAttempts = 24; // 2 minutes
        let attempts = 0;
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.innerHTML = 'Waiting for confirmation...';
        const mpesaResponse = document.getElementById('mpesa-response');
        const poll = setInterval(() => {
            attempts++;
            fetch(`/transaction/status/${checkoutRequestId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'completed' || data.status === 'Success') {
                    clearInterval(poll);
                    if (mpesaResponse) {
                        mpesaResponse.textContent = 'Payment received! Redirecting...';
                        mpesaResponse.className = 'alert alert-success';
                    }
                    setTimeout(() => {
                        window.location.href = `/get-transaction/${data.transaction_id}`; //only work with transaction_id
                    }, 1500);
                } else if (data.status === 'Failed') {
                    clearInterval(poll);
                    if (mpesaResponse) {
                        mpesaResponse.textContent = 'Payment failed. Please try again.';
                        mpesaResponse.className = 'alert alert-danger';
                    }
                } else if (attempts >= maxAttempts) {
                    clearInterval(poll);
                    if (mpesaResponse) {
                        mpesaResponse.textContent = 'Payment confirmation timed out. Please check your transaction status later.';
                        mpesaResponse.className = 'alert alert-warning';
                    }
                }
            })
            .catch(() => {
                if (attempts >= maxAttempts) {
                    clearInterval(poll);
                    if (mpesaResponse) {
                        mpesaResponse.textContent = 'Payment confirmation timed out. Please check your transaction status later.';
                        mpesaResponse.className = 'alert alert-warning';
                    }
                }
            });
        }, pollInterval);
    }

    // Show Popup for Search Transaction
    const searchBtn = document.getElementById('search-transaction-btn');
    const popup = document.getElementById('searchTransactionPopup');
    const closeBtn = document.getElementById('closeSearchPopup');
    const popupContent = popup?.querySelector('.popup-content-anim');

    if (searchBtn && popup) {
        searchBtn.addEventListener('click', function (e) {
            e.preventDefault();
            popup.classList.add('active');
            if (popupContent) {
                popupContent.classList.remove('popupIn');
                void popupContent.offsetWidth;
                popupContent.classList.add('popupIn');
            }
        });
    }

    if (closeBtn && popup) {
        closeBtn.addEventListener('click', function () {
            popup.classList.remove('active');
        });
    }

    if (popup) {
        popup.addEventListener('click', function (e) {
            if (e.target === popup) popup.classList.remove('active');
        });
    }

    // Basic AJAX Search with Loading Spinner
    const basicForm = document.getElementById('basic-search-transaction-form');
    if (basicForm) {
        basicForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const resultDiv = document.getElementById('basic-search-transaction-result');
            const idInput = document.getElementById('basic-search-transaction-id');
            const btn = document.getElementById('search-btn');

            resultDiv.style.display = 'none';
            resultDiv.innerHTML = '';

            const id = idInput?.value.trim();
            if (!id) return;

            // Show loading spinner
            btn.classList.add('btn-loading');
            btn.innerHTML = 'Searching <span class="spinner"></span>';

            fetch(`/transaction/search?id=${encodeURIComponent(id)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                btn.classList.remove('btn-loading');
                btn.innerHTML = 'Search';
                resultDiv.style.display = 'block';

                let transaction = null;
                if (data.transaction) {
                    transaction = data.transaction;
                } else if (Array.isArray(data.data) && data.data.length > 0) {
                    transaction = data.data[0];
                }

                if (data.success && transaction) {
                    resultDiv.className = 'alert alert-success';
                    resultDiv.innerHTML = `<strong>Transaction Found:</strong><br>ID: ${transaction.transaction_id || transaction.id}<br>Status: ${transaction.status}`;
                    setTimeout(() => {
                        const viewId = transaction.transaction_id || transaction.id;
                        resultDiv.innerHTML += `<br><a href="/get-transaction/${viewId}" class="btn btn-outline mt-2 small-btn">View Transaction <i class='fas fa-arrow-right' style='margin-left:6px;'></i></a>`;
                    }, 3000);
                } else {
                    resultDiv.className = 'alert alert-warning';
                    resultDiv.textContent = data.message || 'Transaction not found.';
                }
            })
            .catch(() => {
                btn.classList.remove('btn-loading');
                btn.innerHTML = 'Search';
                resultDiv.style.display = 'block';
                resultDiv.className = 'alert alert-danger';
                resultDiv.textContent = 'Error searching transaction.';
            });
        });
    }

    //Paybill of buygoods
    

    // Preloader Hide on Page Load
    window.addEventListener('load', function () {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.style.opacity = 0;
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 400);
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Animate custom transaction type group
    const transactionType = document.getElementById('transaction-type');
    const customTransactionTypeGroup = document.getElementById('custom-transaction-type-group');
    function updateTransactionType() {
        if (transactionType.value === 'other') {
            customTransactionTypeGroup.style.display = '';
            customTransactionTypeGroup.style.opacity = 0;
            customTransactionTypeGroup.style.transition = 'opacity 0.4s cubic-bezier(0.4,0,0.2,1)';
            setTimeout(() => { customTransactionTypeGroup.style.opacity = 1; }, 10);
        } else {
            customTransactionTypeGroup.style.opacity = 0;
            customTransactionTypeGroup.style.transition = 'opacity 0.3s cubic-bezier(0.4,0,0.2,1)';
            setTimeout(() => { customTransactionTypeGroup.style.display = 'none'; }, 300);
        }
    }
    transactionType.addEventListener('change', updateTransactionType);
    updateTransactionType();

    // Animate paybill/till group
    const paymentMethod = document.getElementById('payment-method');
    const paybillTillGroup = document.getElementById('paybill-till-group');
    function updatePaybillTill() {
        if (paymentMethod.value === 'paybill') {
            paybillTillGroup.style.display = '';
            paybillTillGroup.style.opacity = 0;
            paybillTillGroup.style.transition = 'opacity 0.4s cubic-bezier(0.4,0,0.2,1)';
            setTimeout(() => { paybillTillGroup.style.opacity = 1; }, 10);
        } else {
            paybillTillGroup.style.opacity = 0;
            paybillTillGroup.style.transition = 'opacity 0.3s cubic-bezier(0.4,0,0.2,1)';
            setTimeout(() => { paybillTillGroup.style.display = 'none'; }, 300);
        }
    }
    paymentMethod.addEventListener('change', updatePaybillTill);
    updatePaybillTill();
});
</script>

</body>
</html>