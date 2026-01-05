<?php
/**
 * Header with Avatar Template Part
 * Bootstrap Example: Header with avatar and search
 */
?>
<header class="p-3 border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between justify-content-lg-start">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="d-flex align-items-center h-100 mb-2 mb-lg-0 link-body-emphasis text-decoration-none" data-aos="fade-in">
                <?php echo bootstrap_theme_get_responsive_logo(); ?>
            </a>

            <div class="col-12 h-100 col-lg-auto me-lg-auto">
                <?php 
                echo bootstrap_theme_get_responsive_menu( array(
                    'menu_class' => 'nav justify-content-center',
                    'collapse_id' => 'avatarNavbar',
                    'show_cart' => false,
                    'show_auth' => false,
                ) );
                ?>
            </div>

            <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search" name="s" class="form-control" placeholder="<?php esc_attr_e('Buscar...', 'bootstrap-theme'); ?>" aria-label="<?php esc_attr_e('Buscar', 'bootstrap-theme'); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
            </form>

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
                    <button type="button" class="btn btn-outline-primary position-relative cart-toggle me-3" <?php echo $button_attrs; ?> aria-label="<?php esc_attr_e( 'Open Shopping Cart', 'bootstrap-theme' ); ?>">
                        <svg class="icon">
                            <use xlink:href="#fa-cart-shopping"></use>
                        </svg>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                            <?php echo WC()->cart->get_cart_contents_count(); ?>
                        </span>
                    </button>
                <?php else : ?>
                    <a href="<?php echo $button_url; ?>" class="btn btn-outline-primary position-relative cart-toggle me-3" aria-label="<?php esc_attr_e( 'View Shopping Cart', 'bootstrap-theme' ); ?>">
                        <svg class="icon">
                            <use xlink:href="#fa-cart-shopping"></use>
                        </svg>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                            <?php echo WC()->cart->get_cart_contents_count(); ?>
                        </span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <div class="dropdown text-end">
                <?php if ( is_user_logged_in() ) : ?>
                    <?php $current_user = wp_get_current_user(); ?>
                    <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo get_avatar( $current_user->ID, 32, '', '', array( 'class' => 'rounded-circle' ) ); ?>
                    </a>
                    <ul class="dropdown-menu text-small">
                        <li class="item-settings"><a class="dropdown-item" href="<?php echo esc_url( admin_url( 'options-general.php' ) ); ?>">
                            <svg class="icon">
                                <use xlink:href="#fa-gear"></use>
                            </svg>
                            <?php esc_html_e( 'Settings', 'bootstrap-theme' ); ?></a>
                        </li>
                        <li class="item-profile"><a class="dropdown-item" href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>">
                            <svg class="icon">
                                <use xlink:href="#fa-user"></use>
                            </svg>
                            <?php esc_html_e( 'Profile', 'bootstrap-theme' ); ?>
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li class="item-sign-out"><a class="dropdown-item" href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">
                            <svg class="icon">
                                <use xlink:href="#fa-right-from-bracket"></use>
                            </svg>
                            <?php esc_html_e( 'Sign out', 'bootstrap-theme' ); ?>
                        </a></li>
                    </ul>
                <?php else : ?>
                    <a href="<?php echo esc_url( wp_login_url() ); ?>" class="d-block btn-login link-body-emphasis text-decoration-none">
                        <svg class="icon">
                            <use xlink:href="#fa-right-to-bracket"></use>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>