<?php
/**
 * Template Helper Functions for Theme Options
 * 
 * Funciones para insertar automáticamente los scripts y estilos
 * configurados en las opciones del tema
 * 
 * @package Bootstrap_Theme
 */
// Insertar Google Analytics en el head
function bootstrap_theme_insert_google_analytics() {
	$ga_id = bootstrap_theme_get_extra_option('google_analytics_id');

	if ( ! empty( $ga_id ) && ! is_admin() && ! current_user_can( 'manage_options' ) ) {
		// Google Analytics 4
		if ( strpos( $ga_id, 'G-' ) === 0 ) {
			?>
			<!-- Google Analytics 4 -->
			<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga_id ); ?>"></script>
			<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);} 
			gtag('js', new Date());
			gtag('config', '<?php echo esc_js( $ga_id ); ?>');
			</script>
			<?php
		}
		// Universal Analytics (legacy)
		elseif ( strpos( $ga_id, 'UA-' ) === 0 ) {
			?>
			<!-- Universal Analytics -->
			<script async src="https://www.google-analytics.com/analytics.js"></script>
			<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
			ga('create', '<?php echo esc_js( $ga_id ); ?>', 'auto');
			ga('send', 'pageview');
			</script>
			<?php
		}
	}
}
add_action( 'wp_head', 'bootstrap_theme_insert_google_analytics' );

/**
 * Insertar Google Tag Manager en el head
 */
function bootstrap_theme_insert_gtm_head() {
	$gtm_id = bootstrap_theme_get_extra_option('google_tag_manager_id');
	
	if ( ! empty( $gtm_id ) && ! is_admin() && ! current_user_can( 'manage_options' ) ) {
		?>
		<!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','<?php echo esc_js( $gtm_id ); ?>');</script>
		<!-- End Google Tag Manager -->
		<?php
	}
}
add_action( 'wp_head', 'bootstrap_theme_insert_gtm_head' );

/**
 * Insertar Google Tag Manager noscript en el body
 */
function bootstrap_theme_insert_gtm_body() {
	$gtm_id = bootstrap_theme_get_extra_option('google_tag_manager_id');
	
	if ( ! empty( $gtm_id ) && ! is_admin() && ! current_user_can( 'manage_options' ) ) {
		?>
		<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $gtm_id ); ?>"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
		<!-- End Google Tag Manager (noscript) -->
		<?php
	}
}
add_action( 'wp_body_open', 'bootstrap_theme_insert_gtm_body' );

/**
 * Insertar Facebook Pixel
 */
function bootstrap_theme_insert_facebook_pixel() {
	$pixel_id = bootstrap_theme_get_extra_option('facebook_pixel_id');
	
	if ( ! empty( $pixel_id ) && ! is_admin() && ! current_user_can( 'manage_options' ) ) {
		?>
		<!-- Facebook Pixel Code -->
		<script>
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t,s)}(window, document,'script',
		'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '<?php echo esc_js( $pixel_id ); ?>');
		fbq('track', 'PageView');
		</script>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=<?php echo esc_attr( $pixel_id ); ?>&ev=PageView&noscript=1"
		/></noscript>
		<!-- End Facebook Pixel Code -->
		<?php
	}
}
add_action( 'wp_head', 'bootstrap_theme_insert_facebook_pixel' );

/**
 * Insertar scripts personalizados en el head
 */
function bootstrap_theme_insert_custom_head_scripts() {
	$custom_scripts = bootstrap_theme_get_extra_option('custom_head_scripts');
	
	if ( ! empty( $custom_scripts ) && ! is_admin() ) {
		// Decodificar entidades para permitir < y > dentro de bloques (maneja casos doble-encoded)
		$custom_scripts_decoded = wp_specialchars_decode( $custom_scripts, ENT_QUOTES );
		$custom_scripts_decoded = html_entity_decode( $custom_scripts_decoded, ENT_QUOTES, 'UTF-8' );
		$custom_scripts_decoded = strtr( $custom_scripts_decoded, array(
			'&amp;lt;' => '<',
			'&amp;gt;' => '>',
			'&amp;amp;' => '&',
		) );
		// Imprimir directamente el contenido decodificado para evitar que '>' se convierta en &gt;
		// Si el usuario no incluye etiquetas, envolver en <script> para que se ejecute como JS
		$has_tags = ( strpos( $custom_scripts_decoded, '<script' ) !== false ) || ( strpos( $custom_scripts_decoded, '<style' ) !== false );
		if ( $has_tags ) {
			echo $custom_scripts_decoded;
		} else {
			// Por defecto, tratarlo como JavaScript en el head
			echo '<script>' . $custom_scripts_decoded . '</script>';
		}
	}
}
add_action( 'wp_head', 'bootstrap_theme_insert_custom_head_scripts', 99 );

