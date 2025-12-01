<?php
/**
 * Check if a page-specific option is set (not null/empty)
 */
function bootstrap_theme_is_page_option_set($option_name, $post_id = null) {
	if (!$post_id) {
		$post_id = get_the_ID();
	}
	if (!$post_id) {
		return false;
	}
	$value = get_field($option_name, $post_id);
	return $value !== null && $value !== '';
}

/**
 * Helper functions for page-specific options
 * 
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Get page-specific option with fallback to global options
 */
function bootstrap_theme_get_page_option($option_name, $post_id = null) {
	if (!$post_id) {
		$post_id = get_the_ID();
	}
	
	if (!$post_id) {
		return '';
	}
	
	// Get page-specific option
	$page_value = get_field($option_name, $post_id);
	
	// Return page value if it exists and is not empty string
	if ($page_value !== '' && $page_value !== null) {
		return $page_value;
	}
	
	// Fallback to global option based on option name
	switch($option_name) {
		case 'page_show_title':
			// Global fallback respects theme option; default true if unset
			$global = bootstrap_theme_get_option('show_page_titles');
			return $global === '' ? true : (bool) $global;
		case 'page_show_breadcrumbs':
			return bootstrap_theme_get_option('enable_breadcrumbs');
		case 'page_show_sidebar':
			return bootstrap_theme_get_option('show_sidebar');
		case 'page_show_meta_date':
			$global_date = bootstrap_theme_get_option('show_meta_date');
			return $global_date === '' ? true : (bool) $global_date;
		case 'page_show_meta_author':
			$global_author = bootstrap_theme_get_option('show_meta_author');
			return $global_author === '' ? true : (bool) $global_author;
		case 'page_container_width':
			return bootstrap_theme_get_option('container_width');
		case 'page_header_style':
			return get_field('header_style', 'option');
		case 'page_full_width':
			return false; // Default not full width
		case 'header_style':
			// For backward compatibility and direct header_style calls
			$page_header = get_field('page_header_style', $post_id);
			if ($page_header !== '' && $page_header !== null) {
				return $page_header;
			}
			// Get from customization options instead of general options
			return get_field('header_style', 'option');
		default:
			return '';
	}
}

/**
 * Check if current page should show title
 */
function bootstrap_theme_should_show_page_title() {
	$post_id = get_the_ID();
	if ($post_id) {
		$override = get_field('page_override', $post_id);
		if ($override) {
			$page_value = get_field('page_show_title', $post_id);
			// Si override está activo, usar el valor de la página (incluso si es false/0)
			if ($page_value !== null && $page_value !== '') {
				return (bool)$page_value;
			}
			// Si override está activo pero el campo está vacío, usar el default del campo (true)
			return true;
		}
	}
	// Si no hay override o no hay post_id, usar configuración global
	$global = get_field('show_page_titles', 'option');
	return $global === '' ? true : (bool)$global;
}

/**
 * Check if current page should show breadcrumbs
 */
function bootstrap_theme_should_show_breadcrumbs() {
	$post_id = get_the_ID();
	if ($post_id) {
		$override = get_field('page_override', $post_id);
		if ($override) {
			$page_value = get_field('page_show_breadcrumbs', $post_id);
			if ($page_value !== null && $page_value !== '') {
				return (bool)$page_value;
			}
			return true;
		}
	}
	$global = get_field('enable_breadcrumbs', 'option');
	return $global === '' ? true : (bool)$global;
}

/**
 * Check if current page should show sidebar
 */
function bootstrap_theme_should_show_sidebar() {
	$post_id = get_the_ID();
	if ($post_id) {
		$override = get_field('page_override', $post_id);
		if ($override) {
			$page_value = get_field('page_show_sidebar', $post_id);
			if ($page_value !== null && $page_value !== '') {
				return (bool)$page_value;
			}
			return true;
		}
	}
	$global = get_field('show_sidebar', 'option');
	return $global === '' ? true : (bool)$global;
}

/**
 * Check if meta date should be shown
 */
function bootstrap_theme_should_show_meta_date() {
	$post_id = get_the_ID();
	if ($post_id) {
		$override = get_field('page_override', $post_id);
		if ($override) {
			$page_value = get_field('page_show_meta_date', $post_id);
			if ($page_value !== null && $page_value !== '') {
				return (bool)$page_value;
			}
			return true;
		}
	}
	$global = get_field('show_meta_date', 'option');
	return $global === '' ? true : (bool)$global;
}

/**
 * Check if meta author should be shown
 */
