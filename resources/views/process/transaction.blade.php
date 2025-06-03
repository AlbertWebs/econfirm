<!-- resources/views/view.blade.php -->
@extends('process.master')

@section('title', 'Transaction Details - e-confirm')

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
    {{ $stkPush->phone ?? 'N/A' }}
@endsection

@section('content')
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
                <div class="col-md-6 mb-md-0">
                    <h6 class="text-muted">Amount Funded</h6>
                    <div class="fw-bold">KES {{ number_format($transaction->transaction_amount ?? 0, 2) }}</div>
                    <h6 class="text-muted">Escrow Fee</h6>
                    <div class="fw-bold">KES {{ number_format($transaction->transaction_fee ?? 0, 2) }}</div>
                    
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Parties</h6>
                    <div><strong>Sender:</strong> {{ $transaction->sender_mobile ?? '-' }}</div>
                    {{-- if payment method is m-pesa Recipient is the paybill-till-number but just show the recipient as the contact number --}}

                    @if($transaction->payment_method === 'mpesa')
                        <div><strong>Recipient:</strong> {{ $transaction->receiver_mobile ?? '-' }}</div>
                    @else
                    <div><strong>Recipient Contact:</strong> {{ $transaction->receiver_mobile ?? '-' }} &nbsp; &nbsp; <strong>Paybill/Till:</strong> {{ $transaction->paybill_till_number ?? '-' }}</div>
                    @endif
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
            <div class="d-flex gap-2">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
                @if($transaction->status === 'pending' || $transaction->status === 'Escrow Funded')
                    <a href="{{ route('approve.transaction', $transaction->transaction_id) }}" class="btn btn-success"><i class="fas fa-check me-1"></i> Approve</a>
                    <button class="btn btn-danger"><i class="fas fa-times me-1"></i> Cancel</button>
                @endif
                <button class="btn btn-outline-primary"><i class="fas fa-download me-1"></i> Export Contract</button>
            </div>
        </div>
    </div>
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
@endsection
