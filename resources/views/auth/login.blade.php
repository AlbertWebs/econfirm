@extends('process.master')

@section('content')
<style>
    .login-page {
        --login-green: #18743c;
        --login-green-dark: #155f32;
        --login-green-soft: rgba(24, 116, 60, 0.08);
        --login-green-border: rgba(24, 116, 60, 0.2);
    }
    .login-page .min-vh-50 {
        min-height: calc(100vh - 220px);
    }
    .login-page .auth-card {
        border: 1px solid var(--login-green-border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(24, 116, 60, 0.12), 0 4px 12px rgba(0, 0, 0, 0.06);
        max-width: 440px;
        margin-left: auto;
        margin-right: auto;
    }
    .login-page .auth-header {
        background: linear-gradient(135deg, var(--login-green) 0%, #1e8f4a 100%);
        color: white;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-align: center;
        padding: 1.15rem 1rem;
        font-size: 1.05rem;
    }
    .login-page .card-body {
        padding: 1.5rem 1.35rem 1.75rem;
        background: linear-gradient(180deg, #fafdfb 0%, #fff 48%);
    }
    .login-page .login-segment {
        background: var(--login-green-soft);
        border: 1px solid var(--login-green-border);
        border-radius: 12px;
        padding: 4px;
    }
    .login-page .login-segment .nav-link {
        color: #495057;
        border-radius: 10px !important;
        padding: 0.65rem 0.75rem;
        font-size: 0.9rem;
        font-weight: 500;
        border: none;
        transition: background 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
    }
    .login-page .login-segment .nav-link:hover:not(.active) {
        background: rgba(255, 255, 255, 0.65);
        color: var(--login-green);
    }
    .login-page .login-segment .nav-link.active {
        color: var(--login-green);
        background: #fff;
        box-shadow: 0 2px 8px rgba(24, 116, 60, 0.12);
        font-weight: 600;
    }
    .login-page .btn-primary {
        background-color: var(--login-green);
        border-color: var(--login-green);
        padding: 0.55rem 1.25rem;
        font-weight: 600;
        border-radius: 10px;
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }
    .login-page .btn-primary:hover:not(:disabled) {
        background-color: var(--login-green-dark);
        border-color: var(--login-green-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(24, 116, 60, 0.25);
    }
    .login-page .btn-primary:disabled {
        opacity: 0.75;
        cursor: not-allowed;
    }
    .login-page .btn-outline-primary {
        color: var(--login-green);
        border-color: var(--login-green);
        border-radius: 10px;
        font-weight: 600;
        padding: 0.5rem 1rem;
    }
    .login-page .btn-outline-primary:hover:not(:disabled) {
        background-color: var(--login-green);
        border-color: var(--login-green);
    }
    .login-page .form-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #374151;
        margin-bottom: 0.4rem;
    }
    .login-page .form-control {
        border-radius: 10px;
        padding: 0.65rem 0.85rem;
        border-color: #dee2e6;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .login-page .form-control:focus {
        border-color: var(--login-green);
        box-shadow: 0 0 0 0.2rem rgba(24, 116, 60, 0.15);
    }
    .login-page .form-check-input:checked {
        background-color: var(--login-green);
        border-color: var(--login-green);
    }
    /* Icon boxes: distinct panels (match left addon + right toggle) */
    .login-page .login-input-wrap {
        border-radius: 10px;
        align-items: stretch;
    }
    .login-page .login-input-wrap .input-group-text {
        background-color: #eef1f4 !important;
        color: #495057;
        border: 1px solid #ced4da;
        border-right: 0;
        min-width: 3.15rem;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-left: 0.9rem;
        padding-right: 0.9rem;
    }
    .login-page .login-input-wrap--start-only .input-group-text {
        border-radius: 10px 0 0 10px;
    }
    .login-page .login-input-wrap--password .input-group-text {
        border-radius: 10px 0 0 10px;
    }
    .login-page .login-input-wrap .form-control {
        border: 1px solid #ced4da;
        border-left: 0;
        border-right: 0;
        box-shadow: none;
        border-radius: 0;
    }
    .login-page .login-input-wrap--start-only .form-control {
        border-right: 1px solid #ced4da;
        border-radius: 0 10px 10px 0;
    }
    .login-page .login-input-wrap:focus-within {
        box-shadow: 0 0 0 0.2rem rgba(24, 116, 60, 0.18);
        border-radius: 10px;
    }
    .login-page .login-input-wrap:focus-within .input-group-text,
    .login-page .login-input-wrap:focus-within .form-control,
    .login-page .login-input-wrap:focus-within .password-toggle-btn {
        border-color: var(--login-green);
    }
    .login-page .login-input-wrap .form-control:focus {
        box-shadow: none;
        z-index: 2;
    }
    .login-page .login-input-wrap .password-toggle-btn {
        background-color: #eef1f4;
        color: #495057;
        border: 1px solid #ced4da;
        border-left: 0;
        min-width: 3.15rem;
        border-radius: 0 10px 10px 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .login-page .login-input-wrap .password-toggle-btn:hover,
    .login-page .login-input-wrap .password-toggle-btn:focus {
        background-color: #e4e9ee;
        border-color: var(--login-green);
        color: var(--login-green);
        z-index: 2;
    }
    .login-page .login-input-wrap:has(.form-control.is-invalid) .input-group-text,
    .login-page .login-input-wrap:has(.form-control.is-invalid) .form-control,
    .login-page .login-input-wrap:has(.form-control.is-invalid) .password-toggle-btn {
        border-color: #dc3545;
    }
    .login-page .login-input-wrap:has(.form-control.is-invalid):focus-within {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.2);
    }
    .login-page .input-group .form-control.is-invalid {
        z-index: 0;
    }
    /* Font Awesome in input addons — consistent size & alignment */
    .login-page .input-group-text i.fas {
        width: 1.125rem;
        text-align: center;
        opacity: 0.85;
    }
    /* Min height set from measured email tab (see script) so both options match the taller email stack */
    .login-page .login-tab-panels {
        min-height: 0;
    }
    .login-page .forgot-link {
        color: var(--login-green);
        font-size: 0.875rem;
        font-weight: 500;
    }
    .login-page .forgot-link:hover {
        color: var(--login-green-dark);
        text-decoration: underline !important;
    }
    .login-page .login-intro {
        font-size: 0.9rem;
        line-height: 1.5;
        color: #5c6670;
    }
    .login-page .step-badge {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .login-page .step-badge.done {
        background: var(--login-green);
        color: #fff;
    }
    .login-page .step-badge.pending {
        background: #e9ecef;
        color: #6c757d;
    }
    .login-page .otp-panel {
        border: 1px dashed var(--login-green-border);
        border-radius: 12px;
        background: #fff;
        padding: 1rem 1rem 1.1rem;
    }
    .login-page .otp-input {
        font-size: 1.35rem;
        font-weight: 600;
        letter-spacing: 0.4em;
        text-align: center;
        font-variant-numeric: tabular-nums;
        padding-left: 1rem;
        padding-right: 1rem;
    }
    .login-page .alert-success {
        border: none;
        border-left: 4px solid var(--login-green);
        border-radius: 10px;
        background: #e8f5ec;
        color: #155f32;
    }
    .login-page .spin {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        animation: login-spin 0.7s linear infinite;
        vertical-align: -0.125em;
    }
    .login-page .btn-primary .spin {
        border: 2px solid rgba(255, 255, 255, 0.35);
        border-top-color: #fff;
    }
    .login-page .btn-outline-primary .spin {
        border: 2px solid rgba(24, 116, 60, 0.2);
        border-top-color: #18743c;
    }
    @keyframes login-spin {
        to { transform: rotate(360deg); }
    }
</style>

@php
    $showOtpTab = $errors->has('phone') || $errors->has('otp') || session('login_otp_phone') || session('phone_otp_sent');
@endphp

<div class="login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-50 py-4">
            <div class="col-12">
                <div class="card auth-card shadow">
                    <div class="auth-header">
                        <i class="fas fa-shield-alt me-2" aria-hidden="true"></i> {{ __('Welcome back') }}
                    </div>

                    <div class="card-body">
                        <div class="login-segment mb-4">
                            <ul class="nav nav-pills nav-justified" id="loginTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link w-100 {{ $showOtpTab ? '' : 'active' }}" id="login-tab-email"
                                        data-bs-toggle="tab" data-bs-target="#login-pane-email" type="button" role="tab"
                                        aria-controls="login-pane-email" aria-selected="{{ $showOtpTab ? 'false' : 'true' }}">
                                        <i class="fas fa-envelope me-1" aria-hidden="true"></i> {{ __('Email') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link w-100 {{ $showOtpTab ? 'active' : '' }}" id="login-tab-otp"
                                        data-bs-toggle="tab" data-bs-target="#login-pane-otp" type="button" role="tab"
                                        aria-controls="login-pane-otp" aria-selected="{{ $showOtpTab ? 'true' : 'false' }}">
                                        <i class="fas fa-mobile-alt me-1" aria-hidden="true"></i> {{ __('OTP') }}
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content login-tab-panels" id="loginTabsContent">
                            <div class="tab-pane fade {{ $showOtpTab ? '' : 'show active' }}" id="login-pane-email" role="tabpanel"
                                aria-labelledby="login-tab-email" tabindex="0">
                                <p class="login-intro mb-4">{{ __('Sign in with the email and password for your account.') }}</p>

                                <form method="POST" action="{{ route('login') }}" class="js-login-form" data-loading-label="{{ __('Signing in…') }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="email" class="form-label">{{ __('Email address') }}</label>
                                        <div class="input-group input-group-lg login-input-wrap login-input-wrap--start-only has-validation">
                                            <span class="input-group-text" aria-hidden="true">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input id="email" type="email"
                                                class="form-control shadow-none @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email') }}" required autocomplete="email"
                                                placeholder="you@example.com"
                                                @if (! $showOtpTab) autofocus @endif>
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">{{ __('Password') }}</label>
                                        <div class="input-group input-group-lg login-input-wrap login-input-wrap--password has-validation">
                                            <span class="input-group-text" aria-hidden="true">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input id="password" type="password"
                                                class="form-control shadow-none @error('password') is-invalid @enderror"
                                                name="password" required autocomplete="current-password"
                                                placeholder="••••••••"
                                                aria-describedby="passwordToggle">
                                            <button type="button" class="btn password-toggle-btn px-3" id="passwordToggle"
                                                title="{{ __('Show password') }}" aria-label="{{ __('Show password') }}"
                                                aria-pressed="false" tabindex="0">
                                                <i class="fas fa-eye" id="passwordToggleIcon" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                                {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="remember">{{ __('Remember me') }}</label>
                                        </div>
                                        @if (Route::has('password.request'))
                                            <a class="forgot-link text-decoration-none" href="{{ route('password.request') }}">
                                                {{ __('Forgot password?') }}
                                            </a>
                                        @endif
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-2 mb-0 js-login-submit">
                                        <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>
                                        <span class="js-login-submit-text">{{ __('Sign in') }}</span>
                                    </button>
                                    @if (Route::has('register'))
                                        <p class="text-center small text-muted mt-3 mb-0">
                                            {{ __("Don't have an account?") }}
                                            <a href="{{ route('register') }}" class="forgot-link text-decoration-none">{{ __('Sign up') }}</a>
                                        </p>
                                    @endif
                                </form>
                            </div>

                            <div class="tab-pane fade {{ $showOtpTab ? 'show active' : '' }}" id="login-pane-otp" role="tabpanel"
                                aria-labelledby="login-tab-otp" tabindex="0">
                                <p class="login-intro mb-3">{{ __('Use the mobile number linked to your account. We will text you a one-time code.') }}</p>

                                @if (session('phone_otp_sent'))
                                    <div class="alert alert-success small py-2 px-3 mb-4 d-flex align-items-start gap-2" role="alert">
                                        <i class="fas fa-check-circle flex-shrink-0 mt-1" aria-hidden="true"></i>
                                        <span>{{ __('Verification code sent. Check your phone for the SMS.') }}</span>
                                    </div>
                                @endif

                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="step-badge {{ session('login_otp_phone') ? 'done' : 'pending' }}">1</span>
                                    <span class="small fw-semibold text-secondary">{{ __('Phone number') }}</span>
                                </div>
                                <form method="POST" action="{{ route('login.phone.send-otp') }}" class="js-login-form mb-4" data-loading-label="{{ __('Sending…') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="login_phone" class="form-label visually-hidden">{{ __('Phone number') }}</label>
                                        <div class="input-group input-group-lg login-input-wrap login-input-wrap--start-only has-validation">
                                            <span class="input-group-text" aria-hidden="true">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                            <input id="login_phone" type="tel" name="phone" inputmode="tel" autocomplete="tel"
                                                class="form-control shadow-none @error('phone') is-invalid @enderror"
                                                value="{{ old('phone') }}"
                                                placeholder="{{ __('e.g. 07XX XXX XXX') }}"
                                                @if ($showOtpTab) autofocus @endif>
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary w-100 py-2 js-login-submit">
                                        <i class="fas fa-paper-plane me-2" aria-hidden="true"></i>
                                        <span class="js-login-submit-text">{{ __('Send verification code') }}</span>
                                    </button>
                                    @if (Route::has('register'))
                                        <p class="text-center small text-muted mt-3 mb-0">
                                            {{ __("Need an account first?") }}
                                            <a href="{{ route('register') }}" class="forgot-link text-decoration-none">{{ __('Sign up') }}</a>
                                        </p>
                                    @endif
                                </form>

                                @if (session('login_otp_phone'))
                                    <div class="d-flex align-items-center gap-2 mb-2 mt-2">
                                        <span class="step-badge done">2</span>
                                        <span class="small fw-semibold text-secondary">{{ __('Enter code') }}</span>
                                    </div>
                                    <div class="otp-panel">
                                        <form method="POST" action="{{ route('login.phone') }}" class="js-login-form" data-loading-label="{{ __('Verifying…') }}">
                                            @csrf
                                            <label for="login_otp_code" class="form-label">{{ __('6-digit code') }}</label>
                                            <input id="login_otp_code" type="text" name="otp" maxlength="6" inputmode="numeric"
                                                pattern="[0-9]*"
                                                class="form-control otp-input @error('otp') is-invalid @enderror"
                                                placeholder="••••••"
                                                autocomplete="one-time-code"
                                                aria-label="{{ __('Six digit verification code') }}"
                                                required>
                                            @error('otp')
                                                <div class="invalid-feedback d-block text-center">{{ $message }}</div>
                                            @enderror
                                            <p class="text-center small text-muted mt-2 mb-3">
                                                {{ __('Sent to') }}
                                                <span class="fw-semibold text-dark">***{{ substr((string) session('login_otp_phone'), -3) }}</span>
                                            </p>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="remember" id="remember_phone_otp"
                                                    value="1" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="remember_phone_otp">{{ __('Remember me') }}</label>
                                            </div>
                                            <div class="d-grid gap-2">
                                                <button type="submit" class="btn btn-primary py-2 js-login-submit">
                                                    <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>
                                                    <span class="js-login-submit-text">{{ __('Verify and sign in') }}</span>
                                                </button>
                                                <a href="{{ route('login.phone.cancel') }}" class="btn btn-link btn-sm text-decoration-none text-secondary py-1">
                                                    {{ __('Use a different number') }}
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var emailPane = document.getElementById('login-pane-email');
    var tabPanels = document.querySelector('.login-tab-panels');
    if (emailPane && tabPanels) {
        function applyLoginTabMinHeightFromEmailPane() {
            var clone = emailPane.cloneNode(true);
            clone.removeAttribute('id');
            clone.classList.add('show', 'active');
            clone.setAttribute('aria-hidden', 'true');
            var w = tabPanels.getBoundingClientRect().width || tabPanels.offsetWidth;
            if (w < 1) {
                w = 400;
            }
            clone.style.cssText = 'position:absolute;left:-9999px;top:0;display:block;visibility:hidden;width:' + w + 'px;pointer-events:none';
            document.body.appendChild(clone);
            var h = clone.offsetHeight;
            document.body.removeChild(clone);
            if (h > 0) {
                tabPanels.style.minHeight = Math.ceil(h) + 'px';
            }
        }
        applyLoginTabMinHeightFromEmailPane();
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(applyLoginTabMinHeightFromEmailPane, 150);
        });
    }

    var btn = document.getElementById('passwordToggle');
    var input = document.getElementById('password');
    var icon = document.getElementById('passwordToggleIcon');
    if (btn && input && icon) {
        function setVisible(visible) {
            input.type = visible ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !visible);
            icon.classList.toggle('fa-eye-slash', visible);
            var showLabel = @json(__('Show password'));
            var hideLabel = @json(__('Hide password'));
            btn.setAttribute('aria-label', visible ? hideLabel : showLabel);
            btn.setAttribute('title', visible ? hideLabel : showLabel);
            btn.setAttribute('aria-pressed', visible ? 'true' : 'false');
        }
        btn.addEventListener('click', function () {
            setVisible(input.type === 'password');
        });
    }

    document.querySelectorAll('.js-login-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            var label = form.getAttribute('data-loading-label') || @json(__('Please wait…'));
            var submit = form.querySelector('.js-login-submit');
            var textEl = form.querySelector('.js-login-submit-text');
            if (!submit || submit.disabled) return;
            submit.disabled = true;
            if (textEl) {
                textEl.textContent = label;
                var spin = document.createElement('span');
                spin.className = 'spin ms-2';
                spin.setAttribute('aria-hidden', 'true');
                submit.appendChild(spin);
            }
        });
    });

    var otpInput = document.getElementById('login_otp_code');
    if (otpInput) {
        otpInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    }
});
</script>
@endpush
