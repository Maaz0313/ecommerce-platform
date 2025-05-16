@extends('layouts.admin')

@section('title', 'Manage Orders')

@section('scripts')
    <script src="{{ asset('js/admin-orders.js') }}"></script>
@endsection

@section('styles')
    <style>
        /* Ensure dropdown menus are visible and properly sized */
        .dropdown-menu {
            z-index: 1030;
            position: absolute;
            max-height: none !important;
            overflow: visible !important;
            transform: none !important;
        }

        /* Ensure the table container doesn't clip the dropdown */
        .table-responsive {
            overflow: visible !important;
        }

        /* Ensure the card doesn't clip the dropdown */
        .card {
            overflow: visible !important;
        }

        /* Add some spacing between dropdown items */
        .dropdown-item {
            padding: 8px 16px;
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')

    <div class="card shadow-sm overflow-visible">
        <div class="card-header bg-light">
            <h3 class="card-title">Order Management</h3>
        </div>
        <div class="card-body overflow-visible">
            <div class="table-responsive overflow-visible">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>
                                    <div>{{ $order->customer_name }}</div>
                                    <small class="text-muted">{{ $order->customer_email }}</small>
                                </td>
                                <td>₨{{ number_format($order->total_amount, 2) }}</td>
                                <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <span
                                        class="badge
                                        @if ($order->status == 'delivered') bg-success
                                        @elseif($order->status == 'shipped') bg-info
                                        @elseif($order->status == 'preparing_for_shipment') bg-primary
                                        @elseif($order->status == 'cancelled') bg-danger
                                        @else bg-warning @endif">
                                        @if ($order->status == 'order_received')
                                            Order Received
                                        @elseif ($order->status == 'preparing_for_shipment')
                                            Preparing for Shipment
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if ($order->payment_method == 'cod')
                                        Cash on Delivery
                                    @else
                                        {{ ucfirst($order->payment_method) }}
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                id="dropdownMenuButton{{ $order->id }}" data-bs-toggle="dropdown"
                                                data-bs-auto-close="outside" aria-expanded="false">
                                                Status
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="dropdownMenuButton{{ $order->id }}"
                                                data-bs-popper="static">
                                                <li>
                                                    <a href="#"
                                                        class="dropdown-item status-link {{ $order->status == 'order_received' ? 'active' : '' }}"
                                                        data-order-id="{{ $order->id }}" data-status="order_received">
                                                        <i class="fas fa-clock me-2 text-warning"></i> Order Received
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#"
                                                        class="dropdown-item status-link {{ $order->status == 'preparing_for_shipment' ? 'active' : '' }}"
                                                        data-order-id="{{ $order->id }}"
                                                        data-status="preparing_for_shipment">
                                                        <i class="fas fa-box-open me-2 text-primary"></i> Preparing for
                                                        Shipment
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#"
                                                        class="dropdown-item status-link {{ $order->status == 'shipped' ? 'active' : '' }}"
                                                        data-order-id="{{ $order->id }}" data-status="shipped">
                                                        <i class="fas fa-shipping-fast me-2 text-info"></i> Shipped
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#"
                                                        class="dropdown-item status-link {{ $order->status == 'delivered' ? 'active' : '' }}"
                                                        data-order-id="{{ $order->id }}" data-status="delivered">
                                                        <i class="fas fa-check-circle me-2 text-success"></i> Delivered
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#"
                                                        class="dropdown-item status-link {{ $order->status == 'cancelled' ? 'active' : '' }}"
                                                        data-order-id="{{ $order->id }}" data-status="cancelled">
                                                        <i class="fas fa-times-circle me-2 text-danger"></i> Cancelled
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Hidden form for status updates -->
                                        <form id="status-form-{{ $order->id }}"
                                            action="{{ route('admin.orders.status', $order->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            <input type="hidden" name="status" id="status-input-{{ $order->id }}">
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection
