@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <h1 class="mb-4">Checkout</h1>

    @php
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    @endphp

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                    id="customer_name" name="customer_name"
                                    value="{{ old('customer_name', auth()->user()->name ?? '') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                    id="customer_email" name="customer_email"
                                    value="{{ old('customer_email', auth()->user()->email ?? '') }}" required>
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}">
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address *</label>
                            <textarea class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address"
                                name="shipping_address" rows="3" required>{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Order Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <h5>Payment Method</h5>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_cod"
                                    value="cod"
                                    {{ old('payment_method', session('checkout_form_data.payment_method')) == 'stripe' ? '' : 'checked' }}>
                                <label class="form-check-label" for="payment_cod">
                                    Cash on Delivery
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_stripe"
                                    value="stripe"
                                    {{ old('payment_method', session('checkout_form_data.payment_method')) == 'stripe' ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_stripe">
                                    Credit/Debit Card (Visa/Mastercard)
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Place Order</button>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">Back to Cart</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    @foreach ($cartItems as $id => $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                        @endphp
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $item['name'] }} x {{ $item['quantity'] }}</span>
                            <span>₨{{ number_format($subtotal, 2) }}</span>
                        </div>
                    @endforeach

                    <hr>

                    @php
                        $coupon = session()->get('coupon');
                        $discount = $coupon ? $coupon['discount'] : 0;
                        $discountedTotal = $total - $discount;
                    @endphp

                    <!-- Coupon Code Section -->
                    <div class="mb-3">
                        @if (!session()->has('coupon'))
                            <div class="coupon-form">
                                <label for="coupon-code" class="form-label">Have a coupon?</label>
                                <div class="input-group mb-2">
                                    <input type="text" id="coupon-code" class="form-control"
                                        placeholder="Enter coupon code">
                                    <button type="button" id="apply-coupon-btn" class="btn btn-outline-primary">
                                        <i class="fas fa-tag"></i> Apply
                                    </button>
                                </div>
                                <div id="coupon-message" class="small"></div>
                            </div>
                        @else
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>₨{{ number_format($total, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>
                                    Discount ({{ $coupon['code'] }})
                                    <button type="button" id="remove-coupon-btn"
                                        class="btn btn-sm btn-link text-danger p-0 ms-2">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </span>
                                <span>-₨{{ number_format($discount, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold">₨{{ number_format($coupon ? $discountedTotal : $total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Restore form data from localStorage if available
            restoreFormData();

            // Apply coupon with AJAX
            $('#apply-coupon-btn').on('click', function() {
                const $button = $(this);
                const $input = $('#coupon-code');
                const code = $input.val().trim();
                const $message = $('#coupon-message');

                if (!code) {
                    $message.html('<span class="text-danger">Please enter a coupon code</span>');
                    return;
                }

                // Save form data to localStorage before submitting
                saveFormData();

                // Add loading state
                $button.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                );
                $button.prop('disabled', true);
                $message.html('');

                $.ajax({
                    url: "{{ route('coupon.apply') }}",
                    type: "POST",
                    data: {
                        code: code,
                        _token: "{{ csrf_token() }}"
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reload the page to show the applied coupon
                            showToast('Success', response.message, 'success');
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Show error message
                            $message.html('<span class="text-danger">' + response.message +
                                '</span>');
                            $button.html('<i class="fas fa-tag"></i> Apply');
                            $button.prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        $message.html(
                            '<span class="text-danger">An error occurred. Please try again.</span>'
                        );
                        $button.html('<i class="fas fa-tag"></i> Apply');
                        $button.prop('disabled', false);
                    }
                });
            });

            // Remove coupon with AJAX
            $('#remove-coupon-btn').on('click', function() {
                const $button = $(this);

                // Save form data to localStorage before submitting
                saveFormData();

                // Add loading state
                $button.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                );
                $button.prop('disabled', true);

                $.ajax({
                    url: "{{ route('coupon.remove') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reload the page to show the removed coupon
                            showToast('Success', response.message, 'success');
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // Show error message
                            showToast('Error', response.message, 'danger');
                            $button.html('<i class="fas fa-times"></i> Remove');
                            $button.prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        showToast('Error', 'An error occurred. Please try again.', 'danger');
                        $button.html('<i class="fas fa-times"></i> Remove');
                        $button.prop('disabled', false);
                    }
                });
            });

            // Function to save form data to localStorage
            function saveFormData() {
                const formData = {
                    customer_name: $('#customer_name').val(),
                    customer_email: $('#customer_email').val(),
                    customer_phone: $('#customer_phone').val(),
                    shipping_address: $('#shipping_address').val(),
                    notes: $('#notes').val(),
                    payment_method: $('input[name="payment_method"]:checked').val()
                };

                localStorage.setItem('checkout_form_data', JSON.stringify(formData));
            }

            // Function to restore form data from localStorage
            function restoreFormData() {
                const savedData = localStorage.getItem('checkout_form_data');

                if (savedData) {
                    try {
                        const formData = JSON.parse(savedData);

                        // Only fill empty fields with saved data
                        if (!$('#customer_name').val() && formData.customer_name) {
                            $('#customer_name').val(formData.customer_name);
                        }

                        if (!$('#customer_email').val() && formData.customer_email) {
                            $('#customer_email').val(formData.customer_email);
                        }

                        if (!$('#customer_phone').val() && formData.customer_phone) {
                            $('#customer_phone').val(formData.customer_phone);
                        }

                        if (!$('#shipping_address').val() && formData.shipping_address) {
                            $('#shipping_address').val(formData.shipping_address);
                        }

                        if (!$('#notes').val() && formData.notes) {
                            $('#notes').val(formData.notes);
                        }

                        if (formData.payment_method) {
                            $(`input[name="payment_method"][value="${formData.payment_method}"]`).prop('checked',
                                true);
                        }
                    } catch (e) {
                        console.error('Error restoring form data:', e);
                        localStorage.removeItem('checkout_form_data');
                    }
                }
            }

            // Clear localStorage when form is submitted
            $('form').on('submit', function() {
                localStorage.removeItem('checkout_form_data');
            });

            // Helper function to show toast messages
            function showToast(title, message, type) {
                // Create toast container if it doesn't exist
                if ($('#toast-container').length === 0) {
                    $('body').append(
                        '<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>'
                    );
                }

                // Generate a unique ID for this toast
                const toastId = 'toast-' + Date.now();

                const toastHtml = `
                    <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <strong>${title}:</strong> ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;

                // Add toast to container
                $('#toast-container').append(toastHtml);

                // Get the toast element
                const $toast = $('#' + toastId);

                try {
                    // Initialize Bootstrap toast
                    const toast = new bootstrap.Toast($toast[0], {
                        autohide: true,
                        delay: 3000
                    });

                    // Show the toast
                    toast.show();

                    // Remove toast from DOM after it's hidden
                    $toast.on('hidden.bs.toast', function() {
                        $(this).remove();
                    });
                } catch (error) {
                    console.error('Error showing toast:', error);
                    // Fallback alert if toast fails
                    alert(`${title}: ${message}`);
                    $toast.remove();
                }
            }
        });
    </script>
@endsection
