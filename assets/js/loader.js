// Loader for site front
window.addEventListener('DOMContentLoaded', function() {
  var loader = document.getElementById('site-loader');
  if (!loader) return;

  var hidden = false;
  var aosInited = false;
  function initAOSOnce(){
    if (aosInited) return;
    aosInited = true;
    try {
      if (typeof AOS === 'object' && typeof AOS.init === 'function') {
        var globalAos = (typeof window.bootstrapThemeAOS === 'object' && window.bootstrapThemeAOS) ? window.bootstrapThemeAOS : {};
        var enabled = (typeof globalAos.enable === 'boolean') ? globalAos.enable : true;
        if (!enabled) return;

        var config = {
          duration: 800,
          easing: 'ease-in-out-cubic',
          once: false,
          mirror: true,
          offset: 100,
          disable: false
        };

        if (typeof globalAos.duration === 'number') { config.duration = globalAos.duration; }
        if (typeof globalAos.easing === 'string' && globalAos.easing) { config.easing = globalAos.easing; }
        if (typeof globalAos.once === 'boolean') { config.once = globalAos.once; }
        if (typeof globalAos.mirror === 'boolean') { config.mirror = globalAos.mirror; }
        if (typeof globalAos.offset === 'number') { config.offset = globalAos.offset; }

        var disable = (globalAos.disable === true || globalAos.disable === 'true') ? true : globalAos.disable;
        if (disable) { config.disable = disable; }

        AOS.init(config);
      }
    } catch (e) { /* silent */ }
  }
  function hideLoader() {
    if (hidden) return;
    hidden = true;
    loader.classList.add('loaded');
    loader.style.display = 'none';
    initAOSOnce();
  }

  // Always hide on window load
  window.addEventListener('load', hideLoader);
  // Hide also on BFCache restore
  window.addEventListener('pageshow', function(e){ if (e && e.persisted) hideLoader(); });
  // Fallback timer: if load never fires or a dependency stalls
  setTimeout(hideLoader, 2500);
  // AOS se inicializa en hideLoader para asegurar que el overlay ya no esté visible
});
