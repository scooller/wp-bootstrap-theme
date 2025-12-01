<?php
/**
 * Hero section template part
 *
 * @package Bootstrap_Theme
 */

// Verificar si se debe mostrar el hero
$show_hero = bootstrap_theme_get_option('show_hero');

if ( ! $show_hero ) {
	return;
}

// Obtener configuraciones del hero
$hero_title = bootstrap_theme_get_option('hero_title');
$hero_subtitle = bootstrap_theme_get_option('hero_subtitle');
$hero_button_text = bootstrap_theme_get_option('hero_button_text');
$hero_button_url = bootstrap_theme_get_option('hero_button_url');
$hero_button_style = bootstrap_theme_get_option('hero_button_style');
$hero_image = bootstrap_theme_get_option('hero_image');
$hero_text_color = bootstrap_theme_get_option('hero_text_color');
$hero_background_type = bootstrap_theme_get_option('hero_background_type');
$hero_background_color = bootstrap_theme_get_option('hero_background_color');
$hero_background_image = bootstrap_theme_get_option('hero_background_image');

// Preparar estilos del hero
$hero_styles = array();
$hero_classes = array( 'hero-section', 'py-5' );

// Configurar fondo
switch ( $hero_background_type ) {
	case 'color':
		$hero_styles[] = 'background-color: ' . esc_attr( $hero_background_color );
		break;
	case 'gradient':
        $hero_styles[] = 'background: linear-gradient(135deg, ' . esc_attr( $hero_background_color ) . ' 0%, ' . esc_attr( bootstrap_theme_adjust_brightness( $hero_background_color, -30 ) ) . ' 100%)';
		break;
	case 'image':
		if ( $hero_background_image && isset( $hero_background_image['url'] ) ) {
			$hero_styles[] = 'background-image: url(' . esc_url( $hero_background_image['url'] ) . ')';
			$hero_styles[] = 'background-size: cover';
			$hero_styles[] = 'background-position: center';
			$hero_styles[] = 'background-repeat: no-repeat';
		}
		break;
}

// Configurar color del texto
if ( $hero_text_color ) {
	$hero_styles[] = 'color: ' . esc_attr( $hero_text_color );
	
	// Agregar clase de texto según el color
    $brightness = bootstrap_theme_hex_brightness( $hero_text_color );
	if ( $brightness > 128 ) {
		$hero_classes[] = 'text-dark';
	} else {
		$hero_classes[] = 'text-white';
	}
}

$hero_style_attr = ! empty( $hero_styles ) ? 'style="' . implode( '; ', $hero_styles ) . '"' : '';
?>

<section class="<?php echo esc_attr( implode( ' ', $hero_classes ) ); ?>" <?php echo $hero_style_attr; ?>>
    <div class="<?php echo esc_attr( bootstrap_theme_get_option('container_width') ); ?>">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <?php if ( ! empty( $hero_title ) ) : ?>
                <h1 class="display-4 fw-bold mb-4">
                    <?php echo esc_html( $hero_title ); ?>
                </h1>
                <?php endif; ?>
                
                <?php if ( ! empty( $hero_subtitle ) ) : ?>
                <p class="lead mb-4">
                    <?php echo wp_kses_post( $hero_subtitle ); ?>
                </p>
                <?php endif; ?>
                
                <div class="hero-buttons">
                    <?php if ( ! empty( $hero_button_text ) && ! empty( $hero_button_url ) ) : ?>
                        <a href="<?php echo esc_url( $hero_button_url ); ?>" class="btn <?php echo esc_attr( $hero_button_style ); ?> btn-lg me-3">
                            <?php echo esc_html( $hero_button_text ); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>
                            <?php esc_html_e( 'Comprar Ahora', 'bootstrap-theme' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center">
                    <?php if ( $hero_image && isset( $hero_image['url'] ) ) : ?>
                        <img src="<?php echo esc_url( $hero_image['url'] ); ?>" 
                             alt="<?php echo esc_attr( $hero_image['alt'] ?? __( 'Imagen Hero', 'bootstrap-theme' ) ); ?>" 
                             class="img-fluid rounded">
                    <?php else : ?>
                        <div class="placeholder-image bg-light rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                            <i class="fas fa-image fa-5x text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php ?>
