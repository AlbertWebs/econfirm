<!-- resources/views/dashboard/approve.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Transaction - e-confirm</title>
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
        
    
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Approve Transaction</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Enter full name" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" name="phone" id="phone" class="form-control" placeholder="e.g. +254712345678" value="{{ $sender_mobile }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="company" class="form-label">Company</label>
                            <input type="text" name="company" id="company" class="form-control" placeholder="Your Company" value="{{ old('company') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="street" class="form-label">Street Address</label>
                            <input type="text" name="street" id="street" class="form-control" placeholder="e.g. Prestige Plaza" value="{{ old('street') }}">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="city" class="form-label">City</label>
                            <input type="text" name="city" id="city" class="form-control" placeholder="e.g. Nairobi" value="{{ old('city') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="state" class="form-label">State</label>
                            <input type="text" name="state" id="state" class="form-control" placeholder="e.g. Nairobi" value="{{ old('state') }}">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="zip" class="form-label">Zip Code</label>
                            <input type="text" name="zip" id="zip" class="form-control" placeholder="e.g. 00100" value="{{ old('zip') }}">
                        </div>
                    </div>

                    <!-- Add a placeholder for messages -->
                    <div id="profileUpdateMsg" class="mt-3"></div>

                    <button type="submit" class="btn btn-outline-success w-100">
                        <i class="fas fa-save me-1"></i> Save Profile Details
                    </button>
                </form>
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
    <script>
        $('#profileUpdateForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let action = form.attr('action');
            let button = form.find('button[type=submit]');
            let msgBox = $('#profileUpdateMsg');

            button.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Saving...'
            );

            $.ajax({
                url: action,
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    msgBox.html(`<div class="alert alert-success">${response.message}</div>`);
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
                    button.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Profile Details');
                }
            });
        });
    </script>

</body>
</html>
