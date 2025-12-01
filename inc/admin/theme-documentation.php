<?php
/**
 * Theme Documentation Page
 * Muestra el README.md del tema en el admin
 *
 * @package Bootstrap_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Agregar página de documentación en Herramientas
 */
function bootstrap_theme_add_docs_page() {
    add_management_page(
        __('Documentación del Tema', 'bootstrap-theme'),
        __('Documentación del Tema', 'bootstrap-theme'),
        'manage_options',
        'bootstrap-theme-docs',
        'bootstrap_theme_render_documentation_page'
    );
}
add_action('admin_menu', 'bootstrap_theme_add_docs_page');

/**
 * Render Documentation Page
 */
function bootstrap_theme_render_documentation_page() {
    $readme_path = get_template_directory() . '/README.md';
    
    if (!file_exists($readme_path)) {
        echo '<div class="wrap"><h1>' . esc_html__('Documentación', 'bootstrap-theme') . '</h1>';
        echo '<div class="notice notice-error"><p>' . esc_html__('No se encontró el archivo README.md', 'bootstrap-theme') . '</p></div></div>';
        return;
    }

    $markdown = file_get_contents($readme_path);
    $html = bootstrap_theme_markdown_to_html($markdown);
    
    echo '<div class="wrap bootstrap-theme-docs">';
    echo '<h1>' . esc_html__('Bootstrap Theme - Documentación', 'bootstrap-theme') . '</h1>';
    echo '<div class="bootstrap-theme-readme-content">';
    echo wp_kses_post($html);
    echo '</div>';
    echo '</div>';
}

/**
 * Enqueue styles for documentation page
 */
function bootstrap_theme_docs_enqueue_styles($hook) {
    if ($hook !== 'configuración-del-tema_page_bootstrap-theme-docs') {
        return;
    }

    wp_enqueue_style(
        'bootstrap-theme-docs',
        get_template_directory_uri() . '/inc/admin/admin-styles.css',
        array(),
        '1.6.3'
    );
}
add_action('admin_enqueue_scripts', 'bootstrap_theme_docs_enqueue_styles');

/**
 * Markdown to HTML converter
 */
function bootstrap_theme_markdown_to_html($markdown) {
        $md = str_replace(["\r\n", "\r"], "\n", (string)$markdown);

        // Extract fenced code blocks first
        $codeblocks = [];
        $md = preg_replace_callback('/```([a-zA-Z0-9_-]+)?\n([\s\S]*?)\n```/', function($m) use (&$codeblocks) {
            $lang = isset($m[1]) ? trim($m[1]) : '';
            $code = htmlspecialchars($m[2], ENT_QUOTES, 'UTF-8');
            $id = 'THEMECODE'.count($codeblocks).'X';
            $class = $lang ? ' class="language-'.esc_attr($lang).'"' : '';
            $codeblocks[$id] = '<pre><code'.$class.'>'.$code.'</code></pre>';
            return $id;
        }, $md);

        // Split into blocks by blank lines
        $blocks = preg_split('/\n\s*\n/', trim($md));
        $html_parts = [];

        foreach ($blocks as $block) {
            $lines = explode("\n", $block);
            
            // Table detection
            if (count($lines) >= 2 && strpos($lines[0], '|') !== false && preg_match('/^\s*\|?\s*[:\-\s\|]+\s*\|?\s*$/', $lines[1])) {
                $html_parts[] = bootstrap_theme_render_table($lines);
                continue;
            }
            
            // List detection
            if (preg_match('/^\s*([*\-+]\s+|\d+\.\s+)/', $lines[0])) {
                $html_parts[] = bootstrap_theme_render_list($lines);
                continue;
            }
            
            // Blockquote detection
            if (preg_match('/^\s*>\s+/', $lines[0])) {
                $html_parts[] = bootstrap_theme_render_blockquote($lines);
                continue;
            }
            
            // Header detection
            if (preg_match('/^\s*#{1,6}\s+/', $lines[0])) {
                foreach ($lines as $ln) {
                    $html_parts[] = bootstrap_theme_render_heading($ln);
                }
                continue;
            }
            
            // Horizontal rule
            if (preg_match('/^\s*([-*_])\1{2,}\s*$/', trim($block))) {
                $html_parts[] = '<hr />';
                continue;
            }
            
            // Paragraph fallback
            $p = bootstrap_theme_render_inline(implode(' ', $lines));
            $html_parts[] = '<p>'.$p.'</p>';
        }

        $html = implode("\n", $html_parts);
        
        // Restore fenced code blocks
        foreach ($codeblocks as $k => $v) {
            $html = str_replace($k, $v, $html);
        }
        
        return $html;
}

