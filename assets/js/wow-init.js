// Initialize WOW.js for Animate.css v4
// WOW by default adds the class `animated` (v3). We override to use `animate__animated` (v4)
(function(){
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
  } catch (e) {
    // Fail silently if WOW is not available
  }
})();
