@extends('process.master')

@section('content')
<style>
    .register-page {
        --reg-green: #18743c;
        --reg-green-dark: #155f32;
        --reg-green-soft: rgba(24, 116, 60, 0.08);
        --reg-green-border: rgba(24, 116, 60, 0.2);
    }
    .register-page .min-vh-50 {
        min-height: calc(100vh - 220px);
    }
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
        border-right: 0;
        min-width: 3.15rem;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-left: 0.9rem;
        padding-right: 0.9rem;
    }
    .register-page .input-group .form-control {
        border-left: 0;
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
</style>

<div class="register-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-50 py-4">
            <div class="col-12">
                <div class="card auth-card shadow">
                    <div class="auth-header">
                        <i class="fas fa-user-plus me-2" aria-hidden="true"></i> {{ __('Create your account') }}
                    </div>

                    <div class="card-body">
                        <p class="auth-intro mb-4">{{ __('Sign up to start secure escrow transactions with eConfirm.') }}</p>
                        <form method="POST" action="{{ route('register') }}">
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

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-user-plus me-2" aria-hidden="true"></i>
                                {{ __('Create account') }}
                            </button>

                            <p class="text-center small text-muted mt-3 mb-0">
                                {{ __('Already have an account?') }}
                                <a href="{{ route('login') }}" class="forgot-link text-decoration-none">{{ __('Sign in') }}</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
