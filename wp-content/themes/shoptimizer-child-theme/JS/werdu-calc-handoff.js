/**
 * Werdu Calculator → Beratung handoff (shared)
 */
(function (window, document) {
  'use strict';

  var STORAGE_KEY = 'werduCalcHandoff';
  var CTA_TEXT_RE = /beratung|fachanalyse|anfordern/i;
  var KONTAKT_RE = /\/kontakt\/?/i;

  function config() {
    return window.werduCalcConfig || {};
  }

  function beratungBase() {
    var url = (config().beratungUrl || '/beratung-anfragen/').toString();
    if (url.charAt(url.length - 1) !== '/') {
      url += '/';
    }
    return url;
  }

  function parseNumber(value) {
    if (typeof value === 'number' && isFinite(value)) {
      return value;
    }
    var raw = String(value).replace(/\s/g, '').replace(/€/g, '');
    if (raw === '-' || raw === '--' || raw === '\u2013' || raw === '') {
      return '';
    }
    var digitAt = raw.search(/\d/);
    var leading = digitAt === -1 ? '' : raw.slice(digitAt).replace(/[^\d.,].*$/, '');
    var s = leading.replace(/\./g, '').replace(/,/g, '.');
    var num = parseFloat(s);
    return isNaN(num) ? '' : num;
  }

  function clean(value) {
    if (value === null || value === undefined) {
      return '';
    }
    return String(value).trim();
  }

  function first() {
    var i;
    for (i = 0; i < arguments.length; i++) {
      if (arguments[i] !== undefined && arguments[i] !== null && arguments[i] !== '') {
        return arguments[i];
      }
    }
    return '';
  }

  function normalize(raw) {
    raw = raw || {};
    var monthly = Array.isArray(raw.monthly) ? raw.monthly : [];
    return {
      kwh: parseNumber(first(raw.kwh, raw.kapazitaet, raw.capacity)),
      peak: parseNumber(first(raw.peak, raw.pv, raw.pvLeistung)),
      savings: parseNumber(first(raw.savings, raw.ersparnis)),
      plz: clean(first(raw.plz, raw.location)),
      usage: parseNumber(first(raw.usage, raw.verbrauch, raw.consumption)),
      autarky: parseNumber(first(raw.autarky, raw.autarkie)),
      selfConsumption: parseNumber(first(raw.selfConsumption, raw.eigenverbrauch)),
      goal: clean(first(raw.goal, raw.ziel)),
      email: clean(raw.email),
      plan: clean(first(raw.plan, raw.system)),
      roi: parseNumber(first(raw.roi, raw.amortisation, raw.amort)),
      annualYield: parseNumber(first(raw.annualYield, raw.ertrag, raw.jahresertrag)),
      monthly: monthly,
      installerRequested: !!raw.installerRequested,
      source: clean(raw.source || 'calculator')
    };
  }

  function toQuery(payload) {
    var p = normalize(payload);
    var params = new URLSearchParams();
    function setBoth(a, b, value) {
      if (value === '' || value === null || value === undefined) {
        return;
      }
      params.set(a, String(value));
      if (b) {
        params.set(b, String(value));
      }
    }
    setBoth('kwh', 'kapazitaet', p.kwh);
    setBoth('savings', 'ersparnis', p.savings);
    setBoth('autarky', 'autarkie', p.autarky);
    setBoth('peak', 'pv', p.peak);
    setBoth('plan', 'system', p.plan);
    setBoth('roi', 'amortisation', p.roi);
    if (p.plz) params.set('plz', p.plz);
    if (p.usage) params.set('usage', String(p.usage));
    if (p.selfConsumption) params.set('selfConsumption', String(p.selfConsumption));
    if (p.goal) params.set('goal', p.goal);
    if (p.email) params.set('email', p.email);
    if (p.annualYield) params.set('annualYield', String(p.annualYield));
    if (p.source) params.set('source', p.source);
    if (p.installerRequested) params.set('installerRequested', '1');
    if (p.monthly && p.monthly.length) {
      try {
        params.set('monthly', JSON.stringify(p.monthly));
      } catch (e) { /* ignore */ }
    }
    return params.toString();
  }

  function buildUrl(payload) {
    var base = beratungBase();
    var q = toQuery(payload);
    return q ? base + '?' + q : base;
  }

  function persist(payload) {
    var data = normalize(payload);
    if (!data.kwh && !data.savings) {
      return data;
    }
    var json = JSON.stringify(data);
    try {
      window.sessionStorage.setItem(STORAGE_KEY, json);
    } catch (e1) { /* private mode */ }
    try {
      window.localStorage.removeItem(STORAGE_KEY);
    } catch (e2) { /* ignore */ }
    return data;
  }

  function readStored() {
    var json = '';
    try {
      json = window.sessionStorage.getItem(STORAGE_KEY) || '';
    } catch (e1) { json = ''; }
    if (!json) {
      return null;
    }
    try {
      return normalize(JSON.parse(json));
    } catch (e3) {
      return null;
    }
  }

  function isBeratungCta(el) {
    if (!el || el.nodeType !== 1) {
      return false;
    }
    if (el.classList && el.classList.contains('werdu-calc-cta')) {
      return true;
    }
    var id = (el.id || '').toLowerCase();
    if (
      id === 'werdu-beratung-cta' ||
      id === 'cta-link' ||
      id === 'wr5-cta-link' ||
      id === 'wr5-beratung-link' ||
      id === 'wr5-bottom-beratung-link' ||
      id === 'calc-cta-link'
    ) {
      return true;
    }
    var text = (el.textContent || el.value || '').replace(/\s+/g, ' ');
    return CTA_TEXT_RE.test(text);
  }

  function applyHref(el, url) {
    if (!el || !url) {
      return;
    }
    if (el.tagName === 'A') {
      el.setAttribute('href', url);
    } else if (el.tagName === 'FORM') {
      el.setAttribute('action', url);
    } else if (el.dataset) {
      el.dataset.werduBeratungUrl = url;
    }
  }

  function rewriteCtas(payload) {
    var url = buildUrl(payload);
    var nodes = document.querySelectorAll(
      'a, button, input[type="submit"], input[type="button"], [data-werdu-cta]'
    );
    var i;
    for (i = 0; i < nodes.length; i++) {
      if (isBeratungCta(nodes[i])) {
        applyHref(nodes[i], url);
      }
    }
    return url;
  }

  function persistAndLink(payload) {
    var data = persist(payload);
    var url = rewriteCtas(data);
    rewriteKontaktLinks();
    return { data: data, url: url };
  }

  function ensureBaseHref() {
    rewriteCtas(readStored() || {});
    rewriteKontaktLinks();
  }

  function isKontaktUrl(url) {
    if (!url) {
      return false;
    }
    var s = String(url);
    if (/^mailto:|^tel:|^javascript:/i.test(s)) {
      return false;
    }
    return /(?:werdu\.de)?\/kontakt(?:\/|\?|#|$)/i.test(s);
  }

  function rewriteKontaktLinks() {
    var url = buildUrl(readStored() || {});
    var nodes = document.querySelectorAll('a[href], form[action]');
    var i;
    for (i = 0; i < nodes.length; i++) {
      var el = nodes[i];
      var attr = el.tagName === 'FORM' ? 'action' : 'href';
      var current = el.getAttribute(attr) || '';
      if (!isKontaktUrl(current)) {
        continue;
      }
      if (el.closest && el.closest('.menu-item, .widget_nav_menu, .site-header, header, .site-footer, footer')) {
        continue;
      }
      el.setAttribute(attr, url);
      if (el.tagName === 'A') {
        el.classList.add('werdu-calc-cta');
      }
    }
    return url;
  }

  window.werduCalcHandoff = {
    STORAGE_KEY: STORAGE_KEY,
    parseNumber: parseNumber,
    normalize: normalize,
    buildUrl: buildUrl,
    persist: persist,
    readStored: readStored,
    persistAndLink: persistAndLink,
    rewriteCtas: rewriteCtas,
    ensureBaseHref: ensureBaseHref,
    isBeratungCta: isBeratungCta,
    beratungBase: beratungBase,
    isKontaktUrl: isKontaktUrl,
    rewriteKontaktLinks: rewriteKontaktLinks,
    KONTAKT_RE: KONTAKT_RE
  };

  try {
    document.dispatchEvent(new CustomEvent('werdu-calc-handoff-ready'));
  } catch (err) {
    var ev = document.createEvent('Event');
    ev.initEvent('werdu-calc-handoff-ready', true, true);
    document.dispatchEvent(ev);
  }
})(window, document);
