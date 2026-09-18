{{-- Ctrl+K / ⌘K search: pages from the sidebar (instant) + terminals, visits, jobs,
     tickets, clients, projects and employees from /search (per the user's permissions). --}}
<div id="mvPalette" class="mv-pal hidden" role="dialog" aria-modal="true" aria-label="Search">
  <div class="mv-pal-backdrop" data-mv-pal-close></div>
  <div class="mv-pal-box">
    <div class="mv-pal-input">
      <svg class="mv-i" aria-hidden="true"><use href="#i-search"/></svg>
      <input id="mvPalInput" type="text" autocomplete="off" spellcheck="false"
             placeholder="Search terminals, visits, tickets, clients… or go to a page"
             role="combobox" aria-expanded="true" aria-controls="mvPalList" aria-autocomplete="list">
      <kbd>Esc</kbd>
    </div>
    <div id="mvPalList" class="mv-pal-list" role="listbox" aria-label="Results"></div>
    <div id="mvPalStatus" class="mv-pal-status hidden" role="status"></div>
    <div class="mv-pal-foot">
      <span><kbd>↑</kbd><kbd>↓</kbd> move</span>
      <span><kbd>Enter</kbd> open</span>
      <span><kbd>Esc</kbd> close</span>
    </div>
  </div>
</div>
<script>
(function () {
  var root = document.getElementById('mvPalette');
  if (!root) return;
  var input = document.getElementById('mvPalInput');
  var list = document.getElementById('mvPalList');
  var status = document.getElementById('mvPalStatus');
  var endpoint = @json(route('search'));
  var isMac = /Mac|iPhone|iPad/.test(navigator.platform || navigator.userAgent || '');

  document.querySelectorAll('[data-mv-kbd]').forEach(function (k) { k.textContent = isMac ? '⌘K' : 'Ctrl K'; });

  // Pages: every link in the sidebar (labels exactly as in the menu)
  var pages = [], seen = {};
  function collectPages() {
    pages = []; seen = {};
    document.querySelectorAll('.mv-side a[href]').forEach(function (a) {
      if (a.closest('.mv-me, .mv-brand')) return; // skip the logo link and the user row
      var clone = a.cloneNode(true);
      clone.querySelectorAll('.mv-ver, svg').forEach(function (n) { n.remove(); });
      var title = clone.textContent.replace(/\s+/g, ' ').trim();
      if (!title || seen[title] || a.getAttribute('href') === '#') return;
      seen[title] = true;
      var sec = a.closest('.mv-sec'), label = sec && sec.querySelector('.mv-sec-btn span');
      pages.push({ group: 'Pages', icon: 'arrow-right', title: title, sub: label ? label.textContent.trim() : '', url: a.href, newTab: a.target === '_blank' });
    });
  }

  var items = [], active = 0, timer = null, ctrl = null, lastFocus = null;

  function matchPages(q) {
    if (!q) return pages.slice(0, 8);
    var words = q.toLowerCase().split(/\s+/).filter(Boolean);
    return pages.filter(function (p) {
      var hay = (p.title + ' ' + p.sub).toLowerCase();
      return words.every(function (w) { return hay.indexOf(w) !== -1; });
    }).slice(0, 6);
  }

  function icon(name) {
    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('class', 'mv-i'); svg.setAttribute('aria-hidden', 'true');
    var use = document.createElementNS('http://www.w3.org/2000/svg', 'use');
    use.setAttribute('href', '#i-' + name);
    svg.appendChild(use);
    return svg;
  }

  function setStatus(msg) { status.textContent = msg || ''; status.classList.toggle('hidden', !msg); }

  function render(msg) {
    list.textContent = '';
    var group = null;
    items.forEach(function (it, i) {
      if (it.group !== group) {
        group = it.group;
        var h = document.createElement('div');
        h.className = 'mv-pal-group'; h.textContent = group;
        list.appendChild(h);
      }
      var a = document.createElement('a');
      a.href = it.url; a.id = 'mvPalItem' + i; a.className = 'mv-pal-item';
      a.setAttribute('role', 'option');
      if (it.newTab) { a.target = '_blank'; a.rel = 'noopener'; }
      var ic = document.createElement('span'); ic.className = 'mv-pal-ic'; ic.appendChild(icon(it.icon || 'arrow-right'));
      var tx = document.createElement('span'); tx.className = 'mv-pal-text';
      var t = document.createElement('strong'); t.textContent = it.title; tx.appendChild(t);
      if (it.sub) { var s = document.createElement('span'); s.textContent = it.sub; tx.appendChild(s); }
      a.appendChild(ic); a.appendChild(tx);
      a.addEventListener('mousemove', function () { if (active !== i) { active = i; highlight(false); } });
      a.addEventListener('click', function () { close(true); });
      list.appendChild(a);
    });
    highlight(true);
    setStatus(msg);
  }

  function highlight(reset) {
    list.querySelectorAll('.mv-pal-item').forEach(function (el, i) {
      el.classList.toggle('is-active', i === active);
      el.setAttribute('aria-selected', i === active ? 'true' : 'false');
    });
    var el = document.getElementById('mvPalItem' + active);
    if (el && !reset) el.scrollIntoView({ block: 'nearest' });
    if (reset) list.scrollTop = 0;
    input.setAttribute('aria-activedescendant', el ? el.id : '');
  }

  function search(q) {
    var pageHits = matchPages(q);
    items = pageHits; active = 0;
    if (ctrl) { ctrl.abort(); ctrl = null; }
    if (q.length < 2) { render(q ? 'Type one more letter to search records' : ''); return; }
    render('Searching…');
    ctrl = new AbortController();
    fetch(endpoint + '?q=' + encodeURIComponent(q), {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin', signal: ctrl.signal
    })
      .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(function (d) {
        if (input.value.trim() !== q) return;
        items = pageHits.concat(Array.isArray(d.results) ? d.results : []); active = 0;
        render(items.length ? '' : 'No matches for “' + q + '”');
      })
      .catch(function (e) {
        if (e.name === 'AbortError') return;
        render(items.length ? 'Records could not be searched just now; pages are still listed.' : 'Search is unavailable right now. Try again in a moment.');
      });
  }

  function go(it, newTab) {
    if (newTab || it.newTab) { window.open(it.url, '_blank', 'noopener'); close(false); }
    else { close(true); window.location.href = it.url; }
  }

  function open() {
    if (!pages.length) collectPages();
    lastFocus = document.activeElement;
    root.classList.remove('hidden');
    document.documentElement.style.overflow = 'hidden';
    input.value = '';
    items = matchPages(''); active = 0; render('');
    setTimeout(function () { input.focus(); }, 0);
  }

  function close(navigating) {
    if (root.classList.contains('hidden')) return;
    root.classList.add('hidden');
    document.documentElement.style.overflow = '';
    if (ctrl) { ctrl.abort(); ctrl = null; }
    clearTimeout(timer);
    if (!navigating && lastFocus && lastFocus.focus) lastFocus.focus();
  }

  input.addEventListener('input', function () {
    clearTimeout(timer);
    var q = input.value.trim();
    timer = setTimeout(function () { search(q); }, 160);
  });
  input.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowDown') { e.preventDefault(); if (items.length) { active = (active + 1) % items.length; highlight(false); } }
    else if (e.key === 'ArrowUp') { e.preventDefault(); if (items.length) { active = (active - 1 + items.length) % items.length; highlight(false); } }
    else if (e.key === 'Enter') { e.preventDefault(); if (items[active]) go(items[active], e.ctrlKey || e.metaKey); }
    else if (e.key === 'Escape') { e.preventDefault(); close(false); }
    else if (e.key === 'Tab') { e.preventDefault(); }
  });
  root.querySelector('[data-mv-pal-close]').addEventListener('click', function () { close(false); });

  document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && !e.altKey && !e.shiftKey && (e.key === 'k' || e.key === 'K')) {
      e.preventDefault();
      if (root.classList.contains('hidden')) open(); else close(false);
    }
  });

  window.mvPalette = { open: open, close: function () { close(false); } };
})();
</script>
