@extends('process.master')

@section('title', 'eConfirm - Customer Portal')

@section('header-actions')
    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-home me-1"></i> Home
    </a>
@endsection

@section('content')
<div class="mx-auto" style="max-width: 980px;">
    <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
            <h4 class="fw-bold mb-1">Customer Portal</h4>
            <p class="text-muted mb-0">Real-time escrow data tied to your phone number.</p>
                    </div>
                </div>

    @if(!$portalPhone)
        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                <h6 class="fw-bold mb-2">Access with Phone + OTP</h6>
                <p class="text-muted small mb-3">
                    Enter the phone number you used while creating escrow, then verify with OTP.
                </p>
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <input type="text" id="portalPhone" class="form-control" placeholder="07XXXXXXXX or +2547XXXXXXXX">
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="button" id="portalSendOtpBtn" class="btn btn-outline-primary w-100">Send OTP</button>
                    </div>
                    <div class="col-12 col-md-3">
                        <input type="text" id="portalOtp" class="form-control" maxlength="6" placeholder="OTP">
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="button" id="portalVerifyOtpBtn" class="btn btn-success w-100">Verify OTP</button>
                    </div>
                </div>
                <div id="portalOtpMsg" class="small mt-2 text-muted"></div>
                    </div>
                            </div>
    @else
        <div class="alert alert-success d-flex align-items-center justify-content-between mb-4">
            <div>
                <strong>Portal phone:</strong> {{ $portalPhone }}
                        </div>
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-success">Create New Escrow</a>
                        </div>
                        
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Active</div>
                        <div class="fs-4 fw-bold">{{ $activeCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Completed</div>
                        <div class="fs-4 fw-bold">{{ $completedCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Total Value</div>
                        <div class="fs-6 fw-bold">KES {{ number_format($totalValue, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">Recent Records</div>
                        <div class="fs-4 fw-bold">{{ $transactions->count() }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold">Your Escrow Transactions</h6>
                                        </div>
            <div class="card-body p-0">
                @if($transactions->isEmpty())
                    <div class="p-4 text-muted">No transactions found for this phone.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $tx)
                                    <tr>
                                        <td class="fw-semibold">{{ $tx->transaction_id }}</td>
                                        <td>{{ $tx->transaction_type ?? '-' }}</td>
                                        <td><span class="badge bg-secondary">{{ $tx->status }}</span></td>
                                        <td>KES {{ number_format((float) $tx->transaction_amount, 2) }}</td>
                                        <td>{{ optional($tx->created_at)->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('transaction.index', ['id' => $tx->transaction_id]) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
    </div>

<script>
(() => {
    const sendBtn = document.getElementById('portalSendOtpBtn');
    const verifyBtn = document.getElementById('portalVerifyOtpBtn');
    const phoneEl = document.getElementById('portalPhone');
    const otpEl = document.getElementById('portalOtp');
    const msgEl = document.getElementById('portalOtpMsg');

    if (!sendBtn || !verifyBtn || !phoneEl || !otpEl || !msgEl) return;

    const setMsg = (text, ok = true) => {
        msgEl.textContent = text;
        msgEl.classList.toggle('text-danger', !ok);
        msgEl.classList.toggle('text-success', ok);
    };

    sendBtn.addEventListener('click', async () => {
        setMsg('Sending OTP…', true);
        sendBtn.disabled = true;
        try {
            const res = await fetch('{{ route('portal.phone.send-otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ phone: phoneEl.value.trim() })
            });
            const data = await res.json();
            if (!res.ok || !data.success) {
                setMsg(data.message || 'Failed to send OTP.', false);
                return;
            }
            setMsg(data.message || 'OTP sent.', true);
        } catch (_) {
            setMsg('Network error while sending OTP.', false);
        } finally {
            sendBtn.disabled = false;
        }
    });

    verifyBtn.addEventListener('click', async () => {
        setMsg('Verifying…', true);
        verifyBtn.disabled = true;
        try {
            const res = await fetch('{{ route('portal.phone.verify-otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ otp: otpEl.value.trim() })
            });
            const data = await res.json();
            if (!res.ok || !data.success) {
                setMsg(data.message || 'OTP verification failed.', false);
                return;
            }
            setMsg('Verified. Loading your portal…', true);
            window.location.reload();
        } catch (_) {
            setMsg('Network error while verifying OTP.', false);
        } finally {
            verifyBtn.disabled = false;
        }
    });
})();
</script>
@endsection