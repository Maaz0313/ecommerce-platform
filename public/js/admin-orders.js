// Admin orders page specific script
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin orders script loaded');
    
    // Fix for dropdown menus in the orders table
    if (document.querySelector('.table-responsive')) {
        // Make sure the table doesn't clip the dropdowns
        document.querySelector('.table-responsive').style.overflow = 'visible';
        
        // Make sure the card doesn't clip the dropdowns
        const cardBody = document.querySelector('.card-body');
        if (cardBody) {
            cardBody.style.overflow = 'visible';
        }
        
        const card = document.querySelector('.card');
        if (card) {
            card.style.overflow = 'visible';
        }
    }
    
    // Add event listener for dropdown toggle
    document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            // Force the dropdown to be visible
            setTimeout(function() {
                const menu = toggle.nextElementSibling;
                if (menu && menu.classList.contains('dropdown-menu')) {
                    menu.style.display = 'block';
                    menu.style.position = 'absolute';
                    menu.style.transform = 'none';
                    menu.style.inset = 'auto auto auto auto';
                    menu.style.maxHeight = 'none';
                    menu.style.overflow = 'visible';
                    menu.style.zIndex = '1050';
                }
            }, 0);
        });
    });
});
