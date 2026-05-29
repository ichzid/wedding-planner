-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 29, 2026 at 04:27 PM
-- Server version: 8.0.45
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wedding-planner`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kua_documents`
--

CREATE TABLE `kua_documents` (
  `id` bigint UNSIGNED NOT NULL,
  `wedding_id` bigint UNSIGNED NOT NULL,
  `no` int NOT NULL DEFAULT '0',
  `nama_dokumen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpw_status` tinyint(1) NOT NULL DEFAULT '0',
  `cpp_status` tinyint(1) NOT NULL DEFAULT '0',
  `biaya` bigint NOT NULL DEFAULT '0',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kua_documents`
--

INSERT INTO `kua_documents` (`id`, `wedding_id`, `no`, `nama_dokumen`, `cpw_status`, `cpp_status`, `biaya`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Surat Pengantar Nikah (N1)', 0, 0, 0, 'Didapat dari kelurahan atau desa setempat', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(2, 1, 2, 'Surat Persetujuan Mempelai (N4)', 0, 0, 0, 'Surat pernyataan setuju untuk menikah tanpa paksaan', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(3, 1, 3, 'Surat Izin Orang Tua (N5)', 0, 0, 0, 'Wajib jika calon pengantin berusia di bawah 21 tahun', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(4, 1, 4, 'Fotokopi KTP dan KK', 0, 0, 0, 'Masing-masing calon pengantin dan orang tua/wali', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(5, 1, 5, 'Fotokopi Akta Kelahiran', 0, 0, 0, 'Atau surat keterangan kelahiran dari kelurahan/desa', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(6, 1, 6, 'Pasfoto Latar Biru 2x3', 0, 0, 0, 'Masing-masing 4 lembar', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(7, 1, 7, 'Pasfoto Latar Biru 3x4', 0, 0, 0, 'Masing-masing 4 lembar', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(8, 1, 8, 'Surat Keterangan Imunisasi TT', 0, 0, 0, 'Dari Puskesmas (untuk calon pengantin wanita)', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(9, 1, 9, 'Surat Pernyataan Belum Pernah Menikah', 0, 0, 0, 'Ditandatangani di atas meterai Rp10.000', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(10, 1, 10, 'Akta Cerai Asli', 0, 0, 0, 'Kondisional: Jika status duda/janda cerai hidup', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(11, 1, 11, 'Surat Keterangan Kematian (N6)', 0, 0, 0, 'Kondisional: Jika status duda/janda cerai mati', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(12, 1, 12, 'Surat Rekomendasi Nikah', 0, 0, 0, 'Kondisional: Jika menikah di luar kecamatan asal', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(13, 1, 13, 'Surat Izin/Dispensasi Atasan', 0, 0, 0, 'Kondisional: Jika calon anggota TNI/Polri', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(14, 1, 14, 'Dispensasi Pengadilan Agama', 0, 0, 0, 'Kondisional: Jika berusia di bawah 19 tahun', '2026-05-19 21:21:06', '2026-05-20 20:06:21'),
(15, 1, 15, 'Surat Keterangan Wali', 0, 0, 0, 'Kondisional: Jika wali nikah bukan ayah kandung', '2026-05-19 21:21:06', '2026-05-20 20:06:21');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000001_create_weddings_table', 1),
(5, '2024_01_01_000002_create_wedding_budget_table', 1),
(6, '2024_01_01_000003_create_seserahan_list_table', 1),
(7, '2024_01_01_000004_create_wedding_checklist_table', 1),
(8, '2024_01_01_000005_create_kua_documents_table', 1),
(9, '2026_05_20_042917_create_wedding_guests_table', 1),
(10, '2026_05_20_000001_add_sumber_dana_to_wedding_budget_table', 2),
(11, '2026_05_20_000002_standardize_seserahan_untuk_values', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seserahan_list`
--

CREATE TABLE `seserahan_list` (
  `id` bigint UNSIGNED NOT NULL,
  `wedding_id` bigint UNSIGNED NOT NULL,
  `no` int NOT NULL DEFAULT '0',
  `kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_item` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `untuk` enum('cpp','cpw') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cpp',
  `qty` int NOT NULL DEFAULT '1',
  `satuan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga` bigint NOT NULL DEFAULT '0',
  `status` enum('belum','sudah_dibeli') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seserahan_list`
--

INSERT INTO `seserahan_list` (`id`, `wedding_id`, `no`, `kategori`, `nama_item`, `untuk`, `qty`, `satuan`, `harga`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Perhiasan', 'Cincin Nikah Emas CPW', 'cpw', 1, 'pcs', 5000000, 'belum', '2026-05-19 21:21:06', '2026-05-26 08:33:57'),
(2, 1, 6, 'Perlengkapan Ibadah', 'Mukenah', 'cpw', 1, 'pcs', 241000, 'sudah_dibeli', '2026-05-19 21:21:06', '2026-05-26 08:33:57'),
(3, 1, 7, 'Perlengkapan Ibadah', 'Al-Qur\'an', 'cpw', 1, 'pcs', 166000, 'sudah_dibeli', '2026-05-19 21:21:06', '2026-05-26 08:33:57'),
(4, 1, 10, 'Tas & Sepatu', 'Tas Pesta Wanita', 'cpw', 1, 'buah', 1000000, 'belum', '2026-05-19 21:21:06', '2026-05-26 08:33:57'),
(5, 1, 11, 'Tas & Sepatu', 'Sepatu Wanita', 'cpw', 1, 'pasang', 700000, 'belum', '2026-05-19 21:21:06', '2026-05-26 08:33:57'),
(6, 1, 12, 'Kosmetik & Skincare', 'Skincare & Makeup', 'cpw', 1, 'set', 1000000, 'belum', '2026-05-19 21:21:06', '2026-05-26 08:33:57'),
(7, 1, 13, 'Kosmetik & Skincare', 'Perawatan Tubuh & Parfum', 'cpw', 1, 'set', 500000, 'belum', '2026-05-19 21:21:06', '2026-05-26 08:33:57'),
(8, 1, 14, 'Makanan & Buah', 'Kue Basah Hantaran', 'cpw', 1, 'kotak', 150000, 'belum', '2026-05-19 21:21:06', '2026-05-26 08:33:57'),
(9, 1, 15, 'Makanan & Buah', 'Parcel Buah Segar', 'cpw', 1, 'kotak', 150000, 'belum', '2026-05-19 21:21:06', '2026-05-26 08:33:57'),
(10, 1, 1, 'Perhiasan', 'Mahar/Mas Kawin (Logam Mulia 5gram)', 'cpw', 1, 'keping', 14000000, 'sudah_dibeli', '2026-05-19 23:47:37', '2026-05-26 08:33:57'),
(12, 1, 5, 'Perhiasan', 'Cincin Nikah CPP', 'cpp', 1, 'pcs', 800000, 'belum', '2026-05-22 20:44:29', '2026-05-26 08:33:57'),
(13, 1, 8, 'Perlengkapan Ibadah', 'Sajadah Couple', 'cpw', 2, 'pcs', 100000, 'belum', '2026-05-26 02:00:39', '2026-05-26 08:33:57'),
(14, 1, 3, 'Perhiasan', 'Gelang Emas CPW', 'cpw', 1, 'pcs', 6200000, 'belum', '2026-05-26 08:20:45', '2026-05-26 08:33:57'),
(15, 1, 4, 'Perhiasan', 'Kalung Emas CPW', 'cpw', 1, 'pcs', 9000000, 'belum', '2026-05-26 08:22:21', '2026-05-26 08:33:57'),
(16, 1, 9, 'Perlengkapan Mandi', 'Handuk Couple', 'cpw', 2, 'pcs', 150000, 'belum', '2026-05-26 08:33:46', '2026-05-26 08:33:57');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('22CmbSGGcMAzYOaygVdXN6l7pHbsOnDdqdi40pI2', NULL, '115.85.72.242', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJLZlNhMng0enhaME9MVGl5TFNCSEZZeXg3N3FPeTBOa3hqUHV3c0oyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWRcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1780023137),
('2IYk3PXD7XP0GVmfadzmbxN9vEGo9lu8KcgfqKN6', NULL, '114.122.10.115', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJqSzdIbzg1SGx4elRFV2ZMY0R6R1g0Nkk2bmhaZnl0NTJPTXM0WmtUIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWRcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1780042711),
('2Jw2ogWGpsWAg73IZv6R7iYjjdovzqysW8IUBUPQ', NULL, '74.7.242.30', 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.4; +https://openai.com/gptbot)', 'eyJfdG9rZW4iOiJyVGtBcWU5cDZGanlCT0VkY012VjRUam5YMWFiYmduRWZFenlYUEFFIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWRcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1779985985),
('33inV8Hlf7QtstTVqa0lyxfeL1TbYRRkqorNXj1x', NULL, '66.132.172.97', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'eyJfdG9rZW4iOiJsSm51bWI5Z2VzaVRWYmRGeWkwQzQ0QkxnQ1M3R2YwT0UzN0hrNjFCIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWQiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1780010012),
('3RePuLVxFZSE535wfWOxnMjrUx93pYVkKmOYpglC', NULL, '180.243.0.26', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ3NThreGdxNTY1eGNnZjdGVmNjbk5MV09YRTV6N01RTDcyMFBLTDRWIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWRcL3RhbXUiLCJyb3V0ZSI6InRhbXUuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1779902212),
('5mkEdmpHprhrwMbpwZhgH8KwOqtZjAIGsQ6Z8Xm3', NULL, '91.92.42.120', 'Mozilla/5.0 (X11; Linux x86_64; rv:133.0) Gecko/20100101 Firefox/133.0', 'eyJfdG9rZW4iOiJoRkRGRmVNZ1VaWUlPd0ZWeEJsV01oajh6cVVLWUFFbGsxa0toN25sIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWRcL2luZGV4LnBocCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1779874776),
('cnfXA9h3wrTqe023KS5p3knstyPVlYzQ7YXlzcvn', NULL, '155.2.228.196', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36', 'eyJfdG9rZW4iOiJDbVZkZmZrV3R6MThBRW5jTjdPUkFBSzZJQ2xJdWlKTHhaSUJ5Ukk3IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWQiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1779861750),
('FowI7fsE8jgmumDthMNJf1c66MzRQHf00OqDzRfK', NULL, '157.15.40.6', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'eyJfdG9rZW4iOiJXVUFOdDBKNTdRdm5Od0RmOHVJYUdiV3pUOWxoMk5zRXBsZWIycE5JIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWRcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1780001629),
('g0uQb15sibV2K9qFTYn9Xo6Ytq7pXvU94KuGrnyn', NULL, '54.216.140.197', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJwNmwwRFhveDlRU0JJNmRzdHU5cDJYM2tZWWE5MmdqeUc1eTRXQ2EzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWRcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1779848550),
('qB1BgrxpwIUXga3CzZF4SZoKQiFobmSgpolH8R0r', NULL, '66.132.172.97', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'eyJfdG9rZW4iOiJpcGtkTnJZYVBNMzhGdEI3R0pLcTMwTXVwTndKWGduMzJ2S0FhMkZ6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWQiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1780009980),
('RHMcreAok5ZUSAKhdEsLFxtN9Iw8uyh8jgnSFChF', NULL, '157.15.40.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'eyJfdG9rZW4iOiJnTGY4ekszbFlXUmszY0lXYmV2RkFvcDIxdDNBeFJRODVmOUpYUFhqIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1780001432),
('sy2czApAVyvF7l5kgerT0MWk5dna1uw8RfiHf7bs', NULL, '114.122.42.27', 'Mozilla/5.0 (iPhone; CPU iPhone OS 26_2_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/148.0.7778.166 Mobile/15E148 Safari/604.1', 'eyJfdG9rZW4iOiJ2M243ekFENEtzbGNuWmZudFpla3hHYmNhVDZjWEkyY2N6cjZuazFVIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWRcL3RhbXUiLCJyb3V0ZSI6InRhbXUuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1780010872),
('uIsgXtvkos8RiTm6V6c9HSdWNuY826BuwrhF2ARu', NULL, '157.15.40.6', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'eyJfdG9rZW4iOiJ6bkZsNlk0SGd3R2lhUWdYWU4yb1RXS29IZXZFbjRtNmRCZ2c0MTdZIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWQiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1780001628),
('WIvakjOaoqAcjMg7ylJDxbx0sPRMGO5ks6P8pCum', NULL, '66.132.172.97', 'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)', 'eyJfdG9rZW4iOiIyMGFjSjU0YnYxWHVlT3ZCV2JpeVZrUGtXSlRnNWVXbzhSbFBTQUZtIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWRcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1780009983),
('xGbZgcKjwxNdhwsRITD9SwU1gOCuL6JHovcF57tm', NULL, '157.15.40.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'eyJfdG9rZW4iOiJPc2VvZjBrUXZIdzBGZG55UEZpUE1ESk5zOHlmT3ZiMGRXeWVHY1FsIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1780001432),
('za1LxnfEJ2LJOEneNRubTpGn8mIdTJPTBo9KEtDx', NULL, '115.85.72.242', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJhb3lIbUs2czhSb1E5RVdUYXpobEZwNHlVQVRwNHQ4ZG1laHZlcTVHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9uaWthaC5pY2htYWwubXkuaWRcL2J1ZGdldCIsInJvdXRlIjoiYnVkZ2V0LmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1780036877);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weddings`
--

CREATE TABLE `weddings` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `nama_cpw` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_cpp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_nikah` date NOT NULL,
  `lokasi_akad` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi_resepsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `weddings`
--

INSERT INTO `weddings` (`id`, `user_id`, `nama_cpw`, `nama_cpp`, `tanggal_nikah`, `lokasi_akad`, `lokasi_resepsi`, `created_at`, `updated_at`) VALUES
(1, 1, 'Triana', 'Ihmal', '2027-06-27', 'Coffeshop xxx', 'Coffeshop xxx', '2026-05-19 21:21:06', '2026-05-19 21:21:06');

-- --------------------------------------------------------

--
-- Table structure for table `wedding_budget`
--

CREATE TABLE `wedding_budget` (
  `id` bigint UNSIGNED NOT NULL,
  `wedding_id` bigint UNSIGNED NOT NULL,
  `no` int NOT NULL DEFAULT '0',
  `kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estimasi_budget` bigint NOT NULL DEFAULT '0',
  `dp` bigint NOT NULL DEFAULT '0',
  `pelunasan` bigint NOT NULL DEFAULT '0',
  `sumber_dana` enum('cpp','cpw') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cpp',
  `status` enum('belum','dp_terbayar','lunas') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum',
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wedding_budget`
--

INSERT INTO `wedding_budget` (`id`, `wedding_id`, `no`, `kategori`, `item`, `vendor`, `estimasi_budget`, `dp`, `pelunasan`, `sumber_dana`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Venue', 'Sewa Vanue', 'Coffee Shop xxx', 6000000, 0, 0, 'cpp', 'belum', '-', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(2, 1, 4, 'Venue', 'Dekor Intimate Wedding', 'Dekor xxx', 8500000, 0, 0, 'cpp', 'belum', '- Backdrop akad 3-4 meter ✅\n- ⁠Dekor photobooth simpel ✅\n- Welcome gate simpel ✅\n- ⁠Welcome sign mirror + standing flower ✅\n- ⁠Set meja kursi akad ✅\n- ⁠Set meja kursi tamu ✅\n- ⁠Altar jalan include aisle bunga artificial 3-4 meter ✅\n- ⁠Lighting dibawah dekorasi dengan lampu sorot ✅\n- ⁠Standing foto (opsional) karna akan cetak sendiri, tim dekor bantu pemasangan dan peletakannya \n- ⁠Drapery ✅', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(3, 1, 7, 'Catering', 'Catering 150 - 200 pax', 'Pondok Bali Lestari', 20000000, 0, 0, 'cpp', 'belum', 'Harga 60rb/pax buffet + 1 stall makanan pilihan + gubukan', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(5, 1, 5, 'Dokumentasi', 'Paket Fotografer dan Videografer (Shooting & Cinematic)', 'Cinema xxx', 9000000, 0, 0, 'cpp', 'belum', 'Paket Fotografer dan Videografer Lamaran, Prewed & Wedding', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(6, 1, 6, 'Attire & Makeup', 'MUA Include Attire CPW', 'MUA xxx', 2350000, 0, 0, 'cpp', 'belum', 'Attire Akad', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(7, 1, 17, 'Attire & Makeup', 'Seragam Keluarga', 'Tanah Abang/Online', 6000000, 0, 0, 'cpw', 'belum', 'Beli baju jadi', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(8, 1, 9, 'Entertainment', 'Sewa Soundsystem + Penyanyi 1', '-', 2000000, 0, 0, 'cpp', 'belum', 'Durasi 5-6 jam', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(9, 1, 8, 'Entertainment', 'MC + WO', 'Adhesyah', 5000000, 0, 0, 'cpp', 'belum', 'MC + 5 Kru', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(10, 1, 12, 'Seserahan', 'Paket Kotak Seserahan', '-', 500000, 0, 0, 'cpp', 'belum', 'Isi seserahan wanita', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(11, 1, 1, 'Seserahan', 'Mahar Pernikahan', '-', 35000000, 14000000, 0, 'cpp', 'dp_terbayar', 'Logam Mulia 5 Gram (Sudah dibeli)\nCincin, Kalung dan Gelang Emas CPW\nCincin Perak CPP', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(12, 1, 13, 'Lainnya', 'Undangan Cetak & Digital', '-', 500000, 0, 0, 'cpp', 'belum', 'Cetak 100 lembar + undangan website', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(13, 1, 19, 'Lainnya', 'Souvenir Pernikahan', 'xxx', 1000000, 0, 0, 'cpw', 'belum', '-', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(14, 1, 14, 'Lainnya', 'Biaya KUA & Penghulu', 'KUA Setempat', 1500000, 0, 0, 'cpp', 'belum', 'Biaya pendaftaran dan transport penghulu', '2026-05-19 21:21:06', '2026-05-26 08:18:18'),
(15, 1, 18, 'Transport', 'Rental Mobil', '-', 1000000, 0, 0, 'cpw', 'belum', 'Rental mobil untuk keluarga CPP dan CPW', '2026-05-20 06:41:12', '2026-05-26 08:18:18'),
(16, 1, 10, 'Catering', 'Stand Jajanan', 'Sweet Corner', 1500000, 0, 0, 'cpp', 'belum', '-', '2026-05-20 06:55:12', '2026-05-26 08:18:18'),
(17, 1, 11, 'Catering', 'Stand Kopi', '-', 1500000, 0, 0, 'cpp', 'belum', '-', '2026-05-20 06:55:44', '2026-05-26 08:18:18'),
(24, 1, 15, 'Attire & Makeup', 'Attire Wedding CPP', 'Beli Online', 250000, 0, 0, 'cpw', 'belum', NULL, '2026-05-26 01:49:40', '2026-05-26 08:18:18'),
(25, 1, 16, 'Attire & Makeup', 'Baju Lamaran CPP', 'Beli Online', 170000, 0, 170000, 'cpw', 'lunas', NULL, '2026-05-26 01:56:53', '2026-05-26 08:18:18'),
(26, 1, 2, 'Seserahan', 'Seserahan untuk CPW', 'Beli online', 4000000, 0, 0, 'cpp', 'dp_terbayar', 'Perlengkapan Ibadah, Skincare, Bodycare, Makeup, Handuk Couple, Piyama Couple, dll', '2026-05-26 08:11:57', '2026-05-26 08:18:18'),
(27, 1, 20, 'Seserahan', 'Seserahan untuk CPP', 'Beli online', 1000000, 0, 0, 'cpw', 'belum', 'Perlengkapan Ibadah, dll', '2026-05-26 08:15:55', '2026-05-26 08:18:18'),
(28, 1, 21, 'Attire & Makeup', 'Henna', '-', 300000, 0, 0, 'cpw', 'belum', NULL, '2026-05-27 09:16:52', '2026-05-27 09:16:52');

-- --------------------------------------------------------

--
-- Table structure for table `wedding_checklist`
--

CREATE TABLE `wedding_checklist` (
  `id` bigint UNSIGNED NOT NULL,
  `wedding_id` bigint UNSIGNED NOT NULL,
  `no` int NOT NULL DEFAULT '0',
  `bulan_range` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `persiapan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wedding_checklist`
--

INSERT INTO `wedding_checklist` (`id`, `wedding_id`, `no`, `bulan_range`, `persiapan`, `detail`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 12, 'H-6 s/d 5 Bulan', 'Tentukan tanggal & tempat akad/resepsi', 'Koordinasikan dengan kedua keluarga', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(2, 1, 4, 'H-12 s/d 11 Bulan', 'Buat anggaran pernikahan', 'Estimasikan semua biaya yang diperlukan', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(3, 1, 2, 'H-12 s/d 11 Bulan', 'Tentukan tema & konsep pernikahan', 'Pilih warna, dekor, dan suasana yang diinginkan', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(4, 1, 1, 'H-12 s/d 11 Bulan', 'Survey venue', 'Minimal 3 venue untuk perbandingan', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(5, 1, 9, 'H-8 s/d 7 Bulan', 'Buat daftar tamu undangan', 'Perkiraan jumlah tamu dari kedua belah pihak', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(6, 1, 3, 'H-12 s/d 11 Bulan', 'Booking fotografer & videografer', 'Pastikan tanggal sudah tersedia', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(7, 1, 7, 'H-10 s/d 9 Bulan', 'Pilih dan booking katering', 'Lakukan food tasting sebelum konfirmasi', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(8, 1, 8, 'H-10 s/d 9 Bulan', 'Tentukan konsep dekorasi', 'Diskusikan detail dengan vendor dekor', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(9, 1, 6, 'H-10 s/d 9 Bulan', 'Mulai cari gaun & jas pengantin', 'Butik biasanya butuh 3-4 bulan untuk order', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(10, 1, 13, 'H-6 s/d 5 Bulan', 'Booking MUA (Make Up Artist)', 'Lakukan test make up sebelum booking', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(11, 1, 10, 'H-8 s/d 7 Bulan', 'Pesan undangan pernikahan', 'Desain dan cetak undangan fisik & digital', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(12, 1, 17, 'H-4 s/d 3 Bulan', 'Booking entertainment / band', 'Tentukan lagu-lagu yang ingin dimainkan', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(13, 1, 11, 'H-8 s/d 7 Bulan', 'Urus dokumen ke KUA', 'Siapkan semua persyaratan dokumen', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(14, 1, 19, 'H-2 s/d 1 Bulan', 'Lakukan fitting gaun pengantin', 'Pastikan ukuran sudah pas', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(15, 1, 5, 'H-10 s/d 9 Bulan', 'Siapkan hadiah seserahan', 'Mulai beli item-item seserahan', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(16, 1, 20, 'H-2 s/d 1 Bulan', 'Kirim undangan gelombang pertama', 'Untuk tamu VIP dan luar kota', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(18, 1, 14, 'H-4 s/d 3 Bulan', 'Konfirmasi ulang semua vendor', 'Pastikan semua masih sesuai kesepakatan', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(19, 1, 15, 'H-4 s/d 3 Bulan', 'Kirim sisa undangan', 'Untuk tamu umum dan kerabat', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(20, 1, 16, 'H-4 s/d 3 Bulan', 'Beli souvenir pernikahan', 'Sesuaikan jumlah dengan tamu undangan', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59'),
(21, 1, 18, 'H-2 s/d 1 Bulan', 'Persiapkan rundown acara', 'Koordinasikan dengan MC dan vendor', 0, '2026-05-19 21:21:06', '2026-05-24 21:00:59');

-- --------------------------------------------------------

--
-- Table structure for table `wedding_guests`
--

CREATE TABLE `wedding_guests` (
  `id` bigint UNSIGNED NOT NULL,
  `wedding_id` bigint UNSIGNED NOT NULL,
  `no` int NOT NULL,
  `nama_tamu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pihak` enum('cpw','cpp','umum') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'umum',
  `status` enum('belum_dikirim','sudah_dikirim','hadir','tidak_hadir') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_dikirim',
  `catatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wedding_guests`
--

INSERT INTO `wedding_guests` (`id`, `wedding_id`, `no`, `nama_tamu`, `pihak`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Bapak', 'cpw', 'belum_dikirim', 'Orang tua CPW', '2026-05-26 02:02:39', '2026-05-28 15:22:27'),
(2, 1, 2, 'Mamak', 'cpw', 'belum_dikirim', 'Orang tua CPW', '2026-05-26 02:02:53', '2026-05-28 15:22:27'),
(3, 1, 3, 'Novi', 'cpw', 'belum_dikirim', 'Kakak kandung CPW', '2026-05-26 02:03:12', '2026-05-28 15:22:27'),
(4, 1, 4, 'Gindrok', 'cpw', 'belum_dikirim', 'Ipar CPW', '2026-05-26 02:03:26', '2026-05-28 15:22:27'),
(5, 1, 5, 'Lia', 'cpw', 'belum_dikirim', 'Kakak kandung CPW', '2026-05-26 02:03:39', '2026-05-28 15:22:27'),
(6, 1, 6, 'Edi', 'cpw', 'belum_dikirim', 'Ipar CPW', '2026-05-26 02:03:49', '2026-05-28 15:22:27'),
(7, 1, 7, 'Triana', 'cpw', 'belum_dikirim', 'CPW', '2026-05-26 02:04:13', '2026-05-28 15:22:27'),
(8, 1, 8, 'Selvi', 'cpw', 'belum_dikirim', 'Adik kandung CPW', '2026-05-26 02:04:26', '2026-05-28 15:22:27'),
(9, 1, 9, 'Penda', 'cpw', 'belum_dikirim', 'Ipar CPW', '2026-05-26 02:04:37', '2026-05-28 15:22:27'),
(10, 1, 10, 'Bocil - bocil', 'cpw', 'belum_dikirim', 'Ponakan CPW', '2026-05-26 02:05:38', '2026-05-28 15:22:27'),
(11, 1, 11, 'Kaliyem', 'cpw', 'belum_dikirim', 'Nenek CPW', '2026-05-26 02:06:12', '2026-05-28 15:22:27'),
(12, 1, 12, 'Wakirin', 'cpw', 'belum_dikirim', 'Kakek CPW', '2026-05-26 02:06:24', '2026-05-28 15:22:27'),
(13, 1, 14, 'Imang', 'cpw', 'belum_dikirim', 'Paklek CPW', '2026-05-26 02:07:05', '2026-05-28 15:22:27'),
(14, 1, 15, 'Sri', 'cpw', 'belum_dikirim', 'Buklek CPW', '2026-05-26 02:07:18', '2026-05-28 15:22:27'),
(15, 1, 16, 'Dewi', 'cpw', 'belum_dikirim', 'Sepupu CPW', '2026-05-26 02:07:47', '2026-05-28 15:22:27'),
(16, 1, 17, 'Biyu', 'cpw', 'belum_dikirim', 'Sepupu CPW', '2026-05-26 02:08:01', '2026-05-28 15:22:27'),
(17, 1, 18, 'Simpen', 'cpw', 'belum_dikirim', 'Nenek CPW', '2026-05-26 02:08:28', '2026-05-28 15:22:27'),
(18, 1, 19, 'Gibos', 'cpw', 'belum_dikirim', 'Sepupu CPW', '2026-05-26 02:08:38', '2026-05-28 15:22:27'),
(19, 1, 20, 'Sunar', 'cpw', 'belum_dikirim', 'Tetangga CPW', '2026-05-26 02:09:32', '2026-05-28 15:22:27'),
(20, 1, 21, 'Ani', 'cpw', 'belum_dikirim', 'Tetangga CPW', '2026-05-26 02:09:46', '2026-05-28 15:22:27'),
(21, 1, 22, 'Della', 'cpw', 'belum_dikirim', 'Sepupu CPW', '2026-05-26 02:09:59', '2026-05-28 15:22:27'),
(22, 1, 23, 'Andri', 'cpw', 'belum_dikirim', 'Partner Della', '2026-05-26 02:10:56', '2026-05-28 15:22:27'),
(23, 1, 24, 'Sitiek', 'cpw', 'belum_dikirim', 'Bestie CPW', '2026-05-26 02:12:19', '2026-05-28 15:22:27'),
(24, 1, 25, 'Irwan', 'cpw', 'belum_dikirim', 'Partner Sitiek', '2026-05-26 02:12:47', '2026-05-28 15:22:27'),
(25, 1, 26, 'Mamak sitiek', 'cpw', 'belum_dikirim', 'Orang tua Sitiek', '2026-05-26 02:13:14', '2026-05-28 15:22:27'),
(26, 1, 27, 'Bapak Sitiek', 'cpw', 'belum_dikirim', 'Orang tua Sitiek', '2026-05-26 02:13:45', '2026-05-28 15:22:27'),
(27, 1, 28, 'Mama Popy', 'cpw', 'belum_dikirim', 'Orang tua Popy', '2026-05-26 02:14:59', '2026-05-28 15:22:27'),
(28, 1, 29, 'Papa Popy', 'cpw', 'belum_dikirim', 'Orang tua Popy', '2026-05-26 02:15:35', '2026-05-28 15:22:27'),
(29, 1, 13, 'Mardi', 'cpw', 'belum_dikirim', 'Uwak CPW', '2026-05-26 02:16:28', '2026-05-28 15:22:27'),
(30, 1, 32, 'Alin', 'cpw', 'belum_dikirim', 'Sepupu CPW', '2026-05-26 02:17:08', '2026-05-28 15:22:27'),
(31, 1, 33, 'Juni', 'cpw', 'belum_dikirim', 'Istri Alin', '2026-05-26 02:17:33', '2026-05-28 15:22:27'),
(32, 1, 34, 'Dani', 'cpw', 'belum_dikirim', 'Sepupu CPW', '2026-05-26 02:17:41', '2026-05-28 15:22:27'),
(33, 1, 35, 'Legiman', 'cpw', 'belum_dikirim', 'Suami Dani', '2026-05-26 02:18:01', '2026-05-28 15:22:27'),
(34, 1, 36, 'Sari', 'cpw', 'belum_dikirim', 'Sepupu CPW', '2026-05-26 02:18:11', '2026-05-28 15:22:27'),
(35, 1, 37, 'Budi', 'cpw', 'belum_dikirim', 'Suami Sari', '2026-05-26 02:18:22', '2026-05-28 15:22:27'),
(36, 1, 38, 'Nining', 'cpw', 'belum_dikirim', 'Sepupu CPW', '2026-05-26 02:18:37', '2026-05-28 15:22:27'),
(37, 1, 39, 'Rahmad', 'cpw', 'belum_dikirim', 'Suami Nining', '2026-05-26 02:20:45', '2026-05-28 15:22:27'),
(38, 1, 40, 'Vicky', 'cpw', 'belum_dikirim', 'Teman CPW', '2026-05-26 02:21:10', '2026-05-28 15:22:27'),
(39, 1, 42, 'Vindy', 'cpw', 'belum_dikirim', 'Teman CPW', '2026-05-26 02:21:22', '2026-05-28 15:22:27'),
(40, 1, 44, 'Jean', 'cpw', 'belum_dikirim', 'Teman CPW', '2026-05-26 02:21:33', '2026-05-28 15:22:28'),
(41, 1, 30, 'Atik', 'cpw', 'belum_dikirim', 'Buklek CPW', '2026-05-27 08:55:56', '2026-05-28 15:22:27'),
(42, 1, 31, 'Dedi', 'cpw', 'belum_dikirim', 'Sepupu CPW', '2026-05-27 08:56:35', '2026-05-28 15:22:27'),
(43, 1, 45, 'Wendy', 'cpw', 'belum_dikirim', 'Teman CPW', '2026-05-27 08:59:20', '2026-05-28 15:22:28'),
(44, 1, 41, 'Ade', 'cpw', 'belum_dikirim', 'Suami Vicky', '2026-05-27 08:59:52', '2026-05-28 15:22:27'),
(45, 1, 43, 'Wanda', 'cpw', 'belum_dikirim', 'Suami Vindy', '2026-05-27 09:07:21', '2026-05-28 15:22:28'),
(46, 1, 46, 'Eva', 'cpw', 'belum_dikirim', 'Istri Wendy', '2026-05-27 09:07:51', '2026-05-28 15:22:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kua_documents`
--
ALTER TABLE `kua_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kua_documents_wedding_id_foreign` (`wedding_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `seserahan_list`
--
ALTER TABLE `seserahan_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seserahan_list_wedding_id_foreign` (`wedding_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `weddings`
--
ALTER TABLE `weddings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wedding_budget`
--
ALTER TABLE `wedding_budget`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wedding_budget_wedding_id_foreign` (`wedding_id`);

--
-- Indexes for table `wedding_checklist`
--
ALTER TABLE `wedding_checklist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wedding_checklist_wedding_id_foreign` (`wedding_id`);

--
-- Indexes for table `wedding_guests`
--
ALTER TABLE `wedding_guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wedding_guests_wedding_id_foreign` (`wedding_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kua_documents`
--
ALTER TABLE `kua_documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `seserahan_list`
--
ALTER TABLE `seserahan_list`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weddings`
--
ALTER TABLE `weddings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wedding_budget`
--
ALTER TABLE `wedding_budget`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `wedding_checklist`
--
ALTER TABLE `wedding_checklist`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `wedding_guests`
--
ALTER TABLE `wedding_guests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kua_documents`
--
ALTER TABLE `kua_documents`
  ADD CONSTRAINT `kua_documents_wedding_id_foreign` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seserahan_list`
--
ALTER TABLE `seserahan_list`
  ADD CONSTRAINT `seserahan_list_wedding_id_foreign` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wedding_budget`
--
ALTER TABLE `wedding_budget`
  ADD CONSTRAINT `wedding_budget_wedding_id_foreign` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wedding_checklist`
--
ALTER TABLE `wedding_checklist`
  ADD CONSTRAINT `wedding_checklist_wedding_id_foreign` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wedding_guests`
--
ALTER TABLE `wedding_guests`
  ADD CONSTRAINT `wedding_guests_wedding_id_foreign` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
