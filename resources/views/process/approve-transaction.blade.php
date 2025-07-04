<!-- resources/views/dashboard/approve.blade.php -->
@extends('process.master')

@section('title', 'Approve Transaction - e-confirm')

@push('head')
    <!-- Any additional head content can go here -->
@endpush

@section('header-actions')
    <button class="btn btn-outline-secondary btn-sm me-3 position-relative"> 
        <i class="fas fa-gavel me-1"></i>
        Raise Dispute
    </button>
@endsection

@section('user-phone')
    @if(Auth::check())
        {{ Auth::user()->name ?? 'N/A' }}
    @else
      {{ $stkPush->phone ?? 'N/A' }}
    @endif
@endsection

@section('content')

    @if($transaction->status === 'Completed')
    <div id="transaction-details-card">
        <div class="card mx-auto mb-4" style="max-width: 600px;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Transaction Details</h5>
                <a href="{{ route('e-contract.print', $transaction->id) }}" class="btn btn-outline-primary btn-sm text-white">
                    <i class="fas fa-download me-1"></i> Export Contracts
                </a>
            </div>
            <div class="card-body">
                <div class="mb-2"><strong>ID:</strong> {{ $transaction->transaction_id ?? 'N/A' }}</div>
                <div class="mb-2"><strong>Type:</strong> {{ $transaction->transaction_type ?? '-' }}</div>
                <div class="mb-2"><strong>Status:</strong> <span class="badge bg-{{ $transaction->status_color ?? 'success' }}">{{ ucfirst($transaction->status ?? 'Unknown') }}</span></div>
                <div class="mb-2"><strong>Amount:</strong> KES {{ number_format($transaction->transaction_amount ?? 0, 2) }}</div>
                <div class="mb-2"><strong>Sender:</strong> {{ $transaction->sender_mobile ?? '-' }}</div>
                {{-- if payment method is m-pesa Recipient is the paybill-till-number but just show the recipient as the contact number --}}
                @if($transaction->payment_method === 'mpesa')
                    <div class="mb-2"><strong>Recipient:</strong> {{ $transaction->receiver_mobile ?? '-' }}</div>
                @else
                    <div class="mb-2"><strong>Recipient Till:</strong> {{ $transaction->paybill_till_number ?? '-' }} &nbsp; &nbsp; <strong>Recepient Contact:</strong> {{ $transaction->receiver_mobile ?? '-' }}</div>
                @endif
                <div class="mb-2"><strong>Date Created:</strong> {{ $transaction->created_at ? $transaction->created_at->format('M d, Y') : '-' }}</div>
                <div class="mb-2"><strong>Details:</strong> {{ $transaction->transaction_details ?? '-' }}</div>
                <div class="mb-2"><strong>Progress:</strong>
                    @php
                    $progress = 0;
                    if ($transaction->status === 'pending') {
                        $progress = 0;
                    } elseif ($transaction->status === 'Escrow Funded') {
                        $progress = 50;
                    } elseif ($transaction->status === 'Completed') {
                        $progress = 100;
                    }
                    @endphp
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: {{ $progress }}%"></div>
                    </div>
                    <small class="text-muted">{{ $progress }}% complete</small>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="d-flex justify-content-center">
        <button class="btn btn-link mb-3 theme-color" type="button" id="toggle-details-btn">
            <i class="fas fa-eye me-1" id="toggle-details-icon"></i>
            <span id="toggle-details-text">Show</span> Transaction Details
        </button>
    </div>
    <div id="transaction-details-card" style="display: none; transition: max-height 0.5s cubic-bezier(0.4,0,0.2,1), opacity 0.5s; overflow: hidden; max-height: 0; opacity: 0;">
        <div class="card mx-auto mb-4" style="max-width: 600px;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Transaction Details</h5>
                <a href="{{ route('e-contract.print', $transaction->id) }}" class="btn btn-outline-primary btn-sm text-white">
                    <i class="fas fa-download me-1"></i> Export Contract
                </a>
            </div>
            <div class="card-body">
                <div class="mb-2"><strong>ID:</strong> {{ $transaction->transaction_id ?? 'N/A' }}</div>
                <div class="mb-2"><strong>Type:</strong> {{ $transaction->transaction_type ?? '-' }}</div>
                <div class="mb-2"><strong>Status:</strong> <span class="badge bg-{{ $transaction->status_color ?? 'success' }}">{{ ucfirst($transaction->status ?? 'Unknown') }}</span></div>
                <div class="mb-2"><strong>Amount:</strong> KES {{ number_format($transaction->transaction_amount ?? 0, 2) }}</div>
                <div class="mb-2"><strong>Sender:</strong> {{ $transaction->sender_mobile ?? '-' }}</div>
                {{-- if payment method is m-pesa Recipient is the paybill-till-number but just show the recipient as the contact number --}}
                @if($transaction->payment_method === 'mpesa')
                    <div class="mb-2"><strong>Recipient:</strong> {{ $transaction->receiver_mobile ?? '-' }}</div>
                @else
                    <div class="mb-2"><strong>Recipient Till:</strong> {{ $transaction->paybill_till_number ?? '-' }} &nbsp; &nbsp; <strong>Recepient Contact:</strong> {{ $transaction->receiver_mobile ?? '-' }}</div>
                @endif
                <div class="mb-2"><strong>Date Created:</strong> {{ $transaction->created_at ? $transaction->created_at->format('M d, Y') : '-' }}</div>
                <div class="mb-2"><strong>Details:</strong> {{ $transaction->transaction_details ?? '-' }}</div>
                <div class="mb-2"><strong>Progress:</strong>
                    @php
                    $progress = 0;
                    if ($transaction->status === 'pending') {
                        $progress = 0;
                    } elseif ($transaction->status === 'Escrow Funded') {
                        $progress = 50;
                    } elseif ($transaction->status === 'Completed') {
                        $progress = 100;
                    }
                    @endphp
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-primary" style="width: {{ $progress }}%"></div>
                    </div>
                    <small class="text-muted">{{ $progress }}% complete</small>
                </div>
            </div>
        </div>
    </div>
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Approve Transaction</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('transaction.approve', $transaction->id) }}" id="approve-transaction-form">
                @csrf
                <div class="mb-3">
                    <label for="comment" class="form-label">Comment</label>
                    <textarea class="form-control" id="comment" name="comment" rows="2" placeholder="Add a comment (optional)"></textarea>
                </div>
                <div class="mb-3">
                    <label for="review" class="form-label">Review</label>
                    <select class="form-select" id="review" name="review" required>
                        <option value="positive">Positive</option>
                        <option value="neutral">Neutral</option>
                        <option value="negative">Negative</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="otp" class="form-label">OTP</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="otp" name="otp" maxlength="6" placeholder="Enter OTP" required>
                        <button type="button" class="btn btn-outline-primary" id="request-otp-btn"><i class="fas fa-key me-1"></i> Request OTP</button>
                    </div>
                    <small id="otp-message" class="form-text text-success d-none">OTP sent!</small>
                </div>
                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check me-1"></i> Approve Transaction</button>
                {{--  --}}
                <br><br>
                <div class="d-flex gap-2">
                    <a href="{{ route('transaction.index', $transaction->transaction_id) }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>

                    <a style="position:absolute; right:16px;" href="{{route('home')}}" class="btn btn-danger"><i class="fas fa-times me-1"></i> Cancel</a>
                </div>
                {{--  --}}
                
            </form>
        </div>
    </div>
    @endif
    <style>
        .raise-dispute-glow {
            overflow: visible;
        }
        .raise-dispute-glow::after,
        .raise-dispute-glow .heartbeat-glow {
            content: '';
            position: absolute;
            top: 50%;
            right: -18px;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            background: #ff3860;
            border-radius: 50%;
            box-shadow: 0 0 8px 4px #ff386066;
            animation: heartbeat 1.2s infinite;
            z-index: 2;
        }
        @keyframes heartbeat {
            0% { transform: translateY(-50%) scale(1); box-shadow: 0 0 8px 4px #ff386066; }
            20% { transform: translateY(-50%) scale(1.2); box-shadow: 0 0 16px 8px #ff386099; }
            40% { transform: translateY(-50%) scale(1); box-shadow: 0 0 8px 4px #ff386066; }
            60% { transform: translateY(-50%) scale(1.2); box-shadow: 0 0 16px 8px #ff386099; }
            100% { transform: translateY(-50%) scale(1); box-shadow: 0 0 8px 4px #ff386066; }
        }
    </style>
    @push('scripts')
    <script>
        document.getElementById('request-otp-btn')?.addEventListener('click', function() {
            const msg = document.getElementById('otp-message');
            msg.classList.remove('d-none');
            msg.textContent = 'Sending OTP...';

            fetch('{{ route("create.otp") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    transaction_id: '{{ $transaction->transaction_id }}',
                    phone: '{{ $stkPush->phone ?? $transaction->receiver_mobile ?? "" }}'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    msg.textContent = 'OTP sent!';
                    msg.classList.remove('text-danger');
                    msg.classList.add('text-success');
                } else {
                    msg.textContent = data.message || 'Failed to send OTP.';
                    msg.classList.remove('text-success');
                    msg.classList.add('text-danger');
                }
                setTimeout(() => msg.classList.add('d-none'), 4000);
            })
            .catch(() => {
                msg.textContent = 'Failed to send OTP.';
                msg.classList.remove('text-success');
                msg.classList.add('text-danger');
                setTimeout(() => msg.classList.add('d-none'), 4000);
            });
        });
        //approve-transaction-form
        const approveForm = document.getElementById('approve-transaction-form');
        approveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(approveForm);
            fetch(approveForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Handle successful approval
                    alert('Transaction approved successfully!');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to approve transaction.');
                }
            })
            .catch(() => {
                // Handle network error
            });
        });
        // Toggle transaction details with animation
        const toggleBtn = document.getElementById('toggle-details-btn');
        const detailsCard = document.getElementById('transaction-details-card');
        const toggleText = document.getElementById('toggle-details-text');
        const toggleIcon = document.getElementById('toggle-details-icon');
        let detailsVisible = false;
        function animateShow(el) {
            el.style.display = '';
            el.style.maxHeight = el.scrollHeight + 'px';
            el.style.opacity = '1';
        }
        function animateHide(el) {
            el.style.maxHeight = '0';
            el.style.opacity = '0';
            setTimeout(() => { el.style.display = 'none'; }, 500);
        }
        if (toggleBtn && detailsCard && toggleText && toggleIcon) {
            // Hide by default
            detailsCard.style.display = 'none';
            detailsCard.style.maxHeight = '0';
            detailsCard.style.opacity = '0';
            detailsVisible = false;
            toggleText.textContent = 'Show';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
            toggleBtn.addEventListener('click', function() {
                if (!detailsVisible) {
                    animateShow(detailsCard);
                    toggleText.textContent = 'Hide';
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    animateHide(detailsCard);
                    toggleText.textContent = 'Show';
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                }
                detailsVisible = !detailsVisible;
            });
        }
    </script>
    @endpush
@endsection
