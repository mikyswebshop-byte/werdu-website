/**
 * /solarbatterie-rechner/ bridge — live container #werdu-calc-isolated
 * Plus fact fixes: real product price for Amortisation, AIO vs Batterie labels, table facts.
 */
(function (window, document) {
  'use strict';

  var ROOT_ID = 'werdu-calc-isolated';
  var lastSignature = '';
  var PRODUCT_PRICES = {
    'prod-10': 2799,
    'prod-16green': 1990,
    'prod-16': 2345,
    'prod-15aio': 2899,
    'prod-30': 3499,
    'prod-30aio': 4839
  };
  var AIO_IDS = { 'prod-10': 1, 'prod-15aio': 1, 'prod-30aio': 1 };

  function textOf(id) {
    var el = document.getElementById(id);
    return el ? String(el.textContent || el.value || '').trim() : '';
  }

  function val(id) {
    var el = document.getElementById(id);
    return el ? String(el.value || '').trim() : '';
  }

  function dataValue(id) {
    var el = document.getElementById(id);
    if (!el) {
      return '';
    }
    if (el.getAttribute('data-value')) {
      return el.getAttribute('data-value');
    }
    return textOf(id);
  }

  function parse(value) {
    return window.werduCalcHandoff ? window.werduCalcHandoff.parseNumber(value) : value;
  }

  function readMonthly() {
    var bars = document.querySelectorAll('#monthly-chart .werdu-month-bar');
    var months = [];
    var i;
    for (i = 0; i < bars.length; i++) {
      months.push({
        month: i + 1,
        yield: parseFloat(bars[i].getAttribute('data-yield') || bars[i].style.height) || 0,
        usage: 0,
        battery: parseFloat(bars[i].getAttribute('data-battery') || 0) || 0
      });
    }
    return months;
  }

  function bestProductId() {
    var best = document.querySelector('#' + ROOT_ID + ' .werdu-product-item.best-match');
    return best && best.id ? best.id : '';
  }

  function euro(n) {
    return Math.round(n).toLocaleString('de-DE') + ' €';
  }

  function fixFacts() {
    var root = document.getElementById(ROOT_ID);
    if (!root) {
      return;
    }

    // Fill missing product prices from shop facts
    Object.keys(PRODUCT_PRICES).forEach(function (id) {
      var card = document.getElementById(id);
      if (!card) {
        return;
      }
      var priceEl = card.querySelector('.werdu-product-price');
      if (priceEl && (!priceEl.textContent || priceEl.textContent.indexOf('—') !== -1 || !/\d/.test(priceEl.textContent))) {
        priceEl.textContent = euro(PRODUCT_PRICES[id]);
      }
      var typeEl = card.querySelector('.werdu-product-type');
      if (typeEl && AIO_IDS[id]) {
        typeEl.textContent = id === 'prod-30aio' ? 'All-in-One 3-phasig (Batterie + WR)' : 'All-in-One (Batterie + Wechselrichter)';
      } else if (typeEl && /Batterie/i.test(typeEl.textContent || '')) {
        typeEl.textContent = 'Nur Batterie (ohne Wechselrichter)';
      }
    });

    // Comparison table facts (no 10 kWh battery; Basen from 1.990; 5y warranty; Basen delivery)
    var rows = root.querySelectorAll('.werdu-compare-table tbody tr');
    var r;
    for (r = 0; r < rows.length; r++) {
      var th = rows[r].querySelector('th');
      var td = rows[r].querySelector('td.price-werdu, td:nth-child(2)');
      if (!th || !td) {
        continue;
      }
      var label = (th.textContent || '').replace(/\s+/g, ' ').trim();
      if (/Preis\s*10/i.test(label) || /10-16/i.test(label)) {
        th.textContent = 'Preis Batterie ab 16 kWh';
        td.textContent = 'ab 1.990 €';
        td.classList.add('price-werdu');
      } else if (/Garantie/i.test(label)) {
        td.textContent = '5 Jahre';
      } else if (/Lieferzeit/i.test(label)) {
        td.textContent = 'Basen Green 5–10 Werktage · sonst produktbezogen im Shop';
      }
    }

    // Guarantee strip under CTA
    var subs = root.querySelectorAll('.werdu-final-cta .sub');
    var s;
    for (s = 0; s < subs.length; s++) {
      subs[s].innerHTML = subs[s].innerHTML.replace(/10\s*Jahre\s*Garantie/gi, '5 Jahre Garantie');
    }

    // Remove duplicate Beratung/Fachanalyse anchors injected as plain links
    var finalBox = root.querySelector('.werdu-final-cta');
    if (finalBox) {
      var anchors = finalBox.querySelectorAll('a');
      var primary = document.getElementById('wr5-cta-link') || document.getElementById('werdu-beratung-cta');
      var a;
      for (a = 0; a < anchors.length; a++) {
        var el = anchors[a];
        if (el.classList.contains('werdu-calc-cta-bridge')) {
          el.parentNode.removeChild(el);
          continue;
        }
        if (primary && el !== primary && /Fachanalyse|Beratung anfragen/i.test(el.textContent || '')) {
          if (!el.classList.contains('btn') && !el.classList.contains('werdu-cta-secondary')) {
            el.parentNode.removeChild(el);
          }
        }
      }
      if (primary && /Fachanalyse/i.test(primary.textContent || '')) {
        primary.textContent = 'Kostenlose Beratung anfragen';
      }
    }

    // Amortisation must use real product price, not kWh * pauschal
    var pid = bestProductId();
    var price = pid && PRODUCT_PRICES[pid] ? PRODUCT_PRICES[pid] : 0;
    var savings = parseFloat(dataValue('res-savings'), 10);
    if (!isFinite(savings) || savings <= 0) {
      savings = parse(textOf('res-savings'));
    }
    if (price > 0 && savings > 0) {
      var roi = price / savings;
      var roiLabel = roi <= 20 ? roi.toFixed(1) : '>20';
      var resRoi = document.getElementById('res-roi');
      if (resRoi) {
        resRoi.textContent = roiLabel + ' Jahre';
        resRoi.setAttribute('data-value', roi <= 20 ? roi.toFixed(1) : '20+');
      }
      var roiYear = document.getElementById('roi-year');
      if (roiYear) {
        roiYear.textContent = String(Math.ceil(roi));
      }
      var roiText = document.getElementById('roi-text');
      if (roiText) {
        roiText.textContent = Math.ceil(roi) + ' Jahren';
      }
    }

    // Capacity KPI: make All-in-One explicit (no standalone 10 kWh battery)
    var resBattery = document.getElementById('res-battery');
    if (resBattery && pid) {
      var kwh = dataValue('res-battery') || (textOf('res-battery').match(/[\d.,]+/) || [''])[0];
      if (AIO_IDS[pid]) {
        resBattery.textContent = kwh + ' kWh All-in-One';
      } else {
        resBattery.textContent = kwh + ' kWh Batterie';
      }
    }
  }

  function collectPayload() {
    var savings = dataValue('res-savings') || dataValue('cta-savings') || textOf('res-savings');
    return {
      kwh: parse(dataValue('res-battery') || textOf('res-battery')),
      peak: parse(val('w-pv')),
      savings: parse(savings),
      plz: val('w-plz'),
      usage: parse(val('w-consumption')),
      autarky: parse(dataValue('res-autarky') || textOf('res-autarky')),
      selfConsumption: '',
      goal: (function () {
        var selected = document.querySelector('#w-season .werdu-option.selected');
        return selected ? (selected.getAttribute('data-value') || selected.textContent.trim()) : '';
      })(),
      email: '',
      plan: bestProductId() || '',
      roi: parse(dataValue('res-roi') || textOf('res-roi')),
      annualYield: '',
      monthly: readMonthly(),
      installerRequested: false,
      source: 'solarbatterie-rechner'
    };
  }

  function signature(payload) {
    return [payload.kwh, payload.savings, payload.autarky, payload.peak, payload.usage, payload.plan, payload.roi].join('|');
  }

  function runHandoff() {
    fixFacts();
    if (!window.werduCalcHandoff) {
      return;
    }
    var payload = collectPayload();
    var sig = signature(payload);
    if (!payload.kwh && !payload.savings) {
      window.werduCalcHandoff.ensureBaseHref();
      rewriteFinalCta(window.werduCalcHandoff.buildUrl(payload));
      return;
    }
    if (sig === lastSignature) {
      return;
    }
    lastSignature = sig;
    var result = window.werduCalcHandoff.persistAndLink(payload);
    rewriteFinalCta(result.url);
  }

  function rewriteFinalCta(url) {
    var primary = document.getElementById('wr5-cta-link') || document.getElementById('werdu-beratung-cta');
    if (primary) {
      primary.setAttribute('href', url);
      primary.classList.add('werdu-calc-cta');
      return;
    }
    var links = document.querySelectorAll('#' + ROOT_ID + ' .werdu-final-cta a.btn');
    var i;
    for (i = 0; i < links.length; i++) {
      links[i].setAttribute('href', url);
      links[i].classList.add('werdu-calc-cta');
    }
  }

  function observe() {
    var root = document.getElementById(ROOT_ID);
    if (!root || root.__werduHandoffObserved) {
      return;
    }
    root.__werduHandoffObserved = true;
    var timer = null;
    var mo = new MutationObserver(function () {
      window.clearTimeout(timer);
      timer = window.setTimeout(runHandoff, 80);
    });
    mo.observe(root, {
      subtree: true,
      childList: true,
      characterData: true,
      attributes: true,
      attributeFilter: ['data-value', 'style', 'class']
    });
  }

  function bindClicks() {
    document.addEventListener(
      'click',
      function (e) {
        var t = e.target;
        if (!t) {
          return;
        }
        var btn = t.closest ? t.closest('button, a, input') : t;
        var text = ((btn && (btn.textContent || btn.value)) || '').toLowerCase();
        if (/berechnen|berechnung|ergebnis|calculate/.test(text)) {
          window.setTimeout(runHandoff, 120);
          window.setTimeout(runHandoff, 600);
        }
      },
      true
    );
  }

  function wrapCalculate() {
    if (typeof window.wCalculate === 'function' && !window.wCalculate.__werduHandoffWrapped) {
      var orig = window.wCalculate;
      window.wCalculate = function () {
        var r = orig.apply(this, arguments);
        window.setTimeout(runHandoff, 50);
        window.setTimeout(fixFacts, 100);
        return r;
      };
      window.wCalculate.__werduHandoffWrapped = true;
    }
  }

  function applyIncomingFields() {
    var q = new URLSearchParams(window.location.search || '');
    var stored = window.werduCalcHandoff ? window.werduCalcHandoff.readStored() : null;
    var plz = (q.get('plz') || (stored && stored.plz) || '').replace(/\D/g, '').slice(0, 5);
    var usageRaw = q.get('usage') || q.get('verbrauch') || (stored && stored.usage) || '';
    var usage = parseFloat(String(usageRaw).replace(',', '.'), 10);
    var plzEl = document.getElementById('w-plz');
    if (plzEl && plz.length === 5) {
      plzEl.value = plz;
    }
    var cons = document.getElementById('w-consumption');
    var disp = document.getElementById('w-consumption-val');
    if (cons && isFinite(usage) && usage >= 1000) {
      var max = parseFloat(cons.max) || 15000;
      var min = parseFloat(cons.min) || 1000;
      var clipped = Math.min(max, Math.max(min, usage));
      cons.value = String(clipped);
      if (disp) {
        disp.textContent = clipped.toLocaleString('de-DE') + ' kWh/Jahr';
      }
      cons.dispatchEvent(new Event('input', { bubbles: true }));
    }
  }

  function boot() {
    if (!document.getElementById(ROOT_ID)) {
      return;
    }
    fixFacts();
    if (!window.werduCalcHandoff) {
      window.setTimeout(boot, 120);
      return;
    }
    applyIncomingFields();
    window.setTimeout(applyIncomingFields, 80);
    window.setTimeout(applyIncomingFields, 400);
    observe();
    bindClicks();
    wrapCalculate();
    window.werduCalcHandoff.ensureBaseHref();
    window.setTimeout(wrapCalculate, 800);
    window.setTimeout(runHandoff, 200);
    window.setTimeout(fixFacts, 500);
  }

  document.addEventListener('werdu-calc-handoff-ready', boot);
  if (document.readyState !== 'loading') {
    boot();
  } else {
    document.addEventListener('DOMContentLoaded', boot);
  }
})(window, document);
