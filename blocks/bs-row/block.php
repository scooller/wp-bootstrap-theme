<?php
/**
 * Bootstrap Row Block
 * 
 * @package Bootstrap_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render Bootstrap Row Block
 */
function bootstrap_theme_render_bs_row_block($attributes, $content, $block) {
    $alignItems = $attributes['alignItems'] ?? '';
    $justifyContent = $attributes['justifyContent'] ?? '';
    $gutters = $attributes['gutters'] ?? '';
    $noGutters = $attributes['noGutters'] ?? false;
    
    // Build row classes
    $classes = array('row');
    
    if ($noGutters) {
        $classes[] = 'g-0';
    } else if (!empty($gutters)) {
        $classes[] = $gutters;
    }
    
    if (!empty($alignItems)) {
        $classes[] = 'align-items-' . $alignItems;
    }
    
    if (!empty($justifyContent)) {
        $classes[] = 'justify-content-' . $justifyContent;
    }
    
    // Add custom CSS classes from Advanced panel
    if (!empty($attributes['className'])) {
        $classes[] = $attributes['className'];
    }

    // Alternative way to get custom classes from block object
    if (isset($block->attributes['className']) && !empty($block->attributes['className'])) {
        $classes[] = $block->attributes['className'];
    }
    
    // Get animation data attributes
    $animation_attrs = '';
    if (function_exists('bootstrap_theme_get_animation_attributes')) {
        $animation_attrs = bootstrap_theme_get_animation_attributes($attributes, $block);
    }

    $class_string = implode(' ', array_unique($classes));

    $output = '<div class="' . esc_attr($class_string) . '"' . $animation_attrs . '>';    // Add content from InnerBlocks
    if (!empty($content)) {
        $output .= $content;
    } else {
        $output .= '<div class="col"><p>' . __('Add columns to your row.', 'bootstrap-theme') . '</p></div>';
    }
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Register Bootstrap Row Block
 */
function bootstrap_theme_register_bs_row_block() {
    register_block_type('bootstrap-theme/bs-row', array(
        'render_callback' => 'bootstrap_theme_render_bs_row_block',
        'attributes' => array(
            'alignItems' => array(
                'type' => 'string',
                'default' => ''
            ),
            'justifyContent' => array(
                'type' => 'string',
                'default' => ''
            ),
            'gutters' => array(
                'type' => 'string',
                'default' => ''
            ),
            'noGutters' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'className' => array(
                'type' => 'string',
                'default' => ''
            ),
            'aosAnimation' => array(
                'type' => 'string',
                'default' => ''
            ),
            'aosDelay' => array(
                'type' => 'number',
                'default' => 0
            ),
            'aosDuration' => array(
                'type' => 'number',
                'default' => 800
            ),
            'aosEasing' => array(
                'type' => 'string',
                'default' => 'ease-in-out-cubic'
            ),
            'aosOnce' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'aosMirror' => array(
                'type' => 'boolean',
                'default' => true
            ),
            'aosAnchorPlacement' => array(
                'type' => 'string',
                'default' => 'top-bottom'
            )
        )
    ));
}
add_action('init', 'bootstrap_theme_register_bs_row_block');
