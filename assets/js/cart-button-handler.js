/**
 * Cart Button Classes Handler
 * Updates "View Cart" button classes to Bootstrap format
 */
(function($) {
    'use strict';

    function updateCartButtonClasses() {
        const cartButtons = document.querySelectorAll('a.added_to_cart.wc-forward');
        cartButtons.forEach(function(button) {
            button.className = button.className.replace('added_to_cart wc-forward', 'btn btn-primary btn-sm mt-2 w-100');
        });
    }

    $(document).ready(function() {
        updateCartButtonClasses();

        // Update after WooCommerce AJAX events
        $(document.body).on('added_to_cart', function() {
            setTimeout(updateCartButtonClasses, 100);
        });

        // Observe DOM changes for dynamically added buttons
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    updateCartButtonClasses();
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });

})(jQuery);
