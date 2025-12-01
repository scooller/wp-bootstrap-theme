<?php
/**
 * FontAwesome Usage Detector
 * 
 * Detecta si la página actual necesita FontAwesome para evitar carga innecesaria
 *
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detecta si FontAwesome es necesario en la página actual
 *
 * @return bool True si se necesita FontAwesome, false si no
 */
function bootstrap_theme_needs_fontawesome() {
	// Cache por request para evitar múltiples verificaciones
	static $needs_fontawesome = null;
	
	if ( $needs_fontawesome !== null ) {
		return $needs_fontawesome;
	}
	
	$needs_fontawesome = false;
	
	// 1. Verificar si hay menús con iconos FontAwesome
	if ( bootstrap_theme_menu_has_icons() ) {
		$needs_fontawesome = true;
		return $needs_fontawesome;
	}
	
	// 2. Verificar si hay widgets activos que usan iconos
	if ( bootstrap_theme_widgets_have_icons() ) {
		$needs_fontawesome = true;
		return $needs_fontawesome;
	}
	
	// 3. Verificar si el contenido actual tiene bloques que usan iconos
	if ( bootstrap_theme_content_has_icon_blocks() ) {
		$needs_fontawesome = true;
		return $needs_fontawesome;
	}
	
	// 4. WooCommerce usa FontAwesome en varias plantillas
	if ( class_exists( 'WooCommerce' ) ) {
		if ( 
			( function_exists( 'is_shop' ) && is_shop() ) ||
			( function_exists( 'is_product' ) && is_product() ) ||
			( function_exists( 'is_product_category' ) && is_product_category() ) ||
			( function_exists( 'is_product_tag' ) && is_product_tag() ) ||
			( function_exists( 'is_cart' ) && is_cart() ) ||
			( function_exists( 'is_checkout' ) && is_checkout() ) ||
			( function_exists( 'is_account_page' ) && is_account_page() )
		) {
			$needs_fontawesome = true;
			return $needs_fontawesome;
		}
	}
	
	// 5. Verificar si hay redes sociales configuradas (usan iconos FontAwesome)
	if ( bootstrap_theme_has_social_media() ) {
		$needs_fontawesome = true;
		return $needs_fontawesome;
	}
	
	return $needs_fontawesome;
}

/**
 * Verifica si hay menús con iconos FontAwesome
 *
 * @return bool
 */
function bootstrap_theme_menu_has_icons() {
	global $wpdb;
	
	// Buscar en postmeta si hay items de menú con iconos
	$has_icons = $wpdb->get_var(
		"SELECT COUNT(*) 
		FROM {$wpdb->postmeta} 
		WHERE meta_key = '_menu_item_fa_icon' 
		AND meta_value != '' 
		LIMIT 1"
	);
	
	return ( $has_icons > 0 );
}

/**
 * Verifica si hay widgets activos con contenido que use iconos
 *
 * @return bool
 */
function bootstrap_theme_widgets_have_icons() {
	// Obtener todas las sidebars activas
	$sidebars = array( 'sidebar-1', 'footer-1', 'footer-2', 'footer-3', 'footer-4' );
	
	foreach ( $sidebars as $sidebar_id ) {
		if ( is_active_sidebar( $sidebar_id ) ) {
			// Capturar output del sidebar
			ob_start();
			dynamic_sidebar( $sidebar_id );
			$sidebar_content = ob_get_clean();
			
			// Buscar clases de FontAwesome (fa-, fab, fas, far, fal, fad)
			if ( preg_match( '/class=["\'][^"\']*\b(fa-|fab\s|fas\s|far\s|fal\s|fad\s)/i', $sidebar_content ) ) {
				return true;
			}
		}
	}
	
	return false;
}

/**
 * Verifica si el contenido actual tiene bloques que usan iconos
 *
 * @return bool
 */
function bootstrap_theme_content_has_icon_blocks() {
	// Solo verificar en contenido singular
	if ( ! is_singular() ) {
		return false;
	}
	
	global $post;
	
	if ( ! $post || ! has_blocks( $post->post_content ) ) {
		return false;
	}
	
	$blocks = parse_blocks( $post->post_content );
	
	return bootstrap_theme_check_blocks_for_icons( $blocks );
}

/**
 * Verifica recursivamente si hay bloques con iconos
 *
 * @param array $blocks Array de bloques parseados
 * @return bool
 */
function bootstrap_theme_check_blocks_for_icons( $blocks ) {
	foreach ( $blocks as $block ) {
		// Verificar contenido HTML del bloque
		if ( ! empty( $block['innerHTML'] ) ) {
			if ( preg_match( '/class=["\'][^"\']*\b(fa-|fab\s|fas\s|far\s|fal\s|fad\s)/i', $block['innerHTML'] ) ) {
				return true;
			}
		}
		
		// Verificar bloques anidados (innerBlocks)
		if ( ! empty( $block['innerBlocks'] ) ) {
			if ( bootstrap_theme_check_blocks_for_icons( $block['innerBlocks'] ) ) {
				return true;
			}
		}
	}
	
	return false;
}

/**
 * Verifica si hay redes sociales configuradas en ACF
 *
 * @return bool
 */
function bootstrap_theme_has_social_media() {
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	
	$social_networks = array(
		'facebook_url',
		'twitter_url',
		'instagram_url',
		'linkedin_url',
		'youtube_url',
		'github_url'
	);
	
	foreach ( $social_networks as $network ) {
		$url = $cache_manager->get_option( $network, 'bootstrap_customization_options' );
		if ( ! empty( $url ) ) {
			return true;
		}
	}
	
	return false;
}
