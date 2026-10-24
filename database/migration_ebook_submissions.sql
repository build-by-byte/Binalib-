-- Jalankan pada database BinaLib yang sudah ada.
-- Tidak menghapus tabel atau data yang sudah tersimpan.
CREATE TABLE IF NOT EXISTS `ebook_submissions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `author` VARCHAR(150) NOT NULL,
    `category` VARCHAR(100) NOT NULL DEFAULT 'Umum',
    `description` TEXT NULL,
    `publication_year` SMALLINT UNSIGNED NOT NULL,
    `cover_path` VARCHAR(255) NULL,
    `pdf_path` VARCHAR(255) NOT NULL,
    `file_size` BIGINT UNSIGNED NOT NULL DEFAULT 0,
    `status` ENUM('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu',
    `review_note` VARCHAR(500) NULL,
    `reviewed_by` INT UNSIGNED NULL,
    `reviewed_at` DATETIME NULL,
    `approved_ebook_id` INT UNSIGNED NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ebook_submissions_status` (`status`),
    INDEX `idx_ebook_submissions_student` (`student_id`),
    CONSTRAINT `fk_ebook_submissions_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ebook_submissions_admin` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kolom metadata untuk tahun terbit dan cover otomatis halaman pertama PDF.
ALTER TABLE `ebook_submissions` ADD COLUMN IF NOT EXISTS `publication_year` SMALLINT UNSIGNED NOT NULL DEFAULT 2026 AFTER `description`;
ALTER TABLE `ebook_submissions` ADD COLUMN IF NOT EXISTS `cover_path` VARCHAR(255) NULL AFTER `publication_year`;
