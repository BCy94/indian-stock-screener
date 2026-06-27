/* So Called Investor — shared site interactions */
(function () {
  'use strict';

  /* ---------- Theme toggle ---------- */
  var root = document.documentElement;
  var saved = localStorage.getItem('sci-theme');
  var prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
  if (saved) {
    root.setAttribute('data-theme', saved);
  } else if (prefersLight) {
    root.setAttribute('data-theme', 'light');
  }

  function toggleTheme() {
    var current = root.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
    var next = current === 'light' ? 'dark' : 'light';
    if (next === 'dark') {
      root.removeAttribute('data-theme');
    } else {
      root.setAttribute('data-theme', 'light');
    }
    localStorage.setItem('sci-theme', next);
  }

  document.querySelectorAll('.theme-toggle').forEach(function (btn) {
    btn.addEventListener('click', toggleTheme);
  });

  /* ---------- Navbar scroll state ---------- */
  var navbar = document.querySelector('.navbar');
  function onScroll() {
    if (!navbar) return;
    navbar.classList.toggle('scrolled', window.scrollY > 30);
    var btt = document.querySelector('.back-to-top');
    if (btt) btt.classList.toggle('show', window.scrollY > 600);
  }
  document.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------- Mobile menu ---------- */
  var burger = document.querySelector('.nav-burger');
  var panel = document.querySelector('.mobile-panel');
  if (burger && panel) {
    burger.addEventListener('click', function () {
      burger.classList.toggle('open');
      panel.classList.toggle('open');
    });
    panel.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () {
        burger.classList.remove('open');
        panel.classList.remove('open');
      });
    });
  }

  /* ---------- Active nav link ---------- */
  var path = (window.location.pathname.split('/').pop() || 'index.html');
  document.querySelectorAll('.nav-links a, .mobile-panel a').forEach(function (a) {
    var href = a.getAttribute('href');
    if (href === path || (path === '' && href === 'index.html')) {
      a.classList.add('active');
    }
  });

  /* ---------- Back to top ---------- */
  var backToTop = document.querySelector('.back-to-top');
  if (backToTop) {
    backToTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ---------- Scroll reveal ---------- */
  var revealEls = document.querySelectorAll('.reveal, .reveal-stagger');
  if ('IntersectionObserver' in window && revealEls.length) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });
    revealEls.forEach(function (el) { io.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('in'); });
  }

  /* ---------- Animated counters ---------- */
  function animateCount(el) {
    var target = parseFloat(el.getAttribute('data-count'));
    var decimals = el.getAttribute('data-decimals') ? parseInt(el.getAttribute('data-decimals'), 10) : 0;
    var suffix = el.getAttribute('data-suffix') || '';
    var duration = 1400;
    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      var value = target * eased;
      el.textContent = value.toFixed(decimals) + suffix;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  var counters = document.querySelectorAll('[data-count]');
  if ('IntersectionObserver' in window && counters.length) {
    var countIo = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCount(entry.target);
          countIo.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });
    counters.forEach(function (el) { countIo.observe(el); });
  }

  /* ---------- Stock ticker tape ---------- */
  var TICKER_STOCKS = [
    ['RELIANCE', 2954.10, 1.24], ['TCS', 4102.55, -0.42], ['HDFCBANK', 1687.30, 0.85],
    ['INFY', 1842.75, 1.63], ['ICICIBANK', 1264.90, -0.31], ['HINDUNILVR', 2398.20, 0.18],
    ['SBIN', 832.45, 2.05], ['BHARTIARTL', 1598.60, 0.97], ['ITC', 468.35, -0.56],
    ['LT', 3712.80, 1.12], ['KOTAKBANK', 1789.15, -0.22], ['AXISBANK', 1142.50, 0.64],
    ['BAJFINANCE', 7245.90, 1.88], ['MARUTI', 12480.25, -0.71], ['ASIANPAINT', 2865.40, 0.39],
    ['SUNPHARMA', 1789.60, 1.04], ['TITAN', 3542.15, -0.18], ['WIPRO', 562.80, 0.93],
    ['ONGC', 268.45, -0.85], ['TATAMOTORS', 968.20, 2.41]
  ];

  function buildTicker() {
    var track = document.querySelector('.ticker-track');
    if (!track) return;
    var html = '';
    var doubled = TICKER_STOCKS.concat(TICKER_STOCKS);
    doubled.forEach(function (s) {
      var up = s[2] >= 0;
      html += '<div class="ticker-item">' +
        '<span class="sym">' + s[0] + '</span>' +
        '<span>₹' + s[1].toLocaleString('en-IN', { minimumFractionDigits: 2 }) + '</span>' +
        '<span class="chg ' + (up ? 'up' : 'down') + '">' + (up ? '▲' : '▼') + ' ' + Math.abs(s[2]).toFixed(2) + '%</span>' +
        '</div>';
    });
    track.innerHTML = html;
  }
  buildTicker();

  /* ---------- Filter pills (courses/materials) ---------- */
  var filterRow = document.querySelector('.filter-row');
  if (filterRow) {
    var filterPills = filterRow.querySelectorAll('.filter-pill');
    var filterTargets = document.querySelectorAll('[data-level]');
    filterPills.forEach(function (pill) {
      pill.addEventListener('click', function () {
        filterPills.forEach(function (p) { p.classList.remove('active'); });
        pill.classList.add('active');
        var filter = pill.getAttribute('data-filter') || 'all';
        filterTargets.forEach(function (card) {
          var levels = (card.getAttribute('data-level') || '').split(/\s+/);
          var show = filter === 'all' || levels.indexOf(filter) !== -1;
          card.style.display = show ? '' : 'none';
        });
      });
    });
  }

  /* ---------- Accordion (FAQ) ---------- */
  document.querySelectorAll('.accordion-head').forEach(function (head) {
    head.addEventListener('click', function () {
      var item = head.closest('.accordion-item');
      var body = item.querySelector('.accordion-body');
      var isOpen = item.classList.contains('open');
      item.parentElement.querySelectorAll('.accordion-item.open').forEach(function (other) {
        other.classList.remove('open');
        other.querySelector('.accordion-body').style.maxHeight = null;
      });
      if (!isOpen) {
        item.classList.add('open');
        body.style.maxHeight = body.scrollHeight + 'px';
      }
    });
  });

  /* ---------- Investment calculator (SIP / Lumpsum) ---------- */
  var calc = document.querySelector('[data-calculator]');
  if (calc) {
    var calcMode = 'sip';

    function calcGet(name) {
      var el = calc.querySelector('[data-calc-input="' + name + '"]');
      return el ? parseFloat(el.value) || 0 : 0;
    }

    function fmtINR(n) {
      return '₹' + Math.round(n).toLocaleString('en-IN');
    }

    function runCalc() {
      var rate = calcGet('rate');
      var years = calcGet('years');
      var monthlyRate = rate / 100 / 12;
      var invested, total;
      var series = [];

      if (calcMode === 'sip') {
        var amount = calcGet('amount');
        invested = amount * years * 12;
        for (var y = 1; y <= years; y++) {
          var n = y * 12;
          var fv = monthlyRate > 0
            ? amount * ((Math.pow(1 + monthlyRate, n) - 1) / monthlyRate) * (1 + monthlyRate)
            : amount * n;
          series.push(fv);
        }
      } else {
        var lumpsum = calcGet('lumpsum');
        invested = lumpsum;
        for (var y2 = 1; y2 <= years; y2++) {
          series.push(lumpsum * Math.pow(1 + rate / 100, y2));
        }
      }
      total = series.length ? series[series.length - 1] : 0;
      var returns = total - invested;

      var totalOut = calc.querySelector('[data-calc-out="total"]');
      var investedOut = calc.querySelector('[data-calc-out="invested"]');
      var returnsOut = calc.querySelector('[data-calc-out="returns"]');
      var barOut = calc.querySelector('[data-calc-out="bar"]');
      if (totalOut) totalOut.textContent = fmtINR(total);
      if (investedOut) investedOut.textContent = fmtINR(invested);
      if (returnsOut) returnsOut.textContent = fmtINR(returns);
      if (barOut) barOut.style.width = (total > 0 ? Math.min(100, (invested / total) * 100) : 0) + '%';

      var chart = calc.querySelector('[data-calc-out="chart"]');
      if (chart && series.length) {
        var max = Math.max.apply(null, series);
        var w = 300, h = 140;
        var pts = series.map(function (v, i) {
          var x = series.length === 1 ? w : (i / (series.length - 1)) * w;
          var py = h - (max > 0 ? (v / max) * (h - 10) : 0) - 4;
          return [x, py];
        });
        var line = 'M' + pts.map(function (p) { return p[0].toFixed(1) + ',' + p[1].toFixed(1); }).join(' L');
        var linePath = chart.querySelector('path.line');
        var fillPath = chart.querySelector('path.fill');
        if (linePath) linePath.setAttribute('d', line);
        if (fillPath) fillPath.setAttribute('d', line + ' L' + w + ',' + h + ' L0,' + h + ' Z');
      }
    }

    calc.querySelectorAll('[data-calc-input]').forEach(function (input) {
      input.addEventListener('input', function () {
        var name = input.getAttribute('data-calc-input');
        var slider = calc.querySelector('[data-calc-slider="' + name + '"]');
        if (slider) slider.value = input.value;
        runCalc();
      });
    });
    calc.querySelectorAll('[data-calc-slider]').forEach(function (slider) {
      slider.addEventListener('input', function () {
        var name = slider.getAttribute('data-calc-slider');
        var input = calc.querySelector('[data-calc-input="' + name + '"]');
        if (input) input.value = slider.value;
        runCalc();
      });
    });

    var calcTabs = calc.querySelectorAll('[data-calc-mode]');
    calcTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        calcTabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');
        calcMode = tab.getAttribute('data-calc-mode');
        calc.querySelectorAll('[data-calc-mode-field]').forEach(function (field) {
          field.hidden = field.getAttribute('data-calc-mode-field') !== calcMode;
        });
        runCalc();
      });
    });

    runCalc();
  }

  /* ---------- Placeholder form handling (no backend yet) ---------- */
  document.querySelectorAll('form[data-placeholder-form]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var msg = form.querySelector('.form-msg');
      if (msg) {
        msg.textContent = msg.getAttribute('data-success') || 'Thanks! We’ll be in touch soon.';
        msg.classList.add('show');
      }
      form.reset();
    });
  });
})();
