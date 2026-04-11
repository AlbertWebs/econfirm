@extends('process.master')

@section('title', __('Reset password') . ' | e-confirm Portal')

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
        <div class="col-md-6 col-lg-5">
            <div class="card auth-card shadow">
                <div class="auth-header">
                    <i class="fas fa-key me-2"></i> {{ __('Set a new password') }}
                </div>

                <div class="card-body px-4 py-4">
                    <p class="text-muted small mb-4">
                        {{ __('Choose a strong new password for your account. Use a password you do not reuse on other sites.') }}
                    </p>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1 text-secondary"></i> {{ __('Email address') }}
                            </label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                value="{{ $email ?? old('email') }}"
                                required
                                autocomplete="email"
                                autofocus>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1 text-secondary"></i> {{ __('New password') }}
                            </label>
                            <div class="input-group">
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password"
                                    required
                                    autocomplete="new-password"
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
                            <p class="form-text small text-muted mb-0 mt-1">{{ __('Use at least 8 characters, mixing letters and numbers.') }}</p>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-1 text-secondary"></i> {{ __('Confirm new password') }}
                            </label>
                            <div class="input-group">
                                <input id="password_confirmation" type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    aria-describedby="passwordConfirmToggle">
                                <button type="button" class="btn password-toggle-btn" id="passwordConfirmToggle"
                                    title="{{ __('Show password') }}" aria-label="{{ __('Show password') }}"
                                    aria-pressed="false" tabindex="0">
                                    <i class="fas fa-eye" id="passwordConfirmToggleIcon" aria-hidden="true"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check me-2"></i>{{ __('Reset password') }}
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var showLabel = @json(__('Show password'));
    var hideLabel = @json(__('Hide password'));

    function bindPasswordToggle(toggleId, inputId, iconId) {
        var btn = document.getElementById(toggleId);
        var input = document.getElementById(inputId);
        var icon = document.getElementById(iconId);
        if (!btn || !input || !icon) return;

        function setVisible(visible) {
            input.type = visible ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !visible);
            icon.classList.toggle('fa-eye-slash', visible);
            btn.setAttribute('aria-label', visible ? hideLabel : showLabel);
            btn.setAttribute('title', visible ? hideLabel : showLabel);
            btn.setAttribute('aria-pressed', visible ? 'true' : 'false');
        }

        btn.addEventListener('click', function () {
            setVisible(input.type === 'password');
        });
    }

    bindPasswordToggle('passwordToggle', 'password', 'passwordToggleIcon');
    bindPasswordToggle('passwordConfirmToggle', 'password_confirmation', 'passwordConfirmToggleIcon');
});
</script>
@endpush
