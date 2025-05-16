@extends('layouts.app')

@section('title', 'Order Details')

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

            <!-- Order Details Content -->
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Order #{{ $order->id }}</h5>
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
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Customer Information</h6>
                                <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                                <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                                <p><strong>Phone:</strong> {{ $order->customer_phone ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Order Details</h6>
                                <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                                <p><strong>Payment Method:</strong>
                                    @if ($order->payment_method == 'cod')
                                        Cash on Delivery
                                    @elseif ($order->payment_method == 'stripe')
                                        Credit/Debit Card
                                    @else
                                        {{ ucfirst($order->payment_method) }}
                                    @endif
                                </p>
                                <p><strong>Payment Status:</strong>
                                    @if ($order->payment_status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif ($order->payment_status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif ($order->payment_status == 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @else
                                        <span class="badge bg-secondary">Not Applicable</span>
                                    @endif
                                </p>
                                <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>
                            </div>
                        </div>

                        <hr>

                        <h6>Order Items</h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderItems as $item)
                                        <tr>
                                            <td>{{ $item->product_name }}</td>
                                            <td>₨{{ number_format($item->price, 2) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td class="text-end">₨{{ number_format($item->price * $item->quantity, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Subtotal:</th>
                                        <th class="text-end">₨{{ number_format($order->subtotal_amount, 2) }}</th>
                                    </tr>
                                    @if ($order->discount_amount > 0)
                                        <tr>
                                            <th colspan="3" class="text-end text-success">
                                                Discount ({{ $order->coupon_code }}):
                                            </th>
                                            <th class="text-end text-success">
                                                -₨{{ number_format($order->discount_amount, 2) }}</th>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th class="text-end">₨{{ number_format($order->total_amount, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @if ($order->notes)
                            <div class="mt-4">
                                <h6>Order Notes</h6>
                                <p>{{ $order->notes }}</p>
                            </div>
                        @endif

                        @if ($order->payment_method == 'stripe' && $order->payment_status == 'pending')
                            <div class="mt-4">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Payment Required</h6>
                                    <p class="mb-0">Your order has been placed but payment is still pending. Please
                                        complete your payment to process your order.</p>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('payments.process', $order->id) }}" class="btn btn-success">
                                        <i class="fas fa-credit-card me-2"></i> Pay Now
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('orders.history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Order History
                        </a>

                        @php
                            $cancellableStatuses = ['order_received', 'preparing_for_shipment'];
                        @endphp

                        @if (in_array($order->status, $cancellableStatuses))
                            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline ms-2">
                                @csrf
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to cancel this order? This action cannot be undone.')">
                                    <i class="fas fa-times-circle me-2"></i> Cancel Order
                                </button>
                            </form>
                        @endif
                    </div>

                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-cart me-2"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
