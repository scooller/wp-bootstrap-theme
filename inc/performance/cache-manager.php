<?php
/**
 * Cache Manager for Theme Options
 * 
 * Sistema centralizado de cache para opciones ACF del tema
 * Reduce queries repetitivas y mejora performance
 * 
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clase para gestionar el cache de opciones del tema
 */
class Bootstrap_Theme_Cache_Manager {
	
	/**
	 * Grupo de cache para WP Object Cache
	 */
	const CACHE_GROUP = 'bootstrap_theme';
	
	/**
	 * TTL por defecto: 1 hora
	 */
	const CACHE_TTL = 3600;
	
	/**
	 * Singleton instance
	 */
	private static $instance = null;
	
	/**
	 * Cache en memoria para el request actual
	 */
	private $memory_cache = array();
	
	/**
	 * Get singleton instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {
		// Hook para invalidar cache cuando se guardan opciones ACF
		add_action( 'acf/save_post', array( $this, 'invalidate_on_save' ), 20 );
	}
	
	/**
	 * Obtener opción con cache
	 * 
	 * @param string $field_name Nombre del campo ACF
	 * @param mixed $default Valor por defecto
	 * @param string $prefix Prefijo del campo (general, customization, extras, woocommerce)
	 * @return mixed
	 */
	public function get_option( $field_name, $default = '', $prefix = '' ) {
		// Verificar si el cache está habilitado (sin usar cache para esta verificación)
		if ( ! $this->is_cache_enabled() ) {
			return $this->get_option_direct( $field_name, $default, $prefix );
		}
		
		// Construir la key completa
		$full_key = $prefix ? "{$prefix}_{$field_name}" : $field_name;
		$cache_key = "option_{$full_key}";
		
		// 1. Verificar memoria cache del request actual
		if ( isset( $this->memory_cache[ $cache_key ] ) ) {
			return $this->memory_cache[ $cache_key ];
		}
		
		// 2. Verificar WP Object Cache
		$cached = wp_cache_get( $cache_key, self::CACHE_GROUP );
		if ( false !== $cached ) {
			$this->memory_cache[ $cache_key ] = $cached;
			return $cached;
		}
		
		// 3. Obtener de ACF y guardar en cache
		$value = $this->get_option_direct( $field_name, $default, $prefix );
		
		// 4. Guardar en caches
		$this->memory_cache[ $cache_key ] = $value;
		wp_cache_set( $cache_key, $value, self::CACHE_GROUP, self::CACHE_TTL );
		
		return $value;
	}
	
	/**
	 * Obtener opción directamente sin cache
	 * 
	 * @param string $field_name Nombre del campo ACF
	 * @param mixed $default Valor por defecto
	 * @param string $prefix Prefijo del campo
	 * @return mixed
	 */
	private function get_option_direct( $field_name, $default = '', $prefix = '' ) {
		$full_key = $prefix ? "{$prefix}_{$field_name}" : $field_name;
		$value = $default;
		
		if ( function_exists( 'get_field' ) ) {
			// Intentar con prefijo primero
			$acf_value = get_field( $full_key, 'option' );
			if ( false === $acf_value || null === $acf_value || '' === $acf_value ) {
				// Fallback sin prefijo
				$acf_value = get_field( $field_name, 'option' );
			}
			
			if ( false !== $acf_value && null !== $acf_value && '' !== $acf_value ) {
				$value = $acf_value;
			}
		}
		
		return $value;
	}
	
	/**
	 * Verificar si el cache está habilitado
	 * 
	 * @return bool
	 */
	private function is_cache_enabled() {
		// Verificar directamente sin usar cache (para evitar recursión infinita)
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}
		
		$enabled = get_field( 'extras_enable_cache', 'option' );
		
		// Si no está configurado, activar por defecto
		if ( null === $enabled || false === $enabled ) {
			return true; // Default: habilitado
		}
		
