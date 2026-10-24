<?php

require_once __DIR__ . "/includes/auth.php";

require_admin();

?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>BinaLib - E-Book</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="simple-v4.css">

    <style>
        html {
            visibility: hidden;
        }

        html.binalib-ready {
            visibility: visible;
        }

        .ebook-cover-cell img {
            width: 42px;
            height: 58px;
            object-fit: cover;
            border-radius: 7px;
            border: 1px solid #DDE8E1;
        }

        .ebook-cover-empty {
            width: 42px;
            height: 58px;
            border-radius: 7px;
            background: #EAF6EE;
            display: grid;
            place-items: center;
            color: #0B6B3A;
            font-size: 10px;
            text-align: center;
            padding: 4px;
        }

        .ebook-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .ebook-file-help {
            font-size: 12px;
            color: #66776e;
            line-height: 1.6;
        }

        .ebook-detail {
            color: #66776e;
            font-size: 12px;
            line-height: 1.6;
        }

        .modal textarea {
            min-height: 110px;
            resize: vertical;
        }

        .content {
            padding: 30px clamp(20px, 3vw, 48px) 48px !important;
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        .page-head {
            gap: 24px;
            margin-bottom: 26px;
            align-items: center;
        }

        .card {
            padding: 22px !important;
            margin-bottom: 24px;
            border-radius: 14px;
        }

        .section-title {
            gap: 20px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
            border-radius: 10px;
        }

        .table {
            width: 100%;
            min-width: 900px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th,
        .table td {
            padding: 14px 16px !important;
            line-height: 1.55;
            vertical-align: middle;
            white-space: normal;
        }

        .table th {
            font-size: 11px;
            letter-spacing: .04em;
        }

        .table td small {
            display: block;
            margin-top: 4px;
        }

        .toolbar {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 16px;
        }

        .toolbar .input {
            min-width: 0;
            flex: 1;
        }

        .modal {
            max-height: 90vh;
            overflow-y: auto;
            padding: 28px !important;
        }

        .form-grid {
            gap: 18px !important;
        }

        @media (max-width: 760px) {
            .content {
                padding: 22px 14px 36px !important;
            }

            .page-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .section-title {
                flex-direction: column;
            }

            .card {
                padding: 16px !important;
            }

            .toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .modal {
                width: calc(100% - 24px);
                padding: 18px !important;
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
                        <span class="material-symbols-outlined">grid_view</span>Dashboard
                    </a>

                    <a data-path="data-siswa" href="data-siswa.php">
                        <span class="material-symbols-outlined">group</span>Data Siswa
                    </a>

                    <a data-path="registrasi-rfid" href="registrasi-rfid.php">
                        <span class="material-symbols-outlined">contactless</span>Registrasi RFID
                    </a>

                    <a data-path="data-buku" href="data-buku.php">
                        <span class="material-symbols-outlined">auto_stories</span>Data Buku
                    </a>

                    <a class="active" data-path="ebooks" href="ebook.php">
                        <span class="material-symbols-outlined">menu_book</span>E-Book
                    </a>

                    <a data-path="laporan" href="laporan.php">
                        <span class="material-symbols-outlined">analytics</span>Laporan
                    </a>

                    <a data-path="pengaturan" href="pengaturan.php">
                        <span class="material-symbols-outlined">settings</span>Pengaturan
                    </a>
                </nav>
            </div>

            <div class="sidebar-bottom">
                <a class="nav logout" data-path="login" href="index.php">
                    <span class="material-symbols-outlined">logout</span>Keluar
                </a>
            </div>
        </aside>

        <div class="main">
            <header class="topbar">
                <input class="search" placeholder="Cari buku, siswa, atau peminjaman...">

                <div class="profile">
                    <b>Admin</b>
                    <span>Pustakawan Admin</span>
                </div>
            </header>

            <main class="content">
                <section class="page-head">
                    <div>
                        <div class="eyebrow">Koleksi Digital</div>
                        <h1>E-Book</h1>
                        <p>Kelola koleksi E-Book yang digunakan siswa untuk membaca dan mengunduh PDF secara mandiri.</p>
                    </div>

                    <button class="btn btn-primary" id="addEbook">+ Tambah E-Book</button>
                </section>

                <section class="card" style="margin-bottom:20px">
                    <div class="section-title">
                        <div>
                            <div class="eyebrow">Moderasi Konten</div>
                            <h2>Pengajuan E-Book dari Siswa</h2>
                            <p class="ebook-file-help">Periksa relevansi, keamanan, dan hak distribusi sebelum menyetujui.</p>
                        </div>

                        <button class="btn btn-outline" id="refreshSubmissions">Refresh Pengajuan</button>
                    </div>

                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Siswa / NIS</th>
                                    <th>Judul</th>
                                    <th>File</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="submissionRows">
                                <tr>
                                    <td colspan="6" class="empty">Memuat pengajuan...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card">
                    <div class="toolbar">
                        <input class="input" id="ebookSearch" placeholder="Cari kode, judul, penulis, atau kategori...">
                        <button class="btn btn-outline" id="refreshEbooks">Refresh</button>
                    </div>

                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Cover</th>
                                    <th>Kode</th>
                                    <th>Judul E-Book</th>
                                    <th>Penulis</th>
                                    <th>Kategori</th>
                                    <th>Tahun</th>
                                    <th>Status</th>
                                    <th class="actions">Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="ebookRows"></tbody>
                        </table>
                    </div>
                </section>

                <div class="modal-backdrop" id="ebookModal">
                    <div class="modal">
                        <h2 id="modalTitle">Tambah E-Book</h2>
                        <p>PDF disimpan di server BinaLib. Cover akan dibuat otomatis dari halaman pertama PDF.</p>

                        <form id="ebookForm" enctype="multipart/form-data">
                            <div class="form-grid">
                                <div class="field">
                                    <label>Kode E-Book</label>
                                    <input class="input" name="code" required>
                                </div>

                                <div class="field">
                                    <label>Tahun Terbit</label>
                                    <input class="input" name="year" type="number" min="1900" max="2100">
                                </div>

                                <div class="field full">
                                    <label>Judul E-Book</label>
                                    <input class="input" name="title" required>
                                </div>

                                <div class="field">
                                    <label>Penulis</label>
                                    <input class="input" name="author" required>
                                </div>

                                <div class="field">
                                    <label>Penerbit</label>
                                    <input class="input" name="publisher">
                                </div>

                                <div class="field">
                                    <label>ISBN</label>
                                    <input class="input" name="isbn">
                                </div>

                                <div class="field">
                                    <label>Kategori</label>
                                    <input class="input" name="category" placeholder="Contoh: Informatika">
                                </div>

                                <div class="field">
                                    <label>Jumlah Halaman</label>
                                    <input class="input" name="pageCount" type="number" min="1">
                                </div>

                                <div class="field">
                                    <label>Status</label>
                                    <select class="input" name="status">
                                        <option>Aktif</option>
                                        <option>Nonaktif</option>
                                    </select>
                                </div>

                                <div class="field full">
                                    <label>Deskripsi</label>
                                    <textarea class="input" name="description" placeholder="Deskripsi singkat E-Book..."></textarea>
                                </div>

                                <div class="field">
                                    <label>Cover (opsional)</label>
                                    <input class="input" name="cover" type="file" accept="image/jpeg,image/png,image/webp">
                                    <small class="ebook-file-help">JPG, PNG, atau WebP. Maks. 2 MB. Saat edit, kosongkan jika ingin mempertahankan cover lama.</small>
                                </div>

                                <div class="field">
                                    <label>File PDF</label>
                                    <input class="input" name="pdf" type="file" accept="application/pdf,.pdf">
                                    <small class="ebook-file-help">PDF. Maks. 50 MB. Wajib saat menambah E-Book; saat edit boleh dikosongkan.</small>
                                </div>
                            </div>

                            <div class="modal-actions">
                                <button type="button" class="btn btn-outline" id="closeEbook">Batal</button>
                                <button class="btn btn-primary">Simpan E-Book</button>
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
            const rows = document.getElementById('ebookRows');
            const search = document.getElementById('ebookSearch');
            const modal = document.getElementById('ebookModal');
            const form = document.getElementById('ebookForm');
            const title = document.getElementById('modalTitle');

            let editId = 0;

            function data() {
                return BinaLib.get('ebooks', []);
            }

            function render() {
                const q = search.value.trim().toLowerCase();

                const list = data().filter(e =>
                    [e.code, e.title, e.author, e.category, e.publisher, e.isbn, e.year]
                        .some(v => String(v || '').toLowerCase().includes(q))
                );

                rows.innerHTML = list.length
                    ? list.map(e => `
                        <tr>
                            <td class="ebook-cover-cell">
                                ${e.coverPath
                                    ? `<img src="${BinaLib.escapeHtml(e.coverPath)}" alt="">`
                                    : '<div class="ebook-cover-empty">No<br>Cover</div>'
                                }
                            </td>
                            <td><strong>${BinaLib.escapeHtml(e.code)}</strong></td>
                            <td>
                                <strong>${BinaLib.escapeHtml(e.title)}</strong>
                                <small class="ebook-detail">${BinaLib.escapeHtml(e.publisher || '')}</small>
                            </td>
                            <td>${BinaLib.escapeHtml(e.author)}</td>
                            <td>${BinaLib.escapeHtml(e.category || '-')}</td>
                            <td>${e.year || '-'}</td>
                            <td>
                                <span class="badge ${e.status === 'Aktif' ? 'badge-green' : 'badge-red'}">
                                    ${BinaLib.escapeHtml(e.status)}
                                </span>
                            </td>
                            <td class="actions">
                                <button class="btn btn-outline" data-edit="${e.id}">Edit</button>
                                <button class="btn ${e.status === 'Aktif' ? 'btn-danger' : 'btn-primary'}" data-toggle="${e.id}" data-status="${e.status}">
                                    ${e.status === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan'}
                                </button>
                            </td>
                        </tr>
                    `).join('')
                    : '<tr><td colspan="8" class="empty">Data E-Book tidak ditemukan.</td></tr>';
            }

            async function loadSubmissions() {
                const target = document.getElementById('submissionRows');

                try {
                    const res = await fetch('api.php?action=get&key=ebook_submissions', {
                        credentials: 'same-origin'
                    });

                    const data = await res.json();

                    if (!res.ok || data.ok === false) {
                        throw new Error(data.message || 'Gagal memuat pengajuan.');
                    }

                    const list = data.data || [];

                    target.innerHTML = list.length
                        ? list.map(x => `
                            <tr>
                                <td>${BinaLib.escapeHtml(x.created_at || '-')}</td>
                                <td>
                                    <strong>${BinaLib.escapeHtml(x.student_name || '-')}</strong>
                                    <small class="ebook-detail">NIS: ${BinaLib.escapeHtml(x.nis || '-')}</small>
                                    <small class="ebook-detail">UID: ${BinaLib.escapeHtml(x.rfid_uid || '-')}</small>
                                </td>
                                <td>
                                    <strong>${BinaLib.escapeHtml(x.title)}</strong>
                                    <small class="ebook-detail">
                                        ${BinaLib.escapeHtml(x.author)} · ${BinaLib.escapeHtml(x.category || 'Umum')}
                                    </small>
                                    <small class="ebook-detail">${BinaLib.escapeHtml(x.description || '')}</small>
                                </td>
                                <td>
                                    <a href="ebook-file.php?submission=${Number(x.id)}" target="_blank" rel="noopener">Preview PDF</a>
                                    <small class="ebook-detail">${(Number(x.file_size || 0) / 1024 / 1024).toFixed(1)} MB</small>
                                </td>
                                <td>
                                    <span class="badge ${x.status === 'Disetujui' ? 'badge-green' : x.status === 'Ditolak' ? 'badge-red' : ''}">
                                        ${BinaLib.escapeHtml(x.status)}
                                    </span>
                                    ${x.review_note
                                        ? `<small class="ebook-detail">${BinaLib.escapeHtml(x.review_note)}</small>`
                                        : ''
                                    }
                                </td>
                                <td>
                                    ${x.status === 'Menunggu'
                                        ? `<button class="btn btn-primary" data-approve="${x.id}">Setujui</button>
                                           <button class="btn btn-outline" data-reject="${x.id}">Tolak</button>`
                                        : '-'
                                    }
                                </td>
                            </tr>
                        `).join('')
                        : '<tr><td colspan="6" class="empty">Belum ada pengajuan E-Book dari siswa.</td></tr>';

                } catch (err) {
                    target.innerHTML = `
                        <tr>
                            <td colspan="6" class="empty">${BinaLib.escapeHtml(err.message)}</td>
                        </tr>
                    `;
                }
            }

            document.getElementById('refreshSubmissions').onclick = loadSubmissions;

            document.getElementById('submissionRows').onclick = async e => {
                const approve = e.target.closest('[data-approve]');
                const reject = e.target.closest('[data-reject]');

                if (!approve && !reject) return;

                const id = Number((approve || reject).dataset[approve ? 'approve' : 'reject']);

                let note = '';

                if (reject) {
                    note = prompt(
                        'Masukkan alasan penolakan:',
                        'Konten belum memenuhi ketentuan perpustakaan.'
                    );

                    if (note === null) return;
                }

                if (!confirm(approve ? 'Setujui dan publikasikan E-Book ini?' : 'Tolak pengajuan E-Book ini?')) {
                    return;
                }

                try {
                    const res = await fetch('api.php?action=moderate_ebook_submission', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id,
                            decision: approve ? 'approve' : 'reject',
                            note
                        })
                    });

                    const data = await res.json();

                    if (!res.ok || data.ok === false) {
                        throw new Error(data.message || 'Moderasi gagal.');
                    }

                    BinaLib.refresh('ebooks');
                    loadSubmissions();

                    BinaLib.saveToast(
                        approve
                            ? 'E-Book disetujui dan dipublikasikan.'
                            : 'Pengajuan ditolak.'
                    );

                } catch (err) {
                    BinaLib.saveToast(err.message);
                }
            };

            function open(item) {
                editId = item?.id || 0;

                title.textContent = editId ? 'Edit E-Book' : 'Tambah E-Book';

                [
                    'code',
                    'title',
                    'author',
                    'publisher',
                    'year',
                    'isbn',
                    'category',
                    'pageCount',
                    'description',
                    'status'
                ].forEach(k => {
                    form.elements[k].value = item?.[k] ?? (k === 'status' ? 'Aktif' : '');
                });

                form.elements.cover.value = '';
                form.elements.pdf.value = '';

                const pdfHelp = form.elements.pdf.closest('.field')?.querySelector('small');

                if (pdfHelp) {
                    pdfHelp.textContent = editId
                        ? 'PDF. Maks. 50 MB. Kosongkan jika ingin mempertahankan file lama.'
                        : 'PDF. Maks. 50 MB. Wajib saat menambah E-Book.';
                }

                modal.classList.add('open');
                form.elements.title.focus();
            }

            function close() {
                modal.classList.remove('open');
                form.reset();
                editId = 0;
            }

            document.getElementById('addEbook').onclick = () => open();
            document.getElementById('closeEbook').onclick = close;
            document.getElementById('refreshEbooks').onclick = render;
            search.oninput = render;

            rows.onclick = e => {
                const ed = e.target.closest('[data-edit]');
                const tg = e.target.closest('[data-toggle]');

                if (ed) {
                    open(data().find(x => Number(x.id) === Number(ed.dataset.edit)));
                }

                if (tg) {
                    const id = Number(tg.dataset.toggle);
                    const next = tg.dataset.status === 'Aktif' ? 'Nonaktif' : 'Aktif';

                    if (!confirm(`${next === 'Aktif' ? 'Aktifkan' : 'Nonaktifkan'} E-Book ini?`)) {
                        return;
                    }

                    const result = BinaLib.reqEbookToggle(id, next);

                    if (result) {
                        BinaLib.refresh('ebooks');
                        render();
                    }
                }
            };

            form.onsubmit = async e => {
                e.preventDefault();

                const submit = form.querySelector('button[type="submit"], button:not([type])');
                const fd = new FormData(form);

                fd.set('id', String(editId));

                const pdf = form.elements.pdf.files?.[0];
                const cover = form.elements.cover.files?.[0];

                if (!editId && !pdf) {
                    return BinaLib.saveToast('File PDF wajib dipilih saat menambah E-Book.');
                }

                if (pdf && (pdf.type !== 'application/pdf' || pdf.size > 50 * 1024 * 1024)) {
                    return BinaLib.saveToast('Pilih file PDF yang valid dengan ukuran maksimal 50 MB.');
                }

                if (
                    cover &&
                    (!['image/jpeg', 'image/png', 'image/webp'].includes(cover.type) ||
                        cover.size > 2 * 1024 * 1024)
                ) {
                    return BinaLib.saveToast('Cover harus JPG, PNG, atau WebP dengan ukuran maksimal 2 MB.');
                }

                const wasEditing = Boolean(editId);

                if (submit) {
                    submit.disabled = true;
                    submit.dataset.originalText = submit.textContent;
                    submit.textContent = 'Menyimpan...';
                }

                try {
                    const res = await fetch('api.php?action=save_ebook', {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: fd
                    });

                    const r = await res.json().catch(() => ({}));

                    if (!res.ok || r.ok === false) {
                        throw new Error(r.message || `E-Book gagal disimpan (HTTP ${res.status}).`);
                    }

                    BinaLib.refresh('ebooks');
                    close();
                    render();

                    BinaLib.saveToast(
                        wasEditing
                            ? 'E-Book berhasil diperbarui.'
                            : 'E-Book berhasil ditambahkan.'
                    );

                } catch (err) {
                    BinaLib.saveToast(err.message || 'E-Book gagal disimpan.');

                } finally {
                    if (submit) {
                        submit.disabled = false;
                        submit.textContent = submit.dataset.originalText || 'Simpan E-Book';
                    }
                }
            };

            render();
            loadSubmissions();
        })();
    </script>

</body>

</html>
```