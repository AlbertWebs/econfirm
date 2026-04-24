<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>API developer — e-confirm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
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
            <a href="{{ route('home') }}" class="db-brand" aria-label="e-confirm — home">
                <img src="{{ asset('uploads/logo-hoz.png') }}" class="db-brand__logo-full d-none d-md-block" alt="e-confirm" decoding="async" style="height:2rem;">
                <img src="{{ asset('uploads/favicon.png') }}" class="d-md-none db-brand__icon-img" width="40" height="40" alt="">
            </a>
            <div class="db-header__actions">
                <a href="{{ route('api-documentation') }}" class="btn btn-sm btn-outline-secondary me-1 d-none d-sm-inline-flex align-items-center gap-1" style="border-radius:0.5rem;font-weight:600;">API docs</a>
                @if (Route::has('user.dashboard'))
                    <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-outline-primary me-1 d-none d-sm-inline-flex align-items-center gap-1" style="border-radius:0.5rem;font-weight:600;">Portal</a>
                @endif
                <div class="db-user" title="{{ Auth::user()->name }}">
                    <span class="db-user__avatar" aria-hidden="true">{{ strtoupper(\Illuminate\Support\Str::substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                </div>
                <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="db-btn-logout" title="Log out" aria-label="Log out">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>
    </header>

    <div class="container py-4 db-page--mnav" style="max-width: 880px;">
        <h1 class="h3 fw-bold mb-1">API developer</h1>
        <p class="text-muted small mb-4">Base URLs, your secret key, and recent escrow rows created with this key.</p>

        @if (session('new_api_key'))
            <div class="alert alert-warning border-0 shadow-sm" role="alert">
                <strong>Copy your new key now.</strong> For security it is not shown again after you leave this page.
                <div class="input-group my-2">
                    <input type="text" class="form-control font-monospace small" readonly id="flash-new-key" value="{{ session('new_api_key') }}">
                    <button class="btn btn-dark" type="button" data-copy-target="#flash-new-key"><i class="fas fa-copy"></i></button>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Endpoints</h2>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><span class="badge bg-light text-dark border me-2">GET</span> <code id="t-ping">{{ $apiRootUrl }}/ping</code> <button type="button" class="btn btn-link btn-sm p-0" data-copy-target="#t-ping">Copy</button></li>
                    <li class="mb-2"><span class="text-muted me-1">v1 base</span> <code id="t-v1">{{ $apiV1Url }}</code> <button type="button" class="btn btn-link btn-sm p-0" data-copy-target="#t-v1">Copy</button></li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-2">API key</h2>
                <p class="small text-muted mb-2">Send as <code>Authorization: Bearer &lt;key&gt;</code> on v1 requests.</p>
                @if ($keyPreview)
                    <p class="font-monospace mb-2">Current key: <strong>{{ $keyPreview }}</strong></p>
                @else
                    <p class="small text-muted mb-2">You do not have a key yet. Generate one to call the API.</p>
                @endif
                <form method="post" action="{{ route('api.key.regenerate') }}" class="d-inline" onsubmit="return confirm('{{ $keyPreview ? 'Rotate your key? The old key stops working immediately.' : 'Generate a new API key?' }}');">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">{{ $keyPreview ? 'Rotate key' : 'Generate key' }}</button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-2">Example (curl)</h2>
@php
    $bearer = session('new_api_key') ?? 'YOUR_API_KEY';
    $sampleCurl = "# Health check (no key)\ncurl -sS \"{{ $apiRootUrl }}/ping\"\n\n# Authenticated (replace txn id after creating a transaction)\ncurl -sS -X GET \"{{ $apiV1Url }}/transactions/txn_xxxxxxxx\" \\\n  -H \"Authorization: Bearer {$bearer}\"";
@endphp
                <pre class="bg-light p-3 rounded small mb-0 overflow-auto" id="curl-sample">{{ $sampleCurl }}</pre>
                <button type="button" class="btn btn-outline-dark btn-sm mt-2" data-copy-target="#curl-sample">Copy curl</button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Your API escrow transactions (latest 20)</h2>
                @if ($apiTransactions->isEmpty())
                    <p class="small text-muted mb-0">No rows yet. Create one with <code>POST {{ $apiV1Url }}/transactions</code>.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle small mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($apiTransactions as $t)
                                    <tr>
                                        <td><code class="small">{{ $t->transaction_id }}</code></td>
                                        <td><span class="badge bg-secondary">{{ $t->status }}</span></td>
                                        <td>{{ $t->currency }} {{ number_format((float) $t->transaction_amount, 2) }}</td>
                                        <td class="text-muted">{{ $t->created_at?->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-copy-target]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var sel = btn.getAttribute('data-copy-target');
                var el = document.querySelector(sel);
                if (!el) return;
                var text = el.tagName === 'INPUT' ? el.value : (el.textContent || '').trim();
                navigator.clipboard.writeText(text).then(function() {
                    btn.setAttribute('title', 'Copied');
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
