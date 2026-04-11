@extends('process.master')

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
    .password-toggle-btn {
        border-color: #ced4da;
        color: #495057;
    }
    .password-toggle-btn:hover,
    .password-toggle-btn:focus {
        background-color: #f8f9fa;
        border-color: #18743c;
        color: #18743c;
    }
    .input-group .form-control.is-invalid {
        z-index: 0;
    }
</style>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-50">
        <div class="col-md-6">
            <div class="card auth-card shadow">
                <div class="auth-header">
                    <i class="bi bi-person-circle me-2"></i> {{ __('Login') }}
                </div>

                <div class="card-body px-4 py-3">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        {{-- Email Field --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope-fill me-1"></i> {{ __('Email Address') }}
                            </label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password Field --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock-fill me-1"></i> {{ __('Password') }}
                            </label>
                            <div class="input-group">
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="current-password"
                                    aria-describedby="passwordToggle">
                                <button type="button" class="btn password-toggle-btn" id="passwordToggle"
                                    title="{{ __('Show password') }}" aria-label="{{ __('Show password') }}"
                                    aria-pressed="false" tabindex="0">
                                    <i class="fas fa-eye" id="passwordToggleIcon" aria-hidden="true"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>

                        {{-- Submit & Forgot Password --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right me-1"></i> {{ __('Login') }}
                            </button>

                            @if (Route::has('password.request'))
                                <a class="text-decoration-none" href="{{ route('password.request') }}">
                                    <i class="bi bi-question-circle me-1"></i> {{ __('Forgot Password?') }}
                                </a>
                            @endif
                        </div>
                    </form>
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
    if (!btn || !input || !icon) return;

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
});
</script>
@endpush
