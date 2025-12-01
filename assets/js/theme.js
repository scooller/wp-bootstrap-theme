/**
 * Theme JavaScript
 * Bootstrap Theme by scooller
 */

(function($) {
    'use strict';

    // DOM Ready
    $(document).ready(function() {
        
        // Initialize theme functions
        initNavigation();
        initProductHover();
        initScrollToTop();
        initSearchToggle();
        initMobileMenu();
        
        // Initialize tooltips and popovers
        if (typeof bootstrap !== 'undefined') {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize Bootstrap popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        }
    });

    // Navigation Functions
    function initNavigation() {
        // Add active class to current menu item
        var currentUrl = window.location.href;
        $('.navbar-nav .nav-link').each(function() {
            if ($(this).attr('href') === currentUrl) {
                $(this).addClass('active');
            }
        });

        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 80
                }, 1000);
            }
        });

        // Navbar scroll effect
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('.navbar').addClass('scrolled');
            } else {
                $('.navbar').removeClass('scrolled');
            }
        });
    }

    // Product hover effects for WooCommerce
    function initProductHover() {
        $('.product-item').hover(
            function() {
                $(this).find('.product-actions').addClass('show');
            },
            function() {
                $(this).find('.product-actions').removeClass('show');
            }
        );

        // Quick view functionality (if needed)
        $('.quick-view-btn').on('click', function(e) {
            e.preventDefault();
            var productId = $(this).data('product-id');
            // Add your quick view logic here
            console.log('Quick view for product:', productId);
        });
    }

    // Scroll to top button
    function initScrollToTop() {
        // Create scroll to top button
        $('body').append('<button id="scroll-to-top" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px; z-index: 1000; display: none;"><i class="fas fa-arrow-up"></i></button>');

        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $('#scroll-to-top').fadeIn();
            } else {
                $('#scroll-to-top').fadeOut();
            }
        });

        $('#scroll-to-top').on('click', function() {
            $('html, body').animate({scrollTop: 0}, 800);
        });
    }

    // Search toggle functionality
    function initSearchToggle() {
        $('.search-toggle').on('click', function(e) {
            e.preventDefault();
            $('.search-form').toggleClass('show');
            $('.search-form input').focus();
        });

        // Close search when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-form, .search-toggle').length) {
                $('.search-form').removeClass('show');
            }
        });
    }

    // Mobile menu enhancements
    function initMobileMenu() {
        // Add dropdown indicators for mobile
        $('.navbar-nav .dropdown-toggle').append('<i class="fas fa-chevron-down ms-1"></i>');

        // Handle mobile submenu toggles
        $('.navbar-nav .dropdown-toggle').on('click', function(e) {
            if ($(window).width() < 992) {
                e.preventDefault();
                $(this).next('.dropdown-menu').slideToggle();
                $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
            }
        });
    }

    // Update cart count - Global function
    function updateCartCount() {
        if (typeof bootstrap_theme_ajax === 'undefined') {
            console.error('bootstrap_theme_ajax is not defined');
            return;
        }
        
        $.ajax({
            url: bootstrap_theme_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_cart_count',
                nonce: bootstrap_theme_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.cart-count').text(response.data);
                }
            },
            error: function() {
                console.error('Error updating cart count');
            }
        });
    }

    // Cart functionality
    function initCartFunctions() {

        // Add to cart AJAX
        $(document).on('click', '.ajax-add-to-cart', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var productId = $button.data('product-id');
            
            $button.addClass('loading').prop('disabled', true);
            
            $.ajax({
                url: bootstrap_theme_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'add_to_cart',
                    product_id: productId,
                    nonce: bootstrap_theme_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $button.removeClass('loading').prop('disabled', false);
                        updateCartCount();
                        
                        // Show success message
                        showNotification('Product added to cart!', 'success');
                    } else {
                        $button.removeClass('loading').prop('disabled', false);
                        showNotification('Error adding product to cart.', 'error');
                    }
                },
                error: function() {
                    $button.removeClass('loading').prop('disabled', false);
                    showNotification('Error adding product to cart.', 'error');
                }
            });
        });
    }

    // Notification system
    function showNotification(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var notification = $('<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            message +
            '</div>');

        $('body').append(notification);

        // Auto remove after 5 seconds
        setTimeout(function() {
            notification.alert('close');
        }, 5000);
    }

    // Form validation enhancement
    function initFormValidation() {
        // Add Bootstrap validation classes
        $('.needs-validation').on('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(this).addClass('was-validated');
        });

        // Real-time validation feedback
        $('.form-control').on('blur', function() {
            if (this.checkValidity()) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        });
    }

    // Initialize WooCommerce specific functions
    if (typeof wc_add_to_cart_params !== 'undefined') {
        initCartFunctions();
        initCartOffcanvas();
    }

    // Cart Off-canvas functionality
    function initCartOffcanvas() {
        // Update cart off-canvas content via AJAX
        function updateCartOffcanvasContent() {
            if (typeof bootstrap_theme_ajax === 'undefined') {
                console.error('bootstrap_theme_ajax is not defined');
                return;
            }

            $.ajax({
                url: bootstrap_theme_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_cart_offcanvas_content',
                    nonce: bootstrap_theme_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#cart-offcanvas-content').html(response.data);
                    }
                },
                error: function() {
                    console.error('Error loading cart content');
                }
            });
        }

        // On click, only act if this element actually toggles the offcanvas
        $(document).on('click', '.cart-toggle', function() {
            var target = $(this).attr('data-bs-target');
            if (target === '#cartOffcanvas') {
                updateCartOffcanvasContent();
                // Let Bootstrap data API handle showing; no need to preventDefault or call show()
            }
        });

        // Also refresh content whenever the offcanvas is about to show (covers other triggers)
        var offcanvasEl = document.getElementById('cartOffcanvas');
        if (offcanvasEl && typeof bootstrap !== 'undefined') {
            offcanvasEl.addEventListener('show.bs.offcanvas', function() {
                updateCartOffcanvasContent();
            });
        }

        // Update cart when items are added
        $(document.body).on('added_to_cart', function() {
            updateCartOffcanvasContent();
            updateCartCount();
        });
    }

    // Initialize form validation
    initFormValidation();

    // Image lazy loading fallback
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            var imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        }
    }

    // Initialize lazy loading
    initLazyLoading();

    // Accessibility improvements
    function initAccessibility() {
        // Skip link functionality
        $('.skip-link').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                target.focus();
            }
        });

        // Keyboard navigation for dropdowns
        $('.dropdown-toggle').on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).click();
            }
        });
    }

    // Initialize accessibility features
    initAccessibility();

})(jQuery);