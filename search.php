<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Bootstrap_Theme
 */

get_header();
?>

<div class="container-fluid">
	<div class="row">
		<main id="primary" class="site-main col-lg-8">

			<?php if ( have_posts() ) : ?>

				<header class="page-header mb-4">
					<h1 class="page-title">
						<?php
						/* translators: %s: search query. */
						printf( esc_html__( 'Search Results for: %s', 'bootstrap-theme' ), '<span class="text-primary">' . get_search_query() . '</span>' );
						?>
					</h1>
				</header><!-- .page-header -->

				<?php
				// Detectar si hay productos en los resultados
				$has_products = false;
				$has_other_content = false;
				$temp_query = clone $wp_query;
				
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						if ( 'product' === get_post_type() ) {
							$has_products = true;
						} else {
							$has_other_content = true;
						}
					endwhile;
					wp_reset_postdata();
				endif;

				// Si hay productos, mostrarlos en formato WooCommerce
				if ( $has_products ) :
					?>
					<div class="woocommerce">
						<?php if ( $has_other_content ) : ?>
							<h2 class="h4 mb-3"><?php esc_html_e( 'Products', 'bootstrap-theme' ); ?></h2>
						<?php endif; ?>

						<ul class="products row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mb-4">
							<?php
							while ( have_posts() ) :
								the_post();
								
								if ( 'product' === get_post_type() ) :
									global $product;
									
									// Ensure visibility
									if ( empty( $product ) || ! $product->is_visible() ) {
										continue;
									}

									// Variable global para animación con autoincremento
									global $woocommerce_product_animation_delay, $n_product;
									if ( ! isset( $woocommerce_product_animation_delay ) ) {
										$woocommerce_product_animation_delay = 0;
										$n_product = 0;
									}
									$n_product++;
									$delay_seconds = $woocommerce_product_animation_delay * 0.1;
									$woocommerce_product_animation_delay++;
									if ( $n_product % 21 == 0 ) {
										$woocommerce_product_animation_delay = 0;
									}
									?>
									<li class="item-product col" data-aos="flip-left" data-aos-delay="<?php echo esc_attr( intval( $delay_seconds * 100 ) ); ?>">
										<div <?php wc_product_class( 'card h-100 text-center smooth-shadow', $product ); ?>>
											<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

											<div class="card-img-top position-relative">
												<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
											</div>

											<div class="card-body d-flex flex-column">
												<?php
												do_action( 'woocommerce_shop_loop_item_title' );
												do_action( 'woocommerce_after_shop_loop_item_title' );
												?>

												<div class="mt-auto">
													<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
												</div>
											</div>
										</div>
									</li>
									<?php
								endif;
							endwhile;
							wp_reset_postdata();
							?>
						</ul>
					</div>
					<?php
				endif;

				// Si hay otro contenido (posts, páginas, etc)
				if ( $has_other_content ) :
					if ( $has_products ) :
						?>
						<h2 class="h4 mb-3 mt-5"><?php esc_html_e( 'Other Content', 'bootstrap-theme' ); ?></h2>
						<?php
					endif;

					while ( have_posts() ) :
						the_post();

						if ( 'product' !== get_post_type() ) :
							get_template_part( 'template-parts/content', 'search' );
						endif;

					endwhile;
				endif;
				?>

				<?php get_template_part( 'template-parts/pagination' ); ?>

			<?php else : ?>

				<?php get_template_part( 'template-parts/content', 'none' ); ?>

			<?php endif; ?>

		</main><!-- #main -->

		<?php get_sidebar(); ?>
	</div><!-- .row -->
</div><!-- .container-fluid -->

<?php
get_footer();
