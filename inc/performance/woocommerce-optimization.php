<?php
/**
 * WooCommerce Performance Optimization
 * 
 * Sistema de optimización de rendimiento para WooCommerce
 * Controlado por toggles en ACF Options > WooCommerce > Performance
 * 
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inicializar optimizaciones de WooCommerce
 */
function bootstrap_theme_init_wc_optimizations() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	// Deshabilitar scripts no utilizados
	bootstrap_theme_wc_disable_unused_scripts();
	
	// Optimizar cart fragments
	bootstrap_theme_wc_optimize_cart_fragments();
	
	// Cache de queries
	bootstrap_theme_wc_cache_queries();
	
	// Optimizar product queries
	bootstrap_theme_wc_optimize_product_queries();
	
	// Deshabilitar reviews si está configurado
	bootstrap_theme_wc_disable_reviews();
	
	// Limitar REST API
	bootstrap_theme_wc_limit_rest_api();
	
	// Lazy load de productos relacionados
	bootstrap_theme_wc_lazy_load_related();
}
add_action( 'init', 'bootstrap_theme_init_wc_optimizations', 20 );

/**
 * Deshabilitar scripts no utilizados de WooCommerce
 */
function bootstrap_theme_wc_disable_unused_scripts() {
	if ( ! function_exists( 'bootstrap_theme_get_woocommerce_option' ) ) {
		return;
	}

	$disabled_scripts = bootstrap_theme_get_woocommerce_option( 'wc_disable_scripts' );
	
	if ( ! is_array( $disabled_scripts ) || empty( $disabled_scripts ) ) {
		return;
	}

	add_action( 'wp_enqueue_scripts', function() use ( $disabled_scripts ) {
		// Select2
		if ( in_array( 'select2', $disabled_scripts, true ) ) {
			wp_dequeue_style( 'select2' );
			wp_deregister_style( 'select2' );
			wp_dequeue_script( 'select2' );
			wp_deregister_script( 'select2' );
			wp_dequeue_script( 'selectWoo' );
			wp_deregister_script( 'selectWoo' );
		}

		// prettyPhoto (legacy lightbox)
		if ( in_array( 'prettyphoto', $disabled_scripts, true ) ) {
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
			wp_deregister_style( 'woocommerce_prettyPhoto_css' );
			wp_dequeue_script( 'prettyPhoto' );
			wp_deregister_script( 'prettyPhoto' );
			wp_dequeue_script( 'prettyPhoto-init' );
			wp_deregister_script( 'prettyPhoto-init' );
		}

		// Zoom
		if ( in_array( 'zoom', $disabled_scripts, true ) ) {
			remove_theme_support( 'wc-product-gallery-zoom' );
			wp_dequeue_script( 'zoom' );
			wp_deregister_script( 'zoom' );
		}

		// PhotoSwipe
		if ( in_array( 'photoswipe', $disabled_scripts, true ) ) {
			remove_theme_support( 'wc-product-gallery-lightbox' );
			wp_dequeue_style( 'photoswipe' );
			wp_deregister_style( 'photoswipe' );
			wp_dequeue_style( 'photoswipe-default-skin' );
			wp_deregister_style( 'photoswipe-default-skin' );
			wp_dequeue_script( 'photoswipe' );
			wp_deregister_script( 'photoswipe' );
			wp_dequeue_script( 'photoswipe-ui-default' );
			wp_deregister_script( 'photoswipe-ui-default' );
		}

		// FlexSlider
		if ( in_array( 'flexslider', $disabled_scripts, true ) ) {
			remove_theme_support( 'wc-product-gallery-slider' );
			wp_dequeue_style( 'flexslider' );
			wp_deregister_style( 'flexslider' );
			wp_dequeue_script( 'flexslider' );
			wp_deregister_script( 'flexslider' );
		}
	}, 99 );
}

/**
 * Optimizar Cart Fragments (reducir requests AJAX)
 */
function bootstrap_theme_wc_optimize_cart_fragments() {
	if ( ! function_exists( 'bootstrap_theme_get_woocommerce_option' ) ) {
		return;
	}

	$optimize = (bool) bootstrap_theme_get_woocommerce_option( 'wc_optimize_cart_fragments' );
	
	if ( ! $optimize ) {
		return;
	}

	$allowed_pages = bootstrap_theme_get_woocommerce_option( 'wc_cart_fragments_pages' );
	if ( ! is_array( $allowed_pages ) ) {
		$allowed_pages = array( 'cart', 'checkout' );
	}

	add_action( 'wp_enqueue_scripts', function() use ( $allowed_pages ) {
		// Determinar si estamos en una página permitida
		$is_allowed = false;

		if ( in_array( 'shop', $allowed_pages, true ) && ( function_exists( 'is_shop' ) && is_shop() ) ) {
			$is_allowed = true;
		}
		if ( in_array( 'product', $allowed_pages, true ) && ( function_exists( 'is_product' ) && is_product() ) ) {
			$is_allowed = true;
		}
		if ( in_array( 'cart', $allowed_pages, true ) && ( function_exists( 'is_cart' ) && is_cart() ) ) {
			$is_allowed = true;
		}
		if ( in_array( 'checkout', $allowed_pages, true ) && ( function_exists( 'is_checkout' ) && is_checkout() ) ) {
			$is_allowed = true;
		}

		// Si no está permitido, deshabilitar cart fragments
		if ( ! $is_allowed ) {
			wp_dequeue_script( 'wc-cart-fragments' );
			wp_deregister_script( 'wc-cart-fragments' );
		}
	}, 99 );
}

