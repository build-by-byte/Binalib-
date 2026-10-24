<?php

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

function input(): array
{
    $x = json_decode(
        file_get_contents('php://input'),
        true
    );

    return is_array($x) ? $x : [];
}

function out($data, int $code = 200): never
{
    http_response_code($code);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );

    exit;
}

function ok($data = null): never
{
    out([
        'ok' => true,
        'data' => $data
    ]);
}

function fail(string $m, int $c = 400): never
{
    out([
        'ok' => false,
        'message' => $m
    ], $c);
}

function admin(): void
{
    if (empty($_SESSION['bls_admin_id'])) {
        fail('Akses admin diperlukan.', 401);
    }
}

function ms(array $r): array
{
    return [
        'id' => (int) $r['id'],
        'libraryId' => $r['library_id'],
        'nis' => $r['nis'],
        'name' => $r['name'],
        'class' => $r['class_name'],
        'rfid' => $r['rfid_uid'],
        'status' => $r['status']
    ];
}

function mb(array $r): array
{
    return [
        'id' => (int) $r['id'],
        'code' => $r['code'],
        'title' => $r['title'],
        'author' => $r['author'],
        'category' => $r['category'] ?? null,
        'year' => $r['publication_year'] !== null
            ? (int) $r['publication_year']
            : null,
        'stock' => (int) $r['stock'],
        'imageData' => $r['image_data']
    ];
}

function mt(array $r): array
{
    return [
        'id' => (int) $r['id'],
        'studentId' => $r['student_id'] !== null
            ? (int) $r['student_id']
            : null,
        'bookId' => $r['book_id'] !== null
            ? (int) $r['book_id']
            : null,
        'quantity' => (int) $r['quantity'],
        'loanType' => $r['loan_type'],
        'className' => $r['class_name'],
        'borrowedAt' => $r['borrowed_at'],
        'returnedAt' => $r['returned_at'],
        'status' => $r['status']
    ];
}

function meb(array $r): array
{
    return [
        'id' => (int) $r['id'],
        'code' => $r['code'],
        'title' => $r['title'],
        'author' => $r['author'],
        'publisher' => $r['publisher'],
        'year' => $r['publication_year'] !== null
            ? (int) $r['publication_year']
            : null,
        'isbn' => $r['isbn'],
        'category' => $r['category'],
        'description' => $r['description'],
        'coverPath' => $r['cover_path'],
        'pdfPath' => $r['pdf_path'],
        'fileSize' => (int) $r['file_size'],
        'pageCount' => $r['page_count'] !== null
            ? (int) $r['page_count']
            : null,
        'status' => $r['status'],
        'createdAt' => $r['created_at'],
        'updatedAt' => $r['updated_at']
    ];
}

function ebook_student(): int
{
    $id = (int) ($_SESSION['bls_ebook_student_id'] ?? 0);

    if ($id < 1) {
        fail('Identitas siswa untuk E-Book belum diverifikasi.', 401);
    }

    return $id;
}

function upload_dir(string $type): string
{
    $dir = __DIR__ . '/uploads/ebooks/' . $type;

    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        fail('Folder upload E-Book tidak dapat dibuat. Periksa izin tulis folder uploads/ebooks.', 500);
    }

    if (!is_writable($dir)) {
        fail('Folder upload E-Book tidak dapat ditulis oleh PHP. Periksa izin folder uploads/ebooks.', 500);
    }

    return $dir;
}

function save_upload(array $file, string $type, array $allowed, int $maxBytes): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        fail('File gagal diunggah.');
    }

    if ((int) ($file['size'] ?? 0) > $maxBytes) {
        fail('Ukuran file terlalu besar.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!in_array($mime, $allowed, true)) {
        fail('Format file tidak didukung.');
    }

    $ext = 'bin';

    if ($type === 'pdf') {
        $ext = 'pdf';
    } elseif ($mime === 'image/png') {
        $ext = 'png';
    } elseif ($mime === 'image/webp') {
        $ext = 'webp';
    } else {
        $ext = 'jpg';
    }

    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $target = upload_dir($type) . '/' . $name;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        fail('File tidak dapat disimpan di server.');
    }

    return 'uploads/ebooks/' . $type . '/' . $name;
}

function book_upload_dir(): string
{
    $dir = __DIR__ . '/uploads/books';

    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        fail('Folder upload gambar buku tidak dapat dibuat di server.');
    }

    // XAMPP/LAMPP lokal sering menjalankan Apache dengan user yang berbeda
    // dari user yang mengekstrak project. Coba longgarkan permission folder
    // upload pada lingkungan development lokal sebelum gagal.
    if (!is_writable($dir)) {
        @chmod($dir, 0777);
    }

    if (!is_writable($dir)) {
        fail(
            'Folder uploads/books tidak dapat ditulis oleh PHP. ' .
            'Pada Linux XAMPP jalankan: chmod -R 777 uploads'
        );
    }

    return $dir;
}

function save_book_image(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        if (($file['error'] ?? 0) === UPLOAD_ERR_INI_SIZE) {
            fail('Ukuran gambar melebihi batas upload PHP di server.');
        }

        fail('Gambar buku gagal diunggah. Kode upload PHP: ' . (string) ($file['error'] ?? 'unknown'));
    }

    $maxBytes = 5 * 1024 * 1024;

    if ((int) ($file['size'] ?? 0) > $maxBytes) {
        fail('Ukuran gambar buku maksimal 5 MB.');
    }

    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        fail('File gambar buku tidak valid.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    if (!isset($extensions[$mime])) {
        fail('Format gambar tidak didukung. Gunakan JPG, PNG, atau WebP.');
    }

    $name = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    $target = book_upload_dir() . '/' . $name;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        fail('File gambar tidak dapat disimpan di server. Periksa permission folder uploads/books.');
    }

    return 'uploads/books/' . $name;
}

