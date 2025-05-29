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
    <!-- Font Awesome Free CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-jQygMP4idkU0zCQvLFqGmcNybLZjvPGY0WrgqgT3gh9tXvGXh7MBWYgyE/0uWYxGjPZzqPY7N5H+Gp3lObd6Aw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <!-- Search Transaction Popup (Basic) -->
    <div id="searchTransactionPopup" style="display:none; position:fixed; z-index:1050; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:10px; max-width:350px; width:90%; margin:auto; padding:2rem 1.5rem; box-shadow:0 8px 32px rgba(0,0,0,0.18); position:relative;">
            <button id="closeSearchPopup" style="position:absolute; top:10px; right:10px; background:none; border:none; font-size:1.5rem; color:#888; cursor:pointer;">&times;</button>
            <h5 style="margin-bottom:1rem;">Search Transaction</h5>
            <form id="basic-search-transaction-form">
            <div class="form-group mb-3"> 
                <label for="basic-search-transaction-id" class="form-label">Transaction ID</label>

                <input type="text" class="form-control w-100" id="basic-search-transaction-id" name="transaction_id" placeholder="Enter Transaction ID" required>
            </div>
            <br>
            <button type="submit" class="btn btn-primary w-100">Search</button>
            </form>
            <div style="text-align: center; font-size:10px; line-height:1.2; padding-top: 10px;" id="basic-search-transaction-result" class="mt-3" style="display:none;"></div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>

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

        document.addEventListener('DOMContentLoaded', function() {
    // On-scroll animation using Intersection Observer
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

    // Transaction type animation
    var transactionType = document.getElementById('transaction-type');
    var customTypeGroup = document.getElementById('custom-transaction-type-group');
    if (transactionType && customTypeGroup) {
        customTypeGroup.style.display = transactionType.value === 'other' ? 'block' : 'none';
        transactionType.addEventListener('change', function() {
            if (this.value === 'other') {
                customTypeGroup.style.display = 'block';
                customTypeGroup.style.opacity = 0;
                customTypeGroup.style.transition = 'opacity 0.4s cubic-bezier(0.4,0,0.2,1)';
                setTimeout(function() {
                    customTypeGroup.style.opacity = 1;
                }, 10);
            } else {
                customTypeGroup.style.opacity = 0;
                customTypeGroup.style.transition = 'opacity 0.3s cubic-bezier(0.4,0,0.2,1)';
                setTimeout(function() {
                    customTypeGroup.style.display = 'none';
                }, 300);
            }
        });
    }

    // AJAX submit for transaction form
    const form = document.querySelector('.transaction-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
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
                submitBtn.innerHTML = 'Fund Your Escrow <svg style="vertical-align: middle; margin-left: 8px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12" /><polyline points="12 5 19 12 12 19" /></svg>';
                const mpesaResponse = document.getElementById('mpesa-response');
                mpesaResponse.style.display = 'block';
                if (data.success) {
                    form.reset();
                    mpesaResponse.textContent = data.message || 'Transaction submitted successfully!';
                    mpesaResponse.classList.remove('text-warning', 'alert-warning', 'alert-success');
                    mpesaResponse.classList.add('alert', 'alert-success');
                } else {
                    mpesaResponse.textContent = data.message || 'Submission failed. Please try again.';
                    mpesaResponse.classList.remove('text-success', 'alert-success', 'alert-warning');
                    mpesaResponse.classList.add('alert', 'alert-warning');
                }
            })
            .catch(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Fund Your Escrow <svg style="vertical-align: middle; margin-left: 8px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12" /><polyline points="12 5 19 12 12 19" /></svg>';
                alert('Submission failed. Please try again.');
            });
        });
    }

    // Show popup for search transaction
    var searchBtn = document.getElementById('search-transaction-btn');
    var popup = document.getElementById('searchTransactionPopup');
    var closeBtn = document.getElementById('closeSearchPopup');
    if (searchBtn && popup) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            popup.style.display = 'flex';
        });
    }
    if (closeBtn && popup) {
        closeBtn.addEventListener('click', function() {
            popup.style.display = 'none';
        });
    }
    if (popup) {
        popup.addEventListener('click', function(e) {
            if (e.target === popup) popup.style.display = 'none';
        });
    }
    // Basic AJAX search
    var basicForm = document.getElementById('basic-search-transaction-form');
    if (basicForm) {
        basicForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var resultDiv = document.getElementById('basic-search-transaction-result');
            resultDiv.style.display = 'none';
            resultDiv.innerHTML = '';
            var id = document.getElementById('basic-search-transaction-id').value.trim();
            if (!id) return;
            fetch(`/transaction/search?id=${encodeURIComponent(id)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                resultDiv.style.display = 'block';
                // Support both array and object response
                let transaction = null;
                if (data.transaction) {
                    transaction = data.transaction;
                } else if (data.data && Array.isArray(data.data) && data.data.length > 0) {
                    transaction = data.data[0];
                }
                if (data.success && transaction) {
                    resultDiv.className = 'alert alert-success';
                    resultDiv.innerHTML = `<strong>Transaction Found:</strong><br>ID: ${transaction.transaction_id || transaction.id}<br>Status: ${transaction.status}`;
                    // Show the button after 3 seconds
                    setTimeout(() => {
                        const viewId = transaction.transaction_id || transaction.id;
                        resultDiv.innerHTML += `<br><a href="/transaction/${viewId}" class="btn btn-primary mt-2">View Details</a>`;
                    }, 3000);
                } else {
                    resultDiv.className = 'alert alert-warning';
                    resultDiv.textContent = data.message || 'Transaction not found.';
                }
            })
            .catch(() => {
                resultDiv.style.display = 'block';
                resultDiv.className = 'alert alert-danger';
                resultDiv.textContent = 'Error searching transaction.';
            });
        });
    }

    // Preloader hide on page load
    window.addEventListener('load', function() {
        var preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.style.opacity = 0;
            setTimeout(function() {
                preloader.style.display = 'none';
            }, 400);
        }
    });
});
    </script>
</body>
</html>