/**
 * Insertar scripts personalizados en el footer
 */
function bootstrap_theme_insert_custom_footer_scripts() {
	$custom_scripts = bootstrap_theme_get_extra_option('custom_footer_scripts');
	
	if ( ! empty( $custom_scripts ) && ! is_admin() ) {
		// Decodificar entidades para permitir < y > dentro de bloques (maneja casos doble-encoded)
		$custom_scripts_decoded = wp_specialchars_decode( $custom_scripts, ENT_QUOTES );
		$custom_scripts_decoded = html_entity_decode( $custom_scripts_decoded, ENT_QUOTES, 'UTF-8' );
		$custom_scripts_decoded = strtr( $custom_scripts_decoded, array(
			'&amp;lt;' => '<',
			'&amp;gt;' => '>',
			'&amp;amp;' => '&',
		) );
		// Imprimir directamente el contenido decodificado para evitar que '>' se convierta en &gt;
		// Si el usuario no incluye etiquetas, envolver en <script> para que se ejecute como JS
		$has_tags = ( strpos( $custom_scripts_decoded, '<script' ) !== false ) || ( strpos( $custom_scripts_decoded, '<style' ) !== false );
		if ( $has_tags ) {
			echo $custom_scripts_decoded;
		} else {
			// Por defecto, tratarlo como JavaScript en el footer
			echo '<script>' . $custom_scripts_decoded . '</script>';
		}
	}
}
add_action( 'wp_footer', 'bootstrap_theme_insert_custom_footer_scripts', 99 );

// Eliminado: CSS personalizado desde Extras (usar "CSS adicional" de WordPress en el Personalizador)

/**
 * Insertar Google Fonts
 */
function bootstrap_theme_insert_google_fonts() {
	// Permitir URL manual si está configurada en Personalización
	$manual_url = trim( (string) bootstrap_theme_get_customization_option('google_fonts_manual') );

	// Usar la nueva función simplificada de Google Fonts como fallback
	$google_fonts_url = $manual_url !== '' ? $manual_url : bootstrap_theme_generate_simple_google_fonts_url();
	
	if ( ! empty( $google_fonts_url ) && ! is_admin() ) {
		wp_enqueue_style( 'bootstrap-theme-google-fonts', esc_url( $google_fonts_url ), array(), null );
	}
}
add_action( 'wp_enqueue_scripts', 'bootstrap_theme_insert_google_fonts', 1 );

/**
 * Aplicar estilos de personalización
 */
