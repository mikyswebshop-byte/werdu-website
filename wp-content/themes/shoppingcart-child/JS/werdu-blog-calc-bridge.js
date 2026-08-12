/**
 * Blog / Gratis Autarkie-Rechner bridge — wraps calculateWerdu()
 * Redirects CTA from /kontakt/ → /beratung-anfragen/ with calc params.
 */
(function () {
  'use strict';

  var MONTH_FACTORS = [0.03, 0.05, 0.09, 0.13, 0.15, 0.15, 0.15, 0.13, 0.10, 0.07, 0.04, 0.02];
  var MONTH_NAMES = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];
  var bridged = false;

  function handoff() {
    return window.werduCalcHandoff || null;
  }

  function buildMonthly(peak, usage, autarky) {
    var annualYield = (parseFloat(peak) || 8) * 950;
    var monthlyCons = (parseFloat(usage) || 4500) / 12;
    var a = parseFloat(autarky) || 70;
    return MONTH_FACTORS.map(function (factor, idx) {
      var gen = Math.round(annualYield * factor);
      var cons = Math.round(monthlyCons);
      var stored = Math.round(Math.min(cons * (a / 100), gen * 0.55));
      return { month: MONTH_NAMES[idx], generation: gen, consumption: cons, battery: stored };
    });
  }

  function captureAfterCalc() {
    var plzEl = document.getElementById('plz');
    var usageEl = document.getElementById('usage');
    var peakEl = document.getElementById('peakNow');
    var priceEl = document.getElementById('price');
    if (!plzEl || !usageEl) return;

    var plz = String(plzEl.value || '').replace(/\D/g, '').substring(0, 5);
    var usage = parseFloat(usageEl.value) || 0;
    var peakNow = peakEl ? (parseFloat(peakEl.value) || 0) : 0;
    var price = priceEl ? (parseFloat(priceEl.value) || 0.38) : 0.38;

    // Prefer rendered result values when available
    var recKwhEl = document.getElementById('rec-kwh');
    var savingsEl = document.getElementById('savings-text');
    var resTransEl = document.getElementById('res-trans');

    var kwh = 16;
    if (recKwhEl) {
      var parsed = parseFloat(String(recKwhEl.textContent || '').replace(',', '.'));
      if (!isNaN(parsed) && parsed > 0) kwh = Math.round(parsed * 10) / 10;
    }

    var autarky = 70;
    if (resTransEl) {
      var aParsed = parseFloat(String(resTransEl.textContent || '').replace(/[^\d.]/g, ''));
      if (!isNaN(aParsed)) autarky = aParsed;
    }

    var savings = Math.round(usage * (autarky / 100) * price * 0.9);
    if (savingsEl) {
      var sMatch = String(savingsEl.textContent || '').match(/(\d[\d.\s]*)/);
      if (sMatch) {
        var sVal = parseInt(sMatch[1].replace(/[.\s]/g, ''), 10);
        if (!isNaN(sVal) && sVal > 0) savings = sVal;
      }
    }

    // Goal from ambition radios / activeGoal
    var goal = 'summer';
    if (typeof window.activeGoal === 'string' && window.activeGoal) {
      goal = window.activeGoal;
    } else {
      var active = document.querySelector('.werdu-radio-item.active');
      if (active) {
        var g = active.getAttribute('data-goal') || active.getAttribute('onclick') || '';
        if (/winter/i.test(g)) goal = 'winter';
        else if (/trans/i.test(g)) goal = 'trans';
        else goal = 'summer';
      }
    }

    // Peak: use recommended target from verdict if current PV is 0
    var peak = peakNow;
    if (!peak) {
      var verdict = document.getElementById('verdict-main');
      if (verdict) {
        var pMatch = String(verdict.innerHTML || '').match(/([\d.,]+)\s*kWp/i);
        if (pMatch) peak = parseFloat(pMatch[1].replace(',', '.')) || peak;
      }
    }
    if (!peak) peak = Math.max(1, usage / 1000);

    var data = {
      kwh: String(kwh),
      peak: String(Math.round(peak * 10) / 10),
      pv: String(Math.round(peak * 10) / 10),
      savings: String(savings),
      ersparnis: String(savings),
      plz: plz,
      usage: String(Math.round(usage)),
      verbrauch: String(Math.round(usage)),
      autarky: String(Math.round(autarky)),
      autarkie: String(Math.round(autarky)),
      goal: goal,
      selfConsumption: String(Math.min(95, Math.round(autarky * 0.9))),
      monthly: buildMonthly(peak, usage, autarky),
      source: 'gratis-heimspeicher-rechner',
      version: 2
    };

    var api = handoff();
    if (api) {
      api.persistAndLink(data, {
        ensureCtaSelector: '#results, .werdu-results, .werdu-cta-box',
        ctaLabel: 'Jetzt Beratung anfordern →'
      });
    }

    // Force known CTA ids (Elementor markup)
    var url = (api && api.buildBeratungUrl(data)) || (
      '/beratung-anfragen/?kapazitaet=' + encodeURIComponent(kwh) +
      '&kwh=' + encodeURIComponent(kwh) +
      '&ersparnis=' + encodeURIComponent(savings) +
      '&savings=' + encodeURIComponent(savings) +
      '&plz=' + encodeURIComponent(plz)
    );
    ['cta-link', 'wr5-beratung-link'].forEach(function (id) {
      var el = document.getElementById(id);
      if (el) {
        el.href = url;
        el.classList.add('werdu-calc-cta');
      }
    });

    // Rewrite remaining kontakt Beratung CTAs in content
    document.querySelectorAll('a[href*="/kontakt/"]').forEach(function (a) {
      var text = (a.textContent || '').toLowerCase();
      if (/beratung|analyse|angebot anfordern/i.test(text)) {
        a.href = url;
        a.classList.add('werdu-calc-cta');
      }
    });
  }

  function ensureBaseHref() {
    // Graceful fallback: point the CTA at /beratung-anfragen/ even before any
    // calculation has run, so it never points at the legacy /kontakt/ URL.
    var el = document.getElementById('cta-link');
    if (!el) return;
    var href = el.getAttribute('href') || '';
    if (href.indexOf('beratung-anfragen') === -1) {
      el.href = '/beratung-anfragen/';
      el.classList.add('werdu-calc-cta');
    }
  }

  function wrapCalculateWerdu() {
    if (typeof window.calculateWerdu !== 'function') return false;
    if (window.calculateWerdu.__werduBridged) {
      bridged = true;
      return true;
    }
    var original = window.calculateWerdu;
    window.calculateWerdu = function () {
      var result = original.apply(this, arguments);
      setTimeout(captureAfterCalc, 0);
      setTimeout(captureAfterCalc, 150);
      return result;
    };
    window.calculateWerdu.__werduBridged = true;
    bridged = true;
    return true;
  }

  function bindFallback() {
    document.querySelectorAll('button[onclick*="calculateWerdu"]').forEach(function (btn) {
      if (btn.dataset.werduBridge === '1') return;
      btn.dataset.werduBridge = '1';
      btn.addEventListener('click', function () {
        setTimeout(captureAfterCalc, 80);
        setTimeout(captureAfterCalc, 300);
      });
    });
  }

  function tryBridge() {
    ensureBaseHref();
    if (!document.getElementById('plz') || !document.getElementById('usage')) return bridged;
    wrapCalculateWerdu();
    bindFallback();
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
