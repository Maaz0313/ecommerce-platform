@extends('layouts.app')

@section('title', 'About Us')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="display-4 mb-4">About Our Ecommerce Platform</h1>
                <p class="lead">We're dedicated to providing quality products and exceptional service to our customers.</p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-6">
                <h2>Our Story</h2>
                <p>Founded in 2023, our ecommerce platform was created with a simple mission: to make quality products
                    accessible to everyone at competitive prices. What started as a small online store has grown into a
                    comprehensive marketplace offering a wide range of products across multiple categories.</p>
                <p>We believe in the power of technology to transform the shopping experience, making it more convenient,
                    personalized, and enjoyable for customers around the world.</p>
            </div>
            <div class="col-md-6">
                <div class="bg-light p-4 rounded shadow-sm h-100 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <i class="fas fa-store fa-5x text-primary mb-3"></i>
                        <h3>Trusted by Thousands</h3>
                        <p class="mb-0">Over 10,000 happy customers and counting!</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2>Our Values</h2>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-gem fa-3x text-primary mb-3"></i>
                        <h3 class="card-title h4">Quality</h3>
                        <p class="card-text">We carefully select and curate our products to ensure only the highest quality
                            items are available to our customers.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h3 class="card-title h4">Trust</h3>
                        <p class="card-text">We believe in building long-term relationships with our customers based on
                            trust, transparency, and reliability.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                        <h3 class="card-title h4">Service</h3>
                        <p class="card-text">Our dedicated customer service team is always ready to help with any questions
                            or concerns you may have.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2>Meet Our Team</h2>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 100px; height: 100px;">
                            <span class="h1">JD</span>
                        </div>
                        <h3 class="card-title h5">John Doe</h3>
                        <p class="card-text text-muted">Founder & CEO</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 100px; height: 100px;">
                            <span class="h1">JS</span>
                        </div>
                        <h3 class="card-title h5">Jane Smith</h3>
                        <p class="card-text text-muted">Chief Operations Officer</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 100px; height: 100px;">
                            <span class="h1">RJ</span>
                        </div>
                        <h3 class="card-title h5">Robert Johnson</h3>
                        <p class="card-text text-muted">Chief Marketing Officer</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="rounded-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="width: 100px; height: 100px;">
                            <span class="h1">SW</span>
                        </div>
                        <h3 class="card-title h5">Sarah Williams</h3>
                        <p class="card-text text-muted">Customer Service Manager</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-envelope me-2"></i> Get in Touch
                </a>
            </div>
        </div>
    </div>
@endsection