function bootstrap_theme_apply_customization_styles() {
	if ( is_admin() ) {
		return;
	}

	$css_vars = array();

	// Sistema completo de colores Bootstrap 5.3
	// Bootstrap usa variables CSS en :root que luego los componentes heredan
	$colors = array(
		'primary'   => array( 'default' => '#0d6efd', 'value' => bootstrap_theme_get_customization_option('primary_color') ),
		'secondary' => array( 'default' => '#6c757d', 'value' => bootstrap_theme_get_customization_option('secondary_color') ),
		'success'   => array( 'default' => '#198754', 'value' => bootstrap_theme_get_customization_option('success_color') ),
		'danger'    => array( 'default' => '#dc3545', 'value' => bootstrap_theme_get_customization_option('danger_color') ),
		'warning'   => array( 'default' => '#ffc107', 'value' => bootstrap_theme_get_customization_option('warning_color') ),
		'info'      => array( 'default' => '#0dcaf0', 'value' => bootstrap_theme_get_customization_option('info_color') ),
		'light'     => array( 'default' => '#f8f9fa', 'value' => bootstrap_theme_get_customization_option('light_color') ),
		'dark'      => array( 'default' => '#212529', 'value' => bootstrap_theme_get_customization_option('dark_color') ),
	);

	foreach ( $colors as $name => $color_data ) {
		if ( $color_data['value'] && $color_data['value'] !== $color_data['default'] ) {
			$color_hex = $color_data['value'];
			$color_rgb = bootstrap_theme_hex_to_rgb( $color_hex );
			
			// Variables principales de color (usadas por todos los componentes Bootstrap)
			$css_vars[] = '--bs-' . $name . ': ' . esc_attr( $color_hex );
			$css_vars[] = '--bs-' . $name . '-rgb: ' . $color_rgb;
			
			// Variables de estado (hover, active, subtle) que Bootstrap genera desde los colores base
			$css_vars[] = '--bs-' . $name . '-border-subtle: ' . bootstrap_theme_adjust_brightness( $color_hex, -20 );
			$css_vars[] = '--bs-' . $name . '-bg-subtle: ' . bootstrap_theme_adjust_brightness( $color_hex, 40 );
			$css_vars[] = '--bs-' . $name . '-text-emphasis: ' . bootstrap_theme_adjust_brightness( $color_hex, -30 );
		}
	}

	// Color de enlaces
	$link_color = bootstrap_theme_get_customization_option('link_color');
	if ( $link_color && $link_color !== '#0d6efd' ) {
		$css_vars[] = '--bs-link-color: ' . esc_attr( $link_color );
		$css_vars[] = '--bs-link-color-rgb: ' . bootstrap_theme_hex_to_rgb( $link_color );
		$css_vars[] = '--bs-link-hover-color: ' . bootstrap_theme_adjust_brightness( $link_color, -15 );
		$css_vars[] = '--bs-link-hover-color-rgb: ' . bootstrap_theme_hex_to_rgb( bootstrap_theme_adjust_brightness( $link_color, -15 ) );
	}

	// Color de bordes
	$border_color = bootstrap_theme_get_customization_option('border_color');
	if ( $border_color && $border_color !== '#dee2e6' ) {
		$css_vars[] = '--bs-border-color: ' . esc_attr( $border_color );
		$css_vars[] = '--bs-border-color-translucent: ' . esc_attr( $border_color ) . '40'; // 25% opacidad
	}

	// Tipografía - usar nuevos campos de Google Fonts (sin prefijo customization_)
	$body_font = bootstrap_theme_get_option('google_fonts_body');
	$heading_font = bootstrap_theme_get_option('google_fonts_headings');
	$base_font_size = bootstrap_theme_get_customization_option('base_font_size');
	// NOTA: line_height removido - usar el valor por defecto de Bootstrap

	if ( ! empty( $body_font ) ) {
		$css_vars[] = '--bs-body-font-family: "' . esc_attr( $body_font ) . '", sans-serif';
	}
	if ( ! empty( $heading_font ) && $heading_font !== 'inherit' ) {
		$css_vars[] = '--bs-heading-font-family: "' . esc_attr( $heading_font ) . '", sans-serif';
	}
	if ( $base_font_size !== 16 ) {
		$css_vars[] = '--bs-body-font-size: ' . intval( $base_font_size ) . 'px';
	}
	// NOTA: line_height CSS removido - usar Bootstrap por defecto

	// Header y Footer
	$header_bg = bootstrap_theme_get_customization_option('header_background');
	$header_text = bootstrap_theme_get_customization_option('header_text_color');
	$footer_bg = bootstrap_theme_get_customization_option('footer_background');
	$footer_text = bootstrap_theme_get_customization_option('footer_text_color');

	// Solo generar CSS si hay variables o estilos personalizados
	if ( empty( $css_vars ) && $header_bg === '#ffffff' && $header_text === '#000000' && $footer_bg === '#f8f9fa' && $footer_text === '#6c757d' && empty( $body_font ) && empty( $heading_font ) ) {
		return; // No hay personalizaciones, no generar CSS
	}

	// Iniciar buffer de CSS minificado
	$css = '<style id="bootstrap-theme-customization">';
	
	// Variables CSS en :root (compactas)
	if ( ! empty( $css_vars ) ) {
		$css .= ':root{' . implode( ';', $css_vars ) . ';}';
	}

	// Generar clases de botones solo si hay colores personalizados diferentes a los defaults
	foreach ( $colors as $name => $color_data ) {
		if ( $color_data['value'] && $color_data['value'] !== $color_data['default'] ) {
			$color_hex = $color_data['value'];
			$color_rgb = bootstrap_theme_hex_to_rgb( $color_hex );
			
			// Calcular brillo una sola vez
			$brightness = bootstrap_theme_hex_brightness( $color_hex );
			$is_light = $brightness > 128;
			$text_color = $is_light ? '#000' : '#fff';
			
			// Calcular estados (factor basado en brillo)
			$hover_factor = $is_light ? 15 : -15;
			$active_factor = $is_light ? 20 : -20;
			$hover_bg = bootstrap_theme_adjust_brightness( $color_hex, $hover_factor );
			$hover_border = bootstrap_theme_adjust_brightness( $color_hex, $is_light ? 20 : -20 );
			$active_bg = bootstrap_theme_adjust_brightness( $color_hex, $active_factor );
			$active_border = bootstrap_theme_adjust_brightness( $color_hex, $is_light ? 25 : -25 );
			
			// Botón normal (minificado)
			$css .= ".btn-$name{";
			$css .= "--bs-btn-color:$text_color;";
			$css .= "--bs-btn-bg:$color_hex;";
			$css .= "--bs-btn-border-color:$color_hex;";
			$css .= "--bs-btn-hover-color:$text_color;";
			$css .= "--bs-btn-hover-bg:$hover_bg;";
			$css .= "--bs-btn-hover-border-color:$hover_border;";
			$css .= "--bs-btn-focus-shadow-rgb:$color_rgb;";
			$css .= "--bs-btn-active-color:$text_color;";
			$css .= "--bs-btn-active-bg:$active_bg;";
			$css .= "--bs-btn-active-border-color:$active_border;";
			$css .= "--bs-btn-disabled-color:$text_color;";
			$css .= "--bs-btn-disabled-bg:$color_hex;";
			$css .= "--bs-btn-disabled-border-color:$color_hex;";
			$css .= '}';
			
			// Botón outline (minificado)
			$css .= ".btn-outline-$name{";
			$css .= "--bs-btn-color:$color_hex;";
			$css .= "--bs-btn-border-color:$color_hex;";
			$css .= "--bs-btn-hover-color:$text_color;";
			$css .= "--bs-btn-hover-bg:$color_hex;";
			$css .= "--bs-btn-hover-border-color:$color_hex;";
			$css .= "--bs-btn-focus-shadow-rgb:$color_rgb;";
			$css .= "--bs-btn-active-color:$text_color;";
			$css .= "--bs-btn-active-bg:$color_hex;";
			$css .= "--bs-btn-active-border-color:$color_hex;";
			$css .= "--bs-btn-disabled-color:$color_hex;";
			$css .= "--bs-btn-disabled-border-color:$color_hex;";
			$css .= '}';
		}
	}

	// Header styles (compactos)
	if ( $header_bg !== '#ffffff' || $header_text !== '#000000' ) {
		$css .= ".site-header,.navbar{background-color:$header_bg!important;color:$header_text;}";
		$css .= ".navbar-brand,.navbar-nav .nav-link{color:$header_text!important;}";
	}

	// Footer styles (compactos)
	if ( $footer_bg !== '#f8f9fa' || $footer_text !== '#6c757d' ) {
		$css .= ".site-footer{background-color:$footer_bg!important;color:$footer_text!important;}";
	}

	// Tipografía (compactos)
	if ( ! empty( $heading_font ) && $heading_font !== 'inherit' ) {
		$css .= 'h1,h2,h3,h4,h5,h6,.h1,.h2,.h3,.h4,.h5,.h6{font-family:var(--bs-heading-font-family);}';
	}
	if ( ! empty( $body_font ) ) {
		$css .= 'body,.btn,.form-control,.form-select{font-family:var(--bs-body-font-family);}';
	}

	// Componentes con estado activo que usan primary
	$primary_color = bootstrap_theme_get_customization_option('primary_color');
	if ( $primary_color && $primary_color !== '#0d6efd' ) {
		$primary_rgb = bootstrap_theme_hex_to_rgb( $primary_color );
		
		// List group active (sin minificar para claridad)
		$css .= '.list-group-item.active{';
		$css .= 'color:#fff;';
		$css .= "background-color:$primary_color;";
		$css .= "border-color:$primary_color;";
		$css .= '}';
		
		// Nav pills active
		$css .= '.nav-pills .nav-link.active,.nav-pills .show>.nav-link{';
		$css .= 'color:#fff;';
		$css .= "background-color:$primary_color;";
		$css .= '}';
		
		// Pagination active
		$css .= '.page-item.active .page-link{';
		$css .= 'color:#fff;';
		$css .= "background-color:$primary_color;";
		$css .= "border-color:$primary_color;";
		$css .= '}';
		
		// Progress bar
		$css .= '.progress-bar{';
		$css .= "background-color:$primary_color;";
		$css .= '}';
	}

	$css .= '</style>';
	echo $css;
}
add_action( 'wp_head', 'bootstrap_theme_apply_customization_styles', 98 );

