<?php
/**
 * Bootstrap Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Check for ACF Pro dependency
function bootstrap_theme_check_acf_pro() {
	if ( ! class_exists( 'ACF' ) || ! function_exists( 'acf_get_setting' ) ) {
		add_action( 'admin_notices', 'bootstrap_theme_acf_missing_notice' );
		return false;
	}
	
	// Check if it's ACF Pro (has options page capability)
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		add_action( 'admin_notices', 'bootstrap_theme_acf_pro_missing_notice' );
		return false;
	}
	
	return true;
}

function bootstrap_theme_acf_missing_notice() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><strong><?php esc_html_e( 'Bootstrap Theme:', 'bootstrap-theme' ); ?></strong> <?php esc_html_e( 'Este tema requiere Advanced Custom Fields (ACF) para funcionar correctamente.', 'bootstrap-theme' ); ?> <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=advanced+custom+fields&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'Instalar ACF', 'bootstrap-theme' ); ?></a></p>
	</div>
	<?php
}

function bootstrap_theme_acf_pro_missing_notice() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><strong><?php esc_html_e( 'Bootstrap Theme:', 'bootstrap-theme' ); ?></strong> <?php esc_html_e( 'Este tema requiere Advanced Custom Fields PRO para funcionar correctamente. La versión gratuita no es suficiente.', 'bootstrap-theme' ); ?> <a href="https://www.advancedcustomfields.com/pro/" target="_blank"><?php esc_html_e( 'Conseguir ACF Pro', 'bootstrap-theme' ); ?></a></p>
	</div>
	<?php
}

// Run ACF check
bootstrap_theme_check_acf_pro();

// Bootstrap version in the theme from Composer
define( 'BOOTSTRAP_THEME_VERSION', '5.3.x' );
// Theme version for cache busting
define( 'BOOTSTRAP_THEME_BUILD_VERSION', '1.6.4' );

/**
 * Temporary ACF Sync Force (Remove after sync)
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function bootstrap_theme_setup() {
	// Make theme available for translation.
	load_theme_textdomain( 'bootstrap-theme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in multiple locations.
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary', 'bootstrap-theme' ),
			'footer'  => esc_html__( 'Footer', 'bootstrap-theme' ),
		)
	);

	// Switch default core markup to output valid HTML5.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for core custom logo.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	// Add support for responsive embedded content.
	add_theme_support( 'responsive-embeds' );

	// Add support for block editor wide alignment.
	add_theme_support( 'align-wide' );

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Enqueue editor styles inside the block editor iframe.
	// Include theme editor styles, our blocks editor styles, and Bootstrap CSS so grid/components preview correctly.
	add_editor_style( array(
		'assets/css/editor-style.css',
		'blocks/blocks-editor.css',
		'vendor/twbs/bootstrap/dist/css/bootstrap.min.css' // note: path is relative to theme root
	) );

	// Declare WooCommerce support
	add_theme_support( 'woocommerce' );
	
	// Declare support for WooCommerce features
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'bootstrap_theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 */
function bootstrap_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'bootstrap_theme_content_width', 1140 );
}
add_action( 'after_setup_theme', 'bootstrap_theme_content_width', 0 );

/**
 * Register widget area.
 */
