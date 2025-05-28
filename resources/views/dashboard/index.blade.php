<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureEscrow - Customer Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('theme/dashboard.css') }}" rel="stylesheet">
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
                <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                    Documents
                </button>
            </li>
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
                        <h2 class="card-title">Welcome back, John Smith!</h2>
                        <p class="card-text text-white-50">Manage your escrow transactions securely and efficiently.</p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-title text-muted">Active Transactions</h6>
                                        <h2 class="fw-bold">3</h2>
                                        <small class="text-muted">Currently in progress</small>
                                    </div>
                                    <i class="fas fa-clock text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-title text-muted">Completed</h6>
                                        <h2 class="fw-bold">12</h2>
                                        <small class="text-muted">Successfully closed</small>
                                    </div>
                                    <i class="fas fa-shield-alt text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-title text-muted">Total Value</h6>
                                        <h2 class="fw-bold">$125,000</h2>
                                        <small class="text-muted">In escrow transactions</small>
                                    </div>
                                    <i class="fas fa-dollar-sign text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                    </div>
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

                        <!-- Transactions List -->
                        <div class="transaction-item border rounded p-4 mb-3">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="fw-semibold mb-0 me-3">Real Estate Purchase - 123 Main St</h6>
                                        <span class="badge bg-success me-2">completed</span>
                                        <span class="badge bg-primary">buy</span>
                                    </div>
                                    <div class="row text-muted small">
                                        <div class="col-sm-4"><strong>ID:</strong> ESC-2024-001</div>
                                        <div class="col-sm-4"><i class="fas fa-calendar me-1"></i> Jan 15, 2024</div>
                                        <div class="col-sm-4">Counterparty: Jane Doe Properties</div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span>Progress</span>
                                            <span>100%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 text-lg-end">
                                    <div class="mb-3">
                                        <h3 class="fw-bold">$450,000</h3>
                                        <small class="text-muted">Escrow Amount</small>
                                    </div>
                                    <div class="d-flex flex-column flex-lg-row gap-2">
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> View
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-download me-1"></i> Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="transaction-item border rounded p-4 mb-3">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <h6 class="fw-semibold mb-0 me-3">Vehicle Sale - 2023 Tesla Model S</h6>
                                        <span class="badge bg-primary me-2">in-progress</span>
                                        <span class="badge bg-warning">sell</span>
                                    </div>
                                    <div class="row text-muted small">
                                        <div class="col-sm-4"><strong>ID:</strong> ESC-2024-002</div>
                                        <div class="col-sm-4"><i class="fas fa-calendar me-1"></i> Jan 20, 2024</div>
                                        <div class="col-sm-4">Counterparty: Mike Johnson</div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span>Progress</span>
                                            <span>65%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: 65%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 text-lg-end">
                                    <div class="mb-3">
                                        <h3 class="fw-bold">$75,000</h3>
                                        <small class="text-muted">Escrow Amount</small>
                                    </div>
                                    <div class="d-flex flex-column flex-lg-row gap-2">
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> View
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-download me-1"></i> Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#billing-info">Billing</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="personal-info">
                                <form>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" value="John Smith">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" class="form-control" value="john.smith@email.com">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control" placeholder="+1 (555) 123-4567">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Company</label>
                                            <input type="text" class="form-control" placeholder="Your company name">
                                        </div>
                                    </div>

                                    <h6 class="mb-3">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        Address Information
                                    </h6>
                                    
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Street Address</label>
                                            <input type="text" class="form-control" placeholder="123 Main Street">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" placeholder="San Francisco">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">State</label>
                                            <input type="text" class="form-control" placeholder="CA">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">ZIP Code</label>
                                            <input type="text" class="form-control" placeholder="94105">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="security-settings">
                                <div class="mb-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-key me-2"></i>
                                        Password & Authentication
                                    </h6>
                                    
                                    <form class="mb-4">
                                        <div class="mb-3">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control">
                                        </div>
                                        <button type="submit" class="btn btn-outline-primary">Update Password</button>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>