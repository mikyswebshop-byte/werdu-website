/**
 * WERDU Calc Handoff — shared URL + storage helpers for all calculators
 * Persists to sessionStorage + localStorage and builds /beratung-anfragen/ URLs.
 */
(function (w) {
  'use strict';

  var BERATUNG_PATH = '/beratung-anfragen/';
  var STORAGE_KEY = 'werdu_calc_result';
  var LEGACY_KEY = 'werdu_calc_data';

  function beratungBase() {
    try {
      return w.location.origin + BERATUNG_PATH;
    } catch (e) {
      return BERATUNG_PATH;
    }
  }

  function clean(val) {
    if (val === null || val === undefined) return '';
    return String(val).trim();
  }

  function stripNum(val) {
    var s = clean(val)
      .replace(/\u00a0/g, ' ')
      .replace(/[€$]/g, '')
      .replace(/\s*(kWh|kWp|kw|Wh|Wp|ct|EUR|Euro|Autarkie|%)\s*/gi, '')
      .replace(/\s+/g, '')
      .replace(',', '.');
    var m = s.match(/-?\d+(?:\.\d+)?/);
    return m ? m[0] : '';
  }

  /**
   * Normalize any calculator payload into the canonical handoff shape.
   */
  function normalize(raw) {
    raw = raw || {};
    var kwh = stripNum(raw.kwh != null ? raw.kwh : raw.capacity);
    var peak = stripNum(raw.peak != null ? raw.peak : (raw.pv != null ? raw.pv : raw.peakNow));
    var savings = stripNum(raw.savings != null ? raw.savings : (raw.ersparnis != null ? raw.ersparnis : raw.yearlySavings));
    var plz = clean(raw.plz != null ? raw.plz : raw.location).replace(/\D/g, '').substring(0, 5);
    var usage = stripNum(raw.usage != null ? raw.usage : raw.verbrauch);
    var autarky = stripNum(raw.autarky != null ? raw.autarky : raw.autarkie);
    var selfConsumption = stripNum(raw.selfConsumption != null ? raw.selfConsumption : raw.self);
    var goal = clean(raw.goal != null ? raw.goal : raw.plan) || 'year';
    var email = clean(raw.email || '');
    var plan = clean(raw.plan || raw.system || '');
    var roi = stripNum(raw.roi != null ? raw.roi : raw.amortisation);

    return {
      kwh: kwh,
      peak: peak,
      savings: savings,
      plz: plz,
      usage: usage,
      autarky: autarky,
      selfConsumption: selfConsumption,
      goal: goal,
      email: email,
      plan: plan,
      roi: roi,
      annualYield: stripNum(raw.annualYield || raw.ertrag || ''),
      monthly: Array.isArray(raw.monthly) ? raw.monthly : null,
      installerRequested: !!raw.installerRequested,
      source: clean(raw.source) || 'calc',
      version: 2
    };
  }

  function buildBeratungUrl(data) {
    var d = normalize(data);
    var q = [];
    function add(key, val) {
      if (val === null || val === undefined || val === '') return;
      q.push(encodeURIComponent(key) + '=' + encodeURIComponent(val));
    }
    add('kwh', d.kwh);
    add('peak', d.peak);
    add('savings', d.savings);
    add('plz', d.plz);
    add('goal', d.goal);
    add('usage', d.usage);
    add('autarky', d.autarky);
    add('self', d.selfConsumption);
    // Aliases for older / alternate consumers
    add('verbrauch', d.usage);
    add('ersparnis', d.savings);
    add('autarkie', d.autarky);
    add('pv', d.peak);
    add('kapazitaet', d.kwh);
    if (d.roi) {
      add('roi', d.roi);
      add('amortisation', d.roi);
    }
    if (d.email) add('email', d.email);
    if (d.plan) {
      add('plan', d.plan);
      add('system', d.plan);
    }
    if (d.installerRequested) add('installer', '1');
    return beratungBase() + (q.length ? '?' + q.join('&') : '');
  }

  function persist(data) {
    var d = normalize(data);
    try {
      w.sessionStorage.setItem(STORAGE_KEY, JSON.stringify(d));
    } catch (e) {}
    try {
      w.localStorage.setItem(STORAGE_KEY, JSON.stringify(d));
      w.localStorage.setItem(LEGACY_KEY, JSON.stringify({
        plz: d.plz,
        pv: d.peak,
        verbrauch: d.usage,
        email: d.email,
        plan: d.plan || d.goal,
        system: d.plan,
        autarkie: d.autarky,
        ersparnis: d.savings,
        kwh: d.kwh,
        kapazitaet: d.kwh,
        peak: d.peak,
        savings: d.savings,
        usage: d.usage,
        autarky: d.autarky,
        roi: d.roi,
        amortisation: d.roi
      }));
    } catch (e2) {}
    return d;
  }

  function isCalcCta(anchor) {
    if (!anchor || !anchor.tagName || anchor.tagName.toLowerCase() !== 'a') return false;
    if (anchor.classList.contains('werdu-calc-cta')) return true;
    if (anchor.id === 'werdu-beratung-cta' || anchor.id === 'wr5-beratung-link' ||
        anchor.id === 'wr5-bottom-beratung-link' || anchor.id === 'cta-link') {
      return true;
    }
    var href = (anchor.getAttribute('href') || '').toLowerCase();
    var text = (anchor.textContent || '').toLowerCase();
    var pointsBeratung = href.indexOf('beratung-anfragen') !== -1 || href.indexOf('/kontakt') !== -1;
    var looksLikeCta = /beratung|fachanalyse|anfordern|analyse\s*&\s*angebot/i.test(text);
    var inCalc = !!(
      anchor.closest('#werdu-calc-isolated, #solarbatterie-rechner, .werdu-calc-section, .werdu-calc-result, #calc-result, #results, .wr5-beratung-cta, .werdu-final-cta, .werdu-cta-box')
    );
    return (pointsBeratung && (looksLikeCta || inCalc)) || (looksLikeCta && inCalc);
  }

  function updateCtas(url) {
    var updated = 0;
    Array.prototype.forEach.call(document.querySelectorAll('a'), function (a) {
      if (!isCalcCta(a)) return;
      a.href = url;
      a.classList.add('werdu-calc-cta');
      updated += 1;
    });
    return updated;
  }

  /**
   * Persist result + rewrite Beratung CTAs. Returns the normalized data + url.
   */
  function persistAndLink(raw, opts) {
    opts = opts || {};
    var data = persist(raw);
    var url = buildBeratungUrl(data);
    updateCtas(url);

    if (opts.ensureCtaSelector) {
      var host = document.querySelector(opts.ensureCtaSelector);
      if (host && !host.querySelector('a.werdu-calc-cta, #werdu-beratung-cta, #cta-link')) {
        var link = document.createElement('a');
        link.className = 'werdu-calc-cta werdu-calc-cta-bridge';
        link.id = 'werdu-beratung-cta';
        link.href = url;
        link.textContent = opts.ctaLabel || 'Kostenlose Fachanalyse anfordern →';
        link.style.cssText = opts.ctaStyle || 'display:inline-block;margin-top:16px;background:#FF6600;color:#fff;font-weight:800;padding:14px 22px;border-radius:12px;text-decoration:none;';
        host.appendChild(link);
      }
    }

    return { data: data, url: url };
  }

  w.werduCalcHandoff = {
    normalize: normalize,
    buildBeratungUrl: buildBeratungUrl,
    persist: persist,
    updateCtas: updateCtas,
    persistAndLink: persistAndLink,
    STORAGE_KEY: STORAGE_KEY,
    LEGACY_KEY: LEGACY_KEY
  };
})(window);
