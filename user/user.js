(() => {
  const $ = s => document.querySelector(s), esc = BinaLib.escapeHtml;
  const settings = () => BinaLib.getSettings();
  const RFID_UID_LENGTH = 10;
  let rfidScanTimer = null;
  let mode = null, member = null, loanType = 'pribadi', selectedBooks = {}, returnSelection = [];

  const maxQty = () => {
    const s = settings();
    return loanType === 'kelas' ? s.maxClassCopies : s.maxPersonalCopies;
  };
  const coverFallback = title => `<div class="generated-cover"><b>${esc(title)}</b><span>Koleksi ${esc(settings().libraryName)}</span></div>`;
  const bookCover = b => { const src = b.imageData || b.image || ''; return src ? `<img src="${esc(src)}" alt="${esc(b.title)}">` : coverFallback(b.title); };

  function applySystemSettings(){
    const s = settings();
    const label = document.getElementById('libraryNameLabel');
    if(label) label.textContent = s.libraryName;
    updateLimitLabels();
  }

  function resetBorrow(){ selectedBooks = {}; }
  function showStart(){
    mode = null; member = null; resetBorrow(); returnSelection = [];
    $('#startScreen').classList.remove('hidden');
    $('#serviceScreen').classList.add('hidden');
    $('#scanCard').classList.remove('hidden');
    $('#memberSection').classList.add('hidden');
    $('#borrowContent').classList.add('hidden');
    $('#returnContent').classList.add('hidden');
    $('#rfidInput').value = '';
    $('#rfidWarning').classList.add('hidden');
    setTimeout(() => $('#rfidInput').focus(), 80);
  }

  function chooseMode(m){
    mode = m; member = null; resetBorrow(); returnSelection = [];
    $('#startScreen').classList.add('hidden');
    $('#serviceScreen').classList.remove('hidden');
    $('#scanCard').classList.remove('hidden');
    $('#memberSection').classList.add('hidden');
    $('#borrowContent').classList.add('hidden');
    $('#returnContent').classList.add('hidden');
    $('#serviceEyebrow').textContent = m === 'borrow' ? 'PEMINJAMAN' : 'PENGEMBALIAN';
    $('#rfidInput').value = '';
    $('#rfidWarning').classList.add('hidden');
    setTimeout(() => $('#rfidInput').focus(), 80);
  }

  function setMember(s){
    $('#memberName').textContent = s.name || '-';
    $('#memberNis').textContent = s.nis || '-';
    $('#memberClass').textContent = s.class || '-';
    $('#memberId').textContent = s.libraryId || '-';
    $('#memberSection').classList.remove('hidden');
  }

  function scan(){
    const uid = $('#rfidInput').value.replace(/\D/g,'').slice(0, RFID_UID_LENGTH);
    $('#rfidInput').value = uid;
    if(uid.length !== RFID_UID_LENGTH) return;
    const s = BinaLib.findStudentRFID(uid); member = s || null;
    if(!s){
      $('#memberSection').classList.add('hidden');
      $('#borrowContent').classList.add('hidden');
      $('#returnContent').classList.add('hidden');
      $('#rfidWarning').classList.remove('hidden');
      $('#rfidWarning').textContent = 'Kartu RFID belum terdaftar di database BinaLib. Silakan lakukan registrasi terlebih dahulu melalui Admin.';
      return;
    }
    $('#rfidWarning').classList.add('hidden');
    $('#scanCard').classList.add('hidden');
    setMember(s);
    if(mode === 'borrow'){
      $('#borrowContent').classList.remove('hidden');
      $('#returnContent').classList.add('hidden');
      resetBorrow();
      loanType = 'pribadi';
      setLoanType('pribadi');
      renderCatalog();
      renderSummary();
    } else {
      $('#returnContent').classList.remove('hidden');
      $('#borrowContent').classList.add('hidden');
      renderReturns();
    }
  }

  function renderCatalog(){
    const q = ($('#bookSearch').value || '').trim().toLowerCase();
    const books = BinaLib.get('books', []).filter(b => [b.code,b.title,b.author].some(v => String(v || '').toLowerCase().includes(q)));
    $('#catalog').innerHTML = books.length ? books.map(b => {
      const selected = Number(selectedBooks[b.id] || 0) > 0;
      const stock = Number(b.stock || 0);
      return `<article class="book-card ${selected ? 'selected' : ''}"><div class="book-cover">${bookCover(b)}</div><div class="book-body"><h3>${esc(b.title)}</h3><p>${esc(b.author || '-')} • ${esc(b.code || '')}</p><span class="book-category">${esc(b.category || 'Umum')}</span><div class="stock-row"><span class="stock">${stock} eksemplar tersedia</span><span class="badge ${stock ? 'badge-green' : 'badge-red'}">${stock ? 'Tersedia' : 'Habis'}</span></div><div class="book-actions"><button class="select-book ${selected ? 'active' : ''}" data-select="${b.id}" ${!stock ? 'disabled' : ''}>${selected ? 'Buku Dipilih' : 'Pilih Buku'}</button></div></div></article>`;
    }).join('') : '<div class="empty" style="grid-column:1/-1">Belum ada data buku.</div>';
    $('#catalog').querySelectorAll('[data-select]').forEach(btn => btn.onclick = () => toggleBook(Number(btn.dataset.select)));
  }

  function updateLimitLabels(){
    const s = settings();
    const p = document.getElementById('personalLimitLabel'), c = document.getElementById('classLimitLabel');
    if(p) p.textContent = `maks. ${s.maxPersonalCopies} eksemplar`;
    if(c) c.textContent = `maks. ${s.maxClassCopies} eksemplar`;
  }

  function totalSelected(){ return Object.values(selectedBooks).reduce((n,v) => n + Math.max(0, Number(v) || 0), 0); }

  function setLoanType(type){
    loanType = type;
    if(type === 'pribadi'){
      const ids = Object.keys(selectedBooks);
      if(ids.length > 1) selectedBooks = { [ids[0]]: 1 };
      if(ids.length === 1) selectedBooks[ids[0]] = 1;
    }
    if(totalSelected() > maxQty()){
      let left = maxQty();
      Object.keys(selectedBooks).forEach(id => {
        const next = Math.min(Number(selectedBooks[id]) || 0, left);
        selectedBooks[id] = next;
        left -= next;
      });
      Object.keys(selectedBooks).forEach(id => { if(!selectedBooks[id]) delete selectedBooks[id]; });
    }
    document.querySelectorAll('[data-loan-type]').forEach(btn => btn.classList.toggle('active', btn.dataset.loanType === type));
    updateLimitLabels();
    renderCatalog();
    renderSummary();
  }

  function toggleBook(id){
    if(!member) return;
    const b = BinaLib.book(id);
    if(!b || Number(b.stock) <= 0) return;
    if(loanType === 'pribadi'){
      selectedBooks = selectedBooks[id] ? {} : { [id]: 1 };
    } else {
      if(selectedBooks[id]) delete selectedBooks[id];
      else {
        if(totalSelected() >= maxQty()) return BinaLib.saveToast(`Maksimal ${maxQty()} eksemplar.`);
        selectedBooks[id] = 1;
      }
    }
    renderCatalog();
    renderSummary();
  }

  function changeSelectedQty(id, delta){
    const b = BinaLib.book(id);
    if(!b) return;
    const current = Number(selectedBooks[id] || 0);
    const other = totalSelected() - current;
    const next = Math.max(1, Math.min(Number(b.stock) || 1, Math.min(maxQty() - other, current + delta)));
    selectedBooks[id] = next;
    renderSummary();
  }

  function setSelectedQty(id, value){
    const b = BinaLib.book(id);
    if(!b) return;
    const other = totalSelected() - Number(selectedBooks[id] || 0);
    const max = Math.min(Number(b.stock) || 1, maxQty() - other);
    const next = Math.max(1, Math.min(max, Number(value) || 1));
    selectedBooks[id] = next;
    renderSummary();
  }

  function renderSummary(){
    const ids = Object.keys(selectedBooks).filter(id => Number(selectedBooks[id]) > 0);
    const max = maxQty();
    const total = totalSelected();
    $('#totalQty').textContent = `${total} / ${max}`;
    if(!ids.length){
      $('#borrowSummary').innerHTML = '<div class="empty">Belum ada buku yang dipilih.</div>';
      $('#quantityBox').classList.add('hidden');
      $('#confirmBorrow').disabled = true;
      return;
    }
    $('#quantityBox').classList.remove('hidden');
    $('#borrowSummary').innerHTML = ids.map(id => {
      const b = BinaLib.book(id), qty = Number(selectedBooks[id]);
      return `<div class="summary-item"><div><strong>${esc(b?.title || '-')}</strong><small>${esc(b?.code || '')} • ${esc(b?.author || '-')}</small><span class="summary-type">${loanType === 'kelas' ? 'Kegunaan Kelas' : 'Penggunaan Pribadi'}</span></div><div class="summary-qty"><button type="button" data-qminus="${id}">−</button><input type="number" min="1" value="${qty}" data-qinput="${id}"><button type="button" data-qplus="${id}">+</button><button class="remove-book" type="button" data-qremove="${id}">Hapus</button></div></div>`;
    }).join('');
    $('#quantityLabel').textContent = `${total} / ${max}`;
    $('#confirmBorrow').disabled = total < 1 || total > max;
    $('#borrowSummary').querySelectorAll('[data-qminus]').forEach(x => x.onclick = () => changeSelectedQty(Number(x.dataset.qminus), -1));
    $('#borrowSummary').querySelectorAll('[data-qplus]').forEach(x => x.onclick = () => changeSelectedQty(Number(x.dataset.qplus), 1));
    $('#borrowSummary').querySelectorAll('[data-qremove]').forEach(x => x.onclick = () => { delete selectedBooks[x.dataset.qremove]; renderCatalog(); renderSummary(); });
    $('#borrowSummary').querySelectorAll('[data-qinput]').forEach(x => x.onchange = () => setSelectedQty(Number(x.dataset.qinput), x.value));
  }

  function confirmBorrow(){
    if(!member) return;
    const items = Object.entries(selectedBooks).map(([bookId, quantity]) => ({ bookId:Number(bookId), quantity:Number(quantity) }));
    const total = items.reduce((n,x) => n + x.quantity, 0);
    if(!items.length) return BinaLib.saveToast('Pilih minimal satu buku.');
    if(total > maxQty()) return BinaLib.saveToast(`Maksimal ${maxQty()} eksemplar.`);
    if(loanType === 'pribadi' && (items.length !== 1 || total !== 1)) return BinaLib.saveToast('Penggunaan pribadi maksimal 1 buku.');
    const result = BinaLib.createBorrowings({ studentId:Number(member.id), items, loanType:loanType === 'kelas' ? 'Kegunaan Kelas' : 'Penggunaan Pribadi' });
    if(!result) return;
    BinaLib.saveToast(`Peminjaman berhasil dicatat untuk ${result.count} judul buku.`);
    showStart();
  }

  function renderReturns(){
    const active = BinaLib.activeBorrowings(member.id);
    const activeIds = active.map(x => Number(x.id));
    returnSelection = returnSelection.filter(id => activeIds.includes(Number(id)));
    $('#returnList').innerHTML = active.length ? active.map(x => {
      const b = BinaLib.book(x.bookId), id = Number(x.id), checked = returnSelection.includes(id), type = x.loanType === 'Kegunaan Kelas' ? 'Kegunaan Kelas' : 'Penggunaan Pribadi';
      return `<label class="return-item ${checked ? 'selected' : ''}"><input class="return-check" type="checkbox" data-return-id="${id}" ${checked ? 'checked' : ''}><div class="return-main"><div class="return-title-row"><h3>${esc(b?.title || '-')}</h3><span class="return-status">${esc(type)}</span></div><p>${esc(b?.code || '')} • ${x.quantity} eksemplar • Dipinjam ${esc(BinaLib.formatDateTime(x.borrowedAt))}</p></div></label>`;
    }).join('') : '<div class="empty">Tidak ada buku yang sedang dipinjam oleh siswa ini.</div>';
    $('#returnList').querySelectorAll('[data-return-id]').forEach(cb => cb.onchange = () => {
      const id = Number(cb.dataset.returnId);
      if(cb.checked){ if(!returnSelection.includes(id)) returnSelection.push(id); }
      else returnSelection = returnSelection.filter(x => Number(x) !== id);
      cb.closest('.return-item')?.classList.toggle('selected', cb.checked);
      $('#confirmReturn').disabled = returnSelection.length === 0;
    });
    $('#confirmReturn').disabled = returnSelection.length === 0;
  }

  function confirmReturn(){
    if(!member) return;
    if(!returnSelection.length) return BinaLib.saveToast('Pilih minimal satu buku yang akan dikembalikan.');
    const result = BinaLib.returnBorrowings({ studentId:Number(member.id), ids:returnSelection.map(Number) });
    if(!result) return;
    BinaLib.saveToast(`${result.count || returnSelection.length} peminjaman berhasil dikembalikan.`);
    showStart();
  }

  
  function normalizeRFIDInput(){
    const input = $('#rfidInput');
    const value = String(input.value || '').replace(/\D/g, '').slice(0, RFID_UID_LENGTH);
    input.value = value;
    if(value.length === RFID_UID_LENGTH){
      clearTimeout(rfidScanTimer);
      rfidScanTimer = setTimeout(scan, 40);
    }
  }
  $('#rfidInput').addEventListener('input', normalizeRFIDInput);
  $('#rfidInput').addEventListener('keydown', e => {
    if(e.key === 'Enter'){
      e.preventDefault();
      normalizeRFIDInput();
      if($('#rfidInput').value.length === RFID_UID_LENGTH) scan();
    }
  });
  document.addEventListener('keydown', e => {
    if(mode !== 'borrow' && mode !== 'return') return;
    if(e.ctrlKey || e.altKey || e.metaKey) return;
    const active = document.activeElement;
    const editable = active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA' || active.isContentEditable);

    // RFID reader bekerja seperti keyboard dan mengetik langsung ke #rfidInput.
    // Jangan menangkap digit lagi di document-level handler karena akan menggandakan
    // setiap digit (mis. 0001950658 bisa berubah menjadi UID yang salah).
    if(active === $('#rfidInput')) return;
    if(editable) return;

    if(/^\d$/.test(e.key)){
      const input = $('#rfidInput');
      if(input.value.length >= RFID_UID_LENGTH) input.value = '';
      input.value += e.key;
      normalizeRFIDInput();
      input.focus();
      return;
    }
    if(e.key === 'Enter' && $('#rfidInput').value.length === RFID_UID_LENGTH){
      e.preventDefault();
      scan();
    }
  });
  document.querySelectorAll('.service-button[data-mode]').forEach(btn => {
    btn.addEventListener('click', () => chooseMode(btn.dataset.mode));
  });
  $('#scanBtn').onclick = scan;
  $('#backBtn').onclick = showStart;
  $('#bookSearch').oninput = renderCatalog;
  $('#confirmBorrow').onclick = confirmBorrow;
  $('#confirmReturn').onclick = confirmReturn;
  document.querySelectorAll('[data-loan-type]').forEach(btn => btn.onclick = () => setLoanType(btn.dataset.loanType));

  function updateClock(){ const d = new Date(); $('#clock').textContent = d.toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) + ' • ' + d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}); }
  updateClock(); setInterval(updateClock,1000); applySystemSettings(); showStart();
  window.addEventListener('storage', () => { applySystemSettings(); if(mode === 'borrow' && member){ renderCatalog(); renderSummary(); } if(mode === 'return' && member) renderReturns(); });
})();
