<?php
/**
 * WooCommerce Maintenance Tab
 * 
 * @package Bootstrap_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Agregar tab de mantenimiento a la página de WooCommerce
 */
add_action('admin_menu', 'bootstrap_theme_add_woocommerce_maintenance_tab', 20);
function bootstrap_theme_add_woocommerce_maintenance_tab() {
	if (!current_user_can('administrator')) {
		return;
	}
	
	add_submenu_page(
		'bootstrap-theme-options',
		__('Mantenimiento WooCommerce', 'bootstrap-theme'),
		__('Mantenimiento', 'bootstrap-theme'),
		'administrator',
		'bootstrap-theme-woocommerce-maintenance',
		'bootstrap_theme_render_maintenance_page'
	);
}

/**
 * Renderizar página de mantenimiento
 */
function bootstrap_theme_render_maintenance_page() {
	if (!current_user_can('administrator')) {
		wp_die(__('No tienes permisos para acceder a esta página.', 'bootstrap-theme'));
	}

	// Procesar acciones
	if (isset($_POST['bootstrap_theme_maintenance_action'])) {
		check_admin_referer('bootstrap_theme_maintenance_nonce');
		
		$action = sanitize_text_field($_POST['bootstrap_theme_maintenance_action']);
		
		switch ($action) {
			case 'restore_stock':
				bootstrap_theme_restore_orphan_products_stock();
				break;
			case 'clean_carts':
				bootstrap_theme_clean_abandoned_carts();
				break;
			case 'clear_wc_logs':
				bootstrap_theme_clear_wc_logs();
				break;
			case 'fix_downloadable_files':
				bootstrap_theme_fix_downloadable_files();
				break;
			case 'normalize_downloadables':
				bootstrap_theme_normalize_downloadables();
				break;
		}
	}

	// Obtener stock por defecto de ACF
	$default_stock = 10;
	if (function_exists('get_field')) {
		$default_stock = (int) get_field('woocommerce_default_stock_restore', 'option');
		if ($default_stock <= 0) {
			$default_stock = 10;
		}
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e('Mantenimiento WooCommerce', 'bootstrap-theme'); ?></h1>
		<p><?php esc_html_e('Herramientas de mantenimiento para productos y carritos de la tienda.', 'bootstrap-theme'); ?></p>

		<div class="card" style="max-width: 800px; margin-top: 20px; padding: 20px;">
			<h2 class="title"><?php esc_html_e('Configuración', 'bootstrap-theme'); ?></h2>
			<p><?php printf(
				__('Stock por defecto configurado: <strong>%d unidades</strong>', 'bootstrap-theme'),
				$default_stock
			); ?></p>
			<p>
				<a href="<?php echo esc_url(admin_url('admin.php?page=bootstrap-theme-options-woocommerce')); ?>" class="button">
					<?php esc_html_e('Cambiar configuración', 'bootstrap-theme'); ?>
				</a>
			</p>
		</div>

		<div class="card" style="max-width: 800px; margin-top: 20px; padding: 20px;">
			<h2 class="title"><?php esc_html_e('Restaurar Stock de Productos Huérfanos', 'bootstrap-theme'); ?></h2>
			<p><?php esc_html_e('Esta herramienta identifica productos sin stock que no están asociados a ningún pedido y les asigna stock según la configuración.', 'bootstrap-theme'); ?></p>
			<p><strong><?php esc_html_e('¿Qué hace esta herramienta?', 'bootstrap-theme'); ?></strong></p>
			<ul>
				<li>✅ Busca productos simples sin stock</li>
				<li>✅ Verifica que NO estén en ningún pedido</li>
				<li>✅ Les asigna <?php echo esc_html($default_stock); ?> unidades de stock</li>
				<li>✅ Los marca como "en stock"</li>
			</ul>
			
			<form method="post">
				<?php wp_nonce_field('bootstrap_theme_maintenance_nonce'); ?>
				<input type="hidden" name="bootstrap_theme_maintenance_action" value="restore_stock">
				<p>
					<button type="submit" class="button button-primary" onclick="return confirm('<?php esc_attr_e('¿Estás seguro de que deseas restaurar el stock de productos huérfanos?', 'bootstrap-theme'); ?>')">
						<?php esc_html_e('Restaurar Stock', 'bootstrap-theme'); ?>
					</button>
				</p>
			</form>
		</div>

		<div class="card" style="max-width: 800px; margin-top: 20px; padding: 20px;">
			<h2 class="title"><?php esc_html_e('Limpiar Carritos Abandonados', 'bootstrap-theme'); ?></h2>
			<p><?php esc_html_e('Elimina carritos abandonados y libera las reservas de stock asociadas.', 'bootstrap-theme'); ?></p>
			<p><strong><?php esc_html_e('¿Qué hace esta herramienta?', 'bootstrap-theme'); ?></strong></p>
			<ul>
				<li>✅ Elimina sesiones de carrito de más de 7 días</li>
				<li>✅ Limpia reservas de stock antiguas (más de 30 minutos)</li>
				<li>✅ Libera productos para que otros usuarios puedan comprarlos</li>
			</ul>
			
			<form method="post">
				<?php wp_nonce_field('bootstrap_theme_maintenance_nonce'); ?>
				<input type="hidden" name="bootstrap_theme_maintenance_action" value="clean_carts">
				<p>
					<button type="submit" class="button button-primary" onclick="return confirm('<?php esc_attr_e('¿Estás seguro de que deseas limpiar los carritos abandonados?', 'bootstrap-theme'); ?>')">
						<?php esc_html_e('Limpiar Carritos', 'bootstrap-theme'); ?>
					</button>
				</p>
			</form>
		</div>

		<div class="card" style="max-width: 800px; margin-top: 20px; padding: 20px;">
			<h2 class="title"><?php esc_html_e('Reparar Archivos Descargables', 'bootstrap-theme'); ?></h2>
			<p><?php esc_html_e('Repara productos descargables que no tienen archivos configurados, asignándoles su imagen destacada como archivo descargable.', 'bootstrap-theme'); ?></p>
			<p><strong><?php esc_html_e('¿Qué hace esta herramienta?', 'bootstrap-theme'); ?></strong></p>
			<ul>
				<li>✅ Busca productos marcados como "Descargable"</li>
				<li>✅ Detecta los que no tienen archivos descargables configurados</li>
				<li>✅ Les asigna su imagen destacada como archivo descargable</li>
				<li>✅ Configura nombre del archivo y URL completa</li>
			</ul>
			
			<form method="post">
				<?php wp_nonce_field('bootstrap_theme_maintenance_nonce'); ?>
				<input type="hidden" name="bootstrap_theme_maintenance_action" value="fix_downloadable_files">
				<p>
					<button type="submit" class="button button-primary" onclick="return confirm('<?php esc_attr_e('¿Estás seguro de que deseas reparar los archivos descargables?', 'bootstrap-theme'); ?>')">
						<?php esc_html_e('Reparar Archivos Descargables', 'bootstrap-theme'); ?>
					</button>
				</p>
			</form>
		</div>

		<div class="card" style="max-width: 800px; margin-top: 20px; padding: 20px;">
			<h2 class="title"><?php esc_html_e('Normalizar Productos Descargables', 'bootstrap-theme'); ?></h2>
			<p><?php esc_html_e('Convierte productos descargables en virtuales y establece el límite de descargas como ilimitado.', 'bootstrap-theme'); ?></p>
			<p><strong><?php esc_html_e('¿Qué hace esta herramienta?', 'bootstrap-theme'); ?></strong></p>
			<ul>
				<li>✅ Marca como "Virtual" todo producto con "Descargable" que no lo sea</li>
				<li>✅ Ajusta el "Límite de descargas" a ilimitado</li>
				<li>✅ Quita expiración de descarga (sin límite de días)</li>
				<li>✅ Incluye variaciones descargables</li>
			</ul>
			<form method="post">
				<?php wp_nonce_field('bootstrap_theme_maintenance_nonce'); ?>
				<input type="hidden" name="bootstrap_theme_maintenance_action" value="normalize_downloadables">
				<p>
					<button type="submit" class="button button-primary" onclick="return confirm('<?php esc_attr_e('¿Confirmas que deseas normalizar los productos descargables (virtual + ilimitado)?', 'bootstrap-theme'); ?>')">
						<?php esc_html_e('Normalizar Descargables', 'bootstrap-theme'); ?>
					</button>
				</p>
			</form>
		</div>

		<div class="card" style="max-width: 800px; margin-top: 20px; padding: 20px;">
			<h2 class="title"><?php esc_html_e('Logs de WooCommerce', 'bootstrap-theme'); ?></h2>
			<p><?php esc_html_e('Revisa los últimos errores de WooCommerce, especialmente útil al activar optimizaciones de performance.', 'bootstrap-theme'); ?></p>
			
			<?php 
			$log_files = bootstrap_theme_get_wc_log_files();
			if (empty($log_files)) {
				echo '<div class="notice notice-success inline"><p>' . esc_html__('✅ No hay archivos de log. ¡Todo funciona correctamente!', 'bootstrap-theme') . '</p></div>';
			} else {
				$selected_log = isset($_GET['log_file']) ? sanitize_text_field($_GET['log_file']) : '';
				if (empty($selected_log) && !empty($log_files)) {
					$selected_log = $log_files[0];
				}
				?>
				<form method="get" style="margin-bottom: 15px;">
					<input type="hidden" name="page" value="bootstrap-theme-woocommerce-maintenance">
					<select name="log_file" onchange="this.form.submit()" class="regular-text">
						<?php foreach ($log_files as $log_file): ?>
							<option value="<?php echo esc_attr($log_file); ?>" <?php selected($selected_log, $log_file); ?>>
								<?php echo esc_html($log_file); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<button type="submit" class="button"><?php esc_html_e('Ver Log', 'bootstrap-theme'); ?></button>
				</form>

				<?php if ($selected_log): ?>
					<div style="margin-bottom: 15px;">
						<form method="post" style="display: inline;">
							<?php wp_nonce_field('bootstrap_theme_maintenance_nonce'); ?>
							<input type="hidden" name="bootstrap_theme_maintenance_action" value="clear_wc_logs">
							<input type="hidden" name="log_file" value="<?php echo esc_attr($selected_log); ?>">
							<button type="submit" class="button" onclick="return confirm('<?php esc_attr_e('¿Estás seguro de que deseas eliminar este archivo de log?', 'bootstrap-theme'); ?>')">
								<?php esc_html_e('Eliminar este log', 'bootstrap-theme'); ?>
							</button>
						</form>
						<form method="post" style="display: inline; margin-left: 10px;">
							<?php wp_nonce_field('bootstrap_theme_maintenance_nonce'); ?>
							<input type="hidden" name="bootstrap_theme_maintenance_action" value="clear_wc_logs">
							<button type="submit" class="button button-secondary" onclick="return confirm('<?php esc_attr_e('¿Estás seguro de que deseas eliminar TODOS los logs?', 'bootstrap-theme'); ?>')">
								<?php esc_html_e('Eliminar todos los logs', 'bootstrap-theme'); ?>
							</button>
						</form>
					</div>

					<?php 
					$log_content = bootstrap_theme_read_wc_log($selected_log);
					if ($log_content):
					?>
						<div style="background: #f5f5f5; border: 1px solid #ddd; padding: 15px; max-height: 500px; overflow-y: auto; font-family: monospace; font-size: 12px; white-space: pre-wrap; word-wrap: break-word;">
							<?php echo esc_html($log_content); ?>
						</div>
						<p style="margin-top: 10px;">
							<small><?php printf(
								__('Mostrando últimas %d líneas de %s', 'bootstrap-theme'),
								100,
								'<code>' . esc_html($selected_log) . '</code>'
							); ?></small>
						</p>
					<?php else: ?>
						<div class="notice notice-info inline">
							<p><?php esc_html_e('El archivo de log está vacío.', 'bootstrap-theme'); ?></p>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php } ?>
		</div>
	</div>
	<?php
}

/**
 * Restaurar stock de productos huérfanos
 */
function bootstrap_theme_restore_orphan_products_stock() {
	global $wpdb;
	
	// Obtener stock por defecto de ACF
	$default_stock = 10;
	if (function_exists('get_field')) {
		$default_stock = (int) get_field('woocommerce_default_stock_restore', 'option');
		if ($default_stock <= 0) {
			$default_stock = 10;
		}
	}
	$updated_count = 0;
	
	// Obtener todos los productos simples sin stock
	$products = wc_get_products(array(
		'type' => 'simple',
		'limit' => -1,
		'stock_status' => 'outofstock',
		'manage_stock' => true,
		'return' => 'ids'
	));
	
	foreach ($products as $product_id) {
		// Verificar si el producto está asociado a algún pedido - Compatible con HPOS
		// Buscar órdenes que contengan este producto (más eficiente que revisar todas)
		$has_orders = false;
		$orders = wc_get_orders(array(
			'limit' => -1,
			'return' => 'ids'
		));
		
		foreach ($orders as $order_id) {
			$order = wc_get_order($order_id);
			if (!$order) continue;
			
			foreach ($order->get_items() as $item) {
				if ($item->get_product_id() == $product_id) {
					$has_orders = true;
					break 2; // Salir de ambos loops
				}
			}
		}
		
		// Si no está en ningún pedido, restaurar stock
		if (!$has_orders) {
			$product = wc_get_product($product_id);
			if ($product && $product->managing_stock()) {
				$product->set_stock_quantity($default_stock);
				$product->set_stock_status('instock');
				$product->save();
				$updated_count++;
			}
		}
	}
	
	add_settings_error(
		'bootstrap_theme_maintenance_messages',
		'bootstrap_theme_stock_restored',
		sprintf(
			__('Se restauró el stock de %d producto(s) huérfano(s) a %d unidades.', 'bootstrap-theme'),
			$updated_count,
			$default_stock
		),
		'success'
	);
	
	settings_errors('bootstrap_theme_maintenance_messages');
}

/**
 * Limpiar carritos abandonados
 */
function bootstrap_theme_clean_abandoned_carts() {
	global $wpdb;
	
	$expiration_days = 7;
	$expiration_time = time() - (DAY_IN_SECONDS * $expiration_days);
	$cleaned_count = 0;
	
	// Limpiar sesiones de WooCommerce antiguas
	$sessions_table = $wpdb->prefix . 'woocommerce_sessions';
	
	if ($wpdb->get_var("SHOW TABLES LIKE '$sessions_table'") === $sessions_table) {
		$cleaned_count = $wpdb->query($wpdb->prepare("
			DELETE FROM $sessions_table
			WHERE session_expiry < %d
		", $expiration_time));
	}
	
	// Limpiar transients de reservas de stock antiguas
	$reservations = get_transient('bootstrap_theme_stock_reservations');
	if ($reservations && is_array($reservations)) {
		$current_time = time();
		$cleaned_reservations = 0;
		
		foreach ($reservations as $session_id => $items) {
			foreach ($items as $product_id => $data) {
				if (isset($data['timestamp']) && ($current_time - $data['timestamp']) > 1800) { // 30 minutos
					unset($reservations[$session_id][$product_id]);
					$cleaned_reservations++;
				}
			}
			
			if (empty($reservations[$session_id])) {
				unset($reservations[$session_id]);
			}
		}
		
		set_transient('bootstrap_theme_stock_reservations', $reservations, 30 * MINUTE_IN_SECONDS);
	}
	
	add_settings_error(
		'bootstrap_theme_maintenance_messages',
		'bootstrap_theme_carts_cleaned',
		sprintf(
			__('Se limpiaron %d carrito(s) abandonado(s) de más de %d días.', 'bootstrap-theme'),
			$cleaned_count,
			$expiration_days
		),
		'success'
	);
	
	settings_errors('bootstrap_theme_maintenance_messages');
}

/**
 * Obtener lista de archivos de log de WooCommerce
 */
function bootstrap_theme_get_wc_log_files() {
	if ( ! class_exists( 'WC_Log_Levels' ) ) {
		return array();
	}

	$log_dir = WC_LOG_DIR;
	
	if ( ! is_dir( $log_dir ) ) {
		return array();
	}

	$files = glob( $log_dir . '*.log' );
	
	if ( ! $files ) {
		return array();
	}

	// Ordenar por fecha de modificación (más recientes primero)
	usort( $files, function( $a, $b ) {
		return filemtime( $b ) - filemtime( $a );
	} );

	// Solo devolver nombres de archivo
	return array_map( 'basename', $files );
}

/**
 * Leer contenido de un archivo de log de WooCommerce
 */
function bootstrap_theme_read_wc_log( $log_file, $lines = 100 ) {
	if ( ! class_exists( 'WC_Log_Levels' ) ) {
		return '';
	}

	$log_dir = WC_LOG_DIR;
	$log_path = $log_dir . $log_file;

	// Verificar que el archivo existe y está en el directorio de logs
	if ( ! file_exists( $log_path ) || dirname( $log_path ) !== rtrim( $log_dir, '/' ) ) {
		return '';
	}

	// Leer las últimas N líneas del archivo
	$file = new SplFileObject( $log_path, 'r' );
	$file->seek( PHP_INT_MAX );
	$total_lines = $file->key() + 1;

	$start = max( 0, $total_lines - $lines );
	$content = array();

	$file->seek( $start );
	while ( ! $file->eof() ) {
		$line = $file->fgets();
		if ( $line ) {
			$content[] = $line;
		}
	}

	return implode( '', $content );
}

/**
 * Limpiar archivos de log de WooCommerce
 */
function bootstrap_theme_clear_wc_logs() {
	if ( ! class_exists( 'WC_Log_Levels' ) ) {
		return;
	}

	$log_dir = WC_LOG_DIR;
	
	if ( ! is_dir( $log_dir ) ) {
		return;
	}

	$deleted_count = 0;
	$log_file = isset( $_POST['log_file'] ) ? sanitize_text_field( $_POST['log_file'] ) : '';

	if ( ! empty( $log_file ) ) {
		// Eliminar un solo archivo
		$log_path = $log_dir . $log_file;
		
		if ( file_exists( $log_path ) && dirname( $log_path ) === rtrim( $log_dir, '/' ) ) {
			if ( unlink( $log_path ) ) {
				$deleted_count = 1;
			}
		}
	} else {
		// Eliminar todos los archivos de log
		$files = glob( $log_dir . '*.log' );
		
		if ( $files ) {
			foreach ( $files as $file ) {
				if ( is_file( $file ) && unlink( $file ) ) {
					$deleted_count++;
				}
			}
		}
	}

	$message = $deleted_count === 1 
		? __( 'Se eliminó 1 archivo de log.', 'bootstrap-theme' )
		: sprintf( __( 'Se eliminaron %d archivos de log.', 'bootstrap-theme' ), $deleted_count );

	add_settings_error(
		'bootstrap_theme_maintenance_messages',
		'bootstrap_theme_logs_cleared',
		$message,
		'success'
	);
	
	settings_errors( 'bootstrap_theme_maintenance_messages' );
}

/**
 * Reparar archivos descargables faltantes
 * Asigna la imagen destacada como archivo descargable para productos que no tienen archivos configurados
 */
function bootstrap_theme_fix_downloadable_files() {
	$fixed_count = 0;
	$skipped_count = 0;
	$errors = array();
	
	// Obtener todos los productos descargables
	$products = wc_get_products(array(
		'limit' => -1,
		'downloadable' => true,
		'return' => 'ids'
	));
	
	foreach ($products as $product_id) {
		$product = wc_get_product($product_id);
		
		if (!$product) {
			continue;
		}
		
		// Verificar si ya tiene archivos descargables
		$downloads = $product->get_downloads();
		
		if (!empty($downloads)) {
			$skipped_count++;
			continue;
		}
		
		// Obtener imagen destacada
		$thumbnail_id = get_post_thumbnail_id($product_id);
		
		if (!$thumbnail_id) {
			$errors[] = sprintf(
				__('Producto #%d (%s) no tiene imagen destacada', 'bootstrap-theme'),
				$product_id,
				$product->get_name()
			);
			continue;
		}
		
		// Obtener URL de la imagen en tamaño completo
		$image_url = wp_get_attachment_image_url($thumbnail_id, 'full');
		
		if (!$image_url) {
			$errors[] = sprintf(
				__('No se pudo obtener URL de imagen para producto #%d (%s)', 'bootstrap-theme'),
				$product_id,
				$product->get_name()
			);
			continue;
		}
		
		// Obtener nombre del archivo
		$image_file = basename(get_attached_file($thumbnail_id));
		$product_name = $product->get_name();
		
		// Crear array de descarga
		$download_id = md5($image_url);
		$download_name = sprintf('%s - Imagen', $product_name);
		
		$new_download = new WC_Product_Download();
		$new_download->set_id($download_id);
		$new_download->set_name($download_name);
		$new_download->set_file($image_url);
		
		// Agregar download al producto
		$downloads_array = array($download_id => $new_download);
		$product->set_downloads($downloads_array);
		$product->save();
		
		$fixed_count++;
		
		error_log(sprintf(
			'Bootstrap Theme: Reparado archivo descargable para producto #%d (%s) - Imagen: %s',
			$product_id,
			$product_name,
			$image_file
		));
	}
	
	// Mensaje de resultado
	$message_parts = array();
	
	if ($fixed_count > 0) {
		$message_parts[] = sprintf(
			__('Se repararon %d producto(s) descargable(s).', 'bootstrap-theme'),
			$fixed_count
		);
	}
	
	if ($skipped_count > 0) {
		$message_parts[] = sprintf(
			__('%d producto(s) ya tenían archivos configurados.', 'bootstrap-theme'),
			$skipped_count
		);
	}
	
	if (!empty($errors)) {
		$message_parts[] = sprintf(
			__('%d producto(s) con errores (sin imagen destacada).', 'bootstrap-theme'),
			count($errors)
		);
	}
	
	$message = implode(' ', $message_parts);
	
	if (empty($message)) {
		$message = __('No se encontraron productos descargables para reparar.', 'bootstrap-theme');
	}
	
	$message_type = ($fixed_count > 0) ? 'success' : 'info';
	
	add_settings_error(
		'bootstrap_theme_maintenance_messages',
		'bootstrap_theme_downloadable_fixed',
		$message,
		$message_type
	);
	
	// Mostrar errores si los hay
	if (!empty($errors)) {
		foreach (array_slice($errors, 0, 5) as $error) {
			add_settings_error(
				'bootstrap_theme_maintenance_messages',
				'bootstrap_theme_downloadable_error_' . md5($error),
				'⚠️ ' . $error,
				'warning'
			);
		}
		
		if (count($errors) > 5) {
			add_settings_error(
				'bootstrap_theme_maintenance_messages',
				'bootstrap_theme_downloadable_more_errors',
				sprintf(__('... y %d errores más. Revisa el log para detalles.', 'bootstrap-theme'), count($errors) - 5),
				'warning'
			);
		}
	}
	
	settings_errors('bootstrap_theme_maintenance_messages');
}

function bootstrap_theme_normalize_downloadables() {
    $changed_virtual = 0;
    $changed_limit = 0;
    $changed_expiry = 0;
    $processed = 0;
    $processed_variations = 0;
    $products = wc_get_products(array(
        'limit' => -1,
        'downloadable' => true,
        'return' => 'ids'
    ));
    foreach ($products as $product_id) {
        $product = wc_get_product($product_id);
        if (!$product) { continue; }
        $processed++;
        $changed = false;
        if (! $product->get_virtual()) {
            $product->set_virtual(true);
            $changed_virtual++;
            $changed = true;
        }
        $limit = $product->get_download_limit();
        if ($limit !== -1) {
            $product->set_download_limit(-1);
            $changed_limit++;
            $changed = true;
        }
        $expiry = $product->get_download_expiry();
        if (!empty($expiry) && intval($expiry) !== 0) {
            $product->set_download_expiry(0);
            $changed_expiry++;
            $changed = true;
        }
        if ($changed) { $product->save(); }
    }
    $variation_ids = wc_get_products(array(
        'type' => 'variation',
        'limit' => -1,
        'downloadable' => true,
        'return' => 'ids'
    ));
    foreach ($variation_ids as $vid) {
        $variation = wc_get_product($vid);
        if (! $variation) { continue; }
        $processed_variations++;
        $changed = false;
        if (! $variation->get_virtual()) {
            $variation->set_virtual(true);
            $changed_virtual++;
            $changed = true;
        }
        $vlimit = $variation->get_download_limit();
        if ($vlimit !== -1) {
            $variation->set_download_limit(-1);
            $changed_limit++;
            $changed = true;
        }
        $vexpiry = $variation->get_download_expiry();
        if (!empty($vexpiry) && intval($vexpiry) !== 0) {
            $variation->set_download_expiry(0);
            $changed_expiry++;
            $changed = true;
        }
        if ($changed) { $variation->save(); }
    }
    $total = $processed + $processed_variations;
    $message = sprintf(
        __('Normalización completada. Productos procesados: %d. Marcados como virtual: %d. Límite de descargas ilimitado aplicado: %d. Expiración removida: %d.', 'bootstrap-theme'),
        $total,
        $changed_virtual,
        $changed_limit,
        $changed_expiry
    );
    add_settings_error(
        'bootstrap_theme_maintenance_messages',
        'bootstrap_theme_normalize_downloadables',
        $message,
        'success'
    );
    settings_errors('bootstrap_theme_maintenance_messages');
}
