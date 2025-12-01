<?php
/**
 * Theme Options Admin Page
 * 
 * Configura las páginas de administración del tema usando ACF
 * 
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Include page options helpers
require_once get_template_directory() . '/inc/admin/page-options-helpers.php';

/**
 * Registrar páginas de opciones del tema
 */
function bootstrap_theme_add_options_pages() {
	// Verificar que ACF esté activo
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		add_action( 'admin_notices', 'bootstrap_theme_acf_admin_notice' );
		return;
	}

	// Página principal
	acf_add_options_page( array(
		'page_title' => __( 'Configuración del Tema', 'bootstrap-theme' ),
		'menu_title' => __( 'Configuración del Tema', 'bootstrap-theme' ),
		'menu_slug'  => 'bootstrap-theme-options',
		'capability' => 'manage_options',
		'icon_url'   => 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M102.8 57.3C108.2 51.9 116.6 51.1 123 55.3L241.9 134.5C250.8 140.4 256.1 150.4 256.1 161.1L256.1 210.7L346.9 301.5C380.2 286.5 420.8 292.6 448.1 320L574.2 446.1C592.9 464.8 592.9 495.2 574.2 514L514.1 574.1C495.4 592.8 465 592.8 446.2 574.1L320.1 448C292.7 420.6 286.6 380.1 301.6 346.8L210.8 256L161.2 256C150.5 256 140.5 250.7 134.6 241.8L55.4 122.9C51.2 116.6 52 108.1 57.4 102.7L102.8 57.3zM247.8 360.8C241.5 397.7 250.1 436.7 274 468L179.1 563C151 591.1 105.4 591.1 77.3 563C49.2 534.9 49.2 489.3 77.3 461.2L212.7 325.7L247.9 360.8zM416.1 64C436.2 64 455.5 67.7 473.2 74.5C483.2 78.3 485 91 477.5 98.6L420.8 155.3C417.8 158.3 416.1 162.4 416.1 166.6L416.1 208C416.1 216.8 423.3 224 432.1 224L473.5 224C477.7 224 481.8 222.3 484.8 219.3L541.5 162.6C549.1 155.1 561.8 156.9 565.6 166.9C572.4 184.6 576.1 203.9 576.1 224C576.1 267.2 558.9 306.3 531.1 335.1L482 286C448.9 253 403.5 240.3 360.9 247.6L304.1 190.8L304.1 161.1L303.9 156.1C303.1 143.7 299.5 131.8 293.4 121.2C322.8 86.2 366.8 64 416.1 63.9z"/></svg>' ),
		'position'   => 25,
		'redirect'   => true
	) );

	// Subpágina 1: Opciones
	acf_add_options_sub_page( array(
		'page_title'  => __( 'Opciones Generales', 'bootstrap-theme' ),
		'menu_title'  => __( 'Opciones', 'bootstrap-theme' ),
		'menu_slug'   => 'bootstrap-theme-options-general',
		'parent_slug' => 'bootstrap-theme-options',
		'capability'  => 'manage_options',
	) );

	// Subpágina 2: Personalización
	acf_add_options_sub_page( array(
		'page_title'  => __( 'Personalización', 'bootstrap-theme' ),
		'menu_title'  => __( 'Personalización', 'bootstrap-theme' ),
		'menu_slug'   => 'bootstrap-theme-options-customization',
		'parent_slug' => 'bootstrap-theme-options',
		'capability'  => 'manage_options',
	) );

	// Subpágina 3: Extras
	acf_add_options_sub_page( array(
		'page_title'  => __( 'Extras', 'bootstrap-theme' ),
		'menu_title'  => __( 'Extras', 'bootstrap-theme' ),
		'menu_slug'   => 'bootstrap-theme-options-extras',
		'parent_slug' => 'bootstrap-theme-options',
		'capability'  => 'manage_options',
	) );

	// Subpágina 4: Emails (Newsletter)
	acf_add_options_sub_page( array(
		'page_title'  => __( 'Emails', 'bootstrap-theme' ),
		'menu_title'  => __( 'Emails', 'bootstrap-theme' ),
		'menu_slug'   => 'bootstrap-theme-options-emails',
		'parent_slug' => 'bootstrap-theme-options',
		'capability'  => 'manage_options',
	) );

	// Subpágina 5: WooCommerce
	acf_add_options_sub_page( array(
		'page_title'  => __( 'WooCommerce', 'bootstrap-theme' ),
		'menu_title'  => __( 'WooCommerce', 'bootstrap-theme' ),
		'menu_slug'   => 'bootstrap-theme-options-woocommerce',
		'parent_slug' => 'bootstrap-theme-options',
		'capability'  => 'manage_options',
	) );

	// Mensaje si WooCommerce no está activo
	add_action('admin_notices', function() {
		$screen = get_current_screen();
		if ($screen && $screen->id === 'toplevel_page_bootstrap-theme-options-woocommerce') {
			if (!class_exists('WooCommerce')) {
				echo '<div class="notice notice-warning"><p>';
				esc_html_e('El plugin WooCommerce no está activo. Actívalo para configurar las opciones de la tienda.', 'bootstrap-theme');
				echo '</p></div>';
			}
		}
	});
}
add_action( 'acf/init', 'bootstrap_theme_add_options_pages', 5 );

