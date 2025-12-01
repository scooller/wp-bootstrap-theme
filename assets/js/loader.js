// Loader for site front
window.addEventListener('DOMContentLoaded', function() {
  var loader = document.getElementById('site-loader');
  if (!loader) return;

  var hidden = false;
  var wowInited = false;
  function initWOWOnce(){
    if (wowInited) return;
    wowInited = true;
    try {
      if (typeof WOW === 'function') {
        new WOW({
          boxClass: 'wow',
          animateClass: 'animate__animated',
          offset: 0,
          mobile: true,
          live: true
        }).init();
      }
    } catch (e) { /* silent */ }
  }
  function hideLoader() {
    if (hidden) return;
    hidden = true;
    loader.classList.add('loaded');
    loader.style.display = 'none';
    initWOWOnce();
  }

  // Always hide on window load
  window.addEventListener('load', hideLoader);
  // Hide also on BFCache restore
  window.addEventListener('pageshow', function(e){ if (e && e.persisted) hideLoader(); });
  // Fallback timer: if load never fires or a dependency stalls
  setTimeout(hideLoader, 2500);
  // WOW se inicializa en hideLoader para asegurar que el overlay ya no esté visible
});
