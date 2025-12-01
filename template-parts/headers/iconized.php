<?php
/**
 * Complex Iconized Header Template Part
 * Bootstrap Example: Complex header with icons and search
 */
$dashboard_url = is_user_logged_in() ? admin_url() : '';
//get woocommerce dashboard url
$dashboard_url = function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('dashboard') : $dashboard_url;
?>
<header class="header-iconized">
    <div class="px-3 py-2 text-bg-dark border-bottom">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between justify-content-lg-start">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="d-flex align-items-center h-100 my-2 my-lg-0 me-lg-auto text-white text-decoration-none wow animate__fadeIn">
                    <?php echo bootstrap_theme_get_responsive_logo(); ?>
                </a>

                <div class="col-12 col-lg-auto h-100 my-2">
                    <?php 
                    echo bootstrap_theme_get_responsive_menu( array(
                        'menu_class' => 'nav justify-content-center text-small',
                        'collapse_id' => 'iconizedNavbar',
                        'show_cart' => false,
                        'show_auth' => false,
                    ) );
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="px-3 py-2 border-bottom mb-3">
        <div class="container d-flex flex-wrap justify-content-center">
            <form class="col-12 col-lg-auto mb-2 mb-lg-0 me-lg-auto" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search" name="s" class="form-control" placeholder="<?php esc_attr_e('Buscar...', 'bootstrap-theme'); ?>" aria-label="<?php esc_attr_e('Buscar', 'bootstrap-theme'); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
            </form>

            <div class="text-end">
                <?php 
                $show_cart = function_exists('bootstrap_theme_get_woocommerce_option') 
                    ? bootstrap_theme_get_woocommerce_option('show_cart_icon') 
                    : true;
                if ( class_exists( 'WooCommerce' ) && $show_cart ) : 
                    // Get cart action preference
                    $cart_action = function_exists('bootstrap_theme_get_woocommerce_option') 
                        ? bootstrap_theme_get_woocommerce_option('cart_icon_action') 
                        : 'offcanvas';
                    
                    // Build button attributes based on action
                    $button_attrs = '';
                    $button_url = '#';
                    
                    switch ($cart_action) {
                        case 'cart':
                            $button_url = esc_url( wc_get_cart_url() );
                            $button_attrs = '';
                            break;
                        case 'checkout':
                            $button_url = esc_url( wc_get_checkout_url() );
                            $button_attrs = '';
                            break;
                        case 'offcanvas':
                        default:
                            $button_attrs = 'data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas"';
                            break;
                    }
                ?>
                    <?php if ($cart_action === 'offcanvas') : ?>
                        <button type="button" class="btn btn-outline-primary position-relative cart-toggle me-2" <?php echo $button_attrs; ?> aria-label="<?php esc_attr_e( 'Open Shopping Cart', 'bootstrap-theme' ); ?>">
                            <svg class="icon">
                                <use xlink:href="#fa-cart-shopping"></use>
                            </svg>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                                <?php echo WC()->cart->get_cart_contents_count(); ?>
                            </span>
                        </button>
                    <?php else : ?>
                        <a href="<?php echo $button_url; ?>" class="btn btn-outline-primary position-relative cart-toggle me-2" aria-label="<?php esc_attr_e( 'View Shopping Cart', 'bootstrap-theme' ); ?>">
                            <svg class="icon">
                                <use xlink:href="#fa-cart-shopping"></use>
                            </svg>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                                <?php echo WC()->cart->get_cart_contents_count(); ?>
                            </span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( $dashboard_url ); ?>" class="btn btn-primary btn-dashboard">
                        <svg class="icon">
                            <use xlink:href="#fa-gauge"></use>
                        </svg>
                        <?php esc_html_e( 'Dashboard', 'bootstrap-theme' ); ?>
                    </a>
                    <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="btn btn-outline-primary btn-logout me-2">
                        <svg class="icon">
                            <use xlink:href="#fa-right-from-bracket"></use>
                        </svg>
                        <?php esc_html_e( 'Logout', 'bootstrap-theme' ); ?>
                    </a>                    
                <?php else : ?>
                    <a href="<?php echo esc_url( wp_login_url() ); ?>" class="btn btn-light text-dark btn-login me-2">
                        <svg class="icon">
                            <use xlink:href="#fa-right-to-bracket"></use>
                        </svg>
                        <?php esc_html_e( 'Login', 'bootstrap-theme' ); ?>
                    </a>                    
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>