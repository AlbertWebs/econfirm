<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Create escrow — e-confirm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="{{ asset('theme/dashboard.css') }}?v=4" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('uploads/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f6b3a">
</head>

<body class="db-portal">
    <header class="db-header" role="banner">
        <div class="db-header__inner">
            <a href="{{ route('user.dashboard') }}" class="db-brand" aria-label="Back to portal">
                <img src="{{ asset('uploads/favicon.png') }}"
                     class="d-md-none db-brand__icon-img"
                     width="40"
                     height="40"
                     alt=""
                     fetchpriority="high"
                     decoding="async">
                <img src="{{ asset('uploads/logo-hoz.png') }}"
                     class="d-none d-md-block db-brand__logo-full"
                     alt="e-confirm"
                     decoding="async">
                <span class="visually-hidden">e-confirm — create escrow</span>
            </a>
            <div class="db-header__actions">
                <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-outline-primary d-none d-md-inline-flex align-items-center gap-1" style="border-radius: 0.5rem; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <div class="db-user" title="{{ Auth::user()->name }}">
                    <span class="db-user__avatar" aria-hidden="true">{{ strtoupper(mb_substr((string) (Auth::user()->name ?? 'U'), 0, 1, 'UTF-8')) }}</span>
                    <span class="d-none d-sm-inline js-dashboard-user-name">{{ Auth::user()->name }}</span>
                </div>
                <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="db-btn-logout" title="Log out" aria-label="Log out">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </header>

    <div class="container py-3 py-md-4 db-page--mnav" style="max-width: 720px;">
        <nav class="mb-2 mb-md-3" aria-label="Breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create transaction</li>
            </ol>
        </nav>
        @include('dashboard.partials.escrow-create-form', ['formHeading' => 'Create a new escrow'])
    </div>

    @include('dashboard.partials.mobile-bottom-nav', ['mnavActive' => 'create'])

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('theme/script.js') }}"></script>
    @include('dashboard.scripts')
</body>
</html>