function bootstrap_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'bootstrap-theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'bootstrap-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 1', 'bootstrap-theme' ),
			'id'            => 'footer-1',
			'description'   => esc_html__( 'Add widgets here for the first footer column.', 'bootstrap-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 2', 'bootstrap-theme' ),
			'id'            => 'footer-2',
			'description'   => esc_html__( 'Add widgets here for the second footer column.', 'bootstrap-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 3', 'bootstrap-theme' ),
			'id'            => 'footer-3',
			'description'   => esc_html__( 'Add widgets here for the third footer column.', 'bootstrap-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Footer 4', 'bootstrap-theme' ),
			'id'            => 'footer-4',
			'description'   => esc_html__( 'Add widgets here for the fourth footer column.', 'bootstrap-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'bootstrap_theme_widgets_init' );

// Include frontend components
require_once get_template_directory() . '/inc/frontend/color-scheme-switcher.php';

/**
 * Enqueue scripts and styles.
 */
function bootstrap_theme_scripts() {
	// Forzar jQuery desde CDN en frontend (solución 27/10/2025: scripts del tema no cargaban por conflicto de dependencias)
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'https://code.jquery.com/jquery-3.7.1.min.js', false, '3.7.1', true);
		wp_enqueue_script('jquery');
	}
	// Verificar opciones de performance (con cache)
	$cache_manager = Bootstrap_Theme_Cache_Manager::get_instance();
	
	// Enqueue main stylesheet
	wp_enqueue_style( 'bootstrap-theme-style', get_stylesheet_uri(), array(), BOOTSTRAP_THEME_VERSION );

	// Loader and icons styles (were previously in header inline)
	wp_enqueue_style(
		'bootstrap-theme-loader',
		get_template_directory_uri() . '/assets/css/loader.css',
		array(),
		BOOTSTRAP_THEME_VERSION
	);
	wp_enqueue_style(
		'bootstrap-theme-icons',
		get_template_directory_uri() . '/assets/css/icons.css',
		array(),
		BOOTSTRAP_THEME_VERSION
	);

	// Bootstrap CSS is bundled into our compiled theme stylesheet via Sass (Composer-installed Bootstrap)

	// FontAwesome - Solo cargar si es necesario (ahorra ~350KB)
	if ( bootstrap_theme_needs_fontawesome() ) {
		wp_enqueue_style( 
			'font-awesome', 
			'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css', 
			array(), 
			'6.5.2' 
		);
	}

	// Detectar uso probable de animaciones WOW/Animate independientemente del Lazy Loading
	$has_wow_in_content = false;
	if ( function_exists( 'is_singular' ) && is_singular() ) {
		$post_id = get_the_ID();
		if ( $post_id ) {
			$content = (string) get_post_field( 'post_content', $post_id );
			// Buscar clases típicas de WOW/Animate
			if ( strpos( $content, 'wow' ) !== false || strpos( $content, 'animate__' ) !== false ) {
				$has_wow_in_content = true;
			}
		}
	}

	// Cargar animaciones si:
	// - Hay clases WOW/Animate en el contenido
	// - O hay bloques del tema (que pueden usarlas)
	// - O algunas páginas de WooCommerce donde el tema aplica animaciones por defecto
	$should_load_animations = (
		$has_wow_in_content ||
		has_block( 'bootstrap-theme/' ) ||
		( function_exists( 'is_shop' ) && is_shop() ) ||
		( function_exists( 'is_product' ) && is_product() ) ||
		( function_exists( 'is_product_category' ) && is_product_category() ) ||
		( function_exists( 'is_product_tag' ) && is_product_tag() )
	);
	
	if ( $should_load_animations ) {
		// Animate.css (CDN)
		wp_enqueue_style(
			'animate-css',
			'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css',
			array(),
			'4.1.1'
		);
		
		// WOW.js (CDN) - Inicializado en loader.js
		wp_enqueue_script(
			'wowjs',
			'https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js',
			array(),
			'1.1.2',
			true
		);
	}

	// Enqueue custom styles
	wp_enqueue_style( 
		'bootstrap-theme-custom', 
		get_template_directory_uri() . '/assets/css/theme.css', 
		array(), 
		rand(), // Forzar recarga en cada carga para desarrollo; cambiar a BOOTSTRAP_THEME_VERSION para producción 
	);

	// Fancybox CSS/JS - Solo cargar donde hay galerías o productos con imágenes
	$should_load_fancybox = has_block( 'gallery' ) || 
	                        has_block( 'core/gallery' ) || 
	                        ( function_exists( 'is_singular' ) && is_singular( 'product' ) ) ||
	                        ( function_exists( 'is_shop' ) && is_shop() ) ||
	                        ( function_exists( 'is_product_category' ) && is_product_category() );
	
	if ( $should_load_fancybox ) {
		wp_enqueue_style(
			'fancybox',
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0.30/dist/fancybox/fancybox.css',
			array(),
			'5.0.30'
		);
		wp_enqueue_script(
			'fancybox',
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0.30/dist/fancybox/fancybox.umd.js',
			array('jquery'),
			'5.0.30',
			true
		);
		wp_enqueue_script(
			'fancybox-init',
			get_template_directory_uri() . '/assets/js/fancybox-init.js',
			array('fancybox'),
			BOOTSTRAP_THEME_VERSION,
			true
		);
	}

	// Enqueue Bootstrap JavaScript (local bundle from Composer install)
	wp_enqueue_script(
		'bootstrap',
		get_template_directory_uri() . '/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js',
		array(),
		'5.3.8',
		true
	);

	// Enqueue custom JavaScript
	wp_enqueue_script( 
		'bootstrap-theme-script', 
		get_template_directory_uri() . '/assets/js/theme.js', 
		array( 'jquery', 'bootstrap' ), 
		BOOTSTRAP_THEME_VERSION, 
		true 
	);
	
	// Loader script - Maneja el overlay y opcionalmente inicializa WOW si está presente
	wp_enqueue_script(
		'bootstrap-theme-loader',
		get_template_directory_uri() . '/assets/js/loader.js',
		array(), // No depender de WOW: el script detecta si existe
		BOOTSTRAP_THEME_VERSION,
		true
	);

	// Cart block update handler (only on checkout page)
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		wp_enqueue_script(
			'bootstrap-theme-cart-update',
			get_template_directory_uri() . '/blocks/bs-cart/cart-update-handler.js',
			array( 'jquery' ),
			BOOTSTRAP_THEME_VERSION,
			true
		);
	}

	// Enqueue comment reply script
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Localize script for AJAX
	wp_localize_script( 'bootstrap-theme-script', 'bootstrap_theme_ajax', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'bootstrap_theme_nonce' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'bootstrap_theme_scripts' );

