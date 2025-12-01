<?php
/**
 * Lazy Loading de Imágenes
 * 
 * Sistema condicional de lazy loading basado en opciones de ACF
 * 
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Agregar atributo loading="lazy" a las imágenes del contenido
 * Solo si está habilitado en las opciones del tema
 * 
 * @param string $content El contenido HTML
 * @return string Contenido modificado
 */
function bootstrap_theme_add_lazy_loading_to_content( $content ) {
	// Verificar si lazy loading está habilitado
	if ( ! bootstrap_theme_is_lazy_loading_enabled() ) {
		return $content;
	}
	
	// Si no hay imágenes, retornar sin procesar
	if ( false === strpos( $content, '<img' ) ) {
		return $content;
	}
	
	// Agregar loading="lazy" y decoding="async" a imágenes que no lo tengan
	$content = preg_replace_callback(
		'/<img([^>]+?)\/?>/',
		function( $matches ) {
			$img_tag = $matches[0];
			$attributes = $matches[1];
			
			// Si ya tiene loading="eager" o loading="lazy", no modificar
			if ( preg_match( '/loading=(["\'])(eager|lazy)\1/', $attributes ) ) {
				return $img_tag;
			}
			
			// Agregar loading="lazy"
			$attributes .= ' loading="lazy"';
			
			// Agregar decoding="async" si no existe
			if ( ! preg_match( '/decoding=/', $attributes ) ) {
				$attributes .= ' decoding="async"';
			}
			
			return '<img' . $attributes . '/>';
		},
		$content
	);
	
	return $content;
}

/**
 * Filtro para agregar lazy loading al contenido principal
 */
add_filter( 'the_content', 'bootstrap_theme_add_lazy_loading_to_content', 999 );

/**
 * Filtro para agregar lazy loading a excerpts
 */
add_filter( 'the_excerpt', 'bootstrap_theme_add_lazy_loading_to_content', 999 );

/**
 * Filtro para agregar lazy loading a widgets de texto
 */
add_filter( 'widget_text', 'bootstrap_theme_add_lazy_loading_to_content', 999 );

/**
 * Modificar atributos de imágenes en WP_Query (thumbnails, etc)
 * 
 * @param array $attr Atributos de la imagen
 * @return array Atributos modificados
 */
function bootstrap_theme_add_lazy_loading_attributes( $attr ) {
	// Verificar si lazy loading está habilitado
	if ( ! bootstrap_theme_is_lazy_loading_enabled() ) {
		return $attr;
	}
	
	// Si ya tiene loading definido, no modificar
	if ( isset( $attr['loading'] ) ) {
		return $attr;
	}
	
	// Agregar loading="lazy" y decoding="async"
	$attr['loading'] = 'lazy';
	$attr['decoding'] = 'async';
	
	return $attr;
}

/**
 * Aplicar lazy loading a thumbnails de posts
 */
add_filter( 'wp_get_attachment_image_attributes', 'bootstrap_theme_add_lazy_loading_attributes', 10, 1 );

/**
 * Modificar atributo loading predeterminado de WordPress
 * WordPress 5.5+ agrega loading="lazy" por defecto, este filtro respeta la configuración del tema
 * 
 * @param string|bool $value El valor del atributo loading
 * @param string $image Etiqueta HTML de la imagen
 * @param string $context Contexto donde se usa la imagen
 * @return string|bool Valor modificado del atributo
 */
function bootstrap_theme_override_wp_lazy_loading( $value, $image, $context ) {
	// Si lazy loading está deshabilitado en el tema, deshabilitar el nativo de WP
	if ( ! bootstrap_theme_is_lazy_loading_enabled() ) {
		return false;
	}
	
	// Si está habilitado, permitir el comportamiento predeterminado de WP
	return $value;
}

/**
 * Controlar el lazy loading nativo de WordPress según la configuración del tema
 */
add_filter( 'wp_lazy_loading_enabled', 'bootstrap_theme_override_wp_lazy_loading', 10, 3 );

/**
 * Excluir imágenes above-the-fold del lazy loading
 * Las primeras imágenes no deben tener lazy loading para mejor LCP
 * 
 * @param string $content El contenido HTML
 * @return string Contenido modificado
 */
function bootstrap_theme_exclude_above_fold_images( $content ) {
	// Solo procesar si lazy loading está habilitado
	if ( ! bootstrap_theme_is_lazy_loading_enabled() ) {
		return $content;
	}
	
	// En páginas individuales, excluir la primera imagen del contenido
	if ( is_singular() ) {
		static $first_image_processed = false;
		
		if ( ! $first_image_processed && preg_match( '/<img([^>]+?)\/?>/', $content, $matches, PREG_OFFSET_CAPTURE ) ) {
			$img_tag = $matches[0][0];
			$offset = $matches[0][1];
			
			// Si la imagen tiene loading="lazy", cambiarlo a loading="eager"
			if ( strpos( $img_tag, 'loading="lazy"' ) !== false ) {
				$new_img_tag = str_replace( 'loading="lazy"', 'loading="eager"', $img_tag );
				$content = substr_replace( $content, $new_img_tag, $offset, strlen( $img_tag ) );
			}
			
			$first_image_processed = true;
		}
	}
	
	return $content;
}

/**
 * Aplicar exclusión de above-the-fold después del lazy loading
 */
add_filter( 'the_content', 'bootstrap_theme_exclude_above_fold_images', 1000 );
