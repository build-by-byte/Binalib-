-- =========================================================
-- BinaLib Unified Database
-- Database: binalib_ebook
-- Fitur: Buku Fisik + RFID + Kenaikan Kelas + E-Book
-- =========================================================

CREATE DATABASE IF NOT EXISTS `bls_ebook`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `bls_ebook`;

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `ebook_submissions`;
DROP TABLE IF EXISTS `ebook_downloads`;
DROP TABLE IF EXISTS `ebook_reading_history`;
DROP TABLE IF EXISTS `ebooks`;
DROP TABLE IF EXISTS `promotion_logs`;
DROP TABLE IF EXISTS `borrowings`;
DROP TABLE IF EXISTS `books`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `admins`;

SET FOREIGN_KEY_CHECKS=1;

-- =========================================================
-- ADMINS
-- =========================================================
CREATE TABLE `admins` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NOT NULL DEFAULT 'Admin',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `admins` (`username`, `password_hash`, `name`)
VALUES ('admin', '$2y$12$dvjeXUtpKEZ0ni./wB3ywe/uzCbuCtLR/VxnJ3nB.KE8IfHMXSYwC', 'Admin');

-- =========================================================
-- STUDENTS
-- RFID disimpan sebagai string 10 digit agar leading zero tetap aman.
-- =========================================================
CREATE TABLE `students` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `library_id` VARCHAR(20) NOT NULL UNIQUE,
    `nis` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `class_name` VARCHAR(100) NOT NULL,
    `rfid_uid` VARCHAR(10) NULL UNIQUE,
    `status` ENUM('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_students_rfid` (`rfid_uid`),
    INDEX `idx_students_class` (`class_name`)
) ENGINE=InnoDB;

-- =========================================================
-- BUKU FISIK
-- =========================================================
CREATE TABLE `books` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `title` VARCHAR(200) NOT NULL,
    `author` VARCHAR(150) NOT NULL,
    `category` VARCHAR(100) NOT NULL DEFAULT 'Umum',
    `publication_year` SMALLINT UNSIGNED NULL,
    `stock` INT UNSIGNED NOT NULL DEFAULT 0,
    `image_data` LONGTEXT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_books_title` (`title`),
    INDEX `idx_books_category` (`category`),
    INDEX `idx_books_code` (`code`)
) ENGINE=InnoDB;

INSERT INTO `books`
(`id`,`code`,`title`,`author`,`category`,`publication_year`,`stock`)
VALUES
(1,'BK-001','Pemrograman Web Dasar','Budi Raharjo','Buku Pelajaran',2022,5),
(2,'BK-002','Algoritma dan Struktur Data','Rosa A.S.','Buku Pelajaran',2021,4),
(3,'BK-003','Basis Data untuk SMK','Kurniawan','Buku Pelajaran',2020,6),
(4,'BK-004','Jaringan Komputer','Sofana','Sains & Teknologi',2021,4),
(5,'BK-005','Dasar-Dasar Artificial Intelligence','Suyanto','Sains & Teknologi',2023,2),
(6,'BK-006','Sistem Operasi','Abraham Silberschatz','Sains & Teknologi',2019,2),
(7,'BK-007','tes','tes','Umum',2026,999);

-- =========================================================
-- PEMINJAMAN BUKU FISIK
-- =========================================================
CREATE TABLE `borrowings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT UNSIGNED NULL,
    `book_id` INT UNSIGNED NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `loan_type` ENUM('Penggunaan Pribadi','Kegunaan Kelas') NOT NULL DEFAULT 'Penggunaan Pribadi',
    `class_name` VARCHAR(100) NULL,
    `borrowed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `returned_at` DATETIME NULL,
    `status` ENUM('Dipinjam','Dikembalikan') NOT NULL DEFAULT 'Dipinjam',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_borrowings_student_status` (`student_id`,`status`),
    INDEX `idx_borrowings_book_status` (`book_id`,`status`),
    INDEX `idx_borrowings_borrowed_at` (`borrowed_at`),
    CONSTRAINT `fk_borrowings_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_borrowings_book`
        FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- KENAIKAN KELAS / KELULUSAN
