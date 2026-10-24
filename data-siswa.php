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

    <title>BinaLib - Data Siswa</title>

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


        html.binalib-ready {
            visibility: visible;
        }

        .promotion-card { margin-top: 18px; padding: 24px; }
        .promotion-head { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; margin-bottom:20px; }
        .promotion-head h2 { margin:4px 0 6px; }
        .promotion-head p { margin:0; max-width:760px; }
        .promotion-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:20px; }
        .promotion-stat { border:1px solid var(--border); background:#F6FAF7; border-radius:12px; padding:15px; }
        .promotion-stat span { display:block; color:var(--muted); font-size:11px; margin-bottom:6px; }
        .promotion-stat strong { font-size:22px; color:var(--green-dark); }
        .promotion-actions { display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; }
        .promotion-year { min-width:190px; }
        .promotion-warning { margin-top:14px; }
        .status-active { background:#EAF6EE; color:#0B6B3A; }
        .status-inactive { background:#F2F4F3; color:#64746B; }
        @media (max-width:900px) { .promotion-grid{grid-template-columns:repeat(2,minmax(0,1fr));} .promotion-head{flex-direction:column;} }
        @media (max-width:560px) { .promotion-grid{grid-template-columns:1fr;} .promotion-card{padding:18px;} }
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
                        class="active"
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
                            Perpustakaan
                        </div>

                        <h1>Data Siswa</h1>

                        <p>
                            Data siswa yang terdaftar di database
                            perpustakaan BinaLib.
                        </p>

                    </div>

                    <a
                        class="btn btn-primary"
                        href="registrasi-rfid.php"
                    >
                        + Registrasi RFID
                    </a>

                </section>

                <section class="card">

                    <div class="toolbar">

                        <input
                            class="input"
                            id="studentSearch"
                            placeholder="Cari ID anggota, nama, NIS, kelas, atau RFID..."
                        >

                        <button
                            class="btn btn-outline"
                            id="refreshStudents"
                        >
                            Refresh
                        </button>

                        <span class="muted student-list-note">Hanya siswa aktif ditampilkan</span>

                    </div>

                    <div class="table-wrap">

                        <table class="table">

                            <thead>
                                <tr>
                                    <th>ID Anggota</th>
                                    <th>NIS</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>RFID UID</th>
                                    <th>Status</th>
                                    <th class="actions">Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="studentRows"></tbody>

                        </table>

                    </div>

                </section>

                <section class="card promotion-card">
                    <div class="promotion-head">
                        <div>
                            <div class="eyebrow">TAHUN AJARAN</div>
                            <h2>Kenaikan Kelas & Kelulusan</h2>
                            <p class="muted">
                                Kelas 12 ditandai lulus, kelas 11 naik menjadi kelas 12, dan kelas 10 naik menjadi kelas 11.
                                Data siswa serta riwayat peminjaman tetap tersimpan.
                            </p>
                        </div>
                        <button class="btn btn-outline" id="refreshPromotion" type="button">Perbarui Data</button>
                    </div>
                    <div class="promotion-grid">
                        <div class="promotion-stat"><span>Kelas 10 → Kelas 11</span><strong id="promo10">0</strong></div>
                        <div class="promotion-stat"><span>Kelas 11 → Kelas 12</span><strong id="promo11">0</strong></div>
                        <div class="promotion-stat"><span>Kelas 12 → Lulus</span><strong id="promo12">0</strong></div>
                        <div class="promotion-stat"><span>Kelas 12 masih meminjam</span><strong id="promoBlocked">0</strong></div>
                    </div>
                    <div class="promotion-actions">
                        <div class="field promotion-year">
                            <label for="promotionYear">Tahun Ajaran Baru</label>
                            <input class="input" id="promotionYear" placeholder="2026/2027">
                        </div>
                        <button class="btn btn-primary" id="runPromotion" type="button">Proses Kenaikan Kelas</button>
                    </div>
                    <div class="notice promotion-warning" id="promotionNotice">
                        Pastikan seluruh peminjaman siswa kelas 12 sudah dikembalikan sebelum proses dijalankan.
                    </div>
                </section>

                <div
                    class="modal-backdrop"
                    id="studentModal"
                >

                    <div class="modal">

                        <h2 id="studentModalTitle">
                            Tambah Siswa
                        </h2>

                        <p>
                            Masukkan identitas siswa dan UID kartu RFID.
                        </p>

                        <form id="studentForm">

                            <div class="form-grid">

                                <div class="field">

                                    <label>NIS</label>

                                    <input
                                        class="input"
                                        name="nis"
                                        required
                                    >

                                </div>

                                <div class="field">

                                    <label>Kelas</label>

                                    <select
                                        class="input"
                                        name="class"
                                        required
                                    >
                                        <option value="">
                                            Pilih kelas
                                        </option>

                                        <option value="Kelas 10">
                                            Kelas 10
                                        </option>

                                        <option value="Kelas 11">
                                            Kelas 11
                                        </option>

                                        <option value="Kelas 12">
                                            Kelas 12
                                        </option>

                                        <option value="10 KU-Binar">
                                            10 KU-Binar
                                        </option>

                                        <option value="11 KU-Binar">
                                            11 KU-Binar
                                        </option>

                                        <option value="12 KU-Binar">
                                            12 KU-Binar
                                        </option>
                                    </select>

                                </div>

                                <div class="field full">

                                    <label>Nama Siswa</label>

                                    <input
                                        class="input"
                                        name="name"
                                        required
                                    >

                                </div>

                                <div class="field full">

                                    <label>RFID UID</label>

                                    <input
                                        class="input"
                                        name="rfid"
                                        inputmode="numeric"
                                        maxlength="10"
                                        pattern="[0-9]{10}"
                                        placeholder="10 digit, contoh 0001950658"
                                    >

                                </div>

                            </div>

                            <div class="modal-actions">

                                <button
                                    type="button"
                                    class="btn btn-outline"
                                    id="closeStudent"
                                >
                                    Batal
                                </button>

                                <button
                                    class="btn btn-primary"
                                >
                                    Simpan Siswa
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </main>

        </div>

    </div>

    <script src="binalib.js"></script>

    <script>
        (() => {
            const rows =
                document.getElementById('studentRows');

            const search =
                document.getElementById('studentSearch');

            const modal =
                document.getElementById('studentModal');

            const form =
                document.getElementById('studentForm');

            const title =
                document.getElementById(
                    'studentModalTitle'
                );

            document.getElementById('promotionYear').value = defaultAcademicYear();

            let editId = 0;

            function defaultAcademicYear() {
                const now = new Date();
                const y = now.getFullYear();
                const start = now.getMonth() >= 6 ? y : y - 1;
                return `${start}/${start + 1}`;
            }

            function renderPromotion() {
                const data = BinaLib.promotionPreview();
                if (!data) return;
                document.getElementById('promo10').textContent = data.grade10 ?? 0;
                document.getElementById('promo11').textContent = data.grade11 ?? 0;
                document.getElementById('promo12').textContent = data.grade12 ?? 0;
                document.getElementById('promoBlocked').textContent = data.activeBorrowingsGrade12 ?? 0;
                const notice = document.getElementById('promotionNotice');
                if ((data.activeBorrowingsGrade12 ?? 0) > 0) {
                    notice.className = 'notice danger-text promotion-warning';
                    notice.textContent = 'Proses belum dapat dijalankan karena masih ada peminjaman aktif oleh siswa kelas 12. Kembalikan buku terlebih dahulu.';
                } else {
                    notice.className = 'notice promotion-warning';
                    notice.textContent = 'Tidak ada peminjaman aktif kelas 12. Proses hanya dapat dijalankan satu kali untuk setiap tahun ajaran.';
                }
            }

            function render() {
                const q = search.value
                    .trim()
                    .toLowerCase();

                const classRank = value => {
                    const m = String(value || '').match(/(?:^|[^0-9])(12|11|10)(?:[^0-9]|$)/);
                    return m ? Number(m[1]) : 0;
                };

                const students = BinaLib
                    .get('students')
                    .filter(s => String(s.status || 'Aktif').toLowerCase() === 'aktif')
                    .filter(
                        s =>
                            [
                                s.libraryId,
                                s.nis,
                                s.name,
                                s.class,
                                s.rfid
                            ].some(
                                v =>
                                    String(v ?? '')
                                        .toLowerCase()
                                        .includes(q)
                            )
                    )
                    .sort((a, b) => {
                        const rankDiff = classRank(b.class) - classRank(a.class);
                        if (rankDiff) return rankDiff;
                        const classDiff = String(a.class || '').localeCompare(String(b.class || ''), 'id', { numeric: true });
                        if (classDiff) return classDiff;
                        return String(a.name || '').localeCompare(String(b.name || ''), 'id');
                    });

                rows.innerHTML = students.length
                    ? students
                        .map(
                            s => `
                                <tr>

                                    <td>
                                        <strong>
                                            ${BinaLib.escapeHtml(
                                                s.libraryId || '-'
                                            )}
                                        </strong>
                                    </td>

                                    <td>
                                        ${BinaLib.escapeHtml(s.nis)}
                                    </td>

                                    <td>
                                        ${BinaLib.escapeHtml(s.name)}
                                    </td>

                                    <td>
                                        <span class="student-class-badge">
                                            ${BinaLib.escapeHtml(s.class)}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge badge-green">
                                            ${BinaLib.escapeHtml(s.rfid || '-')}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge ${s.status === 'Aktif' ? 'status-active' : 'status-inactive'}">
                                            ${BinaLib.escapeHtml(s.status || 'Aktif')}
                                        </span>
                                    </td>

                                    <td class="actions">

                                        <button
                                            class="btn btn-outline"
                                            data-edit="${s.id}"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            class="btn btn-danger"
                                            data-delete="${s.id}"
                                        >
                                            Hapus
                                        </button>

                                    </td>

                                </tr>
                            `
                        )
                        .join('')
                    : `
                        <tr>
                            <td
                                colspan="7"
                                class="empty"
                            >
                                Data siswa tidak ditemukan.
                            </td>
                        </tr>
                    `;
            }

            function open(s) {
                editId = s?.id || 0;

                title.textContent = editId
                    ? 'Edit Siswa'
                    : 'Tambah Siswa';

                [
                    'nis',
                    'name',
                    'class',
                    'rfid'
                ].forEach(
                    k => {
                        form.elements[k].value =
                            s?.[k] ?? '';
                    }
                );

                modal.classList.add('open');

                form.elements.name.focus();
            }

            function close() {
                modal.classList.remove('open');

                form.reset();

                editId = 0;
            }

            document
                .getElementById('addStudent')
                ?.addEventListener(
                    'click',
                    () => open()
                );

            document.getElementById(
                'closeStudent'
            ).onclick = close;

            document.getElementById(
                'refreshStudents'
            ).onclick = render;

            document.getElementById('refreshPromotion').onclick = renderPromotion;
            document.getElementById('runPromotion').onclick = () => {
                const year = document.getElementById('promotionYear').value.trim();
                const data = BinaLib.promotionPreview();
                if (!/^\d{4}\/\d{4}$/.test(year)) return BinaLib.saveToast('Format tahun ajaran harus seperti 2026/2027.');
                if (!data) return;
                if ((data.activeBorrowingsGrade12 ?? 0) > 0) return BinaLib.saveToast('Kembalikan seluruh buku yang masih dipinjam siswa kelas 12 terlebih dahulu.');
                if ((data.grade10 ?? 0) + (data.grade11 ?? 0) + (data.grade12 ?? 0) === 0) return BinaLib.saveToast('Tidak ada siswa kelas 10, 11, atau 12 yang aktif untuk diproses.');
                if (!confirm(`Proses kenaikan kelas tahun ajaran ${year}?\n\nKelas 12 → Lulus: ${data.grade12}\nKelas 11 → Kelas 12: ${data.grade11}\nKelas 10 → Kelas 11: ${data.grade10}\n\nData siswa tidak dihapus dan riwayat peminjaman tetap dipertahankan.`)) return;
                const result = BinaLib.promoteStudents({ academic_year: year });
                if (result) { BinaLib.saveToast(`Kenaikan kelas ${year} berhasil diproses.`); render(); renderPromotion(); }
            };

            search.oninput = render;

            rows.onclick = e => {
                const ed =
                    e.target.closest(
                        '[data-edit]'
                    );

                const del =
                    e.target.closest(
                        '[data-delete]'
                    );

                if (ed) {
                    open(
                        BinaLib.student(
                            Number(
                                ed.dataset.edit
                            )
                        )
                    );
                }

                if (del) {
                    const id =
                        Number(
                            del.dataset.delete
                        );

                    if (confirm(
                        'Hapus siswa ini?\n\n' +
                        'Semua riwayat peminjaman siswa akan ikut dihapus. ' +
                        'Jika masih ada buku yang dipinjam, stok buku akan dikembalikan otomatis.'
                    )) {
                        if (
                            BinaLib.remove(
                                'students',
                                id
                            )
                        ) {
                            render();

                            BinaLib.saveToast(
                                'Siswa dihapus.'
                            );
                        }
                    }
                }
            };

            form.onsubmit = e => {
                e.preventDefault();

                const f = new FormData(form);
                const students =
                    BinaLib.get('students');

                const nis = String(
                    f.get('nis')
                ).trim();

                const rfid = String(
                    f.get('rfid')
                ).trim();

                if (
                    students.some(
                        s =>
                            String(s.nis)
                                .toLowerCase() ===
                            nis.toLowerCase() &&
                            Number(s.id) !== editId
                    )
                ) {
                    return BinaLib.saveToast(
                        'NIS sudah digunakan.'
                    );
                }

                if (rfid !== '' && !/^\d{10}$/.test(rfid)) {
                    return BinaLib.saveToast(
                        'RFID UID harus tepat 10 digit angka.'
                    );
                }

                if (
                    rfid !== '' &&
                    students.some(
                        s =>
                            String(s.rfid || '').toLowerCase() ===
                            rfid.toLowerCase() &&
                            Number(s.id) !== editId
                    )
                ) {
                    return BinaLib.saveToast(
                        'RFID UID sudah terdaftar.'
                    );
                }

                const existing = editId
                    ? students.find(
                        s =>
                            Number(s.id) ===
                            editId
                    )
                    : null;

                const item = {
                    id:
                        editId ||
                        BinaLib.uid(students),

                    libraryId:
                        existing?.libraryId ||
                        `LIB-${String(
                            BinaLib.uid(students)
                        ).padStart(5, '0')}`,

                    nis,

                    name: String(
                        f.get('name')
                    ).trim(),

                    class: String(
                        f.get('class')
                    ).trim(),

                    rfid,

                    status:
                        existing?.status ||
                        'Aktif'
                };

                BinaLib.set(
                    'students',
                    editId
                        ? students.map(
                            s =>
                                Number(s.id) ===
                                editId
                                    ? item
                                    : s
                        )
                        : [
                            ...students,
                            item
                        ]
                );

                close();
                render();

                BinaLib.saveToast(
                    editId
                        ? 'Data siswa diperbarui.'
                        : 'Siswa berhasil ditambahkan.'
                );
            };

            modal.onclick = e => {
                if (e.target === modal) {
                    close();
                }
            };

            render();
            renderPromotion();
        })();
    </script>

</body>

</html>