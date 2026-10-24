-- Jalankan setelah memilih database bls_ebook di phpMyAdmin.
-- Query ini hanya memeriksa struktur, tidak mengubah data.

SELECT
    c.COLUMN_NAME AS kolom,
    c.COLUMN_TYPE AS tipe,
    c.IS_NULLABLE AS nullable,
    c.COLUMN_DEFAULT AS nilai_default
FROM information_schema.COLUMNS c
WHERE c.TABLE_SCHEMA = DATABASE()
  AND c.TABLE_NAME = 'ebooks'
ORDER BY c.ORDINAL_POSITION;

-- Daftar kolom yang diperlukan oleh API save_ebook dan mapper meb().
SELECT required.COLUMN_NAME AS kolom_wajib,
       CASE WHEN c.COLUMN_NAME IS NULL THEN 'BELUM ADA' ELSE 'OK' END AS status
FROM (
    SELECT 'id' AS COLUMN_NAME UNION ALL
    SELECT 'code' UNION ALL
    SELECT 'title' UNION ALL
    SELECT 'author' UNION ALL
    SELECT 'publisher' UNION ALL
    SELECT 'publication_year' UNION ALL
    SELECT 'isbn' UNION ALL
    SELECT 'category' UNION ALL
    SELECT 'description' UNION ALL
    SELECT 'cover_path' UNION ALL
    SELECT 'pdf_path' UNION ALL
    SELECT 'file_size' UNION ALL
    SELECT 'page_count' UNION ALL
    SELECT 'status' UNION ALL
    SELECT 'created_at' UNION ALL
    SELECT 'updated_at'
) AS required
LEFT JOIN information_schema.COLUMNS c
  ON c.TABLE_SCHEMA = DATABASE()
 AND c.TABLE_NAME = 'ebooks'
 AND c.COLUMN_NAME = required.COLUMN_NAME
ORDER BY required.COLUMN_NAME;

-- Data E-Book terbaru; jalankan hanya setelah kolom di atas berstatus OK.
SELECT id, code, title, author, publication_year, cover_path, pdf_path, file_size, page_count, status
FROM ebooks
ORDER BY id DESC
LIMIT 20;