-- =========================================================
CREATE TABLE `promotion_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `academic_year` VARCHAR(9) NOT NULL UNIQUE,
    `graduated_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `promoted_11_to_12` INT UNSIGNED NOT NULL DEFAULT 0,
    `promoted_10_to_11` INT UNSIGNED NOT NULL DEFAULT 0,
    `processed_by` INT UNSIGNED NULL,
    `processed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_promotion_logs_processed_at` (`processed_at`)
) ENGINE=InnoDB;

-- =========================================================
-- SETTINGS
-- =========================================================
CREATE TABLE `settings` (
    `id` TINYINT UNSIGNED PRIMARY KEY,
    `max_personal_copies` INT UNSIGNED NOT NULL DEFAULT 1,
    `max_class_copies` INT UNSIGNED NOT NULL DEFAULT 30,
    `library_name` VARCHAR(100) NOT NULL DEFAULT 'BinaLib',
    `admin_name` VARCHAR(100) NOT NULL DEFAULT 'Admin',
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `settings`
(`id`,`max_personal_copies`,`max_class_copies`,`library_name`,`admin_name`)
VALUES (1,1,30,'BinaLib','Admin');

-- =========================================================
-- E-BOOK
-- =========================================================
CREATE TABLE `ebooks` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `author` VARCHAR(150) NOT NULL,
    `publisher` VARCHAR(150) NULL,
    `publication_year` SMALLINT UNSIGNED NULL,
    `isbn` VARCHAR(30) NULL,
    `category` VARCHAR(100) NULL,
    `description` TEXT NULL,
    `cover_path` VARCHAR(255) NULL,
    `pdf_path` VARCHAR(255) NOT NULL,
    `file_size` BIGINT UNSIGNED NOT NULL DEFAULT 0,
    `page_count` INT UNSIGNED NULL,
    `status` ENUM('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ebooks_title` (`title`),
    INDEX `idx_ebooks_category` (`category`),
    INDEX `idx_ebooks_status` (`status`)
) ENGINE=InnoDB;

CREATE TABLE `ebook_reading_history` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT UNSIGNED NOT NULL,
    `ebook_id` INT UNSIGNED NOT NULL,
    `first_read_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_read_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_page` INT UNSIGNED NOT NULL DEFAULT 1,
    `total_pages` INT UNSIGNED NULL,
    `progress_percent` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `duration_seconds` INT UNSIGNED NOT NULL DEFAULT 0,
    `session_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `completed_at` DATETIME NULL,
    UNIQUE KEY `uq_ebook_history_student_ebook` (`student_id`,`ebook_id`),
    INDEX `idx_ebook_history_student` (`student_id`),
    INDEX `idx_ebook_history_ebook` (`ebook_id`),
    INDEX `idx_ebook_history_last_read` (`last_read_at`),
    CONSTRAINT `fk_ebook_history_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ebook_history_ebook`
        FOREIGN KEY (`ebook_id`) REFERENCES `ebooks` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `ebook_downloads` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT UNSIGNED NOT NULL,
    `ebook_id` INT UNSIGNED NOT NULL,
    `downloaded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_ebook_download_student` (`student_id`),
    INDEX `idx_ebook_download_ebook` (`ebook_id`),
    INDEX `idx_ebook_download_date` (`downloaded_at`),
    CONSTRAINT `fk_ebook_download_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ebook_download_ebook`
        FOREIGN KEY (`ebook_id`) REFERENCES `ebooks` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


-- =========================================================
-- PENGAJUAN E-BOOK DARI SISWA (MODERASI ADMIN)
-- =========================================================
CREATE TABLE `ebook_submissions` (
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
) ENGINE=InnoDB;

-- Verifikasi struktur utama
SELECT TABLE_NAME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA='binalib_ebook'
ORDER BY TABLE_NAME;
