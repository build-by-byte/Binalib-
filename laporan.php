<?php

require_once __DIR__ . "/includes/auth.php";

require_admin();

?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width,initial-scale=1"
    >

    <title>BinaLib - Laporan & Statistik</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="simple-v4.css"
    >

    <style>
        html {
            visibility: hidden;
        }

        html.binalib-ready { visibility: visible; }
        .report-period-card { margin-bottom: 20px; padding: 24px 28px; }
        .report-period-layout { display:grid; grid-template-columns:minmax(0,1fr) minmax(330px,420px); gap:28px; align-items:center; }
        .report-period-copy h2 { margin:5px 0 7px; }
        .report-period-copy p { margin:0; }
        .report-period-controls { display:grid; gap:10px; }
        .period-switch { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        .period-btn { width:100%; min-height:44px; }
        .period-date { width:100%; min-height:44px; }
        .period-btn.active { background:#0B6B3A; color:#fff; border-color:#0B6B3A; }
        .bar-chart { display:flex; flex-direction:column; gap:15px; margin-top:8px; }
        .bar-row { display:grid; grid-template-columns:minmax(150px, 240px) 1fr 55px; align-items:center; gap:12px; }
        .bar-label { font-size:13px; font-weight:700; color:#183126; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .bar-track { height:22px; background:#EAF6EE; border-radius:7px; overflow:hidden; }
        .bar-fill { height:100%; background:#0B6B3A; border-radius:7px; min-width:3px; transition:width .25s ease; }
        .bar-value { text-align:right; font-weight:700; font-size:13px; color:#0B6B3A; }
        .chart-empty { padding:35px 10px; text-align:center; color:#718278; }
        @media(max-width:900px){ .report-period-layout{grid-template-columns:1fr;} .report-period-controls{max-width:none;} }
        @media(max-width:760px){ .bar-row{grid-template-columns:1fr 50px}.bar-label{grid-column:1/-1}.bar-track{grid-column:1}.bar-value{grid-column:2;grid-row:2}.report-period-card{padding:20px}.period-switch{grid-template-columns:1fr 1fr;} }
    </style>
</head>

<body>

    <div class="shell">

        <aside class="sidebar">

            <div>

                <div class="brand">

                    <img
                        src="assets/logo-bina-rahayu.png"
                        alt="Logo Bina Rahayu"
                    >

                    <div>
                        <strong>BinaLib</strong>
                        <small>SMK BINA RAHAYU</small>
                    </div>

                </div>

                <div class="userbox">
                    <strong>Admin</strong>
                    <small>Pustakawan Admin</small>
                </div>

                <nav class="nav">

                    <a
                        class=""
                        data-path="dashboard"
                        href="dashboard.php"
                    >
                        <span class="material-symbols-outlined">
                            grid_view
                        </span>
                        Dashboard
                    </a>

                    <a
                        class=""
                        data-path="data-siswa"
                        href="data-siswa.php"
                    >
                        <span class="material-symbols-outlined">
                            group
                        </span>
                        Data Siswa
                    </a>

                    <a
                        class=""
                        data-path="registrasi-rfid"
                        href="registrasi-rfid.php"
                    >
                        <span class="material-symbols-outlined">
                            contactless
                        </span>
                        Registrasi RFID
                    </a>

                    <a
                        class=""
                        data-path="data-buku"
                        href="data-buku.php"
                    >
                        <span class="material-symbols-outlined">
                            auto_stories
                        </span>
                        Data Buku
                    </a>

                    <a
                        class=""
                        data-path="ebooks"
                        href="ebook.php"
                    >
                        <span class="material-symbols-outlined">
                            menu_book
                        </span>
                        E-Book
                    </a>

                    <a
                        class=""
                        data-path="riwayat-aktivitas"
                        href="riwayat-aktivitas.php"
                    >
                        <span class="material-symbols-outlined">
                            history
                        </span>
                        Riwayat Aktivitas
                    </a>

                    <a
                        class="active"
                        data-path="laporan"
                        href="laporan.php"
                    >
                        <span class="material-symbols-outlined">
                            analytics
                        </span>
                        Laporan
                    </a>

                    <a
                        class=""
                        data-path="pengaturan"
                        href="pengaturan.php"
                    >
                        <span class="material-symbols-outlined">
                            settings
                        </span>
                        Pengaturan
                    </a>

                </nav>

            </div>

            <div class="sidebar-bottom">
<a
                    class="nav logout"
                    data-path="login"
                    href="index.php"
                >
                    <span class="material-symbols-outlined">
                        logout
                    </span>
                    Keluar
                </a>

            </div>

        </aside>

        <div class="main">

            <header class="topbar">

                <input
                    class="search"
                    placeholder="Cari buku, siswa, atau peminjaman..."
                >

                <div class="profile">
                    <b>Admin</b>
                    <span>Pustakawan Admin</span>
                </div>

            </header>

            <main class="content">

                <section class="page-head">

                    <div>

                        <div class="eyebrow">
                            Laporan
                        </div>

                        <h1>
                            Laporan & Statistik
                        </h1>

                        <p>
                            Ringkasan data perpustakaan berdasarkan
                            peminjaman yang tersimpan.
                        </p>

                    </div>

                </section>

                <section class="mini-stats">

                    <div class="mini-stat">
                        <span>Total siswa</span>
                        <strong id="repStudents">0</strong>
                    </div>

                    <div class="mini-stat">
                        <span>Judul buku</span>
                        <strong id="repBooks">0</strong>
                    </div>

                    <div class="mini-stat">
                        <span>Eksemplar dipinjam pada periode</span>
                        <strong id="repActive">0</strong>
                    </div>

                    <div class="mini-stat">
                        <span>Total transaksi pada periode</span>
                        <strong id="repTrans">0</strong>
                    </div>

                </section>

                <section class="card report-period-card">
                    <div class="report-period-layout">
                        <div class="report-period-copy">
                            <div class="eyebrow">PERIODE LAPORAN</div>
                            <h2>Filter Laporan</h2>
                            <p class="muted" id="periodLabel">-</p>
                        </div>
                        <div class="report-period-controls">
                            <div class="period-switch">
                                <button class="btn btn-outline period-btn active" data-period="week" type="button">Mingguan</button>
                                <button class="btn btn-outline period-btn" data-period="month" type="button">Bulanan</button>
                            </div>
                            <input class="input period-date" type="date" id="reportDate" aria-label="Tanggal acuan laporan">
                        </div>
                    </div>
                </section>

                <section class="dashboard-grid two-col">

                    <section class="card chart-card">
                        <div class="section-title">
                            <div>
                                <div class="eyebrow">STATISTIK PEMINJAMAN</div>
                                <h2>Buku Paling Banyak Dipinjam</h2>
                                <p class="muted">Jumlah eksemplar yang dipinjam pada periode terpilih.</p>
                            </div>
                        </div>
                        <div id="topBooksChart" class="bar-chart"></div>
                    </section>

                    <section class="card">

                        <div class="section-title">

                            <div>

                                <div class="eyebrow">
                                    Informasi
                                </div>

                                <h2>
                                    Ringkasan Sistem
                                </h2>

                            </div>

                        </div>

                        <div class="metric-row">
                            <span>Eksemplar tersedia</span>
                            <strong id="repAvailable">0</strong>
                        </div>

                        <div class="metric-row">
                            <span>Eksemplar sedang dipinjam</span>
                            <strong id="repBorrowed">0</strong>
                        </div>

                        <div class="metric-row">
                            <span>Kelas terdaftar</span>
                            <strong id="repClasses">0</strong>
                        </div>

                    </section>

                </section>

            </main>

        </div>

    </div>

    <script src="binalib.js"></script>

    <script>
        (() => {
            let period = 'week';
            const dateInput = document.getElementById('reportDate');
            const periodLabel = document.getElementById('periodLabel');
            const chart = document.getElementById('topBooksChart');

            function localISODate(value = new Date()) {
                const d = new Date(value);
                return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
            }
            function parseLocalDate(v) { return new Date(v + 'T00:00:00'); }
            function startEnd() {
                const base = parseLocalDate(dateInput.value || localISODate());
                let start, end;
                if (period === 'month') {
                    start = new Date(base.getFullYear(), base.getMonth(), 1);
                    end = new Date(base.getFullYear(), base.getMonth() + 1, 0);
                } else {
                    const day = base.getDay();
                    const mondayOffset = day === 0 ? -6 : 1 - day;
                    start = new Date(base); start.setDate(base.getDate() + mondayOffset);
                    end = new Date(start); end.setDate(start.getDate() + 6);
                }
                start.setHours(0,0,0,0); end.setHours(23,59,59,999);
                return {start,end};
            }
            function recordDate(v) {
                if(!v) return null;
                const raw=String(v).replace(' ','T');
                const d=new Date(raw);
                return Number.isNaN(d.getTime()) ? null : d;
            }
            function inPeriod(v, range) { const d=recordDate(v); return d && d>=range.start && d<=range.end; }
            function fmt(d) { return d.toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}); }
            function qty(x) { return Math.max(1,Number(x.quantity)||1); }

            function render() {
                const books=BinaLib.get('books');
                const students=BinaLib.get('students');
                const bor=BinaLib.get('borrowings');
                const range=startEnd();
                const filtered=bor.filter(x=>inPeriod(x.borrowedAt,range));
                periodLabel.textContent = period==='month' ? `Bulan ${range.start.toLocaleDateString('id-ID',{month:'long',year:'numeric'})}` : `${fmt(range.start)} – ${fmt(range.end)}`;

                document.getElementById('repStudents').textContent=students.length;
                document.getElementById('repBooks').textContent=books.length;
                document.getElementById('repActive').textContent=filtered.filter(x=>x.status==='Dipinjam').reduce((n,x)=>n+qty(x),0);
                document.getElementById('repTrans').textContent=filtered.length;
                document.getElementById('repAvailable').textContent=books.reduce((n,b)=>n+Number(b.stock||0),0);
                document.getElementById('repBorrowed').textContent=filtered.filter(x=>x.status==='Dipinjam').reduce((n,x)=>n+qty(x),0);
                document.getElementById('repClasses').textContent=new Set(students.map(s=>s.class)).size;

                const counts={};
                filtered.forEach(x=>{ counts[x.bookId]=(counts[x.bookId]||0)+qty(x); });
                const sorted=Object.entries(counts).sort((a,b)=>b[1]-a[1]).slice(0,10);
                const max=sorted.length ? Math.max(...sorted.map(x=>x[1])) : 0;
                chart.innerHTML=sorted.length ? sorted.map(([id,n],i)=>{
                    const book=BinaLib.book(id);
                    const pct=max ? Math.max(3,(n/max)*100) : 0;
                    return `<div class="bar-row"><div class="bar-label" title="${BinaLib.escapeHtml(book?.title||'-')}">${i+1}. ${BinaLib.escapeHtml(book?.title||'-')}</div><div class="bar-track"><div class="bar-fill" style="width:${pct}%"></div></div><div class="bar-value">${n}x</div></div>`;
                }).join('') : '<div class="chart-empty">Belum ada peminjaman pada periode yang dipilih.</div>';
            }

            dateInput.value=localISODate();
            document.querySelectorAll('.period-btn').forEach(btn=>btn.addEventListener('click',()=>{ period=btn.dataset.period; document.querySelectorAll('.period-btn').forEach(b=>b.classList.toggle('active',b===btn)); render(); }));
            dateInput.addEventListener('change',render);
            render();
        })();
    </script>

    <script id="binalib-live-sync">
        window.addEventListener(
            "storage",
            () => location.reload()
        );
    </script>

</body>

</html>