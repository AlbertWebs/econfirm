<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – e-confirm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #18743c;
            color: white;
            padding-top: 1rem;
            position: fixed;
            width: 240px;
        }
        .sidebar a {
            color: #dcdcdc;
            padding: 10px 20px;
            display: block;
            text-decoration: none;
            transition: background 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #145e33;
            color: white;
        }
        .header {
            height: 60px;
            background-color: #ffffff;
            border-bottom: 1px solid #ddd;
            margin-left: 240px;
            display: flex;
            align-items: center;
            padding: 0 20px;
            justify-content: space-between;
        }
        .content {
            margin-left: 240px;
            padding: 20px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h5 class="text-center mb-4"><i class="bi bi-shield-lock-fill"></i> e-confirm</h5>
        <a href="{{ route('admin.dashboard') }}" class="active"><i class="bi bi-house-door me-2"></i> Dashboard</a>
        <a href="#"><i class="bi bi-people-fill me-2"></i> Users</a>
        <a href="#"><i class="bi bi-lock-fill me-2"></i> Escrow Transactions</a>
        <a href="#"><i class="bi bi-bar-chart-fill me-2"></i> Reports</a>
        <a href="#"><i class="bi bi-gear-fill me-2"></i> Settings</a>
        <a href="{{ route('logout') }}" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
           <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

    <!-- Header -->
    <div class="header">
        <div>
            <strong>Admin Panel</strong>
        </div>
        <div>
            <span class="me-3"><i class="bi bi-person-circle"></i> {{ Auth::user()->name }}</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
