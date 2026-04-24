@extends('layouts.developer')

@section('title', 'API developer hub')
@section('page_title', 'API developer hub')
@section('page_subtitle', 'Base URLs, secret key, examples, and API escrow activity')

@section('content')
        <section id="dev-section-overview" class="db-dev-section">
            <p class="text-muted small mb-3">Everything you need to integrate the escrow API in one place. Use the sidebar to jump between sections.</p>

            @if (session('new_api_key'))
                <div class="alert alert-warning border-0 shadow-sm" role="alert">
                    <strong>Copy your new key now.</strong> For security it is not shown again after you leave this page.
                    <div class="input-group my-2">
                        <input type="password" class="form-control font-monospace small" readonly id="flash-new-key" value="{{ session('new_api_key') }}" autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" data-api-key-toggle="#flash-new-key" aria-pressed="false" aria-label="Show or hide key">Show</button>
                        <button class="btn btn-dark" type="button" data-copy-target="#flash-new-key"><i class="fas fa-copy"></i></button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger border-0 shadow-sm" role="alert">{{ session('error') }}</div>
            @endif
        </section>

        <section id="dev-section-api-usage" class="db-dev-section">
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h2 class="h6 fw-bold text-uppercase text-muted mb-1">API usage</h2>
                            <p class="small text-muted mb-0" style="max-width: 42rem;">
                                Visual summary of <strong>escrows created through your API key</strong> (each <code class="small">POST /v1/transactions</code> that succeeds creates one row). This is not a raw HTTP access log—GET and release calls are not counted here.
                            </p>
                        </div>
                        <div class="db-api-usage-limit-badge small">
                            <span class="d-block text-uppercase text-muted" style="font-size: 0.65rem; letter-spacing: 0.06em;">v1 throttle</span>
                            <strong class="text-nowrap">{{ number_format($apiUsage['throttle']['limit']) }}</strong> req / <strong>{{ $apiUsage['throttle']['per'] }}</strong> min / key
                        </div>
                    </div>

                    @if ($apiUsage['totals']['all_time'] === 0)
                        <div class="db-api-usage-empty text-center py-5 px-3 rounded-3 border border-dashed">
                            <div class="text-secondary mb-2"><i class="fas fa-chart-simple fa-2x" aria-hidden="true"></i></div>
                            <p class="fw-semibold mb-1">No API escrows yet</p>
                            <p class="small text-muted mb-0">Create your first transaction with <code class="small">POST {{ $apiV1Url }}/transactions</code>; activity will appear here.</p>
                        </div>
                    @else
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-xl-3">
                                <div class="db-api-usage-stat h-100">
                                    <div class="db-api-usage-stat__value">{{ number_format($apiUsage['totals']['all_time']) }}</div>
                                    <div class="db-api-usage-stat__label">All-time escrows</div>
                                </div>
                            </div>
                            <div class="col-6 col-xl-3">
                                <div class="db-api-usage-stat h-100">
                                    <div class="db-api-usage-stat__value">{{ number_format($apiUsage['totals']['last_7d']) }}</div>
                                    <div class="db-api-usage-stat__label">Last 7 days</div>
                                </div>
                            </div>
                            <div class="col-6 col-xl-3">
                                <div class="db-api-usage-stat h-100">
                                    <div class="db-api-usage-stat__value">{{ number_format($apiUsage['totals']['last_30d']) }}</div>
                                    <div class="db-api-usage-stat__label">Last 30 days</div>
                                </div>
                            </div>
                            <div class="col-6 col-xl-3">
                                <div class="db-api-usage-stat h-100">
                                    <div class="db-api-usage-stat__value">{{ number_format($apiUsage['totals']['this_month']) }}</div>
                                    <div class="db-api-usage-stat__label">This month</div>
                                    <div class="db-api-usage-stat__hint text-muted">Prev. month: {{ number_format($apiUsage['totals']['last_month']) }}</div>
                                </div>
                            </div>
                        </div>

                        @if ($apiUsage['totals']['first_at'] || $apiUsage['totals']['last_at'])
                            <p class="small text-muted mb-4">
                                <i class="fas fa-clock me-1" aria-hidden="true"></i>
                                @if ($apiUsage['totals']['first_at'])
                                    First API escrow: <strong>{{ $apiUsage['totals']['first_at']->format('M j, Y g:i A') }}</strong>
                                @endif
                                @if ($apiUsage['totals']['first_at'] && $apiUsage['totals']['last_at'])
                                    <span class="mx-1">·</span>
                                @endif
                                @if ($apiUsage['totals']['last_at'])
                                    Latest: <strong>{{ $apiUsage['totals']['last_at']->format('M j, Y g:i A') }}</strong>
                                @endif
                            </p>
                        @endif

                        <h3 class="h6 fw-semibold mb-2">Escrows created per day <span class="text-muted fw-normal small">(14 days)</span></h3>
                        <p class="small text-muted mb-2">Taller bars mean more new escrows that day.</p>
                        <div class="db-api-usage-chart mb-4" role="img" aria-label="Escrows created per day for the last fourteen days">
                            @foreach ($apiUsage['daily'] as $d)
                                @php
                                    $pct = $apiUsage['max_daily'] > 0 ? min(100, round(100 * $d['count'] / $apiUsage['max_daily'])) : 0;
                                    $barPct = $d['count'] === 0 ? 0 : max(8, $pct);
                                @endphp
                                <div class="db-api-usage-chart__col" title="{{ $d['label'] }}: {{ number_format($d['count']) }} escrow(s)">
                                    <span class="db-api-usage-chart__num">{{ $d['count'] }}</span>
                                    <div class="db-api-usage-chart__bar-wrap">
                                        <div class="db-api-usage-chart__bar" style="height: {{ $barPct }}%;"></div>
                                    </div>
                                    <span class="db-api-usage-chart__dow">{{ $d['dow'] }}</span>
                                    <span class="db-api-usage-chart__date">{{ $d['label'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <h3 class="h6 fw-semibold mb-3">Volume by currency</h3>
                                <ul class="list-unstyled mb-0">
                                    @foreach ($apiUsage['by_currency'] as $row)
                                        @php
                                            $w = $apiUsage['currency_volume_max'] > 0 ? min(100, round(100 * $row['volume'] / $apiUsage['currency_volume_max'])) : 0;
                                        @endphp
                                        <li class="mb-3">
                                            <div class="d-flex justify-content-between small mb-1">
                                                <span class="fw-semibold"><span class="badge bg-dark me-1">{{ $row['currency'] }}</span> {{ number_format($row['count']) }} escrow(s)</span>
                                                <span class="text-muted font-monospace">{{ number_format($row['volume'], 2) }}</span>
                                            </div>
                                            <div class="db-api-usage-hbar rounded-pill">
                                                <div class="db-api-usage-hbar__fill rounded-pill" style="width: {{ $w }}%;"></div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-lg-6">
                                <h3 class="h6 fw-semibold mb-3">Status mix</h3>
                                <ul class="list-unstyled mb-0">
                                    @foreach ($apiUsage['status_breakdown'] as $row)
                                        @php
                                            $s = strtolower($row['status']);
                                            $tone = match (true) {
                                                str_contains($s, 'complete') || str_contains($s, 'success') || $s === 'paid' => 'success',
                                                str_contains($s, 'pend') || str_contains($s, 'process') || str_contains($s, 'wait') => 'warning',
                                                str_contains($s, 'fail') || str_contains($s, 'cancel') || str_contains($s, 'reject') => 'danger',
                                                default => 'neutral',
                                            };
                                        @endphp
                                        <li class="mb-3">
                                            <div class="d-flex justify-content-between small mb-1">
                                                <span class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $row['status']) }}</span>
                                                <span class="text-muted">{{ number_format($row['count']) }} ({{ $row['pct'] }}%)</span>
                                            </div>
                                            <div class="db-api-usage-hbar rounded-pill">
                                                <div class="db-api-usage-hbar__fill db-api-usage-hbar__fill--{{ $tone }} rounded-pill" style="width: {{ $row['pct'] }}%;"></div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section id="dev-section-endpoints" class="db-dev-section">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Endpoints</h2>
                    <ul class="list-unstyled small mb-0">
                        <li class="mb-2"><span class="badge bg-light text-dark border me-2">GET</span> <code id="t-ping">{{ $apiRootUrl }}/ping</code> <button type="button" class="btn btn-link btn-sm p-0" data-copy-target="#t-ping">Copy</button></li>
                        <li class="mb-2"><span class="text-muted me-1">v1 base</span> <code id="t-v1">{{ $apiV1Url }}</code> <button type="button" class="btn btn-link btn-sm p-0" data-copy-target="#t-v1">Copy</button></li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="dev-section-api-key" class="db-dev-section">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold text-uppercase text-muted mb-2">API key</h2>
                    <p class="small text-muted mb-2">Send as <code>Authorization: Bearer &lt;key&gt;</code> on v1 requests. The full key stays hidden until you choose <strong>Show</strong>.</p>
                    @if ($storedApiKey)
                        <label class="form-label small text-muted mb-1" for="api-key-display">Your secret key</label>
                        <div class="input-group input-group-sm mb-2">
                            <input
                                type="password"
                                readonly
                                class="form-control font-monospace"
                                id="api-key-display"
                                value="{{ $storedApiKey }}"
                                autocomplete="off"
                                spellcheck="false"
                            >
                            <button type="button" class="btn btn-outline-secondary" data-api-key-toggle="#api-key-display" aria-pressed="false" aria-label="Show or hide API key">Show</button>
                            <button type="button" class="btn btn-dark" data-copy-target="#api-key-display" title="Copy full key"><i class="fas fa-copy"></i></button>
                        </div>
                        <p class="small text-muted mb-3">Preview: <code class="small">{{ $keyPreview }}</code></p>
                    @else
                        <p class="small text-muted mb-2">You do not have a key yet. Generate one to call the API.</p>
                    @endif
                    <form method="post" action="{{ route('api.key.regenerate') }}" class="d-inline" onsubmit="return confirm('{{ $keyPreview ? 'Rotate your key? The old key stops working immediately.' : 'Generate a new API key?' }}');">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">{{ $keyPreview ? 'Rotate key' : 'Generate key' }}</button>
                    </form>
                </div>
            </div>
        </section>

        <section id="dev-section-examples" class="db-dev-section">
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
                    <ul class="nav nav-tabs small mb-2 flex-nowrap overflow-auto" id="dev-code-tabs" role="tablist" style="flex-wrap:nowrap;">
                        @foreach ($codeTabs as $key => $label)
                            <li class="nav-item flex-shrink-0" role="presentation">
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
        </section>

        <section id="dev-section-postman" class="db-dev-section">
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
        </section>

        <section id="dev-section-mpesa" class="db-dev-section">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="h6 fw-bold text-uppercase text-muted mb-2">M-Pesa: inbound vs Daraja (.env)</h2>
                    <p class="small text-muted mb-3">
                        <strong>Inbound</strong> URLs are where <strong>Safaricom</strong> POSTs results. <strong>Daraja request</strong> URLs are the values this server sends when it calls STK/B2C/B2B; they come from <code class="small">.env</code> and should match inbound on production.
                        The Escrow <strong>REST API</strong> (<code class="small">/api/v1/…</code>) does <em>not</em> use a separate M-Pesa callback URL per partner—funding still lands on these routes.
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <h3 class="h6 fw-semibold mb-2">Inbound (Safaricom → this app)</h3>
                            <ul class="list-unstyled small mb-0">
                                <li class="mb-2"><span class="badge bg-light text-dark border me-2">POST</span> <code class="small">{{ $mpesaCallbackUrls['inbound']['stk'] }}</code> — STK</li>
                                <li class="mb-2"><span class="badge bg-light text-dark border me-2">POST</span> <code class="small">{{ $mpesaCallbackUrls['inbound']['b2b'] }}</code> — B2B result / timeout</li>
                                <li class="mb-2"><span class="badge bg-light text-dark border me-2">POST</span> <code class="small">{{ $mpesaCallbackUrls['inbound']['b2c_result'] }}</code> — B2C result</li>
                                <li class="mb-2"><span class="badge bg-light text-dark border me-2">POST</span> <code class="small">{{ $mpesaCallbackUrls['inbound']['b2c_timeout'] }}</code> — B2C queue timeout</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h3 class="h6 fw-semibold mb-2">Sent on Daraja requests (effective)</h3>
                            <ul class="list-unstyled small mb-0">
                                @foreach ($mpesaCallbackUrls['rows'] as $row)
                                    @php
                                        $envKey = explode(' ', $row['env_hint'], 2)[0];
                                    @endphp
                                    <li class="mb-3">
                                        <span class="fw-semibold">{{ $row['label'] }}</span>
                                        @if ($row['effective'])
                                            <code class="small d-block mt-1 text-break">{{ $row['effective'] }}</code>
                                        @else
                                            <span class="small text-warning d-block mt-1">Not set — set <code class="small">{{ $envKey }}</code></span>
                                        @endif
                                        @if ($row['matches'])
                                            <span class="badge bg-success mt-1">Matches inbound route</span>
                                        @elseif ($row['effective'])
                                            <span class="badge bg-danger mt-1">Does not match inbound URL</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <p class="small text-muted mb-3">Acknowledgment: HTTP <strong>200</strong> with <code class="small">{"ResultCode":0,"ResultDesc":"Success"}</code> (not part of the escrow API key flow).</p>
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
        </section>

        <section id="dev-section-transactions" class="db-dev-section">
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
        </section>
@endsection

@push('scripts')
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
    document.querySelectorAll('[data-api-key-toggle]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var sel = btn.getAttribute('data-api-key-toggle');
            var el = document.querySelector(sel);
            if (!el || (el.tagName !== 'INPUT' && el.tagName !== 'TEXTAREA')) return;
            var show = el.type === 'password';
            el.type = show ? 'text' : 'password';
            btn.textContent = show ? 'Hide' : 'Show';
            btn.setAttribute('aria-pressed', show ? 'true' : 'false');
            btn.setAttribute('aria-label', show ? 'Hide key' : 'Show key');
        });
    });
</script>
@endpush
