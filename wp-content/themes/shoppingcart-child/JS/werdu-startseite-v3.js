/**
 * WERDU Homepage LP — calculator + handoff
 * Field IDs stay compatible with werdu-homepage-calc-bridge.js
 */
(function () {
  'use strict';

  function beratungBase() {
    try {
      if (window.werduCalcConfig && window.werduCalcConfig.beratungUrl) {
        return window.werduCalcConfig.beratungUrl;
      }
    } catch (e) {}
    return '/beratung-anfragen/';
  }

  function recommendKwh(verbrauch, planVal) {
    var kwh = verbrauch <= 4500 ? 16 : (verbrauch <= 7000 ? 16 : 32);
    var label = String(planVal || '');
    if (/30|32/.test(label)) kwh = 32;
    else if (/\b15\b/.test(label) || /aio/i.test(label)) kwh = 15;
    else if (/\b16\b/.test(label)) kwh = 16;
    else if (/\b10\b/.test(label)) kwh = 10;
    return kwh;
  }

  window.werduCalc = function werduCalc() {
    var plzEl = document.getElementById('calc_location');
    var pvEl = document.getElementById('calc_pv_leistung');
    var verbrauchEl = document.getElementById('calc_verbrauch');
    var emailEl = document.getElementById('calc_email');
    var planEl = document.getElementById('calc_plan');
    var result = document.getElementById('calc-result');

    if (!plzEl || !pvEl || !verbrauchEl) return;

    var plz = String(plzEl.value || '').replace(/\D/g, '').substring(0, 5);
    var pv = parseFloat(pvEl.value) || 0;
    var verbrauch = parseFloat(verbrauchEl.value) || 0;
    if (!pv || !verbrauch) {
      if (plzEl.reportValidity) {
        pvEl.required = true;
        verbrauchEl.required = true;
        pvEl.reportValidity();
      }
      return;
    }

    var dachEl = document.getElementById('calc_dachneigung');
    var ausrEl = document.getElementById('calc_ausrichtung');
    var dach = parseFloat(dachEl ? dachEl.value : 1) || 1;
    var ausrichtung = parseFloat(ausrEl ? ausrEl.value : 1) || 1;
    var ertrag = Math.round(pv * 950 * dach * ausrichtung);
    var ersparnis = Math.round(ertrag * 0.35 * 0.35);
    var autarkie = Math.min(Math.round((ertrag / verbrauch) * 100), 95);
    var kwh = recommendKwh(verbrauch, planEl ? planEl.value : '');
    var email = emailEl ? String(emailEl.value || '').trim() : '';
    var plan = planEl ? String(planEl.value || '') : '';

    var data = {
      kwh: String(kwh),
      peak: String(pv),
      pv: String(pv),
      savings: String(ersparnis),
      ersparnis: String(ersparnis),
      plz: plz,
      usage: String(Math.round(verbrauch)),
      verbrauch: String(Math.round(verbrauch)),
      autarky: String(autarkie),
      autarkie: String(autarkie),
      email: email,
      plan: plan,
      source: 'homepage-lp',
      version: 2
    };

    var url = beratungBase();
    if (window.werduCalcHandoff) {
      url = window.werduCalcHandoff.persistAndLink(data, {}).url || url;
    }

    if (result) {
      var kwhEl = document.getElementById('whp-res-kwh');
      var autEl = document.getElementById('whp-res-autarkie');
      var savEl = document.getElementById('whp-res-spar');
      var cta = result.querySelector('.whp-calc-cta, .werdu-calc-cta');
      if (kwhEl) kwhEl.textContent = kwh + ' kWh';
      if (autEl) autEl.textContent = autarkie + ' %';
      if (savEl) savEl.textContent = 'ca. ' + ersparnis.toLocaleString('de-DE') + ' €';
      if (cta) cta.setAttribute('href', url);
      result.classList.add('is-open');
      result.hidden = false;
      result.style.display = 'block';
    }
  };

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('pv-calculator');
    if (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        window.werduCalc();
      });
    }
    var btn = document.getElementById('calc-submit');
    if (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        window.werduCalc();
      });
    }
  });
})();
