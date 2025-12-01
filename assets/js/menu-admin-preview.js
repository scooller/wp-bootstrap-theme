(function($){
    'use strict';
    
    var initialized = false;
    
    function updatePreview($row) {
        var icon = $row.find('.edit-menu-item-fa-icon').val();
        var isButton = $row.find('.edit-menu-item-is-button').is(':checked');
        var style = $row.find('select[name^="menu-item-button-style"]').val();
        
        // Icon preview
        var $iconPreview = $row.find('.fa-icon-preview');
        if (!$iconPreview.length) {
            $iconPreview = $('<span class="fa-icon-preview" style="display:inline-block;margin-left:10px;vertical-align:middle;"></span>');
            $row.find('.field-fa-icon').append($iconPreview);
        }
        if (icon) {
            var iconClass = icon.startsWith('fa-') ? icon : 'fa-' + icon;
            $iconPreview.html('<i class="fas '+iconClass+'" style="font-size:1.2em;color:#0073aa;"></i>');
        } else {
            $iconPreview.empty();
        }
        
        // Button preview
        var $btnPreview = $row.find('.btn-preview');
        if (!$btnPreview.length) {
            $btnPreview = $('<span class="btn-preview" style="display:inline-block;margin-left:10px;vertical-align:middle;"></span>');
            $row.find('.field-button-style').append($btnPreview);
        }
        if (isButton && style) {
            $btnPreview.html('<button type="button" class="btn '+style+'">Preview</button>');
        } else {
            $btnPreview.empty();
        }
    }

    function createFaIconPicker($input) {
        if (!window.bootstrapThemeFaIconsCategorized || $input.data('fa-picker-initialized')) return;
        
        var $button = $('<button type="button" class="button fa-icon-picker-btn" style="margin-left:5px;">Icono</button>');
        $input.after($button);
        
        var pickerId = 'fa-picker-' + Math.random().toString(36).substr(2, 9);
        var $picker = $('<div id="'+pickerId+'" class="fa-icon-picker" style="position:absolute;z-index:99999;background:#fff;border:1px solid #ccd0d4;border-radius:4px;max-height:320px;overflow:auto;box-shadow:0 1px 3px rgba(0,0,0,0.13);width:340px;padding:10px;display:none;"></div>');
        var $search = $('<input type="text" class="fa-icon-search" placeholder="Buscar icono..." style="width:100%;margin-bottom:8px;padding:3px 8px;border:1px solid #ddd;border-radius:4px;">');
        $picker.append($search);
        var $cats = $('<div class="fa-icon-categories"></div>');
        $picker.append($cats);
        $('body').append($picker);
        
        function renderIcons(filter) {
            $cats.empty();
            Object.keys(window.bootstrapThemeFaIconsCategorized).forEach(function(cat){
                var icons = window.bootstrapThemeFaIconsCategorized[cat].filter(function(icon){
                    return !filter || icon.indexOf(filter) !== -1;
                });
                if (icons.length === 0) return;
                var $cat = $('<div class="fa-icon-category" style="margin-bottom:10px;"></div>');
                $cat.append('<div style="font-weight:bold;margin-bottom:4px;font-size:12px;color:#555;">'+cat+'</div>');
                var $list = $('<div style="display:flex;flex-wrap:wrap;gap:4px;"></div>');
                icons.forEach(function(icon){
                    var $item = $('<div class="fa-icon-item" style="width:32px;height:32px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;border-radius:3px;border:1px solid #ddd;background:#f9f9f9;transition:all 0.2s;" title="'+icon+'"></div>');
                    $item.append('<i class="fas '+icon+'" style="font-size:14px;color:#0073aa;"></i>');
                    $item.on('click', function(e){
                        e.preventDefault();
                        e.stopPropagation();
                        $input.val(icon);
                        updatePreview($input.closest('.menu-item'));
                        $picker.hide();
                    });
                    $item.on('mouseenter', function(){
                        $(this).css('background', '#e3f2fd');
                    }).on('mouseleave', function(){
                        $(this).css('background', '#f9f9f9');
                    });
                    $list.append($item);
                });
                $cat.append($list);
                $cats.append($cat);
            });
        }
        
        $search.on('input', function(){
            renderIcons($(this).val().toLowerCase());
        });
        
        $button.on('mousedown', function(e){
            e.preventDefault();
            e.stopPropagation();
            $('.fa-icon-picker').not('#'+pickerId).hide();
            renderIcons('');
            var buttonOffset = $button.offset();
            $picker.css({
                top: buttonOffset.top + $button.outerHeight() + 5,
                left: buttonOffset.left
            }).show();
            $search.focus();
        });
        
        $(document).on('click.fa-picker', function(e){
            if (!$(e.target).closest('#'+pickerId+', .fa-icon-picker-btn').length) {
                $picker.hide();
            }
        });
        
        $input.data('fa-picker-initialized', true);
    }

    function processMenuItem($item) {
        var $iconInput = $item.find('.edit-menu-item-fa-icon');
        if ($iconInput.length && !$iconInput.data('fa-processed')) {
            createFaIconPicker($iconInput);
            updatePreview($item);
            $iconInput.data('fa-processed', true);
            // Actualizar preview en tiempo real
            $iconInput.on('input.fapreview change.fapreview', function(){
                updatePreview($item);
            });
        }
    }

    function init() {
        if (initialized || !$('#menu-to-edit').length) return;
        initialized = true;
        
        // Process existing items
        $('#menu-to-edit .menu-item').each(function(){
            processMenuItem($(this));
        });
        
        // Use delegated events to avoid conflicts
        $(document).on('input.fapreview keyup.fapreview change.fapreview', '#menu-to-edit .edit-menu-item-fa-icon, #menu-to-edit .edit-menu-item-is-button, #menu-to-edit select[name^="menu-item-button-style"]', function(){
            updatePreview($(this).closest('.menu-item'));
        });
        
        // Watch for new items being added
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    $(mutation.addedNodes).find('.menu-item').each(function(){
                        processMenuItem($(this));
                    });
                }
            });
        });
        
        var menuContainer = document.getElementById('menu-to-edit');
        if (menuContainer) {
            observer.observe(menuContainer, { childList: true, subtree: true });
        }
    }
    
    // Initialize when DOM is ready and after WordPress menu scripts
    $(document).ready(function(){
        setTimeout(init, 100);
    });
    
    // Also initialize on wpNavMenu events if available
    if (typeof wpNavMenu !== 'undefined') {
        $(document).on('menu-item-added', function(){
            setTimeout(function(){
                $('#menu-to-edit .menu-item').each(function(){
                    processMenuItem($(this));
                });
            }, 50);
        });
    }
    
})(jQuery);