/**
 * Aplicar meta descripción por defecto
 */
function bootstrap_theme_insert_meta_description() {
	if ( is_admin() || is_singular() ) {
		return;
	}

	$meta_description = bootstrap_theme_get_extra_option('meta_description');
	
	if ( ! empty( $meta_description ) ) {
		echo '<meta name="description" content="' . esc_attr( $meta_description ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'bootstrap_theme_insert_meta_description', 1 );

/**
 * Aplicar meta keywords
 */
function bootstrap_theme_insert_meta_keywords() {
	if ( is_admin() ) {
		return;
	}

	$meta_keywords = bootstrap_theme_get_extra_option('meta_keywords');
	
	if ( ! empty( $meta_keywords ) ) {
		echo '<meta name="keywords" content="' . esc_attr( $meta_keywords ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'bootstrap_theme_insert_meta_keywords', 1 );

/**
 * Modo mantenimiento
 */
function bootstrap_theme_maintenance_mode() {
	$maintenance_mode = bootstrap_theme_get_extra_option('maintenance_mode');
	
	if ( $maintenance_mode && ! current_user_can( 'manage_options' ) && ! is_admin() ) {
		$message = bootstrap_theme_get_extra_option('maintenance_message');
		
		wp_die( 
			wp_kses_post( $message ), 
			__( 'Sitio en Mantenimiento', 'bootstrap-theme' ), 
			array( 'response' => 503 ) 
		);
	}
}
add_action( 'template_redirect', 'bootstrap_theme_maintenance_mode' );

/**
 * Función helper para ajustar brillo de color
 */
function bootstrap_theme_adjust_brightness( $hex, $steps ) {
	// Convertir hex a RGB
	$hex = str_replace( '#', '', $hex );
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );

	// Ajustar brillo
	$r = max( 0, min( 255, $r + $steps ) );
	$g = max( 0, min( 255, $g + $steps ) );
	$b = max( 0, min( 255, $b + $steps ) );

	// Convertir de vuelta a hex
	return '#' . str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT ) . 
	              str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT ) . 
	              str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );
}