/**
 * Cache de queries pesadas de WooCommerce
 */
function bootstrap_theme_wc_cache_queries() {
	if ( ! function_exists( 'bootstrap_theme_get_woocommerce_option' ) ) {
		return;
	}

	$enable_cache = (bool) bootstrap_theme_get_woocommerce_option( 'wc_cache_queries' );
	
	if ( ! $enable_cache ) {
		return;
	}

	$duration_hours = (int) bootstrap_theme_get_woocommerce_option( 'wc_cache_duration' );
	if ( $duration_hours < 1 ) {
		$duration_hours = 12;
	}
	$cache_duration = $duration_hours * HOUR_IN_SECONDS;

	// Cachear term counts de categorías
	add_filter( 'get_terms', function( $terms, $taxonomies, $args ) use ( $cache_duration ) {
		if ( ! in_array( 'product_cat', (array) $taxonomies, true ) && ! in_array( 'product_tag', (array) $taxonomies, true ) ) {
			return $terms;
		}

		$cache_key = 'wc_term_counts_' . md5( serialize( $args ) );
		$cached = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		set_transient( $cache_key, $terms, $cache_duration );
		return $terms;
	}, 10, 3 );

	// Cachear lookups de variaciones
	add_filter( 'woocommerce_get_children', function( $children, $product_id, $visible_only ) use ( $cache_duration ) {
		if ( ! $visible_only ) {
			$cache_key = 'wc_product_children_' . $product_id;
			$cached = get_transient( $cache_key );

			if ( false !== $cached ) {
				return $cached;
			}

			set_transient( $cache_key, $children, $cache_duration );
		}

		return $children;
	}, 10, 3 );

	// Cachear atributos de productos
	add_filter( 'woocommerce_product_get_attributes', function( $attributes, $product ) use ( $cache_duration ) {
		$cache_key = 'wc_product_attributes_' . $product->get_id();
		$cached = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		set_transient( $cache_key, $attributes, $cache_duration );
		return $attributes;
	}, 10, 2 );

	// Limpiar cache cuando se actualiza un producto
	add_action( 'woocommerce_update_product', function( $product_id ) {
		delete_transient( 'wc_product_children_' . $product_id );
		delete_transient( 'wc_product_attributes_' . $product_id );
		// Limpiar cache de terms
		wp_cache_delete( 'wc_term_counts', 'woocommerce' );
	} );

	// Limpiar cache cuando se actualiza una categoría
	add_action( 'edited_product_cat', function() {
		wp_cache_delete( 'wc_term_counts', 'woocommerce' );
	} );
	add_action( 'edited_product_tag', function() {
		wp_cache_delete( 'wc_term_counts', 'woocommerce' );
	} );
}

/**
 * Optimizar product queries
 */
function bootstrap_theme_wc_optimize_product_queries() {
	if ( ! function_exists( 'bootstrap_theme_get_woocommerce_option' ) ) {
		return;
	}

	$optimize = (bool) bootstrap_theme_get_woocommerce_option( 'wc_optimize_product_queries' );
	
	if ( ! $optimize ) {
		return;
	}

	// Limitar campos en queries cuando solo se necesitan IDs
	add_filter( 'woocommerce_get_catalog_ordering_args', function( $args ) {
		// Solo optimizar en listados, no en single
		if ( is_admin() || ( function_exists( 'is_product' ) && is_product() ) ) {
			return $args;
		}

		// Usar no_found_rows en widgets y shortcodes sin paginación
		if ( ! is_main_query() ) {
			$args['no_found_rows'] = true;
		}

		return $args;
	}, 10 );

	// Optimizar queries de productos relacionados
	add_filter( 'woocommerce_related_products_args', function( $args ) {
		$args['no_found_rows'] = true;
		$args['fields'] = 'ids';
		return $args;
	}, 10 );

	// Optimizar queries de upsells
	add_filter( 'woocommerce_upsell_display_args', function( $args ) {
		$args['no_found_rows'] = true;
		return $args;
	}, 10 );

	// Optimizar query de cross-sells
	add_filter( 'woocommerce_product_crosssell_ids', function( $crosssell_ids, $product_id, $limit ) {
		// Limitar a lo necesario
		if ( count( $crosssell_ids ) > $limit ) {
			$crosssell_ids = array_slice( $crosssell_ids, 0, $limit );
		}
		return $crosssell_ids;
	}, 10, 3 );
}

/**
 * Deshabilitar sistema de reviews
 */
