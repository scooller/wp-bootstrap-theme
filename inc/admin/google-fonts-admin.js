/**
 * Google Fonts Selector Simplificado - JavaScript para el admin
 * 
 * Mejora la experiencia del usuario al seleccionar fuentes de Google
 */

(function($) {
    'use strict';

    // Ejecutar cuando ACF esté listo
    acf.addAction('ready', function() {
        initSimpleGoogleFontsSelector();
    });

    function initSimpleGoogleFontsSelector() {
        // Agregar preview para fuente del cuerpo
        $('[data-name="google_fonts_body"] select').each(function() {
            var $select = $(this);
            
            $select.off('change.fontPreview').on('change.fontPreview', function() {
                var fontFamily = $(this).val();
                if (fontFamily) {
                    loadGoogleFontPreview(fontFamily, $(this), 'body');
                } else {
                    clearFontPreview($(this));
                }
            });

            // Aplicar preview inicial
            if ($select.val()) {
                loadGoogleFontPreview($select.val(), $select, 'body');
            }
        });

        // Agregar preview para fuente de títulos
        $('[data-name="google_fonts_headings"] select').each(function() {
            var $select = $(this);
            
            $select.off('change.fontPreview').on('change.fontPreview', function() {
                var fontFamily = $(this).val();
                if (fontFamily) {
                    loadGoogleFontPreview(fontFamily, $(this), 'heading');
                } else {
                    clearFontPreview($(this));
                }
            });

            // Aplicar preview inicial
            if ($select.val()) {
                loadGoogleFontPreview($select.val(), $select, 'heading');
            }
        });

        // Generar URL automáticamente
        updateGeneratedUrl();
        
        // Actualizar URL cuando cambien las selecciones
        $('[data-name="google_fonts_body"], [data-name="google_fonts_headings"], [data-name="google_fonts_weights"]').on('change', function() {
            setTimeout(updateGeneratedUrl, 500);
        });
    }

    function loadGoogleFontPreview(fontFamily, $select, type) {
        // Limpiar fuente anterior
        $('#google-font-preview-' + fontFamily.replace(/\s+/g, '-')).remove();

        // Cargar nueva fuente para preview
        // Obtener pesos seleccionados y construir el tuple correcto con ';'
        var weights = [];
        $('[data-name="google_fonts_weights"] input:checked').each(function() {
            weights.push($(this).val());
        });
        if (weights.length === 0) {
            weights = ['400', '700']; // Default weights
        }
        var weightsString = weights.join(';');
        var fontUrl = 'https://fonts.googleapis.com/css2?family=' +
                     encodeURIComponent(fontFamily) + ':wght@' + weightsString + '&display=swap';
        
        var $link = $('<link>', {
            id: 'google-font-preview-' + fontFamily.replace(/\s+/g, '-'),
            rel: 'stylesheet',
            href: fontUrl
        });

        $('head').append($link);

        // Aplicar preview al campo
        setTimeout(function() {
            var $wrapper = $select.closest('.acf-field');
            var $preview = $wrapper.find('.font-preview');
            
            if ($preview.length === 0) {
                $preview = $('<div class="font-preview" style="margin-top: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;"></div>');
                $wrapper.append($preview);
            }

            var sampleText = type === 'heading' ? 
                'Sample Heading Typography' : 
                'Sample body text in this beautiful font';

            $preview.html(
                '<p style="font-family: \'' + fontFamily + '\', sans-serif; font-size: ' + 
                (type === 'heading' ? '24px' : '16px') + '; margin: 0; font-weight: 400; color: #333;">' +
                '<strong>' + fontFamily + ' Regular:</strong> ' + sampleText + '</p>' +
                '<p style="font-family: \'' + fontFamily + '\', sans-serif; font-size: ' + 
                (type === 'heading' ? '24px' : '16px') + '; margin: 5px 0 0 0; font-weight: 700; color: #333;">' +
                '<strong>' + fontFamily + ' Bold:</strong> ' + sampleText + '</p>'
            );
        }, 500);
    }

    function clearFontPreview($select) {
        var $wrapper = $select.closest('.acf-field');
        var $preview = $wrapper.find('.font-preview');
        $preview.remove();
    }

    function updateGeneratedUrl() {
        var bodyFont = $('[data-name="google_fonts_body"] select').val();
        var headingFont = $('[data-name="google_fonts_headings"] select').val();
        var weights = [];
        
        $('[data-name="google_fonts_weights"] input:checked').each(function() {
            weights.push($(this).val());
        });

        if (weights.length === 0) {
            weights = ['400', '700']; // Default weights
        }

        var fontsToLoad = [];
        
        if (bodyFont) {
            fontsToLoad.push(bodyFont);
        }
        
        if (headingFont && headingFont !== bodyFont) {
            fontsToLoad.push(headingFont);
        }

        if (fontsToLoad.length > 0) {
            var weightsString = weights.join(';');
            var familyParams = fontsToLoad.map(function(font) {
                return 'family=' + encodeURIComponent(font) + ':wght@' + weightsString;
            }).join('&');
            
            var generatedUrl = 'https://fonts.googleapis.com/css2?' + familyParams + '&display=swap';
            
            // Mostrar URL generada
            var $urlDisplay = $('#generated-fonts-url');
            if ($urlDisplay.length === 0) {
                $urlDisplay = $('<div id="generated-fonts-url" style="margin: 20px 0; padding: 15px; background: #f0f8ff; border: 1px solid #0073aa; border-radius: 4px;"><h4 style="margin-top: 0;">🔗 URL de Google Fonts Generada:</h4><textarea readonly style="width: 100%; margin-top: 10px; font-family: monospace; font-size: 12px; height: 80px; padding: 10px;"></textarea><p style="margin-bottom: 0; margin-top: 10px; font-size: 12px; color: #666;"><strong>Tip:</strong> Esta URL se carga automáticamente en tu sitio. También puedes copiarla para uso manual.</p></div>');
                $('[data-name="google_fonts_weights"]').closest('.acf-field').after($urlDisplay);
            }
            
            $urlDisplay.find('textarea').val(generatedUrl);
            $urlDisplay.show();
        } else {
            $('#generated-fonts-url').hide();
        }
    }

    // Función para limpiar cache (disponible en consola)
    window.clearGoogleFontsCache = function() {
        if (typeof ajaxurl !== 'undefined') {
            $.post(ajaxurl, {
                action: 'clear_google_fonts_cache',
                nonce: window.bootstrap_theme_admin && window.bootstrap_theme_admin.nonce || ''
            }, function(response) {
                if (response.success) {
                    alert('Cache de Google Fonts limpiado exitosamente.');
                    location.reload();
                } else {
                    alert('Error al limpiar el cache.');
                }
            });
        } else {
            alert('AJAX no disponible. Intenta recargar la página.');
        }
    };

    // Función para generar preview manual
    window.previewSelectedFonts = function() {
        var bodyFont = $('[data-name="google_fonts_body"] select').val();
        var headingFont = $('[data-name="google_fonts_headings"] select').val();
        
        if (bodyFont) {
            loadGoogleFontPreview(bodyFont, $('[data-name="google_fonts_body"] select'), 'body');
        }
        
        if (headingFont) {
            loadGoogleFontPreview(headingFont, $('[data-name="google_fonts_headings"] select'), 'heading');
        }
        
        updateGeneratedUrl();
    };

})(jQuery);