<?php
require_once __DIR__ . "/includes/auth.php";
require_admin();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>BinaLib - Riwayat E-Book</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Material+Symbols+Outlined:opsz,Wght,FILL,GRAD@20..48,400,0,0&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="simple-v4.css">

    <style>
        html {
            visibility: hidden;
        }

        .binalib-ready {
            visibility: visible;
        }

        .history-filters {
            display: grid;
            grid-template-columns: 1fr 180px 180px auto;
            gap: 10px;
            align-items: end;
        }

        .history-note {
            font-size: 12px;
            color: #6B7A72;
        }

        .history-progress {
            min-width: 120px;
        }

        .history-progress-track {
            height: 6px;
            background: #EAF0EC;
            border-radius: 99px;
            overflow: hidden;
        }

        .history-progress-fill {
            height: 100%;
            background: #0B6B3A;
        }
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
                    <a data-path="dashboard" href="dashboard.php">
                        <span class="material-symbols-outlined">grid_view</span>
                        Dashboard
                    </a>

                    <a data-path="data-siswa" href="data-siswa.php">
                        <span class="material-symbols-outlined">group</span>
                        Data Siswa
                    </a>

                    <a data-path="registrasi-rfid" href="registrasi-rfid.php">
                        <span class="material-symbols-outlined">contactless</span>
                        Registrasi RFID
                    </a>

                    <a data-path="data-buku" href="data-buku.php">
                        <span class="material-symbols-outlined">auto_stories</span>
                        Data Buku
                    </a>

                    <a data-path="ebooks" href="ebook.php">
                        <span class="material-symbols-outlined">menu_book</span>
                        E-Book
                    </a>

                    <a data-path="laporan" href="laporan.php">
                        <span class="material-symbols-outlined">analytics</span>
                        Laporan
                    </a>

                    <a data-path="pengaturan" href="pengaturan.php">
                        <span class="material-symbols-outlined">settings</span>
                        Pengaturan
                    </a>
                </nav>
            </div>

            <div class="sidebar-bottom">
                <a class="nav logout" data-path="login" href="index.php">
                    <span class="material-symbols-outlined">logout</span>
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
                        <div class="eyebrow">Aktivitas Digital</div>
                        <h1>Riwayat E-Book</h1>
                        <p>
                            Pantau riwayat membaca dan progress E-Book setiap
                            siswa.
                        </p>
                    </div>
                </section>

                <section class="card">
                    <div class="history-filters">
                        <div class="field">
                            <label>Cari</label>
                            <input
                                class="input"
                                id="search"
                                placeholder="Nama, NIS, atau E-Book..."
                            >
                        </div>

                        <div class="field">
                            <label>Dari</label>
                            <input
                                class="input"
                                id="from"
                                type="date"
                            >
                        </div>

                        <div class="field">
                            <label>Sampai</label>
                            <input
                                class="input"
                                id="to"
                                type="date"
                            >
                        </div>

                        <button class="btn btn-outline" id="refresh">
                            Refresh
                        </button>
                    </div>

                    <p class="history-note">
                        Data diambil dari aktivitas membaca yang tersimpan
                        di BinaLib.
                    </p>
                </section>

                <section class="card">
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>NIS</th>
                                    <th>E-Book</th>
                                    <th>Halaman</th>
                                    <th>Progress</th>
                                    <th>Durasi</th>
                                    <th>Sesi</th>
                                    <th>Terakhir Dibaca</th>
                                </tr>
                            </thead>

                            <tbody id="rows"></tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="binalib.js"></script>

    <script>
        (() => {
            const $ = id => document.getElementById(id);
            const rows = $('rows');
            const search = $('search');

            let data = [];

            function duration(s) {
                s = Number(s) || 0;

                const h = Math.floor(s / 3600);
                const m = Math.floor((s % 3600) / 60);
                const sec = s % 60;

                return h
                    ? `${h}j ${m}m`
                    : m
                        ? `${m}m ${sec}d`
                        : `${sec}d`;
            }

            function render() {
                const q = search.value.trim().toLowerCase();
                const from = $('from').value;
                const to = $('to').value;

                const list = data.filter(x => {
                    const hay = [
                        x.student_name,
                        x.nis,
                        x.ebook_title
                    ].join(' ').toLowerCase();

                    if (q && !hay.includes(q)) {
                        return false;
                    }

                    const d = String(x.last_read_at || '').slice(0, 10);

                    if (from && d < from) {
                        return false;
                    }

                    if (to && d > to) {
                        return false;
                    }

                    return true;
                });

                rows.innerHTML = list.length
                    ? list.map(x => `
                        <tr>
                            <td>
                                <strong>
                                    ${BinaLib.escapeHtml(x.student_name || '-')}
                                </strong>
                            </td>

                            <td>
                                ${BinaLib.escapeHtml(x.nis || '-')}
                            </td>

                            <td>
                                ${BinaLib.escapeHtml(x.ebook_title || '-')}
                            </td>

                            <td>
                                ${x.last_page || 1}
                                ${x.total_pages ? ` / ${x.total_pages}` : ''}
                            </td>

                            <td>
                                <div class="history-progress">
                                    <div class="history-progress-track">
                                        <div
                                            class="history-progress-fill"
                                            style="width:${Math.min(
                                                100,
                                                Number(x.progress_percent) || 0
                                            )}%"
                                        ></div>
                                    </div>

                                    <small>
                                        ${Number(
                                            x.progress_percent || 0
                                        ).toFixed(0)}%
                                    </small>
                                </div>
                            </td>

                            <td>
                                ${duration(x.duration_seconds)}
                            </td>

                            <td>
                                ${x.session_count || 0}x
                            </td>

                            <td>
                                ${BinaLib.formatDateTime(x.last_read_at)}
                            </td>
                        </tr>
                    `).join('')
                    : `
                        <tr>
                            <td colspan="8" class="empty">
                                Belum ada riwayat membaca E-Book.
                            </td>
                        </tr>
                    `;
            }

            function load() {
                data = BinaLib.get('ebook_history', []);
                render();
            }

            search.oninput = render;
            $('from').onchange = render;
            $('to').onchange = render;
            $('refresh').onclick = load;

            load();
        })();
    </script>
</body>

</html>