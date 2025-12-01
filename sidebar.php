<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Bootstrap_Theme
 */

// Verificar primero la configuración ACF
$show_sidebar = bootstrap_theme_get_option('show_sidebar');

if ( ! $show_sidebar || ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area col-lg-4">
	<div class="sidebar-content">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</div>
</aside><!-- #secondary -->