/**
 * Función helper para calcular brillo de color hex
 */
function bootstrap_theme_hex_brightness( $hex ) {
	$hex = str_replace( '#', '', $hex );
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );
	
	return ( $r * 299 + $g * 587 + $b * 114 ) / 1000;
}

/**
 * Obtener clase de contenedor según configuración
 */
function bootstrap_theme_get_container_class() {
	return bootstrap_theme_get_option('container_width');
}

/**
 * Verificar si se debe mostrar el menú de navegación
 */
function bootstrap_theme_should_show_navigation() {
	return bootstrap_theme_get_option('show_navigation_menu');
}

/**
 * Obtener lista de Google Fonts usando la API (más extensa)
 */
function bootstrap_theme_get_google_fonts() {
	// Cache las fuentes por 24 horas
	$cache_key = 'bootstrap_theme_google_fonts_extended';
	$fonts = get_transient( $cache_key );
	
	if ( false === $fonts ) {
		// API Key de Google Fonts (puede ser público para esta operación)
		$api_key = 'AIzaSyC8aOO_ALksR0amq2lBydZqhzwcP2ISRQI'; // API key pública para fuentes
		$api_url = "https://www.googleapis.com/webfonts/v1/webfonts?key={$api_key}&sort=popularity";
		
		$response = wp_remote_get( $api_url, array(
			'timeout' => 30,
			'headers' => array(
				'Accept' => 'application/json',
			),
		) );
		
		if ( is_wp_error( $response ) ) {
			// Fallback a fuentes predefinidas si la API falla
			$fonts = bootstrap_theme_get_extended_fallback_fonts();
		} else {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );
			
			if ( isset( $data['items'] ) ) {
				$fonts = array();
				// Limitar a las primeras 200 fuentes más populares
				$font_items = array_slice( $data['items'], 0, 200 );
				
				foreach ( $font_items as $font ) {
					$fonts[ $font['family'] ] = $font['family'];
				}
			} else {
				$fonts = bootstrap_theme_get_extended_fallback_fonts();
			}
		}
		
		// Guardar en cache por 24 horas
		set_transient( $cache_key, $fonts, DAY_IN_SECONDS );
	}
	
	return $fonts;
}

