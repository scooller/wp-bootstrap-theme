<?php
/**
 * The main template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Bootstrap_Theme
 */

get_header();

// Verificar si se debe mostrar el sidebar
$show_sidebar = bootstrap_theme_get_option('show_sidebar');
$main_class = $show_sidebar ? 'col-lg-8' : 'col-12';

$container_width = get_field('container_width') ?: bootstrap_theme_get_option('container_width');

// Determinar clase de contenedor
$container_class = 'container';
if ($container_width === 'fluid') {
	$container_class = 'container-fluid';
} elseif ($container_width && $container_width !== 'default' && $container_class !== 'container') {
	$container_class = 'container-' . $container_width;
}
?>

<div class="<?php echo esc_attr( $container_class ); ?>">	
	<div class="row">
		<?php
		// Display hero section on front page if enabled
		if ( is_front_page() ) {
			get_template_part( 'template-parts/hero' );
		}
		?>
		<main id="primary" class="site-main <?php echo esc_attr( $main_class ); ?>">

			<?php if ( have_posts() ) : ?>

				<?php if ( is_home() && ! is_front_page() ) : ?>
					<header class="page-header mb-4">
						<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
					</header>
				<?php endif; ?>

				<?php
				// Start the Loop.
				while ( have_posts() ) :
					the_post();

					/*
					 * Include the Post-Type-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content', get_post_type() );

				endwhile;

				// Previous/next page navigation.
				get_template_part( 'template-parts/pagination' );

			else :

				get_template_part( 'template-parts/content', 'none' );

			endif;
			?>

		</main><!-- #main -->

		<?php 
		// Mostrar sidebar solo si está habilitado
		if ( $show_sidebar ) {
			get_sidebar();
		}
		?>
	</div><!-- .row -->
</div><!-- .<?php echo esc_attr( $container_class ); ?> -->

<?php
get_footer();
