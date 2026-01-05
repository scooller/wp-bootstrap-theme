<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * @package Bootstrap Theme
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

// Variable global para animación con autoincremento
global $woocommerce_product_animation_delay,$n_product;
if ( ! isset( $woocommerce_product_animation_delay ) ) {
	$woocommerce_product_animation_delay = 0;
    $n_product = 0;
}
$n_product++;
$delay_seconds = $woocommerce_product_animation_delay * 0.1;
$woocommerce_product_animation_delay++;
if($n_product%21==0){
    $woocommerce_product_animation_delay = 0;
}
// Calcular clases responsivas según opciones del tema
$desktop_cols = function_exists('bootstrap_theme_get_woocommerce_option') ? (int) bootstrap_theme_get_woocommerce_option('products_per_row') : 4;
if ($desktop_cols < 1) { $desktop_cols = 4; }
$mobile_cols = function_exists('bootstrap_theme_get_woocommerce_option') ? (int) bootstrap_theme_get_woocommerce_option('products_per_row_mobile') : 2;
if ($mobile_cols < 1) { $mobile_cols = 2; }
// Asegurar divisores válidos para Bootstrap (1-12)
$desktop_span = max(1, min(12, (int) round(12 / $desktop_cols)));
$mobile_span = max(1, min(12, (int) round(12 / $mobile_cols)));
$desktop_class = 'col-lg-' . $desktop_span;
// Usar misma clase para md para consistencia
$md_class = 'col-md-' . $desktop_span;
$mobile_class = 'col-' . $mobile_span;
// Clase extra según tipo de producto
$product_type_class = '';
if ( isset( $product ) && is_a( $product, 'WC_Product' ) ) {
	$ptype = $product->get_type();
	if ( $ptype ) {
		$product_type_class = ' product-type-' . sanitize_html_class( $ptype );
	}
}
?>
<div class="item-product <?php echo esc_attr($mobile_class . ' ' . $md_class . ' ' . $desktop_class); ?><?php echo esc_attr( $product_type_class ); ?>" data-aos="flip-left" data-aos-delay="<?php echo esc_attr( intval( $delay_seconds * 100 ) ); ?>">
<div <?php wc_product_class( 'card h-100 text-center smooth-shadow', $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' );
	?>

	<div class="card-img-top position-relative">
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop_item_title.
		 *
		 * @hooked woocommerce_show_product_loop_sale_flash - 10
		 * @hooked woocommerce_template_loop_product_thumbnail - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item_title' );
		?>
	</div>

	<div class="card-body d-flex flex-column">
		<?php
		/**
		 * Hook: woocommerce_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		do_action( 'woocommerce_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_after_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );
		?>
		<div class="data-group">
			<!-- price and add to cart button -->
			<div class="mb-2">
				<?php
				// Mostrar precio aquí además del hook superior, para ubicarlo junto al botón
				if ( function_exists( 'woocommerce_template_loop_price' ) ) {
					woocommerce_template_loop_price();
				}
				?>
			</div>
			<div class="woo-btns mt-auto">
				<?php
				/**
				 * Hook: woocommerce_after_shop_loop_item.
				 *
				 * @hooked woocommerce_template_loop_product_link_close - 5
				 * @hooked woocommerce_template_loop_add_to_cart - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item' );
				?>
			</div>
		</div>
	</div>
</div>
</div>