/**
 * Fuentes fallback extendidas si la API de Google Fonts no funciona
 */
function bootstrap_theme_get_extended_fallback_fonts() {
	return array(
		'' => '-- Usar fuente del tema --',
		'Roboto' => 'Roboto',
		'Open Sans' => 'Open Sans',
		'Lato' => 'Lato',
		'Montserrat' => 'Montserrat',
		'Source Sans Pro' => 'Source Sans Pro',
		'Raleway' => 'Raleway',
		'Poppins' => 'Poppins',
		'Nunito' => 'Nunito',
		'Ubuntu' => 'Ubuntu',
		'Playfair Display' => 'Playfair Display',
		'Merriweather' => 'Merriweather',
		'PT Sans' => 'PT Sans',
		'Noto Sans' => 'Noto Sans',
		'Work Sans' => 'Work Sans',
		'Fira Sans' => 'Fira Sans',
		'Inter' => 'Inter',
		'Dancing Script' => 'Dancing Script',
		'Lobster' => 'Lobster',
		'Pacifico' => 'Pacifico',
		'Quicksand' => 'Quicksand',
		'Oswald' => 'Oswald',
		'Source Code Pro' => 'Source Code Pro',
		'Crimson Text' => 'Crimson Text',
		'Libre Baskerville' => 'Libre Baskerville',
		'Oxygen' => 'Oxygen',
		'Slabo 27px' => 'Slabo 27px',
		'Libre Franklin' => 'Libre Franklin',
		'PT Serif' => 'PT Serif',
		'Roboto Condensed' => 'Roboto Condensed',
		'Roboto Slab' => 'Roboto Slab',
		'Mukti' => 'Mukti',
		'Titillium Web' => 'Titillium Web',
		'Arimo' => 'Arimo',
		'Karla' => 'Karla',
		'Rubik' => 'Rubik',
		'IBM Plex Sans' => 'IBM Plex Sans',
		'Barlow' => 'Barlow',
		'Manrope' => 'Manrope',
		'DM Sans' => 'DM Sans',
		'Heebo' => 'Heebo',
		'Cabin' => 'Cabin',
		'Dosis' => 'Dosis',
		'Exo 2' => 'Exo 2',
		'Jost' => 'Jost',
		'Commissioner' => 'Commissioner',
		'Red Hat Display' => 'Red Hat Display',
		'Epilogue' => 'Epilogue'
	);
}

/**
 * Generar URL de Google Fonts simplificada
 */
