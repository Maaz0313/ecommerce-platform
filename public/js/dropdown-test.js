// Dropdown test script
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dropdown test script loaded');
    
    // Check if Bootstrap is loaded
    if (typeof bootstrap !== 'undefined') {
        console.log('Bootstrap is loaded');
        
        // Initialize all dropdowns
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
        
        console.log('Initialized ' + dropdownList.length + ' dropdowns');
    } else {
        console.error('Bootstrap is not loaded!');
    }
});
