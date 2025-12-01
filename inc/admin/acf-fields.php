<?php
/**
 * ACF Fields Import from JSON
 * 
 * Script para importar campos ACF desde archivos JSON homologados
 * 
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Convertir color hexadecimal a RGB
 * Bootstrap 5.3 requiere valores RGB para algunas variables CSS
 */
function bootstrap_theme_hex_to_rgb( $hex ) {
	$hex = ltrim( $hex, '#' );
	
	if ( strlen( $hex ) === 3 ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );
	
	return "$r, $g, $b";
}

/**
 * Importar campos ACF desde archivos JSON
 */
function bootstrap_theme_import_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$json_path = get_template_directory() . '/inc/admin/acf-json/';
	
	// Array de archivos JSON a importar
	$json_files = array(
		'group_bootstrap_theme_general_options.json',
		'group_bootstrap_theme_customization.json',
		'group_bootstrap_theme_extras.json',
		'group_bootstrap_theme_page_options.json',
		'group_bootstrap_theme_emails.json',
		'group_bootstrap_theme_woocommerce.json'
	);

	foreach ( $json_files as $file ) {
		$file_path = $json_path . $file;
		if ( file_exists( $file_path ) ) {
			$json_content = file_get_contents( $file_path );
			$field_group = json_decode( $json_content, true );
			
			if ( $field_group && isset( $field_group['key'] ) ) {
				// Solo importar si no existe ya
				if ( ! acf_get_field_group( $field_group['key'] ) ) {
					acf_add_local_field_group( $field_group );
				}
			}
		}
	}
}
add_action( 'acf/init', 'bootstrap_theme_import_acf_fields' );

/**
 * Poblar dinámicamente las opciones de Google Fonts para cuerpo
 */
function bootstrap_theme_populate_body_fonts_choices( $field ) {
	// Solo aplicar a nuestro campo específico
	if ( $field['name'] !== 'google_fonts_body' ) {
		return $field;
	}
	
	// Obtener fuentes de Google Fonts
	$fonts = bootstrap_theme_get_google_fonts();
	
	if ( ! empty( $fonts ) ) {
		// Mantener la primera opción "-- Usar fuente del tema --"
		$first_choice = array( '' => '-- Usar fuente del tema --' );
		$field['choices'] = array_merge( $first_choice, $fonts );
	}
	
	return $field;
}
add_filter( 'acf/load_field/name=google_fonts_body', 'bootstrap_theme_populate_body_fonts_choices' );

/**
 * Poblar dinámicamente las opciones de Google Fonts para títulos
 */
function bootstrap_theme_populate_headings_fonts_choices( $field ) {
	// Solo aplicar a nuestro campo específico
	if ( $field['name'] !== 'google_fonts_headings' ) {
		return $field;
	}
	
	// Obtener fuentes de Google Fonts
	$fonts = bootstrap_theme_get_google_fonts();
	
	if ( ! empty( $fonts ) ) {
		// Mantener las primeras opciones
		$first_choices = array( 
			'' => '-- Usar misma fuente del cuerpo --',
		);
		$field['choices'] = array_merge( $first_choices, $fonts );
	}
	
	return $field;
}
add_filter( 'acf/load_field/name=google_fonts_headings', 'bootstrap_theme_populate_headings_fonts_choices' );

/**
 * Permitir archivos WebP en el campo de Logo Personalizado (ACF)
 * Garantiza que, aunque el grupo exista en DB, el campo acepte .webp
 */
function bootstrap_theme_allow_webp_for_custom_logo( $field ) {
	if ( empty( $field ) || empty( $field['type'] ) || $field['type'] !== 'image' ) {
		return $field;
	}

	// Asegurar que el campo tenga la propiedad mime_types como string
	$mime = isset( $field['mime_types'] ) ? (string) $field['mime_types'] : '';
	$parts = array_filter( array_map( 'trim', explode( ',', $mime ) ) );
	if ( ! in_array( 'webp', $parts, true ) ) {
		$parts[] = 'webp';
	}
	$field['mime_types'] = implode( ',', $parts );
	return $field;
}
add_filter( 'acf/load_field/key=field_custom_logo', 'bootstrap_theme_allow_webp_for_custom_logo' );