// Enqueue admin JS for menu item preview
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'nav-menus.php') {
        // Enqueue FontAwesome for icon preview in admin
        wp_enqueue_style( 
            'font-awesome-admin', 
            'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css', 
            array(), 
            '6.5.2' 
        );
        
        wp_enqueue_script(
            'bootstrap-theme-fa-icons-list',
            get_template_directory_uri() . '/assets/js/fa-icons-list.js',
            array(),
            BOOTSTRAP_THEME_VERSION,
            true
        );
        wp_enqueue_script(
            'bootstrap-theme-fa-icons-categorized',
            get_template_directory_uri() . '/assets/js/fa-icons-categorized.js',
            array(),
            BOOTSTRAP_THEME_VERSION,
            true
        );
        wp_enqueue_script(
            'bootstrap-theme-menu-admin-preview',
            get_template_directory_uri() . '/assets/js/menu-admin-preview.js',
            array('jquery','bootstrap-theme-fa-icons-list','bootstrap-theme-fa-icons-categorized'),
            BOOTSTRAP_THEME_VERSION,
            true
        );
    }
});

// ACF JSON Save/Load configurado en inc/admin/theme-options.php para evitar duplicación

/**
 * Enable shortcodes in widgets - Simple approach
 */
function bootstrap_theme_enable_shortcodes_in_widgets() {
    add_filter('widget_text', 'do_shortcode');
    add_filter('widget_custom_html', 'do_shortcode');
    add_filter('widget_text_content', 'do_shortcode');
}
add_action('widgets_init', 'bootstrap_theme_enable_shortcodes_in_widgets');

/**
 * Display sidebar with shortcode support
 */
function bootstrap_theme_dynamic_sidebar_with_shortcodes($sidebar_id) {
    if (is_active_sidebar($sidebar_id)) {
        ob_start();
        dynamic_sidebar($sidebar_id);
        $widget_output = ob_get_clean();
        echo do_shortcode($widget_output);
    }
}

/**
 * Campos personalizados para ítems de menú: icono FontAwesome, botón y estilo Bootstrap
 */
