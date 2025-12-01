<?php
/**
 * The header for our theme
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Bootstrap_Theme
 */

?>
<!doctype html>
<html <?php language_attributes(); bootstrap_theme_add_color_scheme_attribute(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div id="site-loader">
		<div class="spinner-border" role="status">
			<span class="visually-hidden"><?php esc_html_e( 'Loading...', 'bootstrap-theme' ); ?></span>
		</div>
	</div>
<?php wp_body_open(); ?>

<?php 
// Incluir iconos SVG de FontAwesome
get_template_part( 'template-parts/svg-icons' );

// Incluir off-canvas del carrito si WooCommerce está activo
if ( class_exists( 'WooCommerce' ) ) {
	get_template_part( 'template-parts/woocommerce/cart-offcanvas' );
}
?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'bootstrap-theme' ); ?></a>

	<!-- loader.js ahora se encola vía wp_enqueue_script para evitar cargas duplicadas -->
	<?php 
	// Obtener el estilo de header seleccionado (global o específico de página)
	$header_style = bootstrap_theme_get_page_option( 'header_style' );
	if ( ! $header_style ) {
		$header_style = 'simple'; // Valor por defecto
	}

	// Cargar el template part correcto basado en el header_style
	$header_file = 'template-parts/headers/' . $header_style;
	
	// Verificar si existe el archivo, sino cargar el simple por defecto
	$template_path = get_template_directory() . '/' . $header_file . '.php';
	if ( ! file_exists( $template_path ) ) {
		$header_file = 'template-parts/headers/simple';
	}
	
	// Incluir el contenedor si es necesario para ciertos headers
	$container_headers = array( 'simple', 'centered', 'with-buttons' );
	?>
	<div class="site-header">
	<?php
	if ( in_array( $header_style, $container_headers ) ) :
		?>
		<div class="site-header <?php echo esc_attr( bootstrap_theme_get_option('container_width') ); ?>">
			<?php get_template_part( $header_file ); ?>
		</div>
		<?php
	else :
		get_template_part( $header_file );
	endif;
	?>
	</div>
