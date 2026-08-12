/**
 * WERDU Partner — Major City Fallback Matrix (IP geo) + form helpers
 * Small towns / villages are mapped to the nearest German Metropolregion hub.
 */
(function () {
  'use strict';

  var cfg = window.werduPartner || {};
  var root = document.getElementById('werdu-partner');
  if (!root) return;

  var geoEl = root.querySelector('[data-wp-geo]');
  var plzInput = root.querySelector('[name="partner-plz"], [name="your-plz"], #partner-plz');
  var radiusSelect = root.querySelector('[name="partner-radius"], #partner-radius');
  var form = root.querySelector('#werdu-partner-form');
  var msg = root.querySelector('[data-wp-form-msg]');

  var MAJOR_HUBS = Array.isArray(cfg.majorHubs) && cfg.majorHubs.length
    ? cfg.majorHubs
    : [
        { plz: '10117', city: 'Berlin', lat: 52.52, lon: 13.405 },
        { plz: '20095', city: 'Hamburg', lat: 53.5511, lon: 9.9937 },
        { plz: '80331', city: 'München', lat: 48.1351, lon: 11.582 },
        { plz: '50667', city: 'Köln', lat: 50.9375, lon: 6.9603 },
        { plz: '60311', city: 'Frankfurt', lat: 50.1109, lon: 8.6821 },
        { plz: '70173', city: 'Stuttgart', lat: 48.7758, lon: 9.1829 },
        { plz: '40213', city: 'Düsseldorf', lat: 51.2277, lon: 6.7735 },
        { plz: '04109', city: 'Leipzig', lat: 51.3397, lon: 12.3731 },
        { plz: '30159', city: 'Hannover', lat: 52.3759, lon: 9.732 },
        { plz: '28195', city: 'Bremen', lat: 53.0793, lon: 8.8017 },
        { plz: '90402', city: 'Nürnberg', lat: 49.4521, lon: 11.0767 },
        { plz: '44135', city: 'Dortmund', lat: 51.5136, lon: 7.4653 }
      ];

  var PLZ_MATRIX = cfg.plzMatrix || {};
  var DEFAULT_GEO = cfg.defaultGeo || { plz: '10117', city: 'Berlin' };

  var CITY_ALIASES = {
    Berlin: 'Berlin',
    Hamburg: 'Hamburg',
    München: 'München',
    Munich: 'München',
    Köln: 'Köln',
    Cologne: 'Köln',
    Frankfurt: 'Frankfurt',
    'Frankfurt am Main': 'Frankfurt',
    Stuttgart: 'Stuttgart',
    Düsseldorf: 'Düsseldorf',
    Dusseldorf: 'Düsseldorf',
    Leipzig: 'Leipzig',
    Hannover: 'Hannover',
    Hanover: 'Hannover',
    Bremen: 'Bremen',
    Nürnberg: 'Nürnberg',
    Nuremberg: 'Nürnberg',
    Dortmund: 'Dortmund'
  };

  function hubByCityName(city) {
    if (!city) return null;
    var name = String(city).trim();
    if (CITY_ALIASES[name]) {
      return findHubByCity(CITY_ALIASES[name]);
    }
    var lower = name.toLowerCase();
    var aliasKeys = Object.keys(CITY_ALIASES);
    for (var i = 0; i < aliasKeys.length; i++) {
      var alias = aliasKeys[i];
      if (lower.indexOf(alias.toLowerCase()) !== -1 || alias.toLowerCase().indexOf(lower) !== -1) {
        return findHubByCity(CITY_ALIASES[alias]);
      }
    }
    return null;
  }

  function findHubByCity(cityName) {
    for (var i = 0; i < MAJOR_HUBS.length; i++) {
      if (MAJOR_HUBS[i].city === cityName) {
        return { plz: MAJOR_HUBS[i].plz, city: MAJOR_HUBS[i].city };
      }
    }
    return null;
  }

  function haversineKm(lat1, lon1, lat2, lon2) {
    var R = 6371;
    var dLat = ((lat2 - lat1) * Math.PI) / 180;
    var dLon = ((lon2 - lon1) * Math.PI) / 180;
    var a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos((lat1 * Math.PI) / 180) *
        Math.cos((lat2 * Math.PI) / 180) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  }

  function nearestHubByCoords(lat, lon) {
    var best = MAJOR_HUBS[0];
    var bestKm = Infinity;
    for (var i = 0; i < MAJOR_HUBS.length; i++) {
      var h = MAJOR_HUBS[i];
      var km = haversineKm(lat, lon, h.lat, h.lon);
      if (km < bestKm) {
        bestKm = km;
        best = h;
      }
    }
    return { plz: best.plz, city: best.city };
  }

  function hubByPlz(plz) {
    var digits = String(plz || '').replace(/\D/g, '').slice(0, 5);
    if (digits.length < 2) return null;
    var prefix = digits.slice(0, 2);
    var hubCity = PLZ_MATRIX[prefix];
    if (hubCity) return findHubByCity(hubCity);
    return null;
  }

  /**
   * Major City Fallback Matrix — never keep a small town / village.
   */
  function normalizeToMajorHub(raw) {
    var city = (raw && raw.city) || '';
    var plz = (raw && (raw.plz || raw.postal)) || '';
    var lat = raw && raw.lat != null ? Number(raw.lat) : null;
    var lon = raw && (raw.lon != null ? Number(raw.lon) : raw.longitude != null ? Number(raw.longitude) : null);

    var byCity = hubByCityName(city);
    if (byCity) return byCity;

    if (lat != null && lon != null && !isNaN(lat) && !isNaN(lon) && !(lat === 0 && lon === 0)) {
      return nearestHubByCoords(lat, lon);
    }

    var byPlz = hubByPlz(plz);
    if (byPlz) return byPlz;

    return { plz: DEFAULT_GEO.plz, city: DEFAULT_GEO.city };
  }

  function setDefaultRadius() {
    if (radiusSelect && !radiusSelect.dataset.userTouched) {
      radiusSelect.value = '50';
    }
  }

  function setGeoNotice(plz, city) {
    if (!plz || !city) return;
    var hub = normalizeToMajorHub({ plz: plz, city: city });
    plz = hub.plz;
    city = hub.city;

    if (geoEl) {
      var plzNode = geoEl.querySelector('[data-wp-plz]');
      var cityNode = geoEl.querySelector('[data-wp-city]');
      if (plzNode) plzNode.textContent = plz;
      if (cityNode) cityNode.textContent = city;
      geoEl.classList.add('is-visible');
      geoEl.setAttribute('aria-hidden', 'false');
    }

    if (plzInput && !plzInput.value) {
      plzInput.value = plz;
    }
    setDefaultRadius();
  }

  function fetchGeo() {
    var url = cfg.ajaxUrl || '/wp-admin/admin-ajax.php';
    var body = new FormData();
    body.append('action', 'werdu_partner_geo');
    if (cfg.nonce) body.append('nonce', cfg.nonce);

    fetch(url, { method: 'POST', body: body, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data && data.success && data.data) {
          setGeoNotice(data.data.plz, data.data.city);
          return;
        }
        return clientFallback();
      })
      .catch(function () {
        return clientFallback();
      });
  }

  function clientFallback() {
    return fetch('https://ipapi.co/json/', { credentials: 'omit' })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (!d || d.error) {
          setGeoNotice(DEFAULT_GEO.plz, DEFAULT_GEO.city);
          return;
        }
        var country = (d.country_code || d.country || '').toUpperCase();
        if (country && country !== 'DE') {
          setGeoNotice(DEFAULT_GEO.plz, DEFAULT_GEO.city);
          return;
        }
        var hub = normalizeToMajorHub({
          city: d.city || d.region || '',
          plz: d.postal || '',
          lat: d.latitude,
          lon: d.longitude
        });
        setGeoNotice(hub.plz, hub.city);
      })
      .catch(function () {
        setGeoNotice(DEFAULT_GEO.plz, DEFAULT_GEO.city);
      });
  }

  if (radiusSelect) {
    radiusSelect.addEventListener('change', function () {
      radiusSelect.dataset.userTouched = '1';
    });
  }

  root.querySelectorAll('.wp-type-option input').forEach(function (input) {
    input.addEventListener('change', function () {
      root.querySelectorAll('.wp-type-option').forEach(function (el) {
        el.classList.toggle('is-active', !!el.querySelector('input:checked'));
      });
    });
  });

  function selectPartnerType(typ) {
    var radio = root.querySelector('input[name="partner-typ"][value="' + typ + '"]');
    if (radio) {
      radio.checked = true;
      radio.dispatchEvent(new Event('change', { bubbles: true }));
    }
      var formSection = document.getElementById('anmeldung') || document.getElementById('partner-anfrage');
      if (formSection) formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // Card click / Enter → preselect partnership type
  root.querySelectorAll('[data-wp-select-type]').forEach(function (card) {
    card.addEventListener('click', function (e) {
      if (e.target.closest('a')) return;
      selectPartnerType(card.getAttribute('data-wp-select-type'));
    });
    card.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        selectPartnerType(card.getAttribute('data-wp-select-type'));
      }
    });
  });

  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (msg) {
        msg.className = 'wp-form-msg';
        msg.textContent = '';
      }
      var fd = new FormData(form);
      fd.append('action', 'werdu_partner_apply');
      if (cfg.nonce) fd.append('nonce', cfg.nonce);

      var btn = form.querySelector('[type="submit"]');
      if (btn) {
        btn.disabled = true;
        btn.dataset.label = btn.textContent;
        btn.textContent = 'Wird gesendet…';
      }

      fetch(cfg.ajaxUrl || '/wp-admin/admin-ajax.php', {
        method: 'POST',
        body: fd,
        credentials: 'same-origin'
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data && data.success) {
            if (msg) {
              msg.className = 'wp-form-msg is-ok';
              msg.textContent = (data.data && data.data.message) || 'Vielen Dank — wir melden uns zeitnah bei Ihnen.';
            }
            form.reset();
            setDefaultRadius();
          } else {
            if (msg) {
              msg.className = 'wp-form-msg is-err';
              msg.textContent = (data && data.data && data.data.message) || 'Senden fehlgeschlagen. Bitte erneut versuchen.';
            }
          }
        })
        .catch(function () {
          if (msg) {
            msg.className = 'wp-form-msg is-err';
            msg.textContent = 'Netzwerkfehler. Bitte später erneut versuchen.';
          }
        })
        .finally(function () {
          if (btn) {
            btn.disabled = false;
            btn.textContent = btn.dataset.label || 'Partnerschaft anfragen';
          }
        });
    });
  }

  fetchGeo();
})();
