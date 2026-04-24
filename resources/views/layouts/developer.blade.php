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
    <link href="{{ asset('theme/dashboard.css') }}?v=11" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('uploads/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f6b3a">
    @stack('head')
</head>
<body class="db-portal db-dev-hub">
    <a class="visually-hidden-focusable btn btn-sm btn-primary position-fixed rounded-pill shadow" style="top:0.5rem;left:0.5rem;z-index:1080;" href="#db-dev-main">Skip to content</a>

    <div class="db-dev-app" id="db-dev-app">
        <aside class="db-dev-sidebar" id="db-dev-sidebar" aria-label="Developer hub navigation">
            <div class="db-dev-sidebar__header">
                <a href="{{ route('api.home') }}" class="db-dev-sidebar__brand text-decoration-none">
                    <span class="db-dev-sidebar__brand-icon" aria-hidden="true"><i class="fas fa-code"></i></span>
                    <div class="db-dev-sidebar__brand-text">
                        <span class="db-dev-sidebar__brand-title">API hub</span>
                        <span class="db-dev-sidebar__brand-sub">e-confirm · developer</span>
                    </div>
                </a>
                <div class="db-dev-sidebar__header-actions">
                    <button type="button" class="db-dev-sidebar__icon-btn d-lg-none" id="db-dev-sidebar-close" aria-label="Close menu">
                        <i class="fas fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <nav id="db-dev-sidebar-nav" class="db-dev-sidebar__nav nav flex-column" aria-label="Page sections">
                <ul class="db-dev-sidebar__nav-list list-unstyled mb-0 py-1 px-1" role="list">
                    <li>
                        <a class="nav-link db-dev-navlink" href="#dev-section-overview">
                            <span class="db-dev-navlink__icon" aria-hidden="true"><i class="fas fa-gauge-high"></i></span>
                            <span class="db-dev-navlink__label">Overview</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link db-dev-navlink" href="#dev-section-api-usage">
                            <span class="db-dev-navlink__icon" aria-hidden="true"><i class="fas fa-chart-column"></i></span>
                            <span class="db-dev-navlink__label">API usage</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link db-dev-navlink" href="#dev-section-endpoints">
                            <span class="db-dev-navlink__icon" aria-hidden="true"><i class="fas fa-link"></i></span>
                            <span class="db-dev-navlink__label">Endpoints</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link db-dev-navlink" href="#dev-section-api-key">
                            <span class="db-dev-navlink__icon" aria-hidden="true"><i class="fas fa-key"></i></span>
                            <span class="db-dev-navlink__label">API key</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link db-dev-navlink" href="#dev-section-examples">
                            <span class="db-dev-navlink__icon" aria-hidden="true"><i class="fas fa-terminal"></i></span>
                            <span class="db-dev-navlink__label">Code examples</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link db-dev-navlink" href="#dev-section-postman">
                            <span class="db-dev-navlink__icon" aria-hidden="true"><i class="fas fa-paper-plane"></i></span>
                            <span class="db-dev-navlink__label">Postman</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link db-dev-navlink" href="#dev-section-mpesa">
                            <span class="db-dev-navlink__icon" aria-hidden="true"><i class="fas fa-mobile-screen-button"></i></span>
                            <span class="db-dev-navlink__label">M-Pesa URLs</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link db-dev-navlink" href="#dev-section-transactions">
                            <span class="db-dev-navlink__icon" aria-hidden="true"><i class="fas fa-table"></i></span>
                            <span class="db-dev-navlink__label">Transactions</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="db-dev-sidebar__footer">
                <a href="{{ route('api-documentation') }}" class="db-dev-sidebar__footer-link"><i class="fas fa-book me-2" aria-hidden="true"></i>Public API docs</a>
                @if (Route::has('user.dashboard'))
                    <a href="{{ route('user.dashboard') }}" class="db-dev-sidebar__footer-link"><i class="fas fa-arrow-up-right-from-square me-2" aria-hidden="true"></i>Customer portal</a>
                @endif
                <a href="{{ route('home') }}" class="db-dev-sidebar__footer-link"><i class="fas fa-house me-2" aria-hidden="true"></i>Site home</a>
                <button type="submit" form="logout-form" class="db-dev-sidebar__footer-logout">
                    <span class="db-dev-sidebar__footer-logout-icon" aria-hidden="true"><i class="fas fa-arrow-right-from-bracket"></i></span>
                    <span>Log out</span>
                </button>
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
                    <div class="dropdown db-dev-user-dropdown">
                        <button type="button"
                                class="btn db-dev-user-dropdown-toggle dropdown-toggle d-flex align-items-center gap-2"
                                id="db-dev-user-menu-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                aria-haspopup="true"
                                aria-label="Account menu">
                            <span class="db-user__avatar" aria-hidden="true">{{ strtoupper(\Illuminate\Support\Str::substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                            <span class="js-dashboard-user-name text-truncate d-none d-sm-inline text-start" style="max-width: 9rem;">{{ Auth::user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2" style="min-width: 12.5rem;" aria-labelledby="db-dev-user-menu-toggle">
                            <li class="px-3 pb-2 mb-1 border-bottom border-light-subtle">
                                <div class="small text-muted text-uppercase fw-semibold" style="letter-spacing: 0.04em;">Signed in</div>
                                <div class="fw-semibold text-truncate" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
                                @if (Auth::user()->email)
                                    <div class="small text-muted text-truncate">{{ Auth::user()->email }}</div>
                                @endif
                            </li>
                            <li>
                                <a class="dropdown-item rounded-2 mx-1" href="{{ route('api-documentation') }}">
                                    <i class="fas fa-book fa-fw me-2 text-secondary" aria-hidden="true"></i>Public API docs
                                </a>
                            </li>
                            @if (Route::has('user.dashboard'))
                                <li>
                                    <a class="dropdown-item rounded-2 mx-1" href="{{ route('user.dashboard') }}">
                                        <i class="fas fa-gauge-high fa-fw me-2 text-secondary" aria-hidden="true"></i>Customer portal
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a class="dropdown-item rounded-2 mx-1" href="{{ route('home') }}">
                                    <i class="fas fa-house fa-fw me-2 text-secondary" aria-hidden="true"></i>Site home
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-2"></li>
                            <li>
                                <button type="submit" form="logout-form" class="dropdown-item rounded-2 mx-1 text-danger">
                                    <i class="fas fa-arrow-right-from-bracket fa-fw me-2" aria-hidden="true"></i>Log out
                                </button>
                            </li>
                        </ul>
                    </div>
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
            var closeBtn = document.getElementById('db-dev-sidebar-close');
            closeBtn && closeBtn.addEventListener('click', closeSidebar);
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
