<?php
/**
 * Simple asset bundler for theme-controlled CSS/JS
 *
 * - CSS: concatenate + lightweight minify for theme CSS assets we own
 * - JS: concatenate theme JS assets we own (no aggressive minify to avoid breakage)
 *
 * Controlled by ACF options:
 * - extras_enable_css_compression
 * - extras_enable_js_compression
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hook late to replace multiple enqueues with single bundles when enabled
 */
add_action( 'wp_enqueue_scripts', function() {
    // CSS bundle
    if ( function_exists( 'bootstrap_theme_is_css_compression_enabled' ) && bootstrap_theme_is_css_compression_enabled() ) {
        bootstrap_theme_build_css_bundle();
    }

    // JS bundle
    if ( function_exists( 'bootstrap_theme_is_js_compression_enabled' ) && bootstrap_theme_is_js_compression_enabled() ) {
        bootstrap_theme_build_js_bundle();
    }
}, 999 );

/**
 * Build and enqueue a single CSS bundle from theme-owned styles
 */
function bootstrap_theme_build_css_bundle() {
    global $wp_styles;
    if ( ! isset( $wp_styles ) || ! $wp_styles instanceof WP_Styles ) {
        return;
    }

    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();

    // Theme CSS handles we control (adjust as needed)
    $handles = array(
        'bootstrap-theme-style', // style.css
        'bootstrap-theme-loader', // assets/css/loader.css
        'bootstrap-theme-icons',  // assets/css/icons.css
        'bootstrap-theme-custom', // assets/css/theme.css (Sass compiled)
    );

    $sources = array();
    foreach ( $handles as $h ) {
        if ( empty( $wp_styles->registered[ $h ] ) ) {
            continue;
        }
        $src = $wp_styles->registered[ $h ]->src;
        if ( ! $src ) {
            continue;
        }
        // Only bundle theme-local files
        if ( strpos( $src, $theme_uri ) !== 0 ) {
            continue;
        }
        $rel = substr( $src, strlen( $theme_uri ) );
        $path = wp_normalize_path( $theme_dir . $rel );
        if ( file_exists( $path ) && is_readable( $path ) ) {
            $sources[] = $path;
        }
    }

    if ( empty( $sources ) ) {
        return;
    }

    $bundle_info = bootstrap_theme_generate_bundle_file( $sources, 'css' );
    if ( ! $bundle_info ) {
        return;
    }

    // Dequeue originals and enqueue bundle
    foreach ( $handles as $h ) {
        wp_dequeue_style( $h );
    }

    wp_enqueue_style(
        'bootstrap-theme-bundle-css',
        $bundle_info['url'],
        array(),
        $bundle_info['ver']
    );
}

/**
 * Build and enqueue a single JS bundle from theme-owned scripts
 */
function bootstrap_theme_build_js_bundle() {
    global $wp_scripts;
    if ( ! isset( $wp_scripts ) || ! $wp_scripts instanceof WP_Scripts ) {
        return;
    }

    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();

    // Theme JS handles to bundle (keep jquery/bootstrap separate)
    $handles = array(
        'bootstrap-theme-script', // assets/js/theme.js
        'bootstrap-theme-loader', // assets/js/loader.js
    );

    $sources = array();
    foreach ( $handles as $h ) {
        if ( empty( $wp_scripts->registered[ $h ] ) ) {
            continue;
        }
        $src = $wp_scripts->registered[ $h ]->src;
        if ( ! $src ) {
            continue;
        }
        // Only bundle theme-local files
        if ( strpos( $src, $theme_uri ) !== 0 ) {
            continue;
        }
        $rel = substr( $src, strlen( $theme_uri ) );
        $path = wp_normalize_path( $theme_dir . $rel );
        if ( file_exists( $path ) && is_readable( $path ) ) {
            $sources[] = $path;
        }
    }

    if ( empty( $sources ) ) {
        return;
    }

    $bundle_info = bootstrap_theme_generate_bundle_file( $sources, 'js' );
    if ( ! $bundle_info ) {
        return;
    }

    // Dequeue originals and enqueue bundle (keep dependencies intact)
    foreach ( $handles as $h ) {
        wp_dequeue_script( $h );
        wp_deregister_script( $h );
    }

    wp_enqueue_script(
        'bootstrap-theme-bundle-js',
        $bundle_info['url'],
        array( 'jquery', 'bootstrap' ),
        $bundle_info['ver'],
        true
    );
}

/**
 * Generate bundle file in uploads cache folder
 *
 * @param array $files Absolute file paths
 * @param string $type 'css' or 'js'
 * @return array|false ['path'=>..., 'url'=>..., 'ver'=>...]
 */
function bootstrap_theme_generate_bundle_file( array $files, $type ) {
    $uploads = wp_get_upload_dir();
    if ( empty( $uploads['basedir'] ) || empty( $uploads['baseurl'] ) ) {
        return false;
    }
    $cache_dir = wp_normalize_path( trailingslashit( $uploads['basedir'] ) . 'bootstrap-theme-cache' );
    $cache_url = trailingslashit( $uploads['baseurl'] ) . 'bootstrap-theme-cache/';
    if ( ! file_exists( $cache_dir ) ) {
        wp_mkdir_p( $cache_dir );
    }

    // Build hash from file mtimes and sizes to invalidate when assets change
    $sig = $type;
    foreach ( $files as $f ) {
        $sig .= '|' . basename( $f ) . ':' . @filemtime( $f ) . ':' . @filesize( $f );
    }
    $hash = md5( $sig );
    $bundle_name = 'bundle-' . $hash . '.' . $type;
    $bundle_path = wp_normalize_path( $cache_dir . '/' . $bundle_name );
    $bundle_url  = $cache_url . $bundle_name;

    if ( ! file_exists( $bundle_path ) ) {
        $content = '';
        foreach ( $files as $f ) {
            $raw = @file_get_contents( $f );
            if ( false === $raw ) {
                continue;
            }
            if ( 'css' === $type ) {
                if ( function_exists( 'bootstrap_theme_minify_css' ) ) {
                    $raw = bootstrap_theme_minify_css( $raw );
                } else {
                    $raw = bootstrap_theme_minify_css_fallback( $raw );
                }
            } else {
                // Keep JS safe: trim only
                $raw = rtrim( $raw ) . ";\n";
            }
            $content .= $raw . "\n";
        }
        // Write atomically
        @file_put_contents( $bundle_path, $content );
    }

    return array(
        'path' => $bundle_path,
        'url'  => $bundle_url,
        'ver'  => @filemtime( $bundle_path ) ?: $hash,
    );
}

/**
 * Fallback CSS minifier (if the theme helper isn't loaded yet)
 */
function bootstrap_theme_minify_css_fallback( $css ) {
    // Remove comments
    $css = preg_replace( '!/\*.*?\*/!s', '', $css );
    // Remove whitespace
    $css = preg_replace( '/\s+/', ' ', $css );
    $css = str_replace( array( "\n", "\r", "\t" ), ' ', $css );
    // Remove space around symbols
    $css = preg_replace( '/\s*([{};:,>])\s*/', '$1', $css );
    // Remove last semicolon in block
    $css = preg_replace( '/;}/', '}', $css );
    return trim( $css );
}