/**
 * Generar CSS personalizado basado en opciones de ACF
 */
function bootstrap_theme_generate_custom_css() {
	$css = '';
	
	// Sistema completo de colores Bootstrap 5.3
	$colors = array(
		'primary'   => array( 'default' => '#0d6efd', 'option' => 'primary_color' ),
		'secondary' => array( 'default' => '#6c757d', 'option' => 'secondary_color' ),
		'success'   => array( 'default' => '#198754', 'option' => 'success_color' ),
		'danger'    => array( 'default' => '#dc3545', 'option' => 'danger_color' ),
		'warning'   => array( 'default' => '#ffc107', 'option' => 'warning_color' ),
		'info'      => array( 'default' => '#0dcaf0', 'option' => 'info_color' ),
		'light'     => array( 'default' => '#f8f9fa', 'option' => 'light_color' ),
		'dark'      => array( 'default' => '#212529', 'option' => 'dark_color' ),
	);

	$css_vars = array();

	foreach ( $colors as $name => $color_data ) {
		$color_value = bootstrap_theme_get_customization_option( $color_data['option'] );
		
		if ( $color_value && $color_value !== $color_data['default'] ) {
			$rgb = bootstrap_theme_hex_to_rgb( $color_value );
			$css_vars[] = '--bs-' . $name . ': ' . esc_attr( $color_value );
			$css_vars[] = '--bs-' . $name . '-rgb: ' . $rgb;
		}
	}

	// Variables adicionales de Bootstrap
	$link_color = bootstrap_theme_get_customization_option('link_color');
	if ( $link_color && $link_color !== '#0d6efd' ) {
		$css_vars[] = '--bs-link-color: ' . esc_attr( $link_color );
		$css_vars[] = '--bs-link-color-rgb: ' . bootstrap_theme_hex_to_rgb( $link_color );
	}

	$border_color = bootstrap_theme_get_customization_option('border_color');
	if ( $border_color && $border_color !== '#dee2e6' ) {
		$css_vars[] = '--bs-border-color: ' . esc_attr( $border_color );
	}

	if ( ! empty( $css_vars ) ) {
		$css .= ':root { ' . implode( '; ', $css_vars ) . '; }' . "\n";
	}
	
	// Color scheme
	$color_scheme = bootstrap_theme_get_customization_option('color_scheme');
	if ( $color_scheme && $color_scheme !== 'auto' ) {
		$css .= '/* Color scheme: ' . esc_attr( $color_scheme ) . ' (manejado por Bootstrap data-bs-theme) */' . "\n";
	}
	
	// Fuentes Google Fonts
	$body_font = bootstrap_theme_get_customization_option('google_fonts_body');
	if ( $body_font ) {
		$css .= 'body { font-family: \'' . esc_attr( $body_font ) . '\', sans-serif; }' . "\n";
	}
	
	$heading_font = bootstrap_theme_get_customization_option('google_fonts_headings');
	if ( $heading_font ) {
		$css .= 'h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 { font-family: \'' . esc_attr( $heading_font ) . '\', serif; }' . "\n";
	}
	
	// Fuentes manuales
	$body_font_manual = bootstrap_theme_get_customization_option('body_font');
	if ( $body_font_manual && $body_font_manual !== '-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif' ) {
		$css .= 'body { font-family: ' . esc_attr( $body_font_manual ) . '; }' . "\n";
	}
	
	$heading_font_manual = bootstrap_theme_get_customization_option('heading_font');
	if ( $heading_font_manual && $heading_font_manual !== 'inherit' ) {
		$css .= 'h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 { font-family: ' . esc_attr( $heading_font_manual ) . '; }' . "\n";
	}
	
	// Tamaño base de fuente
	$base_font_size = bootstrap_theme_get_customization_option('base_font_size');
	if ( $base_font_size && $base_font_size != 16 ) {
		$css .= 'html { font-size: ' . intval( $base_font_size ) . 'px; }' . "\n";
	}
	
	// Header styles
	$header_style = bootstrap_theme_get_customization_option('header_style');
	
	// Estilos específicos de header según el tipo seleccionado
	switch ( $header_style ) {
		case 'simple':
			$css .= '/* Simple header styling already handled by template */' . "\n";
			break;
		case 'centered':
			$css .= '/* Centered header styling already handled by template */' . "\n";
			break;
		case 'with-buttons':
			$css .= '/* Header with buttons styling already handled by template */' . "\n";
			break;
		case 'dark':
			$css .= '/* Dark header styling already handled by template */' . "\n";
			break;
		case 'with-avatar':
			$css .= '/* Header with avatar styling already handled by template */' . "\n";
			break;
		case 'compact-dropdown':
			$css .= '/* Compact dropdown header styling already handled by template */' . "\n";
			break;
		case 'double':
			$css .= '/* Double header styling already handled by template */' . "\n";
			break;
		case 'iconized':
			$css .= '/* Iconized header styling already handled by template */' . "\n";
			break;
	}
	
	// Footer styles
	$footer_style = bootstrap_theme_get_customization_option('footer_style');
	
	// Estilos específicos de footer según el tipo seleccionado
	switch ( $footer_style ) {
		case 'footer-simple':
			$css .= 'footer { padding: 1rem 0; text-align: center; border-top: 1px solid #dee2e6; }' . "\n";
			break;
		case 'footer-with-columns':
			$css .= 'footer { padding: 3rem 0 1rem; } footer .footer-columns { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }' . "\n";
			break;
		case 'footer-with-icons':
			$css .= 'footer .social-icons { text-align: center; margin: 1rem 0; } footer .social-icons a { margin: 0 0.5rem; font-size: 1.5rem; }' . "\n";
			break;
		case 'footer-with-newsletter':
			$css .= 'footer .newsletter { background: rgba(255,255,255,0.1); padding: 2rem; border-radius: 0.5rem; margin-bottom: 2rem; }' . "\n";
			break;
		case 'footer-minimal':
			$css .= 'footer { padding: 0.5rem 0; font-size: 0.875rem; text-align: center; }' . "\n";
			break;
		case 'footer-dark':
			$css .= 'footer { background-color: #212529 !important; color: #ffffff !important; }' . "\n";
			break;
		case 'footer-sticky':
			$css .= 'html, body { height: 100%; } .site-wrapper { min-height: 100vh; display: flex; flex-direction: column; } .content { flex: 1; } footer { margin-top: auto; }' . "\n";
			break;
	}
	
	// Breadcrumb styles
	$breadcrumb_style = bootstrap_theme_get_customization_option('breadcrumb_style');
	
	switch ( $breadcrumb_style ) {
		case 'breadcrumb-slash':
			$css .= '.breadcrumb-item + .breadcrumb-item::before { content: "/"; }' . "\n";
			break;
		case 'breadcrumb-arrow':
			$css .= '.breadcrumb-item + .breadcrumb-item::before { content: ">"; }' . "\n";
			break;
		case 'breadcrumb-custom':
			$css .= '.breadcrumb-item + .breadcrumb-item::before { content: "→"; }' . "\n";
			break;
		case 'breadcrumb-card':
			$css .= '.breadcrumb { background: #ffffff; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.75rem 1rem; }' . "\n";
			break;
		case 'breadcrumb-bg-light':
			$css .= '.breadcrumb { background-color: #f8f9fa; }' . "\n";
			break;
		case 'breadcrumb-no-background':
			$css .= '.breadcrumb { background: none; padding: 0; }' . "\n";
			break;
	}
	
	return $css;
}

