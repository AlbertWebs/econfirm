<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'e-confirm Portal')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('theme/dashboard.css') }}" rel="stylesheet">
     <link rel="icon" type="image/png" href="{{ asset('uploads/favicon.png') }}">
     {{-- csrf --}}
     <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')
</head>
<body class="bg-light">
    <style>
        /* Keep content visible above fixed footer on desktop/tablet. */
        body {
            padding-bottom: 96px;
        }

        .portal-main {
            padding-top: 1rem;
            padding-bottom: 1.5rem;
        }

        @media (max-width: 767.98px) {
            /* On mobile, keep content above bottom navigation. */
            body {
                padding-bottom: 88px;
            }

            .portal-main {
                padding-top: 0.5rem;
                padding-bottom: 1rem;
            }
        }
    </style>

    <!-- Header -->
    <header class="bg-white border-bottom shadow-sm mb-4">
        <div class="container-fluid">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 py-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <img src="{{ asset('uploads/favicon.png') }}" alt="eConfirm" class="rounded" style="width: 32px; height: 32px; object-fit: contain;">
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">e-confirm</h5>
                        <small class="text-muted">
                            @if(Auth::check())
                                Welcome {{ Auth::user()->name ?? 'N/A' }}
                            @else
                            Customer Portal
                            @endif
                            
                        </small>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto justify-content-start justify-content-md-end">
                    @auth
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary btn-sm position-relative">
                            <i class="fas fa-dashboard me-1"></i>
                            My Account
                        </a>
                    @endauth
                    @yield('header-actions')
                    @auth
                        <a class="btn btn-outline-secondary btn-sm" title="{{ __('Logout') }}" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <div class="container portal-main">
        @yield('content')
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="d-md-none position-fixed bottom-0 start-0 end-0 bg-white border-top shadow-lg" style="z-index: 1060;">
        <div class="d-grid" style="grid-template-columns: repeat(4, 1fr); min-height: 64px;">
            <a href="{{ route('home') }}" class="d-flex flex-column align-items-center justify-content-center text-decoration-none text-muted small py-2">
                <i class="fas fa-home mb-1"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('support') }}" class="d-flex flex-column align-items-center justify-content-center text-decoration-none text-muted small py-2">
                <i class="fas fa-headset mb-1"></i>
                <span>Support</span>
            </a>
            <a href="{{ route('scam.watch') }}" class="d-flex flex-column align-items-center justify-content-center text-decoration-none text-muted small py-2">
                <i class="fas fa-shield-alt mb-1"></i>
                <span>Confirm</span>
            </a>
            @auth
                <a href="{{ route('user.dashboard') }}" class="d-flex flex-column align-items-center justify-content-center text-decoration-none text-success small py-2">
                    <i class="fas fa-user-circle mb-1"></i>
                    <span>Account</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="d-flex flex-column align-items-center justify-content-center text-decoration-none text-success small py-2">
                    <i class="fas fa-sign-in-alt mb-1"></i>
                    <span>Login</span>
                </a>
            @endauth
        </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
