<?php
/**
 * Template Name: Test Page
 */

get_header();
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info mt-4" role="alert">
                <h4 class="alert-heading"><?php esc_html_e('¡Tema Bootstrap Funcionando!', 'bootstrap-theme'); ?></h4>
                <p><?php esc_html_e('Si puedes ver este mensaje con estilos de Bootstrap, significa que el tema está cargando correctamente.', 'bootstrap-theme'); ?></p>
                <hr>
                <p class="mb-0"><?php esc_html_e('Los estilos CSS de Bootstrap 5.3 se están aplicando correctamente.', 'bootstrap-theme'); ?></p>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h2>Test de Elementos Bootstrap</h2>
                </div>
                <div class="card-body">
                    <p class="card-text">Este es un test de los elementos básicos de Bootstrap:</p>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5>Botones</h5>
                            <button type="button" class="btn btn-primary me-2">Primary</button>
                            <button type="button" class="btn btn-secondary me-2">Secondary</button>
                            <button type="button" class="btn btn-success">Success</button>
                        </div>
                        <div class="col-md-6">
                            <h5>Typography</h5>
                            <p class="lead">Este es un párrafo principal.</p>
                            <p>Este es un párrafo normal con <strong>texto en negrita</strong> y <em>texto en cursiva</em>.</p>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Google Fonts Test</h5>
                            <p style="font-family: var(--bs-body-font-family, 'Open Sans', sans-serif);">
                                Esta línea debería usar la fuente del cuerpo seleccionada en las opciones del tema.
                            </p>
                            <h3 style="font-family: var(--bs-heading-font-family, 'Roboto', sans-serif);">
                                Este título debería usar la fuente de títulos seleccionada
                            </h3>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    <h2>Test de Iconos FontAwesome en el Theme</h2>
                </div>
                <div class="card-body">
                    <div class="mt-4">
                    <?php
                    $svg_file = get_template_directory() . '/template-parts/svg-icons.php';
                    $svg_content = file_get_contents($svg_file);
                    preg_match_all('/<symbol[^>]+id="([^"]+)"[^>]*>/i', $svg_content, $matches);
                    $ids = $matches[1];
                    if (empty($ids)) return '<p>No se encontraron iconos SVG.</p>';
                    ?>
                    <div class="svg-icons-list" style="display:flex;flex-wrap:wrap;gap:2em;align-items:center">
                        <?php
                    foreach ($ids as $id) : ?>
                            <div style="text-align:center">
                            <svg class="icon" width="32" height="32"><use xlink:href="#<?php echo $id;?>"></use></svg><br>
                            <code><?php echo $id;?></code>
                            </div>
                    <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Iconos y Shortcodes -->
            <div class="mt-5 mb-5">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h2 class="mb-0">
                            <svg class="icon me-2">
                                <use xlink:href="#fa-code"></use>
                            </svg>
                            Iconos SVG FontAwesome y Shortcodes
                        </h2>
                    </div>
                    <div class="card-body">
                        <p class="lead">El tema incluye un sistema completo de iconos SVG FontAwesome optimizados y shortcodes funcionales.</p>

                        <!-- Iconos SVG FontAwesome -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h3 class="text-info border-bottom pb-2">
                                    <svg class="icon me-2">
                                        <use xlink:href="#fa-icons"></use>
                                    </svg>
                                    Sistema de Iconos SVG FontAwesome
                                </h3>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">¿Cómo usar los iconos SVG?</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> El tema incluye más de 50 iconos FontAwesome optimizados como SVG symbols para mejor performance.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Implementación Básica:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <pre><code>&lt;svg class="icon"&gt;
    &lt;use xlink:href="#fa-home"&gt;&lt;/use&gt;
