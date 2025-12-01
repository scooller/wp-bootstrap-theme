(function(){
	'use strict';
	if (!window.wcLazyRelated || !window.wcLazyRelated.ajaxUrl) return;

	function toFormBody(obj){
		return Object.keys(obj).map(function(k){
			return encodeURIComponent(k)+'='+encodeURIComponent(obj[k]);
		}).join('&');
	}

	function loadSection(el){
		if (!el || el.dataset.loaded) return;
		var productId = el.getAttribute('data-product-id');
		var type = el.getAttribute('data-type');
		if (!productId || !type) return;
		el.dataset.loaded = '1';

		fetch(wcLazyRelated.ajaxUrl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			credentials: 'same-origin',
			body: toFormBody({
				action: 'load_related_products',
				product_id: productId,
				type: type,
				nonce: wcLazyRelated.nonce
			})
		}).then(function(res){ return res.json(); })
		.then(function(data){
			if (data && data.success && data.data && typeof data.data.html === 'string') {
				el.innerHTML = data.data.html;
				el.classList.add('wc-lazy-related--loaded');
			}
		}).catch(function(){ /* silent */ });
	}

	document.addEventListener('DOMContentLoaded', function(){
		var items = document.querySelectorAll('.wc-lazy-related');
		if (!items.length) return;

		if ('IntersectionObserver' in window) {
			var io = new IntersectionObserver(function(entries){
				entries.forEach(function(entry){
					if (entry.isIntersecting) {
						loadSection(entry.target);
						io.unobserve(entry.target);
					}
				});
			}, { rootMargin: '200px 0px' });

			items.forEach(function(el){ io.observe(el); });
		} else {
			items.forEach(loadSection);
		}
	});
})();