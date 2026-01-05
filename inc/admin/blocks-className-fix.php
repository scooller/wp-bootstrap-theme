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
 * Función helper para obtener data attributes de animación AOS
 * 
 * Soporta opciones de configuración AOS:
 * - aosAnimation: tipo de animación (fade-up, fade-down, flip-left, etc)
 * - aosDelay: delay en ms (0-3000)
 * - aosDuration: duración en ms (100-3000)
 * - aosEasing: easing function (linear, ease-in-quad, ease-out-quad, etc)
 * - aosOnce: animar solo una vez (true/false)
 * - aosMirror: repetir cuando se scrollea hacia arriba (true/false)
 * - aosAnchorPlacement: posición del anclaje (top-bottom, center-bottom, bottom-center, etc)
 * 
 * @param array $attributes Atributos del bloque
 * @param WP_Block $block Objeto del bloque
 * @return string String con data attributes HTML
 */
function bootstrap_theme_get_animation_attributes($attributes, $block) {
    $data_attrs = '';
    
    // Verificar si hay animación AOS
    $aosAnimation = $attributes['aosAnimation'] ?? ($block->attributes['aosAnimation'] ?? '');
    if (!empty($aosAnimation)) {
        $data_attrs .= ' data-aos="' . esc_attr($aosAnimation) . '"';
        
        // Delay en milisegundos
        $aosDelay = $attributes['aosDelay'] ?? ($block->attributes['aosDelay'] ?? '');
        if (!empty($aosDelay) && $aosDelay > 0) {
            $data_attrs .= ' data-aos-delay="' . esc_attr($aosDelay) . '"';
        }
        
        // Duration en milisegundos
        $aosDuration = $attributes['aosDuration'] ?? ($block->attributes['aosDuration'] ?? '');
        if (!empty($aosDuration) && $aosDuration > 0) {
            $data_attrs .= ' data-aos-duration="' . esc_attr($aosDuration) . '"';
        }
        
        // Easing
        $aosEasing = $attributes['aosEasing'] ?? ($block->attributes['aosEasing'] ?? '');
        if (!empty($aosEasing)) {
            $data_attrs .= ' data-aos-easing="' . esc_attr($aosEasing) . '"';
        }
        
        // Once (animar una sola vez)
        $aosOnce = $attributes['aosOnce'] ?? ($block->attributes['aosOnce'] ?? false);
        if ($aosOnce) {
            $data_attrs .= ' data-aos-once="true"';
        }
        
        // Mirror (repetir en scroll hacia arriba)
        $aosMirror = $attributes['aosMirror'] ?? ($block->attributes['aosMirror'] ?? true);
        if ($aosMirror) {
            $data_attrs .= ' data-aos-mirror="true"';
        } else {
            $data_attrs .= ' data-aos-mirror="false"';
        }
        
        // Anchor Placement
        $aosAnchorPlacement = $attributes['aosAnchorPlacement'] ?? ($block->attributes['aosAnchorPlacement'] ?? '');
        if (!empty($aosAnchorPlacement)) {
            $data_attrs .= ' data-aos-anchor-placement="' . esc_attr($aosAnchorPlacement) . '"';
        }
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