function bootstrap_theme_should_show_meta_author() {
	$post_id = get_the_ID();
	if ($post_id) {
		$override = get_field('page_override', $post_id);
		if ($override) {
			$page_value = get_field('page_show_meta_author', $post_id);
			if ($page_value !== null && $page_value !== '') {
				return (bool)$page_value;
			}
			return true;
		}
	}
	$global = get_field('show_meta_author', 'option');
	return $global === '' ? true : (bool)$global;
}

/**
 * Get page title (custom or default)
 */
function bootstrap_theme_get_page_title() {
	$custom_title = bootstrap_theme_get_page_option('page_custom_title');
	
	if (!empty($custom_title)) {
		return $custom_title;
	}
	
	// Fallback to default title
	if (is_home()) {
		return get_bloginfo('name');
	} elseif (is_archive()) {
		return get_the_archive_title();
	} elseif (is_search()) {
		return sprintf(__('Search Results for: %s', 'bootstrap-theme'), get_search_query());
	} elseif (is_404()) {
		return __('Page Not Found', 'bootstrap-theme');
	} else {
		return get_the_title();
	}
}

/**
 * Get container width for current page
 */
function bootstrap_theme_get_page_container_width() {
	$page_width = bootstrap_theme_get_page_option('page_container_width');
	
	if (!empty($page_width)) {
		return $page_width;
	}
	
	return bootstrap_theme_get_option('container_width');
}

/**
 * Check if current page is full width
 */
function bootstrap_theme_is_page_full_width() {
	return bootstrap_theme_get_page_option('page_full_width');
}

/**
 * Generate page-specific classes
 */
function bootstrap_theme_get_page_classes() {
	$classes = array();
	
	// Header style class
	$header_style = bootstrap_theme_get_customization_option('header_style');
	if ($header_style && $header_style !== 'default') {
		$classes[] = 'header-' . $header_style;
	}
	
	// Full width class
	if (bootstrap_theme_is_page_full_width()) {
		$classes[] = 'page-full-width';
	}
	
	// No sidebar class
	if (!bootstrap_theme_should_show_sidebar()) {
		$classes[] = 'no-sidebar';
	}
	
	return implode(' ', $classes);
}

/**
 * Render breadcrumbs with custom styling
 */
function bootstrap_theme_render_breadcrumbs() {
	if (!bootstrap_theme_should_show_breadcrumbs()) {
		return;
	}
	
	$breadcrumb_style = bootstrap_theme_get_customization_option('breadcrumb_style');
	$breadcrumb_class = 'breadcrumb';
	
	// Add style-specific classes
	switch($breadcrumb_style) {
		case 'breadcrumb-card':
			$breadcrumb_class .= ' breadcrumb-card';
			break;
		case 'breadcrumb-bg-light':
			$breadcrumb_class .= ' bg-light';
			break;
		case 'breadcrumb-no-background':
			$breadcrumb_class .= ' breadcrumb-no-bg';
			break;
	}
	
	echo '<nav aria-label="breadcrumb">';
	echo '<ol class="' . esc_attr($breadcrumb_class) . '">';
	
	// Home link
	echo '<li class="breadcrumb-item"><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'bootstrap-theme') . '</a></li>';
	
	if (is_category() || is_single()) {
		$category = get_the_category();
		if ($category) {
			foreach ($category as $cat) {
				echo '<li class="breadcrumb-item"><a href="' . esc_url(get_category_link($cat->term_id)) . '">' . esc_html($cat->name) . '</a></li>';
			}
		}
		
		if (is_single()) {
			echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title()) . '</li>';
		}
	} elseif (is_page()) {
		$ancestors = get_post_ancestors(get_the_ID());
		if ($ancestors) {
			$ancestors = array_reverse($ancestors);
			foreach ($ancestors as $ancestor) {
				echo '<li class="breadcrumb-item"><a href="' . esc_url(get_permalink($ancestor)) . '">' . esc_html(get_the_title($ancestor)) . '</a></li>';
			}
		}
		echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title()) . '</li>';
	} elseif (is_archive()) {
		echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_archive_title()) . '</li>';
	} elseif (is_search()) {
		echo '<li class="breadcrumb-item active" aria-current="page">' . sprintf(__('Search Results for: %s', 'bootstrap-theme'), get_search_query()) . '</li>';
	} elseif (is_404()) {
		echo '<li class="breadcrumb-item active" aria-current="page">' . __('Page Not Found', 'bootstrap-theme') . '</li>';
	}
	
	echo '</ol>';
	echo '</nav>';
}