add_filter('wp_nav_menu_item_custom_fields', function($item_id, $item, $depth, $args) {
    // Icono FontAwesome
    $icon = get_post_meta($item_id, '_menu_item_fa_icon', true);
    echo '<p class="field-fa-icon description description-wide">
        <label for="edit-menu-item-fa-icon-' . $item_id . '">' . esc_html__('Icono FontAwesome (ej: fa-home)', 'bootstrap-theme') . '<br />
        <input type="text" id="edit-menu-item-fa-icon-' . $item_id . '" class="widefat code edit-menu-item-fa-icon" style="display: inline-block; width: 80%;" name="menu-item-fa-icon[' . $item_id . ']" value="' . esc_attr($icon) . '" />
        </label>';
    echo '<br><a href="https://fontawesome.com/icons" target="_blank" rel="noopener" style="font-size:13px;">' . esc_html__('Ver todos los iconos FontAwesome', 'bootstrap-theme') . '</a>';
    if ($icon) {
        echo '<span style="display:inline-block;margin-left:10px;vertical-align:middle;">'
            . '<svg class="icon"><use xlink:href="#' . esc_attr($icon) . '"></use></svg></span>';
    }
    echo '</p>';

    // Mostrar como botón
    $is_button = get_post_meta($item_id, '_menu_item_is_button', true);
    echo '<p class="field-is-button description description-wide">
        <label for="edit-menu-item-is-button-' . $item_id . '">
        <input type="checkbox" id="edit-menu-item-is-button-' . $item_id . '" name="menu-item-is-button[' . $item_id . ']" value="1"' . checked($is_button, '1', false) . ' /> '
        . esc_html__('Mostrar como botón Bootstrap', 'bootstrap-theme') . '</label></p>';

    // Estilo de botón Bootstrap
    $button_style = get_post_meta($item_id, '_menu_item_button_style', true);
    $styles = array(
        'btn-primary' => 'Primario',
        'btn-secondary' => 'Secundario',
        'btn-success' => 'Éxito',
        'btn-danger' => 'Peligro',
        'btn-warning' => 'Advertencia',
        'btn-info' => 'Info',
        'btn-light' => 'Claro',
        'btn-dark' => 'Oscuro',
        'btn-outline-primary' => 'Outline Primario',
        'btn-outline-secondary' => 'Outline Secundario',
        'btn-outline-success' => 'Outline Éxito',
        'btn-outline-danger' => 'Outline Peligro',
        'btn-outline-warning' => 'Outline Advertencia',
        'btn-outline-info' => 'Outline Info',
        'btn-outline-light' => 'Outline Claro',
        'btn-outline-dark' => 'Outline Oscuro',
    );
    echo '<p class="field-button-style description description-wide">
        <label for="edit-menu-item-button-style-' . $item_id . '">' . esc_html__('Estilo de botón Bootstrap', 'bootstrap-theme') . '<br />
        <select id="edit-menu-item-button-style-' . $item_id . '" name="menu-item-button-style[' . $item_id . ']">';
    echo '<option value="">' . esc_html__('(Sin estilo)', 'bootstrap-theme') . '</option>';
    foreach ($styles as $class => $label) {
        echo '<option value="' . esc_attr($class) . '"' . selected($button_style, $class, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select></label>';
    if ($is_button && $button_style) {
        echo '<span style="display:inline-block;margin-left:10px;vertical-align:middle;">'
            . '<button type="button" class="btn ' . esc_attr($button_style) . '">Preview</button></span>';
    }
    echo '</p>';
}, 10, 4);

// Guardar los campos personalizados
add_action('wp_update_nav_menu_item', function($menu_id, $menu_item_db_id, $args) {
    // Icono FontAwesome
    $icon = isset($_POST['menu-item-fa-icon'][$menu_item_db_id]) ? sanitize_text_field($_POST['menu-item-fa-icon'][$menu_item_db_id]) : '';
    update_post_meta($menu_item_db_id, '_menu_item_fa_icon', $icon);

    // Mostrar como botón
    $is_button = isset($_POST['menu-item-is-button'][$menu_item_db_id]) ? '1' : '';
    update_post_meta($menu_item_db_id, '_menu_item_is_button', $is_button);

    // Estilo de botón Bootstrap
    $button_style = isset($_POST['menu-item-button-style'][$menu_item_db_id]) ? sanitize_text_field($_POST['menu-item-button-style'][$menu_item_db_id]) : '';
    update_post_meta($menu_item_db_id, '_menu_item_button_style', $button_style);
}, 10, 3);

/**
 * Include required files.
 */
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/bootstrap-navwalker.php';
require get_template_directory() . '/inc/login-customizer.php';

// Load performance optimization system
require get_template_directory() . '/inc/performance/cache-manager.php';
require get_template_directory() . '/inc/performance/preload-assets.php';
require get_template_directory() . '/inc/performance/fontawesome-detector.php';
require get_template_directory() . '/inc/performance/lazy-loading.php';
require get_template_directory() . '/inc/performance/critical-css.php';
require get_template_directory() . '/inc/performance/asset-minifier.php';

// Load theme options and admin functionality
require get_template_directory() . '/inc/admin/theme-options.php';
require get_template_directory() . '/inc/admin/template-helpers.php';
require get_template_directory() . '/inc/admin/acf-fields.php';
require get_template_directory() . '/inc/admin/newsletter-db.php';
require get_template_directory() . '/inc/admin/newsletter-admin.php';
require get_template_directory() . '/inc/admin/theme-documentation.php';

// Load custom blocks
require get_template_directory() . '/blocks/blocks.php';

// Load theme shortcodes (frontend helpers)
require get_template_directory() . '/inc/shortcodes.php';

// Load WooCommerce compatibility file.
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce-functions.php';
	require get_template_directory() . '/inc/admin/woocommerce-test-products.php';
	require get_template_directory() . '/inc/stock-control.php';
	require get_template_directory() . '/inc/performance/woocommerce-optimization.php';
}

// Debug de ACF removido para dejar el tema limpio

/**
 * Custom template tags for this theme.
 */
if ( ! function_exists( 'bootstrap_theme_posted_on' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function bootstrap_theme_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( 'Posted on %s', 'post date', 'bootstrap-theme' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on text-muted">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'bootstrap_theme_posted_by' ) ) {
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function bootstrap_theme_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'bootstrap-theme' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline text-muted"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'bootstrap_theme_entry_footer' ) ) {
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function bootstrap_theme_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'bootstrap-theme' ) );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links badge bg-primary me-2">%1$s</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'bootstrap-theme' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links badge bg-secondary">%1$s</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link ms-2">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'bootstrap-theme' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);
			echo '</span>';
		}
	}
}