		return (bool) $enabled;
	}
	
	/**
	 * Obtener todas las opciones de un grupo
	 * 
	 * @param string $prefix Prefijo del grupo (general, customization, extras, woocommerce)
	 * @return array
	 */
	public function get_group_options( $prefix = '' ) {
		$cache_key = "group_{$prefix}_all";
		
		// Verificar memoria cache
		if ( isset( $this->memory_cache[ $cache_key ] ) ) {
			return $this->memory_cache[ $cache_key ];
		}
		
		// Verificar WP Object Cache
		$cached = wp_cache_get( $cache_key, self::CACHE_GROUP );
		if ( false !== $cached ) {
			$this->memory_cache[ $cache_key ] = $cached;
			return $cached;
		}
		
		// Obtener de ACF
		$options = array();
		if ( function_exists( 'get_fields' ) ) {
			$all_fields = get_fields( 'option' );
			if ( $all_fields && is_array( $all_fields ) ) {
				// Filtrar solo las del prefijo
				if ( $prefix ) {
					foreach ( $all_fields as $key => $value ) {
						if ( strpos( $key, $prefix . '_' ) === 0 ) {
							$options[ $key ] = $value;
						}
					}
				} else {
					$options = $all_fields;
				}
			}
		}
		
		// Guardar en caches
		$this->memory_cache[ $cache_key ] = $options;
		wp_cache_set( $cache_key, $options, self::CACHE_GROUP, self::CACHE_TTL );
		
		return $options;
	}
	
	/**
	 * Invalidar cache al guardar opciones ACF
	 * 
	 * @param int $post_id
	 */
	public function invalidate_on_save( $post_id ) {
		// Solo procesar si es una página de opciones
		if ( $post_id !== 'options' && strpos( $post_id, 'option' ) === false ) {
			return;
		}
		
		// Limpiar todo el grupo de cache
		$this->flush_group_cache();
		
		// Limpiar transients de CSS personalizado (normal y minificado)
		delete_transient( 'bootstrap_theme_custom_css' );
		delete_transient( 'bootstrap_theme_custom_css_minified' );

		// Limpiar bundles generados (CSS/JS) en uploads/bootstrap-theme-cache
		$uploads = wp_get_upload_dir();
		if ( ! empty( $uploads['basedir'] ) ) {
			$cache_dir = trailingslashit( $uploads['basedir'] ) . 'bootstrap-theme-cache';
			if ( file_exists( $cache_dir ) && is_dir( $cache_dir ) ) {
				$files = glob( $cache_dir . DIRECTORY_SEPARATOR . 'bundle-*.*' );
				if ( is_array( $files ) ) {
					foreach ( $files as $f ) {
						@unlink( $f );
					}
				}
			}
		}
	}
	
	/**
	 * Limpiar todo el cache del grupo
	 */
	public function flush_group_cache() {
		// Limpiar memoria cache del request
		$this->memory_cache = array();
		
		// WP Object Cache no tiene método para limpiar grupo completo
		// Intentar limpiar los keys conocidos
		$common_keys = array(
			'option_products_per_row',
			'option_woocommerce_products_per_row',
			'option_enable_lazy_loading',
			'option_extras_enable_lazy_loading',
			'option_enable_preload_fonts',
			'option_extras_enable_preload_fonts',
			'option_extras_enable_css_compression',
			'option_extras_enable_js_compression',
			'group_general_all',
			'group_customization_all',
			'group_extras_all',
			'group_woocommerce_all',
		);
		
		foreach ( $common_keys as $key ) {
			wp_cache_delete( $key, self::CACHE_GROUP );
		}
	}
	
	/**
	 * Verificar si una opción de performance está habilitada
	 * 
	* Admite opciones: lazy_loading, preload_fonts, compression (OR de CSS/JS), css_compression, js_compression
	* - compression: OR de css_compression y js_compression (sin fallback a bandera antigua)
	 * - css_compression: usa extras_enable_css_compression
	 * - js_compression: usa extras_enable_js_compression
	 * 
	 * @param string $option_name Nombre corto de la opción
	 * @return bool
	 */
	public function is_performance_enabled( $option_name ) {
		// 'compression' ahora significa OR de CSS y JS (sin fallback al flag antiguo)
		if ( 'compression' === $option_name ) {
			$css = (bool) $this->get_option( 'enable_css_compression', 0, 'extras' );
			$js  = (bool) $this->get_option( 'enable_js_compression', 0, 'extras' );
			return ( $css || $js );
		}

		// Nuevos flags explícitos
		if ( 'css_compression' === $option_name ) {
			return (bool) $this->get_option( 'enable_css_compression', 0, 'extras' );
		}
		if ( 'js_compression' === $option_name ) {
			return (bool) $this->get_option( 'enable_js_compression', 0, 'extras' );
		}

		// Flags existentes (lazy_loading, preload_fonts)
		$value = $this->get_option( "enable_{$option_name}", 0, 'extras' );
		return (bool) $value;
	}
}

/**
 * Funciones helper para facilitar el uso
 */

/**
 * Obtener opción general con cache
 */
function bootstrap_theme_get_option_cached( $field_name, $default = '' ) {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	return $cache_manager->get_option( $field_name, $default, '' );
}

/**
 * Obtener opción de personalización con cache
 */
function bootstrap_theme_get_customization_option_cached( $field_name, $default = '' ) {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	return $cache_manager->get_option( $field_name, $default, 'customization' );
}

/**
 * Obtener opción extra con cache
 */
function bootstrap_theme_get_extra_option_cached( $field_name, $default = '' ) {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	return $cache_manager->get_option( $field_name, $default, 'extras' );
}

/**
 * Obtener opción WooCommerce con cache
 */
function bootstrap_theme_get_woocommerce_option_cached( $field_name, $default = '' ) {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	return $cache_manager->get_option( $field_name, $default, 'woocommerce' );
}

/**
 * Verificar si lazy loading está habilitado
 */
function bootstrap_theme_is_lazy_loading_enabled() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	return $cache_manager->is_performance_enabled( 'lazy_loading' );
}

/**
 * Verificar si preload de fuentes está habilitado
 */
function bootstrap_theme_is_preload_fonts_enabled() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	return $cache_manager->is_performance_enabled( 'preload_fonts' );
}

/**
 * Verificar si compresión está habilitada
 */
function bootstrap_theme_is_compression_enabled() {
	// Helper de conveniencia: OR de CSS y JS
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	return (
		$cache_manager->is_performance_enabled( 'css_compression' ) ||
		$cache_manager->is_performance_enabled( 'js_compression' )
	);
}

/**
 * Verificar si la compresión de CSS está habilitada
 */
function bootstrap_theme_is_css_compression_enabled() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	return $cache_manager->is_performance_enabled( 'css_compression' );
}

/**
 * Verificar si la compresión de JS está habilitada
 */
function bootstrap_theme_is_js_compression_enabled() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	return $cache_manager->is_performance_enabled( 'js_compression' );
}

/**
 * Verificar si el cache está habilitado
 */
function bootstrap_theme_is_cache_enabled() {
	if ( ! function_exists( 'get_field' ) ) {
		return false;
	}
	
	$enabled = get_field( 'extras_enable_cache', 'option' );
	
	// Si no está configurado, activar por defecto
	if ( null === $enabled || false === $enabled ) {
		return true; // Default: habilitado
	}
	
	return (bool) $enabled;
}
