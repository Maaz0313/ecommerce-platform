<!DOCTYPE html>
<html lang="en" style="margin:0; padding:0; height:100%; width:100%; overflow-x:hidden;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ecommerce Platform')</title>
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script> <!-- Layout fixes -->
    <link rel="stylesheet" href="{{ asset('css/layout-fixes.css') }}">
    <!-- Cart buttons styling -->
    <link rel="stylesheet" href="{{ asset('css/cart-buttons.css') }}">
    <!-- Enhanced cart buttons styling -->
    <link rel="stylesheet" href="{{ asset('css/enhanced-cart-buttons.css') }}">
    <!-- Product card fixes -->
    <link rel="stylesheet" href="{{ asset('css/product-card-fixes.css') }}">
    <!-- Custom CSS -->
    <style>
        /* Body structure styling moved to layout-fixes.css */
        .main-content {
            flex: 1 0 auto;
            display: flex;
            flex-direction: column;
        }

        .footer {
            flex-shrink: 0;
            margin-top: 0;
            padding-bottom: 0;
        }

        /* Product card styling moved to product-card-fixes.css */

        .navbar-brand {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
        }

        .btn-primary:hover {
            background-color: #3a56d4;
            border-color: #3a56d4;
        }
    </style>
    @yield('styles')
</head>

<body style="margin:0; padding:0; min-height:100vh; display:flex; flex-direction:column; overflow-x:hidden;">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name', 'Ecommerce Platform') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Products</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Categories
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                            @foreach ($navCategories as $category)
                                <li>
                                    <a class="dropdown-item" href="{{ route('categories.show', $category->slug) }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('coupons.index') }}">
                            <i class="fas fa-ticket-alt me-1"></i> Coupons
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">Contact Us</a>
                    </li>
                </ul>
                <form class="d-flex me-3" action="{{ route('products.index') }}" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search products..."
                        value="{{ request('search') }}" aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        @php
                            $cartItems = session()->get('cart', []);
                            $cartCount = 0;
                            foreach ($cartItems as $item) {
                                $cartCount += $item['quantity'];
                            }
                        @endphp
                        <a class="nav-link" href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <span id="cart-badge"
                                class="badge bg-danger {{ $cartCount > 0 ? '' : 'd-none' }}">{{ $cartCount }}</span>
                        </a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i> Register
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="fas fa-user-circle me-1"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('orders.history') }}">
                                        <i class="fas fa-history me-1"></i> Order History
                                    </a>
                                </li>
                                @if (Auth::user()->isAdmin())
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-cogs me-1"></i> Admin Dashboard
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <div class="main-content container"
        style="flex: 1 0 auto; margin-bottom: 0 !important; padding-bottom: 0 !important;">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div> <!-- Footer -->
    <footer class="footer bg-dark text-white w-100 vw-100 mt-0 pt-2 pb-1"
        style="margin-top: 0 !important; padding-top: 1rem !important; padding-bottom: 0.5rem !important;">
        <div class="container px-4 py-0 my-0">
            <div class="row gy-2">
                <div class="col-md-6">
                    <h5 class="mb-1">{{ config('app.name', 'Ecommerce Platform') }}</h5>
                    <p class="mb-2">A simple ecommerce platform for small businesses.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('products.index') }}" class="text-white">Products</a></li>
                        <li><a href="{{ route('coupons.index') }}" class="text-white">Coupons</a></li>
                        <li><a href="{{ route('cart.index') }}" class="text-white">Cart</a></li>
                        <li><a href="{{ route('contact') }}" class="text-white">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> info@example.com</li>
                        <li><i class="fas fa-phone me-2"></i> +1 123 456 7890</li>
                    </ul>
                </div>
            </div>
            <hr class="my-1">
            <div class="text-center py-1">
                <small class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Ecommerce Platform') }}. All
                    rights reserved.</small>
            </div>
        </div>
    </footer> <!-- Bootstrap 5.3 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Product card interaction script -->
    <script src="{{ asset('js/product-card.js') }}"></script>
    <!-- Cart handling functionality -->
    <script src="{{ asset('js/cart-handler.js') }}"></script>
    @yield('scripts')
</body>

</html>