/**
 * Inyectar CSS personalizado en el head (con cache si está habilitado)
 */
function bootstrap_theme_inject_custom_css() {
	// Verificar si el cache está habilitado
	$cache_enabled = function_exists( 'bootstrap_theme_is_cache_enabled' ) 
		? bootstrap_theme_is_cache_enabled() 
		: true;
	
	// Verificar si la compresión está habilitada (para minificación)
	$compression_enabled = function_exists( 'bootstrap_theme_is_compression_enabled' ) 
		? bootstrap_theme_is_compression_enabled() 
		: false;
	
	$css = false;
	$cache_key = 'bootstrap_theme_custom_css';
	
	// Si la compresión está activa, usar clave diferente para cache minificado
	if ( $compression_enabled ) {
		$cache_key .= '_minified';
	}
	
	// Si el cache está habilitado, intentar obtener desde transient
	if ( $cache_enabled ) {
		$css = get_transient( $cache_key );
	}
	
	// Si no existe en cache o cache deshabilitado, generar
	if ( false === $css ) {
		$css = bootstrap_theme_generate_custom_css();
		
		// Minificar CSS si la compresión está habilitada
		if ( $compression_enabled ) {
			$css = bootstrap_theme_minify_css( $css );
		}
		
		// Guardar en transient solo si cache está habilitado
		if ( $cache_enabled ) {
			set_transient( $cache_key, $css, DAY_IN_SECONDS );
		}
	}
	
	if ( ! empty( $css ) ) {
		echo '<style type="text/css" id="bootstrap-theme-custom-css">' . "\n";
		echo $css;
		echo '</style>' . "\n";
	}
}

