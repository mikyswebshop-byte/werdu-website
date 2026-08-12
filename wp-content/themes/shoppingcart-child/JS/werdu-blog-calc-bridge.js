/**
 * Blog / Gratis Autarkie-Rechner bridge — wraps calculateWerdu()
 * Redirects CTA from /kontakt/ → /beratung-anfragen/ with calc params.
 */
(function () {
  'use strict';

  var MONTH_FACTORS = [0.03, 0.05, 0.09, 0.13, 0.15, 0.15, 0.15, 0.13, 0.10, 0.07, 0.04, 0.02];
  var MONTH_NAMES = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];
  var bridged = false;
  var lastKnownUrl = null;
  var observerStarted = false;

  function handoff() {
    return window.werduCalcHandoff || null;
  }

  function isTargetPage() {
    return window.location.pathname.indexOf('gratis-heimspeicher-rechner-online') !== -1;
  }

  function currentBeratungUrl() {
    return lastKnownUrl ||
      (window.werduCalcConfig && window.werduCalcConfig.beratungUrl) ||
      '/beratung-anfragen/';
  }

  function isKontaktUrl(value) {
    if (!value) return false;
    return /\/kontakt(\/|$|\?|#)|werdu\.de\/kontakt/i.test(value);
  }

  /**
   * Intercept EVERY link and form action on this page pointing to /kontakt/
   * or werdu.de/kontakt — regardless of button text or CSS class — and
   * redirect it to the current Beratung URL (with calc params once known).
   */
  function rewriteKontaktTargets() {
    if (!isTargetPage()) return;
    var url = currentBeratungUrl();

    document.querySelectorAll('a[href]').forEach(function (a) {
      var href = a.getAttribute('href') || '';
      if (isKontaktUrl(href)) {
        a.setAttribute('href', url);
        a.href = url;
        a.classList.add('werdu-calc-cta');
      }
    });

    document.querySelectorAll('form[action]').forEach(function (f) {
      var action = f.getAttribute('action') || '';
      if (isKontaktUrl(action)) {
        f.setAttribute('action', url);
      }
    });

    guardCtaLinkElement();
  }

  var guardedCtaEl = null;

  /**
   * calculateWerdu() (defined inline in the Elementor page content of
   * /gratis-heimspeicher-rechner-online/, not in a theme/plugin file) sets
   * #cta-link's href to "/kontakt/..." or "https://werdu.de/kontakt/..."
   * after every calculation. Since that function itself cannot be edited
   * here, #cta-link is hardened directly so ANY href change containing
   * "kontakt" — via .href assignment, .setAttribute('href', ...), or any
   * other means — is forced to the current Beratung URL instead.
   */
  function guardCtaLinkElement() {
    if (!isTargetPage()) return;
    var el = document.getElementById('cta-link');
    if (!el || el === guardedCtaEl) return;
    guardedCtaEl = el;

    function sanitize(value) {
      return isKontaktUrl(value) ? currentBeratungUrl() : value;
    }

    // Layer 1: intercept setAttribute('href', ...) synchronously.
    try {
      var originalSetAttribute = el.setAttribute.bind(el);
      el.setAttribute = function (name, value) {
        if (String(name).toLowerCase() === 'href') {
          value = sanitize(value);
        }
        return originalSetAttribute(name, value);
      };
    } catch (e) { /* noop */ }

    // Layer 2: intercept the .href property setter synchronously
    // (e.g. el.href = 'https://werdu.de/kontakt/...').
    try {
      var proto = window.HTMLAnchorElement && window.HTMLAnchorElement.prototype;
      var hrefDescriptor = proto && Object.getOwnPropertyDescriptor(proto, 'href');
      if (hrefDescriptor && hrefDescriptor.configurable && hrefDescriptor.get && hrefDescriptor.set) {
        Object.defineProperty(el, 'href', {
          configurable: true,
          enumerable: true,
          get: function () {
            return hrefDescriptor.get.call(el);
          },
          set: function (value) {
            hrefDescriptor.set.call(el, sanitize(value));
          }
        });
      }
    } catch (e) { /* noop */ }

    // Layer 3: attribute MutationObserver as a final safety net in case
    // something bypasses both interceptors above.
    if (typeof MutationObserver !== 'undefined') {
      var mo = new MutationObserver(function () {
        var current = el.getAttribute('href') || '';
        if (isKontaktUrl(current)) {
          var url = currentBeratungUrl();
          el.setAttribute('href', url);
          el.classList.add('werdu-calc-cta');
        }
      });
      mo.observe(el, { attributes: true, attributeFilter: ['href'] });
    }

    // Enforce immediately in case it's already pointing at /kontakt/.
    var initialHref = el.getAttribute('href') || '';
    if (isKontaktUrl(initialHref)) {
      el.setAttribute('href', currentBeratungUrl());
      el.classList.add('werdu-calc-cta');
    }
  }

  function startKontaktObserver() {
    if (observerStarted || !isTargetPage() || typeof MutationObserver === 'undefined') return;
    observerStarted = true;
    var mo = new MutationObserver(function () {
      rewriteKontaktTargets();
    });
    mo.observe(document.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: ['href', 'action'] });
  }

  function findActionableAncestor(el) {
    var node = el;
    var depth = 0;
    while (node && node.nodeType === 1 && depth < 6) {
      var tag = node.tagName;
      if (tag === 'A' || tag === 'BUTTON' ||
          (node.getAttribute && (node.getAttribute('onclick') || node.getAttribute('role') === 'button'))) {
        return node;
      }
      node = node.parentElement;
      depth++;
    }
    return el;
  }

  /**
   * Matches the "Analyse & Angebot anfordern" CTA (and shorter "Analyse"
   * variants, e.g. on mobile) of the PV-Autarkie-Rechner, regardless of tag,
   * CSS class, or how its click handler was attached (inline onclick,
   * addEventListener, jQuery, etc.).
   */
  function isAnalyseCtaElement(el) {
    if (!el || !el.textContent) return false;
    var text = el.textContent.replace(/\s+/g, ' ').trim();
    if (!text || text.length > 60) return false;
    return /analyse\s*&?\s*angebot\s*anfordern/i.test(text) || /^analyse$/i.test(text);
  }

  var analyseGuardInstalled = false;

  /**
   * Capture-phase click listener installed on document: fires BEFORE any
   * inline onclick or addEventListener handler on the clicked button, so we
   * can block a hardcoded window.location.href/window.open('/kontakt/')
   * redirect and send the user to the current Beratung URL instead — no
   * matter how the original handler was implemented (it lives in Elementor
   * page content in the database, not in a theme file we can edit directly).
   */
  function installAnalyseClickGuard() {
    if (analyseGuardInstalled || !isTargetPage()) return;
    analyseGuardInstalled = true;
    document.addEventListener('click', function (evt) {
      var actionable = findActionableAncestor(evt.target);
      if (!isAnalyseCtaElement(actionable)) return;
      evt.preventDefault();
      evt.stopImmediatePropagation();
      evt.stopPropagation();
      window.location.href = currentBeratungUrl();
    }, true);
  }

  var windowOpenGuardInstalled = false;

  /**
   * Monkey-patch window.open so any JS-based window.open('/kontakt/', ...)
   * or window.open('https://werdu.de/kontakt/...') on this page is
   * redirected to the Beratung URL instead.
   */
  function installWindowOpenGuard() {
    if (windowOpenGuardInstalled || !isTargetPage() || typeof window.open !== 'function') return;
    windowOpenGuardInstalled = true;
    var originalOpen = window.open;
    window.open = function (url) {
      var args = Array.prototype.slice.call(arguments);
      if (isKontaktUrl(url)) {
        args[0] = currentBeratungUrl();
      }
      return originalOpen.apply(window, args);
    };
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
    var beratungBase = (window.werduCalcConfig && window.werduCalcConfig.beratungUrl) || '/beratung-anfragen/';
    var url = (api && api.buildBeratungUrl(data)) || (
      beratungBase + '?kapazitaet=' + encodeURIComponent(kwh) +
      '&kwh=' + encodeURIComponent(kwh) +
      '&ersparnis=' + encodeURIComponent(savings) +
      '&savings=' + encodeURIComponent(savings) +
      '&plz=' + encodeURIComponent(plz)
    );
    lastKnownUrl = url;
    ['cta-link', 'wr5-beratung-link'].forEach(function (id) {
      var el = document.getElementById(id);
      if (el) {
        el.href = url;
        el.classList.add('werdu-calc-cta');
      }
    });

    // Intercept EVERY remaining /kontakt/ link or form action on this page,
    // regardless of button text or CSS class.
    rewriteKontaktTargets();
  }

  function ensureBaseHref() {
    // Graceful fallback: point the CTA at /beratung-anfragen/ (current
    // environment, via home_url() when available) even before any
    // calculation has run, so it never points at the legacy /kontakt/ URL.
    var el = document.getElementById('cta-link');
    if (el) {
      var base = (window.werduCalcConfig && window.werduCalcConfig.beratungUrl) || '/beratung-anfragen/';
      var href = el.getAttribute('href') || '';
      if (href.indexOf('beratung-anfragen') === -1) {
        el.href = base;
        el.classList.add('werdu-calc-cta');
      }
    }
    // Catch every other /kontakt/ link or form action on this page too,
    // even before a calculation has run.
    rewriteKontaktTargets();
    startKontaktObserver();
    installAnalyseClickGuard();
    installWindowOpenGuard();
  }

  // Install the click/window.open guards immediately (does not need to wait
  // for DOMContentLoaded — a capture listener on document is safe to attach
  // as soon as this script runs, and window.open can be patched right away).
  installAnalyseClickGuard();
  installWindowOpenGuard();

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
