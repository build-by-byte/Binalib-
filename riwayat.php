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
        content="width=device-width, initial-scale=1"
    >

    <title>BinaLib - Riwayat Peminjaman</title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

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

        html.binalib-ready {
            visibility: visible;
        }

        .loan-category-badge {
            display: inline-block;
            padding: 6px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .loan-category-badge.personal-loan {
            background: #eef5ff;
            color: #315b8a;
        }

        .loan-category-badge.class-loan {
            background: #eaf6ee;
            color: #0b6b3a;
        }

        .table-sub { display: block; margin-top: 4px; font-size: 11px; color: #718278; }
        .history-toolbar { display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; }
        .history-date-control { display:flex; flex-direction:column; gap:6px; }
        .history-date-control label { font-size:12px; font-weight:700; color:#52645a; }
        .history-search { min-width:280px; flex:1; }
        @media (max-width: 760px) { .history-search { min-width:100%; } }
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
                        class=""
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
                            Peminjaman
                        </div>

                        <h1>
                            Riwayat Peminjaman
                        </h1>

                        <p>
                            Daftar peminjaman yang tersimpan,
                            diurutkan dari aktivitas terbaru.
                        </p>

                    </div>

                </section>

                <section class="mini-stats">

                    <div class="mini-stat">
                        <span>Total peminjaman</span>
                        <strong id="histTotal">0</strong>
                    </div>

                    <div class="mini-stat">
                        <span>Masih dipinjam</span>
                        <strong id="histActive">0</strong>
                    </div>

                    <div class="mini-stat">
                        <span>Dikembalikan</span>
                        <strong id="histReturned">0</strong>
                    </div>

                </section>

                <section class="card">

                    <div class="toolbar history-toolbar">

                        <div class="history-date-control">
                            <label for="historyDate">Tanggal riwayat</label>
                            <input class="input" type="date" id="historyDate">
                        </div>

                        <input
                            class="input history-search"
                            id="historySearch"
                            placeholder="Cari siswa, buku, kelas, status..."
                        >

                        <button
                            class="btn btn-outline"
                            id="todayHistory"
                            type="button"
                        >
                            Hari Ini
                        </button>

                        <button
                            class="btn btn-outline"
                            id="refreshHistory"
                            type="button"
                        >
                            Refresh
                        </button>

                    </div>

                    <div class="table-wrap">

                        <table class="table">

                            <thead>

                                <tr>
                                    <th>Tanggal & Jam</th>
                                    <th>Siswa</th>
                                    <th>Buku</th>
                                    <th>Kelas</th>
                                    <th>Kategori Peminjaman</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody id="historyRows"></tbody>

                        </table>

                    </div>

                </section>

            </main>

        </div>

    </div>

    <script src="binalib.js"></script>

    <script>
        (() => {
            const rows = document.getElementById('historyRows');
            const search = document.getElementById('historySearch');
            const dateInput = document.getElementById('historyDate');

            function localISODate(value = new Date()) {
                const d = new Date(value);
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${day}`;
            }

            function recordDate(value) {
                if (!value) return '';
                const raw = String(value);
                // MySQL DATETIME from the local school server.
                return raw.length >= 10 ? raw.slice(0, 10) : raw;
            }

            function dateParts(value) {
                if (!value) return { date: '-', time: '' };
                const raw = String(value);
                const d = new Date(raw.length === 10 ? raw + 'T00:00:00' : raw.replace(' ', 'T'));
                if (Number.isNaN(d.getTime())) return { date: '-', time: '' };
                return {
                    date: d.toLocaleDateString('id-ID', { weekday:'long', day:'2-digit', month:'long', year:'numeric' }),
                    time: d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', second:'2-digit' })
                };
            }

            function category(x) {
                return x.loanType === 'Kegunaan Kelas' ? 'Kegunaan Kelas' : 'Penggunaan Pribadi';
            }

            function render() {
                const all = BinaLib.get('borrowings').slice().sort((a,b) => String(b.borrowedAt || '').localeCompare(String(a.borrowedAt || '')));
                const selectedDate = dateInput.value;
                const dateData = selectedDate ? all.filter(x => recordDate(x.borrowedAt) === selectedDate) : all;
                const active = dateData.filter(x => x.status === 'Dipinjam');
                const returned = dateData.filter(x => x.status === 'Dikembalikan');

                document.getElementById('histTotal').textContent = dateData.length;
                document.getElementById('histActive').textContent = active.length;
                document.getElementById('histReturned').textContent = returned.length;

                const q = search.value.trim().toLowerCase();
                const data = dateData.filter(x => {
                    const st = BinaLib.student(x.studentId);
                    const b = BinaLib.book(x.bookId);
                    return [st?.name, st?.nis, st?.class, b?.title, b?.code, x.status, x.loanType, category(x)]
                        .some(v => String(v || '').toLowerCase().includes(q));
                });

                rows.innerHTML = data.length ? data.map(x => {
                    const st = BinaLib.student(x.studentId);
                    const b = BinaLib.book(x.bookId);
                    const qty = Math.max(1, Number(x.quantity) || 1);
                    const dt = dateParts(x.borrowedAt);
                    return `
                        <tr>
                            <td><strong>${BinaLib.escapeHtml(dt.date)}</strong><small class="table-sub">${BinaLib.escapeHtml(dt.time)}</small></td>
                            <td><strong>${BinaLib.escapeHtml(st?.name || '-')}</strong><small class="table-sub">${BinaLib.escapeHtml(st?.nis || '')}</small></td>
                            <td>${BinaLib.escapeHtml(b?.title || '-')}<small class="table-sub">${BinaLib.escapeHtml(b?.code || '')}</small></td>
                            <td><span class="student-class-badge">${BinaLib.escapeHtml(st?.class || x.className || '-')}</span></td>
                            <td><span class="loan-category-badge ${x.loanType === 'Kegunaan Kelas' ? 'class-loan' : 'personal-loan'}">${BinaLib.escapeHtml(category(x))}</span></td>
                            <td>${qty}</td>
                            <td><span class="status ${x.status === 'Dipinjam' ? 'status-borrowed' : 'status-returned'}">${BinaLib.escapeHtml(x.status || '-')}</span></td>
                        </tr>`;
                }).join('') : `<tr><td colspan="7"><div class="empty">Tidak ada riwayat peminjaman pada ${dateInput.value ? new Date(dateInput.value + 'T00:00:00').toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : 'periode ini'}.</div></td></tr>`;
            }

            dateInput.value = localISODate();
            dateInput.addEventListener('change', render);
            search.addEventListener('input', render);
            document.getElementById('todayHistory').addEventListener('click', () => { dateInput.value = localISODate(); render(); });
            document.getElementById('refreshHistory').addEventListener('click', () => { delete window.__binalibHistoryCache; location.reload(); });
            render();
        })();
    </script>

    <script id="binalib-live-sync">
        window.addEventListener(
            'storage',
            () => location.reload()
        );
    </script>

</body>

</html>