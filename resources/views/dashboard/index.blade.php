<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureEscrow - Customer Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('theme/dashboard.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('uploads/favicon.png') }}">
     <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Font Awesome Free CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-light">
    <!-- Header -->
    <header class="bg-white border-bottom shadow-sm">
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
                    {{-- <button class="btn btn-outline-secondary btn-sm me-3">
                        <i class="fas fa-bell me-1"></i>
                        Notifications
                    </button> --}}
                    <div class="d-flex align-items-center me-3">
                       
                        <span class="fw-medium">{{Auth::User()->name}}</span>
                    </div>
                    <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                    {{--  --}}
                  
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                    {{--  --}}
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid py-4">
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="portalTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button" role="tab">
                    Dashboard
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab">
                    Transactions
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="create-transaction-tab" data-bs-toggle="tab" data-bs-target="#create-transaction" type="button" role="tab">
                    Create Transaction
                </button>
            </li>
            {{-- <li class="nav-item" role="presentation">
                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                    Documents
                </button>
            </li> --}}
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                    Profile
                </button>
            </li>
        </ul>

        <div class="tab-content" id="portalTabsContent">
            <!-- Dashboard Tab -->
            <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                <!-- Welcome Section -->
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <h2 class="card-title">Welcome back, {{Auth::User()->name}}!</h2>
                        <p class="card-text text-white-50">Manage your escrow transactions securely and efficiently.</p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-title text-muted">Active Transactions</h6>
                                        <h2 class="fw-bold">{{$AllPendingTransactionsCount}}</h2>
                                        <small class="text-muted">Currently in progress</small>
                                    </div>
                                    <i class="fas fa-clock text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-title text-muted">Completed</h6>
                                        <h2 class="fw-bold">{{$AllCompletedTransactionsCount}}</h2>
                                        <small class="text-muted">Successfully closed</small>
                                    </div>
                                    <i class="fas fa-shield-alt text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-title text-muted">Total Value</h6>
                                        <h2 class="fw-bold">kes {{$AllPendingTransactionsAmount}}</h2>
                                        <small class="text-muted">In escrow transactions</small>
                                    </div>
                                    <i class="fas fa-dollar-sign text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-title text-muted">Pending Documents</h6>
                                        <h2 class="fw-bold">2</h2>
                                        <small class="text-muted">Require your attention</small>
                                    </div>
                                    <i class="fas fa-file-text text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center p-3 bg-success bg-opacity-10 rounded mb-3">
                            <div class="bg-success rounded-circle me-3" style="width: 8px; height: 8px;"></div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">Transaction ESC-2024-001 completed</div>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                            <span class="badge bg-success">Completed</span>
                        </div>
                        
                        <div class="d-flex align-items-center p-3 bg-primary bg-opacity-10 rounded mb-3">
                            <div class="bg-primary rounded-circle me-3" style="width: 8px; height: 8px;"></div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">Document uploaded for ESC-2024-003</div>
                                <small class="text-muted">1 day ago</small>
                            </div>
                            <span class="badge bg-primary">Updated</span>
                        </div>
                        
                        <div class="d-flex align-items-center p-3 bg-warning bg-opacity-10 rounded">
                            <div class="bg-warning rounded-circle me-3" style="width: 8px; height: 8px;"></div>
                            <div class="flex-grow-1">
                                <div class="fw-medium">ESC-2024-002 waiting for buyer signature</div>
                                <small class="text-muted">2 days ago</small>
                            </div>
                            <span class="badge bg-warning">Pending</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="create-transaction" role="tabpanel">
                <!-- Welcome Section -->
                
                <style>
                /* Form Styles */
                .hero-form {
                    background: white;
                    border-radius: 0.75rem;
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                    border: 3px solid #18743c;
                }

                .form-container {
                    padding: 1.5rem;
                }

                .form-container h3 {
                    text-align: center;
                    margin-bottom: 1.5rem;
                    font-size: 1.25rem;
                }

                .transaction-form {
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                }

                .form-group {
                    display: flex;
                    flex-direction: column;
                }

                .form-group label {
                    font-weight: 500;
                    color: #374151;
                    margin-bottom: 0.25rem;
                    font-size: 0.875rem;
                }

                .form-group input,
                .form-group select,
                .form-group textarea {
                    padding: 0.5rem 0.75rem;
                    border: 1px solid #d1d5db;
                    border-radius: 0.375rem;
                    font-size: 0.875rem;
                    transition: border-color 0.2s, box-shadow 0.2s;
                }

                .form-group input:focus,
                .form-group select:focus,
                .form-group textarea:focus {
                    outline: none;
                    border-color: #18743c;
                    box-shadow: 0 0 0 3px rgba(24, 116, 60, 0.1);
                }

                .input-with-prefix {
                    position: relative;
                }

                .prefix {
                    position: absolute;
                    left: 0.75rem;
                    top: 50%;
                    transform: translateY(-50%);
                    color: #000000;
                    font-size: 0.875rem;
                }

                .input-with-prefix input {
                    padding-left: 3rem;
                }

                .form-row {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 1rem;
                }

                .form-disclaimer {
                    font-size: 0.75rem;
                    color: #000000;
                    text-align: center;
                    margin-top: 0.5rem;
                }

                .form-disclaimer a {
                    color: #18743c;
                    text-decoration: none;
                }

                .form-disclaimer a:hover {
                    text-decoration: underline;
                }

                </style>
                {{--  --}}
                <div class="hero-form animate-on-scroll" style="max-width: 600px; margin: 0 auto; ">
                    <div class="form-container">
                        <h3>Start a Secure Transaction</h3>
                        <form class="transaction-form">
                            <div class="form-group">
                                <label for="transaction-type">Transaction Type</label>
                                <select id="transaction-type" name="transaction-type">
                                    <option value="">Select a transaction type</option>
                                    <option value="ecommerce">E-commerce Marketplace Transactions</option>
                                    <option value="services">Professional Services (Consulting, Legal, Accounting)</option>
                                    <option value="real-estate">Real Estate (Land, Plots, Rentals)</option>
                                    <option value="vehicle">Vehicle Sales (Cars, Motorbikes, Trucks)</option>
                                    <option value="business">Business Transfers & Partnerships</option>
                                    <option value="freelance">Freelance Work & Digital Services</option>
                                    <option value="goods">High-Value Goods (Electronics, Machinery, Furniture)</option>
                                    <option value="construction">Construction & Renovation Projects</option>
                                    <option value="agriculture">Agricultural Produce & Equipment</option>
                                    <option value="legal">Legal Settlements & Compensation</option>
                                    <option value="import-export">Import/Export Transactions</option>
                                    <option value="tenders">Government or Corporate Tender Payments</option>
                                    <option value="education">Education Payments (International Tuition, School Fees)</option>
                                    <option value="personal">Personal Loans & Informal Lending</option>
                                    <option value="crypto">Crypto & Forex Trading Agreements</option>
                                    <option value="rentals">Equipment & Property Rentals</option>   
                                    <option value="charity">Charity Donations & Fundraising</option>
                                    <option value="events">Event Ticket Sales & Bookings</option>
                                    <option value="subscriptions">Subscription Services (Software, Memberships)</option>
                                    <option value="affiliate">Affiliate Marketing Payments</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-group" id="custom-transaction-type-group" style="display:none;">
                                <label for="custom-transaction-type">Specify Transaction Type</label>
                                <input type="text" id="custom-transaction-type" name="custom-transaction-type" placeholder="Enter custom transaction type" class="w-100">
                            </div>
                            
                            {{-- <div class="form-group">
                                <label for="transaction-amount">Transaction Amount</label>
                                <div class="input-with-prefix">
                                    <span class="prefix">kes &nbsp;</span>
                                    <input type="number" id="transaction-amount" name="transaction-amount" placeholder="Amount" class="w-100">
                                </div>
                            </div> --}}

                            <div class="form-row">
                                
                                <div class="form-group">
                                    <label for="payment-method">Payment Method</label>
                                    <select id="payment-method" name="payment-method">
                                        <option value="mpesa">M-Pesa Number</option>
                                        <option value="paybill">Paybill/Buy Goods</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="transaction-amount">Transaction Amount</label>
                                    <div class="input-with-prefix">
                                        <span class="prefix">kes &nbsp;</span>
                                        <input type="number" id="transaction-amount" name="transaction-amount" placeholder="Amount" class="w-100">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="paybill-till-group" style="display:none;">
                                <label for="paybill-till-number">Buy Goods or Paybill Number</label>
                                <div>
                                    <input type="text" id="paybill-till-number" name="paybill-till-number" value="{{ old('paybill-till-number') }}" placeholder="Buy Goods or Paybill Number" class="w-100">
                                </div>
                            </div> 
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="sender-mobile">Your Mobile Number</label>
                                    <input type="tel" value="{{Auth::User()->phone}}" id="sender-mobile" name="sender-mobile" placeholder="+254723000000">
                                </div>
                                <div class="form-group">
                                    <label for="receiver-mobile">Recipient Mobile Number</label>
                                    <input type="tel" id="receiver-mobile" name="receiver-mobile" placeholder="+254723000000">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="transaction-details">Transaction Details</label>
                                <textarea id="transaction-details" name="transaction-details" rows="3" placeholder="Describe your transaction..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-full">
                                Fund Your Escrow
                                <svg style="vertical-align: middle; margin-left: 8px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                    <polyline points="12 5 19 12 12 19" />
                                </svg>
                            </button>
                            <p style="text-align:center; margin:0 auto; font-size:10px; display:none;" id="mpesa-response"></p>

                            <p class="form-disclaimer">
                                By submitting this form, you agree to our <a target="new" href="{{route('terms.conditions')}}">Terms of Service</a> and <a target="new" href="{{route('privacy.policy')}}">Privacy Policy</a>.
                            </p>
                        </form>
                    </div>
                </div>
                {{--  --}}
             
            </div>
            
            <!-- Transactions Tab -->
            <div class="tab-pane fade" id="transactions" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>
                            Transaction Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <div class="position-relative">
                                    <i class="fas fa-search position-absolute start-0 top-50 translate-middle-y ms-3 text-muted"></i>
                                    <input type="text" class="form-control ps-5" placeholder="Search transactions...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select">
                                    <option>All Status</option>
                                    <option>Pending</option>
                                    <option>In Progress</option>
                                    <option>Completed</option>
                                    <option>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        @foreach ($transactions as $transaction)
                        <!-- Transactions List -->
                        <div class="transaction-item border rounded p-4 mb-3">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="fw-semibold mb-0 me-3" style="text-transform: capitalize">{{$transaction->payment_method}} Transaction</h6>
                                        <small style="text-align: center; width:80%">
                                            {{ $transaction->transaction_details ?? 'No details provided' }}
                                        </small>
                                        <div style="position: relative; right: 0; top: 0;">
                                            {{-- <i class="fas fa-ellipsis-v text-muted"></i> --}}
                                            @if($transaction->status == 'Completed')
                                                <span class="badge bg-success me-2">{{$transaction->status}}</span>
                                            @elseif($transaction->status == 'pending')
                                                <span class="badge bg-warning me-2">{{$transaction->status}}</span>
                                            @elseif($transaction->status == 'stk_failed')
                                                <span class="badge bg-danger">{{$transaction->status}}</span>
                                            @elseif($transaction->status == 'stk_initiated')
                                                <span class="badge bg-info">{{$transaction->status}}</span>
                                            @else
                                                <span class="badge bg-primary me-2">{{$transaction->status}}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row text-muted small">
                                        <div class="col-sm-4"><strong>ID:</strong> {{$transaction->transaction_id}}</div>
                                        <div class="col-sm-4"><i class="fas fa-calendar me-1"></i> {{$transaction->created_at->format('M d, Y')}}</div>
                                        <div class="col-sm-4">Counterparty: {{$transaction->receiver_mobile}}</div>
                                    </div>
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
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span>Progress</span>
                                            <span>{{ $progress }}%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 text-lg-end">
                                    <div class="mb-3">
                                        <h3 class="fw-bold">kes {{$transaction->transaction_amount}}</h3>
                                        <small class="text-muted">Escrow Amount</small>
                                    </div>
                                    <div class="d-flex flex-column flex-lg-row gap-2">
                                        <a href="{{route('view.transaction', $transaction->id)}}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> View
                                        </a>
                                        <a href="{{ route('e-contract.print', $transaction->id) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-download me-1"></i> Export
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <!-- Documents Tab -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-file-text me-2"></i>
                            Document Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills mb-4">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#all-docs">All Documents</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#upload-docs">Upload New</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="all-docs">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" placeholder="Search documents...">
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-outline-primary">
                                            <i class="fas fa-download me-2"></i> Download All
                                        </button>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-pdf text-primary fs-2 me-2"></i>
                                                        <div>
                                                            <h6 class="card-title mb-0">Purchase Agreement.pdf</h6>
                                                            <small class="text-muted">Contract</small>
                                                        </div>
                                                    </div>
                                                    <span class="badge bg-success">approved</span>
                                                </div>
                                                <div class="small text-muted mb-3">
                                                    <div><i class="fas fa-calendar me-1"></i> Jan 20, 2024</div>
                                                    <div><i class="fas fa-user me-1"></i> John Smith</div>
                                                    <div>Size: 2.3 MB</div>
                                                    <div>Transaction: ESC-2024-001</div>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-outline-primary btn-sm flex-fill">
                                                        <i class="fas fa-eye me-1"></i> View
                                                    </button>
                                                    <button class="btn btn-outline-secondary btn-sm flex-fill">
                                                        <i class="fas fa-download me-1"></i> Download
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="upload-docs">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Upload New Document</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="border-2 border-dashed border-secondary rounded p-5 text-center mb-4">
                                            <i class="fas fa-upload fs-1 text-muted mb-3"></i>
                                            <h5>Drop files here or click to browse</h5>
                                            <p class="text-muted">Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (Max 10MB)</p>
                                            <input type="file" class="form-control d-none" id="fileUpload">
                                            <label for="fileUpload" class="btn btn-primary">Select File</label>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Document Type</label>
                                                <select class="form-select">
                                                    <option>Contract</option>
                                                    <option>Inspection Report</option>
                                                    <option>Financial Statement</option>
                                                    <option>Insurance Document</option>
                                                    <option>Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Transaction ID</label>
                                                <select class="form-select">
                                                    <option>ESC-2024-001</option>
                                                    <option>ESC-2024-002</option>
                                                    <option>ESC-2024-003</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Description (Optional)</label>
                                                <textarea class="form-control" rows="3" placeholder="Add any notes about this document..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Tab -->
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>
                            Profile Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills mb-4">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#personal-info">Personal</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#security-settings">Security</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#notification-settings">Notifications</button>
                            </li>
                            {{-- <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#billing-info">Billing</button>
                            </li> --}}
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="personal-info">
                                <form method="POST" action="{{ route('user.update') }}" id="profileForm">
                                    @csrf
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" name="name" value="{{Auth::User()->name}}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" name="email" value="{{Auth::User()->email}}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" name="phone" value="{{Auth::User()->phone}}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Company</label>
                                            <input type="text" class="form-control" name="company" value="{{Auth::User()->company}}">
                                        </div>
                                    </div>

                                    <h6 class="mb-3">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        Address Information
                                    </h6>
                                    
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Street Address</label>
                                            <input value="{{Auth::User()->street}}"  name="street" type="text" class="form-control" placeholder="Prestige Plaza, Ngong Road, Kilimani, Nairobi">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input value="{{Auth::User()->city}}"  name="city" type="text" class="form-control" placeholder="Nairobi">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">State</label>
                                            <input value="{{Auth::User()->state}}"  name="state" type="text" class="form-control" placeholder="Nairobi">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">ZIP Code</label>
                                            <input value="{{Auth::User()->zip}}"  name="zip" type="text" class="form-control" placeholder="00100">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        Save Changes
                                    </button>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="security-settings">
                                <div class="mb-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-key me-2"></i>
                                        Password & Authentication
                                    </h6>
                                    
                                    <form id="passwordUpdateForm" class="mb-4">
                                        @csrf

                                        <div class="mb-3">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" name="current_password" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" name="new_password" class="form-control" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Confirm New Password</label>
                                            <input type="password" name="new_password_confirmation" class="form-control" required>
                                        </div>

                                        <button type="submit" class="btn btn-outline-primary" id="updatePasswordBtn">
                                            Update Password
                                        </button>

                                        <div id="passwordUpdateMsg" class="mt-3"></div>
                                    </form>

                                </div>

                                <div class="border-top pt-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-shield-alt me-2"></i>
                                        Two-Factor Authentication
                                    </h6>
                                    
                                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                        <div>
                                            <div class="fw-medium">Enable Two-Factor Authentication</div>
                                            <small class="text-muted">Add an extra layer of security to your account</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="twoFactorSwitch">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="notification-settings">
                                <h6 class="mb-3">
                                    <i class="fas fa-bell me-2"></i>
                                    Notification Preferences
                                </h6>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                        <div>
                                            <div class="fw-medium">Email Notifications</div>
                                            <small class="text-muted">Receive updates about your transactions via email</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" checked>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                        <div>
                                            <div class="fw-medium">SMS Notifications</div>
                                            <small class="text-muted">Get important alerts via text message</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox">
                                        </div>
                                    </div>
                                </div>
                            </div>
{{-- 
                            <div class="tab-pane fade" id="billing-info">
                                <h6 class="mb-3">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Billing Information
                                </h6>
                                
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white px-2 py-1 rounded me-3 small fw-bold">VISA</div>
                                                <div>
                                                    <div class="fw-medium">•••• •••• •••• 4242</div>
                                                    <small class="text-muted">Expires 12/25</small>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="btn btn-outline-primary btn-sm me-2">Edit</button>
                                                <button class="btn btn-outline-danger btn-sm">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button class="btn btn-outline-primary w-100 mb-4">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Add Payment Method
                                </button>

                                <div class="border-top pt-4">
                                    <h6 class="mb-3">Billing History</h6>
                                    
                                    <div class="d-flex justify-content-between align-items-center p-3 border rounded mb-2">
                                        <div>
                                            <div class="fw-medium">Escrow Service Fee</div>
                                            <small class="text-muted">January 15, 2024</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-medium">$250.00</div>
                                            <button class="btn btn-link btn-sm p-0 text-primary">Download</button>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="formToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="formToastBody">
                    Success!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    {{--  --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#profileForm').submit(function(e) {
            e.preventDefault();

            let form = $(this);
            let actionUrl = form.attr('action');
            let formData = form.serialize();
            let submitBtn = $('#submitBtn');

            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving changes...');

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#formResponse').html(
                        `<div class="alert alert-success">Profile updated successfully.</div>`
                    );

                    // ✅ Show toast
                    $('#formToastBody').text('Profile updated successfully.');
                    let toast = new bootstrap.Toast(document.getElementById('formToast'));
                    toast.show();
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let message = '<div class="alert alert-danger"><ul>';
                    $.each(errors, function(key, val) {
                        message += `<li>${val}</li>`;
                    });
                    message += '</ul></div>';
                    $('#formResponse').html(message);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('Save Changes');
                }
            });
        });

        $('#passwordUpdateForm').on('submit', function(e) {
            e.preventDefault();

            let btn = $('#updatePasswordBtn');
            let msgBox = $('#passwordUpdateMsg');
            
            btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Updating...'
            );

            $.ajax({
                url: '{{ route("user.update-password") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    msgBox.html(`<div class="alert alert-success">${response.message}</div>`);
                    $('#passwordUpdateForm')[0].reset();

                    // ✅ Show toast
                    $('#formToastBody').text('Password updated successfully.');
                    let toast = new bootstrap.Toast(document.getElementById('formToast'));
                    toast.show();

                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 2000);
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let message = '<div class="alert alert-danger"><ul>';

                    if (errors) {
                        $.each(errors, function(key, val) {
                            message += `<li>${val}</li>`;
                        });
                    } else {
                        message += `<li>${xhr.responseJSON.message || 'An error occurred.'}</li>`;
                    }

                    message += '</ul></div>';
                    msgBox.html(message);
                },
                complete: function() {
                    btn.prop('disabled', false).html('Update Password');
                }
            });
        });
    </script>


   @include('dashboard.scripts')




    {{--  --}}
</body>
</html>