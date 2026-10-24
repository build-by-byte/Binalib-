<?php
require_once __DIR__ . "/includes/auth.php";
require_admin();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>BinaLib - Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0&display=swap" rel="stylesheet">
<link rel="stylesheet" href="simple-v4.css">
<style>
html{visibility:hidden}.dashboard-collection-block{margin-top:18px}.dashboard-collection-title{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}.dashboard-collection-title h3{font-size:13px;margin:0;color:var(--green-dark)}.dashboard-collection-title span{font-size:10px;color:var(--muted)}.dashboard-mini-table .table{min-width:0}.activity-type{font-size:10px;font-weight:700;color:var(--green);text-transform:uppercase;letter-spacing:.04em}.activity-download{color:#8a6500}.activity-read{color:#5b4aa0}.dashboard-empty{padding:20px;text-align:center;color:var(--muted);font-size:12px}.today-activity-list{display:flex;flex-direction:column}.today-activity-list .metric-row{gap:16px}.today-activity-list .metric-row small{display:block;color:var(--muted);font-size:10px;margin-top:2px}.stat-card.ebook .stat-icon{color:#5b4aa0;background:#f0ecfb}
</style>
</head>
<body>
<div class="shell">
<aside class="sidebar"><div>
<div class="brand"><img src="assets/logo-bina-rahayu.png" alt="Logo Bina Rahayu"><div><strong>BinaLib</strong><small>SMK BINA RAHAYU</small></div></div>
<div class="userbox"><strong>Admin</strong><small>Pustakawan Admin</small></div>
<nav class="nav">
<a data-path="dashboard" href="dashboard.php"><span class="material-symbols-outlined">grid_view</span>Dashboard</a>
<a data-path="data-siswa" href="data-siswa.php"><span class="material-symbols-outlined">group</span>Data Siswa</a>
<a data-path="registrasi-rfid" href="registrasi-rfid.php"><span class="material-symbols-outlined">contactless</span>Registrasi RFID</a>
<a data-path="data-buku" href="data-buku.php"><span class="material-symbols-outlined">auto_stories</span>Data Buku</a>
<a data-path="ebooks" href="ebook.php"><span class="material-symbols-outlined">menu_book</span>E-Book</a>
<a data-path="riwayat-aktivitas" href="riwayat-aktivitas.php"><span class="material-symbols-outlined">history</span>Riwayat Aktivitas</a>
<a data-path="laporan" href="laporan.php"><span class="material-symbols-outlined">analytics</span>Laporan</a>
<a data-path="pengaturan" href="pengaturan.php"><span class="material-symbols-outlined">settings</span>Pengaturan</a>
</nav></div>
<div class="sidebar-bottom"><a class="nav logout" data-path="login" href="index.php"><span class="material-symbols-outlined">logout</span>Keluar</a></div></aside>
<div class="main"><header class="topbar"><input class="search" placeholder="Cari buku, siswa, atau aktivitas..."><div class="profile"><b>Admin</b><span>Pustakawan Admin</span></div></header>
<main class="content">
<section class="page-head"><div><div class="eyebrow">Perpustakaan</div><h1>Dashboard</h1><p>Ringkasan aktivitas dan kondisi perpustakaan BinaLib.</p></div></section>
<section class="stats-grid">
<article class="stat-card"><div><span>Total Siswa</span><strong id="statStudents">0</strong><small>siswa aktif terdaftar</small></div><span class="material-symbols-outlined stat-icon">group</span></article>
<article class="stat-card"><div><span>Total Buku</span><strong id="statBooks">0</strong><small>seluruh eksemplar fisik</small></div><span class="material-symbols-outlined stat-icon">auto_stories</span></article>
<article class="stat-card ebook"><div><span>Total E-Book</span><strong id="statEbooks">0</strong><small>koleksi E-Book aktif</small></div><span class="material-symbols-outlined stat-icon">menu_book</span></article>
<article class="stat-card"><div><span>Sedang Dipinjam</span><strong id="statActive">0</strong><small>eksemplar fisik aktif</small></div><span class="material-symbols-outlined stat-icon">library_books</span></article>
</section>
<section class="dashboard-grid two-col">
<section class="card"><div class="section-title"><div><div class="eyebrow">Aktivitas</div><h2>Aktivitas Terbaru</h2></div><a class="text-link" href="riwayat-aktivitas.php">Lihat semua</a></div>
<div class="table-wrap"><table class="table"><thead><tr><th>Tanggal</th><th>Siswa</th><th>Aktivitas</th><th>Item</th><th>Detail</th></tr></thead><tbody id="recentRows"></tbody></table></div></section>
<section class="card collection-table"><div class="section-title"><div><div class="eyebrow">Koleksi</div><h2>Koleksi Buku & E-Book</h2></div><a class="text-link" href="ebook.php">Kelola E-Book</a></div>
<div class="dashboard-collection-block dashboard-mini-table"><div class="dashboard-collection-title"><h3>Buku Fisik</h3><span id="physicalCollectionCount">0 judul</span></div><div class="table-wrap"><table class="table"><thead><tr><th>Kode</th><th>Judul</th><th>Stok</th></tr></thead><tbody id="collectionRows"></tbody></table></div></div>
<div class="dashboard-collection-block dashboard-mini-table"><div class="dashboard-collection-title"><h3>E-Book</h3><span id="ebookCollectionCount">0 judul</span></div><div class="table-wrap"><table class="table"><thead><tr><th>Kode</th><th>Judul</th><th>Status</th></tr></thead><tbody id="ebookCollectionRows"></tbody></table></div></div>
</section></section>
<section class="dashboard-grid two-col">
<section class="card"><div class="section-title"><div><div class="eyebrow">Aktivitas</div><h2>Ringkasan Aktivitas Hari Ini</h2></div></div>
<div class="today-activity-list">
<div class="metric-row"><div><span>Peminjaman buku</span><small>Transaksi fisik hari ini</small></div><strong id="todayLoans">0</strong></div>
<div class="metric-row"><div><span>Pengembalian buku</span><small>Transaksi fisik hari ini</small></div><strong id="todayReturns">0</strong></div>
<div class="metric-row"><div><span>E-Book dibaca</span><small>Sesi membaca hari ini</small></div><strong id="todayReads">0</strong></div>
<div class="metric-row"><div><span>E-Book diunduh</span><small>Download PDF hari ini</small></div><strong id="todayDownloads">0</strong></div>
<div class="metric-row"><div><span>Total aktivitas</span><small>Seluruh aktivitas hari ini</small></div><strong id="todayTotal">0</strong></div>
</div></section>
<section class="card"><div class="section-title"><div><div class="eyebrow">E-Book</div><h2>Aktivitas Digital</h2></div><a class="text-link" href="riwayat-aktivitas.php">Lihat aktivitas</a></div>
<div class="metric-row"><span>Total pembaca E-Book</span><strong id="ebookReaders">0</strong></div>
<div class="metric-row"><span>Total download E-Book</span><strong id="ebookDownloads">0</strong></div>
<div class="metric-row"><span>Total sesi membaca</span><strong id="ebookSessions">0</strong></div>
<div class="metric-row"><span>Terakhir diunduh</span><strong id="lastDownload">-</strong></div>
</section>
</section>
</main></div></div>
<script src="binalib.js"></script>
<script>
(() => {
const students=BinaLib.refresh('students')||[], books=BinaLib.refresh('books')||[], bor=BinaLib.refresh('borrowings')||[], ebooks=BinaLib.refresh('ebooks')||[], ebookHistory=BinaLib.refresh('ebook_history')||[], ebookDownloads=BinaLib.refresh('ebook_downloads')||[], today=BinaLib.todayISO();
const qty=x=>Math.max(1,Number(x.quantity)||1);
const esc=BinaLib.escapeHtml;
const active=bor.filter(x=>x.status==='Dipinjam');
const activeEbooks=ebooks.filter(e=>String(e.status||'')==='Aktif');
const totalCopies=books.reduce((n,b)=>n+Number(b.stock||0),0)+active.reduce((n,x)=>n+qty(x),0);
document.getElementById('statStudents').textContent=students.length;
document.getElementById('statBooks').textContent=totalCopies;
document.getElementById('statEbooks').textContent=activeEbooks.length;
document.getElementById('statActive').textContent=active.reduce((n,x)=>n+qty(x),0);

const physicalActivities=[];
bor.forEach(x=>{const s=BinaLib.student(x.studentId), b=BinaLib.book(x.bookId);
  // Ignore incomplete/deleted book records in dashboard activity.
  if(!s?.name || !b?.title || !b?.code || x.borrowedAt==null || x.status==null) return;
  physicalActivities.push({at:x.borrowedAt,type:'Peminjaman',student:s.name,sub:s.class||'',item:b.title,itemSub:b.code,detail:qty(x)+' buku',tone:'',order:Number(x.id)||0});
  if(x.returnedAt){physicalActivities.push({at:x.returnedAt,type:'Pengembalian',student:s.name,sub:s.class||'',item:b.title,itemSub:b.code,detail:'Dikembalikan',tone:'activity-download',order:Number(x.id)||0});}
});
const digitalActivities=[];
ebookHistory.forEach(x=>{if(!x.student_name||!x.ebook_title||!x.last_read_at) return;digitalActivities.push({at:x.last_read_at,type:'Membaca E-Book',student:x.student_name,sub:x.nis||'',item:x.ebook_title,itemSub:'Progress '+Math.round(Number(x.progress_percent)||0)+'%',detail:(x.session_count||0)+' sesi',tone:'activity-read',order:Number(x.id)||0});});
ebookDownloads.forEach(x=>{if(!x.student_name||!x.ebook_title||!x.downloaded_at) return;digitalActivities.push({at:x.downloaded_at,type:'Download E-Book',student:x.student_name,sub:x.nis||'',item:x.ebook_title,itemSub:x.ebook_code||'',detail:'Downloaded at '+BinaLib.formatDateTime(x.downloaded_at),tone:'activity-download',order:Number(x.id)||0});});
const activities=[...physicalActivities,...digitalActivities].sort((a,b)=>String(b.at).localeCompare(String(a.at))||b.order-a.order).slice(0,6);
document.getElementById('recentRows').innerHTML=activities.length?activities.map(a=>`<tr><td>${BinaLib.formatDateTime(a.at)}</td><td><strong>${esc(a.student)}</strong><small class="table-sub">${esc(a.sub)}</small></td><td><span class="activity-type ${a.tone}">${esc(a.type)}</span></td><td>${esc(a.item)}<small class="table-sub">${esc(a.itemSub)}</small></td><td>${esc(a.detail)}</td></tr>`).join(''):'<tr><td colspan="5" class="empty">Belum ada aktivitas yang lengkap.</td></tr>';

const rows=books.slice().sort((a,b)=>String(a.code||'').localeCompare(String(b.code||'')));
document.getElementById('physicalCollectionCount').textContent=rows.length+' judul';
document.getElementById('collectionRows').innerHTML=rows.length?rows.slice(0,7).map(b=>`<tr><td><strong>${esc(b.code||'-')}</strong></td><td>${esc(b.title||'-')}<small class="table-sub">${esc(b.author||'')}</small></td><td><span class="badge ${Number(b.stock||0)?'badge-green':'badge-red'}">${Number(b.stock||0)} tersedia</span></td></tr>`).join(''):'<tr><td colspan="3" class="empty">Belum ada data buku.</td></tr>';
document.getElementById('ebookCollectionCount').textContent=activeEbooks.length+' judul';
document.getElementById('ebookCollectionRows').innerHTML=activeEbooks.length?activeEbooks.slice(0,7).map(e=>`<tr><td><strong>${esc(e.code||'-')}</strong></td><td>${esc(e.title||'-')}<small class="table-sub">${esc(e.author||'')}</small></td><td><span class="badge badge-green">Aktif</span></td></tr>`).join(''):'<tr><td colspan="3" class="empty">Belum ada E-Book aktif.</td></tr>';

const todayLoans=bor.filter(x=>BinaLib.sameDay(x.borrowedAt)).reduce((n,x)=>n+qty(x),0);
const todayReturns=bor.filter(x=>BinaLib.sameDay(x.returnedAt)).reduce((n,x)=>n+qty(x),0);
const todayReads=ebookHistory.filter(x=>BinaLib.sameDay(x.last_read_at)).length;
const todayDownloads=ebookDownloads.filter(x=>BinaLib.sameDay(x.downloaded_at)).length;
document.getElementById('todayLoans').textContent=todayLoans;document.getElementById('todayReturns').textContent=todayReturns;document.getElementById('todayReads').textContent=todayReads;document.getElementById('todayDownloads').textContent=todayDownloads;document.getElementById('todayTotal').textContent=todayLoans+todayReturns+todayReads+todayDownloads;
document.getElementById('ebookReaders').textContent=new Set(ebookHistory.map(x=>x.student_id).filter(Boolean)).size;
document.getElementById('ebookDownloads').textContent=ebookDownloads.length;
document.getElementById('ebookSessions').textContent=ebookHistory.reduce((n,x)=>n+Number(x.session_count||0),0);
const latestDownload=ebookDownloads.find(x=>x.downloaded_at);document.getElementById('lastDownload').textContent=latestDownload?BinaLib.formatDateTime(latestDownload.downloaded_at):'-';
})();
</script>
</body></html>
