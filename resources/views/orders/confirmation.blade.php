@extends('layouts.app')

@section('title', 'Order Confirmation')

@section('content')
    <div class="text-center mb-5">
        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
        <h1 class="mt-3">Thank You for Your Order!</h1>
        <p class="lead">Your order has been placed successfully.</p>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Order Number:</h6>
                            <p class="mb-0">#{{ $order->id }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Order Date:</h6>
                            <p class="mb-0">{{ $order->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Customer Name:</h6>
                            <p class="mb-0">{{ $order->customer_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Email:</h6>
                            <p class="mb-0">{{ $order->customer_email }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Phone:</h6>
                            <p class="mb-0">{{ $order->customer_phone ?: 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Status:</h6>
                            <p class="mb-0">
                                <span class="badge bg-primary">{{ ucfirst($order->status) }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Payment Method:</h6>
                            <p class="mb-0">
                                @if ($order->payment_method == 'cod')
                                    Cash on Delivery
                                @else
                                    {{ ucfirst($order->payment_method) }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6>Shipping Address:</h6>
                        <p class="mb-0">{{ $order->shipping_address }}</p>
                    </div>

                    <h6>Order Items:</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderItems as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>${{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end">Subtotal:</td>
                                    <td>${{ number_format($order->subtotal_amount, 2) }}</td>
                                </tr>
                                @if ($order->discount_amount > 0)
                                    <tr>
                                        <td colspan="3" class="text-end text-success">
                                            Discount ({{ $order->coupon_code }}):
                                        </td>
                                        <td class="text-success">-${{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if ($order->notes)
                        <div class="mt-3">
                            <h6>Order Notes:</h6>
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-center my-4">
                <a href="{{ route('products.index') }}" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>
@endsection
