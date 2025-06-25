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
    <!-- Header -->
    <header class="bg-white border-bottom shadow-sm mb-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center">
                    <div class="p-2 bg-primary rounded me-3">
                        <i class="fas fa-shield-alt text-white"></i>
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
                <div class="d-flex align-items-center">
                     <a href="{{route('user.dashboard')}}" class="btn btn-outline-primary btn-sm me-3 position-relative"> 
                        <i class="fas fa-dashboard me-1"></i>
                        My Account
                     </a>
                    @yield('header-actions')
                    {{-- <div class="d-flex align-items-center me-3">
                        <div class="bg-primary bg-opacity-25 rounded-circles p-2 me-2" style="border-radius: 5px;">
                            <i class="fas fa-user" style="color:#ffffff"></i>
                        </div>
                        <span class="fw-medium">@yield('user-phone', 'N/A')</span>
                    </div> --}}
                 
                    <a  class="btn btn-outline-secondary btn-sm" title="Logout" class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out"></i>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                </div>
            </div>
        </div>
    </header>

    <div class="container py-5">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="footer bg-white border-top shadow-sm py-3 mt-auto fixed-bottom">
        <div class="container text-center">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="mb-2 mb-md-0">
                    <img src="{{ asset('uploads/logo.png') }}" alt="e-confirm Logo" style="height: 40px; vertical-align: middle;">
                    <span class="ms-2 text-muted">&copy; {{ date('Y') }} e-confirm. All rights reserved.</span>
                </div>
                <div>
                    <span class="text-muted small">Licensed and regulated escrow service</span>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
