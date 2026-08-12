/**
 * Werdu Beratung Prefill — enriched lead handoff
 * URL params + sessionStorage.werdu_calc_result (v2 with monthly[])
 * Renders summary card + CSS bar chart; fills CF7 hidden fields.
 */
(function () {
  'use strict';

  var CF7_FIELDS = {
    kwh: 'calc-kwh',
    savings: 'calc-savings',
    peak: 'calc-peak',
    plz: 'your-plz',
    usage: 'calc-usage',
    goal: 'calc-goal',
    autarky: 'calc-autarky',
    selfConsumption: 'calc-self-consumption',
    fullBreakdown: 'calc-full-breakdown',
    installer: 'calc-installer-option'
  };

  var INSTALLER_YES = 'Ja, Montagepartner-Vermittlung gewünscht (300€-500€ Richtwert)';
  var INSTALLER_NO = 'Nein, reine Gerätelieferung';

  var MONTH_FACTORS = [0.03, 0.05, 0.09, 0.13, 0.15, 0.15, 0.15, 0.13, 0.10, 0.07, 0.04, 0.02];
  var MONTH_NAMES = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];

  function stripUnits(value) {
    if (value === null || value === undefined) return '';
    var s = String(value).trim();
    if (!s) return '';
    if (/^(summer|year|winter|trans|sommer|jahres|uebergang|übergang)$/i.test(s)) {
      return s.toLowerCase().replace('ü', 'ue');
    }
    s = s
      .replace(/\u00a0/g, ' ')
      .replace(/[€$]/g, '')
      .replace(/\s*(kWh|kWp|kw|Wh|Wp|ct|EUR|Euro|Autarkie|%)\s*/gi, '')
      .replace(/\s+/g, '')
      .replace(',', '.');
    var m = s.match(/-?\d+(?:\.\d+)?/);
    return m ? m[0] : s;
  }

  function estimateMonthly(peak, usage, autarky) {
    var p = parseFloat(peak) || 8;
    var u = parseFloat(usage) || 4500;
    var a = parseFloat(autarky) || 70;
    var annualYield = p * 950;
    var monthlyCons = u / 12;
    return MONTH_FACTORS.map(function (factor, idx) {
      var gen = Math.round(annualYield * factor);
      var cons = Math.round(monthlyCons);
      var stored = Math.round(Math.min(cons * (a / 100), gen * 0.55));
      return { month: MONTH_NAMES[idx], generation: gen, consumption: cons, battery: stored };
    });
  }

  function formatBreakdownText(data) {
    var lines = [];
    lines.push('Kapazität: ' + data.kwh + ' kWh');
    lines.push('PV-Peak: ' + data.peak + ' kWp');
    lines.push('Ersparnis: ' + data.savings + ' EUR/Jahr');
    lines.push('Autarkie: ' + (data.autarky || '--') + ' %');
    lines.push('Eigenverbrauch: ' + (data.selfConsumption || '--') + ' %');
    lines.push('Verbrauch: ' + (data.usage || '--') + ' kWh/Jahr');
    lines.push('PLZ: ' + (data.plz || '--'));
    lines.push('Ziel: ' + (data.goal || '--'));
    lines.push('Montagepartner: ' + (data.installerOption || INSTALLER_NO));
    if (data.annualYield) lines.push('Jahresertrag: ' + data.annualYield + ' kWh');
    if (data.monthly && data.monthly.length) {
      lines.push('--- Monatliche Aufschlüsselung (Ertrag / Verbrauch / Speicher) ---');
      data.monthly.forEach(function (row) {
        lines.push(
          (row.month || '') + ': ' +
          (row.generation || 0) + ' / ' +
          (row.consumption || 0) + ' / ' +
          (row.battery || 0) + ' kWh'
        );
      });
    }
    return lines.join('\n');
  }

  function readFullSession() {
    var keys = ['werdu_calc_result', 'werdu_calc_data', 'wr5_calc_result'];
    var stores = [];
    try { stores.push(window.sessionStorage); } catch (e) {}
    try { stores.push(window.localStorage); } catch (e2) {}

    for (var s = 0; s < stores.length; s++) {
      for (var k = 0; k < keys.length; k++) {
        try {
          var raw = stores[s].getItem(keys[k]);
          if (!raw) continue;
          var data = JSON.parse(raw);
          if (!data || typeof data !== 'object') continue;
          // Map legacy alias keys into canonical ones
          if (!data.usage && data.verbrauch) data.usage = data.verbrauch;
          if (!data.savings && data.ersparnis) data.savings = data.ersparnis;
          if (!data.autarky && data.autarkie) data.autarky = data.autarkie;
          if (!data.peak && data.pv) data.peak = data.pv;
          if (!data.kwh && data.sp) data.kwh = data.sp;
          if (!data.kwh && data.kapazitaet) data.kwh = data.kapazitaet;
          if (!data.goal && data.system) data.goal = data.system;
          if (!data.roi && data.amortisation) data.roi = data.amortisation;
          if (data.kwh || data.savings || data.peak || data.plz) return data;
        } catch (err) {}
      }
    }
    return null;
  }

  function normalizeData(raw, sessionRaw) {
    var base = sessionRaw && typeof sessionRaw === 'object' ? sessionRaw : {};
    var src = raw || {};

    function pick() {
      for (var i = 0; i < arguments.length; i++) {
        var v = arguments[i];
        if (v !== null && v !== undefined && String(v).trim() !== '') return v;
      }
      return '';
    }

    var data = {
      kwh: stripUnits(pick(src.kwh, src.capacity, src.kapazitaet, base.kwh, base.capacity, base.kapazitaet, base.sp)),
      peak: stripUnits(pick(src.peak, src.pv, base.peak, base.pv)),
      savings: stripUnits(pick(src.savings, src.ersparnis, base.savings, base.ersparnis)),
      plz: stripUnits(pick(src.plz, base.plz)).replace(/\D/g, '').substring(0, 5),
      goal: stripUnits(pick(src.goal, src.plan, base.goal, base.plan)) || String(pick(src.goal, src.plan, base.goal, base.plan) || '').trim(),
      usage: stripUnits(pick(src.usage, src.verbrauch, base.usage, base.verbrauch)),
      autarky: stripUnits(pick(src.autarky, src.autarkie, base.autarky, base.autarkie)),
      selfConsumption: stripUnits(
        pick(src.selfConsumption, src.self, base.selfConsumption, base.self)
      ),
      roi: stripUnits(pick(src.roi, src.amortisation, base.roi, base.amortisation)),
      annualYield: stripUnits(pick(src.annualYield, base.annualYield)),
      email: String(pick(src.email, base.email) || '').trim(),
      monthly: Array.isArray(src.monthly) ? src.monthly : (Array.isArray(base.monthly) ? base.monthly : null),
      installerRequested: !!(src.installerRequested != null ? src.installerRequested : base.installerRequested),
      installerOption: '',
      source: src.source || base.source || 'unknown',
      version: 2
    };

    data.installerOption = data.installerRequested ? INSTALLER_YES : INSTALLER_NO;

    if (!data.monthly || !data.monthly.length) {
      data.monthly = estimateMonthly(data.peak, data.usage, data.autarky || 70);
    }
    return data;
  }

  function parseInstallerFlag(raw) {
    if (raw === null || raw === undefined || raw === '') return null;
    var s = String(raw).trim().toLowerCase();
    if (s === '1' || s === 'yes' || s === 'ja' || s === 'true') return true;
    if (s === '0' || s === 'no' || s === 'nein' || s === 'false') return false;
    return null;
  }

  function readParams() {
    var params = new URLSearchParams(window.location.search);
    var installerParam = parseInstallerFlag(params.get('installer'));
    var fromUrl = {
      kwh: params.get('kwh') || params.get('kapazitaet'),
      peak: params.get('peak') || params.get('pv'),
      savings: params.get('savings') || params.get('ersparnis'),
      plz: params.get('plz'),
      goal: params.get('goal') || params.get('plan') || params.get('system'),
      usage: params.get('usage') || params.get('verbrauch'),
      autarky: params.get('autarky') || params.get('autarkie'),
      self: params.get('self'),
      roi: params.get('roi') || params.get('amortisation'),
      email: params.get('email'),
      installerRequested: installerParam
    };
    var sessionRaw = readFullSession();
    var hasUrlCore = !!(stripUnits(fromUrl.kwh) && stripUnits(fromUrl.savings) && stripUnits(fromUrl.peak));
    // Soft core: allow prefill when at least PLZ + usage/savings present (blog calc)
    var hasUrlSoft = !!(stripUnits(fromUrl.plz) && (stripUnits(fromUrl.usage) || stripUnits(fromUrl.savings) || stripUnits(fromUrl.kwh)));

    if (installerParam === true && sessionRaw) {
      sessionRaw.installerRequested = true;
    }

    var data;
    if (hasUrlCore || hasUrlSoft) {
      data = normalizeData(fromUrl, sessionRaw);
    } else if (sessionRaw && (sessionRaw.kwh || sessionRaw.savings || sessionRaw.peak)) {
      data = normalizeData(
        installerParam !== null ? { installerRequested: installerParam } : {},
        sessionRaw
      );
    } else {
      data = normalizeData(fromUrl, sessionRaw);
    }

    // Infer missing kwh/peak/savings from soft params so UI still shows
    if (data && !data.kwh && data.usage) {
      var u = parseFloat(data.usage) || 0;
      data.kwh = String(u <= 4500 ? 16 : (u <= 7000 ? 16 : 32));
    }
    if (data && !data.peak && data.usage) {
      data.peak = String(Math.max(1, Math.round((parseFloat(data.usage) / 1000) * 10) / 10));
    }
    if (data && !data.savings && data.usage) {
      data.savings = String(Math.round((parseFloat(data.usage) || 0) * 0.35 * 0.35));
    }

    if (installerParam !== null) {
      data.installerRequested = installerParam;
      data.installerOption = installerParam ? INSTALLER_YES : INSTALLER_NO;
    }
    return data;
  }

  function setText(id, text) {
    var el = document.getElementById(id);
    if (el) el.textContent = text;
  }

  function setCf7Field(name, value) {
    if (value === null || value === undefined || value === '') return;
    var str = String(value);
    var nodes = document.getElementsByName(name);
    var i;
    if (nodes && nodes.length) {
      for (i = 0; i < nodes.length; i++) {
        nodes[i].value = str;
      }
      return;
    }
    var form = document.querySelector('.wpcf7-form') || document.querySelector('form.wpcf7-form');
    if (!form) return;
    var existing = form.querySelector('input.werdu-calc-hidden[name="' + name + '"]');
    if (existing) {
      existing.value = str;
      return;
    }
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = str;
    input.className = 'werdu-calc-hidden';
    form.appendChild(input);
  }

  function injectStyles() {
    if (document.getElementById('werdu-beratung-enrich-css')) return;
    var style = document.createElement('style');
    style.id = 'werdu-beratung-enrich-css';
    style.textContent =
      '#werdu-calc-enrich{margin:24px auto 32px;max-width:1000px;padding:0 20px;box-sizing:border-box;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif}' +
      '#werdu-calc-enrich .wce-card{background:#fff;border:2px solid #FF6600;border-radius:20px;padding:28px 24px;box-shadow:0 16px 40px rgba(26,82,118,.12)}' +
      '#werdu-calc-enrich h3{margin:0 0 8px;color:#1a5276;font-size:1.35rem;font-weight:800}' +
      '#werdu-calc-enrich .wce-sub{margin:0 0 20px;color:#64748b;font-size:.95rem}' +
      '#werdu-calc-enrich .wce-metrics{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:12px;margin-bottom:22px}' +
      '#werdu-calc-enrich .wce-metric{background:#FFF8F5;border:1px solid #ffe0cc;border-radius:12px;padding:14px 10px;text-align:center}' +
      '#werdu-calc-enrich .wce-metric strong{display:block;font-size:1.35rem;color:#FF6600;font-weight:800}' +
      '#werdu-calc-enrich .wce-metric span{display:block;font-size:.72rem;font-weight:700;color:#1a5276;text-transform:uppercase;letter-spacing:.03em;margin-top:4px}' +
      '#werdu-calc-enrich .wce-chart-title{font-size:.9rem;font-weight:800;color:#1a5276;margin:0 0 10px}' +
      '#werdu-calc-enrich .wce-chart{display:flex;align-items:flex-end;gap:6px;height:160px;padding:12px 8px 0;background:linear-gradient(180deg,#F0F8FF 0%,#fff 100%);border-radius:12px;border:1px solid #e2e8f0}' +
      '#werdu-calc-enrich .wce-col{flex:1;display:flex;flex-direction:column;align-items:center;height:100%;justify-content:flex-end;min-width:0}' +
      '#werdu-calc-enrich .wce-bars{display:flex;align-items:flex-end;gap:2px;width:100%;height:calc(100% - 18px)}' +
      '#werdu-calc-enrich .wce-bar{flex:1;border-radius:3px 3px 0 0;min-height:2px;transition:height .4s ease}' +
      '#werdu-calc-enrich .wce-bar.gen{background:#0099FF}' +
      '#werdu-calc-enrich .wce-bar.cons{background:#cbd5e1}' +
      '#werdu-calc-enrich .wce-bar.bat{background:#FF6600}' +
      '#werdu-calc-enrich .wce-label{font-size:10px;color:#64748b;margin-top:6px;font-weight:600}' +
      '#werdu-calc-enrich .wce-legend{display:flex;flex-wrap:wrap;gap:14px;margin-top:12px;font-size:12px;color:#475569;font-weight:600}' +
      '#werdu-calc-enrich .wce-dot{display:inline-block;width:10px;height:10px;border-radius:2px;margin-right:6px;vertical-align:middle}' +
      '@media(max-width:600px){#werdu-calc-enrich .wce-chart{gap:3px;height:140px}#werdu-calc-enrich .wce-label{font-size:8px}}';
    document.head.appendChild(style);
  }

  function ensureEnrichCard() {
    var existing = document.getElementById('werdu-calc-enrich');
    if (existing) return existing;

    var card = document.createElement('div');
    card.id = 'werdu-calc-enrich';
    card.innerHTML =
      '<div class="wce-card">' +
        '<h3>Ihre Rechner-Ergebnisse</h3>' +
        '<p class="wce-sub">Übernommen aus Ihrer Speichersimulation — prüfen Sie die Werte und ergänzen Sie Ihre Kontaktdaten.</p>' +
        '<div class="wce-metrics" id="wce-metrics"></div>' +
        '<p class="wce-chart-title">Monatliche Abdeckung: Ertrag · Verbrauch · Speicheranteil</p>' +
        '<div class="wce-chart" id="wce-chart" role="img" aria-label="Monatsdiagramm Ertrag Verbrauch Speicher"></div>' +
        '<div class="wce-legend">' +
          '<span><i class="wce-dot" style="background:#0099FF"></i>PV-Ertrag</span>' +
          '<span><i class="wce-dot" style="background:#cbd5e1"></i>Verbrauch</span>' +
          '<span><i class="wce-dot" style="background:#FF6600"></i>Speicher</span>' +
        '</div>' +
      '</div>';

    var anchor =
      document.querySelector('.form-container') ||
      document.querySelector('.wpcf7') ||
      document.getElementById('calcSummary') ||
      document.getElementById('werdu-master-page');

    if (anchor && anchor.parentNode) {
      if (anchor.id === 'calcSummary' || anchor.classList.contains('form-container') || anchor.classList.contains('wpcf7')) {
        anchor.parentNode.insertBefore(card, anchor);
      } else {
        anchor.insertBefore(card, anchor.firstChild);
      }
    } else {
      document.body.insertBefore(card, document.body.firstChild);
    }
    return card;
  }

  function renderEnrichCard(data) {
    injectStyles();
    var wrap = ensureEnrichCard();
    if (!wrap) return;

    var metrics = document.getElementById('wce-metrics');
    if (metrics) {
      metrics.innerHTML =
        metricHtml(data.kwh + ' kWh', 'Kapazität') +
        metricHtml(data.peak + ' kWp', 'PV-Peak') +
        metricHtml(data.savings + ' €', 'Ersparnis/Jahr') +
        metricHtml((data.autarky || '--') + ' %', 'Autarkie') +
        metricHtml((data.selfConsumption || '--') + ' %', 'Eigenverbrauch') +
        metricHtml(data.plz || '--', 'PLZ');
    }

    var chart = document.getElementById('wce-chart');
    if (!chart || !data.monthly || !data.monthly.length) return;

    var maxVal = 1;
    data.monthly.forEach(function (row) {
      maxVal = Math.max(maxVal, row.generation || 0, row.consumption || 0, row.battery || 0);
    });

    chart.innerHTML = '';
    data.monthly.forEach(function (row) {
      var col = document.createElement('div');
      col.className = 'wce-col';
      var bars = document.createElement('div');
      bars.className = 'wce-bars';
      bars.appendChild(barEl('gen', row.generation, maxVal));
      bars.appendChild(barEl('cons', row.consumption, maxVal));
      bars.appendChild(barEl('bat', row.battery, maxVal));
      var label = document.createElement('div');
      label.className = 'wce-label';
      label.textContent = row.month || '';
      col.appendChild(bars);
      col.appendChild(label);
      chart.appendChild(col);
    });
  }

  function metricHtml(value, label) {
    return '<div class="wce-metric"><strong>' + value + '</strong><span>' + label + '</span></div>';
  }

  function barEl(type, value, maxVal) {
    var el = document.createElement('div');
    el.className = 'wce-bar ' + type;
    var h = Math.max(2, Math.round(((value || 0) / maxVal) * 100));
    el.style.height = h + '%';
    el.title = (value || 0) + ' kWh';
    return el;
  }

  function renderInstallerOption(data) {
    var form = document.querySelector('.wpcf7-form') || document.querySelector('form.wpcf7-form');
    var wrap = document.getElementById('werdu-installer-beratung');
    if (!wrap) {
      wrap = document.createElement('div');
      wrap.id = 'werdu-installer-beratung';
      wrap.innerHTML =
        '<label style="display:flex;gap:12px;align-items:flex-start;cursor:pointer;margin:0 0 8px;padding:14px 16px;border:2px solid #FF6600;border-radius:12px;background:linear-gradient(135deg,#FFF8F5,#F0F8FF);font-weight:600;color:#1a1a2e;line-height:1.45;">' +
          '<input type="checkbox" id="werdu-installer-beratung-cb" style="margin-top:4px;width:18px;height:18px;accent-color:#FF6600;flex-shrink:0;" />' +
          '<span>Auf Wunsch: Vermittlung eines lokalen Montage-Partners für den schnellen DC- &amp; Kommunikationsanschluss (Plus/Minus-Kabel &amp; Datenkabel). Richtwert nach Marktcheck (Juli 2026): ca. 300 € - 500 € Montageaufwand direkt mit dem Installateur.</span>' +
        '</label>' +
        '<p style="margin:0 0 18px;font-size:0.85rem;color:#64748b;">Unverbindlicher Richtwert — Abrechnung erfolgt direkt mit dem Installateur.</p>';

      var anchor = document.querySelector('.form-container') || form;
      if (anchor) {
        if (form && form.parentNode) {
          form.parentNode.insertBefore(wrap, form);
        } else {
          anchor.insertBefore(wrap, anchor.firstChild);
        }
      }
    }

    var cb = document.getElementById('werdu-installer-beratung-cb');
    if (!cb) return;

    cb.checked = !!data.installerRequested;

    function syncInstaller() {
      var yes = !!cb.checked;
      data.installerRequested = yes;
      data.installerOption = yes ? INSTALLER_YES : INSTALLER_NO;
      setCf7Field(CF7_FIELDS.installer, data.installerOption);
      try {
        var stored = readFullSession() || {};
        stored.installerRequested = yes;
        stored.installerOption = data.installerOption;
        sessionStorage.setItem('werdu_calc_result', JSON.stringify(Object.assign({}, stored, data)));
      } catch (e) {}
    }

    cb.onchange = syncInstaller;
    syncInstaller();
  }

  function applyInstallerOnly(data) {
    if (!data) return false;
    data.installerOption = data.installerRequested ? INSTALLER_YES : INSTALLER_NO;
    setCf7Field(CF7_FIELDS.installer, data.installerOption);
    renderInstallerOption(data);
    return true;
  }

  function applyPrefill(data) {
    if (!data) return false;

    var hasCore = !!(data.kwh && data.savings && data.peak);
    var hasSoft = !!(data.plz || data.usage || data.autarky || data.email);
    if (!hasCore && !hasSoft && !data.installerRequested) {
      return false;
    }
    if (!hasCore) {
      // Still show installer + whatever fields we have
      if (data.email) setCf7Field('your-email', data.email);
      if (data.plz) setCf7Field(CF7_FIELDS.plz, data.plz);
      applyInstallerOnly(data);
      if (!data.kwh && !data.savings && !data.peak) return !!data.installerRequested || !!(data.plz || data.email);
    }

    var step1 = document.getElementById('step1');
    var step2 = document.getElementById('step2');
    var smartBanner = document.getElementById('smartBanner');
    var bannerNoData = document.getElementById('bannerNoData');
    var bannerHasData = document.getElementById('bannerHasData');
    var calcSummary = document.getElementById('calcSummary');

    if (hasCore) {
      if (step1) {
        step1.classList.add('completed');
        step1.classList.remove('active', 'inactive');
        var stepNum = step1.querySelector('.step-num');
        if (stepNum) stepNum.textContent = '✓';
      }
      if (step2) {
        step2.classList.add('active');
        step2.classList.remove('inactive');
      }

      if (bannerNoData) bannerNoData.style.display = 'none';
      if (bannerHasData) bannerHasData.style.display = 'flex';
      if (smartBanner) {
        smartBanner.style.borderColor = '#1AFF00';
        smartBanner.style.background = 'linear-gradient(135deg, #F5FFFA 0%, #E8FFE5 100%)';
      }
      if (calcSummary) calcSummary.style.display = 'flex';
    }

    if (data.kwh) {
      setText('valKwh', data.kwh + ' kWh');
      setText('chipKwh', '⚡ ' + data.kwh + ' kWh');
    }
    if (data.savings) {
      setText('valSavings', '€ ' + data.savings);
      setText('chipSavings', '💰 € ' + data.savings + '/Jahr');
    }
    if (data.peak) {
      setText('valPeak', data.peak + ' kWp');
      setText('chipPeak', '☀️ ' + data.peak + ' kWp');
    }
    setText('valPlz', data.plz || '--');
    setText('chipPlz', '📍 ' + (data.plz || '--'));

    if (data.usage) {
      setText('valUsage', data.usage + ' kWh');
      setText('chipUsage', '🏠 ' + data.usage + ' kWh');
    }
    if (data.goal) {
      setText('valGoal', data.goal);
      setText('chipGoal', '🎯 ' + data.goal);
    }
    if (data.autarky) {
      setText('valAutarky', data.autarky + ' %');
      setText('chipAutarky', '🔋 ' + data.autarky + ' %');
    }

    setCf7Field(CF7_FIELDS.kwh, data.kwh);
    setCf7Field(CF7_FIELDS.savings, data.savings);
    setCf7Field(CF7_FIELDS.peak, data.peak);
    setCf7Field(CF7_FIELDS.plz, data.plz);
    setCf7Field(CF7_FIELDS.usage, data.usage);
    setCf7Field(CF7_FIELDS.goal, data.goal);
    setCf7Field(CF7_FIELDS.autarky, data.autarky);
    setCf7Field(CF7_FIELDS.selfConsumption, data.selfConsumption);
    setCf7Field(CF7_FIELDS.fullBreakdown, formatBreakdownText(data));
    setCf7Field(CF7_FIELDS.installer, data.installerOption || INSTALLER_NO);
    if (data.email) setCf7Field('your-email', data.email);

    if (hasCore) renderEnrichCard(data);
    renderInstallerOption(data);
    return true;
  }

  function initWerduBeratungPrefill() {
    var data = readParams();
    var ok = applyPrefill(data);
    if (ok && data && data.kwh) {
      try {
        sessionStorage.setItem('werdu_calc_result', JSON.stringify(data));
      } catch (e) {}
    }
  }

  window.werduBeratungPrefill = initWerduBeratungPrefill;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWerduBeratungPrefill);
  } else {
    initWerduBeratungPrefill();
  }
  window.addEventListener('load', initWerduBeratungPrefill);

  document.addEventListener('wpcf7mailsent', function () {
    var spinner = document.querySelector('.wpcf7-spinner');
    if (spinner) spinner.style.display = 'none';
  }, false);
})();
