<?php
/**
 * Bootstrap Container Block
 * 
 * @package Bootstrap_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render Bootstrap Container Block
 */
function bootstrap_theme_render_bs_container_block($attributes, $content, $block) {
    $type = $attributes['type'] ?? 'container';
    $fluid = $attributes['fluid'] ?? false;
    $breakpoint = $attributes['breakpoint'] ?? '';
    $backgroundColor = $attributes['backgroundColor'] ?? '';
    $textColor = $attributes['textColor'] ?? '';
    $padding = $attributes['padding'] ?? '';
    $margin = $attributes['margin'] ?? '';
    // New background options
    $bgType = $attributes['bgType'] ?? 'none'; // none|solid|gradient|image
    $bgColor = $attributes['bgColor'] ?? '';
    $bgGradientFrom = $attributes['bgGradientFrom'] ?? '';
    $bgGradientTo = $attributes['bgGradientTo'] ?? '';
    $bgGradientDirection = $attributes['bgGradientDirection'] ?? 'to right';
    $backgroundImage = $attributes['backgroundImage'] ?? '';
    $bgSize = $attributes['bgSize'] ?? 'cover';
    $bgPosition = $attributes['bgPosition'] ?? 'center';
    $bgRepeat = $attributes['bgRepeat'] ?? 'no-repeat';
    $bgAttachment = $attributes['bgAttachment'] ?? 'scroll';
    $anchor = $attributes['anchor'] ?? '';
    
    // Build container classes
    $classes = array();
    
    if ($fluid) {
        $classes[] = 'container-fluid';
    } else if (!empty($breakpoint)) {
        $classes[] = 'container-' . $breakpoint;
    } else {
        $classes[] = 'container';
    }
    
    // Add utility classes
    if (!empty($backgroundColor)) {
        $classes[] = $backgroundColor;
    }
    
    if (!empty($textColor)) {
        $classes[] = $textColor;
    }
    
    if (!empty($padding)) {
        $classes[] = $padding;
    }
    
    if (!empty($margin)) {
        $classes[] = $margin;
    }
    
    // Add custom CSS classes from Advanced panel
    if (!empty($attributes['className'])) {
        $classes[] = $attributes['className'];
    }
    
    // Alternative way to get custom classes from block object
    if (isset($block->attributes['className']) && !empty($block->attributes['className'])) {
        $classes[] = $block->attributes['className'];
    }
    
    $class_string = implode(' ', array_unique($classes));

    // Build inline styles for custom background
    $styles = array();
    $sanitize_color = function($color) {
        $color = trim($color);
        if ($color === '') return '';
        // Accept hex (#RGB or #RRGGBB)
        if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color)) return $color;
        // Accept rgb/rgba()
        if (preg_match('/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}(\s*,\s*(0|1|0?\.\d+))?\s*\)$/', $color)) return $color;
        // Accept hsl/hsla() basic
        if (preg_match('/^hsla?\(.*\)$/', $color)) return $color;
        return '';
    };

    if ($bgType === 'solid') {
        $color = $sanitize_color($bgColor);
        if ($color) {
            $styles['background-color'] = $color;
        }
    } elseif ($bgType === 'gradient') {
        $from = $sanitize_color($bgGradientFrom);
        $to = $sanitize_color($bgGradientTo);
        $dir = in_array($bgGradientDirection, array('to right','to left','to bottom','to top','45deg','135deg'), true) ? $bgGradientDirection : 'to right';
        if ($from && $to) {
            $styles['background-image'] = 'linear-gradient(' . $dir . ', ' . $from . ', ' . $to . ')';
        }
    } elseif ($bgType === 'image' && !empty($backgroundImage)) {
        $styles['background-image'] = 'url(' . esc_url($backgroundImage) . ')';
        $styles['background-size'] = in_array($bgSize, array('cover','contain','auto'), true) ? $bgSize : 'cover';
        $styles['background-position'] = esc_attr($bgPosition);
        $styles['background-repeat'] = in_array($bgRepeat, array('no-repeat','repeat','repeat-x','repeat-y'), true) ? $bgRepeat : 'no-repeat';
        $styles['background-attachment'] = in_array($bgAttachment, array('scroll','fixed','local'), true) ? $bgAttachment : 'scroll';
    }

    $style_string = '';
    if (!empty($styles)) {
        $pairs = array();
        foreach ($styles as $k => $v) {
            $pairs[] = $k . ':' . $v;
        }
        $style_string = ' style="' . esc_attr(implode(';', $pairs)) . '"';
    }

    $id_attr = '';
    if (!empty($anchor)) {
        $id_attr = ' id="' . esc_attr($anchor) . '"';
    }

    // Get AOS animation attributes
    $aos_attrs = bootstrap_theme_get_animation_attributes($attributes, $block);

    $output = '<div class="' . esc_attr($class_string) . '"' . $id_attr . $style_string . $aos_attrs . '>';
    
    // Add content from InnerBlocks
    if (!empty($content)) {
        $output .= $content;
    } else {
        $output .= '<p>' . __('Add content to your container.', 'bootstrap-theme') . '</p>';
    }
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Register Bootstrap Container Block
 */
function bootstrap_theme_register_bs_container_block() {
    register_block_type('bootstrap-theme/bs-container', array(
        'render_callback' => 'bootstrap_theme_render_bs_container_block',
        'attributes' => array(
            'type' => array(
                'type' => 'string',
                'default' => 'container'
            ),
            'fluid' => array(
                'type' => 'boolean',
                'default' => false
            ),
            'breakpoint' => array(
                'type' => 'string',
                'default' => ''
            ),
            'backgroundColor' => array(
                'type' => 'string',
                'default' => ''
            ),
            'textColor' => array(
                'type' => 'string',
                'default' => ''
            ),
            'padding' => array(
                'type' => 'string',
                'default' => ''
            ),
            'margin' => array(
                'type' => 'string',
                'default' => ''
            ),
            // New background attributes
            'bgType' => array(
                'type' => 'string',
                'default' => 'none'
            ),
            'bgColor' => array(
                'type' => 'string',
                'default' => ''
            ),
            'bgGradientFrom' => array(
                'type' => 'string',
                'default' => ''
            ),
            'bgGradientTo' => array(
                'type' => 'string',
                'default' => ''
            ),
            'bgGradientDirection' => array(
                'type' => 'string',
                'default' => 'to right'
            ),
            'backgroundImage' => array(
                'type' => 'string',
                'default' => ''
            ),
            'bgSize' => array(
                'type' => 'string',
                'default' => 'cover'
            ),
            'bgPosition' => array(
                'type' => 'string',
                'default' => 'center'
            ),
            'bgRepeat' => array(
                'type' => 'string',
                'default' => 'no-repeat'
            ),
            'bgAttachment' => array(
                'type' => 'string',
                'default' => 'scroll'
            ),
            'anchor' => array(
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
            ),
            'className' => array(
                'type' => 'string',
                'default' => ''
            )
        )
    ));
}
add_action('init', 'bootstrap_theme_register_bs_container_block');
