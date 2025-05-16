@extends('layouts.app')

@section('title', $product->name)

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/product-details.css') }}">
@endsection

@section('content')
    <div class="container product-detail-container mt-3 mb-0 pb-0">
        <!-- Product Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Products</a>
                </li>
                <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->category->slug) }}"
                        class="text-decoration-none">{{ $product->category->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-6 mb-4">
                <!-- Product Image Gallery -->
                <div class="product-gallery card border-0 shadow-sm rounded-4 overflow-hidden">
                    <!-- Main product image -->
                    <div class="main-image-container position-relative">
                        <div class="image-wrapper">
                            @if ($product->image)
                                <img src="{{ asset('images/products/' . $product->image) }}"
                                    class="rounded-top main-product-image" alt="{{ $product->name }}" id="mainProductImage">
                            @else
                                <div
                                    class="bg-light d-flex align-items-center justify-content-center rounded-top no-image-placeholder">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div> <!-- Expand button -->
                        <button
                            class="btn btn-light btn-sm position-absolute top-0 end-0 m-3 rounded-circle shadow-sm expand-btn"
                            onclick="openFullscreenView()" title="View full image">
                            <i class="fas fa-expand-alt"></i>
                        </button>
                    </div>

                    <!-- Image gallery thumbnails with horizontal scrolling -->
                    @if (($product->images && $product->images->count() > 0) || $product->image)
                        <div class="thumbnail-gallery p-2 bg-light border-top">
                            <div class="d-flex align-items-center position-relative">
                                <!-- Previous button -->
                                <button class="btn btn-sm btn-light thumbnail-nav-btn prev-btn me-2"
                                    onclick="scrollThumbnails('prev')">
                                    <i class="fas fa-chevron-left"></i>
                                </button>

                                <!-- Thumbnails container -->
                                <div class="d-flex flex-nowrap overflow-hidden thumbnail-scroll-container"
                                    id="thumbnailContainer">
                                    @if ($product->image)
                                        <div class="thumbnail-item mx-1">
                                            <div class="thumbnail-container thumbnail-active"
                                                onclick="changeMainImage(this, '{{ asset('images/products/' . $product->image) }}')">
                                                <img src="{{ asset('images/products/' . $product->image) }}"
                                                    alt="{{ $product->name }}">
                                            </div>
                                        </div>
                                    @endif

                                    @foreach ($product->images->where('is_primary', false) as $image)
                                        <div class="thumbnail-item mx-1">
                                            <div class="thumbnail-container"
                                                onclick="changeMainImage(this, '{{ asset('images/products/' . $image->image_path) }}')">
                                                <img src="{{ asset('images/products/' . $image->image_path) }}"
                                                    alt="{{ $product->name }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Next button -->
                                <button class="btn btn-sm btn-light thumbnail-nav-btn next-btn ms-2"
                                    onclick="scrollThumbnails('next')">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h1 class="display-6 fw-bold mb-2">{{ $product->name }}</h1>

                    <!-- Category badge -->
                    <div class="mb-3">
                        <a href="{{ route('categories.show', $product->category->slug) }}" class="text-decoration-none">
                            <span class="badge rounded-pill bg-light text-dark border">
                                <i class="fas fa-tag me-1 text-muted"></i> {{ $product->category->name }}
                            </span>
                        </a>
                    </div>

                    <!-- Price section -->
                    <div class="mb-4 d-flex align-items-center">
                        <span class="fs-2 fw-bold text-primary me-3">₨{{ number_format($product->price, 2) }}</span>
                    </div>

                    <!-- Stock status -->
                    <div class="mb-4">
                        @if ($product->stock > 0)
                            <div class="d-flex align-items-center">
                                <span class="badge bg-success me-2 p-2">
                                    <i class="fas fa-check-circle me-1"></i> In Stock
                                </span>
                                <span class="text-muted">{{ $product->stock }} items available</span>
                            </div>
                        @else
                            <span class="badge bg-danger p-2">
                                <i class="fas fa-times-circle me-1"></i> Out of Stock
                            </span>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h5 class="fw-semibold text-dark mb-2">Description</h5>
                        <p class="text-muted">{{ $product->description }}</p>
                    </div>

                    <!-- Divider -->
                    <hr class="mb-4">

                    <!-- Add to cart section -->
                    @if ($product->stock > 0)
                        <div id="addToCartForm">
                            <input type="hidden" id="product_id" value="{{ $product->id }}">
                            <div class="mb-4">
                                <label for="quantity" class="form-label fw-semibold">Quantity</label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" id="decreaseQty">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="quantity" class="form-control text-center" value="1"
                                        min="1" max="{{ $product->stock }}">
                                    <button class="btn btn-outline-secondary" type="button" id="increaseQty">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Maximum {{ $product->stock }} items</small>
                            </div>
                            <div class="d-grid gap-2">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <button type="button" id="addToCartBtn"
                                            class="btn btn-primary btn-lg custom-cart-btn w-100">
                                            <span class="d-flex align-items-center">
                                                <i class="fas fa-shopping-cart me-2"></i>
                                                <span>Add to Cart</span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" id="buyNowBtn"
                                            class="btn btn-success btn-lg custom-cart-btn w-100">
                                            <span class="d-flex align-items-center">
                                                <i class="fas fa-bolt me-2"></i>
                                                <span>Buy Now</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="addToCartSuccess" class="d-none">
                            <div class="alert alert-success mb-4">
                                <i class="fas fa-check-circle me-2"></i> Product added to cart!
                            </div>
                            <div class="d-grid gap-2"> <a href="{{ route('cart.index') }}"
                                    class="btn btn-success custom-cart-btn">
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        <span>View Cart</span>
                                    </span>
                                </a>
                                <button type="button" id="continueShoppingBtn"
                                    class="btn btn-outline-primary custom-cart-btn">
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        <span>Continue Shopping</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="d-grid gap-2"> <button class="btn btn-secondary btn-lg custom-cart-btn" disabled>
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-times-circle me-2"></i>
                                    <span>Out of Stock</span>
                                </span>
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary custom-cart-btn">
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    <span>Back to Products</span>
                                </span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @if ($relatedProducts->count() > 0)
            <div class="related-products mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fw-bold m-0">Related Products</h3>
                    <a href="{{ route('categories.show', $product->category->slug) }}"
                        class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-th-list me-1"></i> View All in {{ $product->category->name }}
                    </a>
                </div>

                <div class="row g-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="col-6 col-md-3">
                            <x-product-card :product="$relatedProduct" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <!-- Fullscreen image modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content bg-light">
                    <div class="modal-header border-0 bg-dark text-white">
                        <h5 class="modal-title" id="imageModalLabel">{{ $product->name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-0">
                        <img id="fullscreenImage" src="" class="img-fluid" alt="{{ $product->name }}">
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- explicitly closing product-detail-container -->
@endsection
@section('scripts')
    <script>
        // Function to change main product image
        function changeMainImage(thumbnailElement, imageSrc) {
            // Update the main image
            const mainImage = document.getElementById('mainProductImage');
            mainImage.src = imageSrc;

            // Remove active class from all thumbnails
            const thumbnails = document.querySelectorAll('.thumbnail-container');
            thumbnails.forEach(thumbnail => {
                thumbnail.classList.remove('thumbnail-active');
            });

            // Add active class to the clicked thumbnail
            thumbnailElement.classList.add('thumbnail-active');
        }

        // Function to scroll thumbnails left/right
        function scrollThumbnails(direction) {
            const container = document.getElementById('thumbnailContainer');
            const scrollAmount = 100;

            if (direction === 'next') {
                container.scrollLeft += scrollAmount;
            } else {
                container.scrollLeft -= scrollAmount;
            }
        }

        // Function to open fullscreen view
        function openFullscreenView() {
            const mainImage = document.getElementById('mainProductImage');
            const fullscreenImage = document.getElementById('fullscreenImage');

            fullscreenImage.src = mainImage.src;

            const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();
        }

        // Document ready initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Empty function - no initialization needed
        });
    </script>
    <script>
        $(document).ready(function() {
            // Quantity increment/decrement
            $('#increaseQty').click(function() {
                let quantity = parseInt($('#quantity').val());
                const max = parseInt($('#quantity').attr('max'));

                if (quantity < max) {
                    $('#quantity').val(quantity + 1);
                }
            });

            $('#decreaseQty').click(function() {
                let quantity = parseInt($('#quantity').val());

                if (quantity > 1) {
                    $('#quantity').val(quantity - 1);
                }
            });

            // Add to cart
            $('#addToCartBtn').click(function() {
                const productId = $('#product_id').val();
                const quantity = $('#quantity').val();

                // Add loading state
                const $btn = $(this);
                $btn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...'
                );
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('cart.add') }}",
                    type: "POST",
                    data: {
                        product_id: productId,
                        quantity: quantity,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update cart count in nav
                            updateCartBadge(response.cartCount);

                            // Show success message
                            $('#addToCartForm').addClass('d-none');
                            $('#addToCartSuccess').removeClass('d-none');

                            // Scroll to top if needed
                            $('html, body').animate({
                                scrollTop: 0
                            }, 'slow');

                            // Show toast notification
                            showToast('Success', response.message, 'success');
                        } else {
                            // Show error message as toast
                            showToast('Error', response.message, 'danger');
                            $btn.html('Add to Cart');
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        // Show error message as toast
                        showToast('Error', 'An error occurred. Please try again.', 'danger');
                        $btn.html('Add to Cart');
                        $btn.prop('disabled', false);
                    }
                });
            });

            // Buy Now button
            $('#buyNowBtn').click(function() {
                const productId = $('#product_id').val();
                const quantity = $('#quantity').val();

                // Add loading state
                const $btn = $(this);
                $btn.html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
                );
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('cart.buyNow') }}",
                    type: "POST",
                    data: {
                        product_id: productId,
                        quantity: quantity,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            // Redirect to checkout page
                            window.location.href = response.redirect;
                        } else {
                            // Show error message as toast
                            showToast('Error', response.message, 'danger');
                            $btn.html(
                                '<span class="d-flex align-items-center"><i class="fas fa-bolt me-2"></i><span>Buy Now</span></span>'
                                );
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        // Show error message as toast
                        showToast('Error', 'An error occurred. Please try again.', 'danger');
                        $btn.html(
                            '<span class="d-flex align-items-center"><i class="fas fa-bolt me-2"></i><span>Buy Now</span></span>'
                            );
                        $btn.prop('disabled', false);
                    }
                });
            });

            // Continue shopping button
            $('#continueShoppingBtn').click(function() {
                $('#addToCartSuccess').addClass('d-none');
                $('#addToCartForm').removeClass('d-none');
                $('#addToCartBtn').html(
                    '<span class="d-flex align-items-center"><i class="fas fa-shopping-cart me-2"></i><span>Add to Cart</span></span>'
                );
                $('#addToCartBtn').prop('disabled', false);
            });

            // Function to update cart badge
            function updateCartBadge(count) {
                const $cartBadge = $('#cart-badge');
                if ($cartBadge.length) {
                    $cartBadge.text(count);
                    $cartBadge.removeClass('d-none');
                }
            }

            // Helper function to show toast messages
            function showToast(title, message, type) {
                const toastHtml = `
                    <div class="toast align-items-center text-white bg-${type} border-0 position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <strong>${title}:</strong> ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                `;

                const $toast = $(toastHtml);
                $('body').append($toast);

                const toast = new bootstrap.Toast($toast, {
                    autohide: true,
                    delay: 3000
                });

                toast.show();

                // Remove toast from DOM after it's hidden
                $toast.on('hidden.bs.toast', function() {
                    $(this).remove();
                });
            }
        });
    </script>
    <style>
        /* Main Product Image */
        .image-wrapper {
            height: 380px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
            overflow: hidden;
            position: relative;
            padding: 10px;
        }

        .main-product-image {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .main-product-image:hover {
            transform: scale(1.01);
        }

        /* Thumbnails styling */
        .thumbnail-container {
            width: 65px;
            height: 65px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 2px;
        }

        .thumbnail-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .thumbnail-container:hover img {
            transform: scale(1.1);
        }

        .thumbnail-active {
            border: 2px solid #0d6efd;
        }

        .thumbnail-scroll-container {
            scroll-behavior: smooth;
            max-width: 100%;
        }

        /* Thumbnail navigation */
        .thumbnail-nav-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* No image placeholder */
        .no-image-placeholder {
            height: 400px;
            width: 100%;
        }

        /* Related products styling */
        .product-thumbnail {
            height: 200px;
            object-fit: cover;
        }

        .product-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover .product-overlay {
            opacity: 1;
        }

        /* Fullscreen modal */
        #imageModal .modal-body {
            background-color: #222;
            height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #fullscreenImage {
            max-height: 70vh;
        }

        /* Fix footer spacing */
        .product-detail-container {
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
        }

        /* Custom Add to Cart button with even padding */
        .custom-cart-btn {
            padding: 0 1.5rem !important;
            /* Horizontal padding only */
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            letter-spacing: 0.01em;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            height: 3.5rem;
            /* Fixed height for consistency */
            line-height: 1;
            /* Reset line height */
        }

        .custom-cart-btn:not([disabled]):hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Fix FontAwesome icon alignment */
        .custom-cart-btn i {
            font-size: 1rem;
            line-height: 1;
            display: block;
            /* Ensure proper rendering */
            position: relative;
            top: 0;
            /* Adjust if needed after testing */
        }

        /* Adjust the spacing between icon and text */
        .custom-cart-btn i.me-2 {
            margin-right: 0.7rem !important;
        }
    </style>
@endsection
