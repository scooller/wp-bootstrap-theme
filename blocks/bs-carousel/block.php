<?php
/**
 * Bootstrap Carousel Block
 * 
 * @package Bootstrap_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render Bootstrap Carousel Block
 */
function bootstrap_theme_render_bs_carousel_block($attributes, $content, $block) {
    $carouselId = !empty($attributes['carouselId']) ? $attributes['carouselId'] : wp_unique_id('carousel-');
    $showControls = array_key_exists('showControls', $attributes) ? (bool) $attributes['showControls'] : (bool) ($attributes['controls'] ?? true);
    $showIndicators = array_key_exists('showIndicators', $attributes) ? (bool) $attributes['showIndicators'] : (bool) ($attributes['indicators'] ?? true);
    $autoplay = array_key_exists('autoplay', $attributes) ? (bool) $attributes['autoplay'] : (($attributes['ride'] ?? 'carousel') === 'carousel');
    $interval = isset($attributes['interval']) ? (int) $attributes['interval'] : 5000;
    $fade = !empty($attributes['fade']);
    $wrap = array_key_exists('wrap', $attributes) ? (bool) $attributes['wrap'] : true;
    $touch = array_key_exists('touch', $attributes) ? (bool) $attributes['touch'] : true;
    
    // Build carousel classes
    $classes = array('carousel', 'slide');
    
    if ($fade) {
        $classes[] = 'carousel-fade';
    }
    
    // Add custom CSS classes from Advanced panel
    $classes = bootstrap_theme_add_custom_classes($classes, $attributes, $block);
    
    $class_string = implode(' ', array_unique($classes));
    
    $carousel_data = array(
        'data-bs-ride' => $autoplay ? 'carousel' : 'false',
        'data-bs-interval' => $interval,
        'data-bs-wrap' => $wrap ? 'true' : 'false',
        'data-bs-touch' => $touch ? 'true' : 'false'
    );
    
    $data_attrs = '';
    foreach ($carousel_data as $key => $value) {
        $data_attrs .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
    }
    
    $output = '<div class="' . esc_attr($class_string) . '" id="' . esc_attr($carouselId) . '"' . $data_attrs . '>';
    
    // Detect slides and ensure at least one is active to keep Bootstrap JS happy
    $slides_count = 0;
    $active_index = null;

    if (!empty($content)) {
        if (preg_match_all('/class=["\']([^"\']*carousel-item[^"\']*)["\']/', $content, $matches)) {
            $slides_count = count($matches[1]);
            foreach ($matches[1] as $index => $class_string) {
                if (strpos($class_string, 'active') !== false) {
                    $active_index = $index;
                    break;
                }
            }

            if ($slides_count > 0 && $active_index === null) {
                // Mark the first slide as active if none is set
                $content = preg_replace('/class=["\']([^"\']*carousel-item)([^"\']*)["\']/', 'class="$1 active$2"', $content, 1);
                $active_index = 0;
            }
        }
    } else {
        // Default content if no items
        $content .= '<div class="carousel-item active">';
        $content .= '<div class="carousel-placeholder d-block w-100" style="height: 400px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">';
        $content .= '<span class="text-muted">' . __('Add carousel items...', 'bootstrap-theme') . '</span>';
        $content .= '</div>';
        $content .= '</div>';
        $slides_count = 1;
        $active_index = 0;
    }

    // Indicators - build buttons so Bootstrap can sync the active state safely
    if ($showIndicators && $slides_count > 0) {
        $output .= '<div class="carousel-indicators" id="' . esc_attr($carouselId) . '-indicators">';
        for ($i = 0; $i < $slides_count; $i++) {
            $is_active = ($active_index === null) ? ($i === 0) : ($i === $active_index);
            $output .= '<button type="button" data-bs-target="#' . esc_attr($carouselId) . '" data-bs-slide-to="' . esc_attr($i) . '"';
            if ($is_active) {
                $output .= ' class="active" aria-current="true"';
            }
            $output .= ' aria-label="' . esc_attr(sprintf(__('Slide %d', 'bootstrap-theme'), $i + 1)) . '"></button>';
        }
        $output .= '</div>';
    }
    
    // Slides from InnerBlocks
    $output .= '<div class="carousel-inner">';
    $output .= $content;
    $output .= '</div>';
    
    // Controls
    if ($showControls) {
        $output .= '<button class="carousel-control-prev" type="button" data-bs-target="#' . esc_attr($carouselId) . '" data-bs-slide="prev">';
        $output .= '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
        $output .= '<span class="visually-hidden">' . __('Previous', 'bootstrap-theme') . '</span>';
        $output .= '</button>';
        
        $output .= '<button class="carousel-control-next" type="button" data-bs-target="#' . esc_attr($carouselId) . '" data-bs-slide="next">';
        $output .= '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
        $output .= '<span class="visually-hidden">' . __('Next', 'bootstrap-theme') . '</span>';
        $output .= '</button>';
    }
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Register Bootstrap Carousel Block
 */
function bootstrap_theme_register_bs_carousel_block() {
    register_block_type('bootstrap-theme/bs-carousel', array(
        'render_callback' => 'bootstrap_theme_render_bs_carousel_block',
        'supports' => array(
            'html' => true,
        ),
        'attributes' => array(
            'carouselId' => array(
                'type' => 'string',
                'default' => ''
            ),
            'showControls' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'showIndicators' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'autoplay' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'controls' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'indicators' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'ride' => array(
                'type' => 'string',
                'default' => 'carousel'
            ),
            'wrap' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'touch' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'interval' => array(
                'type' => 'integer',
                'default' => 5000
            ),
            'fade' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'preview' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'className' => array(
                'type' => 'string',
                'default' => ''
            )
        )
    ));
}
add_action('init', 'bootstrap_theme_register_bs_carousel_block');
