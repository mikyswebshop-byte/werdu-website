// ===== SOLARBATTERIE-RECHNER v3.0 =====
// Volledig standalone JavaScript - geen jQuery, geen externe dependencies

(function() {
    'use strict';

    var currentStep = 1;
    var totalSteps = 4;

    var regionFactors = {
        '0': 0.95, '1': 0.90, '2': 0.85, '3': 0.88,
        '4': 0.92, '5': 0.95, '6': 1.00, '7': 1.08,
        '8': 1.10, '9': 1.12
    };

    var monthlyFactors = [0.03, 0.05, 0.09, 0.13, 0.15, 0.15, 0.15, 0.13, 0.10, 0.07, 0.04, 0.02];

    var orientationFactors = {
        '0': 1.00, '45': 0.92, '-45': 0.92, '90': 0.78, '-90': 0.78
    };

    var tiltFactors = {
        '15': 0.94, '30': 1.00, '45': 0.96, '60': 0.85
    };

    var baseConsumption = {
        '1': 2300, '2': 3500, '3': 4500, '4': 5500, '5': 7000
    };

    var productPrices = {
        10: 2799, 15: 2499, 16: 2345,
        '15aio': 2899, 30: 3499, '30aio': 4839
    };

    function init() {
        initRanges();
        initOptions();
        updateConsumptionFromPersons();
    }

    function initRanges() {
        var ranges = [
            {id: 'w-consumption', disp: 'w-consumption-val', unit: ' kWh/Jahr'},
            {id: 'w-price', disp: 'w-price-val', unit: ' ct/kWh'},
            {id: 'w-pv', disp: 'w-pv-val', unit: ' kWp'},
            {id: 'w-autarky', disp: 'w-autarky-val', unit: '%'},
            {id: 'w-loss', disp: 'w-loss-val', unit: '%'}
        ];

        ranges.forEach(function(r) {
            var el = document.getElementById(r.id);
            var disp = document.getElementById(r.disp);
            if (!el || !disp) return;

            el.addEventListener('input', function() {
                var v = parseFloat(this.value);
                if (r.id === 'w-consumption') {
                    disp.textContent = v.toLocaleString('de-DE') + r.unit;
                } else {
                    disp.textContent = v + r.unit;
                }
                updateRangeProgress(this);
            });

            updateRangeProgress(el);
        });
    }

    function updateRangeProgress(el) {
        var min = parseFloat(el.min);
        var max = parseFloat(el.max);
        var val = parseFloat(el.value);
        var progress = ((val - min) / (max - min)) * 100;
        el.style.setProperty('--progress', progress + '%');
    }

    function initOptions() {
        var groups = ['w-persons', 'w-ev', 'w-hp', 'w-tilt', 'w-orientation', 'w-shading', 'w-season', 'w-inverter'];

        groups.forEach(function(gid) {
            var grp = document.getElementById(gid);
            if (!grp) return;

            var btns = grp.querySelectorAll('.werdu-option');
            btns.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    btns.forEach(function(b) { b.classList.remove('selected'); });
                    this.classList.add('selected');

                    if (gid === 'w-persons') {
                        updateConsumptionFromPersons();
                    }
                });
            });
        });
    }

    function updateConsumptionFromPersons() {
        var sel = document.querySelector('#w-persons .werdu-option.selected');
        if (!sel) return;

        var p = sel.getAttribute('data-value');
        var base = baseConsumption[p] || 4500;

        var el = document.getElementById('w-consumption');
        var disp = document.getElementById('w-consumption-val');
        if (el && disp) {
            el.value = base;
            disp.textContent = base.toLocaleString('de-DE') + ' kWh/Jahr';
            updateRangeProgress(el);
        }
    }

    window.wNext = function() {
        if (currentStep < totalSteps - 1) {
            goToStep(currentStep + 1);
        }
    };

    window.wPrev = function() {
        if (currentStep > 1) {
            goToStep(currentStep - 1);
        }
    };

    window.wReset = function() {
        goToStep(1);
    };

    function goToStep(step) {
        document.querySelectorAll('.werdu-section').forEach(function(s) {
            s.classList.remove('active');
        });
        var targetSection = document.querySelector('.werdu-section[data-section="' + step + '"]');
        if (targetSection) targetSection.classList.add('active');

        document.querySelectorAll('.werdu-step').forEach(function(s, idx) {
            s.classList.remove('active', 'completed');
            var stepNum = idx + 1;
            if (stepNum < step) s.classList.add('completed');
            if (stepNum === step) s.classList.add('active');
        });

        currentStep = step;

        var wrapper = document.querySelector('.werdu-calc-wrapper');
        if (wrapper) {
            wrapper.scrollIntoView({behavior: 'smooth', block: 'start'});
        }
    }

    window.wCalculate = function() {
        var plz = document.getElementById('w-plz').value || '8';
        var plzPrefix = plz.charAt(0);
        var regionFactor = regionFactors[plzPrefix] || 1.0;

        var personsEl = document.querySelector('#w-persons .werdu-option.selected');
        var persons = parseInt(personsEl ? personsEl.getAttribute('data-value') : '3');

        var consumption = parseFloat(document.getElementById('w-consumption').value) || 4500;
        var price = parseFloat(document.getElementById('w-price').value) || 40;

        var evEl = document.querySelector('#w-ev .werdu-option.selected');
        var evCount = parseInt(evEl ? evEl.getAttribute('data-value') : '0');

        var hpEl = document.querySelector('#w-hp .werdu-option.selected');
        var hpConsumption = parseInt(hpEl ? hpEl.getAttribute('data-value') : '0');

        var pv = parseFloat(document.getElementById('w-pv').value) || 8;

        var tiltEl = document.querySelector('#w-tilt .werdu-option.selected');
        var tilt = tiltEl ? tiltEl.getAttribute('data-value') : '30';

        var orientEl = document.querySelector('#w-orientation .werdu-option.selected');
        var orientation = orientEl ? orientEl.getAttribute('data-value') : '0';

        var shadeEl = document.querySelector('#w-shading .werdu-option.selected');
        var shading = parseFloat(shadeEl ? shadeEl.getAttribute('data-value') : '1.0');

        var autarkyTarget = parseFloat(document.getElementById('w-autarky').value) || 70;

        var seasonEl = document.querySelector('#w-season .werdu-option.selected');
        var season = seasonEl ? seasonEl.getAttribute('data-value') : 'year';

        var systemLoss = parseFloat(document.getElementById('w-loss').value) || 10;

        var inverterEl = document.querySelector('#w-inverter .werdu-option.selected');
        var hasInverter = (inverterEl ? inverterEl.getAttribute('data-value') : 'yes') === 'yes';

        var evConsumption = evCount * 3000;
        var totalConsumption = consumption + evConsumption + hpConsumption;
        var dailyConsumption = totalConsumption / 365;

        var baseYield = 950;
        var orientFactor = orientationFactors[orientation] || 1.0;
        var tiltFactor = tiltFactors[tilt] || 1.0;
        var annualYield = pv * baseYield * regionFactor * orientFactor * tiltFactor * shading;
        var dailyYield = annualYield / 365;

        var efficiencyFactor = Math.pow(1 - (systemLoss / 100), 2);

        var seasonMultiplier = 1.0;
        if (season === 'winter') seasonMultiplier = 2.2;
        if (season === 'summer') seasonMultiplier = 0.7;

        var rawBatterySize = (dailyConsumption * (autarkyTarget / 100) * seasonMultiplier) / efficiencyFactor;
        var recommendedBattery = Math.ceil(rawBatterySize / 5) * 5;

        if (recommendedBattery < 5) recommendedBattery = 5;
        if (recommendedBattery > 50) recommendedBattery = 50;

        var theoreticalAutarky = (dailyYield * efficiencyFactor * 365) / totalConsumption * 100;
        var finalAutarky = Math.min(theoreticalAutarky, autarkyTarget * 0.92, 90);

        var selfConsumptionRate = Math.min((totalConsumption / annualYield) * 100 * 1.4, 95);
        if (annualYield < totalConsumption) {
            selfConsumptionRate = Math.min(annualYield / totalConsumption * 100 * 1.2, 95);
        }

        var gridConsumption = totalConsumption * (1 - finalAutarky / 100);
        var avoidedCost = (totalConsumption - gridConsumption) * (price / 100);
        var feedIn = Math.max(annualYield - totalConsumption, 0);
        var feedInRevenue = feedIn * 0.082;
        var annualSavings = avoidedCost + feedInRevenue;

        var batteryPrice = 0;
        var recommendedProduct = '';

        if (recommendedBattery <= 12) {
            if (hasInverter) {
                batteryPrice = productPrices[15];
                recommendedProduct = 'prod-15';
            } else {
                batteryPrice = productPrices[10];
                recommendedProduct = 'prod-10';
            }
        } else if (recommendedBattery <= 17) {
            if (hasInverter) {
                batteryPrice = productPrices[16];
                recommendedProduct = 'prod-16';
            } else {
                batteryPrice = productPrices['15aio'];
                recommendedProduct = 'prod-15aio';
            }
        } else if (recommendedBattery <= 25) {
            if (hasInverter) {
                batteryPrice = productPrices[16];
                recommendedProduct = 'prod-16';
            } else {
                batteryPrice = productPrices['15aio'];
                recommendedProduct = 'prod-15aio';
            }
        } else {
            if (hasInverter) {
                batteryPrice = productPrices[30];
                recommendedProduct = 'prod-30';
            } else {
                batteryPrice = productPrices['30aio'];
                recommendedProduct = 'prod-30aio';
            }
        }

        var roiYears = batteryPrice / annualSavings;
        var savings10y = annualSavings * 10 - batteryPrice;

        var co2Savings = (totalConsumption - gridConsumption) * 0.477;
        var solarKm = evCount > 0 ? (annualYield * 0.3 / 0.18) : 0;

        var resBattery = document.getElementById('res-battery');
        resBattery.textContent = recommendedBattery + ' kWh';
        resBattery.setAttribute('data-value', recommendedBattery);

        var resAutarky = document.getElementById('res-autarky');
        resAutarky.textContent = Math.round(finalAutarky) + '%';
        resAutarky.setAttribute('data-value', Math.round(finalAutarky));

        var resSavings = document.getElementById('res-savings');
        resSavings.textContent = Math.round(annualSavings).toLocaleString('de-DE') + ' €';
        resSavings.setAttribute('data-value', Math.round(annualSavings));

        var resRoi = document.getElementById('res-roi');
        resRoi.textContent = (roiYears <= 20 ? roiYears.toFixed(1) : ">20") + " Jahre";
        resRoi.setAttribute("data-value", roiYears <= 20 ? roiYears.toFixed(1) : "20+");

        document.getElementById('save-year').textContent = Math.round(annualSavings).toLocaleString('de-DE') + ' €';
        document.getElementById('save-10').textContent = Math.round(savings10y).toLocaleString('de-DE') + ' €';
        document.getElementById('save-co2').textContent = Math.round(co2Savings / 1000 * 10) / 10 + ' t';
        document.getElementById('save-km').textContent = Math.round(solarKm).toLocaleString('de-DE') + ' km';

        document.getElementById('roi-year').textContent = Math.ceil(roiYears);
        document.getElementById('roi-text').textContent = Math.ceil(roiYears) + ' Jahren';
        document.getElementById('cta-savings').textContent = Math.round(annualSavings).toLocaleString('de-DE') + ' €';

        setDonutChart('chart-self', 'chart-self-text', selfConsumptionRate, '#f97316');
        setDonutChart('chart-auto', 'chart-auto-text', finalAutarky, '#22c55e');

        var maxBar = Math.max(dailyYield, dailyConsumption, dailyConsumption * (1 - finalAutarky / 100));
        document.getElementById('bar-solar').style.width = Math.min((dailyYield / maxBar) * 100, 100) + '%';
        document.getElementById('bar-solar').textContent = dailyYield.toFixed(1) + ' kWh';
        document.getElementById('bar-battery').style.width = Math.min((dailyConsumption * (finalAutarky / 100) / maxBar) * 100, 100) + '%';
        document.getElementById('bar-battery').textContent = (dailyConsumption * (finalAutarky / 100)).toFixed(1) + ' kWh';
        document.getElementById('bar-grid').style.width = Math.min((dailyConsumption * (1 - finalAutarky / 100) / maxBar) * 100, 100) + '%';
        document.getElementById('bar-grid').textContent = (dailyConsumption * (1 - finalAutarky / 100)).toFixed(1) + ' kWh';

        var roiPercent = Math.min((roiYears / 20) * 100, 100);
        document.getElementById('roi-line').style.width = roiPercent + '%';
        var rp = document.getElementById('roi-point');
        if (roiYears <= 20) {
            rp.classList.add('active');
        } else {
            rp.classList.remove('active');
        }

        renderMonthlyChart(annualYield, totalConsumption);

        document.querySelectorAll('.werdu-product-item').forEach(function(p) {
            p.classList.remove('best-match');
        });
        var best = document.getElementById(recommendedProduct);
        if (best) {
            best.classList.add('best-match');
        }

        // Lead handoff: lean URL + enriched sessionStorage (full metrics + monthly)
        var cleanKwh = String(recommendedBattery);
        var cleanPeak = String(pv);
        var cleanSavings = String(Math.round(annualSavings));
        var cleanPlz = String(plz).replace(/\D/g, '').substring(0, 5);
        var cleanGoal = String(season || 'year');
        var cleanUsage = String(Math.round(totalConsumption));
        var cleanAutarky = String(Math.round(finalAutarky));
        var cleanSelf = String(Math.round(selfConsumptionRate));
        var cleanRoi = String(roiYears <= 20 ? Math.round(roiYears * 10) / 10 : 20);
        var monthNamesShort = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];
        var monthly = [];
        var monthlyCons = totalConsumption / 12;
        monthlyFactors.forEach(function(factor, idx) {
            var gen = Math.round(annualYield * factor);
            var cons = Math.round(monthlyCons);
            var stored = Math.round(Math.min(cons * (finalAutarky / 100), gen * 0.55));
            monthly.push({
                month: monthNamesShort[idx],
                generation: gen,
                consumption: cons,
                battery: stored
            });
        });

        var calcResult = {
            kwh: cleanKwh,
            peak: cleanPeak,
            savings: cleanSavings,
            plz: cleanPlz,
            goal: cleanGoal,
            usage: cleanUsage,
            autarky: cleanAutarky,
            selfConsumption: cleanSelf,
            roi: cleanRoi,
            amortisation: cleanRoi,
            annualYield: String(Math.round(annualYield)),
            monthly: monthly,
            source: 'solarbatterie-rechner-v3',
            version: 2
        };

        if (window.werduCalcHandoff) {
            window.werduCalcHandoff.persistAndLink(calcResult, {
                ensureCtaSelector: '.werdu-final-cta',
                ctaLabel: 'Kostenlose Fachanalyse anfordern →'
            });
        } else {
            try {
                sessionStorage.setItem('werdu_calc_result', JSON.stringify(calcResult));
                localStorage.setItem('werdu_calc_result', JSON.stringify(calcResult));
                localStorage.setItem('werdu_calc_data', JSON.stringify(calcResult));
            } catch (e) {}

            var beratungUrl = '/beratung-anfragen/?' +
                'kwh=' + encodeURIComponent(cleanKwh) +
                '&peak=' + encodeURIComponent(cleanPeak) +
                '&savings=' + encodeURIComponent(cleanSavings) +
                '&plz=' + encodeURIComponent(cleanPlz) +
                '&goal=' + encodeURIComponent(cleanGoal) +
                '&usage=' + encodeURIComponent(cleanUsage) +
                '&autarky=' + encodeURIComponent(cleanAutarky) +
                '&self=' + encodeURIComponent(cleanSelf) +
                '&verbrauch=' + encodeURIComponent(cleanUsage) +
                '&ersparnis=' + encodeURIComponent(cleanSavings) +
                '&autarkie=' + encodeURIComponent(cleanAutarky) +
                '&pv=' + encodeURIComponent(cleanPeak) +
                '&kapazitaet=' + encodeURIComponent(cleanKwh) +
                '&amortisation=' + encodeURIComponent(cleanRoi) +
                '&roi=' + encodeURIComponent(cleanRoi);

            var ctaLink = document.getElementById('werdu-beratung-cta');
            if (ctaLink) {
                ctaLink.href = beratungUrl;
                ctaLink.classList.add('werdu-calc-cta');
            }
        }

        goToStep(4);
    };

    function setDonutChart(fillId, textId, percent, color) {
        var circle = document.getElementById(fillId);
        var text = document.getElementById(textId);
        if (!circle || !text) return;

        var circumference = 2 * Math.PI * 80;
        var offset = circumference - (percent / 100) * circumference;

        circle.setAttribute('stroke-dasharray', circumference);
        circle.setAttribute('stroke-dashoffset', offset);
        circle.style.stroke = color;
        text.textContent = Math.round(percent) + '%';
    }

    function renderMonthlyChart(annualYield, consumption) {
        var container = document.getElementById('monthly-chart');
        if (!container) return;

        container.innerHTML = '';
        var monthlyConsumption = consumption / 12;
        var monthNames = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];

        monthlyFactors.forEach(function(factor, idx) {
            var monthlyYield = annualYield * factor;
            var maxVal = Math.max(monthlyYield, monthlyConsumption);
            var height = Math.max((monthlyYield / maxVal) * 100, 4);

            var bar = document.createElement('div');
            bar.className = 'werdu-month-bar';
            bar.style.height = height + '%';
            bar.setAttribute('data-value', Math.round(monthlyYield) + ' kWh');
            bar.title = monthNames[idx] + ': ' + Math.round(monthlyYield) + ' kWh Ertrag / ' + Math.round(monthlyConsumption) + ' kWh Verbrauch';
            container.appendChild(bar);
        });
    }

    document.addEventListener('DOMContentLoaded', init);

})();
