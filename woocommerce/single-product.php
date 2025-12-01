<?php
/**
 * The Template for displaying single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * @package BootstrapTheme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop'); ?>

<main id="primary" class="site-main">
    <?php while (have_posts()) : the_post(); ?>
    <?php
    $container_width = get_field('container_width') ?: bootstrap_theme_get_option('container_width');

    // Determinar clase de contenedor
    $container_class = 'container';
    if ($container_width === 'fluid') {
        $container_class = 'container-fluid';
    } elseif ($container_width && $container_width !== 'default' && $container_class !== 'container') {
        $container_class = 'container-' . $container_width;
    }
    ?>
        <div class="<?php echo esc_attr($container_class); ?> my-4">
            <?php wc_get_template_part('content', 'single-product'); ?>
        </div>
        
    <?php endwhile; ?>
</main>

<?php
get_footer('shop');