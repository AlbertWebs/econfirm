@extends('process.auth-master')

@section('content')
<style>
    .register-page {
        --reg-green: #18743c;
        --reg-green-dark: #155f32;
        --reg-green-soft: rgba(24, 116, 60, 0.08);
        --reg-green-border: rgba(24, 116, 60, 0.2);
        --auth-fixed-width: 440px;
    }
    .register-page .min-vh-50 {
        min-height: 100vh;
    }
    .register-page .auth-wrap {
        width: var(--auth-fixed-width);
        max-width: calc(100vw - 2rem);
        margin-inline: auto;
    }
    .register-page .auth-card {
        width: 100%;
    }
    .register-page .auth-segment,
    .register-page #registerTabsContent,
    .register-page #registerTabsContent .tab-pane {
        width: 100% !important;
    }
    .register-page .auth-segment .nav-item {
        flex: 1 1 0;
        min-width: 0;
    }
    .register-page #registerTabsContent {
        width: 100%;
    }
    .register-page #registerTabsContent .tab-pane { width: 100%; }
    .register-page .auth-card {
        border: 1px solid var(--reg-green-border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(24, 116, 60, 0.12), 0 4px 12px rgba(0, 0, 0, 0.06);
        max-width: 480px;
        margin-left: auto;
        margin-right: auto;
    }
    .register-page .auth-header {
        background: linear-gradient(135deg, var(--reg-green) 0%, #1e8f4a 100%);
        color: white;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-align: center;
        padding: 1.15rem 1rem;
        font-size: 1.05rem;
    }
    .register-page .card-body {
        padding: 1.5rem 1.35rem 1.75rem;
        background: linear-gradient(180deg, #fafdfb 0%, #fff 48%);
    }
    .register-page .auth-intro {
        font-size: 0.9rem;
        line-height: 1.5;
        color: #5c6670;
    }
    .register-page .form-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #374151;
        margin-bottom: 0.4rem;
    }
    .register-page .btn-primary {
        background-color: var(--reg-green);
        border-color: var(--reg-green);
        padding: 0.55rem 1.25rem;
        font-weight: 600;
        border-radius: 10px;
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }
    .register-page .btn-primary:hover:not(:disabled) {
        background-color: var(--reg-green-dark);
        border-color: var(--reg-green-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(24, 116, 60, 0.25);
    }
    .register-page .form-control {
        border-radius: 10px;
        padding: 0.65rem 0.85rem;
        border-color: #dee2e6;
    }
    .register-page .form-control:focus {
        border-color: var(--reg-green);
        box-shadow: 0 0 0 0.2rem rgba(24, 116, 60, 0.15);
    }
    .register-page .input-group-text {
        background-color: #eef1f4 !important;
        color: #495057;
        border: 1px solid #ced4da;
        min-width: 3.15rem;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-left: 0.9rem;
        padding-right: 0.9rem;
        border-radius: 10px !important;
    }
    .register-page .input-group {
        gap: 8px;
    }
    .register-page .input-group .form-control {
        border-left: 1px solid #ced4da;
        border-radius: 10px !important;
    }
    .register-page .input-group > .form-control,
    .register-page .input-group > .form-control:not(:first-child),
    .register-page .input-group > .form-control:not(:last-child) {
        border-radius: 10px !important;
    }
    .register-page .input-group:focus-within .input-group-text,
    .register-page .input-group:focus-within .form-control {
        border-color: var(--reg-green);
    }
    .register-page .forgot-link {
        color: var(--reg-green);
        font-size: 0.875rem;
        font-weight: 500;
    }
    .register-page .forgot-link:hover {
        color: var(--reg-green-dark);
        text-decoration: underline !important;
    }
    .register-page .auth-segment {
        background: var(--reg-green-soft);
        border: 1px solid var(--reg-green-border);
        border-radius: 12px;
        padding: 4px;
    }
    .register-page .auth-segment .nav {
        gap: 6px;
    }
    .register-page .auth-segment .nav-link {
        color: #495057;
        border-radius: 10px !important;
        padding: 0.65rem 0.75rem;
        font-size: 0.9rem;
        font-weight: 500;
        border: none;
        transition: background 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
    }
    .register-page .auth-segment .nav-link:hover:not(.active) {
        background: rgba(255, 255, 255, 0.65);
        color: var(--reg-green);
    }
    .register-page .auth-segment .nav-link.active {
        color: var(--reg-green);
        background: #fff;
        box-shadow: 0 2px 8px rgba(24, 116, 60, 0.12);
        font-weight: 600;
    }
    .register-page .step-badge {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .register-page .step-badge.done {
        background: var(--reg-green);
        color: #fff;
    }
    .register-page .step-badge.pending {
        background: #e9ecef;
        color: #6c757d;
    }
    .register-page .otp-panel {
        border: 1px dashed var(--reg-green-border);
        border-radius: 12px;
        background: #fff;
        padding: 1rem;
    }
    .register-page .otp-input {
        font-size: 1.35rem;
        font-weight: 600;
        letter-spacing: 0.4em;
        text-align: center;
        font-variant-numeric: tabular-nums;
        padding-left: 1rem;
        padding-right: 1rem;
        width: 100%;
        max-width: 100%;
    }
    .register-page .alert-success {
        border: none;
        border-left: 4px solid var(--reg-green);
        border-radius: 10px;
        background: #e8f5ec;
        color: #155f32;
    }
    .register-page .btn-outline-primary {
        color: var(--reg-green);
        border-color: var(--reg-green);
        border-radius: 10px;
        font-weight: 600;
        padding: 0.5rem 1rem;
    }
    .register-page .btn-outline-primary:hover:not(:disabled) {
        background-color: var(--reg-green);
        border-color: var(--reg-green);
    }
    .register-page .spin {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        animation: register-spin 0.7s linear infinite;
        vertical-align: -0.125em;
    }
    .register-page .btn-primary .spin {
        border: 2px solid rgba(255, 255, 255, 0.35);
        border-top-color: #fff;
    }
    .register-page .btn-outline-primary .spin {
        border: 2px solid rgba(24, 116, 60, 0.2);
        border-top-color: #18743c;
    }
    @keyframes register-spin {
        to { transform: rotate(360deg); }
    }
    @media (max-height: 860px) {
        .register-page .auth-header {
            padding: 0.9rem 0.9rem;
            font-size: 1rem;
        }
        .register-page .card-body {
            padding: 1.1rem 1rem 1.15rem;
        }
        .register-page .mb-4 {
            margin-bottom: 0.8rem !important;
        }
        .register-page .mb-3 {
            margin-bottom: 0.65rem !important;
        }
        .register-page .form-control {
            padding: 0.55rem 0.75rem;
            font-size: 0.92rem;
        }
        .register-page .btn {
            padding-top: 0.48rem !important;
            padding-bottom: 0.48rem !important;
            font-size: 0.9rem;
        }
        .register-page .otp-panel {
            padding: 0.75rem;
        }
    }
    @media (max-height: 760px) {
        .register-page .auth-wrap {
            transform: scale(0.92);
            transform-origin: top center;
        }
        .register-page .min-vh-50 {
            align-items: flex-start !important;
        }
    }
</style>

@php
    $showOtpTab = $errors->has('phone') || $errors->has('otp') || session('register_otp_phone') || session('register_otp_sent');
@endphp

<div class="register-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-50 py-2">
            <div class="col-12">
                <div class="auth-wrap">
                <div class="text-center mb-3">
                    <a href="{{ route('home') }}" class="d-inline-flex align-items-center justify-content-center" aria-label="Go to homepage">
                        <img src="{{ asset('uploads/logo-hoz.png') }}" alt="eConfirm" style="height: 46px; width: auto; object-fit: contain;">
                    </a>
                </div>
                <div class="card auth-card shadow">
                    <div class="auth-header">
                        <i class="fas fa-user-plus me-2" aria-hidden="true"></i> {{ __('Create your account') }}
                    </div>

                    <div class="card-body">
                        <div class="auth-segment mb-4">
                            <ul class="nav nav-pills nav-justified" id="registerTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link w-100 {{ $showOtpTab ? '' : 'active' }}" id="register-tab-email"
                                        data-bs-toggle="tab" data-bs-target="#register-pane-email" type="button" role="tab"
                                        aria-controls="register-pane-email" aria-selected="{{ $showOtpTab ? 'false' : 'true' }}">
                                        <i class="fas fa-envelope me-1" aria-hidden="true"></i> {{ __('Email') }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link w-100 {{ $showOtpTab ? 'active' : '' }}" id="register-tab-otp"
                                        data-bs-toggle="tab" data-bs-target="#register-pane-otp" type="button" role="tab"
                                        aria-controls="register-pane-otp" aria-selected="{{ $showOtpTab ? 'true' : 'false' }}">
                                        <i class="fas fa-mobile-alt me-1" aria-hidden="true"></i> {{ __('OTP') }}
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content" id="registerTabsContent">
                            <div class="tab-pane fade {{ $showOtpTab ? '' : 'show active' }}" id="register-pane-email" role="tabpanel"
                                aria-labelledby="register-tab-email" tabindex="0">
                                <p class="auth-intro mb-4">{{ __('Sign up with your email and a password. Use OTP if you prefer a phone-first flow.') }}</p>
                                <form method="POST" action="{{ route('register') }}" class="js-register-form" data-loading-label="{{ __('Creating account…') }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="name" class="form-label">{{ __('Full name') }}</label>
                                        <div class="input-group input-group-lg has-validation">
                                            <span class="input-group-text" aria-hidden="true"><i class="fas fa-user"></i></span>
                                            <input id="name" type="text" class="form-control shadow-none @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="John Doe">
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">{{ __('Email address') }}</label>
                                        <div class="input-group input-group-lg has-validation">
                                            <span class="input-group-text" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                                            <input id="email" type="email" class="form-control shadow-none @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com">
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">{{ __('Password') }}</label>
                                        <div class="input-group input-group-lg has-validation">
                                            <span class="input-group-text" aria-hidden="true"><i class="fas fa-lock"></i></span>
                                            <input id="password" type="password" class="form-control shadow-none @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Create a strong password">
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="password-confirm" class="form-label">{{ __('Confirm password') }}</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text" aria-hidden="true"><i class="fas fa-check-circle"></i></span>
                                            <input id="password-confirm" type="password" class="form-control shadow-none" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-2 js-register-submit">
                                        <i class="fas fa-user-plus me-2" aria-hidden="true"></i>
                                        <span class="js-register-submit-text">{{ __('Create account') }}</span>
                                    </button>
                                </form>
                            </div>

                            <div class="tab-pane fade {{ $showOtpTab ? 'show active' : '' }}" id="register-pane-otp" role="tabpanel"
                                aria-labelledby="register-tab-otp" tabindex="0">
                                <p class="auth-intro mb-3">{{ __('Create your account with phone OTP. We will verify your number and sign you in instantly.') }}</p>

                                @if (session('register_otp_sent'))
                                    <div class="alert alert-success small py-2 px-3 mb-4 d-flex align-items-start gap-2" role="alert">
                                        <i class="fas fa-check-circle flex-shrink-0 mt-1" aria-hidden="true"></i>
                                        <span>{{ __('Verification code sent. Check your phone for the SMS.') }}</span>
                                    </div>
                                @endif

                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="step-badge {{ session('register_otp_phone') ? 'done' : 'pending' }}">1</span>
                                    <span class="small fw-semibold text-secondary">{{ __('Your details') }}</span>
                                </div>
                                <form method="POST" action="{{ route('register.phone.send-otp') }}" class="js-register-form mb-4" data-loading-label="{{ __('Sending…') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="register_otp_name" class="form-label">{{ __('Full name') }}</label>
                                        <div class="input-group input-group-lg has-validation">
                                            <span class="input-group-text" aria-hidden="true"><i class="fas fa-user"></i></span>
                                            <input id="register_otp_name" type="text" name="name" autocomplete="name"
                                                class="form-control shadow-none @error('name') is-invalid @enderror"
                                                value="{{ old('name', (string) session('register_otp_name', '')) }}"
                                                placeholder="John Doe"
                                                @if ($showOtpTab) autofocus @endif
                                                required>
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="register_phone" class="form-label">{{ __('Phone number') }}</label>
                                        <div class="input-group input-group-lg has-validation">
                                            <span class="input-group-text" aria-hidden="true"><i class="fas fa-phone"></i></span>
                                            <input id="register_phone" type="tel" name="phone" inputmode="tel" autocomplete="tel"
                                                class="form-control shadow-none @error('phone') is-invalid @enderror"
                                                value="{{ old('phone', (string) session('register_otp_phone', '')) }}"
                                                placeholder="{{ __('e.g. 07XX XXX XXX') }}"
                                                required>
                                        </div>
                                        @error('phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary w-100 py-2 js-register-submit">
                                        <i class="fas fa-paper-plane me-2" aria-hidden="true"></i>
                                        <span class="js-register-submit-text">{{ __('Send verification code') }}</span>
                                    </button>
                                </form>

                                @if (session('register_otp_phone'))
                                    <div class="d-flex align-items-center gap-2 mb-2 mt-2">
                                        <span class="step-badge done">2</span>
                                        <span class="small fw-semibold text-secondary">{{ __('Verify OTP') }}</span>
                                    </div>
                                    <div class="otp-panel">
                                        <form method="POST" action="{{ route('register.phone') }}" class="js-register-form" data-loading-label="{{ __('Verifying…') }}">
                                            @csrf
                                            <label for="register_otp_code" class="form-label">{{ __('6-digit code') }}</label>
                                            <input id="register_otp_code" type="text" name="otp" maxlength="6" inputmode="numeric"
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
                                                <span class="fw-semibold text-dark">***{{ substr((string) session('register_otp_phone'), -3) }}</span>
                                            </p>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="remember" id="remember_phone_register"
                                                    value="1" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="remember_phone_register">{{ __('Remember me') }}</label>
                                            </div>
                                            <div class="d-grid gap-2">
                                                <button type="submit" class="btn btn-primary py-2 js-register-submit">
                                                    <i class="fas fa-user-check me-2" aria-hidden="true"></i>
                                                    <span class="js-register-submit-text">{{ __('Verify and create account') }}</span>
                                                </button>
                                                <a href="{{ route('register.phone.cancel') }}" class="btn btn-link btn-sm text-decoration-none text-secondary py-1">
                                                    {{ __('Use a different number') }}
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="text-center small text-muted mt-3 mb-0">
                            {{ __('Already have an account?') }}
                            <a href="{{ route('login') }}" class="forgot-link text-decoration-none">{{ __('Sign in') }}</a>
                        </p>
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
    document.querySelectorAll('.js-register-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            var label = form.getAttribute('data-loading-label') || @json(__('Please wait…'));
            var submit = form.querySelector('.js-register-submit');
            var textEl = form.querySelector('.js-register-submit-text');
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

    var otpInput = document.getElementById('register_otp_code');
    if (otpInput) {
        otpInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    }
});
</script>
@endpush
