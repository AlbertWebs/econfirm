@extends('process.auth-master')

@section('title', __('Developer sign in'))

@section('content')
<style>
    .dev-login-page {
        --login-green: #18743c;
        --login-green-dark: #155f32;
        --login-green-border: rgba(24, 116, 60, 0.2);
        --auth-fixed-width: 440px;
    }
    .dev-login-page .auth-wrap {
        width: var(--auth-fixed-width);
        max-width: calc(100vw - 2rem);
        margin-inline: auto;
    }
    .dev-login-page .auth-card {
        border: 1px solid var(--login-green-border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(24, 116, 60, 0.12), 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .dev-login-page .auth-header {
        background: linear-gradient(135deg, var(--login-green) 0%, #1e8f4a 100%);
        color: white;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-align: center;
        padding: 1.15rem 1rem;
        font-size: 1.05rem;
    }
    .dev-login-page .card-body {
        padding: 1.5rem 1.35rem 1.75rem;
        background: linear-gradient(180deg, #fafdfb 0%, #fff 48%);
    }
    .dev-login-page .form-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #374151;
        margin-bottom: 0.4rem;
    }
    /* Icon + input as separate tiles (not a fused input-group). */
    .dev-login-page .dev-login-field {
        display: flex;
        align-items: stretch;
        gap: 0.65rem;
        width: 100%;
    }
    .dev-login-page .dev-login-field__icon {
        flex: 0 0 auto;
        width: 3.25rem;
        min-height: 3.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1.05rem;
    }
    .dev-login-page .dev-login-field__control {
        flex: 1 1 auto;
        min-width: 0;
        border-radius: 12px !important;
        padding: 0.7rem 1rem;
        border: 1px solid #ced4da;
        font-size: 1rem;
    }
    .dev-login-page .dev-login-field__toggle {
        flex: 0 0 auto;
        width: 3.25rem;
        min-height: 3.25rem;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }
    .dev-login-page .dev-login-field__toggle:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .dev-login-page .dev-login-field:focus-within .dev-login-field__icon,
    .dev-login-page .dev-login-field:focus-within .dev-login-field__control,
    .dev-login-page .dev-login-field:focus-within .dev-login-field__toggle {
        border-color: var(--login-green);
        box-shadow: 0 0 0 1px rgba(24, 116, 60, 0.12);
    }
    .dev-login-page .dev-login-field:focus-within .dev-login-field__control {
        box-shadow: 0 0 0 0.2rem rgba(24, 116, 60, 0.12);
    }
    .dev-login-page .dev-login-field .dev-login-field__control:focus {
        border-color: var(--login-green);
        box-shadow: 0 0 0 0.2rem rgba(24, 116, 60, 0.15);
        outline: 0;
    }
    .dev-login-page .dev-login-field.is-invalid .dev-login-field__icon,
    .dev-login-page .dev-login-field.is-invalid .dev-login-field__control,
    .dev-login-page .dev-login-field.is-invalid .dev-login-field__toggle {
        border-color: #dc3545;
    }
    .dev-login-page .btn-primary {
        background-color: var(--login-green);
        border-color: var(--login-green);
        padding: 0.55rem 1.25rem;
        font-weight: 600;
        border-radius: 10px;
    }
    .dev-login-page .btn-primary:hover:not(:disabled) {
        background-color: var(--login-green-dark);
        border-color: var(--login-green-dark);
    }
    .dev-login-page .forgot-link {
        color: var(--login-green);
        font-size: 0.875rem;
        font-weight: 500;
    }
    .dev-login-page .forgot-link:hover {
        color: var(--login-green-dark);
    }
    .dev-login-page .login-intro {
        font-size: 0.9rem;
        line-height: 1.5;
        color: #5c6670;
    }
    .dev-login-page .spin {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        animation: dev-login-spin 0.7s linear infinite;
        vertical-align: -0.125em;
        border: 2px solid rgba(255, 255, 255, 0.35);
        border-top-color: #fff;
    }
    @keyframes dev-login-spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="dev-login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-3">
            <div class="col-12">
                <div class="auth-wrap">
                    <div class="text-center mb-3">
                        <a href="{{ route('home') }}" class="d-inline-flex align-items-center justify-content-center" aria-label="{{ __('Go to homepage') }}">
                            <img src="{{ asset('uploads/logo-hoz.png') }}" alt="eConfirm" style="height: 46px; width: auto; object-fit: contain;">
                        </a>
                    </div>
                    <div class="card auth-card shadow">
                        <div class="auth-header">
                            <i class="fas fa-code me-2" aria-hidden="true"></i> {{ __('API developer sign in') }}
                        </div>
                        <div class="card-body">
                            <p class="login-intro mb-4">{{ __('Use the email and password for your API developer account. After signing in you will be taken to your keys and API URLs.') }}</p>

                            @if (session('error'))
                                <div class="alert alert-danger small py-2 px-3 mb-3" role="alert">{{ session('error') }}</div>
                            @endif

                            <form method="POST" action="{{ route('developer.login.submit') }}" class="js-login-form" data-loading-label="{{ __('Signing in…') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('Email address') }}</label>
                                    <div class="dev-login-field @error('email') is-invalid @enderror">
                                        <span class="dev-login-field__icon" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                                        <input id="email" type="email"
                                            class="form-control shadow-none dev-login-field__control @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}" required autocomplete="email"
                                            placeholder="you@example.com" autofocus>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">{{ __('Password') }}</label>
                                    <div class="dev-login-field @error('password') is-invalid @enderror">
                                        <span class="dev-login-field__icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
                                        <input id="password" type="password"
                                            class="form-control shadow-none dev-login-field__control @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="current-password"
                                            placeholder="••••••••" aria-describedby="passwordToggle">
                                        <button type="button" class="btn dev-login-field__toggle" id="passwordToggle"
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
                                        <a class="forgot-link text-decoration-none" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 mb-3 js-login-submit">
                                    <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>
                                    <span class="js-login-submit-text">{{ __('Sign in') }}</span>
                                </button>
                            </form>

                            <p class="text-center small text-muted mb-0 px-1">
                                <a href="{{ route('login') }}" class="forgot-link text-decoration-none">{{ __('Customer sign in') }}</a>
                                @if (Route::has('developer.register'))
                                    <span class="text-muted"> · </span>
                                    <a href="{{ route('developer.register') }}" class="forgot-link text-decoration-none">{{ __('Sign up') }}</a>
                                @elseif (Route::has('register'))
                                    <span class="text-muted"> · </span>
                                    <a href="{{ route('register') }}" class="forgot-link text-decoration-none">{{ __('Sign up') }}</a>
                                @endif
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
});
</script>
@endpush
