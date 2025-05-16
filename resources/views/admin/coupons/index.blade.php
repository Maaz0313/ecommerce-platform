@extends('layouts.admin')

@section('title', 'Admin - Coupons')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Coupons</h1>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Coupon
        </a>
    </div>

    @if (count($coupons) > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Min Order</th>
                                <th>Used / Max</th>
                                <th>Status</th>
                                <th>Expires</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->id }}</td>
                                    <td><code>{{ $coupon->code }}</code></td>
                                    <td>{{ ucfirst($coupon->type) }}</td>
                                    <td>
                                        @if ($coupon->type === 'percentage')
                                            {{ $coupon->value }}%
                                        @else
                                            ₨{{ number_format($coupon->value, 2) }}
                                        @endif
                                    </td>
                                    <td>₨{{ number_format($coupon->min_order_amount, 2) }}</td>
                                    <td>
                                        {{ $coupon->used_count }} /
                                        {{ $coupon->max_uses ? $coupon->max_uses : '∞' }}
                                    </td>
                                    <td>
                                        @if ($coupon->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($coupon->expires_at)
                                            {{ $coupon->expires_at->format('M d, Y') }}
                                        @else
                                            Never
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                                                class="btn btn-sm btn-info me-2">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $coupons->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <h4 class="alert-heading">No coupons found!</h4>
            <p>You haven't created any coupons yet.</p>
            <hr>
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Your First Coupon
            </a>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Confirm delete
            $('.delete-form').on('submit', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this coupon? This action cannot be undone.')) {
                    this.submit();
                }
            });
        });
    </script>
@endsection