/**
 * Agregar enlace a la documentación (config.md) en el admin para las páginas de opciones del tema
 */
add_action('admin_head', function() {
	$screen = get_current_screen();
	if (!$screen) return;

	// IDs de pantallas de opciones del tema
	$targets = array(
		'toplevel_page_bootstrap-theme-options',
		'bootstrap-theme_page_bootstrap-theme-options-general',
		'bootstrap-theme_page_bootstrap-theme-options-customization',
		'bootstrap-theme_page_bootstrap-theme-options-extras',
		'bootstrap-theme_page_bootstrap-theme-options-emails',
		'bootstrap-theme_page_bootstrap-theme-options-woocommerce',
	);

	// if (in_array($screen->id, $targets, true)) {
	// 	// Agregar pestaña de ayuda con enlace a config.md (repositorio local)
	// 	$screen->add_help_tab(array(
	// 		'id'      => 'bootstrap_theme_docs',
	// 		'title'   => __('Documentación', 'bootstrap-theme'),
	// 		'content' => '<p>' . sprintf(
	// 			/* translators: %s: relative path to config.md */
	// 			esc_html__('Consulta la guía de configuración del tema en %s. Asegúrate de tener acceso al archivo en el servidor.', 'bootstrap-theme'),
	// 			'<code>wp-content/themes/bootstrap-theme/config.md</code>'
	// 		) . '</p>'
	// 	));
	// }
});

/**
 * Registrar campos ACF desde JSON
 */
function bootstrap_theme_acf_json_load_point( $paths ) {
	// Añadir directorio para cargar JSON de ACF
	$paths[] = get_template_directory() . '/inc/admin/acf-json';
	return $paths;
}
add_filter( 'acf/settings/load_json', 'bootstrap_theme_acf_json_load_point' );

/**
 * Punto de guardado para campos ACF
 */
function bootstrap_theme_acf_json_save_point( $path ) {
	// Cambiar directorio de guardado para JSON de ACF
	$path = get_template_directory() . '/inc/admin/acf-json';
	return $path;
}
add_filter( 'acf/settings/save_json', 'bootstrap_theme_acf_json_save_point' );

/**
 * Funciones helper para obtener opciones del tema
 */

/**
 * Obtener opción general del tema
 */
function bootstrap_theme_get_option( $name ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $name, 'option' );		
		return $value !== false ? $value : '';
	}
	return '';
}

/**
 * Obtener opciones de personalización
 */
function bootstrap_theme_get_customization_option( $name ) {
	if ( function_exists( 'get_field' ) ) {
		$fields_to_try = array();

		// Build candidate list: prefer prefixed, then raw (or vice versa if already prefixed)
		if ( strpos( $name, 'customization_' ) === 0 ) {
			$fields_to_try[] = $name; // already prefixed
			$fields_to_try[] = substr( $name, strlen( 'customization_' ) ); // fallback to raw
		} else {
			$fields_to_try[] = 'customization_' . $name; // preferred prefixed
			$fields_to_try[] = $name; // fallback to raw
		}

		foreach ( $fields_to_try as $field_name ) {
			$value = get_field( $field_name, 'option' );
			if ( $value !== false && $value !== null && $value !== '' ) {
				return $value;
			}
		}
	}
	return '';
}