/**
 * Generate responsive menu with Bootstrap collapse (only native Bootstrap classes)
 * 
 * @param array $args Menu arguments
 * @return string Menu HTML markup
 */
function bootstrap_theme_get_responsive_menu( $args = array() ) {
	$defaults = array(
		'container_class' => 'd-md-flex h-100 align-items-center',
		'menu_class' => 'nav',
		'show_cart' => true,
		'show_auth' => false,
		'collapse_id' => 'navCollapse',
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	ob_start();
	?>
	<!-- Mobile toggle for smaller screens -->
	<button class="btn btn-outline-secondary d-md-none btn-menu-collapse" type="button" 
			data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr( $args['collapse_id'] ); ?>" 
			aria-controls="<?php echo esc_attr( $args['collapse_id'] ); ?>" aria-expanded="false" 
			aria-label="<?php esc_attr_e( 'Toggle navigation', 'bootstrap-theme' ); ?>">
		<svg class="icon"><use xlink:href="#fa-bars"></use></svg>
	</button>
	
	<!-- Menu content -->
	<div class="collapse d-md-block" id="<?php echo esc_attr( $args['collapse_id'] ); ?>">
		<div class="<?php echo esc_attr( $args['container_class'] ); ?>">
			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'menu_class'     => $args['menu_class'] . ' me-3',
					'container'      => false,
					'depth'          => 2,
					'walker'         => new WP_Bootstrap_Navwalker(),
				) );
				?>
			<?php else : ?>
				<div class="alert alert-info me-3 mb-2 mb-md-0" role="alert">
					<small><?php esc_html_e( 'Please set up a menu in Appearance → Menus and assign it to the "Primary" location.', 'bootstrap-theme' ); ?></small>
				</div>
			<?php endif; ?>
		</div>
	</div>
		<div class="d-md-flex d-inline align-items-center justify-content-center">
			<div class="d-md-flex d-inline align-items-center mt-2 mt-md-0 extra-menu-items">
				<?php 
				$show_cart_option = function_exists('bootstrap_theme_get_woocommerce_option') 
					? bootstrap_theme_get_woocommerce_option('show_cart_icon') 
					: true;
				$show_cart = $args['show_cart'] && $show_cart_option && class_exists( 'WooCommerce' );
				if ( $show_cart ) : 
					// Get cart action preference
					$cart_action = function_exists('bootstrap_theme_get_woocommerce_option') 
						? bootstrap_theme_get_woocommerce_option('cart_icon_action') 
						: 'offcanvas';
					
					// Build button attributes based on action
					$button_attrs = '';
					$button_url = '#';
					
					switch ($cart_action) {
						case 'cart':
							$button_url = esc_url( wc_get_cart_url() );
							$button_attrs = '';
							break;
						case 'checkout':
							$button_url = esc_url( wc_get_checkout_url() );
							$button_attrs = '';
							break;
						case 'offcanvas':
						default:
							$button_attrs = 'data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas"';
							break;
					}
				?>
					<?php if ($cart_action === 'offcanvas') : ?>
						<button type="button" class="btn btn-outline-primary position-relative cart-toggle me-2" <?php echo $button_attrs; ?> aria-label="<?php esc_attr_e( 'Open Shopping Cart', 'bootstrap-theme' ); ?>">
							<svg class="icon">
								<use xlink:href="#fa-cart-shopping"></use>
							</svg>
							<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
								<?php echo WC()->cart->get_cart_contents_count(); ?>
							</span>
						</button>
					<?php else : ?>
						<a href="<?php echo $button_url; ?>" class="btn btn-outline-primary position-relative cart-toggle me-2" aria-label="<?php esc_attr_e( 'View Shopping Cart', 'bootstrap-theme' ); ?>">
							<svg class="icon">
								<use xlink:href="#fa-cart-shopping"></use>
							</svg>
							<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
								<?php echo WC()->cart->get_cart_contents_count(); ?>
							</span>
						</a>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( $args['show_auth'] ) : ?>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="btn btn-outline-primary me-2"><?php esc_html_e( 'Logout', 'bootstrap-theme' ); ?></a>
						<a href="<?php echo esc_url( admin_url() ); ?>" class="btn btn-primary"><?php esc_html_e( 'Dashboard', 'bootstrap-theme' ); ?></a>
					<?php else : ?>
						<a href="<?php echo esc_url( wp_login_url() ); ?>" class="btn btn-outline-primary me-2"><?php esc_html_e( 'Login', 'bootstrap-theme' ); ?></a>
						<a href="<?php echo esc_url( wp_registration_url() ); ?>" class="btn btn-primary"><?php esc_html_e( 'Sign-up', 'bootstrap-theme' ); ?></a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	<?php
	return ob_get_clean();
}