function bootstrap_theme_wc_disable_reviews() {
	if ( ! function_exists( 'bootstrap_theme_get_woocommerce_option' ) ) {
		return;
	}

	$disable = (bool) bootstrap_theme_get_woocommerce_option( 'wc_disable_reviews' );
	
	if ( ! $disable ) {
		return;
	}

	// Deshabilitar reviews en productos
	add_filter( 'woocommerce_product_tabs', function( $tabs ) {
		unset( $tabs['reviews'] );
		return $tabs;
	}, 98 );

	// Remover soporte de comentarios en productos
	add_action( 'init', function() {
		remove_post_type_support( 'product', 'comments' );
	}, 100 );

	// Deshabilitar calificación de productos
	add_filter( 'woocommerce_product_review_comment_form_args', '__return_false' );
	
	// Remover metabox de comentarios del admin
	add_action( 'add_meta_boxes', function() {
		remove_meta_box( 'commentsdiv', 'product', 'normal' );
	}, 50 );
}

/**
 * Limitar REST API de WooCommerce
 */
function bootstrap_theme_wc_limit_rest_api() {
	if ( ! function_exists( 'bootstrap_theme_get_woocommerce_option' ) ) {
		return;
	}

	$limit = (bool) bootstrap_theme_get_woocommerce_option( 'wc_disable_rest_api' );
	
	if ( ! $limit ) {
		return;
	}

	// Deshabilitar endpoints innecesarios de REST API
	add_filter( 'woocommerce_rest_is_request_to_rest_api', function( $is_rest_api ) {
		// Solo permitir REST API para usuarios autenticados con permisos
		if ( $is_rest_api && ! current_user_can( 'manage_woocommerce' ) ) {
			return false;
		}
		return $is_rest_api;
	} );

	// Remover endpoints específicos si no se usan
	add_filter( 'rest_endpoints', function( $endpoints ) {
		// Lista de endpoints a remover (ajustar según necesidad)
		$remove = array(
			'/wc/v3/reports',
			'/wc/v3/reports/sales',
			'/wc/v3/reports/top_sellers',
			'/wc/v3/system_status',
		);

		foreach ( $remove as $endpoint ) {
			if ( isset( $endpoints[ $endpoint ] ) ) {
				unset( $endpoints[ $endpoint ] );
			}
		}

		return $endpoints;
	} );
}

	/**
	 * Lazy load de productos relacionados y upsells
	 */
	function bootstrap_theme_wc_lazy_load_related() {
		if ( ! function_exists( 'bootstrap_theme_get_woocommerce_option' ) ) {
			return;
		}

		$enable = (bool) bootstrap_theme_get_woocommerce_option( 'wc_lazy_load_related' );
		if ( ! $enable ) {
			return;
		}

		// Remover render inicial para diferir carga
		add_action( 'wp', function() {
			if ( function_exists( 'is_product' ) && is_product() ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			}
		} );

		// Agregar placeholders donde corresponden
		add_action( 'woocommerce_after_single_product_summary', function() {
			if ( ! function_exists( 'is_product' ) || ! is_product() ) {
				return;
			}
			echo '<div class="wc-lazy-related" data-type="upsells" data-product-id="' . esc_attr( get_the_ID() ) . '"></div>';
		}, 15 );

		add_action( 'woocommerce_after_single_product_summary', function() {
			if ( ! function_exists( 'is_product' ) || ! is_product() ) {
				return;
			}
			echo '<div class="wc-lazy-related" data-type="related" data-product-id="' . esc_attr( get_the_ID() ) . '"></div>';
		}, 20 );

		// Encolar script solo en single product
		add_action( 'wp_enqueue_scripts', function() {
			if ( ! function_exists( 'is_product' ) || ! is_product() ) {
				return;
			}

			wp_enqueue_script(
				'wc-lazy-related',
				get_template_directory_uri() . '/assets/js/wc-lazy-related.js',
				array(),
				wp_get_theme()->get( 'Version' ),
				true
			);

			wp_localize_script( 'wc-lazy-related', 'wcLazyRelated', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wc_lazy_related' ),
			) );
		} );

		// AJAX endpoints
		add_action( 'wp_ajax_load_related_products', 'bootstrap_theme_ajax_load_related' );
		add_action( 'wp_ajax_nopriv_load_related_products', 'bootstrap_theme_ajax_load_related' );
	}

	/**
	 * AJAX: Render diferido de relacionados/upsells
	 */
	function bootstrap_theme_ajax_load_related() {
		check_ajax_referer( 'wc_lazy_related', 'nonce' );

		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$type       = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( ! $product_id || ! in_array( $type, array( 'related', 'upsells' ), true ) ) {
			wp_send_json_error();
		}

		// Establecer global $product para que los templates funcionen correctamente
		global $product;
		$prev_product = $product;
		$product      = wc_get_product( $product_id );

		ob_start();
		if ( 'upsells' === $type ) {
			woocommerce_upsell_display();
		} else {
			woocommerce_output_related_products();
		}
		$html = ob_get_clean();

		// Restaurar producto previo
		$product = $prev_product;

		wp_send_json_success( array( 'html' => $html ) );
	}
