<?php
/**
 * Centered Header Template Part
 * Bootstrap Example: Centered navigation
 * 
 * site-header class is omitted here but is in the header.php
 */
?>
<header class="py-3">
    <div class="w-100 d-flex justify-content-center mb-2">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="d-inline-flex align-items-center h-100 link-body-emphasis text-decoration-none" data-aos="fade-in">
            <?php echo bootstrap_theme_get_responsive_logo(); ?>
        </a>
    </div>

    <div class="d-md-flex h-100 justify-content-center text-center">
        <?php 
        echo bootstrap_theme_get_responsive_menu( array(
            'menu_class' => 'nav nav-pills',
            'collapse_id' => 'centeredNavbar',
            'show_cart' => true,
            'show_auth' => false,
        ) );
        ?>
    </div>
</header>