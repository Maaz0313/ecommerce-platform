@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h3 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Access Denied</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-lock fa-5x text-danger mb-3"></i>
                            <h4>You do not have permission to access this page</h4>
                            <p class="text-muted">This area is restricted to authorized administrators only.</p>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-home me-1"></i> Return to Home Page
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-sign-in-alt me-1"></i> Log in with Different Account
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