function delete_book_image(?string $path): void
{
    if (!$path || !str_starts_with($path, 'uploads/books/')) {
        return;
    }

    $full = __DIR__ . '/' . $path;

    if (is_file($full)) {
        @unlink($full);
    }
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$in = input();

try {
    $pdo = db();

    switch ($action) {

        case 'login':
            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $u = trim((string) ($in['username'] ?? ''));
            $p = (string) ($in['password'] ?? '');

            $q = $pdo->prepare(
                'SELECT * FROM admins WHERE username=? LIMIT 1'
            );

            $q->execute([$u]);
            $a = $q->fetch();

            if (!$a || !password_verify($p, $a['password_hash'])) {
                fail('Username atau password salah.', 401);
            }

            session_regenerate_id(true);

            $_SESSION['bls_admin_id'] = (int) $a['id'];
            $_SESSION['bls_admin_name'] = $a['name'];

            ok([
                'name' => $a['name']
            ]);

        case 'change_password':
            admin();

            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $current = (string) ($in['currentPassword'] ?? '');
            $new = (string) ($in['newPassword'] ?? '');
            $confirm = (string) ($in['confirmPassword'] ?? '');

            if (
                $current === '' ||
                $new === '' ||
                $confirm === ''
            ) {
                fail('Semua kolom password wajib diisi.');
            }

            if (strlen($new) < 8) {
                fail('Password baru minimal 8 karakter.');
            }

            if ($new !== $confirm) {
                fail('Konfirmasi password tidak sama.');
            }

            $q = $pdo->prepare(
                'SELECT password_hash FROM admins WHERE id=? LIMIT 1'
            );

            $q->execute([
                (int) $_SESSION['bls_admin_id']
            ]);

            $a = $q->fetch();

            if (
                !$a ||
                !password_verify($current, $a['password_hash'])
            ) {
                fail('Password saat ini salah.', 401);
            }

            $hash = password_hash(
                $new,
                PASSWORD_DEFAULT
            );

            $q = $pdo->prepare(
                'UPDATE admins SET password_hash=? WHERE id=?'
            );

            $q->execute([
                $hash,
                (int) $_SESSION['bls_admin_id']
            ]);

            ok();

        case 'logout':
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $p = session_get_cookie_params();

                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $p['path'],
                    $p['domain'],
                    $p['secure'],
                    $p['httponly']
                );
            }

            session_destroy();

            ok();

        case 'me':
            ok([
                'loggedIn' => !empty($_SESSION['bls_admin_id']),
                'adminName' => $_SESSION['bls_admin_name'] ?? null
            ]);

        case 'get':
            $key = (string) ($_GET['key'] ?? '');

            if ($key === 'students') {
                admin();

                $rows = $pdo->query(
                    "SELECT * FROM students
                     WHERE status='Aktif'
                     ORDER BY
                        CASE
                            WHEN class_name REGEXP '(^|[^0-9])12([^0-9]|$)' THEN 3
                            WHEN class_name REGEXP '(^|[^0-9])11([^0-9]|$)' THEN 2
                            WHEN class_name REGEXP '(^|[^0-9])10([^0-9]|$)' THEN 1
                            ELSE 0
                        END DESC,
                        class_name ASC,
                        name ASC,
                        id ASC"
                )->fetchAll();

                ok(array_map('ms', $rows));
            }

            if ($key === 'books') {
                $rows = $pdo->query(
                    'SELECT * FROM books ORDER BY code,id'
                )->fetchAll();

                ok(array_map('mb', $rows));
            }

            if ($key === 'borrowings') {
                admin();

                $rows = $pdo->query(
                    'SELECT * FROM borrowings '
                    . 'ORDER BY borrowed_at DESC,id DESC'
                )->fetchAll();

                ok(array_map('mt', $rows));
            }

            if ($key === 'ebooks') {
                $rows = $pdo->query(
                    "SELECT * FROM ebooks
                     ORDER BY status='Aktif' DESC, title ASC, id DESC"
                )->fetchAll();

                ok(array_map('meb', $rows));
            }

            if ($key === 'ebook_my_history') {
                $sid = ebook_student();

                $q = $pdo->prepare(
                    'SELECT * FROM ebook_reading_history WHERE student_id=? ORDER BY last_read_at DESC'
                );
                $q->execute([$sid]);

                ok($q->fetchAll());
            }

            if ($key === 'ebook_submissions') {
                admin();
                $rows = $pdo->query(
                    "SELECT sub.*, s.name student_name, s.nis, s.rfid_uid, a.name reviewer_name "
                    . "FROM ebook_submissions sub "
                    . "JOIN students s ON s.id=sub.student_id "
                    . "LEFT JOIN admins a ON a.id=sub.reviewed_by "
                    . "ORDER BY FIELD(sub.status,'Menunggu','Disetujui','Ditolak'), sub.created_at DESC"
                )->fetchAll();
                ok($rows);
            }

            if ($key === 'ebook_history') {
                admin();

                $rows = $pdo->query(
                    'SELECT h.*, s.name student_name, s.nis, '
                    . 'e.title ebook_title '
                    . 'FROM ebook_reading_history h '
                    . 'LEFT JOIN students s ON s.id=h.student_id '
                    . 'LEFT JOIN ebooks e ON e.id=h.ebook_id '
                    . 'ORDER BY h.last_read_at DESC,h.id DESC'
                )->fetchAll();

                ok($rows);
            }

            if ($key === 'ebook_downloads') {
                admin();

                $rows = $pdo->query(
                    'SELECT d.*, s.name student_name, s.nis, '
                    . 'e.title ebook_title, e.code ebook_code '
                    . 'FROM ebook_downloads d '
                    . 'LEFT JOIN students s ON s.id=d.student_id '
                    . 'LEFT JOIN ebooks e ON e.id=d.ebook_id '
                    . 'ORDER BY d.downloaded_at DESC,d.id DESC'
                )->fetchAll();

                ok($rows);
            }

            if ($key === 'settings') {
                $r = $pdo->query(
                    'SELECT * FROM settings WHERE id=1'
                )->fetch();

                ok([
                    'maxPersonalCopies' => (int) $r['max_personal_copies'],
                    'maxClassCopies' => (int) $r['max_class_copies'],
                    'libraryName' => $r['library_name'],
                    'adminName' => $r['admin_name']
                ]);
            }

            fail('Data tidak ditemukan.', 404);

        case 'find_rfid':
            $uid = trim((string) ($_GET['uid'] ?? ''));

            if (!$uid) {
                fail('UID RFID kosong.');
            }

            if (!preg_match('/^\d{10}$/', $uid)) {
                fail('UID RFID harus tepat 10 digit angka.');
            }

            $q = $pdo->prepare(
                "SELECT * FROM students
                 WHERE UPPER(TRIM(rfid_uid))
                 = UPPER(TRIM(?))
                 AND status='Aktif'
                 LIMIT 1"
            );

            $q->execute([$uid]);
            $r = $q->fetch();

            ok($r ? ms($r) : null);

        case 'active_borrowings':
            $sid = (int) ($_GET['student_id'] ?? 0);

            if ($sid < 1) {
                fail('ID anggota tidak valid.');
            }

            $q = $pdo->prepare(
                "SELECT * FROM borrowings
                 WHERE student_id=?
                 AND status='Dipinjam'
                 ORDER BY borrowed_at DESC,id DESC"
            );

            $q->execute([$sid]);

            ok(array_map('mt', $q->fetchAll()));

        case 'promotion_preview':
            admin();

            $rows = $pdo->query(
                "SELECT id, class_name, status FROM students WHERE status='Aktif' ORDER BY id"
            )->fetchAll();

            $counts = [
                'grade10' => 0,
                'grade11' => 0,
                'grade12' => 0,
                'other' => 0,
                'activeBorrowingsGrade12' => 0
            ];

            $grade12Ids = [];
            foreach ($rows as $r) {
                $class = trim((string) $r['class_name']);
                if (preg_match('/^(?:Kelas\\s*)?10(?:\\s+KU-Binar)?$/i', $class)) {
                    $counts['grade10']++;
                } elseif (preg_match('/^(?:Kelas\\s*)?11(?:\\s+KU-Binar)?$/i', $class)) {
                    $counts['grade11']++;
                } elseif (preg_match('/^(?:Kelas\\s*)?12(?:\\s+KU-Binar)?$/i', $class)) {
                    $counts['grade12']++;
                    $grade12Ids[] = (int) $r['id'];
                } else {
                    $counts['other']++;
                }
            }

            if ($grade12Ids) {
                $placeholders = implode(',', array_fill(0, count($grade12Ids), '?'));
                $q = $pdo->prepare(
                    "SELECT COUNT(*) FROM borrowings WHERE student_id IN ($placeholders) AND status='Dipinjam'"
                );
                $q->execute($grade12Ids);
                $counts['activeBorrowingsGrade12'] = (int) $q->fetchColumn();
            }

            ok($counts);

        case 'promote_students':
            admin();

            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $academicYear = trim((string) ($in['academic_year'] ?? ''));
            if (!preg_match('/^\\d{4}\\/\\d{4}$/', $academicYear)) {
                fail('Tahun ajaran harus menggunakan format YYYY/YYYY.');
            }

            // Audit table is created by the merged database schema.
            $checkLog = $pdo->prepare(
                'SELECT id, processed_at FROM promotion_logs WHERE academic_year=? LIMIT 1'
            );
            $checkLog->execute([$academicYear]);
            if ($log = $checkLog->fetch()) {
                fail(
                    'Kenaikan kelas untuk tahun ajaran ' . $academicYear .
                    ' sudah pernah diproses pada ' . $log['processed_at'] . '.'
                );
            }

            $pdo->beginTransaction();

            try {
                $active12 = $pdo->query(
                    "SELECT id, name, class_name FROM students
                     WHERE status='Aktif'
                     AND (class_name REGEXP '^(Kelas[[:space:]]*)?12([[:space:]]+KU-Binar)?$')
                     ORDER BY id"
                )->fetchAll();

                $blocked = [];
                if ($active12) {
                    $ids = array_map(static fn($r) => (int) $r['id'], $active12);
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                    $q = $pdo->prepare(
                        "SELECT DISTINCT s.name FROM borrowings b
                         INNER JOIN students s ON s.id=b.student_id
                         WHERE b.student_id IN ($placeholders) AND b.status='Dipinjam'
                         ORDER BY s.name"
                    );
                    $q->execute($ids);
                    $blocked = $q->fetchAll(PDO::FETCH_COLUMN);
                }

                if ($blocked) {
                    throw new RuntimeException(
                        'Proses dibatalkan. ' . count($blocked) .
                        ' siswa kelas 12 masih memiliki buku yang dipinjam: ' .
                        implode(', ', array_slice($blocked, 0, 5)) .
                        (count($blocked) > 5 ? ', dan lainnya.' : '.')
                    );
                }

                $graduate = $pdo->prepare(
                    "UPDATE students SET status='Nonaktif', rfid_uid=NULL, updated_at=CURRENT_TIMESTAMP
                     WHERE status='Aktif'
                     AND class_name REGEXP '^(Kelas[[:space:]]*)?12([[:space:]]+KU-Binar)?$'"
                );
                $graduate->execute();
                $graduated = $graduate->rowCount();

                $to12 = $pdo->prepare(
                    "UPDATE students SET class_name=CASE
                        WHEN class_name REGEXP '^Kelas[[:space:]]*11$' THEN 'Kelas 12'
                        WHEN class_name REGEXP '^11[[:space:]]+KU-Binar$' THEN '12 KU-Binar'
                        ELSE class_name
                     END,
                     updated_at=CURRENT_TIMESTAMP
                     WHERE status='Aktif'
                     AND class_name REGEXP '^(Kelas[[:space:]]*)?11([[:space:]]+KU-Binar)?$'"
                );
                $to12->execute();
                $promoted11 = $to12->rowCount();

                $to11 = $pdo->prepare(
                    "UPDATE students SET class_name=CASE
                        WHEN class_name REGEXP '^Kelas[[:space:]]*10$' THEN 'Kelas 11'
                        WHEN class_name REGEXP '^10[[:space:]]+KU-Binar$' THEN '11 KU-Binar'
                        ELSE class_name
                     END,
                     updated_at=CURRENT_TIMESTAMP
                     WHERE status='Aktif'
                     AND class_name REGEXP '^(Kelas[[:space:]]*)?10([[:space:]]+KU-Binar)?$'"
                );
                $to11->execute();
                $promoted10 = $to11->rowCount();

                $log = $pdo->prepare(
                    'INSERT INTO promotion_logs(academic_year,graduated_count,promoted_11_to_12,promoted_10_to_11,processed_by)
                     VALUES(?,?,?,?,?)'
                );
                $log->execute([
                    $academicYear,
                    $graduated,
                    $promoted11,
                    $promoted10,
                    !empty($_SESSION['bls_admin_id']) ? (int) $_SESSION['bls_admin_id'] : null
                ]);

                $pdo->commit();

                ok([
                    'academicYear' => $academicYear,
                    'graduated' => $graduated,
                    'promoted11' => $promoted11,
                    'promoted10' => $promoted10
                ]);
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                fail($e->getMessage());
            }


        case 'ebook_login':
            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $uid = preg_replace('/\s+/', '', (string) ($in['uid'] ?? ''));
            if ($uid === '' || !preg_match('/^[0-9]{8,10}$/', $uid)) {
                fail('Masukkan UID RFID yang valid. Jika belum mengetahui UID, minta bantuan petugas perpustakaan.');
            }

            $q = $pdo->prepare(
                "SELECT * FROM students WHERE rfid_uid=? AND status='Aktif' LIMIT 1"
            );
            $q->execute([$uid]);
            $s = $q->fetch();
            if (!$s) {
                fail('UID RFID belum terdaftar atau siswa tidak aktif. Silakan minta bantuan petugas perpustakaan.', 401);
            }

            session_regenerate_id(true);
            $_SESSION['bls_ebook_student_id'] = (int) $s['id'];
            ok(ms($s));

        case 'ebook_logout':
            unset($_SESSION['bls_ebook_student_id']);
            ok();

        case 'ebook_me':
            $sid = (int) ($_SESSION['bls_ebook_student_id'] ?? 0);
            if ($sid < 1) {
                ok(null);
            }

            $q = $pdo->prepare(
                "SELECT * FROM students WHERE id=? AND status='Aktif' LIMIT 1"
            );
            $q->execute([$sid]);
            $s = $q->fetch();

            ok($s ? ms($s) : null);

        case 'ebook_submit':
            if ($method !== 'POST') { fail('Method tidak valid.', 405); }
            // PHP mengosongkan $_POST dan $_FILES jika request melebihi post_max_size.
            $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
            $postMax = ini_get('post_max_size');
            $postMaxBytes = 0;
            if (preg_match('/^\s*(\d+)\s*([KMG]?)\s*$/i', (string) $postMax, $m)) {
                $postMaxBytes = (int) $m[1] * match (strtoupper($m[2])) { 'G' => 1024 ** 3, 'M' => 1024 ** 2, 'K' => 1024, default => 1 };
            }
            if ($postMaxBytes > 0 && $contentLength > $postMaxBytes) {
                fail('Total unggahan melebihi batas post_max_size server. Naikkan post_max_size dan upload_max_filesize, lalu restart Apache/PHP.', 413);
            }
            $sid = ebook_student();
            $title = trim((string)($_POST['title'] ?? ''));
            $author = trim((string)($_POST['author'] ?? ''));
            $category = trim((string)($_POST['category'] ?? 'Umum')) ?: 'Umum';
            $description = trim((string)($_POST['description'] ?? ''));
            $year = (int)($_POST['year'] ?? 0);
            if ($year < 1900 || $year > 2100) fail('Tahun terbit harus antara 1900 dan 2100.');
            if ($title === '' || $author === '') fail('Judul dan penulis wajib diisi.');
            if (mb_strlen($title) > 255 || mb_strlen($author) > 150 || mb_strlen($category) > 100) fail('Ada isian yang terlalu panjang.');
            if (empty($_FILES['pdf'])) fail('File PDF tidak diterima server. Periksa upload_max_filesize, post_max_size, dan pilih file PDF kembali.', 400);
            if ((int)($_FILES['pdf']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $uploadError = (int) $_FILES['pdf']['error'];
                if ($uploadError === UPLOAD_ERR_INI_SIZE || $uploadError === UPLOAD_ERR_FORM_SIZE) fail('Ukuran PDF melebihi batas upload server.', 413);
                if ($uploadError === UPLOAD_ERR_NO_TMP_DIR || $uploadError === UPLOAD_ERR_CANT_WRITE) fail('Server tidak dapat menyimpan file sementara. Periksa konfigurasi dan ruang disk.', 500);
                fail('Unggahan PDF gagal. Kode upload: ' . $uploadError, 400);
            }
            $pdfPath = save_upload($_FILES['pdf'], 'pdf', ['application/pdf'], 20 * 1024 * 1024);
            if (!$pdfPath) fail('File PDF wajib dipilih.');
            $size = (int)($_FILES['pdf']['size'] ?? 0);
            $coverPath = null;
            if (!empty($_FILES['cover']['tmp_name']) && (int)($_FILES['cover']['error'] ?? 1) === UPLOAD_ERR_OK) {
                $coverPath = save_upload($_FILES['cover'], 'covers', ['image/jpeg'], 2 * 1024 * 1024);
            }
            $q = $pdo->prepare("INSERT INTO ebook_submissions(student_id,title,author,category,description,publication_year,pdf_path,cover_path,file_size,status) VALUES(?,?,?,?,?,?,?,?,?,'Menunggu')");
            $q->execute([$sid,$title,$author,$category,$description ?: null,$year,$pdfPath,$coverPath,$size]);
            ok(['id'=>(int)$pdo->lastInsertId(),'status'=>'Menunggu']);

        case 'moderate_ebook_submission':
            admin();
            if ($method !== 'POST') fail('Method tidak valid.', 405);
            $id = (int)($in['id'] ?? 0);
            $decision = (string)($in['decision'] ?? '');
            $note = trim((string)($in['note'] ?? ''));
            if (!in_array($decision, ['approve','reject'], true)) fail('Keputusan tidak valid.');
            $q = $pdo->prepare('SELECT * FROM ebook_submissions WHERE id=? LIMIT 1');
            $q->execute([$id]); $sub = $q->fetch();
            if (!$sub) fail('Pengajuan E-Book tidak ditemukan.', 404);
            if ($sub['status'] !== 'Menunggu') fail('Pengajuan ini sudah ditinjau.');
            if ($decision === 'reject') {
                $q = $pdo->prepare("UPDATE ebook_submissions SET status='Ditolak',review_note=?,reviewed_by=?,reviewed_at=NOW() WHERE id=?");
                $q->execute([$note ?: 'Konten belum memenuhi ketentuan perpustakaan.', (int)$_SESSION['bls_admin_id'], $id]);
                ok(['status'=>'Ditolak']);
            }
            $code = 'SIS-' . date('ymd') . '-' . str_pad((string)$id, 5, '0', STR_PAD_LEFT);
            $q = $pdo->prepare("INSERT INTO ebooks(code,title,author,category,description,publication_year,pdf_path,cover_path,file_size,status) VALUES(?,?,?,?,?,?,?,?,?,'Aktif')");
            $q->execute([$code,$sub['title'],$sub['author'],$sub['category'],$sub['description'],$sub['publication_year'],$sub['pdf_path'],$sub['cover_path'],$sub['file_size']]);
            $ebookId = (int)$pdo->lastInsertId();
            $q = $pdo->prepare("UPDATE ebook_submissions SET status='Disetujui',review_note=?,reviewed_by=?,reviewed_at=NOW(),approved_ebook_id=? WHERE id=?");
            $q->execute([$note ?: null, (int)$_SESSION['bls_admin_id'], $ebookId, $id]);
            ok(['status'=>'Disetujui','ebookId'=>$ebookId]);

        case 'ebook_start':
            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $sid = ebook_student();
            $eid = (int) ($in['ebookId'] ?? 0);

            $q = $pdo->prepare(
                "SELECT * FROM ebooks WHERE id=? AND status='Aktif' LIMIT 1"
            );
            $q->execute([$eid]);
            $ebook = $q->fetch();

            if (!$ebook) {
                fail('E-Book tidak ditemukan.', 404);
            }

            $q = $pdo->prepare(
                'SELECT * FROM ebook_reading_history WHERE student_id=? AND ebook_id=? LIMIT 1'
            );
            $q->execute([$sid, $eid]);
            $history = $q->fetch();

            if ($history) {
                $q = $pdo->prepare(
                    'UPDATE ebook_reading_history SET '
                    . 'last_read_at=NOW(),session_count=session_count+1 '
                    . 'WHERE id=?'
                );
                $q->execute([(int) $history['id']]);
            } else {
                $q = $pdo->prepare(
                    'INSERT INTO ebook_reading_history('
                    . 'student_id,ebook_id,first_read_at,last_read_at,'
                    . 'last_page,total_pages,progress_percent,duration_seconds,'
                    . 'session_count,completed_at'
                    . ') VALUES(?,?,NOW(),NOW(),1,?,?,?,1,NULL)'
                );
                $q->execute([
                    $sid,
                    $eid,
                    $ebook['page_count'] !== null ? (int) $ebook['page_count'] : null,
                    0,
                    0
                ]);
            }

            $q = $pdo->prepare(
                'SELECT * FROM ebook_reading_history WHERE student_id=? AND ebook_id=? LIMIT 1'
            );
            $q->execute([$sid, $eid]);

            ok($q->fetch());

        case 'ebook_progress':
            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $sid = ebook_student();
            $eid = (int) ($in['ebookId'] ?? 0);
            $page = max(1, (int) ($in['lastPage'] ?? 1));
            $total = max(0, (int) ($in['totalPages'] ?? 0));
            $duration = max(0, (int) ($in['durationSeconds'] ?? 0));
            $percent = $total > 0
                ? min(100, round(($page / $total) * 100, 2))
                : min(100, max(0, (float) ($in['progressPercent'] ?? 0)));

            $completed = $percent >= 99.5 ? date('Y-m-d H:i:s') : null;

            $q = $pdo->prepare(
                'UPDATE ebook_reading_history SET '
                . 'last_read_at=NOW(),last_page=?,total_pages=?, '
                . 'progress_percent=?,duration_seconds=?,completed_at=COALESCE(?,completed_at) '
                . 'WHERE student_id=? AND ebook_id=?'
            );
            $q->execute([
                $page,
                $total ?: null,
                $percent,
                $duration,
                $completed,
                $sid,
                $eid
            ]);

            ok();

        case 'report_data':
            admin();

            $period = (string) ($_GET['period'] ?? 'this_month');
            $today = new DateTime('today');
            $start = clone $today;
            $end = new DateTime('tomorrow');

            if ($period === 'today') {
                $start = clone $today;
            } elseif ($period === 'last_7_days') {
                $start->modify('-6 days');
            } elseif ($period === 'this_week') {
                $start->modify('monday this week');
            } elseif ($period === 'last_month') {
                $start->modify('first day of last month');
                $end = new DateTime('first day of this month');
            } elseif ($period === 'this_year') {
                $start->setDate((int) $today->format('Y'), 1, 1);
            } elseif ($period === 'custom') {
                $from = trim((string) ($_GET['from'] ?? ''));
                $to = trim((string) ($_GET['to'] ?? ''));

                if ($from !== '') {
                    $start = new DateTime($from . ' 00:00:00');
                }

                if ($to !== '') {
                    $end = new DateTime($to . ' 00:00:00');
                    $end->modify('+1 day');
                }
            }

            $fromSql = $start->format('Y-m-d H:i:s');
            $toSql = $end->format('Y-m-d H:i:s');

            $students = (int) $pdo->query(
                "SELECT COUNT(*) FROM students WHERE status='Aktif'"
            )->fetchColumn();

            $books = (int) $pdo->query(
                "SELECT COUNT(*) FROM books"
            )->fetchColumn();

            $available = (int) $pdo->query(
                'SELECT COALESCE(SUM(stock),0) FROM books'
            )->fetchColumn();

            $activeBorrowed = (int) $pdo->query(
                "SELECT COALESCE(SUM(quantity),0) FROM borrowings WHERE status='Dipinjam'"
            )->fetchColumn();

            $q = $pdo->prepare(
                'SELECT COUNT(*) FROM borrowings WHERE borrowed_at>=? AND borrowed_at<?'
            );
            $q->execute([$fromSql, $toSql]);
            $borrowings = (int) $q->fetchColumn();

            $q = $pdo->prepare(
                'SELECT COUNT(*) FROM borrowings WHERE returned_at>=? AND returned_at<?'
            );
            $q->execute([$fromSql, $toSql]);
            $returns = (int) $q->fetchColumn();

            $q = $pdo->prepare(
                'SELECT COUNT(DISTINCT student_id) FROM ebook_reading_history WHERE last_read_at>=? AND last_read_at<?'
            );
            $q->execute([$fromSql, $toSql]);
            $ebookReaders = (int) $q->fetchColumn();

            $q = $pdo->prepare(
                'SELECT COUNT(*) FROM ebook_downloads WHERE downloaded_at>=? AND downloaded_at<?'
            );
            $q->execute([$fromSql, $toSql]);
            $ebookDownloads = (int) $q->fetchColumn();

            $q = $pdo->prepare(
                "SELECT COUNT(DISTINCT student_id) FROM (
                    SELECT student_id FROM borrowings WHERE borrowed_at>=? AND borrowed_at<?
                    UNION
                    SELECT student_id FROM borrowings WHERE returned_at>=? AND returned_at<?
                    UNION
                    SELECT student_id FROM ebook_reading_history WHERE last_read_at>=? AND last_read_at<?
                    UNION
                    SELECT student_id FROM ebook_downloads WHERE downloaded_at>=? AND downloaded_at<?
                ) active_students"
            );
            $q->execute([
                $fromSql, $toSql,
                $fromSql, $toSql,
                $fromSql, $toSql,
                $fromSql, $toSql
            ]);
            $activeUsers = (int) $q->fetchColumn();

            $q = $pdo->prepare(
                'SELECT b.id,b.title,b.author,COALESCE(SUM(br.quantity),0) total '
                . 'FROM books b LEFT JOIN borrowings br ON br.book_id=b.id '
                . 'AND br.borrowed_at>=? AND br.borrowed_at<? '
                . 'GROUP BY b.id,b.title,b.author '
                . 'ORDER BY total DESC,b.title ASC LIMIT 10'
            );
            $q->execute([$fromSql, $toSql]);
            $topBooks = $q->fetchAll();

            $q = $pdo->prepare(
                'SELECT e.id,e.title,e.author,COUNT(h.id) readers, '
                . 'COALESCE(SUM(h.duration_seconds),0) duration '
                . 'FROM ebooks e LEFT JOIN ebook_reading_history h ON h.ebook_id=e.id '
                . 'AND h.last_read_at>=? AND h.last_read_at<? '
                . "WHERE e.status='Aktif' GROUP BY e.id,e.title,e.author "
                . 'ORDER BY readers DESC,e.title ASC LIMIT 10'
            );
            $q->execute([$fromSql, $toSql]);
            $topEbooks = $q->fetchAll();

            $q = $pdo->prepare(
                'SELECT e.id,e.title,COUNT(d.id) downloads '
                . 'FROM ebooks e LEFT JOIN ebook_downloads d ON d.ebook_id=e.id '
                . 'AND d.downloaded_at>=? AND d.downloaded_at<? '
                . "WHERE e.status='Aktif' GROUP BY e.id,e.title "
                . 'ORDER BY downloads DESC,e.title ASC LIMIT 10'
            );
            $q->execute([$fromSql, $toSql]);
            $topDownloads = $q->fetchAll();

            $q = $pdo->prepare(
                'SELECT DATE(borrowed_at) day,COUNT(*) total FROM borrowings '
                . 'WHERE borrowed_at>=? AND borrowed_at<? GROUP BY DATE(borrowed_at) ORDER BY day'
            );
            $q->execute([$fromSql, $toSql]);
            $borrowDaily = $q->fetchAll();

            $q = $pdo->prepare(
                'SELECT day,COUNT(DISTINCT student_id) total FROM ('
                . 'SELECT DATE(borrowed_at) day,student_id FROM borrowings WHERE borrowed_at>=? AND borrowed_at<? '
                . 'UNION ALL SELECT DATE(returned_at) day,student_id FROM borrowings WHERE returned_at>=? AND returned_at<? '
                . 'UNION ALL SELECT DATE(last_read_at) day,student_id FROM ebook_reading_history WHERE last_read_at>=? AND last_read_at<? '
                . 'UNION ALL SELECT DATE(downloaded_at) day,student_id FROM ebook_downloads WHERE downloaded_at>=? AND downloaded_at<?'
                . ') activity GROUP BY day ORDER BY day'
            );
            $q->execute([
                $fromSql,$toSql,
                $fromSql,$toSql,
                $fromSql,$toSql,
                $fromSql,$toSql
            ]);
            $activeDaily = $q->fetchAll();

            $q = $pdo->prepare(
                'SELECT DATE(last_read_at) day,COUNT(*) total FROM ebook_reading_history '
                . 'WHERE last_read_at>=? AND last_read_at<? GROUP BY DATE(last_read_at) ORDER BY day'
            );
            $q->execute([$fromSql, $toSql]);
            $readDaily = $q->fetchAll();

            ok([
                'period' => [
                    'from' => $fromSql,
                    'to' => $toSql
                ],
                'summary' => [
                    'students' => $students,
                    'books' => $books,
                    'available' => $available,
                    'activeBorrowed' => $activeBorrowed,
                    'borrowings' => $borrowings,
                    'returns' => $returns,
                    'ebookReaders' => $ebookReaders,
                    'ebookDownloads' => $ebookDownloads,
                    'activeUsers' => $activeUsers
                ],
                'topBooks' => $topBooks,
                'topEbooks' => $topEbooks,
                'topDownloads' => $topDownloads,
                'borrowDaily' => $borrowDaily,
                'readDaily' => $readDaily,
                'activeDaily' => $activeDaily
            ]);

        case 'save_ebook':
            admin();

            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $id = (int) ($_POST['id'] ?? 0);
            $code = trim((string) ($_POST['code'] ?? ''));
            $title = trim((string) ($_POST['title'] ?? ''));
            $author = trim((string) ($_POST['author'] ?? ''));
            $publisher = trim((string) ($_POST['publisher'] ?? ''));
            $year = trim((string) ($_POST['year'] ?? ''));
            $isbn = trim((string) ($_POST['isbn'] ?? ''));
            $category = trim((string) ($_POST['category'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $pageCount = trim((string) ($_POST['pageCount'] ?? ''));
            $status = ($_POST['status'] ?? 'Aktif') === 'Nonaktif' ? 'Nonaktif' : 'Aktif';

            if ($code === '' || $title === '' || $author === '') {
                fail('Kode, judul, dan penulis wajib diisi.');
            }

            $q = $pdo->prepare(
                'SELECT id FROM ebooks WHERE LOWER(code)=LOWER(?) AND id<>? LIMIT 1'
            );
            $q->execute([$code, $id]);
            if ($q->fetch()) {
                fail('Kode E-Book sudah digunakan.');
            }

            $q = $id
                ? $pdo->prepare('SELECT * FROM ebooks WHERE id=? LIMIT 1')
                : null;
            if ($q) {
                $q->execute([$id]);
                $old = $q->fetch();
                if (!$old) {
                    fail('E-Book tidak ditemukan.', 404);
                }
            } else {
                $old = null;
            }

            $coverPath = save_upload(
                $_FILES['cover'] ?? [],
                'covers',
                ['image/jpeg', 'image/png', 'image/webp'],
                2 * 1024 * 1024
            );

            $pdfPath = save_upload(
                $_FILES['pdf'] ?? [],
                'pdf',
                ['application/pdf'],
                50 * 1024 * 1024
            );

            $coverPath = $coverPath ?: ($old['cover_path'] ?? null);
            $pdfPath = $pdfPath ?: ($old['pdf_path'] ?? null);
            $fileSize = $pdfPath && is_file(__DIR__ . '/' . $pdfPath)
                ? filesize(__DIR__ . '/' . $pdfPath)
                : (int) ($old['file_size'] ?? 0);

            if (!$pdfPath) {
                fail('File PDF wajib diunggah.');
            }

            if ($id) {
                $q = $pdo->prepare(
                    'UPDATE ebooks SET code=?,title=?,author=?,publisher=?,publication_year=?,isbn=?,category=?,description=?,cover_path=?,pdf_path=?,file_size=?,page_count=?,status=? WHERE id=?'
                );
                $q->execute([
                    $code,
                    $title,
                    $author,
                    $publisher ?: null,
                    $year !== '' ? (int) $year : null,
                    $isbn ?: null,
                    $category ?: null,
                    $description ?: null,
                    $coverPath,
                    $pdfPath,
                    $fileSize,
                    $pageCount !== '' ? max(1, (int) $pageCount) : null,
                    $status,
                    $id
                ]);
            } else {
                $q = $pdo->prepare(
                    'INSERT INTO ebooks(code,title,author,publisher,publication_year,isbn,category,description,cover_path,pdf_path,file_size,page_count,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)'
                );
                $q->execute([
                    $code,
                    $title,
                    $author,
                    $publisher ?: null,
                    $year !== '' ? (int) $year : null,
                    $isbn ?: null,
                    $category ?: null,
                    $description ?: null,
                    $coverPath,
                    $pdfPath,
                    $fileSize,
                    $pageCount !== '' ? max(1, (int) $pageCount) : null,
                    $status
                ]);
                $id = (int) $pdo->lastInsertId();
            }

            ok(['id' => $id]);

        case 'delete_ebook':
            admin();

            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $id = (int) ($in['id'] ?? 0);
            $status = ($in['status'] ?? 'Nonaktif') === 'Aktif' ? 'Aktif' : 'Nonaktif';

            $q = $pdo->prepare('UPDATE ebooks SET status=? WHERE id=?');
            $q->execute([$status, $id]);

            ok();

        case 'save_book':
            admin();

            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $id = (int) ($_POST['id'] ?? 0);
            $code = trim((string) ($_POST['code'] ?? ''));
            $title = trim((string) ($_POST['title'] ?? ''));
            $author = trim((string) ($_POST['author'] ?? ''));
            $category = trim((string) ($_POST['category'] ?? '')) ?: 'Umum';
            $year = trim((string) ($_POST['year'] ?? ''));
            $stock = max(0, (int) ($_POST['stock'] ?? 0));

            if ($code === '' || $title === '' || $author === '') {
                fail('Kode, judul, dan penulis wajib diisi.');
            }

            $imageData = null;
            if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $file = $_FILES['image'];
                if (($file['size'] ?? 0) > 5 * 1024 * 1024) fail('Ukuran gambar buku maksimal 5 MB.');
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
                if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) fail('Format gambar tidak didukung.');
                $raw = file_get_contents($file['tmp_name']);
                $imageData = 'data:' . $mime . ';base64,' . base64_encode($raw);
            }

            if ($id) {
                $q = $pdo->prepare('SELECT image_data FROM books WHERE id=? LIMIT 1');
                $q->execute([$id]);
                $old = $q->fetch();
                if (!$old) fail('Data buku tidak ditemukan.', 404);
                if ($imageData === null) $imageData = $old['image_data'];

                $q = $pdo->prepare('UPDATE books SET code=?,title=?,author=?,category=?,publication_year=?,stock=?,image_data=? WHERE id=?');
                $q->execute([$code,$title,$author,$category,$year !== '' ? (int)$year : null,$stock,$imageData,$id]);
            } else {
                $q = $pdo->prepare('INSERT INTO books(code,title,author,category,publication_year,stock,image_data) VALUES(?,?,?,?,?,?,?)');
                $q->execute([$code,$title,$author,$category,$year !== '' ? (int)$year : null,$stock,$imageData]);
                $id = (int) $pdo->lastInsertId();
            }

            ok(['id' => $id]);

        case 'save_key':
            admin();

            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $key = (string) ($in['key'] ?? '');
            $v = $in['value'] ?? null;

            if ($key === 'students') {

                if (!is_array($v)) {
                    fail('Format data siswa tidak valid.');
                }

                $pdo->beginTransaction();

                try {
                    // Validate unique fields first so edits return a useful
                    // message instead of a generic database/server error.
                    $checkNis = $pdo->prepare(
                        'SELECT id FROM students WHERE nis=? AND id<>? LIMIT 1'
                    );
                    $checkLibrary = $pdo->prepare(
                        'SELECT id FROM students WHERE library_id=? AND id<>? LIMIT 1'
                    );
                    $checkRfid = $pdo->prepare(
                        'SELECT id FROM students WHERE rfid_uid=? AND id<>? LIMIT 1'
                    );

                    $q = $pdo->prepare(
                        'INSERT INTO students('
                        . 'id,library_id,nis,name,class_name,rfid_uid,status'
                        . ') VALUES(?,?,?,?,?,?,?) '
                        . 'ON DUPLICATE KEY UPDATE '
                        . 'library_id=VALUES(library_id),'
                        . 'nis=VALUES(nis),'
                        . 'name=VALUES(name),'
                        . 'class_name=VALUES(class_name),'
                        . 'rfid_uid=VALUES(rfid_uid),'
                        . 'status=VALUES(status)'
                    );

                    foreach ($v as $s) {
                        $id = (int) ($s['id'] ?? 0);
                        $libraryId = trim((string) ($s['libraryId'] ?? ''));
                        $nis = trim((string) ($s['nis'] ?? ''));
                        $name = trim((string) ($s['name'] ?? ''));
                        $class = trim((string) ($s['class'] ?? ''));
                        $rfidRaw = trim((string) ($s['rfid'] ?? ''));
                        $rfid = $rfidRaw !== '' ? $rfidRaw : null;

                        if ($libraryId === '' || $nis === '' || $name === '' || $class === '') {
                            throw new RuntimeException('Data siswa belum lengkap.');
                        }

                        $checkNis->execute([$nis, $id]);
                        if ($checkNis->fetchColumn()) {
                            throw new RuntimeException('NIS sudah digunakan oleh siswa lain.');
                        }

                        $checkLibrary->execute([$libraryId, $id]);
                        if ($checkLibrary->fetchColumn()) {
                            throw new RuntimeException('ID anggota perpustakaan sudah digunakan oleh siswa lain.');
                        }

                        if ($rfid !== null) {
                            // New/changed RFID must be exactly 10 digits.
                            // Legacy RFID values already stored in the database may be shorter;
                            // allow them to pass through an existing bulk save so adding/editing
                            // another student is not blocked by old data. A legacy value must match
                            // the value already stored for the same student.
                            $validNewRfid = preg_match('/^\d{10}$/', $rfid) === 1;

                            if (!$validNewRfid) {
                                $legacyOk = false;
                                if ($id > 0) {
                                    $currentRfidStmt = $pdo->prepare(
                                        'SELECT rfid_uid FROM students WHERE id=? LIMIT 1'
                                    );
                                    $currentRfidStmt->execute([$id]);
                                    $currentRfid = $currentRfidStmt->fetchColumn();
                                    $legacyOk = $currentRfid !== false
                                        && trim((string) $currentRfid) === $rfid;
                                }

                                if (!$legacyOk) {
                                    throw new RuntimeException(
                                        'RFID baru harus tepat 10 digit angka. UID yang diterima: '
                                        . $rfid . ' (' . strlen($rfid) . ' digit).'
                                    );
                                }
                            }

                            $checkRfid->execute([$rfid, $id]);
                            if ($checkRfid->fetchColumn()) {
                                throw new RuntimeException('RFID sudah digunakan oleh siswa lain.');
                            }
                        }

                        $q->execute([
                            $id > 0 ? $id : null,
                            $libraryId,
                            $nis,
                            $name,
                            $class,
                            $rfid,
                            (string) ($s['status'] ?? 'Aktif')
                        ]);
                    }

                    $pdo->commit();
                } catch (Throwable $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    fail($e->getMessage());
                }

                ok();
            }

            if ($key === 'books') {

                if (!is_array($v)) {
                    fail('Format data buku tidak valid.');
                }

                $pdo->beginTransaction();

                $q = $pdo->prepare(
                    'INSERT INTO books('
                    . 'id,code,title,author,category,publication_year,stock,image_data'
                    . ') VALUES(?,?,?,?,?,?,?,?) '
                    . 'ON DUPLICATE KEY UPDATE '
                    . 'code=VALUES(code),'
                    . 'title=VALUES(title),'
                    . 'author=VALUES(author),'
                    . 'category=VALUES(category),'
                    . 'publication_year=VALUES(publication_year),'
                    . 'stock=VALUES(stock),'
                    . 'image_data=VALUES(image_data)'
                );

                foreach ($v as $b) {
                    $q->execute([
                        $b['id'] ?? null,
                        trim((string) ($b['code'] ?? '')),
                        trim((string) ($b['title'] ?? '')),
                        trim((string) ($b['author'] ?? '')),
                        trim((string) ($b['category'] ?? '')) ?: null,
                        ($b['year'] ?? '') !== ''
                            ? (int) $b['year']
                            : null,
                        max(
                            0,
                            (int) ($b['stock'] ?? 0)
                        ),
                        $b['imageData'] ?? null
                    ]);
                }

                $pdo->commit();

                ok();
            }

            if ($key === 'settings') {

                if (!is_array($v)) {
                    fail('Format pengaturan tidak valid.');
                }

                $q = $pdo->prepare(
                    'UPDATE settings SET '
                    . 'max_personal_copies=?,'
                    . 'max_class_copies=?,'
                    . 'library_name=?,'
                    . 'admin_name=? '
                    . 'WHERE id=1'
                );

                $q->execute([
                    max(
                        1,
                        (int) ($v['maxPersonalCopies'] ?? 1)
                    ),
                    max(
                        1,
                        (int) ($v['maxClassCopies'] ?? 30)
                    ),
                    trim(
                        (string) ($v['libraryName'] ?? 'BinaLib')
                    ) ?: 'BinaLib',
                    trim(
                        (string) ($v['adminName'] ?? 'Admin')
                    ) ?: 'Admin'
                ]);

                $_SESSION['bls_admin_name'] =
                    trim(
                        (string) ($v['adminName'] ?? 'Admin')
                    ) ?: 'Admin';

                ok();
            }

            fail('Key tidak didukung.');

        case 'delete':
            admin();

            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $key = (string) ($in['key'] ?? '');
            $id = (int) ($in['id'] ?? 0);

            if ($key === 'students') {

                $q = $pdo->prepare(
                    'SELECT COUNT(*) FROM borrowings '
                    . 'WHERE student_id=?'
                );

                $q->execute([$id]);

                if ((int) $q->fetchColumn() > 0) {
                    fail(
                        'Siswa memiliki riwayat peminjaman dan tidak '
                        . 'dapat dihapus. Nonaktifkan datanya jika '
                        . 'diperlukan.'
                    );
                }

                $q = $pdo->prepare(
                    'DELETE FROM students WHERE id=?'
                );

                $q->execute([$id]);

                ok();
            }

            if ($key === 'books') {

                $q = $pdo->prepare(
                    'SELECT COUNT(*) FROM borrowings '
                    . 'WHERE book_id=? AND status="Dipinjam"'
                );

                $q->execute([$id]);

                if ((int) $q->fetchColumn() > 0) {
                    fail(
                        'Buku masih dipinjam dan tidak dapat dihapus.'
                    );
                }

                $q = $pdo->prepare(
                    'DELETE FROM books WHERE id=?'
                );

                $q->execute([$id]);

                ok();
            }

            fail('Key delete tidak didukung.');

        case 'create_borrowings':
            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $sid = (int) ($in['studentId'] ?? 0);
            $items = is_array($in['items'] ?? null) ? $in['items'] : [];
            $type = ($in['loanType'] ?? '') === 'Kegunaan Kelas'
                ? 'Kegunaan Kelas'
                : 'Penggunaan Pribadi';

            if ($sid < 1 || !$items) {
                fail('Pilih minimal satu buku.');
            }

            if ($type === 'Penggunaan Pribadi' && count($items) > 1) {
                fail('Penggunaan pribadi hanya boleh memilih satu buku.');
            }

            $set = $pdo->query(
                'SELECT * FROM settings WHERE id=1'
            )->fetch();

            $max = $type === 'Kegunaan Kelas'
                ? (int) $set['max_class_copies']
                : (int) $set['max_personal_copies'];

            $totalQty = 0;
            foreach ($items as $item) {
                $totalQty += max(1, (int) ($item['quantity'] ?? 1));
            }

            if ($totalQty > $max) {
                fail("Maksimal {$max} eksemplar untuk jenis peminjaman ini.");
            }

            $pdo->beginTransaction();

            $q = $pdo->prepare(
                "SELECT * FROM students
                 WHERE id=? AND status='Aktif' FOR UPDATE"
            );
            $q->execute([$sid]);
            $student = $q->fetch();

            if (!$student) {
                $pdo->rollBack();
                fail('Anggota tidak ditemukan atau tidak aktif.');
            }

            $created = [];
            $stockQuery = $pdo->prepare(
                'SELECT * FROM books WHERE id=? FOR UPDATE'
            );
            $stockUpdate = $pdo->prepare(
                'UPDATE books SET stock=stock-? WHERE id=?'
            );
            $insert = $pdo->prepare(
                'INSERT INTO borrowings('
                . 'student_id,book_id,quantity,loan_type,class_name,borrowed_at,status'
                . ') VALUES(?,?,?,?,?,NOW(),"Dipinjam")'
            );

            foreach ($items as $item) {
                $bid = (int) ($item['bookId'] ?? 0);
                $qty = max(1, (int) ($item['quantity'] ?? 1));

                $stockQuery->execute([$bid]);
                $book = $stockQuery->fetch();

                if (!$book) {
                    $pdo->rollBack();
                    fail('Salah satu buku tidak ditemukan.');
                }

                if ((int) $book['stock'] < $qty) {
                    $pdo->rollBack();
                    fail('Stok buku "' . $book['title'] . '" tidak mencukupi.');
                }

                $stockUpdate->execute([$qty, $bid]);
                $insert->execute([
                    $sid,
                    $bid,
                    $qty,
                    $type,
                    $student['class_name']
                ]);

                $created[] = (int) $pdo->lastInsertId();
            }

            $pdo->commit();

            ok([
                'ids' => $created,
                'count' => count($created),
                'quantity' => $totalQty
            ]);

        case 'create_borrowing':
            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $sid = (int) ($in['studentId'] ?? 0);
            $bid = (int) ($in['bookId'] ?? 0);
            $qty = max(
                1,
                (int) ($in['quantity'] ?? 1)
            );

            $type = ($in['loanType'] ?? '') === 'Kegunaan Kelas'
                ? 'Kegunaan Kelas'
                : 'Penggunaan Pribadi';

            $set = $pdo->query(
                'SELECT * FROM settings WHERE id=1'
            )->fetch();

            $max = $type === 'Kegunaan Kelas'
                ? (int) $set['max_class_copies']
                : (int) $set['max_personal_copies'];

            if ($qty > $max) {
                fail("Maksimal {$max} eksemplar.");
            }

            $pdo->beginTransaction();

            $q = $pdo->prepare(
                "SELECT * FROM students
                 WHERE id=?
                 AND status='Aktif'
                 FOR UPDATE"
            );

            $q->execute([$sid]);
            $s = $q->fetch();

            if (!$s) {
                $pdo->rollBack();
                fail(
                    'Anggota tidak ditemukan atau tidak aktif.'
                );
            }

            $q = $pdo->prepare(
                'SELECT * FROM books WHERE id=? FOR UPDATE'
            );

            $q->execute([$bid]);
            $b = $q->fetch();

            if (!$b) {
                $pdo->rollBack();
                fail('Buku tidak ditemukan.');
            }

            if ((int) $b['stock'] < $qty) {
                $pdo->rollBack();
                fail('Stok buku tidak mencukupi.');
            }

            $q = $pdo->prepare(
                'UPDATE books SET stock=stock-? WHERE id=?'
            );

            $q->execute([
                $qty,
                $bid
            ]);

            $q = $pdo->prepare(
                'INSERT INTO borrowings('
                . 'student_id,book_id,quantity,loan_type,'
                . 'class_name,borrowed_at,status'
                . ') VALUES(?,?,?,?,?,NOW(),"Dipinjam")'
            );

            $q->execute([
                $sid,
                $bid,
                $qty,
                $type,
                $s['class_name']
            ]);

            $id = (int) $pdo->lastInsertId();

            $pdo->commit();

            ok([
                'id' => $id
            ]);

        case 'return_borrowings':
            if ($method !== 'POST') {
                fail('Method tidak valid.', 405);
            }

            $sid = (int) ($in['studentId'] ?? 0);

            $ids = is_array($in['ids'] ?? null)
                ? array_values(
                    array_filter(
                        array_map(
                            'intval',
                            $in['ids']
                        )
                    )
                )
                : [];

            if ($sid < 1 || !$ids) {
                fail('Pilih minimal satu peminjaman.');
            }

            $pdo->beginTransaction();

            $ph = implode(
                ',',
                array_fill(
                    0,
                    count($ids),
                    '?'
                )
            );

            $q = $pdo->prepare(
                "SELECT * FROM borrowings
                 WHERE student_id=?
                 AND status='Dipinjam'
                 AND id IN($ph)
                 FOR UPDATE"
            );

            $q->execute(
                array_merge(
                    [$sid],
                    $ids
                )
            );

            $rows = $q->fetchAll();

            if (!$rows) {
                $pdo->rollBack();
                fail(
                    'Peminjaman aktif tidak ditemukan.'
                );
            }

            foreach ($rows as $r) {

                $q = $pdo->prepare(
                    'UPDATE borrowings SET '
                    . 'status="Dikembalikan",'
                    . 'returned_at=NOW() '
                    . 'WHERE id=?'
                );

                $q->execute([
                    $r['id']
                ]);

                if ($r['book_id'] !== null) {

                    $q = $pdo->prepare(
                        'UPDATE books SET stock=stock+? '
                        . 'WHERE id=?'
                    );

                    $q->execute([
                        (int) $r['quantity'],
                        (int) $r['book_id']
                    ]);
                }
            }

            $pdo->commit();

            ok([
                'count' => count($rows)
            ]);

        default:
            fail(
                'Action API tidak dikenali.',
                404
            );
    }

} catch (Throwable $e) {

    if (
        isset($pdo) &&
        $pdo->inTransaction()
    ) {
        $pdo->rollBack();
    }

    error_log($e->getMessage());

    fail(
        'Terjadi kesalahan server. Periksa konfigurasi database '
        . 'atau log PHP.',
        500
    );
}