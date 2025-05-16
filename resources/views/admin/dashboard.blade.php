@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content') <div class="row mb-4">
        <div class="col-12">
            <h1>Admin Dashboard</h1>
            <p class="lead">Welcome to the ecommerce platform admin panel</p>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-dark">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus-circle me-1"></i> Add New Product
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-folder-plus me-1"></i> Add New Category
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.orders.index') }}?status=pending" class="btn btn-warning w-100">
                                <i class="fas fa-clock me-1"></i> View Pending Orders
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.products.index') }}?stock=low" class="btn btn-danger w-100">
                                <i class="fas fa-exclamation-triangle me-1"></i> Low Stock Items
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-info w-100">
                                <i class="fas fa-envelope me-1"></i> Contact Messages
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-md-3 mb-4">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Products</h5>
                    <p class="display-4">{{ $productCount }}</p>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-box me-1"></i> Manage Products
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Categories</h5>
                    <p class="display-4">{{ $categoryCount }}</p>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-success mt-2">
                        <i class="fas fa-tags me-1"></i> Manage Categories
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Orders</h5>
                    <p class="display-4">{{ $orderCount }}</p>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-info mt-2">
                        <i class="fas fa-shopping-cart me-1"></i> Manage Orders
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-danger h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Pending Orders</h5>
                    <p class="display-4">{{ $pendingOrderCount }}</p>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-danger mt-2">
                        <i class="fas fa-clock me-1"></i> View Pending
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Processing Orders</h5>
                    <p class="display-4">{{ $processingOrderCount }}</p>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-warning mt-2">
                        <i class="fas fa-shipping-fast me-1"></i> View Processing
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Completed Orders</h5>
                    <p class="display-4">{{ $completedOrderCount }}</p>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-success mt-2">
                        <i class="fas fa-check-circle me-1"></i> View Completed
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Revenue Cards -->
        <div class="col-md-6 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">Total Revenue</h4>
                        <p class="mb-0">From completed orders</p>
                    </div>
                    <div>
                        <h2 class="display-4 mb-0">₨{{ number_format($totalRevenue, 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1">Today's Sales</h4>
                        <p class="mb-0">{{ now()->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <h2 class="display-4 mb-0">₨{{ number_format($todaySales, 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Orders -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    @if ($recentOrders->count() > 0)
                        <div class="list-group">
                            @foreach ($recentOrders as $order)
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Order #{{ $order->id }}</h6>
                                        <small>{{ $order->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $order->customer_name }} -
                                        ₨{{ number_format($order->total_amount, 2) }}</p>
                                    <small class="text-muted">Status: {{ ucfirst($order->status) }}</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No recent orders</p>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-dark">View All Orders</a>
                </div>
            </div>
        </div>

        <!-- Recent Contact Messages -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Contact Messages</h5>
                    @if ($unreadContactCount > 0)
                        <span class="badge bg-primary">{{ $unreadContactCount }} unread</span>
                    @endif
                </div>
                <div class="card-body">
                    @if ($recentContactMessages->count() > 0)
                        <div class="list-group">
                            @foreach ($recentContactMessages as $message)
                                <a href="{{ route('admin.contact-messages.show', $message->id) }}"
                                    class="list-group-item list-group-item-action {{ $message->is_read ? '' : 'list-group-item-primary' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $message->name }}</h6>
                                        <small>{{ $message->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($message->subject, 30) }}</p>
                                    <small class="text-muted">{{ $message->email }}</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No contact messages</p>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-sm btn-outline-dark">View All
                        Messages</a>
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Low Stock Products</h5>
                </div>
                <div class="card-body">
                    @if ($lowStockProducts->count() > 0)
                        <div class="list-group">
                            @foreach ($lowStockProducts as $product)
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <span class="badge bg-warning">{{ $product->stock }} left</span>
                                    </div>
                                    <p class="mb-1">₨{{ number_format($product->price, 2) }}</p>
                                    <small class="text-muted">Category: {{ $product->category->name }}</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No low stock products</p>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-dark">View All
                        Products</a>
                </div>
            </div>
        </div>

        <!-- Popular Products -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Popular Products</h5>
                </div>
                <div class="card-body">
                    @if ($popularProducts->count() > 0)
                        <div class="list-group">
                            @foreach ($popularProducts as $product)
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <span class="badge bg-info">{{ $product->order_items_count }} orders</span>
                                    </div>
                                    <p class="mb-1">₨{{ number_format($product->price, 2) }}</p>
                                    <small class="text-muted">Stock: {{ $product->stock }} remaining</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No popular products data</p>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-dark">View All
                        Products</a>
                </div>
            </div>
        </div>
    </div>
@endsection
