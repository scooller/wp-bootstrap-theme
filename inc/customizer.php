<?php
/**
 * Theme Customizer settings
 *
 * @package BootstrapTheme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add customizer settings
 */
function bootstrap_theme_customize_register($wp_customize)
{

    // Header & Footer Section
    $wp_customize->add_section('bootstrap_theme_header_footer', array(
        'title'    => __('Header y Footer', 'bootstrap-theme'),
        'priority' => 25,
    ));

    // Note: Header/Footer/Breadcrumb styles are configured in Admin > Theme Configuration > Customization
    // The following are kept for reference but main configuration is in ACF
    
    // Header Style Setting (Reference only - use ACF admin page)
    $wp_customize->add_setting('customization_header_style', array(
        'default'           => 'simple',
        'sanitize_callback' => 'sanitize_text_field',
        'type'              => 'option',
    ));

    $wp_customize->add_control('customization_header_style', array(
        'label'       => __('Estilo de Header', 'bootstrap-theme'),
        'description' => __('Nota: La configuración oficial está en Admin > Configuración del Tema > Personalización', 'bootstrap-theme'),
        'section'     => 'bootstrap_theme_header_footer',
        'type'        => 'select',
        'choices'  => array(
            'simple'           => 'Simple Header',
            'centered'         => 'Navegación Centrada',
            'with-buttons'     => 'Header con Botones',
            'dark'             => 'Header Oscuro',
            'with-avatar'      => 'Header con Avatar',
            'compact-dropdown' => 'Dropdown Compacto',
            'double'           => 'Header Doble',
            'iconized'         => 'Header con Iconos',
        ),
        'priority' => 10,
    ));

    // Footer Style Setting
    $wp_customize->add_setting('customization_footer_style', array(
        'default'           => 'default',
        'sanitize_callback' => 'sanitize_text_field',
        'type'              => 'option',
    ));

    $wp_customize->add_control('customization_footer_style', array(
        'label'    => __('Estilo de Footer', 'bootstrap-theme'),
        'section'  => 'bootstrap_theme_header_footer',
        'type'     => 'select',
        'choices'  => array(
            'default'                  => 'Footer Estándar',
            'footer-simple'            => 'Footer Simple',
            'footer-with-columns'      => 'Footer con Columnas',
            'footer-with-icons'        => 'Footer con Iconos Sociales',
            'footer-with-newsletter'   => 'Footer con Newsletter',
            'footer-minimal'           => 'Footer Minimalista',
            'footer-dark'              => 'Footer Oscuro',
            'footer-sticky'            => 'Footer Pegajoso',
        ),
        'priority' => 20,
    ));

    // Breadcrumb Style Setting
    $wp_customize->add_setting('customization_breadcrumb_style', array(
        'default'           => 'default',
        'sanitize_callback' => 'sanitize_text_field',
        'type'              => 'option',
    ));

    $wp_customize->add_control('customization_breadcrumb_style', array(
        'label'    => __('Estilo de Breadcrumbs', 'bootstrap-theme'),
        'section'  => 'bootstrap_theme_header_footer',
        'type'     => 'select',
        'choices'  => array(
            'default'                    => 'Breadcrumb Estándar',
            'breadcrumb-slash'           => 'Separador con Slash (/)',
            'breadcrumb-arrow'           => 'Separador con Flecha (>)',
            'breadcrumb-bullet'          => 'Separador con Bullet (•)',
            'breadcrumb-custom'          => 'Separador Personalizado',
            'breadcrumb-card'            => 'Breadcrumb en Card',
            'breadcrumb-bg-light'        => 'Fondo Claro',
            'breadcrumb-no-background'   => 'Sin Fondo',
        ),
        'priority' => 30,
    ));

    // Hero Section
    $wp_customize->add_section('hero_section', array(
        'title'    => __('Hero Section', 'bootstrap-theme'),
        'priority' => 30,
    ));

    // Hero Title
    $wp_customize->add_setting('hero_title', array(
        'default'           => __('Welcome to Our Website', 'bootstrap-theme'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('hero_title', array(
        'label'   => __('Hero Title', 'bootstrap-theme'),
        'section' => 'hero_section',
        'type'    => 'text',
    ));

    // Hero Description
    $wp_customize->add_setting('hero_description', array(
        'default'           => __('This is a beautiful WordPress theme built with Bootstrap 5.3', 'bootstrap-theme'),
        'sanitize_callback' => 'sanitize_textarea_field',
    ));

    $wp_customize->add_control('hero_description', array(
        'label'   => __('Hero Description', 'bootstrap-theme'),
        'section' => 'hero_section',
        'type'    => 'textarea',
    ));

    // Hero Button Text
    $wp_customize->add_setting('hero_button_text', array(
        'default'           => __('Learn More', 'bootstrap-theme'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('hero_button_text', array(
        'label'   => __('Hero Button Text', 'bootstrap-theme'),
        'section' => 'hero_section',
        'type'    => 'text',
    ));

    // Hero Button URL
    $wp_customize->add_setting('hero_button_url', array(
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control('hero_button_url', array(
        'label'   => __('Hero Button URL', 'bootstrap-theme'),
        'section' => 'hero_section',
        'type'    => 'url',
    ));

    // Hero Image
    $wp_customize->add_setting('hero_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'hero_image', array(
        'label'   => __('Hero Image', 'bootstrap-theme'),
        'section' => 'hero_section',
    )));

    // Colors Section
    $wp_customize->add_section('theme_colors', array(
        'title'    => __('Theme Colors', 'bootstrap-theme'),
        'priority' => 40,
    ));

    // Primary Color
    $wp_customize->add_setting('primary_color', array(
        'default'           => '#0d6efd',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label'   => __('Primary Color', 'bootstrap-theme'),
        'section' => 'theme_colors',
    )));

    // Secondary Color
    $wp_customize->add_setting('secondary_color', array(
        'default'           => '#6c757d',
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color', array(
        'label'   => __('Secondary Color', 'bootstrap-theme'),
        'section' => 'theme_colors',
    )));

    // Footer Section
    $wp_customize->add_section('footer_settings', array(
        'title'    => __('Footer Settings', 'bootstrap-theme'),
        'priority' => 50,
    ));

    // Footer Copyright Text
    $wp_customize->add_setting('footer_copyright', array(
        'default'           => sprintf(__('© %s %s. All rights reserved.', 'bootstrap-theme'), date('Y'), get_bloginfo('name')),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('footer_copyright', array(
        'label'   => __('Footer Copyright Text', 'bootstrap-theme'),
        'section' => 'footer_settings',
        'type'    => 'text',
    ));

    // Show Footer Social Links
    $wp_customize->add_setting('show_social_links', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));

    $wp_customize->add_control('show_social_links', array(
        'label'   => __('Show Social Links', 'bootstrap-theme'),
        'section' => 'footer_settings',
        'type'    => 'checkbox',
    ));

    // Social Links
    $social_networks = array(
        'facebook'  => __('Facebook', 'bootstrap-theme'),
        'twitter'   => __('Twitter', 'bootstrap-theme'),
        'instagram' => __('Instagram', 'bootstrap-theme'),
        'linkedin'  => __('LinkedIn', 'bootstrap-theme'),
        'youtube'   => __('YouTube', 'bootstrap-theme'),
        'github'    => __('GitHub', 'bootstrap-theme'),
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting("social_{$network}", array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control("social_{$network}", array(
            'label'           => $label . __(' URL', 'bootstrap-theme'),
            'section'         => 'footer_settings',
            'type'            => 'url',
            'active_callback' => function () {
                return get_theme_mod('show_social_links', true);
            },
        ));
    }

    // Typography Section
    $wp_customize->add_section('typography', array(
        'title'    => __('Typography', 'bootstrap-theme'),
        'priority' => 60,
    ));

    // Body Font
    $wp_customize->add_setting('body_font', array(
        'default'           => 'Segoe UI',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('body_font', array(
        'label'   => __('Body Font Family', 'bootstrap-theme'),
        'section' => 'typography',
        'type'    => 'select',
        'choices' => array(
            'Segoe UI'      => 'Segoe UI',
            'Arial'         => 'Arial',
            'Helvetica'     => 'Helvetica',
            'Georgia'       => 'Georgia',
            'Times'         => 'Times New Roman',
            'Courier'       => 'Courier New',
            'Verdana'       => 'Verdana',
            'Tahoma'        => 'Tahoma',
        ),
    ));

    // Heading Font
    $wp_customize->add_setting('heading_font', array(
        'default'           => 'Segoe UI',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('heading_font', array(
        'label'   => __('Heading Font Family', 'bootstrap-theme'),
        'section' => 'typography',
        'type'    => 'select',
        'choices' => array(
            'Segoe UI'      => 'Segoe UI',
            'Arial'         => 'Arial',
            'Helvetica'     => 'Helvetica',
            'Georgia'       => 'Georgia',
            'Times'         => 'Times New Roman',
            'Courier'       => 'Courier New',
            'Verdana'       => 'Verdana',
            'Tahoma'        => 'Tahoma',
        ),
    ));

    // Layout Section
    $wp_customize->add_section('layout_settings', array(
        'title'    => __('Layout Settings', 'bootstrap-theme'),
        'priority' => 70,
    ));

    // Container Width
    $wp_customize->add_setting('container_width', array(
        'default'           => 'container',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('container_width', array(
        'label'   => __('Container Width', 'bootstrap-theme'),
        'section' => 'layout_settings',
        'type'    => 'select',
        'choices' => array(
            'container'       => __('Default Container', 'bootstrap-theme'),
            'container-fluid' => __('Full Width Container', 'bootstrap-theme'),
        ),
    ));

    // Sidebar Position
    $wp_customize->add_setting('sidebar_position', array(
        'default'           => 'right',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('sidebar_position', array(
        'label'   => __('Sidebar Position', 'bootstrap-theme'),
        'section' => 'layout_settings',
        'type'    => 'select',
        'choices' => array(
            'right' => __('Right Sidebar', 'bootstrap-theme'),
            'left'  => __('Left Sidebar', 'bootstrap-theme'),
            'none'  => __('No Sidebar', 'bootstrap-theme'),
        ),
    ));
}
add_action('customize_register', 'bootstrap_theme_customize_register');

/**
 * Output custom CSS based on customizer settings
 */
function bootstrap_theme_customizer_css()
{
    $primary_color = get_theme_mod('primary_color', '#0d6efd');
    $secondary_color = get_theme_mod('secondary_color', '#6c757d');
    $body_font = get_theme_mod('body_font', 'Segoe UI');
    $heading_font = get_theme_mod('heading_font', 'Segoe UI');

    // Get ACF customization options for gradients
    $light_color = bootstrap_theme_get_option('customization_light_color');
    $light_gradient = bootstrap_theme_get_option('customization_light_gradient');
    $light_color_secondary = bootstrap_theme_get_option('customization_light_color_secondary');
    $light_gradient_direction = bootstrap_theme_get_option('customization_light_gradient_direction');
    
    $dark_color = bootstrap_theme_get_option('customization_dark_color');
    $dark_gradient = bootstrap_theme_get_option('customization_dark_gradient');
    $dark_color_secondary = bootstrap_theme_get_option('customization_dark_color_secondary');
    $dark_gradient_direction = bootstrap_theme_get_option('customization_dark_gradient_direction');

    // Build light background
    $light_bg = $light_color ?: '#f8f9fa';
    $light_bg_style = '';
    if ($light_gradient && $light_color_secondary) {
        $direction = $light_gradient_direction ?: 'to bottom';
        $light_bg_style = "linear-gradient({$direction}, {$light_color}, {$light_color_secondary})";
    } else {
        $light_bg_style = "{$light_bg}";
    }

    // Build dark background
    $dark_bg = $dark_color ?: '#212529';
    $dark_bg_style = '';
    if ($dark_gradient && $dark_color_secondary) {
        $direction = $dark_gradient_direction ?: 'to bottom';
        $dark_bg_style = "linear-gradient({$direction}, {$dark_color}, {$dark_color_secondary})";
    } else {
        $dark_bg_style = "{$dark_bg}";
    }

    $css = "
        :root {
            --bs-primary: {$primary_color};
            --bs-secondary: {$secondary_color};
            --bs-light: {$light_color};
            --bs-dark: {$dark_color};
        }

        [data-bs-theme=\"light\"] {
            --bd-new-bg: {$light_bg_style};
        }
        
        [data-bs-theme=\"dark\"] {
            --bd-new-bg: {$dark_bg_style};
        }
        
        body {
            font-family: '{$body_font}', sans-serif;
            background: var(--bd-new-bg)!important;
        }
        
        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: '{$heading_font}', sans-serif;
        }
        
        .btn-primary {
            background-color: {$primary_color};
            border-color: {$primary_color};
        }
        
        .btn-primary:hover {
            background-color: " . bootstrap_theme_darken_color($primary_color, 10) . ";
            border-color: " . bootstrap_theme_darken_color($primary_color, 10) . ";
        }
        
        .text-primary {
            color: {$primary_color} !important;
        }
        
        .bg-primary {
            background-color: {$primary_color} !important;
        }
        
        .navbar-dark .navbar-nav .nav-link.active::after {
            background: {$primary_color};
        }
        
        .widget-title {
            border-bottom-color: {$primary_color};
        }
    ";

    wp_add_inline_style('bootstrap-theme-style', $css);
}
add_action('wp_enqueue_scripts', 'bootstrap_theme_customizer_css');

/**
 * Helper function to darken a color
 */
function bootstrap_theme_darken_color($color, $percent)
{
    $color = str_replace('#', '', $color);
    $r = hexdec(substr($color, 0, 2));
    $g = hexdec(substr($color, 2, 2));
    $b = hexdec(substr($color, 4, 2));

    $r = max(0, min(255, $r - ($r * $percent / 100)));
    $g = max(0, min(255, $g - ($g * $percent / 100)));
    $b = max(0, min(255, $b - ($b * $percent / 100)));

    return sprintf('#%02x%02x%02x', $r, $g, $b);
}
