-- BinaLib empty database schema (no seed/user data)
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2026 at 01:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = '+07:00';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bls_ebook`
--
CREATE DATABASE IF NOT EXISTS `db_binalib` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_binalib`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT 'Admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(150) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'Umum',
  `publication_year` smallint(5) UNSIGNED DEFAULT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `image_data` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrowings`
--

CREATE TABLE `borrowings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED DEFAULT NULL,
  `book_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `loan_type` enum('Penggunaan Pribadi','Kegunaan Kelas') NOT NULL DEFAULT 'Penggunaan Pribadi',
  `class_name` varchar(100) DEFAULT NULL,
  `borrowed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `returned_at` datetime DEFAULT NULL,
  `status` enum('Dipinjam','Dikembalikan') NOT NULL DEFAULT 'Dipinjam',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ebooks`
--

CREATE TABLE `ebooks` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(150) NOT NULL,
  `publisher` varchar(150) DEFAULT NULL,
  `publication_year` smallint(5) UNSIGNED DEFAULT NULL,
  `isbn` varchar(30) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_path` varchar(255) DEFAULT NULL,
  `pdf_path` varchar(255) NOT NULL,
  `file_size` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `page_count` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ebook_downloads`
--

CREATE TABLE `ebook_downloads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `ebook_id` int(10) UNSIGNED NOT NULL,
  `downloaded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ebook_reading_history`
--

CREATE TABLE `ebook_reading_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `ebook_id` int(10) UNSIGNED NOT NULL,
  `first_read_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_read_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_page` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `total_pages` int(10) UNSIGNED DEFAULT NULL,
  `progress_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `duration_seconds` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `session_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ebook_submissions`
--

CREATE TABLE `ebook_submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `publication_year` smallint(5) UNSIGNED DEFAULT NULL,
  `pdf_path` varchar(500) NOT NULL,
  `cover_path` varchar(500) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reviewed_by` int(10) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `approved_ebook_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `max_personal_copies` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `max_class_copies` int(10) UNSIGNED NOT NULL DEFAULT 30,
  `library_name` varchar(100) NOT NULL DEFAULT 'BinaLib',
  `admin_name` varchar(100) NOT NULL DEFAULT 'Admin',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(10) UNSIGNED NOT NULL,
  `library_id` varchar(20) NOT NULL,
  `nis` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `rfid_uid` varchar(10) DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_books_title` (`title`),
  ADD KEY `idx_books_category` (`category`),
  ADD KEY `idx_books_code` (`code`);

--
-- Indexes for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_borrowings_student_status` (`student_id`,`status`),
  ADD KEY `idx_borrowings_book_status` (`book_id`,`status`),
  ADD KEY `idx_borrowings_borrowed_at` (`borrowed_at`);

--
-- Indexes for table `ebooks`
--
ALTER TABLE `ebooks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_ebooks_title` (`title`),
  ADD KEY `idx_ebooks_category` (`category`),
  ADD KEY `idx_ebooks_status` (`status`);

--
-- Indexes for table `ebook_downloads`
--
ALTER TABLE `ebook_downloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ebook_download_student` (`student_id`),
  ADD KEY `idx_ebook_download_ebook` (`ebook_id`),
  ADD KEY `idx_ebook_download_date` (`downloaded_at`);

--
-- Indexes for table `ebook_reading_history`
--
ALTER TABLE `ebook_reading_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ebook_history_student_ebook` (`student_id`,`ebook_id`),
  ADD KEY `idx_ebook_history_student` (`student_id`),
  ADD KEY `idx_ebook_history_ebook` (`ebook_id`),
  ADD KEY `idx_ebook_history_last_read` (`last_read_at`);

--
-- Indexes for table `ebook_submissions`
--
ALTER TABLE `ebook_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_submission_student` (`student_id`),
  ADD KEY `idx_submission_status` (`status`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `library_id` (`library_id`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD UNIQUE KEY `rfid_uid` (`rfid_uid`),
  ADD KEY `idx_students_rfid` (`rfid_uid`),
  ADD KEY `idx_students_class` (`class_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `ebooks`
--
ALTER TABLE `ebooks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `ebook_downloads`
--
ALTER TABLE `ebook_downloads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `ebook_reading_history`
--
ALTER TABLE `ebook_reading_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `ebook_submissions`
--
ALTER TABLE `ebook_submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowings`
--
ALTER TABLE `borrowings`
  ADD CONSTRAINT `fk_borrowings_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_borrowings_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ebook_downloads`
--
ALTER TABLE `ebook_downloads`
  ADD CONSTRAINT `fk_ebook_download_ebook` FOREIGN KEY (`ebook_id`) REFERENCES `ebooks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ebook_download_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ebook_reading_history`
--
ALTER TABLE `ebook_reading_history`
  ADD CONSTRAINT `fk_ebook_history_ebook` FOREIGN KEY (`ebook_id`) REFERENCES `ebooks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ebook_history_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
