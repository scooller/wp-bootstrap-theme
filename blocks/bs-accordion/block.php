<?php
/**
 * Bootstrap Accordion Block
 * 
 * @package Bootstrap_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render Bootstrap Accordion Block
 */
function bootstrap_theme_render_bs_accordion_block($attributes, $content, $block) {
    $accordionId = $attributes['accordionId'] ?? 'accordion-' . uniqid();
    $flush = $attributes['flush'] ?? false;
    $alwaysOpen = $attributes['alwaysOpen'] ?? false;
    
    // Build accordion classes
    $classes = array('accordion');
    
    if ($flush) {
        $classes[] = 'accordion-flush';
    }
    
    // Add custom CSS classes from Advanced panel
    $classes = bootstrap_theme_add_custom_classes($classes, $attributes, $block);
    
    // Get animation data attributes
    $animation_attrs = '';
    if (function_exists('bootstrap_theme_get_animation_attributes')) {
        $animation_attrs = bootstrap_theme_get_animation_attributes($attributes, $block);
    }
    
    $class_string = implode(' ', array_unique($classes));
    
    $output = '<div class="' . esc_attr($class_string) . '" id="' . esc_attr($accordionId) . '"' . $animation_attrs;
    
    // Add data attributes for accordion behavior
    if (!$alwaysOpen) {
        $output .= ' data-bs-accordion-parent="true"';
    }
    
    $output .= '>';
    
    // Process the InnerBlocks content (accordion items)
    if (!empty($content)) {
        // The content should already be processed HTML from InnerBlocks.Content
        $output .= $content;
    } else {
        // Default content if no items
        $output .= '<div class="accordion-item">';
        $output .= '<h2 class="accordion-header" id="' . esc_attr($accordionId) . '-heading-0">';
        $output .= '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#' . esc_attr($accordionId) . '-collapse-0" aria-expanded="true" aria-controls="' . esc_attr($accordionId) . '-collapse-0">';
        $output .= __('Accordion Item #1', 'bootstrap-theme');
        $output .= '</button>';
        $output .= '</h2>';
        $output .= '<div id="' . esc_attr($accordionId) . '-collapse-0" class="accordion-collapse collapse show" aria-labelledby="' . esc_attr($accordionId) . '-heading-0" data-bs-parent="#' . esc_attr($accordionId) . '">';
        $output .= '<div class="accordion-body">';
        $output .= __('Add content to your accordion.', 'bootstrap-theme');
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    }
    
    $output .= '</div>'; // accordion
    
    return $output;
}

/**
 * Register Bootstrap Accordion Block
 */
function bootstrap_theme_register_bs_accordion_block() {
    register_block_type('bootstrap-theme/bs-accordion', array(
        'render_callback' => 'bootstrap_theme_render_bs_accordion_block',
        'supports' => array(
            'html' => true,
        ),
        'attributes' => array(
            'accordionId' => array(
                'type' => 'string',
                'default' => ''
            ),
            'flush' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'alwaysOpen' => array(
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
add_action('init', 'bootstrap_theme_register_bs_accordion_block');
