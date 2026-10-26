    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<div class="wp-progress" id="wpProgress"><span></span></div>
<div class="wp-progress-label" id="wpProgressLabel"></div>
<script>
(function () {
  'use strict';

  /* ── Advanced search: attaches to every admin table automatically ──
     Keyword search (optionally scoped to one column), auto-built dropdown
     filters for low-variety columns (status, category, type…), click-to-sort
     headers, match highlighting and a live result count. Opt out with
     data-no-search on the table. */

  function cellValue(td) {
    if (!td) return '';
    var parts = [];
    td.querySelectorAll('input:not([type=hidden]):not([type=checkbox]):not([type=file]), textarea').forEach(function (el) { parts.push(el.value); });
    td.querySelectorAll('select').forEach(function (el) {
      var opt = el.options[el.selectedIndex];
      if (opt) parts.push(opt.text);
    });
    var clone = td.cloneNode(true);
    clone.querySelectorAll('input, select, textarea, script, .wp-row-actions, button').forEach(function (el) { el.remove(); });
    parts.push(clone.textContent);
    return parts.join(' ').replace(/\s+/g, ' ').trim();
  }

  function normalise(text) { return text.toLowerCase(); }

  function clearHighlights(root) {
    root.querySelectorAll('mark.pq-hit').forEach(function (m) {
      m.replaceWith(document.createTextNode(m.textContent));
    });
    root.normalize();
  }

  function highlight(root, term) {
    if (!term) return;
    var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
      acceptNode: function (node) {
        var p = node.parentElement;
        if (!p || p.closest('script, style, select, option, button, textarea, .wp-row-actions')) return NodeFilter.FILTER_REJECT;
        return node.nodeValue.toLowerCase().indexOf(term) !== -1 ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
      }
    });
    var nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);
    nodes.forEach(function (node) {
      var text = node.nodeValue, lower = text.toLowerCase(), frag = document.createDocumentFragment(), i = 0, idx;
      while ((idx = lower.indexOf(term, i)) !== -1) {
        frag.appendChild(document.createTextNode(text.slice(i, idx)));
        var mark = document.createElement('mark');
        mark.className = 'pq-hit';
        mark.textContent = text.slice(idx, idx + term.length);
        frag.appendChild(mark);
        i = idx + term.length;
      }
      frag.appendChild(document.createTextNode(text.slice(i)));
      node.replaceWith(frag);
    });
  }

  var searchers = [];

  function enhanceTable(table) {
    var tbody = table.tBodies[0];
    var head = table.tHead && table.tHead.rows[0];
    if (!tbody || !head) return;

    // Group each data row with any follow-on "detail" rows (a single wide
    // cell spanning the table) so they filter and sort together.
    var groups = [];
    Array.prototype.forEach.call(tbody.rows, function (row) {
      var isDetail = row.cells.length === 1 && row.cells[0].colSpan > 1;
      if (isDetail && groups.length) {
        groups[groups.length - 1].rows.push(row);
      } else if (!isDetail) {
        groups.push({ rows: [row] });
      }
    });
    if (groups.length < 2) return;

    // Spreadsheet row numbers always count the visible rows top to bottom.
    function renumber() {
      var n = 0;
      Array.prototype.forEach.call(tbody.rows, function (r) {
        var cell = r.querySelector('td.sheet-num');
        if (cell && !r.hidden) cell.textContent = ++n;
      });
    }

    var headers = Array.prototype.map.call(head.cells, function (th) { return th.textContent.trim(); });
    groups.forEach(function (g, i) {
      g.index = i;
      g.cells = Array.prototype.map.call(g.rows[0].cells, cellValue);
      g.all = normalise(g.rows.map(function (r) { return cellValue(r); }).join(' ') + ' ' + g.cells.join(' '));
    });

    // Build toolbar.
    var bar = document.createElement('div');
    bar.className = 'pq-search';
    bar.innerHTML =
      '<div class="pq-search-input"><i class="bi bi-search"></i>' +
      '<input type="search" class="form-control form-control-sm" placeholder="Search ' + groups.length + ' items…" autocomplete="off"></div>' +
      '<select class="form-select form-select-sm" data-scope title="Search in"><option value="">All columns</option></select>';
    var input = bar.querySelector('input');
    var scope = bar.querySelector('[data-scope]');
    headers.forEach(function (h, i) {
      if (!h) return;
      var o = document.createElement('option');
      o.value = String(i);
      o.textContent = 'In: ' + h;
      scope.appendChild(o);
    });

    // Dropdown filters for columns with a handful of repeated values.
    var filters = [];
    var declared = head.querySelector('th[data-filter]') !== null;
    headers.forEach(function (h, col) {
      if (!h) return;
      if (declared && !head.cells[col].hasAttribute('data-filter')) return;
      var counts = {};
      groups.forEach(function (g) {
        var v = (g.cells[col] || '').trim();
        if (v && v.length <= 40) counts[v] = (counts[v] || 0) + 1;
      });
      var values = Object.keys(counts);
      var covered = values.reduce(function (n, v) { return n + counts[v]; }, 0);
      if (values.length < 2 || values.length > 10 || values.length >= groups.length || covered < groups.length * 0.6) return;
      if (values.every(function (v) { return /^[\d\s\/.:,\-—]+$/.test(v); })) return; // numbers & dates sort instead
      var sel = document.createElement('select');
      sel.className = 'form-select form-select-sm';
      sel.title = 'Filter by ' + h;
      sel.innerHTML = '<option value="">All ' + h.toLowerCase() + '</option>';
      values.sort().forEach(function (v) {
        var o = document.createElement('option');
        o.value = v;
        o.textContent = v + ' (' + counts[v] + ')';
        sel.appendChild(o);
      });
      bar.appendChild(sel);
      filters.push({ col: col, el: sel });
    });

    var clear = document.createElement('button');
    clear.type = 'button';
    clear.className = 'btn btn-link btn-sm pq-search-clear';
    clear.textContent = 'Clear';
    clear.hidden = true;
    bar.appendChild(clear);

    var count = document.createElement('span');
    count.className = 'pq-search-count';
    bar.appendChild(count);

    var empty = document.createElement('tr');
    empty.innerHTML = '<td colspan="' + head.cells.length + '" class="text-center text-muted py-4">No items match your search.</td>';
    empty.hidden = true;
    tbody.appendChild(empty);

    var anchor = table.closest('.table-responsive') || table;
    anchor.parentNode.insertBefore(bar, anchor);

    function apply() {
      var term = normalise(input.value.trim());
      var col = scope.value;
      var shown = 0;
      clearHighlights(tbody);
      groups.forEach(function (g) {
        var hay = col === '' ? g.all : normalise(g.cells[+col] || '');
        var ok = term.split(/\s+/).every(function (word) { return !word || hay.indexOf(word) !== -1; });
        filters.forEach(function (f) {
          if (f.el.value && (g.cells[f.col] || '').trim() !== f.el.value) ok = false;
        });
        g.rows.forEach(function (r) { r.hidden = !ok; });
        if (ok) {
          shown++;
          if (term) g.rows.forEach(function (r) { term.split(/\s+/).forEach(function (w) { if (w) highlight(r, w); }); });
        }
      });
      empty.hidden = shown !== 0;
      renumber();
      var active = term !== '' || filters.some(function (f) { return f.el.value; });
      clear.hidden = !active;
      count.textContent = active ? shown + ' of ' + groups.length + ' shown' : groups.length + ' items';
    }

    input.addEventListener('input', apply);
    scope.addEventListener('change', apply);
    filters.forEach(function (f) { f.el.addEventListener('change', apply); });
    clear.addEventListener('click', function () {
      input.value = '';
      scope.value = '';
      filters.forEach(function (f) { f.el.value = ''; });
      apply();
      input.focus();
    });

    // Click a header to sort; click again to reverse; a third time restores.
    Array.prototype.forEach.call(head.cells, function (th, col) {
      if (!headers[col] || th.querySelector('input')) return;
      th.classList.add('pq-sortable');
      th.addEventListener('click', function () {
        var dir = th.classList.contains('is-asc') ? 'desc' : th.classList.contains('is-desc') ? '' : 'asc';
        Array.prototype.forEach.call(head.cells, function (c) { c.classList.remove('is-asc', 'is-desc'); });
        if (dir) th.classList.add('is-' + dir);
        var sorted = groups.slice().sort(function (a, b) {
          if (!dir) return a.index - b.index;
          var x = a.cells[col] || '', y = b.cells[col] || '';
          var nx = parseFloat(x.replace(/[^0-9.\-]/g, '')), ny = parseFloat(y.replace(/[^0-9.\-]/g, ''));
          var cmp = (!isNaN(nx) && !isNaN(ny) && /^[\s$€£KSh.,0-9\-\/]+$/i.test(x + y)) ? nx - ny : x.localeCompare(y, undefined, { numeric: true, sensitivity: 'base' });
          return dir === 'asc' ? cmp : -cmp;
        });
        sorted.forEach(function (g) { g.rows.forEach(function (r) { tbody.insertBefore(r, empty); }); });
        renumber();
      });
    });

    apply();
    searchers.push(input);
  }

  document.querySelectorAll('.content-card table, .wp-list table').forEach(function (table) {
    if (table.hasAttribute('data-no-search')) return;
    enhanceTable(table);
  });

  // The top bar search box drives the first table on the page.
  var top = document.getElementById('adminTopSearch');
  if (top) {
    if (!searchers.length) {
      top.closest('.topbar-search').style.display = 'none';
    } else {
      top.addEventListener('input', function () {
        searchers[0].value = top.value;
        searchers[0].dispatchEvent(new Event('input'));
      });
      top.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); searchers[0].scrollIntoView({ behavior: 'smooth', block: 'center' }); }
      });
    }
  }

  /* ── Drag & drop image pickers with instant previews ── */
  document.querySelectorAll('[data-dropzone]').forEach(function (zone) {
    var input = zone.querySelector('input[type=file]');
    var previews = zone.querySelector('.wp-dropzone-previews');
    if (!input.multiple) zone.classList.add('is-single');
    var original = previews.innerHTML;
    var strongEl = zone.querySelector('.wp-dropzone-empty strong');
    var strongText = strongEl ? strongEl.innerHTML : '';

    function render() {
      var files = Array.prototype.slice.call(input.files || []);
      previews.innerHTML = files.length ? '' : original;
      files.forEach(function (file) {
        if (!/^image\//.test(file.type)) return;
        var img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.alt = file.name;
        previews.appendChild(img);
      });
      zone.classList.toggle('has-files', files.length > 0 || original.trim() !== '');
      var strong = zone.querySelector('.wp-dropzone-empty strong');
      if (strong && files.length) strong.innerHTML = files.length + ' picture' + (files.length > 1 ? 's' : '') + ' selected — <u>change</u>';
    }

    input.addEventListener('change', render);

    // Used by the slide-in editor to show the record's current image (or none).
    zone.dzReset = function (src) {
      input.value = '';
      original = src ? '<img src="' + src.replace(/"/g, '&quot;') + '" alt="">' : '';
      if (strongEl) strongEl.innerHTML = strongText;
      render();
    };
    ['dragenter', 'dragover'].forEach(function (ev) { zone.addEventListener(ev, function () { zone.classList.add('is-over'); }); });
    ['dragleave', 'drop'].forEach(function (ev) { zone.addEventListener(ev, function () { zone.classList.remove('is-over'); }); });
  });

  /* ── Upload progress for multipart forms ──
     Sends the form with XHR so the admin sees real progress instead of a
     frozen page, then follows the server's redirect like a normal submit. */
  var bar = document.getElementById('wpProgress');
  var label = document.getElementById('wpProgressLabel');
  document.querySelectorAll('form[enctype="multipart/form-data"]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var hasFile = Array.prototype.some.call(form.querySelectorAll('input[type=file]'), function (i) { return i.files && i.files.length; });
      if (!hasFile || !window.FormData || e.defaultPrevented) return;
      e.preventDefault();
      var data = new FormData(form);
      if (e.submitter && e.submitter.name) data.append(e.submitter.name, e.submitter.value);
      form.querySelectorAll('button, input[type=submit]').forEach(function (b) { b.disabled = true; });
      bar.style.display = 'block';
      label.style.display = 'block';
      label.textContent = 'Uploading… 0%';
      var xhr = new XMLHttpRequest();
      xhr.open('POST', form.getAttribute('action') || window.location.href);
      xhr.upload.addEventListener('progress', function (ev) {
        if (!ev.lengthComputable) return;
        var pct = Math.round(ev.loaded / ev.total * 100);
        bar.firstElementChild.style.width = pct + '%';
        label.textContent = pct < 100 ? 'Uploading… ' + pct + '%' : 'Processing images…';
      });
      // The redirect was already followed (and its flash message consumed),
      // so render that response rather than requesting the page again.
      xhr.addEventListener('load', function () {
        if (xhr.responseURL) history.replaceState(null, '', xhr.responseURL);
        document.open();
        document.write(xhr.responseText);
        document.close();
      });
      xhr.addEventListener('error', function () { form.submit(); });
      xhr.send(data);
    });
  });

  /* ── Spreadsheet slide-in editor ── */
  function toLocalDateTime(v) {
    return v ? String(v).replace(' ', 'T').slice(0, 16) : '';
  }

  document.querySelectorAll('.sheet-editor').forEach(function (panel) {
    var sheetId = panel.id.replace(/^sheet-/, '');
    var form = panel.querySelector('[data-sheet-form]');
    var title = panel.querySelector('[data-sheet-title]');
    var singular = panel.getAttribute('data-singular');
    var del = panel.querySelector('[data-sheet-delete]');
    var delForm = document.getElementById(panel.id + '-delete');
    var imgForm = document.getElementById(panel.id + '-image');
    var galleryWrap = panel.querySelector('[data-sheet-gallery-wrap]');
    var offcanvas = bootstrap.Offcanvas.getOrCreateInstance(panel);

    function open(record) {
      var isEdit = !!record;
      record = record || {};
      form.reset();
      form.elements.action.value = isEdit ? form.getAttribute('data-update') : form.getAttribute('data-create');
      form.elements.id.value = record.id || '';
      title.textContent = (isEdit ? 'Edit ' : 'Add ') + singular;
      del.hidden = !isEdit;
      if (delForm) delForm.elements.id.value = record.id || '';
      if (imgForm) imgForm.elements.id.value = record.id || '';

      Array.prototype.forEach.call(form.elements, function (el) {
        if (!el.name || el.name === 'csrf' || el.name === 'action' || el.name === 'id' || el.type === 'file') return;
        var has = Object.prototype.hasOwnProperty.call(record, el.name);
        var value = has ? record[el.name] : el.getAttribute('data-default');
        if (value === null || value === undefined) value = '';
        if (el.type === 'checkbox') {
          el.checked = has ? (value === true || value === 1 || value === '1') : value === '1';
        } else if (el.type === 'datetime-local') {
          el.value = toLocalDateTime(value);
        } else if (el.tagName === 'SELECT') {
          el.value = String(value);
          if (el.selectedIndex === -1) el.selectedIndex = 0;
        } else {
          el.value = value;
        }
      });

      panel.querySelectorAll('[data-sheet-preview]').forEach(function (wrap) {
        var zone = wrap.querySelector('[data-dropzone]');
        if (zone && zone.dzReset) zone.dzReset(record['_preview_' + wrap.getAttribute('data-sheet-preview')] || '');
      });
      panel.querySelectorAll('[data-sheet-video]').forEach(function (box) {
        var current = record[box.getAttribute('data-sheet-video') === 'offer_video' ? 'video_url' : box.getAttribute('data-sheet-video')];
        box.textContent = current ? 'Current video: ' + String(current).split('/').pop() + ' — choose a file to replace it.' : '';
      });

      if (galleryWrap) {
        var grid = galleryWrap.querySelector('[data-sheet-gallery]');
        var images = record._gallery || [];
        grid.innerHTML = '';
        images.forEach(function (img) {
          var item = document.createElement('div');
          item.className = 'wp-media-item';
          item.innerHTML = '<img alt=""><button type="submit" title="Remove picture">&times;</button>';
          item.querySelector('img').src = img.src;
          var btn = item.querySelector('button');
          btn.setAttribute('form', imgForm.id);
          btn.name = 'image_id';
          btn.value = img.id;
          btn.addEventListener('click', function (e) { if (!confirm('Remove this picture?')) e.preventDefault(); });
          grid.appendChild(item);
        });
        galleryWrap.hidden = !isEdit;
        galleryWrap.querySelector('[data-sheet-gallery-empty]').hidden = images.length > 0;
      }

      offcanvas.show();
    }

    panel.addEventListener('shown.bs.offcanvas', function () {
      var first = form.querySelector('input:not([type=hidden]):not([type=file]):not([type=checkbox]), textarea');
      if (first) first.focus();
    });

    document.querySelectorAll('[data-sheet-new="' + sheetId + '"]').forEach(function (btn) {
      btn.addEventListener('click', function () { open(null); });
    });

    var table = document.querySelector('table[data-sheet="' + sheetId + '"]');
    if (table) {
      table.tBodies[0].addEventListener('click', function (e) {
        var row = e.target.closest('tr[data-record]');
        if (!row || (e.target.closest('a, input, select') && !e.target.closest('.sheet-edit'))) return;
        open(JSON.parse(row.getAttribute('data-record')));
      });
      table.tBodies[0].addEventListener('keydown', function (e) {
        var row = e.target.closest('tr[data-record]');
        if (row && e.key === 'Enter') open(JSON.parse(row.getAttribute('data-record')));
      });
    }

    // Reopen the record after a failed save or image removal (?edit=<id>).
    var reopen = new URLSearchParams(window.location.search).get('edit');
    if (reopen && table) {
      var match = Array.prototype.find.call(table.tBodies[0].rows, function (r) {
        return r.dataset.record && JSON.parse(r.dataset.record).id === parseInt(reopen, 10);
      });
      if (match) open(JSON.parse(match.dataset.record));
    }
  });

  /* ── Bulk-action "select all" checkboxes ── */
  document.querySelectorAll('[data-check-all]').forEach(function (master) {
    var table = master.closest('table');
    master.addEventListener('change', function () {
      table.querySelectorAll('tbody input[type=checkbox][name="ids[]"]').forEach(function (cb) {
        if (!cb.closest('tr').hidden) cb.checked = master.checked;
      });
    });
  });

  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm(el.getAttribute('data-confirm'))) e.preventDefault();
    });
  });
})();
</script>
</body>
</html>
