@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Edit Coupon: {{ $coupon->code }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="code" class="form-label">Coupon Code *</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                            name="code" value="{{ old('code', $coupon->code) }}" required>
                        <small class="form-text text-muted">Enter a unique code for this coupon (e.g., SUMMER2023).</small>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" value="{{ old('description', $coupon->description) }}">
                        <small class="form-text text-muted">A brief description of this coupon.</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Discount Type *</label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                            required>
                            <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>
                                Percentage Discount
                            </option>
                            <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>
                                Fixed Amount Discount
                            </option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="value" class="form-label">Discount Value *</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0"
                                class="form-control @error('value') is-invalid @enderror" id="value" name="value"
                                value="{{ old('value', $coupon->value) }}" required>
                            <span class="input-group-text discount-symbol">
                                {{ $coupon->type === 'percentage' ? '%' : '₨' }}
                            </span>
                        </div>
                        <small class="form-text text-muted">For percentage, enter a value between 1-100. For fixed amount,
                            enter the dollar value.</small>
                        @error('value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="min_order_amount" class="form-label">Minimum Order Amount *</label>
                        <div class="input-group">
                            <span class="input-group-text">₨</span>
                            <input type="number" step="0.01" min="0"
                                class="form-control @error('min_order_amount') is-invalid @enderror" id="min_order_amount"
                                name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount) }}"
                                required>
                        </div>
                        <small class="form-text text-muted">Minimum cart total required to use this coupon.</small>
                        @error('min_order_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="max_uses" class="form-label">Maximum Uses</label>
                        <input type="number" min="0" class="form-control @error('max_uses') is-invalid @enderror"
                            id="max_uses" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}">
                        <small class="form-text text-muted">Leave blank for unlimited uses.</small>
                        @error('max_uses')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="starts_at" class="form-label">Start Date</label>
                        <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror"
                            id="starts_at" name="starts_at"
                            value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
                        <small class="form-text text-muted">Leave blank to start immediately.</small>
                        @error('starts_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="expires_at" class="form-label">Expiry Date</label>
                        <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror"
                            id="expires_at" name="expires_at"
                            value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
                        <small class="form-text text-muted">Leave blank for no expiration.</small>
                        @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                            {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                    <small class="form-text text-muted">Uncheck to disable this coupon.</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Coupon</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Update discount symbol based on type
            $('#type').on('change', function() {
                if ($(this).val() === 'percentage') {
                    $('.discount-symbol').text('%');
                } else {
                    $('.discount-symbol').text('₨');
                }
            });
        });
    </script>
@endsection
