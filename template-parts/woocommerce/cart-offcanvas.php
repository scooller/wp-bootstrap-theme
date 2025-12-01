<?php
/**
 * WooCommerce cart off-canvas template part
 *
 * @package BootstrapTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Solo mostrar si WooCommerce está activo
if (!class_exists('WooCommerce')) {
    return;
}
?>

<!-- Cart Off-canvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
    <div class="offcanvas-header bg-dark text-light">
        <h5 class="offcanvas-title" id="cartOffcanvasLabel">
            <svg class="icon me-2">
                <use xlink:href="#fa-cart-shopping"></use>
            </svg>
            <?php esc_html_e('Shopping Cart', 'bootstrap-theme'); ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div id="cart-offcanvas-content">
            <!-- Skeleton loader -->
            <div class="p-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="skeleton rounded me-3" style="width:48px;height:48px;display:inline-block;"></span>
                    <div class="flex-grow-1">
                        <div class="skeleton mb-2" style="height:18px;width:60%;"></div>
                        <div class="skeleton" style="height:14px;width:40%;"></div>
                    </div>
                </div>
                <div class="skeleton mb-2" style="height:18px;width:80%;"></div>
                <div class="skeleton" style="height:38px;width:100%;border-radius:6px;"></div>
            </div>
        </div>
    </div>
</div>