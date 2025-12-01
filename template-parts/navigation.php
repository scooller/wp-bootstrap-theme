<?php
/**
 * Navigation template part for primary menu
 *
 * @package Bootstrap_Theme
 */

// Verificar si se debe mostrar el menú
$show_navigation = bootstrap_theme_get_option('show_navigation_menu');

if ( ! $show_navigation ) {
	return;
}
?>

<div class="collapse navbar-collapse" id="navbarNav">
	<?php
	wp_nav_menu(
		array(
			'theme_location'  => 'primary',
			'menu_class'      => 'navbar-nav me-auto',
			'container'       => false,
			'fallback_cb'     => false,
			'depth'           => 2,
			'walker'          => new WP_Bootstrap_Navwalker(),
		)
	);
	?>
</div>
