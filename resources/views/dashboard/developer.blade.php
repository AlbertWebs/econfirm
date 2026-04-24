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

        @if (session('error'))
            <div class="alert alert-danger border-0 shadow-sm" role="alert">{{ session('error') }}</div>
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
                <h2 class="h6 fw-bold text-uppercase text-muted mb-2">Example requests (6 languages)</h2>
                <p class="small text-muted mb-3">Ping, create escrow, fetch status, and release use your key and a real <code class="small">transaction_id</code> where shown.</p>
                @php
                    $codeTabs = [
                        'curl' => 'cURL',
                        'javascript' => 'JavaScript',
                        'php' => 'PHP',
                        'python' => 'Python',
                        'ruby' => 'Ruby',
                        'go' => 'Go',
                    ];
                @endphp
                <ul class="nav nav-tabs small mb-2" id="dev-code-tabs" role="tablist">
                    @foreach ($codeTabs as $key => $label)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 px-3 @if ($loop->first) active @endif" id="tab-{{ $key }}" data-bs-toggle="tab" data-bs-target="#pane-{{ $key }}" type="button" role="tab" aria-controls="pane-{{ $key }}" @if ($loop->first) aria-selected="true" @else aria-selected="false" @endif>{{ $label }}</button>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content border border-top-0 rounded-bottom bg-light p-3" id="dev-code-tab-content">
                    @foreach ($codeTabs as $key => $label)
                        <div class="tab-pane fade @if ($loop->first) show active @endif" id="pane-{{ $key }}" role="tabpanel" aria-labelledby="tab-{{ $key }}">
                            <pre class="small mb-2 overflow-auto font-monospace" style="max-height:22rem;white-space:pre-wrap;word-break:break-word;" id="code-sample-{{ $key }}">{{ $codeSamples[$key] ?? '' }}</pre>
                            <button type="button" class="btn btn-outline-dark btn-sm" data-copy-target="#code-sample-{{ $key }}">Copy</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-2">Postman</h2>
                <p class="small text-muted mb-3">Collection v2.1: health check, <code>POST /v1/transactions</code>, get transaction, release. Variables <code class="small">apiRoot</code>, <code class="small">apiKey</code>, <code class="small">transactionId</code>.</p>
                <a href="{{ route('developer.postman.collection') }}" class="btn btn-success btn-sm rounded-pill px-3">Download Postman collection</a>
                @unless ($keyPreview)
                    <p class="small text-muted mt-2 mb-0">Save a key with <strong>Generate key</strong> first; the download uses your stored key (not the one-time flash banner).</p>
                @endunless
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-2">M-Pesa callbacks (inbound)</h2>
                <p class="small text-muted mb-3">Safaricom posts JSON to these URLs. Your app acknowledges with HTTP <strong>200</strong> and a small JSON body (see below). These are <em>not</em> part of the escrow API key flow.</p>
                <ul class="list-unstyled small mb-4">
                    <li class="mb-2"><span class="badge bg-light text-dark border me-2">POST</span> <code class="small">{{ url('/api/mpesa/callback') }}</code> — STK (Lipa na M-Pesa Online)</li>
                    <li class="mb-2"><span class="badge bg-light text-dark border me-2">POST</span> <code class="small">{{ url('/api/mpesa/b2b/callback') }}</code> — B2B result</li>
                    <li class="mb-2"><span class="badge bg-light text-dark border me-2">POST</span> <code class="small">{{ url('/api/mpesa/b2c/callback') }}</code> — B2C result</li>
                    <li class="mb-2"><span class="badge bg-light text-dark border me-2">POST</span> <code class="small">{{ url('/api/mpesa/b2c/timeout') }}</code> — B2C queue timeout (same handler as B2C result)</li>
                </ul>
                <h3 class="h6 fw-semibold mb-2">HTTP response (all handlers)</h3>
                <p class="small text-muted mb-2">After processing (or when the payload shape is unexpected), the server responds with:</p>
                <pre class="bg-light p-3 rounded small mb-4 font-monospace">HTTP/1.1 200 OK
Content-Type: application/json

{"ResultCode":0,"ResultDesc":"Success"}</pre>
                <p class="small text-muted mb-2">For example, the STK handler still returns the same JSON if <code class="small">Body.stkCallback</code> is missing, so Safaricom always gets a clean acknowledgment.</p>
                <h3 class="h6 fw-semibold mb-2">Sample STK callback body (truncated)</h3>
                <pre class="bg-light p-3 rounded small mb-4 font-monospace overflow-auto" style="max-height:14rem;">{
  "Body": {
    "stkCallback": {
      "MerchantRequestID": "29115-34620561-1",
      "CheckoutRequestID": "ws_CO_01012025120000",
      "ResultCode": 0,
      "ResultDesc": "The service request is processed successfully.",
      "CallbackMetadata": {
        "Item": [
          { "Name": "Amount", "Value": 100 },
          { "Name": "MpesaReceiptNumber", "Value": "QGH1234567" },
          { "Name": "Balance" },
          { "Name": "TransactionDate", "Value": 20250101120000 },
          { "Name": "PhoneNumber", "Value": "254712345678" }
        ]
      }
    }
  }
}</pre>
                <h3 class="h6 fw-semibold mb-2">Sample B2B <code class="small">Result</code> (shape)</h3>
                <pre class="bg-light p-3 rounded small mb-4 font-monospace overflow-auto" style="max-height:12rem;">{
  "Result": {
    "ResultType": 0,
    "ResultCode": 0,
    "ResultDesc": "The service request is processed successfully.",
    "OriginatorConversationID": "AG_201...",
    "ConversationID": "AG_201...",
    "TransactionID": "L...",
    "ResultParameters": {
      "ResultParameter": [
        { "Key": "TransactionReceipt", "Value": "..." },
        { "Key": "TransactionCompletedDateTime", "Value": "..." },
        { "Key": "ReceiverPartyPublicName", "Value": "..." },
        { "Key": "PartyB", "Value": "2547..." },
        { "Key": "Amount", "Value": 100 }
      ]
    }
  }
}</pre>
                <h3 class="h6 fw-semibold mb-2">Sample B2C <code class="small">Result</code> (shape)</h3>
                <pre class="bg-light p-3 rounded small mb-0 font-monospace overflow-auto" style="max-height:12rem;">{
  "Result": {
    "ResultType": 0,
    "ResultCode": 0,
    "ResultDesc": "The service request is processed successfully.",
    "OriginatorConversationID": "AG_201...",
    "ConversationID": "AG_201...",
    "TransactionID": "L...",
    "ResultParameters": {
      "ResultParameter": [
        { "Key": "TransactionReceipt", "Value": "..." },
        { "Key": "TransactionCompletedDateTime", "Value": "..." },
        { "Key": "ReceiverPartyPublicName", "Value": "..." },
        { "Key": "TransactionAmount", "Value": 100 }
      ]
    }
  }
}</pre>
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
