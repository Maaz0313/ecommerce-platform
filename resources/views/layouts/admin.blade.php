<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background: #343a40;
            color: #fff;
            transition: all 0.3s;
        }

        #content {
            width: 100%;
            padding: 20px;
        }

        .sidebar-link {
            padding: 10px 15px;
            display: block;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: #495057;
        }

        .sidebar-header {
            padding: 20px;
            background: #212529;
        }

        /* Dropdown menu styles */
        .dropdown-menu {
            min-width: 200px;
            max-height: none !important;
            overflow: visible !important;
        }

        .dropdown-item {
            padding: 8px 16px;
        }

        .dropdown-item.active,
        .dropdown-item:active {
            background-color: #f8f9fa;
            color: #212529;
        }

        /* Fix for table layout */
        .table-responsive {
            overflow: visible !important;
        }

        .card {
            overflow: visible !important;
        }

        /* Fix for dropdown positioning */
        .dropdown {
            position: relative;
        }
    </style>
    @yield('styles')
</head>

<body>
    <div id="sidebar">
        <div class="sidebar-header">
            <h3>Admin Panel</h3>
        </div>
        <ul class="list-unstyled">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.categories.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags me-2"></i> Categories
                </a>
            </li>
            <li>
                <a href="{{ route('admin.products.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="fas fa-box me-2"></i> Products
                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }} d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-shopping-cart me-2"></i> Orders</span>
                    @if (isset($pendingOrderCount) && $pendingOrderCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $pendingOrderCount }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('admin.coupons.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt me-2"></i> Coupons
                </a>
            </li>
            <li>
                <a href="{{ route('home') }}" class="sidebar-link">
                    <i class="fas fa-home me-2"></i> Back to Site
                </a>
            </li>
        </ul>
    </div>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container-fluid">
                <h1 class="fs-4 mb-0">@yield('title', 'Dashboard')</h1>
                <div class="ms-auto">
                    <span class="me-3">{{ Auth::guard('admin')->user()->name ?? 'Admin User' }}</span>
                    <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>
    </div> <!-- jQuery and jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/dropdown-test.js') }}"></script>
    <script src="{{ asset('js/order-status.js') }}"></script>
    @yield('scripts')
</body>

</html>
