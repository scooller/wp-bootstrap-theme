/**
 * WooCommerce Admin Scripts
 * Maneja la visualización de elementos específicos por tab
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Función para mover y controlar visibilidad de "Acciones para productos de prueba"
        function handleTestProductActions() {
            // Buscar el contenedor de acciones
            const actionsSection = $('h3:contains("Acciones para productos de prueba")').first();
            
            if (!actionsSection.length) {
                return;
            }

            // Obtener todos los elementos relacionados (título, descripción, botones)
            const actionsTitle = actionsSection;
            const actionsDescription = actionsTitle.next('p');
            const actionsButtons = actionsDescription.next('.button-group, p').find('.button, .button-primary');
            
            // Crear un contenedor para agrupar todos los elementos
            const actionsContainer = $('<div class="test-products-actions-container" style="display:none;"></div>');
            
            // Agrupar todos los elementos relacionados
            actionsContainer.append(actionsTitle.clone());
            actionsContainer.append(actionsDescription.clone());
            
            // Clonar los botones y sus contenedores
            actionsDescription.next().clone().appendTo(actionsContainer);
            
            // Insertar el contenedor después del último campo del tab "Productos de Prueba"
            const lastTestField = $('.acf-field[data-name="woocommerce_test_generate_images"]');
            if (lastTestField.length) {
                actionsContainer.insertAfter(lastTestField);
            }
            
            // Ocultar los elementos originales
            actionsTitle.hide();
            actionsDescription.hide();
            actionsDescription.nextAll('.button-group, p').first().hide();
            
            // Función para mostrar/ocultar según tab activo
            function toggleActionsVisibility() {
                const activeTab = $('.acf-tab-button.active').data('key');
                
                if (activeTab === 'field_wc_test_products_tab') {
                    actionsContainer.show();
                } else {
                    actionsContainer.hide();
                }
            }
            
            // Ejecutar al cargar y al cambiar tabs
            toggleActionsVisibility();
            
            // Observar cambios en tabs
            $('.acf-tab-button').on('click', function() {
                setTimeout(toggleActionsVisibility, 100);
            });
        }

        // Ejecutar cuando ACF esté listo
        if (typeof acf !== 'undefined') {
            acf.addAction('ready', handleTestProductActions);
        } else {
            // Fallback si ACF no está disponible
            setTimeout(handleTestProductActions, 500);
        }
        
        // Re-ejecutar si hay cambios dinámicos en el DOM
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    handleTestProductActions();
                }
            });
        });
        
        // Observar cambios en el contenedor de opciones
        const optionsContainer = document.querySelector('.acf-fields');
        if (optionsContainer) {
            observer.observe(optionsContainer, {
                childList: true,
                subtree: true
            });
        }
    });

})(jQuery);