function bootstrap_theme_generate_simple_google_fonts_url() {
	$body_font = bootstrap_theme_get_option('google_fonts_body');
	$heading_font = bootstrap_theme_get_option('google_fonts_headings');
	// Nota: este campo NO tiene prefijo customization_ en ACF
	$weights = bootstrap_theme_get_option('google_fonts_weights');
	
	$fonts_to_load = array();
	
	// Si no hay pesos seleccionados, usar por defecto
	if ( empty( $weights ) ) {
		$weights = array( '400', '700' );
	}
	
	// Normalizar y convertir a string (separador ';' requerido por CSS2)
	if ( is_array( $weights ) ) {
		$weights = array_map( 'strval', array_map( 'trim', $weights ) );
		$weights = array_filter( $weights, 'strlen' );
		$weights_string = implode( ';', $weights );
	} else {
		$weights_string = trim( (string) $weights );
		$weights_string = str_replace( ',', ';', $weights_string );
	}
	
	// Agregar fuente del cuerpo
	if ( ! empty( $body_font ) ) {
		$fonts_to_load[ $body_font ] = $weights_string;
	}
	
	// Agregar fuente de títulos si es diferente
	if ( ! empty( $heading_font ) && $heading_font !== $body_font ) {
		$fonts_to_load[ $heading_font ] = $weights_string;
	}
	
	// Si no hay fuentes seleccionadas, retornar vacío
	if ( empty( $fonts_to_load ) ) {
		return '';
	}
	
	// Construir URL
	$families = array();
	foreach ( $fonts_to_load as $font_family => $font_weights ) {
		$family = str_replace( ' ', '+', $font_family );
		$families[] = 'family=' . $family . ':wght@' . $font_weights;
	}
	
	$base_url = 'https://fonts.googleapis.com/css2';
	$family_param = implode( '&', $families );
	$display_param = 'display=swap';
	
	return $base_url . '?' . $family_param . '&' . $display_param;
}

// Nota: Eliminado cargador legado de Google Fonts que usaba una función no definida.
// Ahora la carga se realiza exclusivamente mediante bootstrap_theme_insert_google_fonts()
// con prioridad 1, utilizando bootstrap_theme_generate_simple_google_fonts_url().

/**
 * Limpiar cache de Google Fonts (útil para desarrollo)
 */
function bootstrap_theme_clear_fonts_cache() {
	delete_transient( 'bootstrap_theme_google_fonts' );
}

/**
 * Hook para limpiar cache cuando se guarden las opciones
 */
function bootstrap_theme_clear_fonts_cache_on_save( $post_id ) {
	if ( 'acf-options' === get_post_type( $post_id ) ) {
		bootstrap_theme_clear_fonts_cache();
	}
}
add_action( 'acf/save_post', 'bootstrap_theme_clear_fonts_cache_on_save' );

/**
 * Cargar JavaScript en el admin para Google Fonts
 */
function bootstrap_theme_load_admin_scripts( $hook ) {
	// Solo cargar en páginas de opciones de ACF
	if ( strpos( $hook, 'bootstrap-theme-options' ) !== false ) {
		wp_enqueue_script(
			'bootstrap-theme-google-fonts-admin',
			get_template_directory_uri() . '/inc/admin/google-fonts-admin.js',
			array( 'jquery', 'acf-input' ),
			BOOTSTRAP_THEME_VERSION,
			true
		);

		// Localizar script con datos necesarios
		wp_localize_script( 'bootstrap-theme-google-fonts-admin', 'bootstrap_theme_admin', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'bootstrap_theme_fonts_nonce' ),
		) );
	}
}
add_action( 'admin_enqueue_scripts', 'bootstrap_theme_load_admin_scripts' );

/**
 * AJAX handler para limpiar cache de fuentes
 */
function bootstrap_theme_ajax_clear_fonts_cache() {
	check_ajax_referer( 'bootstrap_theme_fonts_nonce', 'nonce' );
	
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'No tienes permisos para realizar esta acción.', 'bootstrap-theme' ) );
	}

	bootstrap_theme_clear_fonts_cache();
	
	wp_send_json_success( array(
		'message' => __( 'Cache de Google Fonts limpiado exitosamente.', 'bootstrap-theme' )
	) );
}
add_action( 'wp_ajax_clear_google_fonts_cache', 'bootstrap_theme_ajax_clear_fonts_cache' );

/**
 * Modificar la función de carga de Google Fonts para usar tanto selector como manual
 */
function bootstrap_theme_load_google_fonts_updated() {
	// Esta función está deshabilitada - bootstrap_theme_insert_google_fonts() con prioridad 1 maneja todo
	return;
}

// NOTA: No cargar funciones duplicadas - bootstrap_theme_insert_google_fonts() ya maneja Google Fonts
