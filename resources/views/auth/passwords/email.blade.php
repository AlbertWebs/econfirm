@extends('process.master')

@section('title', __('Forgot password') . ' | e-confirm Portal')

@section('content')
<style>
    .auth-card {
        border: 1px solid #18743c;
        border-radius: 10px;
    }
    .auth-header {
        background-color: #18743c;
        color: white;
        font-weight: bold;
        text-align: center;
        padding: 1rem;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
    .btn-primary {
        background-color: #18743c;
        border-color: #18743c;
    }
    .btn-primary:hover {
        background-color: #155f32;
        border-color: #155f32;
    }
    .min-vh-50 {
        min-height: 60vh;
    }
</style>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-50">
        <div class="col-md-6 col-lg-5">
            <div class="card auth-card shadow">
                <div class="auth-header">
                    <i class="fas fa-envelope me-2"></i> {{ __('Forgot your password?') }}
                </div>

                <div class="card-body px-4 py-4">
                    <p class="text-muted small mb-4">
                        {{ __('Enter the email address for your account and we will send you a link to choose a new password.') }}
                    </p>

                    @if (session('status'))
                        <div class="alert alert-success py-2 small mb-4" role="alert">
                            <i class="fas fa-check-circle me-1"></i>{{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1 text-secondary"></i> {{ __('Email address') }}
                            </label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                autofocus
                                placeholder="name@example.com">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>{{ __('Send reset link') }}
                            </button>
                        </div>

                        <p class="text-center small text-muted mb-0">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>{{ __('Back to login') }}
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
