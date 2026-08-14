/**
 * Structure helper for Contact Form 7: wrap label text so CSS can float it.
 * Animation stays CSS-only (transform). Needed because CF7 puts the label
 * text in the same <label> as the input, and Elementor often caches that HTML.
 */
(function () {
  function prepare(label) {
    if (label.querySelector('.werdu-float-label')) {
      return;
    }
    if (label.querySelector('[type="checkbox"], [type="radio"], .wpcf7-acceptance')) {
      return;
    }
    var wrap = label.querySelector('.wpcf7-form-control-wrap');
    if (!wrap) {
      return;
    }
    var field = wrap.querySelector('input:not([type="hidden"]):not([type="submit"]):not([type="checkbox"]):not([type="radio"]), textarea, select');
    if (!field) {
      return;
    }
    if (!field.getAttribute('placeholder')) {
      field.setAttribute('placeholder', ' ');
    } else if (!field.getAttribute('placeholder').trim()) {
      field.setAttribute('placeholder', ' ');
    }

    var parts = [];
    var nodes = [];
    for (var i = 0; i < label.childNodes.length; i++) {
      var n = label.childNodes[i];
      if (n.nodeType === 3) {
        var t = n.textContent.replace(/\s+/g, ' ').trim();
        if (t) {
          parts.push(t);
          nodes.push(n);
        }
      } else if (n.nodeType === 1 && n.tagName === 'BR') {
        nodes.push(n);
      }
    }
    var text = parts.join(' ').replace(/\s*\(Pflichtfeld\)\s*/gi, ' ').replace(/\s*\*\s*$/, '').trim();
    if (!text) {
      return;
    }
    var span = document.createElement('span');
    span.className = 'werdu-float-label';
    span.textContent = text;
    nodes.forEach(function (n) {
      if (n.parentNode === label) {
        label.removeChild(n);
      }
    });
    label.insertBefore(span, wrap);
  }

  function run() {
    var labels = document.querySelectorAll('.wpcf7-form label');
    for (var i = 0; i < labels.length; i++) {
      prepare(labels[i]);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run);
  } else {
    run();
  }
  document.addEventListener('wpcf7init', run);
})();
