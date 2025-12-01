<?php

/**
 * WooCommerce single product template part
 *
 * @package BootstrapTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;
?>


<div class="woocommerce-product-single">
    <div class="row g-0 align-items-center">
        <div class="col-md-6">
            <div class="p-3 product-image text-center">
                <?php
                $image_id = $product->get_image_id();
                if ($image_id):
                    $img_url = wp_get_attachment_url($image_id);
                    $img_large = wp_get_attachment_image_url($image_id, 'large');
                    $img_alt = get_the_title();
                ?>
                    <a href="<?php echo esc_url($img_url); ?>" 
                       class="product-image-fancybox wow animate__fadeIn shadow-effect2" 
                       data-fancybox="product-gallery" 
                       data-caption="<?php echo esc_attr($img_alt); ?>" 
                       id="product-main-image-link">
                        <img id="product-main-image" 
                             src="<?php echo esc_url($img_large); ?>" 
                             class="img-fluid rounded-4 mx-auto"  
                             alt="<?php echo esc_attr($img_alt); ?>">
                    </a>
                <?php else: ?>
                    <img id="product-main-image" 
                         src="<?php echo wc_placeholder_img_src(); ?>" 
                         class="img-fluid rounded-4 mx-auto"
                         alt="Placeholder">
                <?php endif; ?>
                
                <div id="product-thumbnails" class="mt-3 d-flex flex-wrap justify-content-center gap-2">
                    <?php
                    $gallery_ids = $product->get_gallery_image_ids();
                    if (!empty($gallery_ids)):
                        foreach ($gallery_ids as $gallery_id):
                            $gallery_url = wp_get_attachment_url($gallery_id);
                            $gallery_caption = get_the_title();
                    ?>
                        <a href="<?php echo esc_url($gallery_url); ?>" 
                           class="product-image-fancybox wow animate__fadeIn" 
                           data-fancybox="product-gallery" 
                           data-caption="<?php echo esc_attr($gallery_caption); ?>">
                            <?php 
                            echo wp_get_attachment_image($gallery_id, 'thumbnail', false, [
                                'class' => 'img-fluid rounded-3 smooth-shadow mx-auto',
                                'style' => 'width:72px;height:72px;object-fit:cover;'
                            ]); 
                            ?>
                        </a>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="product-detail p-4">
                <?php
                // Mostrar avisos/notices de WooCommerce (errores, mensajes)
                if (function_exists('woocommerce_output_all_notices')) {
                    woocommerce_output_all_notices();
                }
                ?>
                <h1 class="h2 fw-bold mb-3"><?php woocommerce_template_single_title(); ?></h1>
                <div class="d-flex align-items-center mb-2 product-reviews">
                    <span class="fs-2 fw-bold me-3 text-light"><?php woocommerce_template_single_price(); ?></span>
                    <span class="me-2"><?php woocommerce_template_single_rating(); ?></span>
                    <?php
                    $count = $product->get_review_count();
                    if ($count > 0):
                    ?>
                        <a href="#reviews" class="text-decoration-underline text-light small">
                            <?php echo esc_html($count); ?> <?php esc_html_e('Reviews', 'bootstrap-theme'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="mb-3 text-muted fs-5 product-description">
                    <?php
                    $description = $product->get_description();
                    if ($description):
                        echo wpautop(do_shortcode($description));
                    endif;
                    ?>
                </div>
                <hr class="my-3">
                <div class="mb-3 product-categories">
                    <strong class="mb-2 d-block"><?php esc_html_e('Categories', 'bootstrap-theme'); ?></strong>
                    <?php
                    $cats = get_the_terms($product->get_id(), 'product_cat');
                    if ($cats && !is_wp_error($cats)):
                        foreach ($cats as $cat):
                    ?>
                        <span class="badge rounded-pill bg-dark border border-light text-light me-2 mb-2 px-3 py-2" 
                              style="font-size:1rem;cursor:pointer;" 
                              onclick="location.href='<?php echo esc_url(get_term_link($cat)); ?>'">
                            <?php echo esc_html($cat->name); ?>
                        </span>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </div>
                <div class="mt-4 product-add-to-cart">
                    <?php
                    // Para productos variables, usar el formulario de WooCommerce
                    if ($product->is_type('variable')) {
                        woocommerce_template_single_add_to_cart();
                    } else {
                        // Para productos simples, usar formulario que pasa por validación de WooCommerce
                        ?>
                        <?php do_action('woocommerce_before_add_to_cart_form'); ?>
                        <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
                            <?php do_action('woocommerce_before_add_to_cart_button'); ?>
                            
                            <div class="row align-items-end product-actions">
                                <div class="mb-2 col-md-4 col-12 <?php echo $product->is_sold_individually() ? 'd-none' : ''; ?>">
                                    <?php
                                    do_action('woocommerce_before_add_to_cart_quantity');
                                    
                                    woocommerce_quantity_input(
                                        array(
                                            'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
                                            'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
                                            'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(),
                                            'input_id'    => 'product-qty',
                                            'classes'     => array('form-control', 'bg-dark', 'text-light', 'border-light'),
                                        )
                                    );
                                    
                                    do_action('woocommerce_after_add_to_cart_quantity');
                                    ?>
                                </div>
                                <?php if ($product->is_sold_individually()) : ?>
                                <div class="mb-2 col-md-4 col-12">
                                    <span class="badge rounded-pill border border-light px-3 py-2 sold-individually"><?php esc_html_e('Cantidad fija: 1', 'bootstrap-theme'); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="mb-2 col-md col-12">
                                    <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button btn btn-success btn-lg w-100 rounded-pill wow animate__bounceIn" style="font-weight:600;" disabled aria-disabled="true" title="<?php echo esc_attr__('Deshabilitado hasta comprobar disponibilidad', 'bootstrap-theme'); ?>">
                                        <span><?php echo esc_html($product->single_add_to_cart_text()); ?></span>
                                        <svg class="icon ms-2"><use xlink:href="#fa-cart-plus"></use></svg>
                                    </button>
                                </div>
                            </div>
                            
                            <?php do_action('woocommerce_after_add_to_cart_button'); ?>
                        </form>
                        <?php do_action('woocommerce_after_add_to_cart_form'); ?>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <?php 
            // Check if related products should be displayed
            if (function_exists('bootstrap_theme_get_woocommerce_option')) {
                $show_related = bootstrap_theme_get_woocommerce_option('show_related_products');
                if ($show_related) {
                    woocommerce_output_related_products();
                }
            } else {
                // Default behavior if ACF is not available
                woocommerce_output_related_products();
            }
            ?>
        </div>
    </div>
</div>

<?php if ($product->is_type('variable')) : ?>
<script>
jQuery(document).ready(function($) {
    var $form = $('form.variations_form');
    
    if ($form.length) {
        $form.on('found_variation', function(event, variation) {
            // Actualizar imagen principal si la variación tiene imagen
            if (variation.image && variation.image.url) {
                var $mainImage = $('#product-main-image');
                var $mainLink = $('#product-main-image-link');
                
                // Actualizar src de la imagen
                if ($mainImage.length) {
                    $mainImage.attr('src', variation.image.url);
                    $mainImage.attr('srcset', variation.image.srcset || '');
                    $mainImage.attr('alt', variation.image.alt || '');
                }
                
                // Actualizar href del enlace fancybox
                if ($mainLink.length) {
                    $mainLink.attr('href', variation.image.full_src || variation.image.url);
                    $mainLink.attr('data-caption', variation.image.caption || variation.image.title || '');
                }
                
                // Actualizar galería si existe
                if (variation.image.gallery_thumbnail_src && $('#product-thumbnails').length) {
                    // Aquí podrías agregar lógica para actualizar thumbnails de galería
                }
            }
        });
        
        // Restaurar imagen original al resetear variación
        $form.on('reset_data', function() {
            var $mainImage = $('#product-main-image');
            var $mainLink = $('#product-main-image-link');
            
            // Obtener la imagen original del producto
            <?php 
            $original_image_id = $product->get_image_id();
            if ($original_image_id) {
                $original_url = wp_get_attachment_image_url($original_image_id, 'large');
                $original_full = wp_get_attachment_url($original_image_id);
                $original_srcset = wp_get_attachment_image_srcset($original_image_id, 'large');
                $original_alt = get_post_meta($original_image_id, '_wp_attachment_image_alt', true);
            ?>
            if ($mainImage.length) {
                $mainImage.attr('src', '<?php echo esc_url($original_url); ?>');
                $mainImage.attr('srcset', '<?php echo esc_attr($original_srcset); ?>');
                $mainImage.attr('alt', '<?php echo esc_attr($original_alt); ?>');
            }
            if ($mainLink.length) {
                $mainLink.attr('href', '<?php echo esc_url($original_full); ?>');
                $mainLink.attr('data-caption', '<?php echo esc_attr(get_the_title()); ?>');
            }
            <?php } ?>
        });
    }
});
</script>
<?php endif; ?>
