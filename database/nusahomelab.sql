-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 01, 2026 at 12:58 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nusahomelab`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$GbbDDYH9GTCLfCcLA2gQyOKC360flDdsq0MaQ9N573tPQOawWlDei', '2026-06-30 11:15:22');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `year` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `category`, `description`, `year`, `image`, `link`, `sort_order`, `created_at`) VALUES
(1, 'Private Cloud Server', 'Home Server', 'CloudPhoto  pribadi setara Google Photos tanpa biaya berlangganan', '2026', '/nusahomelab/uploads/1782899238-9fd4a44834.jpg', '', 1, '2026-06-30 09:33:51'),
(2, 'Self Hosted Docker Deployment Server', 'Home Server', 'Self Hosted Webserver vanilla dan framework berbasis docker', '2026', '/nusahomelab/uploads/1782899608-f030cc7840.jpg', '', 2, '2026-06-30 09:33:51'),
(3, 'RS Permata Pamulang Website', 'Web Development', 'Website dan Company Profile Rumah Sakit Permata Pamulang', '2023', '/nusahomelab/uploads/1782900393-846549aa09.jpg', '', 6, '2026-06-30 09:33:51'),
(5, 'Batavia Pijar Raya Website', 'Web Development', 'Company Profile PT Batavia Pijar Raaya Jakarta', '2025', '/nusahomelab/uploads/1782901147-8e36bf57c0.jpg', '', 3, '2026-07-01 06:23:49'),
(6, 'RS Permata Pamulang Appointment System', 'Web Apps Development', 'Sistem Pendaftartaran Mandiri bagi pasien rehab medik Rumah Sakit Permata Pamulang', '2025', '/nusahomelab/uploads/1782901764-63f489f94c.jpg', '', 3, '2026-07-01 10:29:24'),
(7, 'Admission Queueing System', 'Web Apps Development', 'Sistem Ticketing Pasien dengan integrasi Caller dan Dashboard Rumah Sakit Permata Pamulang', '2024', '/nusahomelab/uploads/1782903607-73418facfb.jpg', '', 5, '2026-07-01 11:00:07'),
(8, 'ergg', 'gerg', 'erger', 'erger', '', '', 7, '2026-07-01 11:26:01');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `sort_order`) VALUES
(1, 'Website Development', 'Website cepat, responsif, dan mudah dikelola dengan fondasi teknis yang solid.', 2),
(2, 'Private Home Server', 'Cloud pribadi yang aman, mudah diakses dari mana saja, dan dirancang untuk menyimpan serta mengelola data tanpa biaya langganan.', 1),
(3, 'Creative Direction', 'Arah visual konsisten agar brand punya karakter dan daya ingat yang kuat.', 3);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key`, `value`) VALUES
('about', 'Saya Jafari—developer dan desainer yang mengubah ide kompleks menjadi pengalaman digital yang jernih, hangat, dan berkesan.'),
('about_description', 'Nusa Homelab membantu individu, keluarga, dan pelaku usaha membangun ekosistem digital yang aman, modern, dan mudah dikelola. Mulai dari Private Home Server, pengembangan website, hingga layanan kreatif, kami menghadirkan solusi yang dirancang sesuai kebutuhan Anda.'),
('about_label', 'Tentang Kami'),
('about_title', 'Partner Digital untuk Rumah dan Bisnis'),
('benefit_1_text', 'Kami menyediakan layanan mulai dari private cloud, home server, pengembangan website, hingga pengelolaan konten digital, sehingga Anda mendapatkan solusi yang saling terhubung dalam satu layanan.'),
('benefit_1_title', 'Solusi Digital Terintegrasi'),
('benefit_2_text', 'Setiap solusi dirancang agar mudah dioperasikan, aman, dan dapat diakses dari mana saja, tanpa mengharuskan pelanggan memiliki keahlian teknis.'),
('benefit_2_title', 'Aman & User Friendly'),
('benefit_3_text', 'Tidak hanya melakukan instalasi atau pembuatan website, kami juga memberikan konsultasi, pelatihan, serta dukungan teknis agar solusi yang dibangun dapat digunakan secara optimal dalam jangka panjang.'),
('benefit_3_title', 'Dukungan Berkelanjutan'),
('contact_cta', 'Send Mail Now'),
('contact_description', ''),
('contact_label', 'Hubungi Kami'),
('contact_title', 'nusahomelab.my.id'),
('copyright', '© 2026 Nusa Homelab. All Rights Reserved.'),
('email', 'nusahomelab@gmail.com'),
('experience_label', 'All in One Service'),
('experience_years', 'Digital Solution'),
('footer_description', 'Empowering Your Digital Journey. Nusa Homelab menyediakan solusi Private Home Server, Website Development, dan Creative Services untuk membantu Anda tumbuh di era digital.'),
('hero_description', 'Mendampingi perjalanan digital Anda melalui solusi teknologi yang terintegrasi, mulai dari private cloud, home server, website, hingga layanan kreatif untuk mendukung kebutuhan pribadi maupun bisnis.'),
('hero_primary_text', 'Proyek'),
('hero_secondary_text', 'Hubungi Kami'),
('hero_title', 'Empowering Your Digital Journey'),
('instagram', '#'),
('linkedin', '#'),
('location', 'Tangerang Selatan'),
('logo', '/nusahomelab/uploads/logo-1782829843-3179e76c9c.png'),
('name', 'Nusa Homelab'),
('portfolio_cta', ''),
('portfolio_label', 'Portofolio Pilihan'),
('portfolio_title', 'Karya yang mendampingi bisnis anda bertumbuh'),
('project_count', '28+'),
('rating', '4.9'),
('rating_label', 'Client Rating'),
('role', 'Dev and Private Server'),
('service_link_text', ''),
('services_description', 'Dari ide hingga online, kami mendampingi setiap langkah dengan proses yang jelas dan hasil yang terukur.'),
('services_label', 'Layanan Kami'),
('services_title', 'Solusi digital untuk membawa bisnis Anda naik level.'),
('stat_1_label', 'Tim Terintegrasi'),
('stat_1_value', '1'),
('stat_2_label', 'Layanan Utama'),
('stat_2_value', '3'),
('stat_3_label', 'Akses Private Cloud'),
('stat_3_value', '24/7'),
('topbar_link_text', 'Konsultasi gratis'),
('topbar_text', 'Butuh bantuan proyek digital anda?'),
('trust_items', 'Home Server, Website, Creative Direction'),
('trust_label', 'Dipercaya untuk membangun');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_username_unique` (`username`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
