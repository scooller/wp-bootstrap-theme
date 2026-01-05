<?php
/**
 * Bootstrap List Group Block
 * 
 * @package Bootstrap_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render Bootstrap List Group Block
 */
function bootstrap_theme_render_bs_list_group_block($attributes, $content, $block) {
    $flush = $attributes['flush'] ?? false;
    $numbered = $attributes['numbered'] ?? false;
    $horizontal = $attributes['horizontal'] ?? false;
    $breakpoint = $attributes['breakpoint'] ?? '';
    
    // Build list group classes
    $classes = array('list-group');
    
    if ($flush) {
        $classes[] = 'list-group-flush';
    }
    
    if ($numbered) {
        $classes[] = 'list-group-numbered';
    }
    
    if ($horizontal) {
        if (!empty($breakpoint)) {
            $classes[] = 'list-group-horizontal-' . $breakpoint;
        } else {
            $classes[] = 'list-group-horizontal';
        }
    }
    
    // Add custom CSS classes from Advanced panel
    $classes = bootstrap_theme_add_custom_classes($classes, $attributes, $block);
    
    // Get animation data attributes
    $animation_attrs = '';
    if (function_exists('bootstrap_theme_get_animation_attributes')) {
        $animation_attrs = bootstrap_theme_get_animation_attributes($attributes, $block);
    }
    
    $class_string = implode(' ', array_unique($classes));
    
    $tag = $numbered ? 'ol' : 'div';
    
    $output = '<' . $tag . ' class="' . esc_attr($class_string) . '"' . $animation_attrs . '>';
    
    // Process the InnerBlocks content (list group items)
    if (!empty($content)) {
        // The content should already be processed HTML from InnerBlocks.Content
        $output .= $content;
    } else {
        // Default content if no items
        $output .= '<div class="list-group-item">' . __('First item', 'bootstrap-theme') . '</div>';
        $output .= '<div class="list-group-item active">' . __('Second item', 'bootstrap-theme') . '</div>';
        $output .= '<div class="list-group-item">' . __('Third item', 'bootstrap-theme') . '</div>';
    }
    
    $output .= '</' . $tag . '>';
    
    return $output;
}

/**
 * Register Bootstrap List Group Block
 */
function bootstrap_theme_register_bs_list_group_block() {
    register_block_type('bootstrap-theme/bs-list-group', array(
        'render_callback' => 'bootstrap_theme_render_bs_list_group_block',
        'attributes' => array(
            'flush' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'numbered' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'horizontal' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'breakpoint' => array(
                'type' => 'string',
                'default' => ''
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
        ),
        'supports' => array(
            'html' => true
        )
    ));
}
add_action('init', 'bootstrap_theme_register_bs_list_group_block');