/**
 * Obtener opciones extras
 */
function bootstrap_theme_get_extra_option( $name ) {
	if ( function_exists( 'get_field' ) ) {
		$fields_to_try = array();

		if ( strpos( $name, 'extras_' ) === 0 ) {
			$fields_to_try[] = $name; // already prefixed
			$fields_to_try[] = substr( $name, strlen( 'extras_' ) ); // fallback to raw
		} else {
			$fields_to_try[] = 'extras_' . $name; // prefer prefixed
			$fields_to_try[] = $name; // fallback raw
		}

		foreach ( $fields_to_try as $field_name ) {
			$value = get_field( $field_name, 'option' );
			if ( $value !== false && $value !== null && $value !== '' ) {
				return $value;
			}
		}
	}
	return '';
}

/**
 * Obtener opciones WooCommerce
 */
function bootstrap_theme_get_woocommerce_option( $name ) {
	if ( function_exists( 'get_field' ) ) {
		$fields_to_try = array();

		if ( strpos( $name, 'woocommerce_' ) === 0 ) {
			$fields_to_try[] = $name; // ya con prefijo
			$fields_to_try[] = substr( $name, strlen( 'woocommerce_' ) ); // fallback sin prefijo
		} else {
			$fields_to_try[] = 'woocommerce_' . $name; // preferir con prefijo
			$fields_to_try[] = $name; // fallback sin prefijo
		}

		foreach ( $fields_to_try as $field_name ) {
			$value = get_field( $field_name, 'option' );
			if ( $value !== false && $value !== null && $value !== '' ) {
				return $value;
			}
		}
	}
	return '';
}

/**
 * Obtener opción booleana del tema (true_false de ACF) con coerción segura
 */
// Nota: Ya no se necesita get_option_bool; bootstrap_theme_get_option maneja booleans cuando el default es booleano.

/**
 * Verificar si ACF está instalado y activo
 */
function bootstrap_theme_check_acf() {
	if ( ! class_exists( 'ACF' ) ) {
		add_action( 'admin_notices', 'bootstrap_theme_acf_admin_notice' );
	}
}
add_action( 'admin_init', 'bootstrap_theme_check_acf' );

/**
 * Mostrar aviso si ACF no está instalado
 */
function bootstrap_theme_acf_admin_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<strong><?php esc_html_e( 'Bootstrap Theme', 'bootstrap-theme' ); ?>:</strong>
			<?php esc_html_e( 'Este tema requiere que el plugin Advanced Custom Fields esté instalado y activado para acceder a las opciones del tema.', 'bootstrap-theme' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Añadir estilos personalizados al admin
 */
function bootstrap_theme_admin_styles() {
	wp_enqueue_style( 
		'bootstrap-theme-admin', 
		get_template_directory_uri() . '/inc/admin/admin-styles.css', 
		array(), 
		BOOTSTRAP_THEME_VERSION 
	);
}
add_action( 'admin_enqueue_scripts', 'bootstrap_theme_admin_styles' );

/**
 * Añadir scripts personalizados al admin
 */
function bootstrap_theme_admin_scripts( $hook ) {
	// Solo cargar en las páginas de opciones de WooCommerce del tema
	if ( strpos( $hook, 'bootstrap-theme-options-woocommerce' ) !== false ) {
		wp_enqueue_script(
			'bootstrap-theme-woocommerce-admin',
			get_template_directory_uri() . '/inc/admin/woocommerce-admin.js',
			array( 'jquery', 'acf-input' ),
			BOOTSTRAP_THEME_VERSION,
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'bootstrap_theme_admin_scripts' );

/**
 * Enable ACF Customizer support
 */
function bootstrap_theme_enable_acf_customizer() {
	add_theme_support( 'customize-selective-refresh-widgets' );
	
	// Enable ACF for Customizer
	if ( function_exists( 'acf_add_options_page' ) ) {
		add_filter( 'acf/settings/show_admin', '__return_true' );
		add_filter( 'acf/settings/capability', function() {
			return 'edit_theme_options';
		});
	}
}
add_action( 'after_setup_theme', 'bootstrap_theme_enable_acf_customizer' );