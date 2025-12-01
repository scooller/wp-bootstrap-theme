<?php
/**
 * Header with Login Buttons Template Part
 * Bootstrap Example: Header with login buttons
 * 
 * site-header class is omitted here but is in the header.php
 */
?>
<header class="d-flex flex-wrap align-items-center h-100 justify-content-center justify-content-md-between py-3 border-bottom">
    <div class="col-md-3 mb-2 mb-md-0">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="d-inline-flex link-body-emphasis text-decoration-none wow animate__fadeIn">
            <?php echo bootstrap_theme_get_responsive_logo(''); ?>
        </a>
    </div>

    <div class="col-12 col-md-auto">
        <?php 
        echo bootstrap_theme_get_responsive_menu( array(
            'menu_class' => 'nav justify-content-center',
            'container_class' => 'd-flex h-100 flex-column flex-md-row align-items-center',
            'collapse_id' => 'buttonsNavbar',
            'show_cart' => true,
            'show_auth' => true,
        ) );
        ?>
    </div>
</header>