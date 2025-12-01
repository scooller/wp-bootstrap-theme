<?php
/**
 * Double Header Template Part
 * Bootstrap Example: Double header with top nav and main header
 */
?>

    <nav class="py-2 bg-body-tertiary border-bottom">
        <div class="container h-100 align-items-center d-flex flex-wrap">
            <div class="me-auto">
                <?php 
                echo bootstrap_theme_get_responsive_menu( array(
                    'menu_class' => 'nav',
                    'collapse_id' => 'doubleNavbar',
                    'show_cart' => false,
                    'show_auth' => false,
                ) );
                ?>
            </div>

            <ul class="nav">
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
                    <li class="nav-item item-cart">
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
                    </li>
                <?php endif; ?>
                
                <?php if ( is_user_logged_in() ) : ?>
                    <li class="nav-item item-logout"><a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="nav-link link-body-emphasis px-2"><?php esc_html_e( 'Logout', 'bootstrap-theme' ); ?></a></li>
                    <li class="nav-item item-dashboard"><a href="<?php echo esc_url( admin_url() ); ?>" class="nav-link link-body-emphasis px-2"><?php esc_html_e( 'Dashboard', 'bootstrap-theme' ); ?></a></li>
                <?php else : ?>
                    <li class="nav-item item-login"><a href="<?php echo esc_url( wp_login_url() ); ?>" class="nav-link link-body-emphasis px-2"><?php esc_html_e( 'Login', 'bootstrap-theme' ); ?></a></li>
                    <li class="nav-item item-sign-up"><a href="<?php echo esc_url( wp_registration_url() ); ?>" class="nav-link link-body-emphasis px-2"><?php esc_html_e( 'Sign up', 'bootstrap-theme' ); ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <header class="py-3 mb-4 border-bottom">
        <div class="container d-flex flex-wrap justify-content-center">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="d-flex align-items-center mb-3 mb-lg-0 me-lg-auto link-body-emphasis text-decoration-none">
                    <?php echo bootstrap_theme_get_responsive_logo(); ?>
                <span class="fs-4"><?php bloginfo( 'name' ); ?></span>
            </a>

            <form class="col-12 col-lg-auto mb-3 mb-lg-0" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search" name="s" class="form-control" placeholder="<?php esc_attr_e( 'Search...', 'bootstrap-theme' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'bootstrap-theme' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
            </form>
        </div>
    </header>