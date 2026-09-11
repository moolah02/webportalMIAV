{{-- Loading feedback: a thin bar at the top of the window, a busy cursor and a
     busy state on the button that submitted — shown for page changes, form
     submits and background requests (not for the notification poll). --}}
<style>
  #mvProgress { position: fixed; top: 0; left: 0; height: 3px; width: 0; z-index: 3000; pointer-events: none;
    background: var(--mv-accent, #2B64A8); box-shadow: 0 0 8px rgba(43, 100, 168, .45); opacity: 0;
    transition: width .35s ease, opacity .25s ease; }
  #mvProgress.is-on { opacity: 1; }
  html.mv-loading, html.mv-loading * { cursor: progress !important; }
  .mv-is-busy { pointer-events: none; opacity: .72; }
  button.mv-is-busy::after, a.mv-is-busy::after, input.mv-is-busy + .mv-busy-dot {
    content: ""; display: inline-block; width: 12px; height: 12px; margin-left: 8px; vertical-align: -2px;
    border: 2px solid currentColor; border-right-color: transparent; border-radius: 50%; animation: mvSpin .7s linear infinite; }
  @keyframes mvSpin { to { transform: rotate(360deg); } }
  @media (prefers-reduced-motion: reduce) { #mvProgress { transition: none; } button.mv-is-busy::after, a.mv-is-busy::after { animation: none; } }
</style>
<div id="mvProgress" aria-hidden="true"></div>
<script>
(function () {
  var bar = document.getElementById('mvProgress'), root = document.documentElement;
  var active = 0, width = 0, trickle = null, showTimer = null, safety = null;
  var FILE_URL = /\/(export|download)|\.(csv|xlsx?|pdf|zip)(\?|$)|[?&](export|format)=/i;
  var QUIET_URL = /\/notifications/i;

  function set(w) { width = w; bar.style.width = w + '%'; }
  function start() {
    clearTimeout(safety); safety = setTimeout(done, 20000);
    if (bar.classList.contains('is-on')) return;
    root.classList.add('mv-loading'); bar.classList.add('is-on'); set(12);
    clearInterval(trickle);
    trickle = setInterval(function () { if (width < 90) set(width + (90 - width) * 0.08); }, 250);
  }
  function done() {
    clearTimeout(showTimer); clearTimeout(safety); clearInterval(trickle);
    if (!bar.classList.contains('is-on')) { root.classList.remove('mv-loading'); return; }
    set(100);
    setTimeout(function () { bar.classList.remove('is-on'); root.classList.remove('mv-loading'); setTimeout(function () { set(0); }, 260); }, 220);
    document.querySelectorAll('.mv-is-busy').forEach(function (el) { el.classList.remove('mv-is-busy'); });
  }
  window.mvLoading = { start: start, done: done };

  // Page changes: internal links (skipped if the page handled the click itself).
  document.addEventListener('click', function (e) {
    if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    var a = e.target.closest && e.target.closest('a[href]');
    if (!a || a.target === '_blank' || a.hasAttribute('download')) return;
    var href = a.getAttribute('href') || '';
    if (href.charAt(0) === '#' || /^(javascript|mailto|tel):/i.test(href)) return;
    var url; try { url = new URL(a.href, location.href); } catch (err) { return; }
    if (url.origin !== location.origin || FILE_URL.test(url.pathname + url.search)) return;
    if (url.pathname === location.pathname && url.search === location.search && url.hash) return;
    start();
  });

  // Form submits (skipped if a script stopped the submit, or it downloads a file / opens a new tab).
  document.addEventListener('submit', function (e) {
    var form = e.target;
    setTimeout(function () {
      if (e.defaultPrevented || form.target === '_blank' || FILE_URL.test(form.getAttribute('action') || '')) return;
      start();
      var btn = e.submitter || form.querySelector('[type="submit"]');
      if (btn) btn.classList.add('mv-is-busy');
    }, 0);
  });

  // Background requests (fetch, XHR, jQuery): show the bar if one takes longer than 300 ms.
  function begin(url) {
    if (QUIET_URL.test(String(url || ''))) return false;
    active++; clearTimeout(showTimer); showTimer = setTimeout(function () { if (active > 0) start(); }, 300);
    return true;
  }
  function end() { active = Math.max(0, active - 1); if (active === 0) done(); }
  if (window.fetch) {
    var origFetch = window.fetch;
    window.fetch = function (input, init) {
      var tracked = begin(typeof input === 'string' ? input : (input && input.url));
      var p = origFetch.apply(this, arguments);
      if (tracked) p.then(end, end);
      return p;
    };
  }
  var origOpen = XMLHttpRequest.prototype.open, origSend = XMLHttpRequest.prototype.send;
  XMLHttpRequest.prototype.open = function (method, url) { this._mvUrl = url; return origOpen.apply(this, arguments); };
  XMLHttpRequest.prototype.send = function () {
    if (begin(this._mvUrl)) { this.addEventListener('loadend', end, { once: true }); }
    return origSend.apply(this, arguments);
  };

  window.addEventListener('pageshow', function () { active = 0; done(); });
})();
</script>
