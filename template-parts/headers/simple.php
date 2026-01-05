<?php
/**
 * Simple Header Template Part
 * Bootstrap Example: Simple header
 * 
 * site-header class is omitted here but is in the header.php
 */
?>
<header class="d-flex flex-wrap h-100 justify-content-center py-3 border-bottom">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none" data-aos="fade-in">
        <?php echo bootstrap_theme_get_responsive_logo(); ?>        
    </a>
    
    <?php 
    echo bootstrap_theme_get_responsive_menu( array(
        'menu_class' => 'nav nav-pills',
        'container_class' => 'd-flex h-100 align-items-center',
        'collapse_id' => 'simpleNavbar',
        'show_cart' => true,
        'show_auth' => false,
    ) );
    ?>
</header>