&lt;/svg&gt;</code></pre>
                                        </div>
                                        <h6 class="mt-3">Clases Disponibles:</h6>
                                        <ul>
                                            <li><strong>.icon:</strong> Tamaño base (1em)</li>
                                            <li><strong>.icon-sm:</strong> Pequeño (0.875em)</li>
                                            <li><strong>.icon-lg:</strong> Grande (1.25em)</li>
                                            <li><strong>.icon-xl:</strong> Extra grande (1.5em)</li>
                                            <li><strong>.icon-2xl:</strong> Doble extra grande (2em)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Colores Bootstrap:</h6>
                                        <ul>
                                            <li><strong>.icon-primary:</strong> Color primario</li>
                                            <li><strong>.icon-success:</strong> Verde éxito</li>
                                            <li><strong>.icon-danger:</strong> Rojo peligro</li>
                                            <li><strong>.icon-warning:</strong> Amarillo advertencia</li>
                                            <li><strong>.icon-info:</strong> Azul información</li>
                                            <li><strong>.icon-secondary:</strong> Color secundario</li>
                                            <li><strong>.icon-light:</strong> Color claro</li>
                                            <li><strong>.icon-dark:</strong> Color oscuro</li>
                                        </ul>
                                        <h6 class="mt-3">Ejemplos:</h6>
                                        <div class="d-flex gap-3 flex-wrap">
                                            <svg class="icon icon-primary">
                                                <use xlink:href="#fa-home"></use>
                                            </svg>
                                            <svg class="icon icon-success icon-lg">
                                                <use xlink:href="#fa-check"></use>
                                            </svg>
                                            <svg class="icon icon-danger icon-xl">
                                                <use xlink:href="#fa-times"></use>
                                            </svg>
                                            <svg class="icon icon-warning icon-sm">
                                                <use xlink:href="#fa-exclamation-triangle"></use>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Iconos Disponibles por Categoría</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6>🧭 Navegación</h6>
                                        <small class="text-muted">fa-home, fa-arrow-left, fa-arrow-right, fa-bars, fa-search</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>👤 Usuario</h6>
                                        <small class="text-muted">fa-user, fa-users, fa-sign-in-alt, fa-sign-out-alt</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>🛒 E-commerce</h6>
                                        <small class="text-muted">fa-shopping-cart, fa-credit-card, fa-store, fa-gift</small>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <h6>🎵 Multimedia</h6>
                                        <small class="text-muted">fa-play, fa-pause, fa-video, fa-image, fa-music</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>⚙️ Sistema</h6>
                                        <small class="text-muted">fa-cog, fa-wrench, fa-download, fa-upload, fa-sync</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>📄 Documentos</h6>
                                        <small class="text-muted">fa-file, fa-file-pdf, fa-file-word, fa-print</small>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <h6>🎮 Juegos</h6>
                                        <small class="text-muted">fa-gamepad, fa-trophy, fa-star, fa-dice</small>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>🚗 Automóviles</h6>
                                        <small class="text-muted">fa-car, fa-truck, fa-gas-pump, fa-tools</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Cómo Agregar Nuevos Iconos</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Proceso para agregar iconos FontAwesome:</strong></p>
                                <ol>
                                    <li>Ir a <a href="https://fontawesome.com/icons" target="_blank">FontAwesome Icons</a></li>
                                    <li>Buscar y seleccionar el icono deseado</li>
                                    <li>Copiar el path SVG del icono</li>
                                    <li>Editar <code>template-parts/svg-icons.php</code></li>
                                    <li>Agregar el symbol con ID <code>fa-nombre-icono</code></li>
                                </ol>
                                <div class="bg-light p-3 rounded mt-3">
                                    <pre><code>&lt;symbol id="fa-nuevo-icono" viewBox="0 0 512 512"&gt;
    &lt;path d="AQUÍ_VA_EL_PATH_DEL_ICONO"/&gt;
