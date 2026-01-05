<?php
/**
 * Bootstrap Blocks Registration
 * 
 * @package Bootstrap_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include helper for className support
require_once get_template_directory() . '/inc/admin/blocks-className-fix.php';

/**
 * Include all Bootstrap blocks (alphabetically ordered)
 */

// Get all block directories
$blocks_dir = get_template_directory() . '/blocks';
if (is_dir($blocks_dir)) {
    $blocks = array_filter(glob($blocks_dir . '/*'), 'is_dir');
    
    foreach ($blocks as $block) {
        $block_name = basename($block);
        // Skip non-block directories
        if (!str_starts_with($block_name, 'bs-')) {
            continue;
        }
        
        // Skip WooCommerce blocks if WooCommerce is not active
        $woocommerce_blocks = ['bs-cart', 'bs-wc-products', 'bs-checkout-custom-fields', 'bs-shipping-methods'];
        if (in_array($block_name, $woocommerce_blocks, true) && !class_exists('WooCommerce')) {
            continue;
        }
        
        $block_file = $block . '/block.php';
        if (file_exists($block_file)) {
            require_once $block_file;
        }
    }
}

/**
 * Enqueue block editor assets
 */
function bootstrap_theme_block_editor_assets() {
    // List of all Bootstrap blocks that need editor.js loaded
    $blocks_with_editors = [
        'bs-accordion', 'bs-accordion-item', 'bs-alert', 'bs-badge', 'bs-breadcrumb',
        'bs-breadcrumb-item', 'bs-button', 'bs-button-group', 'bs-card', 'bs-carousel',
        'bs-carousel-item', 'bs-column', 'bs-container', 'bs-dropdown', 'bs-dropdown-divider',
        'bs-dropdown-item', 'bs-list-group', 'bs-list-group-item', 'bs-menu', 'bs-modal', 
        'bs-offcanvas', 'bs-pagination', 'bs-pagination-item', 'bs-progress', 'bs-row', 
        'bs-spinner', 'bs-tab-pane', 'bs-tabs', 'bs-toast'
    ];
    
    // Add WooCommerce blocks only if WooCommerce is active
    if (class_exists('WooCommerce')) {
        $blocks_with_editors[] = 'bs-cart';
        $blocks_with_editors[] = 'bs-wc-products';
        $blocks_with_editors[] = 'bs-shipping-methods';
    }

    // Load editor.js for each block
    foreach ($blocks_with_editors as $block_name) {
        $editor_file = get_template_directory() . "/blocks/{$block_name}/editor.js";
        if (file_exists($editor_file)) {
            wp_enqueue_script(
                "bootstrap-theme-{$block_name}-editor",
                get_template_directory_uri() . "/blocks/{$block_name}/editor.js",
                array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n'),
                BOOTSTRAP_THEME_VERSION,
                true
            );
        }
    }

    // Enqueue Bootstrap CSS for block editor (same as frontend)
    wp_enqueue_style(
        'bootstrap-editor',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
        array(),
        '5.3.2'
    );

    // Enqueue FontAwesome for block editor
    wp_enqueue_style(
        'font-awesome-editor',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        array(),
        '6.4.0'
    );

    // Enqueue custom block editor styles
    wp_enqueue_style(
        'bootstrap-theme-blocks-editor',
        get_template_directory_uri() . '/blocks/blocks-editor.css',
        array('bootstrap-editor'),
        BOOTSTRAP_THEME_VERSION
    );

    // AOS in editor for preview of animations
    wp_enqueue_style(
        'aos-css-editor',
        'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css',
        array(),
        '2.3.4'
    );

    // AOS JavaScript in editor for preview
    wp_enqueue_script(
        'aos-js-editor',
        'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js',
        array(),
        '2.3.4',
        true
    );

    // AOS controls for core paragraph/heading blocks
    wp_enqueue_script(
        'bootstrap-theme-aos-core-blocks',
        get_template_directory_uri() . '/blocks/aos-core-blocks.js',
        array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-hooks', 'wp-compose', 'wp-dom-ready'),
        BOOTSTRAP_THEME_BUILD_VERSION,
        true
    );
}
add_action('enqueue_block_editor_assets', 'bootstrap_theme_block_editor_assets');

/**
 * Register Bootstrap block category
 */
function bootstrap_theme_register_block_category($categories) {
    // Add Bootstrap category at the beginning for better visibility
    array_unshift($categories, array(
        'slug'  => 'bootstrap',
        'title' => __('Bootstrap', 'bootstrap-theme'),
        'icon'  => 'admin-customizer'
    ));

    return $categories;
}
add_filter('block_categories_all', 'bootstrap_theme_register_block_category');

/**
 * Enqueue block frontend assets
 */
function bootstrap_theme_enqueue_block_assets() {
    wp_enqueue_style(
        'bootstrap-theme-blocks-frontend',
        get_template_directory_uri() . '/blocks/blocks-frontend.css',
        array(),
        BOOTSTRAP_THEME_VERSION
    );

    // Enqueue WooCommerce block assets only if WooCommerce is active
    if (class_exists('WooCommerce')) {
        // Enqueue cart block specific styles
        wp_enqueue_style(
            'bootstrap-theme-cart-block',
            get_template_directory_uri() . '/blocks/bs-cart/cart-block.css',
            array(),
            BOOTSTRAP_THEME_VERSION
        );
        
        // Enqueue cart block JavaScript
        wp_enqueue_script(
            'bootstrap-theme-cart-handler',
            get_template_directory_uri() . '/blocks/bs-cart/cart-update-handler.js',
            array('jquery'),
            BOOTSTRAP_THEME_VERSION,
            true
        );
        
        // Localize script with AJAX URL
        wp_localize_script('bootstrap-theme-cart-handler', 'bootstrapThemeCart', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bootstrap_theme_cart_nonce')
        ));
    }
}
add_action('enqueue_block_assets', 'bootstrap_theme_enqueue_block_assets');
