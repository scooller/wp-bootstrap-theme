<?php
/**
 * Footer del tema
 * Carga un footer dinámico basado en ACF (Personalización > Estilo de Footer)
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @package Bootstrap_Theme
 */

// Obtener estilo del footer desde Personalización
$footer_style = function_exists('bootstrap_theme_get_customization_option')
    ? bootstrap_theme_get_customization_option('footer_style')
    : 'default';

// Normalizar 'default' a un template existente
if ( ! $footer_style || $footer_style === 'default' ) {
    $footer_style = 'footer-simple';
}

// Componer ruta del template
$footer_file = 'template-parts/footers/' . $footer_style;
$template_path = get_template_directory() . '/' . $footer_file . '.php';

// Fallback si el archivo no existe
if ( ! file_exists( $template_path ) ) {
    $footer_file = 'template-parts/footers/footer-simple';
}

// Incluir el template del footer (cada template contiene su propio <footer>)
get_template_part( $footer_file );
?>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
