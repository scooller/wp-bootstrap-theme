<?php
/**
 * Critical CSS Optimization
 * 
 * Carga CSS crítico inline y difiere CSS no crítico para mejorar LCP
 * 
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Agregar media="print" a CSS no críticos y cargarlos con JS
 * Mejora render-blocking resources significativamente
 */
function bootstrap_theme_defer_non_critical_css( $html, $handle ) {
	// Solo aplicar si la compresión de CSS está habilitada
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'css_compression' ) ) {
		return $html;
	}

	// Lista de CSS que pueden diferirse (no críticos para LCP)
	$defer_styles = array(
		'animate-css',
		'fancybox',
		'woocommerce-layout',
		'woocommerce-smallscreen',
		'woocommerce-general',
	);

	if ( in_array( $handle, $defer_styles, true ) ) {
		// Cambiar media a print temporalmente y agregar onload para cambiarlo a all
		$html = str_replace( "media='all'", "media='print' onload=\"this.media='all'\"", $html );
		$html = str_replace( 'media="all"', 'media="print" onload="this.media=\'all\'"', $html );
		// Agregar noscript fallback
		$html .= '<noscript>' . str_replace( 'media="print" onload="this.media=\'all\'"', 'media="all"', $html ) . '</noscript>';
	}

	return $html;
}
add_filter( 'style_loader_tag', 'bootstrap_theme_defer_non_critical_css', 10, 2 );

/**
 * Preload de fuentes WOFF2 críticas para LCP
 */
function bootstrap_theme_preload_critical_fonts() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'preload_fonts' ) ) {
		return;
	}

	// Preload solo la fuente principal en formato woff2 (más ligero)
	$body_font = $cache_manager->get_option( 'google_fonts_body', '', 'customization' );
	
	if ( $body_font ) {
		// Nota: Google Fonts CSS2 ya maneja la precarga óptima con display=swap
		// Solo agregamos un hint extra para el archivo woff2
		?>
		<link rel="preload" as="style" href="<?php echo esc_url( bootstrap_theme_generate_simple_google_fonts_url() ); ?>" />
		<?php
	}
}
add_action( 'wp_head', 'bootstrap_theme_preload_critical_fonts', 1 );

/**
 * Agregar resource hints para imágenes críticas (LCP)
 */
function bootstrap_theme_preload_lcp_images() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'preload_fonts' ) ) {
		return;
	}

	// Preload del logo si existe (común en LCP)
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
		if ( $logo_url ) {
			?>
			<link rel="preload" as="image" href="<?php echo esc_url( $logo_url ); ?>" />
			<?php
		}
	}
}
add_action( 'wp_head', 'bootstrap_theme_preload_lcp_images', 2 );

/**
 * Optimizar carga de WooCommerce CSS
 * WooCommerce por defecto carga 3 CSS que bloquean el render
 */
function bootstrap_theme_optimize_woocommerce_css() {
	// Solo en páginas WooCommerce
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'css_compression' ) ) {
		return;
	}

	// Diferir CSS de WooCommerce en páginas donde no es crítico
	if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
		// En páginas no-WC, todos los CSS de WC pueden diferirse
		add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
	}
}
add_action( 'wp_enqueue_scripts', 'bootstrap_theme_optimize_woocommerce_css', 99 );

/**
 * Remover CSS/JS no utilizado de bloques de WordPress
 * Reducir ~117 KiB según PageSpeed
 */
function bootstrap_theme_remove_unused_block_assets() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'css_compression' ) ) {
		return;
	}

	// Solo cargar estilos de bloques que realmente se usan en la página
	if ( function_exists( 'wp_should_load_separate_core_block_assets' ) ) {
		add_filter( 'should_load_separate_core_block_assets', '__return_true' );
	}

	// Remover CSS de bloques globales si no se usan
	if ( ! has_blocks() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
	}

	// Remover CSS de bloques específicos no usados
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'bootstrap_theme_remove_unused_block_assets', 100 );

/**
 * Optimizar y reducir JS no usado
 * Reducir ~85 KiB según PageSpeed
 */
function bootstrap_theme_optimize_unused_js() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'js_compression' ) ) {
		return;
	}

	// Remover jQuery Migrate si no es necesario
	if ( ! is_admin() ) {
		wp_deregister_script( 'jquery-migrate' );
	}

	// Remover emoji scripts (no críticos)
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'bootstrap_theme_optimize_unused_js' );

/**
 * Deshabilitar estilos de embeds de WordPress
 */
function bootstrap_theme_disable_embeds() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'js_compression' ) ) {
		return;
	}

	// Remover scripts de oEmbed
	wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_footer', 'bootstrap_theme_disable_embeds' );

/**
 * Optimizar cadena crítica de solicitudes
 * Reducir profundidad del árbol de dependencias
 */
function bootstrap_theme_optimize_critical_chain() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'compression' ) ) {
		return;
	}

	// Remover query strings de assets estáticos para mejor caching
	add_filter( 'style_loader_src', 'bootstrap_theme_remove_script_version', 15, 1 );
	add_filter( 'script_loader_src', 'bootstrap_theme_remove_script_version', 15, 1 );
}
add_action( 'init', 'bootstrap_theme_optimize_critical_chain' );

/**
 * Remover versiones de CSS/JS para mejor caching
 */
function bootstrap_theme_remove_script_version( $src ) {
	if ( strpos( $src, 'ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}

/**
 * Agregar headers de cache agresivos
 */
function bootstrap_theme_add_cache_headers() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'compression' ) ) {
		return;
	}

	if ( ! is_admin() && ! is_user_logged_in() ) {
		// Cache público de 1 año para assets
		header( 'Cache-Control: public, max-age=31536000, immutable', false );
	}
}
add_action( 'send_headers', 'bootstrap_theme_add_cache_headers' );
