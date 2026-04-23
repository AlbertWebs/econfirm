<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create escrow — e-confirm Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('theme/dashboard.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('uploads/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-light">
    <header class="bg-white border-bottom shadow-sm">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <div class="p-2 bg-primary rounded me-3">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">e-confirm</h5>
                        <small class="text-muted">Create escrow</small>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to dashboard
                    </a>
                    <span class="fw-medium d-none d-sm-inline">{{ Auth::user()->name }}</span>
                    <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="container py-4" style="max-width: 720px;">
        <nav class="mb-3" aria-label="Breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Create transaction</li>
            </ol>
        </nav>
        @include('dashboard.partials.escrow-create-form', ['formHeading' => 'Create a new escrow'])
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('theme/script.js') }}"></script>
    @include('dashboard.scripts')
</body>
</html>