if ( ! function_exists( 'wp_body_open' ) ) {
	/**
	 * Shim for sites older than 5.2.
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

/**
 * WooCommerce: Replace default <ul> product loop with Bootstrap row/col grid
 */
if ( class_exists( 'WooCommerce' ) ) {
	// Wrap result count and ordering in a container
	add_action( 'woocommerce_before_shop_loop', function() {
		echo '<div class="woocommerce-toolbar d-flex flex-md-row flex-column justify-content-between align-items-center mb-4">';
	}, 15 );
	
	add_action( 'woocommerce_before_shop_loop', function() {
		echo '</div>';
	}, 35 );
	
	// Replace loop start with Bootstrap row - using dynamic columns from ACF options
	add_filter( 'woocommerce_product_loop_start', function( $html ) {
		$cols = 4; // default
		if (function_exists('bootstrap_theme_get_woocommerce_option_cached')) {
			$cols = (int) bootstrap_theme_get_woocommerce_option_cached('products_per_row');
			if ($cols <= 0) $cols = 4;
		}
		// Bootstrap row-cols classes based on columns setting
		return '<div class="products row row-cols-md-' . $cols . ' g-4">';
	});
	
	// Replace loop end
	add_filter( 'woocommerce_product_loop_end', function( $html ) {
		return '</div>';
	});
	
	// Customize loop add-to-cart button
	// - If product is out of stock, show disabled "Agotado" button instead of "Leer más"
	// - Otherwise, add Bootstrap styles to the default button
	add_filter( 'woocommerce_loop_add_to_cart_link', function( $html, $product, $args ) {
		if ( $product instanceof WC_Product ) {
			if ( ! $product->is_in_stock() ) {
				$label = __( 'Agotado', 'bootstrap-theme' );
				$attrs = array(
					'class' => 'button btn btn-secondary disabled',
					'aria-disabled' => 'true',
					'tabindex' => '-1',
					'style' => 'pointer-events:none;'
				);
				$attr_html = '';
				foreach ( $attrs as $k => $v ) {
					$attr_html .= ' ' . $k . '="' . esc_attr( $v ) . '"';
				}
				return sprintf( '<button type="button"%s>%s</button>', $attr_html, esc_html( $label ) );
			}
			// In stock: add Bootstrap classes to default link markup
			$html = str_replace( 'button', 'button btn btn-primary', $html );
		}
		return $html;
	}, 10, 3 );

	// Removed custom out-of-stock badge to avoid overlap with existing theme badge
	
	// Wrap price with Bootstrap classes
	add_filter( 'woocommerce_get_price_html', function( $price, $product ) {
		if ( ! empty( $price ) ) {
			return '<p class="card-text fw-bold text-primary">' . $price . '</p>';
		}
		return $price;
	}, 10, 2 );
}

// Include WooCommerce maintenance (only in admin)
if ( is_admin() ) {
	require_once get_template_directory() . '/inc/admin/woocommerce-maintenance.php';
}
