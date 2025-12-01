<?php
/**
 * Template part for displaying search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Bootstrap_Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('mb-4 pb-4 border-bottom'); ?>>
	<header class="entry-header mb-3">
		<h2 class="entry-title h4">
			<a href="<?php the_permalink(); ?>" class="text-decoration-none">
				<?php the_title(); ?>
			</a>
		</h2>

		<div class="entry-meta text-muted small">
			<i class="fas fa-calendar-alt me-1"></i>
			<time datetime="<?php echo get_the_date( 'c' ); ?>">
				<?php echo get_the_date(); ?>
			</time>

			<span class="mx-2">|</span>

			<i class="fas fa-user me-1"></i>
			<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="text-muted text-decoration-none">
				<?php the_author(); ?>
			</a>

			<?php if ( 'post' === get_post_type() ) : ?>
				<span class="mx-2">|</span>
				<i class="fas fa-folder me-1"></i>
				<?php the_category( ', ' ); ?>
			<?php endif; ?>
		</div>
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->

	<footer class="entry-footer">
		<a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm">
			<?php esc_html_e( 'Read More', 'bootstrap-theme' ); ?>
			<i class="fas fa-arrow-right ms-1"></i>
		</a>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
