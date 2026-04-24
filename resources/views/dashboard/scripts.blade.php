
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
            const paymentStatusPoll = { timeoutId: null, checkoutId: null, attempts: 0, maxAttempts: 45 };
            const manualCheckWrap = document.getElementById('mpesa-manual-check-wrap');
            const manualCheckBtn = document.getElementById('mpesa-check-status-btn');

            function clearPaymentStatusPoll() {
                if (paymentStatusPoll.timeoutId) {
                    clearTimeout(paymentStatusPoll.timeoutId);
                    paymentStatusPoll.timeoutId = null;
                }
                paymentStatusPoll.checkoutId = null;
                paymentStatusPoll.runOnce = null;
                if (manualCheckWrap) {
                    manualCheckWrap.style.display = 'none';
                }
                if (manualCheckBtn) {
                    manualCheckBtn.disabled = false;
                    manualCheckBtn.textContent = 'Check payment status now';
                }
            }

            if (form) {
                const submitBtn = form.querySelector('button[type="submit"]');
                const defaultBtnHTML = 'Fund Your Escrow <svg style="vertical-align: middle; margin-left: 8px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12" /><polyline points="12 5 19 12 12 19" /></svg>';

                if (manualCheckBtn) {
                    manualCheckBtn.addEventListener('click', function () {
                        if (!paymentStatusPoll.checkoutId) {
                            return;
                        }
                        if (typeof paymentStatusPoll.runOnce === 'function') {
                            paymentStatusPoll.runOnce(true);
                        }
                    });
                }

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
                                
                                mpesaResponse.textContent = data.message || 'M-Pesa prompt sent. Approve the payment on your phone when you get the request, then wait a few seconds for confirmation here.';
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

            // Polling function: first check runs immediately, then every 4s (faster than waiting 5s for first status)
            function pollTransactionStatus(checkoutRequestId) {
                const pollInterval = 4000;
                clearPaymentStatusPoll();
                paymentStatusPoll.checkoutId = checkoutRequestId;
                paymentStatusPoll.attempts = 0;
                if (manualCheckWrap) {
                    manualCheckWrap.style.display = 'block';
                }

                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = 'Waiting for confirmation...';
                }
                const box = document.getElementById('mpesa-response');
                const defaultBtnHtml = 'Fund Your Escrow <svg style="vertical-align: middle; margin-left: 8px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12" /><polyline points="12 5 19 12 12 19" /></svg>';

                function scheduleNext() {
                    if (paymentStatusPoll.timeoutId) {
                        clearTimeout(paymentStatusPoll.timeoutId);
                    }
                    paymentStatusPoll.timeoutId = setTimeout(function () {
                        run(false);
                    }, pollInterval);
                }

                function run(manual) {
                    if (paymentStatusPoll.timeoutId) {
                        clearTimeout(paymentStatusPoll.timeoutId);
                        paymentStatusPoll.timeoutId = null;
                    }
                    if (!manual) {
                        if (paymentStatusPoll.attempts >= paymentStatusPoll.maxAttempts) {
                            if (box) {
                                box.textContent = 'Still waiting for M-Pesa. You can return to the dashboard and look up this transaction, or check your M-Pesa SMS. Use the button below to check status again.';
                                box.className = 'alert alert-warning';
                            }
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = defaultBtnHtml;
                            }
                            return;
                        }
                        paymentStatusPoll.attempts++;
                    } else {
                        if (manualCheckBtn) {
                            manualCheckBtn.disabled = true;
                            manualCheckBtn.textContent = 'Checking…';
                        }
                    }
                    const url = '/transaction/status/' + encodeURIComponent(checkoutRequestId) + '?_=' + Date.now();
                    fetch(url, {
                        cache: 'no-store',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(function (res) {
                        if (res.status === 404) {
                            throw new Error('not_found');
                        }
                        return res.json();
                    })
                    .then(function (data) {
                        if (manual && manualCheckBtn) {
                            manualCheckBtn.disabled = false;
                            manualCheckBtn.textContent = 'Check payment status now';
                        }
                        if (data.status === 'completed' || data.status === 'Success') {
                            if (box) {
                                box.textContent = (data && data.message) ? data.message : 'Your escrow has been funded. Redirecting…';
                                box.className = 'alert alert-success';
                            }
                            clearPaymentStatusPoll();
                            if (submitBtn) {
                                submitBtn.innerHTML = defaultBtnHtml;
                            }
                            if (data && data.transaction_id) {
                                setTimeout(function () {
                                    window.location.href = '/get-transaction/' + data.transaction_id;
                                }, 1200);
                            }
                            return;
                        }
                        if (data.status === 'Failed') {
                            if (box) {
                                box.textContent = (data && data.message) ? data.message : 'Payment was declined or cancelled.';
                                box.className = 'alert alert-danger';
                            }
                            clearPaymentStatusPoll();
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = defaultBtnHtml;
                            }
                            return;
                        }
                        if (box && data && data.message) {
                            box.textContent = data.message;
                            box.className = 'alert alert-info';
                        }
                        scheduleNext();
                    })
                    .catch(function () {
                        if (manual && manualCheckBtn) {
                            manualCheckBtn.disabled = false;
                            manualCheckBtn.textContent = 'Check payment status now';
                        }
                        if (box) {
                            box.textContent = 'Could not read payment status. Check your connection or your dashboard using your transaction ID. You can use the button below to try again.';
                            box.className = 'alert alert-warning';
                        }
                        if (submitBtn) {
                            submitBtn.disabled = false;
                        }
                    });
                }
                paymentStatusPoll.runOnce = run;
                run(false);
            }

            // Show Search Escrow popup
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
            const transactionType = document.getElementById('transaction-type');
            const customTransactionTypeGroup = document.getElementById('custom-transaction-type-group');
            if (transactionType && customTransactionTypeGroup) {
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
            }

            const paymentMethod = document.getElementById('payment-method');
            const paybillTillGroup = document.getElementById('paybill-till-group');
            if (paymentMethod && paybillTillGroup) {
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
            }
        });
    </script>