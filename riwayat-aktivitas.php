<?php
require_once __DIR__ . "/includes/auth.php";
require_admin();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BinaLib - Riwayat Aktivitas</title>

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

        .activity-filters {
            display: grid;
            grid-template-columns: 1fr 170px 170px 180px auto;
            gap: 10px;
            align-items: end;
        }

        .activity-note {
            font-size: 12px;
            color: var(--muted);
        }

        .activity-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 5px 8px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
        }

        .activity-physical {
            background: #eaf6ee;
            color: #0b6b3a;
        }

        .activity-return {
            background: #fff5dc;
            color: #8a6500;
        }

        .activity-read {
            background: #f0ecfb;
            color: #5b4aa0;
        }

        .activity-download {
            background: #eef4ff;
            color: #315a9b;
        }

        .activity-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .activity-summary .mini-stat strong {
            display: block;
            font-size: 22px;
            color: var(--green-dark);
            margin-top: 5px;
        }

        .activity-summary .mini-stat span {
            font-size: 10px;
            color: var(--muted);
        }

        .activity-panels {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .activity-panels .card {
            padding: 24px;
            overflow: hidden;
        }

        .activity-panels .table-wrap {
            padding: 0;
            margin: 0;
            overflow-x: auto;
        }

        .activity-panels .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .activity-panels .table th,
        .activity-panels .table td {
            padding: 13px 16px;
            vertical-align: middle;
        }

        .activity-panels .table th:first-child,
        .activity-panels .table td:first-child {
            padding-left: 18px;
        }

        .activity-panels .table th:last-child,
        .activity-panels .table td:last-child {
            padding-right: 18px;
        }

        .activity-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .activity-panel-head h2 {
            margin: 0;
        }

        .activity-count {
            font-size: 12px;
            color: var(--muted);
            background: #f1f6f2;
            padding: 6px 10px;
            border-radius: 999px;
        }

        @media (max-width: 1050px) {
            .activity-filters {
                grid-template-columns: 1fr 1fr;
            }

            .activity-summary {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 760px) {
            .activity-panels .card {
                padding: 16px;
            }

            .activity-panels .table th,
            .activity-panels .table td {
                padding: 11px 12px;
            }

            .activity-filters {
                grid-template-columns: 1fr;
            }

            .activity-summary {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="shell">
        <aside class="sidebar">
            <div>
                <div class="brand">
                    <img src="assets/logo-bina-rahayu.png" alt="Logo Bina Rahayu">

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

                    <a data-path="riwayat-aktivitas" href="riwayat-aktivitas.php">
                        <span class="material-symbols-outlined">history</span>
                        Riwayat Aktivitas
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
                    placeholder="Cari siswa, buku, E-Book, atau aktivitas..."
                >

                <div class="profile">
                    <b>Admin</b>
                    <span>Pustakawan Admin</span>
                </div>
            </header>

            <main class="content">
                <section class="page-head">
                    <div>
                        <div class="eyebrow">Aktivitas</div>
                        <h1>Riwayat Aktivitas</h1>
                        <p>
                            Riwayat buku fisik dan aktivitas E-Book ditampilkan
                            terpisah agar mudah dipantau.
                        </p>
                    </div>
                </section>

                <section class="activity-summary">
                    <div class="mini-stat">
                        <span>Peminjaman</span>
                        <strong id="sumLoans">0</strong>
                    </div>

                    <div class="mini-stat">
                        <span>Pengembalian</span>
                        <strong id="sumReturns">0</strong>
                    </div>

                    <div class="mini-stat">
                        <span>Baca E-Book</span>
                        <strong id="sumReads">0</strong>
                    </div>

                    <div class="mini-stat">
                        <span>Download E-Book</span>
                        <strong id="sumDownloads">0</strong>
                    </div>
                </section>

                <section class="card">
                    <div class="activity-filters">
                        <div class="field">
                            <label>Cari</label>
                            <input
                                class="input"
                                id="search"
                                placeholder="Nama, NIS, buku, atau E-Book..."
                            >
                        </div>

                        <div class="field">
                            <label>Dari</label>
                            <input class="input" id="from" type="date">
                        </div>

                        <div class="field">
                            <label>Sampai</label>
                            <input class="input" id="to" type="date">
                        </div>

                        <div class="field">
                            <label>Jenis Aktivitas</label>
                            <select class="input" id="type">
                                <option value="">Semua aktivitas</option>
                                <option value="Peminjaman">Peminjaman</option>
                                <option value="Pengembalian">Pengembalian</option>
                                <option value="Membaca E-Book">Membaca E-Book</option>
                                <option value="Download E-Book">Download E-Book</option>
                            </select>
                        </div>

                        <button class="btn btn-outline" id="refresh">
                            Refresh
                        </button>
                    </div>

                    <p class="activity-note">
                        Aktivitas buku fisik yang tidak memiliki data buku
                        lengkap tidak ditampilkan agar riwayat tetap akurat.
                    </p>
                </section>

                <section class="activity-panels">
                    <section class="card">
                        <div class="activity-panel-head">
                            <div>
                                <div class="eyebrow">Perpustakaan Fisik</div>
                                <h2>Riwayat Peminjaman &amp; Pengembalian</h2>
                            </div>

                            <span class="activity-count" id="physicalCount">
                                0 aktivitas
                            </span>
                        </div>

                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Siswa</th>
                                        <th>Aktivitas</th>
                                        <th>Buku</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>

                                <tbody id="physicalRows"></tbody>
                            </table>
                        </div>
                    </section>

                    <section class="card">
                        <div class="activity-panel-head">
                            <div>
                                <div class="eyebrow">Perpustakaan Digital</div>
                                <h2>Riwayat Aktivitas E-Book</h2>
                            </div>

                            <span class="activity-count" id="digitalCount">
                                0 aktivitas
                            </span>
                        </div>

                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Siswa</th>
                                        <th>Aktivitas</th>
                                        <th>E-Book</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>

                                <tbody id="digitalRows"></tbody>
                            </table>
                        </div>
                    </section>
                </section>
            </main>
        </div>
    </div>

    <script src="binalib.js"></script>

    <script>
        (() => {
            const $ = id => document.getElementById(id);
            const rows = $('rows');

            let all = [];

            const esc = BinaLib.escapeHtml;

            const qty = x => Math.max(1, Number(x.quantity) || 1);

            function build() {
                const bor = BinaLib.get('borrowings', []);
                const hist = BinaLib.get('ebook_history', []);
                const downs = BinaLib.get('ebook_downloads', []);
                const out = [];

                bor.forEach(x => {
                    const s = BinaLib.student(x.studentId);
                    const b = BinaLib.book(x.bookId);

                    if (
                        !s?.name ||
                        !b?.title ||
                        !b?.code ||
                        !x.borrowedAt ||
                        !x.status
                    ) {
                        return;
                    }

                    out.push({
                        at: x.borrowedAt,
                        type: 'Peminjaman',
                        student: s.name,
                        nis: s.nis || '',
                        item: b.title,
                        sub: b.code,
                        detail: qty(x) + ' buku'
                    });

                    if (x.returnedAt) {
                        out.push({
                            at: x.returnedAt,
                            type: 'Pengembalian',
                            student: s.name,
                            nis: s.nis || '',
                            item: b.title,
                            sub: b.code,
                            detail: 'Dikembalikan'
                        });
                    }
                });

                hist.forEach(x => {
                    if (
                        !x.student_name ||
                        !x.ebook_title ||
                        !x.last_read_at
                    ) {
                        return;
                    }

                    out.push({
                        at: x.last_read_at,
                        type: 'Membaca E-Book',
                        student: x.student_name,
                        nis: x.nis || '',
                        item: x.ebook_title,
                        sub: 'Progress ' +
                            Math.round(Number(x.progress_percent) || 0) + '%',
                        detail: (x.session_count || 0) + ' sesi'
                    });
                });

                downs.forEach(x => {
                    if (
                        !x.student_name ||
                        !x.ebook_title ||
                        !x.downloaded_at
                    ) {
                        return;
                    }

                    out.push({
                        at: x.downloaded_at,
                        type: 'Download E-Book',
                        student: x.student_name,
                        nis: x.nis || '',
                        item: x.ebook_title,
                        sub: x.ebook_code || '',
                        detail: 'Downloaded at ' +
                            BinaLib.formatDateTime(x.downloaded_at)
                    });
                });

                all = out.sort((a, b) =>
                    String(b.at).localeCompare(String(a.at))
                );
            }

            function render() {
                const q = $('search').value.trim().toLowerCase();
                const from = $('from').value;
                const to = $('to').value;
                const type = $('type').value;

                const list = all.filter(x => {
                    const hay = [
                        x.student,
                        x.nis,
                        x.item,
                        x.sub,
                        x.type,
                        x.detail
                    ].join(' ').toLowerCase();

                    if (q && !hay.includes(q)) {
                        return false;
                    }

                    const d = String(x.at).slice(0, 10);

                    if (from && d < from) {
                        return false;
                    }

                    if (to && d > to) {
                        return false;
                    }

                    if (type && x.type !== type) {
                        return false;
                    }

                    return true;
                });

                $('sumLoans').textContent = all.filter(
                    x => x.type === 'Peminjaman'
                ).length;

                $('sumReturns').textContent = all.filter(
                    x => x.type === 'Pengembalian'
                ).length;

                $('sumReads').textContent = all.filter(
                    x => x.type === 'Membaca E-Book'
                ).length;

                $('sumDownloads').textContent = all.filter(
                    x => x.type === 'Download E-Book'
                ).length;

                rows.innerHTML = list.length
                    ? list.map(x => {
                        const cls = x.type === 'Peminjaman'
                            ? 'activity-physical'
                            : x.type === 'Pengembalian'
                                ? 'activity-return'
                                : x.type === 'Membaca E-Book'
                                    ? 'activity-read'
                                    : 'activity-download';

                        return `
                            <tr>
                                <td>${BinaLib.formatDateTime(x.at)}</td>
                                <td>
                                    <strong>${esc(x.student)}</strong>
                                    <small class="table-sub">
                                        ${esc(x.nis || '')}
                                    </small>
                                </td>
                                <td>
                                    <span class="activity-badge ${cls}">
                                        ${esc(x.type)}
                                    </span>
                                </td>
                                <td>
                                    ${esc(x.item)}
                                    <small class="table-sub">
                                        ${esc(x.sub || '')}
                                    </small>
                                </td>
                                <td>${esc(x.detail)}</td>
                            </tr>
                        `;
                    }).join('')
                    : `
                        <tr>
                            <td colspan="5" class="empty">
                                Belum ada aktivitas yang sesuai.
                            </td>
                        </tr>
                    `;
            }

            function load() {
                build();
                render();
            }

            $('search').oninput = render;
            $('from').onchange = render;
            $('to').onchange = render;
            $('type').onchange = render;
            $('refresh').onclick = load;

            load();
        })();
    </script>
</body>

</html>