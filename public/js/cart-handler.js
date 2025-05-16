/**
 * Cart handling functionality for the ecommerce platform
 * Handles AJAX cart additions from product cards and detail pages
 */
$(document).ready(function () {
    console.log('Cart handler initialized');

    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle form submissions
    $(document).on('submit', '.add-to-cart-form', function (e) {
        console.log('Add to cart form submitted');
        e.preventDefault(); // Prevent default form submission
        e.stopPropagation(); // Stop event propagation

        const $form = $(this);
        handleAddToCart($form);
        return false; // Ensure no default action
    });

    // Also handle button clicks directly for better responsiveness
    $(document).on('click', '.add-to-cart-btn', function (e) {
        console.log('Add to cart button clicked');
        e.preventDefault(); // Prevent default button action
        e.stopPropagation(); // Stop event propagation

        const $form = $(this).closest('form');
        if ($form.length) {
            handleAddToCart($form);
        } else {
            console.error('Form not found for button:', this);
        }
        return false; // Ensure no default action
    });

    // Function to handle add to cart action
    function handleAddToCart($form) {
        console.log('Handling add to cart for form:', $form);

        // Check if form is valid
        if (!$form.length) {
            console.error('Form not found');
            showToast('Error', 'Form not found', 'danger');
            return;
        }

        const $button = $form.find('button');
        const originalButtonText = $button.html();
        const formAction = $form.attr('action');

        // Get form data
        const formData = new FormData($form[0]);

        console.log('Form action:', formAction);
        console.log('Form data:', Array.from(formData.entries()));

        // Prevent duplicate submissions
        if ($button.prop('disabled')) {
            console.log('Button is disabled, preventing duplicate submission');
            return;
        }

        // Add loading state
        $button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        $button.prop('disabled', true);

        // AJAX request to add product to cart
        $.ajax({
            url: formAction,
            type: "POST",
            data: $form.serialize(),
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (response) {
                console.log('AJAX success response:', response);
                if (response.success) {
                    // Update cart count in nav
                    updateCartBadge(response.cartCount);

                    // Show success message as toast
                    showToast('Success', response.message, 'success');

                    // Restore button
                    $button.html(originalButtonText);
                    $button.prop('disabled', false);
                } else {
                    // Show error message as toast
                    showToast('Error', response.message || 'Unknown error', 'danger');
                    $button.html(originalButtonText);
                    $button.prop('disabled', false);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', status, error);
                console.log('Response:', xhr.responseText);

                // Try to parse the error response
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    console.log('Parsed error response:', errorResponse);

                    if (errorResponse.message) {
                        showToast('Error', errorResponse.message, 'danger');
                    } else {
                        showToast('Error', 'An error occurred. Please try again.', 'danger');
                    }
                } catch (e) {
                    showToast('Error', 'An error occurred. Please try again.', 'danger');
                }

                $button.html(originalButtonText);
                $button.prop('disabled', false);
            }
        });
    }

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
        console.log('Showing toast:', title, message, type);

        // Create toast container if it doesn't exist
        if ($('#toast-container').length === 0) {
            $('body').append('<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>');
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
            $toast.on('hidden.bs.toast', function () {
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
