<?php
// BinaLib E-Book student portal.
// Intentionally standalone: it does not depend on the admin/user CSS so the page
// always renders the RFID UID login form at /E-book/.
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>BinaLib — E-Book</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--green:#0B6B3A;--green-dark:#064E2C;--soft:#EAF6EE;--bg:#F6FAF7;--text:#183126;--muted:#6B7A72;--border:#DDE8E1;--white:#fff;--gold:#C99A22;--danger:#B42318}
*{box-sizing:border-box}html,body{margin:0;min-height:100%;font-family:'Plus Jakarta Sans',Arial,sans-serif;background:var(--bg);color:var(--text)}button,input{font:inherit}.top{height:72px;background:#fff;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 34px}.brand{display:flex;align-items:center;gap:11px}.brand img{width:40px;height:40px;object-fit:contain}.brand-title{font-weight:700;font-size:18px}.brand-sub{display:block;color:var(--muted);font-size:10px;letter-spacing:.06em}.clock{font-size:12px;color:var(--muted)}.main{width:min(1120px,calc(100% - 32px));margin:0 auto;padding:38px 0 60px}.login-card{max-width:760px;margin:38px auto;background:#fff;border:1px solid var(--border);border-radius:18px;padding:30px;box-shadow:0 8px 30px rgba(24,49,38,.05)}.eyebrow{font-size:11px;font-weight:700;letter-spacing:.1em;color:var(--green)}h1{font-size:30px;margin:8px 0}.lead{color:var(--muted);line-height:1.7;font-size:13px;margin:0}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px;margin:25px 0 18px}.field label{display:block;font-size:12px;font-weight:700;margin-bottom:7px}.input{width:100%;border:1px solid var(--border);border-radius:10px;padding:12px 13px;background:#fff;color:var(--text);outline:none}.input:focus{border-color:var(--green);box-shadow:0 0 0 3px var(--soft)}.actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.btn{display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--border);background:#fff;color:var(--text);padding:10px 15px;border-radius:9px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none}.btn-primary{background:var(--green);border-color:var(--green);color:#fff}.btn-primary:hover{background:var(--green-dark)}.notice{margin-top:14px;padding:11px 13px;border-radius:10px;background:#fff8e7;border:1px solid #f0e0af;color:#7a5a10;font-size:12px}.notice.error{background:#fff2f1;border-color:#f1c8c4;color:var(--danger)}.hidden{display:none!important}.hero{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;margin-bottom:22px}.hero h1{margin:7px 0 5px}.userbar{display:flex;align-items:center;justify-content:space-between;gap:15px;margin-bottom:20px}.identity strong{display:block;font-size:14px}.identity span{font-size:11px;color:var(--muted)}.search{max-width:410px}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}.ebook{background:#fff;border:1px solid var(--border);border-radius:16px;padding:14px;overflow:hidden}.cover{height:270px;border-radius:11px;background:var(--soft);overflow:hidden;display:flex;align-items:center;justify-content:center}.cover img{width:100%;height:100%;object-fit:cover}.fallback{padding:25px;text-align:center;font-weight:700;line-height:1.5}.ebook-body{padding:15px 2px 2px}.ebook-title{font-size:16px;font-weight:700;margin:0 0 6px}.author{font-size:12px;color:var(--muted);margin:0 0 10px}.badges{display:flex;gap:7px;flex-wrap:wrap}.badge{background:#f0f4f1;color:#587064;padding:5px 8px;border-radius:99px;font-size:10px}.progress{height:6px;background:#e7eee9;border-radius:99px;overflow:hidden;margin:12px 0 6px}.progress>div{height:100%;background:var(--green)}.progress-row{display:flex;justify-content:space-between;color:var(--muted);font-size:10px}.empty{grid-column:1/-1;text-align:center;padding:60px 20px;background:#fff;border:1px dashed var(--border);border-radius:16px;color:var(--muted)}.detail-card{background:#fff;border:1px solid var(--border);border-radius:18px;padding:24px}.detail{display:grid;grid-template-columns:300px 1fr;gap:30px;margin-top:20px}.detail .cover{height:430px}.detail h2{font-size:28px;margin:5px 0 7px}.detail-meta{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:22px 0}.detail-meta div{background:#f7faf8;border-radius:10px;padding:12px}.detail-meta small{display:block;color:var(--muted);font-size:10px;margin-bottom:4px}.detail-meta strong{font-size:12px}.description{color:#53665c;font-size:13px;line-height:1.75;margin:20px 0}.loading{text-align:center;padding:35px;color:var(--muted);grid-column:1/-1}@media(max-width:850px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.detail{grid-template-columns:1fr}.detail .cover{height:360px}}@media(max-width:600px){.top{padding:0 15px}.main{width:min(100% - 24px,1120px);padding-top:22px}.login-card{padding:22px;margin:18px auto}.form-grid{grid-template-columns:1fr}.hero,.userbar{display:block}.search{max-width:none;margin-top:12px}.grid{grid-template-columns:1fr}.detail-meta{grid-template-columns:1fr}.detail .cover{height:320px}.clock{display:none}}
</style>
</head>
<body>
<header class="top">
  <div class="brand">
    <img src="../assets/logo-bina-rahayu.png" alt="Logo Bina Rahayu">
    <div><div class="brand-title">BinaLib</div><span class="brand-sub">E-BOOK PERPUSTAKAAN</span></div>
  </div>
  <div class="clock" id="clock"></div>
</header>
<main class="main">
  <section id="loginView" class="login-card">
    <div class="eyebrow">LAYANAN E-BOOK</div>
    <h1>Masuk ke E-Book</h1>
    <p class="lead">Masukkan UID RFID yang terdaftar pada kartu perpustakaan. Jika belum mengetahui UID, tanyakan kepada petugas perpustakaan.</p>
    <div class="form-grid">
      <div class="field full"><label for="uid">UID RFID</label><input id="uid" class="input" inputmode="numeric" autocomplete="off" placeholder="Masukkan UID RFID" autofocus></div>
    </div>
    <div class="actions"><button id="loginBtn" class="btn btn-primary">Masuk E-Book</button><a class="btn" href="../user/">Kembali</a></div>
    <div id="loginError" class="notice error hidden"></div>
  </section>

  <section id="catalogView" class="hidden">
    <div class="hero"><div><div class="eyebrow">KOLEKSI DIGITAL</div><h1>Katalog E-Book</h1><p class="lead">Pilih E-Book untuk melihat detail, membaca, atau mengunduh PDF.</p></div><button id="logoutBtn" class="btn">Keluar</button></div>
    <div class="userbar"><div class="identity"><strong id="welcome"></strong><span id="identity"></span></div><input id="search" class="input search" placeholder="Cari judul, penulis, kategori..."></div>
    <div style="display:flex;justify-content:flex-end;margin:0 0 18px"><button id="uploadToggle" class="btn btn-primary">+ Ajukan E-Book</button></div>
    <section id="uploadPanel" class="detail-card hidden" style="margin-bottom:22px"><h2>Ajukan E-Book</h2><p class="lead">Unggahan akan diperiksa admin sebelum masuk katalog. Unggah hanya PDF yang relevan untuk pembelajaran dan pastikan Anda memiliki hak untuk membagikannya.</p><form id="uploadForm"><div class="form-grid"><div class="field"><label>Judul E-Book</label><input class="input" name="title" maxlength="255" required></div><div class="field"><label>Penulis</label><input class="input" name="author" maxlength="150" required></div><div class="field"><label>Kategori</label><input class="input" name="category" maxlength="100" placeholder="Contoh: Informatika"></div><div class="field"><label>Tahun Terbit</label><input class="input" name="year" type="number" min="1900" max="2100" placeholder="Contoh: 2024" required></div><div class="field"><label>File PDF (maks. 20 MB)</label><input class="input" type="file" name="pdf" accept="application/pdf,.pdf" required></div><div class="field full"><label>Deskripsi</label><textarea class="input" name="description" maxlength="2000" rows="3" placeholder="Ringkasan isi e-book"></textarea></div></div><p class="lead">Ketentuan: tidak boleh mengunggah konten tidak pantas, berbahaya, melanggar hak cipta, atau tidak relevan dengan pembelajaran. Admin dapat menolak pengajuan yang tidak sesuai.</p><div class="actions"><button class="btn btn-primary" type="submit" id="submitUpload">Kirim untuk Ditinjau</button><button class="btn" type="button" id="cancelUpload">Batal</button></div><div id="uploadMessage" class="notice hidden"></div></form></section>
    <div id="catalog" class="grid"><div class="loading">Memuat katalog...</div></div>
  </section>

  <section id="detailView" class="detail-card hidden">
    <button id="backBtn" class="btn">← Kembali ke Katalog</button>
    <div id="detail" class="detail"></div>
  </section>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function(){
  'use strict';
  const API='../api.php';
  const $=id=>document.getElementById(id);
  const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  let member=null, ebooks=[], history={};
  async function request(action, options){
    options=options||{}; const method=options.method||'GET';
    let url=API+'?action='+encodeURIComponent(action);
    if(options.query) Object.entries(options.query).forEach(([k,v])=>url+='&'+encodeURIComponent(k)+'='+encodeURIComponent(v));
    const res=await fetch(url,{method,credentials:'same-origin',headers:method==='POST'?{'Content-Type':'application/json'}:{},body:method==='POST'?JSON.stringify(options.body||{}):undefined});
    const raw=await res.text(); let data;
    try{data=JSON.parse(raw)}catch(e){throw new Error('Server mengembalikan respons yang tidak valid. Pastikan api.php dan database BinaLib sudah aktif.')}
    if(!res.ok||data.ok===false) throw new Error(data.message||'Permintaan gagal.');
    return data.data;
  }
  function showError(message){$('loginError').textContent=message;$('loginError').classList.remove('hidden')}
  function cover(e){return e.coverPath?'<img src="../'+esc(e.coverPath)+'" alt="'+esc(e.title)+'">':'<div class="fallback">'+esc(e.title)+'</div>'}
  async function login(){
    const uid=$('uid').value.trim();
    $('loginError').classList.add('hidden');
    if(!uid){showError('UID RFID wajib diisi.');return}
    const btn=$('loginBtn');btn.disabled=true;btn.textContent='Memeriksa...';
    try{member=await request('ebook_login',{method:'POST',body:{uid}});await loadData();showCatalog()}
    catch(e){showError(e.message)}finally{btn.disabled=false;btn.textContent='Masuk E-Book'}
  }
  async function loadData(){
    ebooks=await request('get',{query:{key:'ebooks'}})||[];
    const rows=await request('get',{query:{key:'ebook_my_history'}})||[];
    history={};rows.forEach(x=>history[x.ebook_id]=x);
  }
  function showCatalog(){
    $('loginView').classList.add('hidden');$('detailView').classList.add('hidden');$('catalogView').classList.remove('hidden');
    $('welcome').textContent='Halo, '+(member.name||'Siswa');$('identity').textContent=(member.nis||'')+' • '+(member.class||'');renderCatalog();
  }
  function renderCatalog(){
    const q=$('search').value.trim().toLowerCase();
    const list=ebooks.filter(e=>e.status==='Aktif'&&[e.title,e.author,e.category,e.publisher,e.isbn,e.code].some(v=>String(v||'').toLowerCase().includes(q)));
    if(!list.length){$('catalog').innerHTML='<div class="empty">Belum ada E-Book aktif yang tersedia.</div>';return}
    $('catalog').innerHTML=list.map(e=>{const h=history[e.id],p=Math.min(100,Math.max(0,Number(h?.progress_percent||0))),page=Number(h?.last_page||1);return '<article class="ebook"><div class="cover">'+cover(e)+'</div><div class="ebook-body"><h3 class="ebook-title">'+esc(e.title)+'</h3><p class="author">'+esc(e.author||'Penulis tidak tersedia')+'</p><div class="badges"><span class="badge">'+esc(e.category||'Umum')+'</span><span class="badge">'+esc(e.year||'-')+'</span></div>'+(h?'<div class="progress"><div style="width:'+p+'%"></div></div><div class="progress-row"><span>'+p.toFixed(0)+'% selesai</span><span>Hal. '+page+'</span></div>':'')+'<div class="actions" style="margin-top:14px"><button class="btn btn-primary" data-detail="'+e.id+'">'+(h?'Lanjutkan':'Lihat Detail')+'</button></div></div></article>'}).join('');
    document.querySelectorAll('[data-detail]').forEach(b=>b.onclick=()=>showDetail(Number(b.dataset.detail)));
  }
  function showDetail(id){
    const e=ebooks.find(x=>Number(x.id)===id);if(!e)return;const h=history[id],p=Math.min(100,Math.max(0,Number(h?.progress_percent||0)));
    $('catalogView').classList.add('hidden');$('detailView').classList.remove('hidden');
    $('detail').innerHTML='<div class="cover">'+cover(e)+'</div><div><div class="eyebrow">DETAIL E-BOOK</div><h2>'+esc(e.title)+'</h2><p class="lead">'+esc(e.author||'-')+'</p><div class="detail-meta"><div><small>Penerbit</small><strong>'+esc(e.publisher||'-')+'</strong></div><div><small>Tahun</small><strong>'+esc(e.year||'-')+'</strong></div><div><small>ISBN</small><strong>'+esc(e.isbn||'-')+'</strong></div><div><small>Kategori</small><strong>'+esc(e.category||'-')+'</strong></div><div><small>Halaman</small><strong>'+esc(e.pageCount||'Otomatis dari PDF')+'</strong></div><div><small>Progress</small><strong>'+(h?p.toFixed(0)+'%':'Belum dibaca')+'</strong></div></div><div class="description">'+esc(e.description||'Tidak ada deskripsi untuk E-Book ini.')+'</div><div class="actions"><a class="btn btn-primary" href="../reader.php?id='+encodeURIComponent(e.id)+'&from=ebook">'+(h?'Lanjutkan Membaca':'Baca E-Book')+'</a><a class="btn" href="../download.php?id='+encodeURIComponent(e.id)+'">Download PDF</a></div></div>';
  }
  async function logout(){try{await request('ebook_logout')}catch(e){} location.reload()}
  $('loginBtn').onclick=login;$('uid').onkeydown=e=>{if(e.key==='Enter')login()};$('search').oninput=renderCatalog;$('backBtn').onclick=showCatalog;$('logoutBtn').onclick=logout;
  $('uploadToggle').onclick=()=>{$('uploadPanel').classList.toggle('hidden');$('uploadMessage').classList.add('hidden')};
  $('cancelUpload').onclick=()=>{$('uploadPanel').classList.add('hidden');$('uploadForm').reset()};
  $('uploadForm').onsubmit=async e=>{e.preventDefault();const f=$('uploadForm').elements.pdf.files[0];if(!f){showUploadMessage('Pilih file PDF terlebih dahulu.',true);return}if(f.size>20*1024*1024){showUploadMessage('Ukuran PDF maksimal 20 MB.',true);return}const btn=$('submitUpload');btn.disabled=true;btn.textContent='Membuat cover dan mengirim...';try{const fd=new FormData($('uploadForm'));if(window.pdfjsLib){const pdf=await pdfjsLib.getDocument({data:await f.arrayBuffer()}).promise;const page=await pdf.getPage(1);const viewport=page.getViewport({scale:1.2});const canvas=document.createElement('canvas');canvas.width=viewport.width;canvas.height=viewport.height;await page.render({canvasContext:canvas.getContext('2d'),viewport}).promise;const blob=await new Promise(resolve=>canvas.toBlob(resolve,'image/jpeg',0.86));if(blob)fd.append('cover',blob,'cover.jpg')}const res=await fetch('../api.php?action=ebook_submit',{method:'POST',credentials:'same-origin',body:fd});const data=await res.json();if(!res.ok||data.ok===false)throw new Error(data.message||'Pengajuan gagal dikirim.');showUploadMessage('Pengajuan berhasil dikirim dan sedang menunggu persetujuan admin.',false);$('uploadForm').reset()}catch(err){showUploadMessage(err.message,true)}finally{btn.disabled=false;btn.textContent='Kirim untuk Ditinjau'}};
  function showUploadMessage(msg,error){$('uploadMessage').textContent=msg;$('uploadMessage').classList.remove('hidden');$('uploadMessage').className='notice '+(error?'error':'');}
  function clock(){const d=new Date();$('clock').textContent=d.toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'})+' • '+d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'})}clock();setInterval(clock,1000);
  request('ebook_me').then(async m=>{if(m){member=m;try{await loadData();showCatalog()}catch(e){showError(e.message)}}}).catch(()=>{});
})();
</script>
</body>
</html>
