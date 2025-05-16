document.addEventListener('DOMContentLoaded', function () {
    // Get all product cards
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        // Find the form/buttons within the card
        const cardForm = card.querySelector('form');
        const formButtons = card.querySelectorAll('form button');
        const cardTitle = card.querySelector('.card-title');
        const productLink = card.querySelector('.product-link');

        // Add visual aria attributes for accessibility
        card.setAttribute('role', 'group');
        card.setAttribute('aria-label', cardTitle ? 'Product: ' + cardTitle.textContent.trim() : 'Product details');

        // Handle add to cart form specifically
        if (cardForm) {
            // We'll let cart-handler.js handle the form submission
            // Just prevent the click from propagating to the card
            cardForm.addEventListener('click', function (e) {
                e.stopPropagation();
            }, true); // Use capture phase
        }

        // Handle buttons within forms specifically
        formButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.stopPropagation();
                // We'll let cart-handler.js handle the button click
            }, true); // Use capture phase
        });

        // Handle keyboard navigation for accessibility
        card.addEventListener('keydown', function (e) {
            // If Enter or Space key is pressed
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (productLink) {
                    createRippleEffect(card, e.clientX, e.clientY);
                    setTimeout(() => {
                        window.location.href = productLink.getAttribute('href');
                    }, 300);
                }
            }
        });
    });

    // Function to create ripple effect
    function createRippleEffect(element, clientX, clientY) {
        // Create ripple element
        const ripple = document.createElement('div');

        // Get element position
        const rect = element.getBoundingClientRect();

        // Calculate position relative to the element
        const x = clientX - rect.left;
        const y = clientY - rect.top;

        // Style the ripple
        ripple.className = 'ripple-effect';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';

        // Add to element
        element.appendChild(ripple);

        // Remove after animation completes
        setTimeout(() => {
            ripple.remove();
        }, 800);
    }
});
