<?php
/**
 * Preload Critical Assets
 * 
 * Sistema para precargar recursos críticos (fuentes, CSS, JS)
 * Solo se activa cuando la opción está habilitada en ACF
 * 
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DNS Prefetch y Preconnect para recursos externos
 * Mejora la velocidad de conexión a CDNs y servicios externos
 * Siempre activo (no requiere toggle)
 */
function bootstrap_theme_dns_prefetch_preconnect() {
	$resources = array();
	
	// Google Fonts (siempre útil si se usan fuentes personalizadas)
	$body_font = function_exists('bootstrap_theme_get_customization_option') 
		? bootstrap_theme_get_customization_option('google_fonts_body') 
		: '';
	$heading_font = function_exists('bootstrap_theme_get_customization_option') 
		? bootstrap_theme_get_customization_option('google_fonts_headings') 
		: '';
	
	if ( $body_font || $heading_font ) {
		$resources[] = array(
			'href' => 'https://fonts.googleapis.com',
			'crossorigin' => false,
		);
		$resources[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin' => true,
		);
	}
	
	// Detectar si se usará Fancybox (mismo criterio que en functions.php)
	$should_load_fancybox = has_block( 'gallery' ) ||
	                        has_block( 'core/gallery' ) ||
	                        ( function_exists( 'is_singular' ) && is_singular( 'product' ) ) ||
	                        ( function_exists( 'is_shop' ) && is_shop() ) ||
	                        ( function_exists( 'is_product_category' ) && is_product_category() );

	// jsDelivr CDN: necesario para FontAwesome o Fancybox
	if ( ( function_exists('bootstrap_theme_needs_fontawesome') && bootstrap_theme_needs_fontawesome() ) || $should_load_fancybox ) {
		$resources[] = array(
			'href' => 'https://cdn.jsdelivr.net',
			'crossorigin' => false,
		);
	}
	
	// Animate.css + WOW.js CDN: preconnect siempre (bajo costo y frecuente)
	$resources[] = array(
		'href' => 'https://cdnjs.cloudflare.com',
		'crossorigin' => false,
	);
	
	// WooCommerce: Google Analytics, payment gateways, etc (si WC está activo)
	if ( class_exists('WooCommerce') ) {
		// PayPal (común en WooCommerce)
		$resources[] = array(
			'href' => 'https://www.paypal.com',
			'crossorigin' => false,
		);
		$resources[] = array(
			'href' => 'https://www.paypalobjects.com',
			'crossorigin' => false,
		);
	}
	
	// Output: Preconnect (navegadores modernos) + DNS Prefetch (fallback legacy)
	foreach ( $resources as $resource ) {
		$crossorigin = $resource['crossorigin'] ? ' crossorigin' : '';
		
		// Preconnect para navegadores modernos (establece conexión completa)
		echo '<link rel="preconnect" href="' . esc_url( $resource['href'] ) . '"' . $crossorigin . '>' . "\n";
		
		// DNS Prefetch como fallback para navegadores antiguos (solo resuelve DNS)
		echo '<link rel="dns-prefetch" href="' . esc_url( $resource['href'] ) . '">' . "\n";
	}
	
	// Early hints para recursos críticos (HTTP/2 Push)
	if ( function_exists( 'header' ) && ! headers_sent() ) {
		foreach ( $resources as $resource ) {
			@header( 'Link: <' . $resource['href'] . '>; rel=preconnect', false );
		}
	}
}
add_action( 'wp_head', 'bootstrap_theme_dns_prefetch_preconnect', 0 ); // Prioridad 0: lo más temprano posible

/**
 * Precargar fuentes de Google Fonts si está habilitado
 */
function bootstrap_theme_preload_google_fonts() {
	// Verificar si está habilitado
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'preload_fonts' ) ) {
		return;
	}
	
	// Obtener fuentes configuradas
	$body_font = $cache_manager->get_option( 'google_fonts_body', '', 'customization' );
	$heading_font = $cache_manager->get_option( 'google_fonts_headings', '', 'customization' );
	
	// Array de fuentes a precargar
	$fonts_to_preload = array();
	
	if ( $body_font && $body_font !== '' ) {
		$fonts_to_preload[] = $body_font;
	}
	
	if ( $heading_font && $heading_font !== '' && $heading_font !== $body_font ) {
		$fonts_to_preload[] = $heading_font;
	}
	
	if ( empty( $fonts_to_preload ) ) {
		return;
	}
	
	// Generar URL de Google Fonts
	$fonts_url = bootstrap_theme_generate_simple_google_fonts_url();
	
	if ( $fonts_url ) {
		// Reemplazado por resource hints (ver más abajo)
	}
}
// add_action eliminado: se usan resource hints en su lugar

/**
 * Precargar Bootstrap JS (crítico para interactividad)
 * Solo preload, sin preconnect (el archivo es local)
 */
function bootstrap_theme_preload_bootstrap_js() {
	// Solo preload si no es admin y el JS se va a usar
	if ( is_admin() ) {
		return;
	}
	
	$bootstrap_js_url = get_template_directory_uri() . '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js';
	
	// Reemplazado por resource hints (ver más abajo)
}
// add_action eliminado: se usan resource hints en su lugar

/**
 * Font Awesome: usar Resource Hints en lugar de imprimir <link preload>
 * Agrega prefetch condicional del CSS cuando FontAwesome se necesite.
 */
