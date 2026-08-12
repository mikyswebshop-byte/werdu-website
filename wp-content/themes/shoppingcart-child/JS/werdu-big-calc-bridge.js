/**
 * Big Calc (/solarbatterie-rechner/) handoff safety net
 * Works with enqueued werdu-calc.js OR older inlined calculator copies.
 * Reads result DOM + updates Beratung CTA / storage.
 */
(function () {
  'use strict';

  window.__werduBigBridge = true;

  function handoff() {
    return window.werduCalcHandoff || null;
  }

  function numFrom(el) {
    if (!el) return '';
    var data = el.getAttribute('data-value');
    if (data != null && data !== '') return String(data).replace(',', '.');
    var m = String(el.textContent || '').replace(/\u00a0/g, ' ').match(/-?\d+(?:[.,]\d+)?/);
    return m ? m[0].replace(',', '.') : '';
  }

  function captureFromDom() {
    if (!document.getElementById('werdu-calc-isolated')) return;

    var kwh = numFrom(document.getElementById('res-battery'));
    var savings = numFrom(document.getElementById('res-savings')) || numFrom(document.getElementById('cta-savings'));
    var autarky = numFrom(document.getElementById('res-autarky'));
    var plz = '';
    var plzEl = document.getElementById('w-plz');
    if (plzEl) plz = String(plzEl.value || '').replace(/\D/g, '').substring(0, 5);

    var pvEl = document.getElementById('w-pv');
    var peak = pvEl ? String(parseFloat(pvEl.value) || '') : '';

    var usageEl = document.getElementById('w-consumption');
    var usage = usageEl ? String(Math.round(parseFloat(usageEl.value) || 0) || '') : '';

    var seasonEl = document.querySelector('#w-season .werdu-option.selected, [name="w-season"]:checked');
    var goal = 'year';
    if (seasonEl) {
      goal = seasonEl.getAttribute('data-value') || seasonEl.value || 'year';
    }

    if (!kwh || !savings || !peak) return;

    var selfEl = document.getElementById('chart-self-text');
    var selfConsumption = selfEl ? numFrom(selfEl) : '';

    var data = {
      kwh: kwh,
      peak: peak,
      pv: peak,
      savings: savings,
      ersparnis: savings,
      plz: plz,
      usage: usage,
      verbrauch: usage,
      autarky: autarky,
      autarkie: autarky,
      goal: goal,
      selfConsumption: selfConsumption,
      source: 'solarbatterie-rechner-v3',
      version: 2
    };

    var api = handoff();
    if (api) {
      api.persistAndLink(data, {
        ensureCtaSelector: '.werdu-final-cta',
        ctaLabel: 'Kostenlose Fachanalyse anfordern →'
      });
    }

    // Prefer dedicated CTA; also rewrite shop CTA inside final block if needed
    var finalBox = document.querySelector('.werdu-final-cta');
    if (finalBox) {
      var url = (api && api.buildBeratungUrl(data)) || ('/beratung-anfragen/?kwh=' + encodeURIComponent(kwh));
      var cta = document.getElementById('werdu-beratung-cta') || finalBox.querySelector('a.btn, a.werdu-calc-cta');
      if (cta) {
        cta.id = cta.id || 'werdu-beratung-cta';
        cta.href = url;
        cta.classList.add('werdu-calc-cta');
        if (/shop|konfigurieren/i.test(cta.textContent || '')) {
          cta.textContent = 'Kostenlose Fachanalyse anfordern →';
        }
      }
    }
  }

  function observeResults() {
    var root = document.getElementById('werdu-calc-isolated');
    if (!root || root.dataset.werduHandoffObs === '1') return;
    root.dataset.werduHandoffObs = '1';

    var timer = null;
    var obs = new MutationObserver(function () {
      clearTimeout(timer);
      timer = setTimeout(captureFromDom, 50);
    });
    obs.observe(root, { childList: true, subtree: true, characterData: true, attributes: true });

    // Also hook common finish actions
    document.addEventListener('click', function (e) {
      var t = e.target.closest('button, .werdu-btn, [onclick]');
      if (!t) return;
      var label = ((t.textContent || '') + (t.getAttribute('onclick') || '')).toLowerCase();
      if (/berechnen|ergebnis|calculate|wcalculate|wsubmit/.test(label)) {
        setTimeout(captureFromDom, 200);
        setTimeout(captureFromDom, 600);
      }
    });
  }

  function boot() {
    if (!document.getElementById('werdu-calc-isolated')) return;
    observeResults();
    captureFromDom();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
  window.addEventListener('load', boot);
})();
