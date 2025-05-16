@extends('layouts.app')

@section('title', 'Available Coupons')

@section('content')
    <div class="container py-5">
        <h1 class="mb-4">Available Coupons</h1>

        <div class="row">
            @if (count($coupons) > 0)
                @foreach ($coupons as $coupon)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 coupon-card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">{{ $coupon->description ?: 'Special Offer' }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="coupon-code">
                                        <span class="code-text">{{ $coupon->code }}</span>
                                        <button class="btn btn-sm btn-outline-secondary copy-btn"
                                            data-code="{{ $coupon->code }}" data-bs-toggle="tooltip"
                                            title="Copy to clipboard">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="coupon-details">
                                    <div class="discount-value mb-2">
                                        <span class="h3 fw-bold">
                                            @if ($coupon->type === 'percentage')
                                                {{ $coupon->value }}% OFF
                                            @else
                                                ₨{{ number_format($coupon->value, 2) }} OFF
                                            @endif
                                        </span>
                                    </div>

                                    @if ($coupon->min_order_amount > 0)
                                        <p class="mb-2">
                                            <i class="fas fa-info-circle text-info"></i>
                                            Minimum order: ₨{{ number_format($coupon->min_order_amount, 2) }}
                                        </p>
                                    @endif

                                    @if ($coupon->expires_at)
                                        <p class="mb-2">
                                            <i class="fas fa-calendar-alt text-warning"></i>
                                            Expires: {{ $coupon->expires_at->format('M d, Y') }}
                                        </p>
                                    @endif

                                    @if ($coupon->max_uses)
                                        <p class="mb-0">
                                            <i class="fas fa-tag text-secondary"></i>
                                            Limited offer: {{ $coupon->max_uses - $coupon->used_count }} uses remaining
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('cart.index') }}" class="btn btn-primary w-100">
                                    <i class="fas fa-shopping-cart me-1"></i> Apply to Cart
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="alert alert-info">
                        <h4 class="alert-heading">No coupons available right now!</h4>
                        <p>Check back later for special offers and discounts.</p>
                        <hr>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-1"></i> Browse Products
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .coupon-card {
            transition: transform 0.3s;
            border: 1px solid #ddd;
        }

        .coupon-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .coupon-code {
            background: #f8f9fa;
            border: 2px dashed #6c757d;
            padding: 8px 15px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
        }

        .code-text {
            font-family: monospace;
            font-size: 1.2rem;
            font-weight: bold;
            letter-spacing: 1px;
            margin-right: 10px;
        }

        .copy-btn {
            padding: 2px 6px;
        }

        .coupon-details {
            text-align: center;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Copy coupon code to clipboard
            $('.copy-btn').on('click', function() {
                var code = $(this).data('code');
                var $btn = $(this);

                // Create a temporary input element
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(code).select();
                document.execCommand("copy");
                $temp.remove();

                // Update tooltip
                var tooltip = bootstrap.Tooltip.getInstance($btn[0]);
                $btn.attr('data-bs-original-title', 'Copied!');
                tooltip.show();

                // Reset tooltip after 2 seconds
                setTimeout(function() {
                    $btn.attr('data-bs-original-title', 'Copy to clipboard');
                }, 2000);
            });
        });
    </script>
@endsection
