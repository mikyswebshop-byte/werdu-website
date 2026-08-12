/**
 * Homepage Calc 2 bridge — wraps Elementor werduCalc()
 * Updates .werdu-calc-cta with Beratung URL params + session/localStorage.
 */
(function () {
  'use strict';

  var MONTH_FACTORS = [0.03, 0.05, 0.09, 0.13, 0.15, 0.15, 0.15, 0.13, 0.10, 0.07, 0.04, 0.02];
  var MONTH_NAMES = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];
  var bridged = false;

  function handoff() {
    return window.werduCalcHandoff || null;
  }

  function recommendKwh(verbrauch, planEl) {
    var kwh = verbrauch <= 4500 ? 16 : (verbrauch <= 7000 ? 16 : 32);
    if (planEl) {
      var opt = planEl.options[planEl.selectedIndex];
      var label = ((opt && opt.text) ? opt.text : '') + ' ' + planEl.value;
      if (/30|32/.test(label)) kwh = 32;
      else if (/\b15\b/.test(label)) kwh = 15;
      else if (/\b16\b/.test(label)) kwh = 16;
      else if (/\b10\b/.test(label)) kwh = 10;
    }
    return kwh;
  }

  function buildMonthly(annualYield, usage, autarky) {
    var monthlyCons = usage / 12;
    return MONTH_FACTORS.map(function (factor, idx) {
      var gen = Math.round(annualYield * factor);
      var cons = Math.round(monthlyCons);
      var stored = Math.round(Math.min(cons * (autarky / 100), gen * 0.55));
      return { month: MONTH_NAMES[idx], generation: gen, consumption: cons, battery: stored };
    });
  }

  function captureAfterCalc() {
    var plzEl = document.getElementById('calc_location');
    var pvEl = document.getElementById('calc_pv_leistung');
    var verbrauchEl = document.getElementById('calc_verbrauch');
    var emailEl = document.getElementById('calc_email');
    var dachEl = document.getElementById('calc_dachneigung');
    var ausrEl = document.getElementById('calc_ausrichtung');
    var planEl = document.getElementById('calc_plan');

    if (!plzEl || !pvEl || !verbrauchEl) return;

    var plz = String(plzEl.value || '').replace(/\D/g, '').substring(0, 5);
    var pv = parseFloat(pvEl.value) || 0;
    var verbrauch = parseFloat(verbrauchEl.value) || 0;
    var email = emailEl ? String(emailEl.value || '').trim() : '';
    var plan = planEl ? String(planEl.value || '') : '';
    if (!pv || !verbrauch) return;

    var dach = parseFloat(dachEl ? dachEl.value : 1) || 1;
    var ausrichtung = parseFloat(ausrEl ? ausrEl.value : 1) || 1;
    var ertrag = Math.round(pv * 950 * dach * ausrichtung);
    var ersparnis = Math.round(ertrag * 0.35 * 0.35);
    var autarkie = Math.min(Math.round((ertrag / verbrauch) * 100), 95);
    var selfConsumption = Math.min(95, Math.round((verbrauch / Math.max(ertrag, 1)) * 100 * 1.2));
    if (ertrag < verbrauch) {
      selfConsumption = Math.min(95, Math.round((ertrag / verbrauch) * 100 * 1.2));
    }
    var kwh = recommendKwh(verbrauch, planEl);

    var data = {
      kwh: String(kwh),
      peak: String(pv),
      pv: String(pv),
      savings: String(ersparnis),
      ersparnis: String(ersparnis),
      plz: plz,
      goal: plan || 'year',
      plan: plan,
      usage: String(Math.round(verbrauch)),
      verbrauch: String(Math.round(verbrauch)),
      autarky: String(autarkie),
      autarkie: String(autarkie),
      selfConsumption: String(selfConsumption),
      email: email,
      annualYield: String(ertrag),
      monthly: buildMonthly(ertrag, verbrauch, autarkie),
      source: 'homepage-calc-2',
      version: 2
    };

    var api = handoff();
    if (api) {
      api.persistAndLink(data, {
        ensureCtaSelector: '#calc-result, .werdu-calc-result',
        ctaLabel: 'Kostenlose Beratung anfordern →'
      });
    } else {
      // Minimal fallback if shared helper not yet loaded
      try {
        sessionStorage.setItem('werdu_calc_result', JSON.stringify(data));
        localStorage.setItem('werdu_calc_data', JSON.stringify(data));
      } catch (e) {}
      var url = '/beratung-anfragen/?' +
        'kwh=' + encodeURIComponent(data.kwh) +
        '&peak=' + encodeURIComponent(data.peak) +
        '&savings=' + encodeURIComponent(data.savings) +
        '&plz=' + encodeURIComponent(data.plz) +
        '&usage=' + encodeURIComponent(data.usage) +
        '&autarky=' + encodeURIComponent(data.autarky) +
        '&verbrauch=' + encodeURIComponent(data.verbrauch) +
        '&ersparnis=' + encodeURIComponent(data.ersparnis) +
        '&autarkie=' + encodeURIComponent(data.autarkie) +
        '&pv=' + encodeURIComponent(data.pv) +
        '&kapazitaet=' + encodeURIComponent(data.kwh) +
        (email ? '&email=' + encodeURIComponent(email) : '') +
        (plan ? '&plan=' + encodeURIComponent(plan) + '&system=' + encodeURIComponent(plan) : '');
      document.querySelectorAll('a.werdu-calc-cta, a[href*="beratung-anfragen"]').forEach(function (a) {
        a.href = url;
      });
    }
  }

  function ensureBaseHref() {
    // Graceful fallback: point CTAs at /beratung-anfragen/ even before any
    // calculation has run, so it never points at a legacy /kontakt/ URL.
    document.querySelectorAll('.werdu-calc-cta, a[href*="/kontakt/"]').forEach(function (el) {
      var text = (el.textContent || '').toLowerCase();
      var isCta = el.classList.contains('werdu-calc-cta') || /beratung|fachanalyse|anfordern/i.test(text);
      if (!isCta) return;
      var href = el.getAttribute('href') || '';
      if (href.indexOf('beratung-anfragen') === -1) {
        el.href = '/beratung-anfragen/';
        el.classList.add('werdu-calc-cta');
      }
    });
  }

  function wrapWerduCalc() {
    if (typeof window.werduCalc !== 'function') return false;
    if (window.werduCalc.__werduBridged) {
      bridged = true;
      return true;
    }

    var original = window.werduCalc;
    window.werduCalc = function () {
      var result = original.apply(this, arguments);
      setTimeout(captureAfterCalc, 0);
      setTimeout(captureAfterCalc, 100);
      return result;
    };
    window.werduCalc.__werduBridged = true;
    bridged = true;
    return true;
  }

  function bindButtonFallback() {
    document.querySelectorAll('button.werdu-calc-btn, #calc-submit, button[onclick*="werduCalc"]').forEach(function (btn) {
      if (btn.dataset.werduBridge === '1') return;
      btn.dataset.werduBridge = '1';
      btn.addEventListener('click', function () {
        setTimeout(captureAfterCalc, 50);
        setTimeout(captureAfterCalc, 250);
      });
    });
  }

  function tryBridge() {
    ensureBaseHref();
    if (!document.getElementById('calc_location')) return bridged;
    wrapWerduCalc();
    bindButtonFallback();
    return bridged;
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', tryBridge);
  } else {
    tryBridge();
  }
  window.addEventListener('load', tryBridge);

  var attempts = 0;
  var timer = setInterval(function () {
    attempts += 1;
    if (tryBridge() || attempts > 60) clearInterval(timer);
  }, 250);
})();
