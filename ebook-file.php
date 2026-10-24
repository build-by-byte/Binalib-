```php
<?php

require_once __DIR__ . '/config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$pdo = db();
$r = false;

if (isset($_GET['submission'])) {
    if (empty($_SESSION['bls_admin_id'])) {
        http_response_code(403);
        exit('Akses admin diperlukan.');
    }

    $id = (int) $_GET['submission'];

    $q = $pdo->prepare(
        "SELECT pdf_path FROM ebook_submissions WHERE id=? LIMIT 1"
    );

    $q->execute([$id]);
    $r = $q->fetch();

} else {
    if (empty($_SESSION['bls_ebook_student_id'])) {
        http_response_code(401);
        exit('Unauthorized');
    }

    $id = (int) ($_GET['id'] ?? 0);

    $q = $pdo->prepare(
        "SELECT pdf_path FROM ebooks WHERE id=? AND status='Aktif' LIMIT 1"
    );

    $q->execute([$id]);
    $r = $q->fetch();
}

if (!$r || !$r['pdf_path']) {
    http_response_code(404);
    exit('File tidak ditemukan.');
}

$path = __DIR__ . '/' . ltrim($r['pdf_path'], '/');

if (!is_file($path)) {
    http_response_code(404);
    exit('File tidak ditemukan.');
}

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="ebook-' . $id . '.pdf"');

readfile($path);