function bootstrap_theme_fa_resource_hints( $urls, $relation_type ) {
	// Usar solo para prefetch; preconnect a jsdelivr ya se agrega más arriba
	if ( 'prefetch' !== $relation_type ) {
		return $urls;
	}

	// Respetar el toggle de performance si existe y cargar solo cuando haga falta
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'preload_fonts' ) ) {
		return $urls;
	}

	if ( function_exists( 'bootstrap_theme_needs_fontawesome' ) && bootstrap_theme_needs_fontawesome() ) {
		$urls[] = 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css';
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'bootstrap_theme_fa_resource_hints', 10, 2 );

/**
 * Fancybox: agregar resource hints (prefetch) del CSS y JS cuando se usará.
 */
function bootstrap_theme_fancybox_resource_hints( $urls, $relation_type ) {
	if ( 'prefetch' !== $relation_type ) return $urls;

	// Detectar si se usará Fancybox (coincidir con lógica de enqueue)
	$should_load_fancybox = has_block( 'gallery' ) ||
							has_block( 'core/gallery' ) ||
							( function_exists( 'is_singular' ) && is_singular( 'product' ) ) ||
							( function_exists( 'is_shop' ) && is_shop() ) ||
							( function_exists( 'is_product_category' ) && is_product_category() );

	if ( $should_load_fancybox ) {
		$urls[] = 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0.30/dist/fancybox/fancybox.css';
		$urls[] = 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0.30/dist/fancybox/fancybox.umd.js';
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'bootstrap_theme_fancybox_resource_hints', 10, 2 );

/**
 * Diferir scripts no críticos para mejor performance
 */
function bootstrap_theme_defer_non_critical_scripts( $tag, $handle ) {
	// Lista de scripts que pueden diferirse
	$defer_scripts = array(
		'wowjs',
		'fancybox',
		'fancybox-init',
		'bootstrap-theme-script',
	);
	
	// Solo si la compresión está habilitada
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'compression' ) ) {
		return $tag;
	}
	
	if ( in_array( $handle, $defer_scripts, true ) ) {
		return str_replace( ' src', ' defer src', $tag );
	}
	
	return $tag;
}
add_filter( 'script_loader_tag', 'bootstrap_theme_defer_non_critical_scripts', 10, 2 );

/**
 * Agregar atributo display=swap a Google Fonts para mejor performance
 */
function bootstrap_theme_optimize_google_fonts_url( $fonts_url ) {
	if ( $fonts_url && strpos( $fonts_url, 'fonts.googleapis.com' ) !== false ) {
		// Agregar display=swap si no está presente
		if ( strpos( $fonts_url, 'display=' ) === false ) {
			$fonts_url .= '&display=swap';
		}
	}
	return $fonts_url;
}
add_filter( 'bootstrap_theme_google_fonts_url', 'bootstrap_theme_optimize_google_fonts_url' );/**
 * Google Fonts: usar resource hints (prefetch) en lugar de imprimir <link preload>.
 */
function bootstrap_theme_google_fonts_resource_hints( $urls, $relation_type ) {
	if ( 'prefetch' !== $relation_type ) return $urls;

	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	if ( ! $cache_manager->is_performance_enabled( 'preload_fonts' ) ) return $urls;

	$fonts_url = bootstrap_theme_generate_simple_google_fonts_url();
	if ( $fonts_url ) {
		$urls[] = $fonts_url;
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'bootstrap_theme_google_fonts_resource_hints', 10, 2 );

/**
 * Bootstrap JS local: usar resource hints (prefetch) en lugar de imprimir <link preload>.
 */
function bootstrap_theme_bootstrap_js_resource_hints( $urls, $relation_type ) {
	if ( 'prefetch' !== $relation_type ) return $urls;
	if ( is_admin() ) return $urls;

	// Solo añadir si el script está encolado en esta vista
	if ( function_exists( 'wp_script_is' ) && wp_script_is( 'bootstrap', 'enqueued' ) ) {
		$bootstrap_js_url = get_template_directory_uri() . '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js';
		$urls[] = $bootstrap_js_url;
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'bootstrap_theme_bootstrap_js_resource_hints', 10, 2 );

/**
 * Animations (Animate.css + WOW.js): agregar resource hints (prefetch) cuando se usarán.
 * Detecta uso de clases 'wow' o 'animate__' en el contenido, bloques del tema o páginas WC relevantes.
 */
function bootstrap_theme_animations_resource_hints( $urls, $relation_type ) {
	if ( 'prefetch' !== $relation_type ) return $urls;

	// Heurística similar a functions.php
	$has_wow_in_content = false;
	if ( function_exists( 'is_singular' ) && is_singular() ) {
		$post_id = get_the_ID();
		if ( $post_id ) {
			$content = (string) get_post_field( 'post_content', $post_id );
			if ( strpos( $content, 'wow' ) !== false || strpos( $content, 'animate__' ) !== false ) {
				$has_wow_in_content = true;
			}
		}
	}

	$should_load_animations = (
		$has_wow_in_content ||
		has_block( 'bootstrap-theme/' ) ||
		( function_exists( 'is_shop' ) && is_shop() ) ||
		( function_exists( 'is_product' ) && is_product() ) ||
		( function_exists( 'is_product_category' ) && is_product_category() ) ||
		( function_exists( 'is_product_tag' ) && is_product_tag() )
	);

	if ( $should_load_animations ) {
		// Hints para los assets de animación desde cdnjs
		$urls[] = 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css';
		$urls[] = 'https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js';
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'bootstrap_theme_animations_resource_hints', 10, 2 );