/**
 * Minificar CSS inline para reducir tamaño del HTML
 * Solo se aplica si "Habilitar Compresión" está activo
 */
function bootstrap_theme_minify_css( $css ) {
	// Eliminar comentarios CSS
	$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
	
	// Eliminar espacios en blanco innecesarios
	$css = preg_replace( '/\s+/', ' ', $css );
	
	// Eliminar espacios alrededor de caracteres especiales
	$css = preg_replace( '/\s*([{}:;,>+~])\s*/', '$1', $css );
	
	// Eliminar punto y coma antes de cerrar llave
	$css = str_replace( ';}', '}', $css );
	
	// Eliminar espacios al inicio y final
	$css = trim( $css );
	
	return $css;
}

/**
 * Agregar data-bs-theme al HTML usando Bootstrap 5.3 nativo
 */
function bootstrap_theme_add_color_scheme_attribute() {
	$color_scheme = bootstrap_theme_get_customization_option('color_scheme');
	
	if ( $color_scheme && $color_scheme !== 'auto' ) {
		// Para 'light' y 'dark', usar directamente
		echo ' data-bs-theme="' . esc_attr( $color_scheme ) . '"';
	} elseif ( $color_scheme === 'auto' ) {
		// Para 'auto', usar JavaScript para detectar preferencia del sistema
		echo ' data-bs-theme="auto"';
		add_action( 'wp_footer', 'bootstrap_theme_auto_color_scheme_script' );
	}
}

/**
 * Script para manejar el tema automático
 */
function bootstrap_theme_auto_color_scheme_script() {
	?>
	<script>
	// Bootstrap 5.3 color scheme auto-detection
	document.addEventListener('DOMContentLoaded', function() {
		const htmlElement = document.documentElement;
		const colorScheme = htmlElement.getAttribute('data-bs-theme');
		
		if (colorScheme === 'auto') {
			// Detectar preferencia del sistema
			const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
			htmlElement.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
			
			// Escuchar cambios en la preferencia del sistema
			window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
				htmlElement.setAttribute('data-bs-theme', e.matches ? 'dark' : 'light');
			});
		}
	});
	</script>
	<?php
}

// Hook CSS injection
add_action( 'wp_head', 'bootstrap_theme_inject_custom_css', 100 );