function bootstrap_theme_render_heading($line) {
    if (!preg_match('/^(\s*#{1,6})\s+(.*)$/', $line, $m)) {
        return '<p>'.bootstrap_theme_render_inline($line).'</p>';
    }
    $level = min(6, max(1, strlen(trim($m[1]))));
    $text = bootstrap_theme_render_inline(trim($m[2]));
    return '<h'.$level.'>'.$text.'</h'.$level.'>';
}

function bootstrap_theme_render_list($lines) {
    $isOrdered = preg_match('/^\s*\d+\.\s+/', $lines[0]);
    $tag = $isOrdered ? 'ol' : 'ul';
    $items = [];
    
    foreach ($lines as $ln) {
        if (preg_match('/^\s*(?:[*\-+]\s+|\d+\.\s+)(.+)$/', $ln, $m)) {
            $items[] = '<li>'.bootstrap_theme_render_inline(trim($m[1])).'</li>';
        }
    }
    
    return '<'.$tag.'>'.implode('', $items).'</'.$tag.'>';
}

function bootstrap_theme_render_blockquote($lines) {
    $text = [];
    foreach ($lines as $ln) {
        if (preg_match('/^\s*>\s+(.*)$/', $ln, $m)) {
            $text[] = bootstrap_theme_render_inline(trim($m[1]));
        }
    }
    return '<blockquote><p>'.implode(' ', $text).'</p></blockquote>';
}

function bootstrap_theme_render_table($lines) {
    $header = array_shift($lines);
    array_shift($lines); // separator
    
    $cols = array_map('trim', explode('|', trim($header, '|')));
    $cols = array_map('bootstrap_theme_render_inline', $cols);
    
    $thead = '<thead><tr>';
    foreach ($cols as $c) {
        $thead .= '<th>'.$c.'</th>';
    }
    $thead .= '</tr></thead>';
    
    $tbody = '<tbody>';
    foreach ($lines as $ln) {
        $cells = array_map('trim', explode('|', trim($ln, '|')));
        $cells = array_map('bootstrap_theme_render_inline', $cells);
        $tbody .= '<tr>';
        foreach ($cells as $cell) {
            $tbody .= '<td>'.$cell.'</td>';
        }
        $tbody .= '</tr>';
    }
    $tbody .= '</tbody>';
    
    return '<table class="widefat striped">'.$thead.$tbody.'</table>';
}

function bootstrap_theme_render_inline($text) {
    // Bold
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/__(.+?)__/', '<strong>$1</strong>', $text);
    
    // Italic
    $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
    $text = preg_replace('/_(.+?)_/', '<em>$1</em>', $text);
    
    // Inline code - escapar HTML para evitar que tags se interpreten
    $text = preg_replace_callback('/`([^`]+)`/', function($m) {
        return '<code>' . htmlspecialchars($m[1], ENT_QUOTES, 'UTF-8') . '</code>';
    }, $text);
    
    // Links
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank">$1</a>', $text);
    
    // Autolinks
    $text = preg_replace('/<(https?:\/\/[^>]+)>/', '<a href="$1" target="_blank">$1</a>', $text);
    
    return $text;
}
