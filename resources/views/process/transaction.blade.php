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
    @if(Auth::check())
        {{ Auth::user()->name ?? 'N/A' }}
    @else
      {{ $stkPush->phone ?? 'N/A' }}
    @endif
@endsection

<style>

.update-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    max-width: 350px;
    z-index: 1055;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.5s ease-in-out;
}

.update-notification.show {
    opacity: 1;
    transform: translateY(0);
}

.loginPrompt {
    position: fixed;
    bottom: 20px;
    right: 20px;
    max-width: 350px;
    z-index: 1055;
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.5s ease-in-out;
}

.loginPrompt.show {
    opacity: 1;
    transform: translateY(0);
}

</style>

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
                <a href="{{ route('e-contract.print', $transaction->id) }}" class="btn btn-outline-primary"><i class="fas fa-download me-1"></i> Export Contract</a>
            </div>
        </div>
    </div>
    {{--  --}}
    <div id="updateNotification" class="update-notification">
        <div class="card shadow border-0">
            <div class="card-body d-flex align-items-start gap-3">
                <i class="bi bi-info-circle-fill text-primary fs-4 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>Update Required</strong>
                    <p class="mb-2 small text-muted">Please update your profile details for a better experience.</p>
                    <a href="{{ route('profile.edit', $transaction->sender_mobile) }}" class="btn btn-sm btn-outline-primary">
                        Update Profile
                    </a>
                </div>
                <button class="btn-close" aria-label="Close" onclick="hideNotification()"></button>
            </div>
        </div>
    </div>

  
   <div id="loginPrompt" class="loginPrompt position-fixed bottom-0 end-0 m-4" style="z-index: 1055; max-width: 360px;">
        <div class="border rounded-3 shadow-lg bg-light p-3 d-flex gap-3 align-items-start">
            <div class="mt-1 text-success">
                <i class="fas fa-user-check fs-3"></i>
            </div>
            @if(Auth::User())
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">You're All Set</h6>
                <p class="small text-muted mb-2">
                    Okay, you're logged in! Feel free to head over to your dashboard to view your past transactions.
                </p>
               <a target="new" href="{{ route('user.dashboard') }}" class="btn btn-success">
                    <i class="fas fa-dashboard me-1"></i> Dashboard
               </a>
            </div>
            <button type="button" class="btn-close" onclick="document.getElementById('loginPrompt').remove();" aria-label="Close"></button>
            @else
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">You're All Set</h6>
                <p class="small text-muted mb-2">
                    You don’t need to log in, but since you have an account, you can view your past transactions anytime.
                </p>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-sign-in-alt me-1"></i> Log In
                    </button>
                </div>
            <button type="button" class="btn-close" onclick="document.getElementById('loginPrompt').remove();" aria-label="Close"></button>
            @endif
        </div>
    </div>


    {{-- Login Popup --}}
    {{-- <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="loginModalLabel"><i class="fas fa-lock me-1"></i> Secure Login</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form id="customLoginForm">
                    @csrf
                    <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                    <a href="{{ route('password.request') }}" class="text-muted small">Forgot password?</a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="customLoginForm" class="modal-content border-0 shadow"> {{-- moved here --}}
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="loginModalLabel"><i class="fas fa-lock me-1"></i> Secure Login</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" value="" name="email" id="email" class="form-control" autocomplete="off" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" value="" name="password" id="password" class="form-control" autocomplete="off" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>
                    <div id="loginError" class="text-danger small mb-2 d-none">Invalid credentials. Please try again.</div>
                </div>

                <div class="modal-footer justify-content-between">
                    <a href="{{ route('password.request') }}" class="text-muted small">Forgot password?</a>
                    <button type="submit" class="btn btn-success" id="loginBtn">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- Raise Dispute Button with Glow Effect --}}
    <div class="raise-dispute-glow position-fixed bottom-0 end-0 m-4">
        <button class="btn btn-danger">
            <i class="fas fa-exclamation-triangle me-1"></i> Raise Dispute
        </button>
    </div>

    {{--  --}}
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
    <?php
        //Check if the user phone number is in users table phone column
        $User = DB::table('users')->where('phone', $transaction->sender_mobile)->first();
        //if user is available then show the update notification
    ?>
    @if(!$User)
    <script>
        function showNotification() {
            document.getElementById('updateNotification').classList.add('show');
            // Hide notification after 10 seconds
            setTimeout(hideNotification, 10000);
        }

        function hideNotification() {
            document.getElementById('updateNotification').classList.remove('show');
        }

        // Show notification after 3 seconds (or conditionally)
        window.onload = function() {
            setTimeout(showNotification, 3000);
        };
    </script>
    @else
        <script>
            function showLogin() {
                document.getElementById('loginPrompt').classList.add('show');
                // Hide login prompt after 10 seconds
                setTimeout(hideLogin, 10000);
            }

            function hideLogin() {
                document.getElementById('loginPrompt').classList.remove('show');
            }

            // Show login prompt after 3 seconds (or conditionally)
            window.onload = function() {
                setTimeout(showLogin, 3000);
            };
        </script>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#customLoginForm').on('submit', function (e) {
                e.preventDefault();

                let btn = $('#loginBtn');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Logging in...');

                $.ajax({
                    url: '{{ route("custom.login") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function () {
                        location.reload();
                    },
                    error: function () {
                        $('#loginError').removeClass('d-none');
                    },
                    complete: function () {
                        btn.prop('disabled', false).html('<i class="fas fa-sign-in-alt me-1"></i> Login');
                    }
                });
            });
        });
    </script>



@endsection
