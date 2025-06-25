
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