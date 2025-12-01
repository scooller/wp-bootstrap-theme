<?php
/**
 * Helper para corregir el manejo de clases CSS adicionales en bloques Bootstrap
 * 
 * Este archivo debe ser ejecutado manualmente para aplicar correcciones a todos
 * los bloques que no manejen correctamente las clases del panel "Avanzado".
 * 
 * @package Bootstrap_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Función helper para agregar soporte de className a bloques
 * 
 * @param array $classes Array de clases CSS
 * @param array $attributes Atributos del bloque
 * @param WP_Block $block Objeto del bloque
 * @return array Array de clases con className agregado
 */
function bootstrap_theme_add_custom_classes($classes, $attributes, $block) {
    // Add custom CSS classes from Advanced panel
    if (!empty($attributes['className'])) {
        $classes[] = $attributes['className'];
    }
    
    // Alternative way to get custom classes from block object
    if (isset($block->attributes['className']) && !empty($block->attributes['className'])) {
        $classes[] = $block->attributes['className'];
    }
    
    return array_unique($classes);
}

/**
 * Función helper para obtener data attributes de animación
 * 
 * @param array $attributes Atributos del bloque
 * @param WP_Block $block Objeto del bloque
 * @return string String con data attributes HTML
 */
function bootstrap_theme_get_animation_attributes($attributes, $block) {
    $data_attrs = '';
    
    // Verificar si hay delay
    $wowDelay = $attributes['wowDelay'] ?? ($block->attributes['wowDelay'] ?? '');
    if (!empty($wowDelay)) {
        // Asegurar que tenga 's' al final
        $delay = strpos($wowDelay, 's') !== false ? $wowDelay : $wowDelay . 's';
        $data_attrs .= ' data-wow-delay="' . esc_attr($delay) . '"';
    }
    
    // Verificar si hay duration
    $wowDuration = $attributes['wowDuration'] ?? ($block->attributes['wowDuration'] ?? '');
    if (!empty($wowDuration)) {
        // Asegurar que tenga 's' al final
        $duration = strpos($wowDuration, 's') !== false ? $wowDuration : $wowDuration . 's';
        $data_attrs .= ' data-wow-duration="' . esc_attr($duration) . '"';
    }
    
    return $data_attrs;
}

/**
 * Lista de bloques que necesitan el atributo className agregado
 * Estos bloques ya fueron corregidos manualmente:
 * - bs-row ✅
 * - bs-column ✅ 
 * - bs-container ✅
 * - bs-accordion ✅
 * - bs-alert ✅
 * - bs-badge ✅
 * - bs-breadcrumb ✅
 * - bs-button ✅
 * - bs-button-group ✅
 * - bs-card ✅
 * - bs-carousel ✅
 * - bs-close-button ✅
 * - bs-collapse ✅
 * - bs-dropdown ✅
 * - bs-list-group ✅
 * - bs-modal ✅
 * - bs-navbar ✅
 * - bs-navs-tabs ✅
 * - bs-offcanvas ✅
 * - bs-pagination ✅
 * - bs-placeholders ✅
 * - bs-popover ✅
 * - bs-progress ✅
 * - bs-scrollspy ✅
 * - bs-spinner ✅
 * - bs-toast ✅
 * - bs-tooltip ✅
 */
$blocks_need_className = [
    'bs-accordion', 'bs-accordion-item', 'bs-alert', 'bs-badge', 'bs-breadcrumb',
    'bs-breadcrumb-item', 'bs-button', 'bs-button-group', 'bs-card', 'bs-carousel',
    'bs-carousel-item', 'bs-dropdown', 'bs-dropdown-divider', 'bs-dropdown-item', 
    'bs-list-group', 'bs-list-group-item', 'bs-modal', 'bs-offcanvas',
    'bs-pagination', 'bs-pagination-item', 'bs-progress', 'bs-spinner',
    'bs-tab-pane', 'bs-tabs', 'bs-toast'
];

/**
 * Instrucciones para aplicar manualmente a cada bloque:
 * 
 * 1. En la función render del bloque, agregar antes de implode():
 *    $classes = bootstrap_theme_add_custom_classes($classes, $attributes, $block);
 * 
 * 2. En el registro del bloque, agregar en attributes:
 *    'className' => array(
 *        'type' => 'string',
 *        'default' => ''
 *    )
 * 
 * 3. Cambiar implode(' ', $classes) por:
 *    implode(' ', array_unique($classes))
 */

// Lista para referencia rápida - Bloques ya corregidos:
$blocks_fixed = [
    'bs-row',      // ✅ Corregido
    'bs-column',   // ✅ Corregido  
    'bs-container', // ✅ Corregido
    'bs-accordion', // ✅ Corregido
    'bs-alert', // ✅ Corregido
    'bs-badge', // ✅ Corregido
    'bs-breadcrumb', // ✅ Corregido
    'bs-button', // ✅ Corregido
    'bs-button-group', // ✅ Corregido
    'bs-card', // ✅ Corregido
    'bs-carousel', // ✅ Corregido
    'bs-close-button', // ✅ Corregido
    'bs-collapse', // ✅ Corregido
    'bs-dropdown', // ✅ Corregido
    'bs-list-group', // ✅ Corregido
    'bs-modal', // ✅ Corregido
    'bs-navbar', // ✅ Corregido
    'bs-navs-tabs', // ✅ Corregido
    'bs-offcanvas', // ✅ Corregido
    'bs-pagination', // ✅ Corregido
    'bs-placeholders', // ✅ Corregido
    'bs-popover', // ✅ Corregido
    'bs-progress', // ✅ Corregido
    'bs-scrollspy', // ✅ Corregido
    'bs-spinner', // ✅ Corregido
    'bs-toast', // ✅ Corregido
    'bs-tooltip' // ✅ Corregido
];