@extends('layouts.admin')

@section('title', 'Edit Product')

@section('styles')
    <style>
        .sortable-images .sort-handle {
            cursor: move;
            z-index: 10;
            transition: background-color 0.2s;
        }

        .sortable-placeholder {
            border: 2px dashed #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
            height: 100px;
            width: 100px;
            margin-bottom: 0.5rem;
            border-radius: 0.25rem;
        }

        .sortable-images .col-md-3 {
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .sortable-images .col-md-3:hover {
            transform: scale(1.05);
            z-index: 5;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .ui-sortable-helper {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            transform: rotate(2deg);
            z-index: 100;
        }

        .sortable-images .col-md-3.sorting {
            opacity: 0.8;
        }

        /* Add a hint to make it clear the items are sortable */
        .sortable-images:before {
            content: "Drag images to reorder";
            display: block;
            margin-bottom: 10px;
            font-size: 0.8rem;
            color: #6c757d;
            font-style: italic;
        }

        .image-index {
            font-size: 0.75rem;
        }

        /* Loading overlay */
        .sort-loading {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            border-radius: 0.25rem;
        }

        .sort-loading .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h3 class="card-title">Edit Product: {{ $product->name }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="5">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($)</label>
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('price') is-invalid @enderror" id="price"
                                        name="price" value="{{ old('price', $product->price) }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock Quantity</label>
                                    <input type="number" min="0"
                                        class="form-control @error('stock') is-invalid @enderror" id="stock"
                                        name="stock" value="{{ old('stock', $product->stock) }}" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                name="category_id" required>
                                <option value="">Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Primary Product Image</label>
                            @if ($product->image)
                                <div class="mb-2">
                                    <img src="{{ asset('images/products/' . $product->image) }}" class="img-thumbnail"
                                        width="150" alt="{{ $product->name }}">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                                name="image">
                            <small class="form-text text-muted">Upload a new image to replace the current one.</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3"> <label for="additional_images" class="form-label">Additional Product
                                Images</label>
                            @if ($product->images && $product->images->where('is_primary', false)->count() > 0)
                                <div class="card mb-3">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-images me-2"></i>Product Gallery</span>
                                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Drag to
                                            reorder</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-2 sortable-images" id="sortable-images">
                                            @foreach ($product->images->where('is_primary', false)->sortBy('sort_order') as $image)
                                                <div class="col-md-3 mb-2" data-image-id="{{ $image->id }}">
                                                    <div class="position-relative">
                                                        <img src="{{ asset('images/products/' . $image->image_path) }}"
                                                            class="img-thumbnail" alt="Product image">
                                                        <span class="position-absolute badge bg-secondary sort-handle"
                                                            style="top: 0; left: 0; cursor: move;" title="Drag to reorder">
                                                            <i class="fas fa-arrows-alt"></i>
                                                        </span>
                                                        <span class="position-absolute badge bg-info image-index"
                                                            style="bottom: 0; left: 0;" title="Position">
                                                            {{ $loop->iteration }}
                                                        </span>
                                                        <form
                                                            action="{{ route('admin.products.remove-image', $image->id) }}"
                                                            method="POST" class="position-absolute"
                                                            style="top: 0; right: 0;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Are you sure you want to delete this image?');">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <input type="file" class="form-control @error('additional_images.*') is-invalid @enderror"
                                id="additional_images" name="additional_images[]" multiple>
                            <small class="form-text text-muted">Upload additional product images (up to 5). Hold Ctrl to
                                select multiple files.</small>
                            @error('additional_images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active (available for purchase)</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            // Update image position numbers
            function updateImagePositions() {
                $('#sortable-images .col-md-3').each(function(index) {
                    $(this).find('.image-index').text(index + 1);
                });
            }

            // Show loading overlay
            function showLoading() {
                const loadingOverlay = $(
                    '<div class="sort-loading"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                    );
                $('.card-body').append(loadingOverlay);
            }

            // Hide loading overlay
            function hideLoading() {
                $('.sort-loading').remove();
            }

            // Initialize sortable functionality for the images
            $("#sortable-images").sortable({
                handle: ".sort-handle",
                placeholder: "sortable-placeholder",
                helper: "clone",
                opacity: 0.7,
                tolerance: "pointer",
                revert: true,
                scroll: true,
                scrollSensitivity: 100,
                scrollSpeed: 5,
                cancel: "form, button",
                start: function(e, ui) {
                    ui.placeholder.height(ui.item.height());
                    ui.placeholder.width(ui.item.width());
                    // Add a class to show the item is being sorted
                    ui.item.addClass('sorting');
                },
                stop: function(e, ui) {
                    // Remove the sorting class
                    ui.item.removeClass('sorting');
                    // Update image positions
                    updateImagePositions();
                },
                update: function(event, ui) {
                    // Collect the image IDs in their new order
                    const imageIds = $(this).sortable('toArray', {
                        attribute: 'data-image-id'
                    });

                    console.log('Reordering images:', imageIds);

                    // Show loading indicator
                    showLoading();

                    // Send the new order to the server using AJAX
                    $.ajax({
                        url: "{{ route('admin.products.reorder-images', $product->id) }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        data: {
                            image_ids: imageIds
                        },
                        success: function(response) {
                            console.log('Reorder success:', response);

                            // Hide loading indicator
                            hideLoading();

                            // Show success message
                            const alertHtml =
                                '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                'Image order updated successfully' +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>';

                            // Remove any existing alerts and add the new one
                            $('.alert').remove();
                            $('#content nav').after(alertHtml);

                            // Auto dismiss alert after 3 seconds
                            setTimeout(function() {
                                $('.alert').alert('close');
                            }, 3000);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error updating image order:', xhr.responseText);
                            console.error('Status:', status);
                            console.error('Error:', error);

                            // Hide loading indicator
                            hideLoading();

                            const alertHtml =
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                'Error updating image order: ' + (xhr.responseJSON?.error ||
                                    'Please try again.') +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>';

                            // Remove any existing alerts and add the new one
                            $('.alert').remove();
                            $('#content nav').after(alertHtml);
                        }
                    });
                }
            }).disableSelection();

            // Add visual feedback when hovering over sortable items
            $("#sortable-images .col-md-3").hover(
                function() {
                    $(this).find('.sort-handle').addClass('bg-primary').removeClass('bg-secondary');
                },
                function() {
                    $(this).find('.sort-handle').addClass('bg-secondary').removeClass('bg-primary');
                }
            );

            // Allow canceling the sort with ESC key
            $(document).keydown(function(e) {
                if (e.key === "Escape" && $("#sortable-images").sortable("instance")) {
                    $("#sortable-images").sortable("cancel");
                }
            });

            // Initialize position numbers
            updateImagePositions();

            // Add tooltip for better UX
            $('.card-header small').tooltip();
            $('.sort-handle').tooltip();
        });
    </script>
@endsection
