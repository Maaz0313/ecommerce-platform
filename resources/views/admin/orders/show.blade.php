@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Orders
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="card-title mb-0">Order #{{ $order->id }}</h3>
                <span
                    class="badge
                    @if ($order->status == 'delivered') bg-success
                    @elseif($order->status == 'shipped') bg-info
                    @elseif($order->status == 'preparing_for_shipment') bg-primary
                    @elseif($order->status == 'cancelled') bg-danger
                    @else bg-warning @endif px-3 py-2 fs-6">
                    @if ($order->status == 'order_received')
                        Order Received
                    @elseif ($order->status == 'preparing_for_shipment')
                        Preparing for Shipment
                    @else
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    @endif
                </span>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="order_received">
                    <button type="submit"
                        class="btn {{ $order->status == 'order_received' ? 'btn-warning disabled' : 'btn-outline-warning' }}">
                        <i class="fas fa-clock me-2"></i> Order Received
                    </button>
                </form>

                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="preparing_for_shipment">
                    <button type="submit"
                        class="btn {{ $order->status == 'preparing_for_shipment' ? 'btn-primary disabled' : 'btn-outline-primary' }}">
                        <i class="fas fa-box-open me-2"></i> Preparing for Shipment
                    </button>
                </form>

                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="shipped">
                    <button type="submit"
                        class="btn {{ $order->status == 'shipped' ? 'btn-info disabled' : 'btn-outline-info' }}">
                        <i class="fas fa-shipping-fast me-2"></i> Shipped
                    </button>
                </form>

                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="delivered">
                    <button type="submit"
                        class="btn {{ $order->status == 'delivered' ? 'btn-success disabled' : 'btn-outline-success' }}">
                        <i class="fas fa-check-circle me-2"></i> Delivered
                    </button>
                </form>

                <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit"
                        class="btn {{ $order->status == 'cancelled' ? 'btn-danger disabled' : 'btn-outline-danger' }}">
                        <i class="fas fa-times-circle me-2"></i> Cancelled
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Customer Information</h5>
                    <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                    <p><strong>Phone:</strong> {{ $order->customer_phone ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h5>Order Details</h5>
                    <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                    <p><strong>Subtotal:</strong> ₨{{ number_format($order->subtotal_amount, 2) }}</p>
                    @if ($order->discount_amount > 0)
                        <p><strong>Discount:</strong> <span
                                class="text-success">-₨{{ number_format($order->discount_amount, 2) }}
                                ({{ $order->coupon_code }})</span></p>
                    @endif
                    <p><strong>Total Amount:</strong> ₨{{ number_format($order->total_amount, 2) }}</p>
                    <p><strong>Payment Method:</strong>
                        @if ($order->payment_method == 'cod')
                            Cash on Delivery
                        @else
                            {{ ucfirst($order->payment_method) }}
                        @endif
                    </p>
                    <p><strong>Shipping Address:</strong> {{ $order->shipping_address }}</p>
                </div>
            </div>

            <hr>

            <h5>Order Items</h5>
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
                                <td class="text-end">₨{{ number_format($item->price * $item->quantity, 2) }}</td>
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
                                <th class="text-end text-success">-₨{{ number_format($order->discount_amount, 2) }}</th>
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
                    <h5>Customer Notes</h5>
                    <p>{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>


@endsection
