<!-- resources/views/view.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Details - e-confirm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('theme/dashboard.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Header (from dashboard/approve.blade.php) -->
    <header class="bg-white border-bottom shadow-sm mb-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center py-3">
                <div class="d-flex align-items-center">
                    <div class="p-2 bg-primary rounded me-3">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">e-confirm</h5>
                        <small class="text-muted">Customer Portal</small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary btn-sm me-3">
                        <i class="fas fa-bell me-1"></i>
                        Notifications
                    </button>
                    <div class="d-flex align-items-center me-3">
                        <div class="bg-primary bg-opacity-25 rounded-circle p-2 me-2">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <span class="fw-medium">John Smith</span>
                    </div>
                    <button class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>
    <div class="container py-5">
        <a href="{{ url()->previous() }}" class="btn btn-link mb-4"><i class="fas fa-arrow-left me-2"></i>Back to Portal</a>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Transaction #{{ $transaction->id ?? 'N/A' }}</h4>
                <span class="badge bg-{{ $transaction->status_color ?? 'secondary' }}">{{ ucfirst($transaction->status ?? 'Unknown') }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h6 class="text-muted">Type</h6>
                        <div class="fw-bold">{{ $transaction->transaction_type ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Date Created</h6>
                        <div class="fw-bold">{{ $transaction->created_at ? $transaction->created_at->format('M d, Y') : '-' }}</div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h6 class="text-muted">Amount</h6>
                        <div class="fw-bold">KES {{ number_format($transaction->transaction_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Parties</h6>
                        <div><strong>Sender:</strong> {{ $transaction->sender_mobile ?? '-' }}</div>
                        <div><strong>Recipient:</strong> {{ $transaction->receiver_mobile ?? '-' }}</div>
                    </div>
                </div>
                <div class="mb-4">
                    <h6 class="text-muted">Details</h6>
                    <div>{{ $transaction->transaction_details ?? '-' }}</div>
                </div>
                <div class="mb-4">
                    <h6 class="text-muted">Progress</h6>
                    @php
                        $progress = 0;
                        if ($transaction->status === 'pending') {
                            $progress = 0;
                        } elseif ($transaction->status === 'in progress') {
                            $progress = 50;
                        } elseif ($transaction->status === 'completed') {
                            $progress = 100;
                        }
                    @endphp
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: {{ $progress }}%"></div>
                    </div>
                    <small class="text-muted">{{ $progress }}% complete</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('portal') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
                    @if($transaction->status === 'pending' || $transaction->status === 'in progress')
                        <a href="{{ route('transaction.approve', $transaction->transaction_id) }}" class="btn btn-success"><i class="fas fa-check me-1"></i> Approve</a>
                        <button class="btn btn-danger"><i class="fas fa-times me-1"></i> Cancel</button>
                    @endif
                    <button class="btn btn-outline-primary"><i class="fas fa-download me-1"></i> Export PDF</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer (from dashboard/approve.blade.php) -->
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
</body>
</html>
