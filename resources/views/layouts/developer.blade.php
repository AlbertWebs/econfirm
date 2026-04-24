<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'API developer') — e-confirm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Instrument+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="{{ asset('theme/dashboard.css') }}?v=7" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('uploads/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f6b3a">
    @stack('head')
</head>
<body class="db-portal db-dev-hub">
    <a class="visually-hidden-focusable btn btn-sm btn-primary position-fixed rounded-pill shadow" style="top:0.5rem;left:0.5rem;z-index:1080;" href="#db-dev-main">Skip to content</a>

    <div class="db-dev-app" id="db-dev-app">
        <aside class="db-dev-sidebar" id="db-dev-sidebar" aria-label="Developer hub navigation">
            <div class="db-dev-sidebar__brand">
                <a href="{{ route('api.home') }}" class="db-dev-sidebar__logo text-decoration-none text-white">
                    <span class="db-dev-sidebar__logo-mark" aria-hidden="true"><i class="fas fa-code"></i></span>
                    <span>
                        <span class="db-dev-sidebar__logo-title">API hub</span>
                        <span class="db-dev-sidebar__logo-sub">e-confirm</span>
                    </span>
                </a>
            </div>

            <nav id="db-dev-sidebar-nav" class="db-dev-sidebar__nav nav flex-column" aria-label="Page sections">
                <a class="nav-link db-dev-navlink" href="#dev-section-overview"><i class="fas fa-gauge-high fa-fw me-2" aria-hidden="true"></i>Overview</a>
                <a class="nav-link db-dev-navlink" href="#dev-section-api-usage"><i class="fas fa-chart-column fa-fw me-2" aria-hidden="true"></i>API usage</a>
                <a class="nav-link db-dev-navlink" href="#dev-section-endpoints"><i class="fas fa-link fa-fw me-2" aria-hidden="true"></i>Endpoints</a>
                <a class="nav-link db-dev-navlink" href="#dev-section-api-key"><i class="fas fa-key fa-fw me-2" aria-hidden="true"></i>API key</a>
                <a class="nav-link db-dev-navlink" href="#dev-section-examples"><i class="fas fa-terminal fa-fw me-2" aria-hidden="true"></i>Code examples</a>
                <a class="nav-link db-dev-navlink" href="#dev-section-postman"><i class="fas fa-paper-plane fa-fw me-2" aria-hidden="true"></i>Postman</a>
                <a class="nav-link db-dev-navlink" href="#dev-section-mpesa"><i class="fas fa-mobile-screen-button fa-fw me-2" aria-hidden="true"></i>M-Pesa URLs</a>
                <a class="nav-link db-dev-navlink" href="#dev-section-transactions"><i class="fas fa-table fa-fw me-2" aria-hidden="true"></i>Transactions</a>
            </nav>

            <div class="db-dev-sidebar__footer mt-auto">
                <a href="{{ route('api-documentation') }}" class="db-dev-sidebar__mini"><i class="fas fa-book me-2" aria-hidden="true"></i>Public API docs</a>
                @if (Route::has('user.dashboard'))
                    <a href="{{ route('user.dashboard') }}" class="db-dev-sidebar__mini"><i class="fas fa-arrow-up-right-from-square me-2" aria-hidden="true"></i>Customer portal</a>
                @endif
                <a href="{{ route('home') }}" class="db-dev-sidebar__mini"><i class="fas fa-house me-2" aria-hidden="true"></i>Site home</a>
            </div>
        </aside>

        <div class="db-dev-backdrop" data-dev-sidebar-close aria-hidden="true"></div>

        <div class="db-dev-main" id="db-dev-main">
            <header class="db-dev-topbar">
                <div class="db-dev-topbar__start">
                    <button type="button" class="btn db-dev-menu-btn d-lg-none" id="db-dev-sidebar-toggle" data-dev-sidebar-toggle aria-controls="db-dev-sidebar" aria-expanded="false" aria-label="Open menu">
                        <i class="fas fa-bars" aria-hidden="true"></i>
                    </button>
                    <div class="db-dev-topbar__titles">
                        <h1 class="db-dev-topbar__title mb-0">@yield('page_title', 'API developer')</h1>
                        <p class="db-dev-topbar__subtitle mb-0 d-none d-md-block">@yield('page_subtitle', 'Keys, endpoints, and integration helpers')</p>
                    </div>
                </div>
                <div class="db-dev-topbar__actions">
                    <a href="{{ route('api-documentation') }}" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center gap-1 rounded-3 fw-semibold">API docs</a>
                    @if (Route::has('user.dashboard'))
                        <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-outline-primary d-none d-sm-inline-flex align-items-center gap-1 rounded-3 fw-semibold">Portal</a>
                    @endif
                    <div class="db-user d-none d-sm-flex" title="{{ Auth::user()->name }}">
                        <span class="db-user__avatar" aria-hidden="true">{{ strtoupper(\Illuminate\Support\Str::substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                        <span class="js-dashboard-user-name text-truncate" style="max-width:8rem;">{{ Auth::user()->name }}</span>
                    </div>
                    <button type="button" class="db-btn-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Log out" aria-label="Log out">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                    </button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </header>

            <main class="db-dev-content"
                  id="db-dev-scroll"
                  tabindex="0"
                  data-bs-spy="scroll"
                  data-bs-target="#db-dev-sidebar-nav"
                  data-bs-smooth-scroll="true"
                  data-bs-offset="88">
                <div class="db-dev-content__inner">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        (function () {
            var app = document.getElementById('db-dev-app');
            var sidebar = document.getElementById('db-dev-sidebar');
            var toggle = document.getElementById('db-dev-sidebar-toggle');
            var backdrop = document.querySelector('[data-dev-sidebar-close]');
            var mq = window.matchMedia('(min-width: 992px)');

            function setOpen(open) {
                if (!app) return;
                app.classList.toggle('db-dev-app--sidebar-open', open);
                if (backdrop) {
                    backdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
                }
                document.body.classList.toggle('db-dev-hub--nav-open', open);
                if (toggle) toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (toggle) toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            }

            function closeSidebar() {
                setOpen(false);
            }

            toggle && toggle.addEventListener('click', function () {
                setOpen(!app.classList.contains('db-dev-app--sidebar-open'));
            });
            backdrop && backdrop.addEventListener('click', closeSidebar);
            mq.addEventListener('change', function (e) {
                if (e.matches) closeSidebar();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeSidebar();
            });
            sidebar && sidebar.querySelectorAll('a[href^="#"]').forEach(function (a) {
                a.addEventListener('click', function () {
                    if (!mq.matches) closeSidebar();
                });
            });

            var scrollEl = document.getElementById('db-dev-scroll');
            if (scrollEl && window.bootstrap && window.bootstrap.ScrollSpy) {
                window.bootstrap.ScrollSpy.getOrCreateInstance(scrollEl, {
                    target: '#db-dev-sidebar-nav',
                    offset: 88,
                    smoothScroll: true,
                });
            }
        })();
    </script>
</body>
</html>
