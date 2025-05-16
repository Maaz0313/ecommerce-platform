@extends('layouts.app')

@section('title', 'Welcome to Our E-Commerce Platform')

@section('content')
    <!-- Hero Section -->
    <div class="bg-light py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2">
                    <div class="text-center">
                        <img src="https://via.placeholder.com/600x400.png?text=Shop+With+Us" class="img-fluid rounded shadow"
                            alt="Shop with us">
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1 py-4">
                    <h1 class="display-4 fw-bold mb-4">Welcome to Our E-Commerce Platform</h1>
                    <p class="lead mb-4">Your one-stop shop for quality products at competitive prices.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i> Browse Products
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-envelope me-2"></i> Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Featured Categories -->
        <div class="mb-5">
            <h2 class="text-center mb-4 position-relative pb-2">
                <span class="position-relative">Shop by Category
                    <span class="position-absolute start-0 bottom-0 w-100"
                        style="height: 2px; background-color: #4361ee;"></span>
                </span>
            </h2>

            <div class="row">
                @foreach ($categories ?? [] as $category)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm product-card" tabindex="0">
                            <div class="position-absolute top-0 end-0 p-2">
                                <i class="fas fa-external-link-alt text-white bg-primary p-1 rounded-circle"
                                    style="font-size: 10px;"></i>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-folder-open fa-3x text-primary mb-3"></i>
                                <h3 class="card-title h5">{{ $category->name }}</h3>
                                <p class="card-text">{{ Str::limit($category->description, 80) }}</p>
                                <a href="{{ route('categories.show', $category->slug) }}"
                                    class="btn btn-outline-primary stretched-link">
                                    Browse {{ $category->name }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Featured Products -->
        <div class="mb-5">
            <h2 class="text-center mb-4 position-relative pb-2">
                <span class="position-relative">Featured Products
                    <span class="position-absolute start-0 bottom-0 w-100"
                        style="height: 2px; background-color: #4361ee;"></span>
                </span>
            </h2>

            <div class="row">
                @forelse($featuredProducts ?? [] as $product)
                    <div class="col-md-3 mb-4">
                        <x-product-card :product="$product" />
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">No featured products available.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Promotional Banner -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="bg-primary text-white p-4 rounded-3 shadow">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3>Free Shipping on Orders Over $50</h3>
                            <p class="mb-md-0">Shop now and enjoy free shipping on all orders over $50. Limited time offer!
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('products.index') }}" class="btn btn-light">Shop Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose Us -->
        <div class="mb-5">
            <h2 class="text-center mb-4 position-relative pb-2">
                <span class="position-relative">Why Choose Us
                    <span class="position-absolute start-0 bottom-0 w-100"
                        style="height: 2px; background-color: #4361ee;"></span>
                </span>
            </h2>

            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                            <h5>Fast Delivery</h5>
                            <p class="mb-0">Quick and reliable shipping to your doorstep</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                            <h5>Secure Payment</h5>
                            <p class="mb-0">Multiple safe payment methods available</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <i class="fas fa-undo fa-3x text-primary mb-3"></i>
                            <h5>Easy Returns</h5>
                            <p class="mb-0">30-day return policy for all products</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                            <h5>24/7 Support</h5>
                            <p class="mb-0">Round-the-clock customer service</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
