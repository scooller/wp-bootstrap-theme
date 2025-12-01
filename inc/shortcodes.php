<?php
/**
 * Theme shortcodes
 *
 * @package Bootstrap_Theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * [bs_progress] shortcode
 * 
 * Usage examples:
 *  [bs_progress value="45" label="45%" color="success" striped="true" animated="true"]
 *  [bs_progress value="30" min="0" max="200" color="warning" show_label="false"]
 *
 * Attributes:
 *  - value (number)      Current value. Default: 0
 *  - min (number)        Minimum value. Default: 0
 *  - max (number)        Maximum value. Default: 100
 *  - label (string)      Text label inside the bar. Default: "{percent}%"
 *  - show_label (bool)   Whether to show label text. Default: true
 *  - color (string)      Bootstrap color: primary, secondary, success, danger, warning, info, light, dark. Default: primary
 *  - striped (bool)      Adds progress-bar-striped. Default: false
 *  - animated (bool)     Adds progress-bar-striped + progress-bar-animated. Default: false
 */
function bootstrap_theme_shortcode_progressbar($atts = array(), $content = null) {
    $atts = shortcode_atts(array(
        'value'       => 0,
        'min'         => 0,
        'max'         => 100,
        'label'       => '',
        'show_label'  => 'true',
        'color'       => 'primary',
        'striped'     => 'false',
        'animated'    => 'false',
    ), $atts, 'bs_progress');

    // Sanitize numbers
    $min   = is_numeric($atts['min']) ? (float) $atts['min'] : 0;
    $max   = is_numeric($atts['max']) ? (float) $atts['max'] : 100;
    $value = is_numeric($atts['value']) ? (float) $atts['value'] : 0;

    if ($max <= $min) {
        $max = $min + 1; // avoid division by zero
    }

    // Clamp value
    if ($value < $min) $value = $min;
    if ($value > $max) $value = $max;

    $percent = (($value - $min) / ($max - $min)) * 100.0;
    if ($percent < 0) $percent = 0; 
    if ($percent > 100) $percent = 100;

    // Flags
    $show_label = filter_var($atts['show_label'], FILTER_VALIDATE_BOOLEAN);
    $striped    = filter_var($atts['striped'], FILTER_VALIDATE_BOOLEAN);
    $animated   = filter_var($atts['animated'], FILTER_VALIDATE_BOOLEAN);

    // Allowed colors
    $allowed_colors = array('primary','secondary','success','danger','warning','info','light','dark');
    $color = in_array($atts['color'], $allowed_colors, true) ? $atts['color'] : 'primary';

    // Classes
    $bar_classes = array('progress-bar', 'bg-' . $color);
    if ($striped || $animated) {
        $bar_classes[] = 'progress-bar-striped';
    }
    if ($animated) {
        $bar_classes[] = 'progress-bar-animated';
    }

    // Label
    $label = $atts['label'] !== '' ? $atts['label'] : sprintf('%d%%', round($percent));

    ob_start();
    ?>
    <div class="progress">
        <div class="<?php echo esc_attr(implode(' ', $bar_classes)); ?>" role="progressbar"
             style="width: <?php echo esc_attr(number_format($percent, 2, '.', '')); ?>%;"
             aria-valuenow="<?php echo esc_attr($value); ?>" aria-valuemin="<?php echo esc_attr($min); ?>" aria-valuemax="<?php echo esc_attr($max); ?>">
            <?php if ($show_label) : ?>
                <?php echo esc_html($label); ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('bs_progress', 'bootstrap_theme_shortcode_progressbar');

/**
 * [bs_alert] shortcode
 * 
 * Usage examples:
 *  [bs_alert type="success"]Contenido de éxito[/bs_alert]
 *  [bs_alert type="warning" dismissible="true"]Aviso con cerrar[/bs_alert]
 *
 * Attributes:
 *  - type (string)       primary|secondary|success|danger|warning|info|light|dark. Default: primary
 *  - dismissible (bool)  Agrega botón cerrar y clases fade/show. Default: false
 *  - class (string)      Clases extra para el contenedor
 */
function bootstrap_theme_shortcode_alert($atts = array(), $content = null) {
    $atts = shortcode_atts(array(
        'type'        => 'primary',
        'dismissible' => 'false',
        'class'       => '',
    ), $atts, 'bs_alert');

    $allowed = array('primary','secondary','success','danger','warning','info','light','dark');
    $type = in_array($atts['type'], $allowed, true) ? $atts['type'] : 'primary';
    $dismissible = filter_var($atts['dismissible'], FILTER_VALIDATE_BOOLEAN);
    $extra_class = trim($atts['class']);

    $classes = array('alert', 'alert-' . $type);
    if ($dismissible) {
        $classes[] = 'alert-dismissible';
        $classes[] = 'fade';
        $classes[] = 'show';
    }
    if ($extra_class !== '') {
        $classes[] = $extra_class;
    }

    // Procesar contenido con shortcodes y permitir HTML controlado
    $inner = do_shortcode($content);

    ob_start();
    ?>
    <div class="<?php echo esc_attr(implode(' ', $classes)); ?>" role="alert">
        <?php echo $inner; // contenido puede contener shortcodes/HTML controlado ?>
        <?php if ($dismissible) : ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php esc_attr_e('Cerrar', 'bootstrap-theme'); ?>"></button>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('bs_alert', 'bootstrap_theme_shortcode_alert');

/**
 * [bs_button] shortcode
 * 
 * Usage examples:
 *  [bs_button text="Comprar" href="/tienda" type="primary" size="lg"]
 *  [bs_button href="https://example.com" outline="true" target="_blank"]Ir al sitio[/bs_button]
 *
 * Attributes:
 *  - href (string)       URL; si se omite, renderiza <button>
 *  - text (string)       Texto del botón; si no se define, usa el contenido
 *  - type (string)       primary|secondary|success|danger|warning|info|light|dark|link. Default: primary
 *  - outline (bool)      Usa variante outline. Default: false
 *  - size (string)       sm|lg|''
 *  - block (bool)        Ancho completo (w-100). Default: false
 *  - disabled (bool)     Deshabilitado. Default: false
 *  - target (string)     _self|_blank. Default: _self
 *  - class (string)      Clases extra
 *  - tag (string)        a|button. Por defecto auto: 'a' si href, si no 'button'
 */
function bootstrap_theme_shortcode_button($atts = array(), $content = null) {
    $atts = shortcode_atts(array(
        'href'     => '',
        'text'     => '',
        'type'     => 'primary',
        'outline'  => 'false',
        'size'     => '',
        'block'    => 'false',
        'disabled' => 'false',
        'target'   => '_self',
        'class'    => '',
        'tag'      => '',
    ), $atts, 'bs_button');

    $allowed_types = array('primary','secondary','success','danger','warning','info','light','dark','link');
    $type = in_array($atts['type'], $allowed_types, true) ? $atts['type'] : 'primary';
    $outline = filter_var($atts['outline'], FILTER_VALIDATE_BOOLEAN);
    $block = filter_var($atts['block'], FILTER_VALIDATE_BOOLEAN);
    $disabled = filter_var($atts['disabled'], FILTER_VALIDATE_BOOLEAN);
    $size = in_array($atts['size'], array('sm','lg',''), true) ? $atts['size'] : '';
    $extra_class = trim($atts['class']);

    $variant = $outline ? ('btn-outline-' . $type) : ('btn-' . $type);

    $classes = array('btn', $variant);
    if ($size) {
        $classes[] = 'btn-' . $size;
    }
    if ($block) {
        $classes[] = 'w-100';
    }
    if ($disabled) {
        $classes[] = 'disabled';
    }
    if ($extra_class !== '') {
        $classes[] = $extra_class;
    }

    $href = trim($atts['href']);
    $tag = $atts['tag'] !== '' ? strtolower($atts['tag']) : ($href !== '' ? 'a' : 'button');
    if (!in_array($tag, array('a','button'), true)) {
        $tag = $href !== '' ? 'a' : 'button';
    }

    $label = $atts['text'] !== '' ? $atts['text'] : (is_null($content) ? '' : $content);

    // target y rel
    $target = $atts['target'] === '_blank' ? '_blank' : '_self';
    $rel = $target === '_blank' ? 'noopener noreferrer' : '';

    ob_start();
    if ($tag === 'a') : ?>
        <a class="<?php echo esc_attr(implode(' ', $classes)); ?>" href="<?php echo esc_url($href); ?>" target="<?php echo esc_attr($target); ?>"<?php echo $rel ? ' rel="' . esc_attr($rel) . '"' : ''; ?> role="button"<?php echo $disabled ? ' aria-disabled="true" tabindex="-1"' : ''; ?>>
            <?php echo esc_html($label); ?>
        </a>
    <?php else : ?>
        <button type="button" class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php echo $disabled ? ' disabled aria-disabled="true"' : ''; ?>>
            <?php echo esc_html($label); ?>
        </button>
    <?php endif; 
    return ob_get_clean();
}
add_shortcode('bs_button', 'bootstrap_theme_shortcode_button');

/**
 * [bootstrap_newsletter] shortcode - moved here for centralization
 */
add_shortcode('bootstrap_newsletter', 'bootstrap_theme_newsletter_shortcode');

function bootstrap_theme_newsletter_shortcode($atts) {
    ob_start();
    ?>
    <form class="newsletter row" method="post" action="">
        <div class="col-md-5">
            <div class="row mb-3">
                <div class="col-auto">
                    <label for="newsletter_name" class="form-label"><?php esc_html_e('Nombre', 'bootstrap-theme'); ?></label>
                </div>
                <div class="col-auto">
                    <input type="text" class="form-control" id="newsletter_name" name="newsletter_name" required>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="row mb-3">
                <div class="col-auto">
                    <label for="newsletter_email" class="form-label"><?php esc_html_e('Email', 'bootstrap-theme'); ?></label>
                </div>
                <div class="col-auto">
                    <input type="email" class="form-control" id="newsletter_email" name="newsletter_email" required>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-12 d-flex">
            <button type="submit" class="btn btn-primary align-self-end mb-3" name="newsletter_submit"><?php esc_html_e('Suscribirse', 'bootstrap-theme'); ?></button>
        </div>
    </form>
    <?php
    // Procesar suscripción
    if (isset($_POST['newsletter_submit'])) {
        $name = isset($_POST['newsletter_name']) ? sanitize_text_field($_POST['newsletter_name']) : '';
        $email = isset($_POST['newsletter_email']) ? sanitize_email($_POST['newsletter_email']) : '';
        if (is_email($email)) {
            global $wpdb;
            $table = $wpdb->prefix . 'bootstrap_newsletter_subscribers';
            $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE email = %s", $email));
            if (!$exists) {
                $wpdb->insert($table, [
                    'email' => $email,
                    'name' => $name,
                    'subscribed_at' => current_time('mysql')
                ]);
                // Enviar email de confirmación
                $sender = function_exists('get_field') ? get_field('newsletter_sender_name', 'option') : get_bloginfo('name');
                $reply_to = function_exists('get_field') ? get_field('newsletter_reply_to', 'option') : get_bloginfo('admin_email');
                $success_message = function_exists('get_field') ? get_field('newsletter_success_message', 'option') : esc_html__('¡Gracias por suscribirte!', 'bootstrap-theme');
                $newsletter_html = function_exists('get_field') ? get_field('newsletter_custom_html', 'option') : $success_message;
                $headers = [
                    'From: ' . $sender . ' <' . $reply_to . '>',
                    'Reply-To: ' . $reply_to
                ];
                wp_mail($email, esc_html__('¡Gracias por suscribirte!', 'bootstrap-theme'), $newsletter_html, $headers);
                echo '<div class="alert alert-success mt-3">' . esc_html($success_message) . '</div>';
            } else {
                echo '<div class="alert alert-warning mt-3">' . esc_html__('Ya estás suscrito.', 'bootstrap-theme') . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger mt-3">' . esc_html__('Email inválido.', 'bootstrap-theme') . '</div>';
        }
    }
    return ob_get_clean();
}
