@extends('layouts.app')

@section('title', 'Order History')

@section('content')
    <div class="container">
        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 100px; height: 100px">
                                <span class="display-4">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <h4>{{ auth()->user()->name }}</h4>
                        <p class="text-muted">{{ auth()->user()->email }}</p>
                        <p>
                            <span
                                class="badge bg-primary">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Customer' }}</span>
                        </p>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Profile Information
                        </a>
                        <a href="{{ route('orders.history') }}" class="list-group-item list-group-item-action active">
                            <i class="fas fa-history me-2"></i> Order History
                        </a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-cogs me-2"></i> Admin Dashboard
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order History Content -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order History</h5>
                    </div>
                    <div class="card-body">
                        @if ($orders->isEmpty())
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-shopping-bag fa-4x text-muted"></i>
                                </div>
                                <h5>No orders yet</h5>
                                <p class="text-muted">You haven't placed any orders yet.</p>
                                <a href="{{ route('products.index') }}" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i> Shop Now
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td>{{ $order->id }}</td>
                                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <span
                                                        class="badge
                                                        @if ($order->status == 'delivered') bg-success
                                                        @elseif($order->status == 'shipped') bg-info
                                                        @elseif($order->status == 'preparing_for_shipment') bg-primary
                                                        @elseif($order->status == 'cancelled') bg-danger
                                                        @else bg-warning text-dark @endif">
                                                        @if ($order->status == 'order_received')
                                                            Order Received
                                                        @elseif ($order->status == 'preparing_for_shipment')
                                                            Preparing for Shipment
                                                        @else
                                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>₨{{ number_format($order->total_amount, 2) }}</td>
                                                <td>
                                                    <a href="{{ route('orders.show', $order->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        View
                                                    </a>

                                                    @php
                                                        $cancellableStatuses = [
                                                            'order_received',
                                                            'preparing_for_shipment',
                                                        ];
                                                    @endphp

                                                    @if (in_array($order->status, $cancellableStatuses))
                                                        <form action="{{ route('orders.cancel', $order->id) }}"
                                                            method="POST" class="d-inline ms-1">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Are you sure you want to cancel this order? This action cannot be undone.')">
                                                                Cancel
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-4">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
