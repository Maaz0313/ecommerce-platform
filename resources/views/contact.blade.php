@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
    <div class="row mb-5">
        <div class="col-md-8 mx-auto">
            <h1 class="mb-4">Contact Us</h1>

            @if (session('message_sent'))
                <div class="alert alert-success">
                    Thank you for your message! We'll get back to you as soon as possible.
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject"
                                name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5"
                                required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-map-marker-alt fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Visit Us</h5>
                    <p class="card-text">
                        123 Ecommerce Street<br>
                        New York, NY 10001<br>
                        United States
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-envelope fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Email Us</h5>
                    <p class="card-text">
                        <a href="mailto:info@example.com" class="text-decoration-none">info@example.com</a><br>
                        <a href="mailto:support@example.com" class="text-decoration-none">support@example.com</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-phone-alt fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title">Call Us</h5>
                    <p class="card-text">
                        +1 (123) 456-7890<br>
                        +1 (987) 654-3210
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