&lt;/symbol&gt;</code></pre>
                                </div>
                            </div>
                        </div>

                        <!-- Shortcodes -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h3 class="text-success border-bottom pb-2">
                                    <svg class="icon me-2">
                                        <use xlink:href="#fa-magic"></use>
                                    </svg>
                                    Shortcodes Disponibles
                                </h3>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Newsletter Shortcode: <code>[bootstrap_newsletter]</code></h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Shortcode que genera un formulario completo de suscripción al newsletter con validación y envío automático de emails.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Características:</h6>
                                        <ul>
                                            <li>✅ <strong>Campos:</strong> Nombre y Email requeridos</li>
                                            <li>✅ <strong>Validación:</strong> Email válido y nombre no vacío</li>
                                            <li>✅ <strong>Base de datos:</strong> Almacena en tabla personalizada</li>
                                            <li>✅ <strong>Email automático:</strong> Envío de confirmación</li>
                                            <li>✅ <strong>Prevención duplicados:</strong> Verifica emails existentes</li>
                                            <li>✅ <strong>Responsive:</strong> Diseño Bootstrap completo</li>
                                            <li>✅ <strong>Internacionalización:</strong> Textos traducibles</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Uso en Editor:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <pre><code>[bootstrap_newsletter]</code></pre>
                                        </div>
                                        <h6 class="mt-3">Configuración ACF Requerida:</h6>
                                        <ul>
                                            <li><strong>newsletter_sender_name:</strong> Nombre del remitente</li>
                                            <li><strong>newsletter_reply_to:</strong> Email de respuesta</li>
                                            <li><strong>newsletter_success_message:</strong> Mensaje de éxito</li>
                                            <li><strong>newsletter_custom_html:</strong> Email HTML personalizado</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <h6>Ejemplo Visual:</h6>
                                    <div class="border p-4 bg-light rounded">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="row mb-3">
                                                    <div class="col-auto">
                                                        <label for="demo_name" class="form-label"><?php esc_html_e('Nombre', 'bootstrap-theme'); ?></label>
                                                    </div>
                                                    <div class="col-auto">
                                                        <input type="text" class="form-control" id="demo_name" disabled placeholder="Juan Pérez">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="row mb-3">
                                                    <div class="col-auto">
                                                        <label for="demo_email" class="form-label"><?php esc_html_e('Email', 'bootstrap-theme'); ?></label>
                                                    </div>
                                                    <div class="col-auto">
                                                        <input type="email" class="form-control" id="demo_email" disabled placeholder="juan@email.com">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-12 d-flex">
                                                <button type="button" class="btn btn-primary align-self-end mb-3" disabled><?php esc_html_e('Suscribirse', 'bootstrap-theme'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Funciones Helper -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h3 class="text-warning border-bottom pb-2">
                                    <svg class="icon me-2">
                                        <use xlink:href="#fa-wrench"></use>
                                    </svg>
                                    Funciones Helper y Utilidades
                                </h3>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Funciones de Opciones ACF</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Funciones helper para acceder a las opciones de Advanced Custom Fields con fallback inteligente.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Funciones Disponibles:</h6>
                                        <ul>
                                            <li><code>bootstrap_theme_get_option($field)</code></li>
                                            <li><code>bootstrap_theme_get_customization_option($field)</code></li>
                                            <li><code>bootstrap_theme_get_woocommerce_option($field)</code></li>
                                        </ul>
                                        <h6 class="mt-3">Sistema de Fallback:</h6>
                                        <ol>
                                            <li>Primero busca en opciones específicas de página</li>
                                            <li>Si no existe, recurre a opciones globales</li>
                                            <li>Si no hay valor, retorna string vacío</li>
                                        </ol>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplos de Uso:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <pre><code>// Opción general
$logo = bootstrap_theme_get_option('custom_logo');

// Opción de personalización
$primary_color = bootstrap_theme_get_customization_option('primary_color');

// Opción WooCommerce
$shop_title = bootstrap_theme_get_woocommerce_option('shop_title');</code></pre>
                                        </div>
                                        <h6 class="mt-3">Campos ACF Soportados:</h6>
                                        <ul>
                                            <li><strong>group_bootstrap_theme_general_options:</strong> Logo, navegación, footer</li>
                                            <li><strong>group_bootstrap_theme_customization:</strong> Colores, tipografía</li>
                                            <li><strong>group_bootstrap_theme_woocommerce:</strong> Configuración tienda</li>
                                            <li><strong>group_bootstrap_theme_page_options:</strong> Opciones por página</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Sistema de Logo Responsive</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Función helper para generar logos responsive con alturas configurables.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Función Disponible:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <pre><code>bootstrap_theme_get_responsive_logo($classes = 'me-2')</code></pre>
                                        </div>
                                        <h6 class="mt-3">Campos ACF Requeridos:</h6>
                                        <ul>
                                            <li><strong>custom_logo:</strong> Imagen del logo</li>
                                            <li><strong>logo_height:</strong> Altura desktop (px)</li>
                                            <li><strong>logo_mobile_height:</strong> Altura mobile (px)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Características:</h6>
                                        <ul>
                                            <li>✅ <strong>Responsive:</strong> Alturas diferentes para desktop/mobile</li>
                                            <li>✅ <strong>Automático:</strong> Ancho se ajusta proporcionalmente</li>
                                            <li>✅ <strong>CSS Injection:</strong> Estilos inline optimizados</li>
                                            <li>✅ <strong>Performance:</strong> Sin atributos width/height en HTML</li>
                                            <li>✅ <strong>Fallback:</strong> Funciona sin configuración ACF</li>
                                        </ul>
                                        <h6 class="mt-3">CSS Generado:</h6>
                                        <div class="bg-light p-2 rounded small">
                                            <code>img.logo-responsive { height: Xpx; width: auto; }</code><br>
                                            <code>@media (max-width: 767.98px) { height: Ypx; }</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5>
                                        <svg class="icon me-2">
                                            <use xlink:href="#fa-lightbulb"></use>
                                        </svg>
                                        Consejos de Uso
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>🎯 Mejores Prácticas con Iconos:</h6>
                                            <ul>
                                                <li>Usa iconos SVG en lugar de clases FontAwesome para mejor performance</li>
                                                <li>Combina clases de tamaño y color: <code>icon icon-lg icon-primary</code></li>
                                                <li>Los iconos se cargan una sola vez y se reutilizan en toda la página</li>
                                                <li>Agrega nuevos iconos siguiendo el patrón <code>fa-nombre-icono</code></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>🚀 Optimización de Shortcodes:</h6>
                                            <ul>
                                                <li>Configura todos los campos ACF antes de usar el newsletter</li>
                                                <li>Los shortcodes son procesados en el servidor, no en el editor</li>
                                                <li>Usa las funciones helper para acceso consistente a opciones</li>
                                                <li>El sistema de logo responsive mejora el Core Web Vitals</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Manual de Bloques Bootstrap -->
            <div class="mt-5 mb-5">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">
                            <svg class="icon me-2">
                                <use xlink:href="#fa-cubes"></use>
                            </svg>
                            Manual de Bloques Bootstrap - Preview y Características
                        </h2>
                    </div>
                    <div class="card-body">
                        <p class="lead">Esta sección muestra todos los bloques Gutenberg Bootstrap disponibles en el tema, con ejemplos de uso y opciones configurables.</p>

                        <!-- Layout Blocks -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h3 class="text-primary border-bottom pb-2">
                                    <svg class="icon me-2">
                                        <use xlink:href="#fa-layer-group"></use>
                                    </svg>
                                    Layout & Containers
                                </h3>
                            </div>
                        </div>

                        <!-- Container Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Container Block (bs-container)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Bloque contenedor Bootstrap con opciones de responsive design.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Tipo:</strong> container, container-fluid, container-{breakpoint}</li>
                                            <li><strong>Background:</strong> bg-primary, bg-secondary, etc.</li>
                                            <li><strong>Text Color:</strong> text-white, text-dark, etc.</li>
                                            <li><strong>Padding:</strong> p-1, p-2, p-3, p-4, p-5</li>
                                            <li><strong>Margin:</strong> m-1, m-2, m-3, m-4, m-5</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3 bg-light">
                                            <!-- wp:bootstrap-theme/bs-container {"backgroundColor":"bg-light","padding":"p-3"} -->
                                            <div class="container bg-light p-3">
                                                <p>Contenedor con fondo claro y padding.</p>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-container -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Row Block (bs-row)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Fila Bootstrap con opciones de alineación y gutters.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Alineación Vertical:</strong> align-items-start, align-items-center, align-items-end</li>
                                            <li><strong>Justificación:</strong> justify-content-start, justify-content-center, justify-content-end, etc.</li>
                                            <li><strong>Gutters:</strong> g-1, g-2, g-3, g-4, g-5</li>
                                            <li><strong>Sin Gutters:</strong> g-0</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-row {"justifyContent":"justify-content-center","alignItems":"align-items-center"} -->
                                            <div class="row justify-content-center align-items-center">
                                                <!-- wp:bootstrap-theme/bs-column {"md":"6"} -->
                                                <div class="col col-md-6">
                                                    <p>Fila centrada con columna responsive.</p>
                                                </div>
                                                <!-- /wp:bootstrap-theme/bs-column -->
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-row -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Column Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Column Block (bs-column)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Columna Bootstrap con breakpoints responsive.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Breakpoints:</strong> sm, md, lg, xl, xxl (1-12)</li>
                                            <li><strong>Auto Width:</strong> col-auto</li>
                                            <li><strong>Offset:</strong> offset-1, offset-md-2, etc.</li>
                                            <li><strong>Order:</strong> order-1, order-2, etc.</li>
                                            <li><strong>Alineación:</strong> align-self-start, align-self-center, etc.</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-column {"md":"6","lg":"4"} -->
                                            <div class="col col-md-6 col-lg-4">
                                                <p>Columna responsive: md-6, lg-4</p>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-column -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Components Blocks -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h3 class="text-success border-bottom pb-2">
                                    <svg class="icon me-2">
                                        <use xlink:href="#fa-puzzle-piece"></use>
                                    </svg>
                                    Componentes Bootstrap
                                </h3>
                            </div>
                        </div>

                        <!-- Alert Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Alert Block (bs-alert)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Componente de alerta Bootstrap con variantes de color.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Variantes:</strong> primary, secondary, success, danger, warning, info, light, dark</li>
                                            <li><strong>Dismissible:</strong> Con botón de cerrar</li>
                                            <li><strong>Título:</strong> Título opcional para la alerta</li>
                                            <li><strong>Contenido:</strong> InnerBlocks para contenido personalizado</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-alert {"variant":"success","dismissible":true,"heading":"¡Éxito!"} -->
                                            <div class="alert alert-success alert-dismissible" role="alert">
                                                <h4 class="alert-heading">¡Éxito!</h4>
                                                <p>Esta es una alerta de éxito con botón de cerrar.</p>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-alert -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Button Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Button Block (bs-button)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Botón Bootstrap con múltiples variantes y opciones.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Variantes:</strong> btn-primary, btn-secondary, etc.</li>
                                            <li><strong>Tamaños:</strong> btn-sm, btn-lg</li>
                                            <li><strong>Outline:</strong> btn-outline-primary, etc.</li>
                                            <li><strong>Estados:</strong> disabled</li>
                                            <li><strong>Enlaces:</strong> URL y target (_blank, _self)</li>
                                            <li><strong>Iconos:</strong> FontAwesome con posición (left/right)</li>
                                            <li><strong>Texto:</strong> Texto del botón</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-button {"variant":"btn-primary","text":"Botón con Icono","icon":"fa-star","iconPosition":"left"} -->
                                            <button type="button" class="btn btn-primary">
                                                <i class="fa-solid fa-star me-2"></i>
                                                Botón con Icono
                                            </button>
                                            <!-- /wp:bootstrap-theme/bs-button -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Card Block (bs-card)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Componente de tarjeta Bootstrap flexible.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Contenido:</strong> InnerBlocks para contenido personalizado</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-card -->
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Título de la Tarjeta</h5>
                                                    <p class="card-text">Contenido de la tarjeta con InnerBlocks.</p>
                                                </div>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-card -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Badge Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Badge Block (bs-badge)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Componente de insignia Bootstrap.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Contenido:</strong> InnerBlocks para contenido personalizado</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-badge -->
                                            <span class="badge bg-primary">Badge</span>
                                            <!-- /wp:bootstrap-theme/bs-badge -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Components -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h3 class="text-warning border-bottom pb-2">
                                    <svg class="icon me-2">
                                        <use xlink:href="#fa-mouse-pointer"></use>
                                    </svg>
                                    Componentes Interactivos
                                </h3>
                            </div>
                        </div>

                        <!-- Modal Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Modal Block (bs-modal)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Diálogo modal Bootstrap.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Título:</strong> Título del modal</li>
                                            <li><strong>Tamaño:</strong> modal-sm, modal-lg, modal-xl</li>
                                            <li><strong>Centrado:</strong> modal-dialog-centered</li>
                                            <li><strong>Scroll:</strong> modal-dialog-scrollable</li>
                                            <li><strong>Fondo:</strong> static (no cerrar al hacer click fuera)</li>
                                            <li><strong>Contenido:</strong> InnerBlocks para contenido personalizado</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                Abrir Modal
                                            </button>
                                            <!-- wp:bootstrap-theme/bs-modal -->
                                            <div class="modal fade" id="exampleModal" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Título del Modal</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Contenido del modal con InnerBlocks.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-modal -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Offcanvas Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Offcanvas Block (bs-offcanvas)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Panel lateral deslizante Bootstrap.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Título:</strong> Título del offcanvas</li>
                                            <li><strong>Posición:</strong> start, end, top, bottom</li>
                                            <li><strong>Fondo:</strong> Con o sin backdrop</li>
                                            <li><strong>Scroll:</strong> Permitir scroll del body</li>
                                            <li><strong>Botón:</strong> Texto y variante del botón trigger</li>
                                            <li><strong>Contenido:</strong> InnerBlocks para contenido personalizado</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-offcanvas {"title":"Panel Lateral","placement":"start"} -->
                                            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample">
                                                Abrir Offcanvas
                                            </button>
                                            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample">
                                                <div class="offcanvas-header">
                                                    <h5 class="offcanvas-title">Panel Lateral</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                                                </div>
                                                <div class="offcanvas-body">
                                                    <p>Contenido del offcanvas con InnerBlocks.</p>
                                                </div>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-offcanvas -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Collapse Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Collapse Block (bs-collapse)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Componente colapsable Bootstrap.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>ID único:</strong> Identificador del collapse</li>
                                            <li><strong>Botón:</strong> Texto y variante del botón toggle</li>
                                            <li><strong>Horizontal:</strong> collapse-horizontal</li>
                                            <li><strong>Estado inicial:</strong> Mostrar/ocultar por defecto</li>
                                            <li><strong>Contenido:</strong> InnerBlocks para contenido personalizado</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-collapse {"buttonText":"Toggle Content","show":false} -->
                                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample">
                                                Toggle Content
                                            </button>
                                            <div class="collapse" id="collapseExample">
                                                <div class="card card-body">
                                                    <p>Contenido colapsable con InnerBlocks.</p>
                                                </div>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-collapse -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Accordion Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Accordion Block (bs-accordion)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Componente de acordeón Bootstrap.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>ID único:</strong> Identificador del accordion</li>
                                            <li><strong>Items:</strong> Array de elementos con título y contenido</li>
                                            <li><strong>Expandido:</strong> Primer item expandido por defecto</li>
                                            <li><strong>Flush:</strong> Sin bordes externos</li>
                                            <li><strong>Contenido:</strong> InnerBlocks para contenido personalizado</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-accordion -->
                                            <div class="accordion" id="accordionExample">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                                            Item #1
                                                        </button>
                                                    </h2>
                                                    <div id="collapseOne" class="accordion-collapse collapse show">
                                                        <div class="accordion-body">
                                                            <p>Contenido del primer item del acordeón.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-accordion -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Components -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h3 class="text-info border-bottom pb-2">
                                    <svg class="icon me-2">
                                        <use xlink:href="#fa-compass"></use>
                                    </svg>
                                    Navegación y Menús
                                </h3>
                            </div>
                        </div>

                        <!-- Navbar Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Navbar Block (bs-navbar)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Barra de navegación Bootstrap completa.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Brand:</strong> Texto o imagen del logo</li>
                                            <li><strong>Expand:</strong> Breakpoint de colapso (sm, md, lg, xl, xxl)</li>
                                            <li><strong>Tema:</strong> light, dark</li>
                                            <li><strong>Fondo:</strong> bg-primary, bg-dark, etc.</li>
                                            <li><strong>Posición:</strong> fixed-top, fixed-bottom, sticky-top</li>
                                            <li><strong>Contenido:</strong> InnerBlocks para items de navegación</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-navbar {"brand":"Mi Sitio","expand":"lg","theme":"light","background":"bg-light"} -->
                                            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                                                <div class="container-fluid">
                                                    <a class="navbar-brand" href="#">Mi Sitio</a>
                                                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarExample">
                                                        <span class="navbar-toggler-icon"></span>
                                                    </button>
                                                    <div class="collapse navbar-collapse" id="navbarExample">
                                                        <div class="wp-block-bootstrap-theme-bs-navbar-content">
                                                            <p>Contenido de navegación con InnerBlocks.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </nav>
                                            <!-- /wp:bootstrap-theme/bs-navbar -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Breadcrumb Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Breadcrumb Block (bs-breadcrumb)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Migas de pan Bootstrap para navegación.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Contenido:</strong> InnerBlocks para items del breadcrumb</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-breadcrumb -->
                                            <nav aria-label="breadcrumb">
                                                <ol class="breadcrumb">
                                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                                    <li class="breadcrumb-item"><a href="#">Library</a></li>
                                                    <li class="breadcrumb-item active">Data</li>
                                                </ol>
                                            </nav>
                                            <!-- /wp:bootstrap-theme/bs-breadcrumb -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bootstrap Menu Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Bootstrap Menu Block (bs-menu)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Menú de WordPress con estilos Bootstrap (Nav, List Group, Button Group).</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Menú:</strong> Selector de menús de WordPress</li>
                                            <li><strong>Estilo:</strong> nav (ul/li), list-group, button-group</li>
                                            <li><strong>Orientación:</strong> horizontal, vertical</li>
                                            <li><strong>Variante:</strong> Para button-group (primary, secondary, etc.)</li>
                                            <li><strong>Tamaño:</strong> Para button-group (sm, lg)</li>
                                            <li><strong>Nav Justified:</strong> Enlaces toman todo el ancho</li>
                                            <li><strong>Nav Fill:</strong> Fuerza extensión completa</li>
                                            <li><strong>List Group Dividers:</strong> Separadores entre items</li>
                                            <li><strong>Alineación:</strong> start, center, end (horizontal)</li>
                                            <li><strong>Alineación del Texto:</strong> izquierda, centrado, derecha, justificado</li>
                                            <li><strong>Clase Activa:</strong> CSS class para item actual</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplos:</h6>
                                        <div class="border p-3 mb-3">
                                            <h6 class="small text-muted">Estilo Nav:</h6>
                                            <!-- wp:bootstrap-theme/bs-menu {"menuId":"1","style":"nav","orientation":"horizontal","justified":false} -->
                                            <ul class="nav">
                                                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                                            </ul>
                                            <!-- /wp:bootstrap-theme/bs-menu -->
                                        </div>
                                        <div class="border p-3 mb-3">
                                            <h6 class="small text-muted">Estilo List Group:</h6>
                                            <!-- wp:bootstrap-theme/bs-menu {"menuId":"1","style":"list-group","orientation":"vertical"} -->
                                            <div class="list-group">
                                                <a class="list-group-item list-group-item-action active" href="#">Home</a>
                                                <a class="list-group-item list-group-item-action" href="#">About</a>
                                                <a class="list-group-item list-group-item-action" href="#">Services</a>
                                                <a class="list-group-item list-group-item-action" href="#">Contact</a>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-menu -->
                                        </div>
                                        <div class="border p-3">
                                            <h6 class="small text-muted">Estilo Button Group:</h6>
                                            <!-- wp:bootstrap-theme/bs-menu {"menuId":"1","style":"button-group","orientation":"horizontal","variant":"primary"} -->
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-primary" href="#">Home</a>
                                                <a class="btn btn-primary" href="#">About</a>
                                                <a class="btn btn-primary" href="#">Services</a>
                                                <a class="btn btn-primary" href="#">Contact</a>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-menu -->
                                        </div>
                                        <div class="border p-3">
                                            <h6 class="small text-muted">Alineación de Texto - Centrado:</h6>
                                            <!-- wp:bootstrap-theme/bs-menu {"menuId":"1","style":"nav","orientation":"horizontal","textAlign":"center"} -->
                                            <ul class="nav text-center">
                                                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                                            </ul>
                                            <!-- /wp:bootstrap-theme/bs-menu -->
                                        </div>
                                        <div class="border p-3">
                                            <h6 class="small text-muted">Alineación de Texto - Derecha:</h6>
                                            <!-- wp:bootstrap-theme/bs-menu {"menuId":"1","style":"nav","orientation":"horizontal","textAlign":"right"} -->
                                            <ul class="nav text-end">
                                                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                                            </ul>
                                            <!-- /wp:bootstrap-theme/bs-menu -->
                                        </div>
                                        <div class="border p-3">
                                            <h6 class="small text-muted">Alineación de Texto - Justificado:</h6>
                                            <!-- wp:bootstrap-theme/bs-menu {"menuId":"1","style":"nav","orientation":"horizontal","textAlign":"justify"} -->
                                            <ul class="nav text-justify">
                                                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                                            </ul>
                                            <!-- /wp:bootstrap-theme/bs-menu -->
                                        </div>
                                        <div class="border p-3">
                                            <h6 class="small text-muted">Nav Justified y Fill:</h6>
                                            <!-- wp:bootstrap-theme/bs-menu {"menuId":"1","style":"nav","orientation":"horizontal","justified":true,"fill":true} -->
                                            <ul class="nav nav-justified nav-fill">
                                                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                                            </ul>
                                            <!-- /wp:bootstrap-theme/bs-menu -->
                                        </div>
                                        <div class="border p-3">
                                            <h6 class="small text-muted">Button Group Vertical + Texto Derecha:</h6>
                                            <!-- wp:bootstrap-theme/bs-menu {"menuId":"1","style":"button-group","orientation":"vertical","variant":"secondary","textAlign":"right"} -->
                                            <div class="btn-group-vertical text-end" role="group">
                                                <a class="btn btn-secondary" href="#">Home</a>
                                                <a class="btn btn-secondary" href="#">About</a>
                                                <a class="btn btn-secondary" href="#">Services</a>
                                                <a class="btn btn-secondary" href="#">Contact</a>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-menu -->
                                        </div>
                                        <div class="border p-3">
                                            <h6 class="small text-muted">List Group con Dividers + Texto Justificado:</h6>
                                            <!-- wp:bootstrap-theme/bs-menu {"menuId":"1","style":"list-group","orientation":"vertical","dividers":true,"textAlign":"justify"} -->
                                            <div class="list-group text-justify">
                                                <a class="list-group-item list-group-item-action active" href="#">Home</a>
                                                <div class="list-group-item list-group-item-divider p-0 border-0"></div>
                                                <a class="list-group-item list-group-item-action" href="#">About</a>
                                                <div class="list-group-item list-group-item-divider p-0 border-0"></div>
                                                <a class="list-group-item list-group-item-action" href="#">Services</a>
                                                <div class="list-group-item list-group-item-divider p-0 border-0"></div>
                                                <a class="list-group-item list-group-item-action" href="#">Contact</a>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-menu -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress & Indicators -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h3 class="text-danger border-bottom pb-2">
                                    <svg class="icon me-2">
                                        <use xlink:href="#fa-chart-line"></use>
                                    </svg>
                                    Progreso e Indicadores
                                </h3>
                            </div>
                        </div>

                        <!-- Progress Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Progress Block (bs-progress)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Barra de progreso Bootstrap.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Valor:</strong> 0-100</li>
                                            <li><strong>Variante:</strong> bg-primary, bg-success, etc.</li>
                                            <li><strong>Tamaño:</strong> Altura personalizada</li>
                                            <li><strong>Animado:</strong> Barra animada</li>
                                            <li><strong>Rayado:</strong> Estilo rayado</li>
                                            <li><strong>Etiqueta:</strong> Mostrar porcentaje</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-progress {"value":75,"variant":"bg-success","animated":true,"striped":true,"showLabel":true} -->
                                            <div class="progress">
                                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 75%">75%</div>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-progress -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Spinner Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Spinner Block (bs-spinner)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Indicador de carga Bootstrap.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Tipo:</strong> border, grow</li>
                                            <li><strong>Variante:</strong> text-primary, text-success, etc.</li>
                                            <li><strong>Tamaño:</strong> sm (border), sm (grow)</li>
                                            <li><strong>Etiqueta:</strong> Texto para lectores de pantalla</li>
                                            <li><strong>Alineación:</strong> left, center, right</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-spinner {"type":"border","variant":"text-primary","size":"sm","alignment":"center"} -->
                                            <div class="d-flex justify-content-center">
                                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            <!-- /wp:bootstrap-theme/bs-spinner -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Utility Components -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <h3 class="text-secondary border-bottom pb-2">
                                    <svg class="icon me-2">
                                        <use xlink:href="#fa-tools"></use>
                                    </svg>
                                    Utilidades y Controles
                                </h3>
                            </div>
                        </div>

                        <!-- Close Button Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Close Button Block (bs-close-button)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Botón de cerrar Bootstrap.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Deshabilitado:</strong> Estado disabled</li>
                                            <li><strong>Blanco:</strong> btn-close-white para fondos oscuros</li>
                                            <li><strong>Etiqueta ARIA:</strong> Texto para accesibilidad</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-close-button {"ariaLabel":"Cerrar"} -->
                                            <button type="button" class="btn-close" aria-label="Cerrar"></button>
                                            <!-- /wp:bootstrap-theme/bs-close-button -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Popover Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Popover Block (bs-popover)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Componente popover Bootstrap con tooltip emergente.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Título:</strong> Título del popover</li>
                                            <li><strong>Contenido:</strong> Texto del popover</li>
                                            <li><strong>Posición:</strong> top, bottom, left, right, auto</li>
                                            <li><strong>Trigger:</strong> click, hover, focus, manual</li>
                                            <li><strong>Elemento:</strong> button, link, span</li>
                                            <li><strong>Texto del elemento:</strong> Texto del botón/enlace</li>
                                            <li><strong>Variante:</strong> Color del botón trigger</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-popover {"title":"Título del Popover","content":"Contenido del popover","placement":"top","trigger":"click"} -->
                                            <button type="button" class="btn btn-danger" data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="click" title="Título del Popover" data-bs-content="Contenido del popover">
                                                Click me
                                            </button>
                                            <!-- /wp:bootstrap-theme/bs-popover -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tooltip Block -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">Tooltip Block (bs-tooltip)</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Descripción:</strong> Componente tooltip Bootstrap con texto emergente.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Opciones Disponibles:</h6>
                                        <ul>
                                            <li><strong>Texto:</strong> Contenido del tooltip</li>
                                            <li><strong>Posición:</strong> top, bottom, left, right, auto</li>
                                            <li><strong>Trigger:</strong> hover, focus, click, manual</li>
                                            <li><strong>Elemento:</strong> button, link, span</li>
                                            <li><strong>Texto del elemento:</strong> Texto del botón/enlace</li>
                                            <li><strong>Variante:</strong> Color del botón trigger</li>
                                            <li><strong>Clases CSS:</strong> Panel Avanzado (className)</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Ejemplo:</h6>
                                        <div class="border p-3">
                                            <!-- wp:bootstrap-theme/bs-tooltip {"text":"Texto del tooltip","placement":"top","trigger":"hover"} -->
                                            <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" title="Texto del tooltip">
                                                Hover me
                                            </button>
                                            <!-- /wp:bootstrap-theme/bs-tooltip -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Blocks Summary -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5>
                                        <svg class="icon me-2">
                                            <use xlink:href="#fa-info-circle"></use>
                                        </svg>
                                        Bloques Adicionales Disponibles
                                    </h5>
                                    <p>Además de los bloques mostrados arriba, el tema incluye los siguientes bloques que requieren configuración específica:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul>
                                                <li><strong>bs-button-group:</strong> Grupo de botones Bootstrap</li>
                                                <li><strong>bs-carousel:</strong> Carrusel de imágenes</li>
                                                <li><strong>bs-dropdown:</strong> Menú desplegable</li>
                                                <li><strong>bs-list-group:</strong> Lista de elementos</li>
                                                <li><strong>bs-navs-tabs:</strong> Navegación con pestañas</li>
                                                <li><strong>bs-pagination:</strong> Paginación</li>
                                                <li><strong>bs-placeholders:</strong> Placeholders de carga</li>
                                                <li><strong>bs-scrollspy:</strong> Navegación scroll</li>
                                                <li><strong>bs-toast:</strong> Notificaciones toast</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Características Comunes:</h6>
                                            <ul>
                                                <li>✅ <strong>className Support:</strong> Todos los bloques soportan clases CSS del panel Avanzado</li>
                                                <li>✅ <strong>InnerBlocks:</strong> Contenido personalizado con bloques anidados</li>
                                                <li>✅ <strong>Responsive:</strong> Diseño responsive con breakpoints Bootstrap</li>
                                                <li>✅ <strong>Accessibility:</strong> Etiquetas ARIA y navegación por teclado</li>
                                                <li>✅ <strong>Internationalization:</strong> Textos traducibles</li>
                                                <li>✅ <strong>Performance:</strong> JavaScript y CSS optimizados</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
        </div> <!-- close col-12 -->
    </div> <!-- close row -->  
</div> <!-- close container -->
</div>
</div>
</div>

<?php get_footer(); ?>
