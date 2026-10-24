```php
<?php

require_once __DIR__ . '/config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$sid = (int) ($_SESSION['bls_ebook_student_id'] ?? 0);

if ($sid < 1) {
    http_response_code(401);
    exit('Silakan masuk ke layanan E-Book terlebih dahulu.');
}

$id = (int) ($_GET['id'] ?? 0);

$pdo = db();

$q = $pdo->prepare("SELECT * FROM ebooks WHERE id=? AND status='Aktif' LIMIT 1");
$q->execute([$id]);

$r = $q->fetch();

if (!$r || !$r['pdf_path']) {
    http_response_code(404);
    exit('E-Book tidak ditemukan.');
}

$path = __DIR__ . '/' . ltrim($r['pdf_path'], '/');

if (!is_file($path)) {
    http_response_code(404);
    exit('File PDF tidak ditemukan.');
}

$q = $pdo->prepare(
    'INSERT INTO ebook_downloads(student_id,ebook_id,downloaded_at) VALUES(?,?,NOW())'
);

$q->execute([$sid, $id]);

$name = preg_replace('/[^A-Za-z0-9._-]+/', '-', $r['title']) . '.pdf';

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($path));
header('Content-Disposition: attachment; filename="' . $name . '"');

readfile($path);