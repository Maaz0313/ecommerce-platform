@props(['product'])

<div class="card h-100 shadow-sm product-card" tabindex="0">
    @if ($product->image)
        <a href="{{ route('products.show', $product->slug) }}" class="product-link">
            <img src="{{ asset('images/products/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
        </a>
    @else
        <a href="{{ route('products.show', $product->slug) }}" class="product-link">
            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                <i class="fas fa-image fa-3x text-muted"></i>
            </div>
        </a>
    @endif
    <div class="card-body d-flex flex-column">
        <h5 class="card-title">
            <a href="{{ route('products.show', $product->slug) }}" class="product-link">
                {{ Str::limit($product->name, 40) }}
            </a>
        </h5>
        <p class="card-text mb-1 text-muted small">{{ $product->category->name }}</p>
        <p class="card-text text-primary fw-bold">₨{{ number_format($product->price, 2) }}</p>
        @if ($product->stock > 0)
            <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form mt-auto">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="ajax" value="1">
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary add-to-cart-btn">
                        <i class="fas fa-cart-plus me-2"></i> Add to Cart
                    </button>
                </div>
            </form>
        @else
            <div class="mt-auto">
                <button class="btn btn-secondary w-100" disabled>Out of Stock</button>
            </div>
        @endif
    </div>
</div>
