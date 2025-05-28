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
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Transaction #{{ $transaction->id ?? 'N/A' }}</h4>
                <span class="badge bg-{{ $transaction->status_color ?? 'secondary' }}">{{ ucfirst($transaction->status ?? 'Unknown') }}</span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h6 class="text-muted">Type</h6>
                        <div class="fw-bold">{{ $transaction->type ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Date Created</h6>
                        <div class="fw-bold">{{ $transaction->created_at ? $transaction->created_at->format('M d, Y') : '-' }}</div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h6 class="text-muted">Amount</h6>
                        <div class="fw-bold">KES {{ number_format($transaction->amount ?? 0, 2) }}</div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Parties</h6>
                        <div><strong>Sender:</strong> {{ $transaction->sender_mobile ?? '-' }}</div>
                        <div><strong>Recipient:</strong> {{ $transaction->receiver_mobile ?? '-' }}</div>
                    </div>
                </div>
                <div class="mb-4">
                    <h6 class="text-muted">Details</h6>
                    <div>{{ $transaction->details ?? '-' }}</div>
                </div>
                <div class="mb-4">
                    <h6 class="text-muted">Progress</h6>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: {{ $transaction->progress ?? 0 }}%"></div>
                    </div>
                    <small class="text-muted">{{ $transaction->progress ?? 0 }}% complete</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
                    @if($transaction->status === 'pending')
                        <button class="btn btn-success"><i class="fas fa-check me-1"></i> Approve</button>
                        <button class="btn btn-danger"><i class="fas fa-times me-1"></i> Cancel</button>
                    @endif
                    <button class="btn btn-outline-primary"><i class="fas fa-download me-1"></i> Export PDF</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer class="bg-white border-top mt-5 py-3 fixed-bottom">
        <div class="container-fluid text-center text-muted small">
            &copy; 2025 e-confirm. All rights reserved.
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
