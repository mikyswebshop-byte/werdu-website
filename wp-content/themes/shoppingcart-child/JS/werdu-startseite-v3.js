/**
 * WERDU Homepage V2 — interactions (Stage 2)
 * Loaded ONLY on the "Werdu Homepage V2" page template. Does not touch any
 * other page. Depends on werdu-calc-handoff.js (window.werduCalcHandoff) for
 * the capacity-slider CTA, which is enqueued as a script dependency by
 * page-templates/page-werdu-v2.php.
 */
(function () {
  'use strict';

  var reduceMotion = false;
  try {
    reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  } catch (e) {}

  document.addEventListener('DOMContentLoaded', function () {
    bindProgressiveImages();
    bindCapacitySlider();
    bindAddToCartFly();
  });

  /* -----------------------------------------------------------
     Progressive image loading: reveal the full-res <img> once it
     has actually finished loading, over a tiny blurred placeholder.
     ----------------------------------------------------------- */
  function bindProgressiveImages() {
    var imgs = document.querySelectorAll('.img-blur-wrapper .full-img');
    Array.prototype.forEach.call(imgs, function (img) {
      function reveal() {
        img.classList.add('loaded');
      }
      if (img.complete && img.naturalWidth > 0) {
        reveal();
      } else {
        img.addEventListener('load', reveal, { once: true });
        img.addEventListener('error', reveal, { once: true });
      }
    });
  }

  /* -----------------------------------------------------------
     Section 2 — capacity slider. Single value (kWh), driven by one
     --p custom property that the CSS uses for fill/thumb/tooltip.
     On submit: hand off to the existing werduCalcHandoff utility so
     the CTA still lands on /beratung-anfragen/ with parameters,
     exactly like the other 3 calculators.
     ----------------------------------------------------------- */
  function bindCapacitySlider() {
    var slider = document.getElementById('wv2-capacity-range');
    var track = document.getElementById('wv2-slider-track');
    var tooltip = document.getElementById('wv2-slider-tooltip');
    var submitBtn = document.getElementById('wv2-slider-submit');
    var plzInput = document.getElementById('wv2-plz-input');

    if (!slider || !track) {
      return;
    }

    function currentUrl() {
      try {
        if (window.werduCalcConfig && window.werduCalcConfig.beratungUrl) {
          return window.werduCalcConfig.beratungUrl;
        }
      } catch (e) {}
      return '/beratung-anfragen/';
    }

    function updateVisuals() {
      var min = parseFloat(slider.min) || 0;
      var max = parseFloat(slider.max) || 100;
      var val = parseFloat(slider.value) || 0;
      var pct = ((val - min) / (max - min)) * 100;
      pct = Math.max(0, Math.min(100, pct));
      track.style.setProperty('--p', pct + '%');
      if (tooltip) {
        tooltip.textContent = val + ' kWh';
      }
    }

    slider.addEventListener('input', updateVisuals);
    updateVisuals();

    if (submitBtn && !submitBtn.getAttribute('href')) {
      submitBtn.setAttribute('href', currentUrl());
    }

    if (submitBtn) {
      submitBtn.addEventListener('click', function (evt) {
        if (!window.werduCalcHandoff) {
          return; // graceful fallback: default href navigation still works
        }
        evt.preventDefault();
        var payload = {
          kwh: slider.value,
          plz: plzInput ? plzInput.value : '',
          source: 'homepage-v2-slider'
        };
        var result = window.werduCalcHandoff.persistAndLink(payload, {});
        window.location.href = result.url || currentUrl();
      });
    }
  }

  /* -----------------------------------------------------------
     Section 7 — fly-to-cart animation on successful WooCommerce
     AJAX add-to-cart from the Section 3 variant cards. Uses
     WooCommerce's own "added_to_cart" event, so it works with the
     site's existing ajax_add_to_cart buttons without reinventing
     cart logic.
     ----------------------------------------------------------- */
  function bindAddToCartFly() {
    if (typeof window.jQuery === 'undefined') {
      return;
    }
    window.jQuery(document.body).on('added_to_cart', function (event, fragments, cartHash, $button) {
      if (reduceMotion || !$button || !$button.length) {
        return;
      }
      try {
        flyToCart($button[0]);
      } catch (e) {}
    });
  }

  function flyToCart(buttonEl) {
    var card = buttonEl.closest('.variant-card');
    var sourceImg = card ? card.querySelector('img') : null;
    var cartBadge = document.querySelector('.wcmenucart-contents .cart-value');
    if (!sourceImg || !cartBadge) {
      return;
    }

    var srcRect = sourceImg.getBoundingClientRect();
    var dstRect = cartBadge.getBoundingClientRect();

    var clone = sourceImg.cloneNode(true);
    clone.className = 'wv2-fly-clone';
    clone.style.width = srcRect.width + 'px';
    clone.style.height = srcRect.height + 'px';
    clone.style.left = srcRect.left + 'px';
    clone.style.top = srcRect.top + 'px';

    var dx = (dstRect.left + dstRect.width / 2) - (srcRect.left + srcRect.width / 2);
    var dy = (dstRect.top + dstRect.height / 2) - (srcRect.top + srcRect.height / 2);
    clone.style.setProperty('--dx', dx + 'px');
    clone.style.setProperty('--dy', dy + 'px');

    document.body.appendChild(clone);

    clone.addEventListener('animationend', function () {
      clone.remove();
      cartBadge.classList.add('bump');
      setTimeout(function () {
        cartBadge.classList.remove('bump');
      }, 400);
    }, { once: true });
  }
})();
