(function(){
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, ToggleControl, SelectControl, RangeControl, CheckboxControl, Spinner } = wp.components;
    const { __ } = wp.i18n;
    const { createElement, Fragment, useEffect, useState } = wp.element;
    const { apiFetch } = wp;

    registerBlockType('bootstrap-theme/bs-wc-products', {
        title: __('WooCommerce Products (Bootstrap Loop)', 'bootstrap-theme'),
        icon: 'products',
        category: 'bootstrap',
        attributes: {
            useThemeDefaults: { type: 'boolean', default: true },
            productsPerRow:   { type: 'number',  default: 4 },
            productsPerRowMobile: { type: 'number', default: 2 },
            productsPerPage:  { type: 'number',  default: 12 },
            defaultOrderby:   { type: 'string',  default: 'menu_order' },
            defaultOrder:     { type: 'string',  default: 'ASC' },
            showPagination:   { type: 'boolean', default: true },
            showOrdering:     { type: 'boolean', default: true },
            showSearch:       { type: 'boolean', default: true },
            onlyInStock:      { type: 'boolean', default: false },
            categories:       { type: 'array',   default: [] },
            aosAnimation: { type: 'string', default: '' },
            aosDelay: { type: 'number', default: 0 },
            aosDuration: { type: 'number', default: 800 },
            aosEasing: { type: 'string', default: 'ease-in-out-cubic' },
            aosOnce: { type: 'boolean', default: false },
            aosMirror: { type: 'boolean', default: true },
            aosAnchorPlacement: { type: 'string', default: 'top-bottom' }
        },
        edit: function(props){
            const { attributes, setAttributes } = props;
            const [categoryOptions, setCategoryOptions] = useState([]);
            const [loading, setLoading] = useState(true);

            useEffect(() => {
                apiFetch({ path: '/wp/v2/product_cat?per_page=100&_fields=id,name' })
                    .then(categories => {
                        const opts = categories.map(cat => ({ label: cat.name, value: cat.id }));
                        setCategoryOptions(opts);
                        setLoading(false);
                    })
                    .catch(err => {
                        console.error('Error fetching categories:', err);
                        setLoading(false);
                    });
            }, []);

            const handleCategoryToggle = (categoryId) => {
                const newCategories = attributes.categories.includes(categoryId)
                    ? attributes.categories.filter(id => id !== categoryId)
                    : [...attributes.categories, categoryId];
                setAttributes({ categories: newCategories });
            };

            const orderbyOptions = [
                { label: __('Default order', 'bootstrap-theme'), value: 'menu_order' },
                { label: __('Title', 'bootstrap-theme'), value: 'title' },
                { label: __('Date', 'bootstrap-theme'), value: 'date' },
                { label: __('Modified', 'bootstrap-theme'), value: 'modified' },
                { label: __('Price', 'bootstrap-theme'), value: 'price' },
                { label: __('Popularity', 'bootstrap-theme'), value: 'popularity' },
                { label: __('Rating', 'bootstrap-theme'), value: 'rating' },
                { label: __('SKU', 'bootstrap-theme'), value: 'sku' },
                { label: __('Random', 'bootstrap-theme'), value: 'rand' },
            ];
            const orderOptions = [
                { label: 'ASC', value: 'ASC' },
                { label: 'DESC', value: 'DESC' }
            ];
            return createElement(Fragment, {},
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: __('Loop Settings', 'bootstrap-theme') },
                        createElement(ToggleControl, {
                            label: __('Use theme WooCommerce catalog defaults', 'bootstrap-theme'),
                            checked: attributes.useThemeDefaults,
                            onChange: (v)=> setAttributes({useThemeDefaults: v})
                        }),
                        createElement(RangeControl, {
                            label: __('Products per row', 'bootstrap-theme'),
                            min: 1, max: 12, value: attributes.productsPerRow,
                            onChange: (v)=> setAttributes({productsPerRow: v}),
                            help: attributes.useThemeDefaults ? __('Theme default will be used (this value is ignored)', 'bootstrap-theme') : '',
                            disabled: attributes.useThemeDefaults
                        }),
                        createElement(RangeControl, {
                            label: __('Products per row (mobile)', 'bootstrap-theme'),
                            min: 1, max: 6, value: attributes.productsPerRowMobile,
                            onChange: (v)=> setAttributes({productsPerRowMobile: v}),
                            help: attributes.useThemeDefaults ? __('Theme default will be used (this value is ignored)', 'bootstrap-theme') : __('For small screens. Recommended: 1 or 2', 'bootstrap-theme'),
                            disabled: attributes.useThemeDefaults
                        }),
                        createElement(RangeControl, {
                            label: __('Products per page', 'bootstrap-theme'),
                            min: 1, max: 100, value: attributes.productsPerPage,
                            onChange: (v)=> setAttributes({productsPerPage: v}),
                            help: attributes.useThemeDefaults ? __('Theme default will be used (this value is ignored)', 'bootstrap-theme') : '',
                            disabled: attributes.useThemeDefaults
                        }),
                        !attributes.useThemeDefaults && createElement(Fragment, {},
                            createElement(SelectControl, {
                                label: __('Default order by', 'bootstrap-theme'),
                                value: attributes.defaultOrderby,
                                options: orderbyOptions,
                                onChange: (v)=> setAttributes({defaultOrderby: v})
                            }),
                            createElement(SelectControl, {
                                label: __('Default order', 'bootstrap-theme'),
                                value: attributes.defaultOrder,
                                options: orderOptions,
                                onChange: (v)=> setAttributes({defaultOrder: v})
                            })
                        ),
                        createElement(ToggleControl, {
                            label: __('Show pagination', 'bootstrap-theme'),
                            checked: attributes.showPagination,
                            onChange: (v)=> setAttributes({showPagination: v})
                        }),
                        createElement(ToggleControl, {
                            label: __('Show ordering (filter)', 'bootstrap-theme'),
                            checked: attributes.showOrdering,
                            onChange: (v)=> setAttributes({showOrdering: v})
                        }),
                        createElement(ToggleControl, {
                            label: __('Show search', 'bootstrap-theme'),
                            checked: attributes.showSearch,
                            onChange: (v)=> setAttributes({showSearch: v})
                        }),
                        createElement(ToggleControl, {
                            label: __('Only products in stock', 'bootstrap-theme'),
                            checked: attributes.onlyInStock,
                            onChange: (v)=> setAttributes({onlyInStock: v})
                        })
                    ),
                    createElement(PanelBody, { title: __('Product Categories', 'bootstrap-theme') },
                        loading && createElement(Spinner, {}),
                        !loading && categoryOptions.length > 0 && categoryOptions.map(cat =>
                            createElement(CheckboxControl, {
                                key: cat.value,
                                label: cat.label,
                                checked: attributes.categories.includes(cat.value),
                                onChange: () => handleCategoryToggle(cat.value)
                            })
                        ),
                        !loading && categoryOptions.length === 0 && createElement('p', {}, __('No categories found', 'bootstrap-theme'))
                    ),
                    createElement(PanelBody, { title: __('AOS Animation', 'bootstrap-theme'), initialOpen: false },
                        createElement(SelectControl, {
                            label: __('Animation Type', 'bootstrap-theme'),
                            value: attributes.aosAnimation,
                            options: [
                                { label: __('None', 'bootstrap-theme'), value: '' },
                                { label: 'fade', value: 'fade' },
                                { label: 'fade-up', value: 'fade-up' },
                                { label: 'fade-down', value: 'fade-down' },
                                { label: 'fade-left', value: 'fade-left' },
                                { label: 'fade-right', value: 'fade-right' },
                                { label: 'fade-up-right', value: 'fade-up-right' },
                                { label: 'fade-up-left', value: 'fade-up-left' },
                                { label: 'fade-down-right', value: 'fade-down-right' },
                                { label: 'fade-down-left', value: 'fade-down-left' },
                                { label: 'flip-up', value: 'flip-up' },
                                { label: 'flip-down', value: 'flip-down' },
                                { label: 'flip-left', value: 'flip-left' },
                                { label: 'flip-right', value: 'flip-right' },
                                { label: 'slide-up', value: 'slide-up' },
                                { label: 'slide-down', value: 'slide-down' },
                                { label: 'slide-left', value: 'slide-left' },
                                { label: 'slide-right', value: 'slide-right' },
                                { label: 'zoom-in', value: 'zoom-in' },
                                { label: 'zoom-in-up', value: 'zoom-in-up' },
                                { label: 'zoom-in-down', value: 'zoom-in-down' },
                                { label: 'zoom-in-left', value: 'zoom-in-left' },
                                { label: 'zoom-in-right', value: 'zoom-in-right' },
                                { label: 'zoom-out', value: 'zoom-out' },
                                { label: 'zoom-out-up', value: 'zoom-out-up' },
                                { label: 'zoom-out-down', value: 'zoom-out-down' },
                                { label: 'zoom-out-left', value: 'zoom-out-left' },
                                { label: 'zoom-out-right', value: 'zoom-out-right' }
                            ],
                            onChange: (value) => setAttributes({ aosAnimation: value })
                        }),
                        attributes.aosAnimation && createElement(Fragment, {},
                            createElement(RangeControl, {
                                label: __('Delay (ms)', 'bootstrap-theme'),
                                value: attributes.aosDelay,
                                onChange: (value) => setAttributes({ aosDelay: value }),
                                min: 0,
                                max: 3000,
                                step: 100
                            }),
                            createElement(RangeControl, {
                                label: __('Duration (ms)', 'bootstrap-theme'),
                                value: attributes.aosDuration,
                                onChange: (value) => setAttributes({ aosDuration: value }),
                                min: 100,
                                max: 3000,
                                step: 100
                            }),
                            createElement(SelectControl, {
                                label: __('Easing', 'bootstrap-theme'),
                                value: attributes.aosEasing,
                                options: [
                                    { label: 'linear', value: 'linear' },
                                    { label: 'ease', value: 'ease' },
                                    { label: 'ease-in', value: 'ease-in' },
                                    { label: 'ease-out', value: 'ease-out' },
                                    { label: 'ease-in-out', value: 'ease-in-out' },
                                    { label: 'ease-in-back', value: 'ease-in-back' },
                                    { label: 'ease-out-back', value: 'ease-out-back' },
                                    { label: 'ease-in-out-back', value: 'ease-in-out-back' },
                                    { label: 'ease-in-sine', value: 'ease-in-sine' },
                                    { label: 'ease-out-sine', value: 'ease-out-sine' },
                                    { label: 'ease-in-out-sine', value: 'ease-in-out-sine' },
                                    { label: 'ease-in-quad', value: 'ease-in-quad' },
                                    { label: 'ease-out-quad', value: 'ease-out-quad' },
                                    { label: 'ease-in-out-quad', value: 'ease-in-out-quad' },
                                    { label: 'ease-in-cubic', value: 'ease-in-cubic' },
                                    { label: 'ease-out-cubic', value: 'ease-out-cubic' },
                                    { label: 'ease-in-out-cubic', value: 'ease-in-out-cubic' },
                                    { label: 'ease-in-quart', value: 'ease-in-quart' },
                                    { label: 'ease-out-quart', value: 'ease-out-quart' },
                                    { label: 'ease-in-out-quart', value: 'ease-in-out-quart' }
                                ],
                                onChange: (value) => setAttributes({ aosEasing: value })
                            }),
                            createElement(ToggleControl, {
                                label: __('Animate Once', 'bootstrap-theme'),
                                help: __('Only animate once when element is first scrolled into view', 'bootstrap-theme'),
                                checked: attributes.aosOnce,
                                onChange: (value) => setAttributes({ aosOnce: value })
                            }),
                            createElement(ToggleControl, {
                                label: __('Mirror Animation', 'bootstrap-theme'),
                                help: __('Repeat animation when scrolling up', 'bootstrap-theme'),
                                checked: attributes.aosMirror,
                                onChange: (value) => setAttributes({ aosMirror: value })
                            }),
                            createElement(SelectControl, {
                                label: __('Anchor Placement', 'bootstrap-theme'),
                                value: attributes.aosAnchorPlacement,
                                options: [
                                    { label: 'top-bottom', value: 'top-bottom' },
                                    { label: 'top-center', value: 'top-center' },
                                    { label: 'top-top', value: 'top-top' },
                                    { label: 'center-bottom', value: 'center-bottom' },
                                    { label: 'center-center', value: 'center-center' },
                                    { label: 'center-top', value: 'center-top' },
                                    { label: 'bottom-bottom', value: 'bottom-bottom' },
                                    { label: 'bottom-center', value: 'bottom-center' },
                                    { label: 'bottom-top', value: 'bottom-top' }
                                ],
                                onChange: (value) => setAttributes({ aosAnchorPlacement: value })
                            })
                        )
                    )
                ),
                createElement('div', { className: 'bs-wc-products-editor-placeholder border p-3 rounded' },
                    createElement('strong', {}, __('WooCommerce Products', 'bootstrap-theme')),
                    createElement('div', { className: 'text-muted mt-2' }, __('The loop will render on the frontend.', 'bootstrap-theme'))
                )
            );
        },
        save: function(){ return null; }
    });
})();