/* ============================================================
   WERDU.DE — PREMIUM PAGE INTERACTIONS v7.0
   File: /wp-content/themes/shoppingcart-child/js/werdu-pages.js
   Scope: Scroll reveals, counters, FAQ, progress bar
   Author: AI Design System
   ============================================================ */

(function() {
  'use strict';

  // ── CONFIG ─────────────────────────────────────────────────
  const CONFIG = {
    revealOffset: 0.15,
    revealThreshold: 0.1,
    counterDuration: 1800,
    lcosAnimateAt: 0.3,
    isDesktop: window.innerWidth >= 1024,
    prefersReducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches
  };

  // ── SCROLL PROGRESS BAR ────────────────────────────────────
  function initScrollProgress() {
    const bar = document.createElement('div');
    bar.className = 'wd-scroll-progress';
    bar.setAttribute('aria-hidden', 'true');
    document.body.appendChild(bar);

    let ticking = false;
    function updateProgress() {
      const scrollTop = window.scrollY || document.documentElement.scrollTop;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      const progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
      bar.style.width = progress + '%';
      ticking = false;
    }

    window.addEventListener('scroll', function() {
      if (!ticking) {
        requestAnimationFrame(updateProgress);
        ticking = true;
      }
    }, { passive: true });
  }

  // ── SCROLL REVEAL ──────────────────────────────────────────
  function initScrollReveal() {
    if (CONFIG.prefersReducedMotion) return;
    if (!CONFIG.isDesktop) return;

    const revealElements = document.querySelectorAll('.wd-reveal, .wd-step');
    if (!revealElements.length) return;

    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('wd-visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: CONFIG.revealThreshold,
      rootMargin: '0px 0px -' + (window.innerHeight * CONFIG.revealOffset) + 'px 0px'
    });

    revealElements.forEach(function(el) {
      observer.observe(el);
    });
  }

  // ── COUNTER ANIMATION ──────────────────────────────────────
  function initCounters() {
    if (CONFIG.prefersReducedMotion) {
      // Show final values immediately
      document.querySelectorAll('.wd-stat-num[data-target]').forEach(function(el) {
        var target = parseInt(el.getAttribute('data-target'));
        el.textContent = target.toLocaleString('de-DE');
      });
      return;
    }

    var nums = document.querySelectorAll('.wd-stat-num[data-target]');
    if (!nums.length) return;

    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var target = parseInt(el.getAttribute('data-target'));
          var duration = CONFIG.counterDuration;
          var start = performance.now();

          function easeOutCubic(t) {
            return 1 - Math.pow(1 - t, 3);
          }

          function update(now) {
            var progress = Math.min((now - start) / duration, 1);
            var eased = easeOutCubic(progress);
            var current = Math.floor(eased * target);
            el.textContent = current.toLocaleString('de-DE');
            if (progress < 1) {
              requestAnimationFrame(update);
            } else {
              el.textContent = target.toLocaleString('de-DE');
            }
          }

          requestAnimationFrame(update);
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.3 });

    nums.forEach(function(n) { observer.observe(n); });
  }

  // ── LCOS BAR ANIMATION ───────────────────────────────────
  function initLCOSBars() {
    if (CONFIG.prefersReducedMotion) {
      document.querySelectorAll('.wd-lcos-fill').forEach(function(bar) {
        bar.style.width = bar.style.width || '100%';
      });
      return;
    }

    var bars = document.querySelectorAll('.wd-lcos-fill');
    if (!bars.length) return;

    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          var bar = entry.target;
          var targetWidth = bar.getAttribute('data-width') || bar.style.width;
          if (targetWidth) {
            bar.style.width = '0%';
            setTimeout(function() {
              bar.style.width = targetWidth;
            }, 100);
          }
          observer.unobserve(bar);
        }
      });
    }, { threshold: CONFIG.lcosAnimateAt });

    bars.forEach(function(bar) {
      var currentWidth = bar.style.width;
      if (currentWidth) {
        bar.setAttribute('data-width', currentWidth);
        bar.style.width = '0%';
      }
      observer.observe(bar);
    });
  }

  // ── FAQ SMOOTH OPEN/CLOSE ──────────────────────────────────
  function initFAQ() {
    var faqItems = document.querySelectorAll('.wd-faq details');
    if (!faqItems.length) return;

    faqItems.forEach(function(item) {
      item.addEventListener('click', function(e) {
        // Close other items when opening one
        if (!item.hasAttribute('open')) {
          faqItems.forEach(function(other) {
            if (other !== item && other.hasAttribute('open')) {
              other.removeAttribute('open');
            }
          });
        }
      });
    });
  }

  // ── FLOATING PARTICLES (Hero only, desktop) ───────────────
  function initParticles() {
    if (!CONFIG.isDesktop || CONFIG.prefersReducedMotion) return;

    var hero = document.querySelector('.wd-hero');
    if (!hero) return;

    var container = document.createElement('div');
    container.className = 'wd-particles';
    container.setAttribute('aria-hidden', 'true');

    var particleCount = 15;
    for (var i = 0; i < particleCount; i++) {
      var particle = document.createElement('div');
      particle.className = 'wd-particle';
      var size = Math.random() * 6 + 2;
      particle.style.width = size + 'px';
      particle.style.height = size + 'px';
      particle.style.left = Math.random() * 100 + '%';
      particle.style.animationDuration = (Math.random() * 15 + 10) + 's';
      particle.style.animationDelay = (Math.random() * 10) + 's';
      container.appendChild(particle);
    }

    hero.appendChild(container);
  }

  // ── SMOOTH SCROLL FOR ANCHOR LINKS ───────────────────────
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(function(link) {
      link.addEventListener('click', function(e) {
        var targetId = this.getAttribute('href');
        if (targetId === '#') return;
        var target = document.querySelector(targetId);
        if (target) {
          e.preventDefault();
          var offset = 80;
          var targetPosition = target.getBoundingClientRect().top + window.scrollY - offset;
          window.scrollTo({
            top: targetPosition,
            behavior: CONFIG.prefersReducedMotion ? 'auto' : 'smooth'
          });
        }
      });
    });
  }

  // ── BUTTON RIPPLE EFFECT ─────────────────────────────────
  function initRippleEffect() {
    if (!CONFIG.isDesktop) return;

    document.querySelectorAll('.wd-btn-product, .wd-btn-cta, .wd-hero-cta').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        var rect = this.getBoundingClientRect();
        var ripple = document.createElement('span');
        ripple.style.cssText = [
          'position:absolute',
          'border-radius:50%',
          'background:rgba(255,255,255,0.3)',
          'transform:scale(0)',
          'animation:wdRipple 0.6s ease-out',
          'pointer-events:none',
          'width:20px',
          'height:20px',
          'left:' + (e.clientX - rect.left - 10) + 'px',
          'top:' + (e.clientY - rect.top - 10) + 'px'
        ].join(';');
        this.appendChild(ripple);
        setTimeout(function() { ripple.remove(); }, 600);
      });
    });

    // Add ripple keyframe dynamically
    var style = document.createElement('style');
    style.textContent = '@keyframes wdRipple { to { transform: scale(15); opacity: 0; } }';
    document.head.appendChild(style);
  }

  // ── PARALLAX HERO (Desktop only) ───────────────────────────
  function initParallax() {
    if (!CONFIG.isDesktop || CONFIG.prefersReducedMotion) return;

    var hero = document.querySelector('.wd-hero');
    if (!hero) return;

    var ticking = false;
    window.addEventListener('scroll', function() {
      if (!ticking) {
        requestAnimationFrame(function() {
          var scrollY = window.scrollY;
          if (scrollY < window.innerHeight) {
            var parallax = hero.querySelector('.wd-hero-inner');
            if (parallax) {
              parallax.style.transform = 'translateY(' + (scrollY * 0.15) + 'px)';
            }
          }
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }

  // ── NAVBAR SCROLL STATE (if navbar exists) ───────────────
  function initNavbarScroll() {
    var lastScroll = 0;
    var ticking = false;

    window.addEventListener('scroll', function() {
      if (!ticking) {
        requestAnimationFrame(function() {
          var currentScroll = window.scrollY;
          document.body.classList.toggle('wd-scrolled', currentScroll > 50);
          document.body.classList.toggle('wd-scrolled-down', currentScroll > lastScroll && currentScroll > 200);
          lastScroll = currentScroll;
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }

  // ── INITIALIZE ALL ───────────────────────────────────────
  function init() {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', runInit);
    } else {
      runInit();
    }
  }

  function runInit() {
    initScrollProgress();
    initScrollReveal();
    initCounters();
    initLCOSBars();
    initFAQ();
    initParticles();
    initSmoothScroll();
    initRippleEffect();
    initParallax();
    initNavbarScroll();
  }

  init();

})();