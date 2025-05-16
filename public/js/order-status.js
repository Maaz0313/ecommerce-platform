// Order status update script
document.addEventListener('DOMContentLoaded', function () {
    console.log('Order status script loaded');

    // Initialize all dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    dropdownElementList.forEach(function (dropdownToggleEl) {
        var dropdown = new bootstrap.Dropdown(dropdownToggleEl, {
            autoClose: 'outside'
        });
    });

    // Add event listeners to all status links
    document.querySelectorAll('.status-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            const orderId = this.getAttribute('data-order-id');
            const status = this.getAttribute('data-status');

            console.log(`Updating order #${orderId} status to: ${status}`);

            // Set the status value in the hidden form
            const statusInput = document.getElementById(`status-input-${orderId}`);
            if (statusInput) {
                statusInput.value = status;

                // Submit the form
                const form = document.getElementById(`status-form-${orderId}`);
                if (form) {
                    form.submit();
                } else {
                    console.error(`Form not found: status-form-${orderId}`);
                }
            } else {
                console.error(`Status input not found: status-input-${orderId}`);
            }
        });
    });

    // Fix for dropdown positioning
    window.addEventListener('shown.bs.dropdown', function (e) {
        // Ensure the dropdown menu is fully visible
        const dropdown = e.target.nextElementSibling;
        if (dropdown && dropdown.classList.contains('dropdown-menu')) {
            dropdown.style.display = 'block';
            dropdown.style.position = 'absolute';
            dropdown.style.inset = 'auto auto auto auto';
            dropdown.style.transform = 'none';
            dropdown.style.maxHeight = 'none';
        }
    });
});
