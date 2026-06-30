-- JafariLab MySQL schema
-- Import melalui phpMyAdmin atau: mysql -u root -p < database/jafarilab.sql

CREATE DATABASE IF NOT EXISTS `jafarilab`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `jafarilab`;

CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `year` VARCHAR(20),
  `image` VARCHAR(500),
  `link` VARCHAR(500),
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `services` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `key` VARCHAR(100) NOT NULL,
  `value` TEXT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects`
  (`title`, `category`, `description`, `year`, `image`, `link`, `sort_order`)
SELECT
  'Nusa Essentials',
  'E-commerce · Art Direction · Development',
  'Pengalaman belanja untuk brand kebutuhan sehari-hari.',
  '2026', '', '', 1
WHERE NOT EXISTS (SELECT 1 FROM `projects`);

INSERT INTO `projects`
  (`title`, `category`, `description`, `year`, `image`, `link`, `sort_order`)
SELECT
  'Kala Studio',
  'Brand Identity · Web Design',
  'Identitas digital untuk studio arsitektur.',
  '2025', '', '', 2
WHERE NOT EXISTS (SELECT 1 FROM `projects` WHERE `title` = 'Kala Studio');

INSERT INTO `projects`
  (`title`, `category`, `description`, `year`, `image`, `link`, `sort_order`)
SELECT
  'Ruang Sehat',
  'Product Design · Mobile Experience',
  'Aplikasi kebugaran yang terasa personal.',
  '2025', '', '', 3
WHERE NOT EXISTS (SELECT 1 FROM `projects` WHERE `title` = 'Ruang Sehat');

INSERT IGNORE INTO `services` (`id`, `title`, `description`, `sort_order`) VALUES
  (1, 'Website Development', 'Website cepat, responsif, dan mudah dikelola dengan fondasi teknis yang solid.', 1),
  (2, 'UI/UX Design', 'Antarmuka intuitif yang berangkat dari kebutuhan pengguna dan tujuan bisnis.', 2),
  (3, 'Creative Direction', 'Arah visual konsisten agar brand punya karakter dan daya ingat yang kuat.', 3);

INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
  ('name', 'Jafari'),
  ('role', 'Creative Developer'),
  ('location', 'Jakarta'),
  ('hero_title', 'Membuat ide terasa|hidup di layar.'),
  ('about', 'Saya Jafari—developer dan desainer yang mengubah ide kompleks menjadi pengalaman digital yang jernih, hangat, dan berkesan.'),
  ('email', 'hello@jafari.studio'),
  ('instagram', '#'),
  ('linkedin', '#');

-- Tabel admins sengaja tidak diberi password statis.
-- Jalankan aplikasi setelah import; akun pertama dibuat dari ADMIN_USERNAME
-- dan ADMIN_PASSWORD pada file .env dengan password yang sudah di-hash.
