-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Agu 2025 pada 09.38
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `magangspnew`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `auth_activation_attempts`
--

CREATE TABLE `auth_activation_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `auth_activation_attempts`
--

INSERT INTO `auth_activation_attempts` (`id`, `ip_address`, `user_agent`, `token`, `created_at`) VALUES
(1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'e11ed8e23863dbbe6876df46690cec2e', '2025-07-07 03:37:51'),
(2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'fe60487fd747d584c1566cb82fb4f620', '2025-07-07 23:20:25'),
(3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', 'ff2a2ea4c99d2670e985e6d2170ea033', '2025-07-08 04:03:19'),
(4, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'de24688fb7181a3d455e8fe6563e1645', '2025-07-21 10:27:52'),
(5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '5cf4daa13ceccd10c1954521d0f3cf07', '2025-07-31 15:09:29'),
(6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '0d502e55e6be4ccb896a1d08975fd312', '2025-07-31 15:32:01'),
(7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'fe75e7cf257cfb30858d93abd00ce21b', '2025-08-04 11:22:12');

-- --------------------------------------------------------

--
-- Struktur dari tabel `auth_groups`
--

CREATE TABLE `auth_groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `auth_groups`
--

INSERT INTO `auth_groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator'),
(2, 'user', 'Regular User'),
(4, 'pembimbing', 'Pembimbing Unit'),
(5, 'she', 'Unit SHE');

-- --------------------------------------------------------

--
-- Struktur dari tabel `auth_groups_permissions`
--

CREATE TABLE `auth_groups_permissions` (
  `group_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `permission_id` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `auth_groups_users`
--

CREATE TABLE `auth_groups_users` (
  `group_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `auth_groups_users`
--

INSERT INTO `auth_groups_users` (`group_id`, `user_id`) VALUES
(1, 1),
(2, 26),
(4, 23);

-- --------------------------------------------------------

--
-- Struktur dari tabel `auth_logins`
--

CREATE TABLE `auth_logins` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `date` datetime NOT NULL,
  `success` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `auth_logins`
--

INSERT INTO `auth_logins` (`id`, `ip_address`, `email`, `user_id`, `date`, `success`) VALUES
(1, '::1', 'virayukia1234@gmail.com', NULL, '2025-07-03 09:08:02', 0),
(2, '::1', 'virayukia1234@gmail.com', NULL, '2025-07-03 09:08:38', 0),
(3, '::1', 'bordirmotif@gmail.com', 1, '2025-07-03 09:08:56', 1),
(4, '::1', 'bordirmotif@gmail.com', NULL, '2025-07-03 09:21:11', 0),
(5, '::1', 'bordirmotif@gmail.com', 1, '2025-07-03 09:21:26', 1),
(6, '::1', 'bordirmotif@gmail.com', 1, '2025-07-03 09:49:47', 1),
(7, '::1', 'bordirmotif@gmail.com', 1, '2025-07-03 09:59:47', 1),
(8, '::1', 'bordirmotif@gmail.com', 1, '2025-07-04 03:40:00', 1),
(9, '::1', 'virayukia1234@gmail.com', 4, '2025-07-07 03:38:37', 1),
(10, '::1', 'bordirmotif@gmail.com', 1, '2025-07-07 04:46:05', 1),
(11, '::1', 'bordirmotif@gmail.com', 1, '2025-07-07 04:51:19', 1),
(12, '::1', 'virayukia1234@gmail.com', 4, '2025-07-07 07:48:07', 1),
(13, '::1', 'bordirmotif@gmail.com', 1, '2025-07-07 09:14:04', 1),
(14, '::1', 'virayukia1234@gmail.com', 4, '2025-07-07 13:27:03', 1),
(15, '::1', 'bordirmotif@gmail.com', 1, '2025-07-07 13:28:21', 1),
(16, '::1', 'virayukia1234@gmail.com', 4, '2025-07-07 17:15:46', 1),
(17, '::1', 'bordirmotif@gmail.com', 1, '2025-07-07 21:58:06', 1),
(18, '::1', 'virayukia1234@gmail.com', 4, '2025-07-07 22:52:03', 1),
(19, '::1', 'iqda.maulana@gmail.com', 7, '2025-07-07 23:20:42', 1),
(20, '::1', 'virayukia1234@gmail.com', 4, '2025-07-08 01:46:32', 1),
(21, '::1', 'bordirmotif@gmail.com', 1, '2025-07-08 01:47:06', 1),
(22, '::1', 'iqda.maulana@gmail.com', NULL, '2025-07-08 02:03:24', 0),
(23, '::1', 'iqda.maulana@gmail.com', 7, '2025-07-08 02:03:37', 1),
(24, '::1', 'bordirmotif@gmail.com', 1, '2025-07-08 03:56:22', 1),
(25, '::1', 'kuronekochann123@gmail.com', 8, '2025-07-08 04:03:33', 1),
(26, '::1', 'kuronekochann123@gmail.com', 8, '2025-07-08 04:10:14', 1),
(27, '::1', 'virayukia1234@gmail.com', 4, '2025-07-08 04:11:07', 1),
(28, '::1', 'bordirmotif@gmail.com', 1, '2025-07-08 04:11:34', 1),
(29, '::1', 'bordirmotif@gmail.com', 1, '2025-07-08 04:12:48', 1),
(30, '::1', 'virayukia1234@gmail.com', 4, '2025-07-08 04:13:44', 1),
(31, '::1', 'virayukia1234@gmail.com', 4, '2025-07-10 02:45:01', 1),
(32, '::1', 'virayukia1234@gmail.com', NULL, '2025-07-10 08:55:40', 0),
(33, '::1', 'virayukia1234@gmail.com', NULL, '2025-07-10 08:57:06', 0),
(34, '::1', 'virayukia1234@gmail.com', 4, '2025-07-10 09:07:00', 1),
(35, '::1', 'bordirmotif@gmail.com', 1, '2025-07-14 01:33:32', 1),
(36, '::1', 'virayukia1234@gmail.com', 4, '2025-07-14 01:50:48', 1),
(37, '::1', 'bordirmotif@gmail.com', 1, '2025-07-14 07:35:30', 1),
(38, '::1', 'virayukia1234@gmail.com', 4, '2025-07-14 21:59:05', 1),
(39, '::1', 'bordirmotif@gmail.com', 1, '2025-07-15 01:30:01', 1),
(40, '::1', 'bordirmotif@gmail.com', 1, '2025-07-15 01:39:20', 1),
(41, '::1', 'virayukia1234@gmail.com', 4, '2025-07-15 02:42:04', 1),
(42, '::1', 'virayukia1234@gmail.com', 4, '2025-07-15 04:18:51', 1),
(43, '::1', 'iqda.maulana@gmail.com', NULL, '2025-07-15 11:51:03', 0),
(44, '::1', 'iqda.maulana@gmail.com', 7, '2025-07-15 11:51:54', 1),
(45, '::1', 'virayukia1234@gmail.com', 4, '2025-07-15 13:41:36', 1),
(46, '::1', 'kuronekochann123@gmail.com', NULL, '2025-07-15 15:25:52', 0),
(47, '::1', 'kuronekochann123@gmail.com', NULL, '2025-07-15 15:26:02', 0),
(48, '::1', 'kuronekochann123@gmail.com', NULL, '2025-07-15 15:26:14', 0),
(49, '::1', 'bordirmotif@gmail.com', 1, '2025-07-15 15:26:21', 1),
(50, '::1', 'kuronekochann123@gmail.com', NULL, '2025-07-15 15:37:19', 0),
(51, '::1', 'kuronekochann1234@gmail.com', NULL, '2025-07-15 15:37:55', 0),
(52, '::1', 'kuronekochann123@gmail.com', NULL, '2025-07-15 15:38:38', 0),
(53, '::1', 'bordirmotif@gmail.com', 1, '2025-07-15 15:39:25', 1),
(54, '::1', 'kuronekochann123@gmail.com', NULL, '2025-07-15 15:47:37', 0),
(55, '::1', 'kuronekochann123@gmail.com', NULL, '2025-07-15 15:47:53', 0),
(56, '::1', 'kuronekochann123@gmail.com', NULL, '2025-07-15 15:48:45', 0),
(57, '::1', 'kuronekochann1234@gmail.com', 15, '2025-07-15 15:54:19', 1),
(58, '::1', 'virayukia1234@gmail.com', 4, '2025-07-17 16:34:21', 1),
(59, '::1', 'iqda.maulana@gmail.com', NULL, '2025-07-17 16:44:07', 0),
(60, '::1', 'iqda.maulana@gmail.com', NULL, '2025-07-17 16:44:22', 0),
(61, '::1', 'bordirmotif@gmail.com', 1, '2025-07-17 16:45:22', 1),
(62, '::1', 'bordirmotif@gmail.com', 1, '2025-07-18 09:07:40', 1),
(63, '::1', 'bordirmotif@gmail.com', 1, '2025-07-20 17:42:34', 1),
(64, '::1', 'bordirmotif@gmail.com', 1, '2025-07-20 17:49:41', 1),
(65, '::1', 'bordirmotif@gmail.com', 1, '2025-07-20 17:49:43', 1),
(66, '::1', 'virayukia1234@gmail.com', NULL, '2025-07-20 18:07:33', 0),
(67, '::1', 'virayukia1234@gmail.com', 4, '2025-07-20 18:07:43', 1),
(68, '::1', 'virayukia1234@gmail.com', 4, '2025-07-20 18:47:45', 1),
(69, '::1', 'bordirmotif@gmail.com', 1, '2025-07-21 01:54:53', 1),
(70, '::1', 'kuronekochann123@gmail.com', NULL, '2025-07-21 03:45:27', 0),
(71, '::1', 'kuronekochann123@gmail.com', 20, '2025-07-21 03:45:49', 1),
(72, '::1', 'virayukia1234@gmail.com', 4, '2025-07-21 08:31:04', 1),
(73, '::1', 'bordirmotif@gmail.com', 1, '2025-07-21 09:00:22', 1),
(74, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-21 10:39:44', 1),
(75, '::1', 'virayukia1234@gmail.com', 4, '2025-07-21 14:09:50', 1),
(76, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-21 14:10:13', 1),
(77, '::1', 'virayukia1234@gmail.com', 4, '2025-07-21 20:17:17', 1),
(78, '::1', 'virayukia1234@gmail.com', 4, '2025-07-21 20:26:59', 1),
(79, '::1', 'virayukia1234@gmail.com', 4, '2025-07-21 23:34:51', 1),
(80, '::1', 'bordirmotif@gmail.com', 1, '2025-07-22 01:49:14', 1),
(81, '::1', 'virayukia1234@gmail.com', 4, '2025-07-22 02:00:08', 1),
(82, '::1', 'virayukia1234@gmail.com', 4, '2025-07-22 02:04:03', 1),
(83, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-22 02:22:53', 1),
(84, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-22 08:39:56', 1),
(85, '::1', 'bordirmotif@gmail.com', 1, '2025-07-22 08:40:35', 1),
(86, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-22 09:54:31', 1),
(87, '::1', 'bordirmotif@gmail.com', 1, '2025-07-22 14:00:18', 1),
(88, '::1', 'virayukia1234@gmail.com', 4, '2025-07-22 14:37:48', 1),
(89, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-22 15:17:07', 1),
(90, '::1', 'virayukia1234@gmail.com', 4, '2025-07-22 15:49:10', 1),
(91, '::1', 'bordirmotif@gmail.com', 1, '2025-07-22 19:58:59', 1),
(92, '::1', 'NUR.ANITA@sig.id', 23, '2025-07-22 22:29:26', 1),
(93, '::1', 'nur.anita@gmail.com', NULL, '2025-07-22 22:31:26', 0),
(94, '::1', 'NUR.ANITA@sig.id', 23, '2025-07-22 22:31:38', 1),
(95, '::1', 'NUR.ANITA@sig.id', 23, '2025-07-23 03:52:11', 1),
(96, '::1', 'NUR.ANITA@sig.id', 23, '2025-07-23 04:05:22', 1),
(97, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-23 05:18:44', 1),
(98, '::1', 'bordirmotif@gmail.com', 1, '2025-07-23 09:34:02', 1),
(99, '::1', 'virayukia1234@gmail.com', 4, '2025-07-23 14:48:29', 1),
(100, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-23 15:15:09', 1),
(101, '::1', 'virayukia1234@gmail.com', 4, '2025-07-23 15:19:51', 1),
(102, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-23 15:21:20', 1),
(103, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-31 09:33:26', 1),
(104, '::1', 'bordirmotif@gmail.com', 1, '2025-07-31 09:51:56', 1),
(105, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-31 11:52:08', 1),
(106, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-31 12:00:14', 1),
(107, '::1', 'kuronekochann123@gmail.com', 21, '2025-07-31 14:17:37', 1),
(108, '::1', 'bordirmotif@gmail.com', 1, '2025-07-31 14:33:54', 1),
(109, '::1', 'virayukia1234@gmail.com', 25, '2025-07-31 15:09:42', 1),
(110, '::1', 'virayukia1234@gmail.com', 26, '2025-07-31 15:31:48', 0),
(111, '::1', 'virayukia1234@gmail.com', 26, '2025-07-31 15:32:13', 1),
(112, '::1', 'bordirmotif@gmail.com', 1, '2025-08-04 08:33:20', 1),
(113, '::1', 'iqda.maulana@gmail.com', 29, '2025-08-04 11:22:24', 1),
(114, '::1', 'kuronekochann123@gmail.com', 21, '2025-08-04 15:02:07', 1),
(115, '::1', 'bordirmotif@gmail.com', 1, '2025-08-05 08:59:29', 1),
(116, '::1', 'bordirmotif@gmail.com', 1, '2025-08-05 13:28:06', 1),
(117, '::1', 'bordirmotif@gmail.com', 1, '2025-08-06 08:36:34', 1),
(118, '::1', 'bordirmotif@gmail.com', 1, '2025-08-06 08:36:36', 1),
(119, '::1', 'virayukia1234@gmail.com', 26, '2025-08-06 08:39:47', 1),
(120, '::1', 'bordirmotif@gmail.com', 1, '2025-08-06 14:02:54', 1),
(121, '::1', 'virayukia1234@gmail.com', 26, '2025-08-06 14:21:20', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `auth_permissions`
--

CREATE TABLE `auth_permissions` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `auth_reset_attempts`
--

CREATE TABLE `auth_reset_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `auth_reset_attempts`
--

INSERT INTO `auth_reset_attempts` (`id`, `email`, `ip_address`, `user_agent`, `token`, `created_at`) VALUES
(1, 'virayukia1234@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'f0b918019aaaf6f9cc0981b3e9c5df1d', '2025-07-15 12:03:52'),
(2, 'virayukia1234@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'f0b918019aaaf6f9cc0981b3e9c5df1d', '2025-07-15 12:07:27'),
(3, 'virayukia1234@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'f0b918019aaaf6f9cc0981b3e9c5df1d', '2025-07-15 12:07:46'),
(4, 'virayukia1234@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'f0b918019aaaf6f9cc0981b3e9c5df1d', '2025-07-15 12:08:50'),
(5, 'virayukia1234@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'f0b918019aaaf6f9cc0981b3e9c5df1d', '2025-07-15 13:40:34'),
(6, 'virayukia1234@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '6786ce0fea674702639be7eb502abc94', '2025-07-15 13:41:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `selector` varchar(255) NOT NULL,
  `hashedValidator` varchar(255) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `auth_users_permissions`
--

CREATE TABLE `auth_users_permissions` (
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `permission_id` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_jawaban_safety`
--

CREATE TABLE `detail_jawaban_safety` (
  `detail_jawaban_id` int(11) NOT NULL,
  `jawaban_safety_id` int(11) NOT NULL,
  `soal_safety_id` int(11) NOT NULL,
  `jawaban_user` varchar(255) NOT NULL,
  `benar` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `instansi`
--

CREATE TABLE `instansi` (
  `instansi_id` int(11) NOT NULL,
  `nama_instansi` varchar(255) NOT NULL,
  `tingkat` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `instansi`
--

INSERT INTO `instansi` (`instansi_id`, `nama_instansi`, `tingkat`) VALUES
(1, 'Akademi Akuntansi Indonesia', 'pt'),
(2, 'Akademi Komunitas Negeri Bengkalis', 'pt'),
(3, 'Akademi Komunitas Negeri Kota Sawahlunto', 'pt'),
(4, 'Akademi Komunitas Negeri Pesisir Selatan', 'pt'),
(5, 'Akademi Komunitas Negeri Tanah Datar', 'pt'),
(6, 'Binus University', 'pt'),
(7, 'Hochscule Bremen', 'pt'),
(8, 'IAIN Batusangkar', 'pt'),
(9, 'IAIN Imam Bonjol Padang', 'pt'),
(10, 'Institud Teknologi Nasional', 'pt'),
(11, 'Institut Agama Islam Negeri Bukittinggi', 'pt'),
(12, 'Institut Perbanas Jakarta', 'pt'),
(13, 'Institut Pertanian Bogor', 'pt'),
(14, 'Institut Seni Indonesia Padang Panjang', 'pt'),
(15, 'Institut Sepuluh Nopember', 'pt'),
(16, 'Institut Teknologi  Medan', 'pt'),
(17, 'Institut Teknologi  Padang', 'pt'),
(18, 'Institut Teknologi  Sumatera', 'pt'),
(19, 'Institut Teknologi Bandung', 'pt'),
(20, 'Institut Teknologi Dan Bisnis Haji Agus Salim', 'pt'),
(21, 'Institut Teknologi dan Sains Bandung', 'pt'),
(22, 'Institut Teknologi Del', 'pt'),
(23, 'Institut Teknologi Dirgantara Adisutjipto', 'pt'),
(24, 'Institut Teknologi Harapan Bangsa', 'pt'),
(25, 'Institut Teknologi Medan', 'pt'),
(26, 'Institut Teknologi Nasional', 'pt'),
(27, 'Institut Teknologi Nasional Yogyakarta', 'pt'),
(28, 'Institut Teknologi Padang', 'pt'),
(29, 'Institut Teknologi Padang', 'smk'),
(30, 'Institut Teknologi Petroleum Balongan', 'pt'),
(31, 'Institut Teknologi PLN', 'pt'),
(32, 'Institut Teknologi Sepuluh November', 'pt'),
(33, 'Institut Teknologi Sumatera', 'pt'),
(34, 'International Islamic University Malaysia', 'pt'),
(35, 'Kolej Universiti Islam Melaka (KUIM)', 'pt'),
(36, 'Lembaga Ilmu Pengetahuan Islam and Arab (LIPIA) Jakarta', 'pt'),
(37, 'National University Singapore', 'pt'),
(38, 'PGRI Sumatera Barat', 'pt'),
(39, 'PKPU Human Initiative', 'pt'),
(40, 'Politeknik Aceh', 'pt'),
(41, 'Politeknik Aisyiyah Sumbar', 'pt'),
(42, 'Politeknik AKA Bogor', 'pt'),
(43, 'Politeknik ATI Padang', 'pt'),
(44, 'Politeknik Caltex Riau', 'pt'),
(45, 'Politeknik Energi & Mineral Akamigas', 'pt'),
(46, 'Politeknik Jambi', 'pt'),
(47, 'Politeknik Kampar', 'pt'),
(48, 'Politeknik Kelapa Sawit', 'pt'),
(49, 'Politeknik Kesehatan Kemenkes Padang', 'pt'),
(50, 'Politeknik Ketenagakerjaan', 'pt'),
(51, 'Politeknik LP3I', 'pt'),
(52, 'Politeknik Manufaktur ', 'pt'),
(53, 'Politeknik Manufaktur Negeri Bangka Belitung', 'pt'),
(54, 'Politeknik Negeri Bali', 'pt'),
(55, 'Politeknik Negeri Bandung', 'pt'),
(56, 'Politeknik Negeri Banjarmasin', 'pt'),
(57, 'Politeknik Negeri Batam', 'pt'),
(58, 'Politeknik Negeri Bengkalis', 'pt'),
(59, 'Politeknik Negeri Jakarta', 'pt'),
(60, 'Politeknik Negeri Lhokseumawe', 'pt'),
(61, 'Politeknik Negeri Malang', 'pt'),
(62, 'Politeknik Negeri Medan', 'pt'),
(63, 'Politeknik Negeri Padang', 'pt'),
(64, 'Politeknik Negeri Semarang', 'pt'),
(65, 'Politeknik Negeri Sriwijaya', 'pt'),
(66, 'Politeknik Pertanian Negeri Payakumbuh', 'pt'),
(67, 'Politeknik Pos Indonesia', 'pt'),
(68, 'Politeknik Sriwijaya Siak', 'pt'),
(69, 'Politeknik Teknologi Kimia Industri Medan', 'pt'),
(70, 'Politeknik Teknologi Nuklir Indonesia', 'pt'),
(71, 'President University', 'pt'),
(72, 'Rochester University', 'pt'),
(73, 'Sampoerna University', 'pt'),
(74, 'Sekolah Menengah Analisis Kimia', 'smk'),
(75, 'Sekolah Tinggi Ekonomi Syariah', 'pt'),
(76, 'Sekolah Tinggi Ilmu Ekonomi AKBP', 'pt'),
(77, 'Sekolah Tinggi Ilmu Ekonomi Haji Agus Salim Bukittinggi', 'pt'),
(78, 'Sekolah Tinggi Ilmu Ekonomi KBP', 'pt'),
(79, 'Sekolah Tinggi Ilmu Ekonomi Sumatera Barat', 'pt'),
(80, 'Sekolah Tinggi Ilmu Ekonomi Sumbar', 'pt'),
(81, 'Sekolah Tinggi Ilmu Hukum', 'pt'),
(82, 'Sekolah Tinggi Ilmu Kesehatan Alifah Padang', 'pt'),
(83, 'Sekolah Tinggi Ilmu Kesehatan Fort De Kock', 'pt'),
(84, 'Sekolah Tinggi Ilmu Kesehatan Indonesia', 'pt'),
(85, 'Sekolah Tinggi Ilmu Kesehatan Syedza Saintika Padang', 'pt'),
(86, 'Sekolah Tinggi Keguruan & Ilmu Pendidikan PGRI Sumbar', 'pt'),
(87, 'Sekolah Tinggi Manajemen Logistik (STIMLOG) Indonesia', 'pt'),
(88, 'Sekolah Tinggi Teknologi Dumai', 'pt'),
(89, 'Sekolah Tinggi Teknologi Industri Padang', 'pt'),
(90, 'Sekolah Tinggi Teknologi Payakumbuh', 'pt'),
(91, 'SMK  Kosgoro 2 Payakumbuh', 'smk'),
(92, 'SMK  Teknologi Lengayang Pesel', 'smk'),
(93, 'SMK Adi Karya Ranah Pesisir', 'smk'),
(94, 'SMK Cendana Padang Panjang', 'smk'),
(95, 'SMK DEK', 'smk'),
(96, 'SMK Dhuafa Nusantara', 'smk'),
(97, 'SMK Dhuafa Padang', 'smk'),
(98, 'SMK Indonesia Raya', 'smk'),
(99, 'SMK Kartika 1 Padang', 'smk'),
(100, 'SMK Kartika 2 Padang', 'smk'),
(101, 'SMK Kosgoro 1 Bukittinggi', 'smk'),
(102, 'SMK Kosgoro 2 Padang', 'smk'),
(103, 'SMK LMC Model Industri', 'smk'),
(104, 'SMK LMC Tapanuli', 'smk'),
(105, 'SMK Migas Teknologi Riau', 'smk'),
(106, 'SMK Mitra Payakumbuh', 'smk'),
(107, 'SMK Muhammadiyah 1 Kota Malang', 'smk'),
(108, 'SMK Muhammadiyah 1 Padang', 'smk'),
(109, 'SMK Nasional Kayu Tanam', 'smk'),
(110, 'SMK Nasional Padang', 'smk'),
(111, 'SMK Negeri 1 Ampek Angkek', 'smk'),
(112, 'SMK Negeri 1 Ampek Nagari', 'smk'),
(113, 'SMK Negeri 1 Anyer', 'smk'),
(114, 'SMK Negeri 1 Baso', 'smk'),
(115, 'SMK Negeri 1 Batipuh', 'smk'),
(116, 'SMK Negeri 1 Batusangkar', 'smk'),
(117, 'SMK Negeri 1 Bonjol', 'smk'),
(118, 'SMK Negeri 1 Bukit Sundi', 'smk'),
(119, 'SMK Negeri 1 Bukittinggi', 'smk'),
(120, 'SMK Negeri 1 Enam Lingkung', 'smk'),
(121, 'SMK Negeri 1 Gunung Talang', 'smk'),
(122, 'SMK Negeri 1 Gunung Tuleh', 'smk'),
(123, 'SMK Negeri 1 Karimun', 'smk'),
(124, 'SMK Negeri 1 Kecamatan Guguak', 'smk'),
(125, 'SMK Negeri 1 Kecamatan Luak', 'smk'),
(126, 'SMK Negeri 1 Kinali', 'smk'),
(127, 'SMK Negeri 1 Koto Balingka', 'smk'),
(128, 'SMK Negeri 1 Koto Besar', 'smk'),
(129, 'SMK Negeri 1 Kuantan Mudik', 'smk'),
(130, 'SMK Negeri 1 Lembah Gumanti', 'smk'),
(131, 'SMK Negeri 1 Lembah Melintang', 'smk'),
(132, 'SMK Negeri 1 Lintau Buo', 'smk'),
(133, 'SMK Negeri 1 Luak', 'smk'),
(134, 'SMK Negeri 1 Lubuk Basung', 'smk'),
(135, 'SMK Negeri 1 Lubuk sikaping', 'smk'),
(136, 'SMK Negeri 1 Mandau Duri', 'smk'),
(137, 'SMK Negeri 1 Matur', 'smk'),
(138, 'SMK Negeri 1 Merangin', 'smk'),
(139, 'SMK Negeri 1 Padang', 'smk'),
(140, 'SMK Negeri 1 Padang Panjang', 'smk'),
(141, 'SMK Negeri 1 Painan', 'smk'),
(142, 'SMK Negeri 1 Pancung Soal', 'smk'),
(143, 'SMK Negeri 1 Pantai Cermin Kab. Solok', 'smk'),
(144, 'SMK Negeri 1 Pariaman', 'smk'),
(145, 'SMK Negeri 1 Pasaman', 'smk'),
(146, 'SMK Negeri 1 Payakumbuh', 'smk'),
(147, 'SMK Negeri 1 Rao Selatan', 'smk'),
(148, 'SMK Negeri 1 Sasak Ranah Pasisie', 'smk'),
(149, 'SMK Negeri 1 Sawahlunto', 'smk'),
(150, 'SMK Negeri 1 Sintuk Toboh', 'smk'),
(151, 'SMK Negeri 1 Solok', 'smk'),
(152, 'SMK Negeri 1 Solok Selatan', 'smk'),
(153, 'SMK Negeri 1 Sumatera Barat', 'smk'),
(154, 'SMK Negeri 1 Sungai Limau', 'smk'),
(155, 'SMK Negeri 1 Sungai Rumbai', 'smk'),
(156, 'SMK Negeri 1 Sutera', 'smk'),
(157, 'SMK Negeri 1 Tanjung Baru', 'smk'),
(158, 'SMK Negeri 1 Tanjung Raya', 'smk'),
(159, 'SMK Negeri 1 Tebo', 'smk'),
(160, 'SMK Negeri 1 Tilatang Kamang', 'smk'),
(161, 'SMK Negeri 1 Timpeh', 'smk'),
(162, 'SMK Negeri 1 Toboh Gadang', 'smk'),
(163, 'SMK Negeri 2 Batangtoru', 'smk'),
(164, 'SMK Negeri 2 Batusangkar', 'smk'),
(165, 'SMK Negeri 2 Bukittinggi', 'smk'),
(166, 'SMK Negeri 2 Dumai', 'smk'),
(167, 'SMK Negeri 2 Gunung Talang', 'smk'),
(168, 'SMK Negeri 2 Kalianda', 'smk'),
(169, 'SMK Negeri 2 Kecamatan Guguak', 'smk'),
(170, 'SMK Negeri 2 Padang', 'smk'),
(171, 'SMK Negeri 2 Padang Panjang', 'smk'),
(172, 'SMK Negeri 2 Painan', 'smk'),
(173, 'SMK Negeri 2 Pariaman', 'smk'),
(174, 'SMK Negeri 2 Payakumbuh', 'smk'),
(175, 'SMK Negeri 2 Pekanbaru', 'smk'),
(176, 'SMK Negeri 2 Sawahlunto', 'smk'),
(177, 'SMK Negeri 2 Sijunjung', 'smk'),
(178, 'SMK Negeri 2 Solok', 'smk'),
(179, 'SMK Negeri 2 Sumatera Barat', 'smk'),
(180, 'SMK Negeri 2 Sungai Penuh', 'smk'),
(181, 'SMK Negeri 3 Bungo', 'smk'),
(182, 'SMK Negeri 3 Mandau', 'smk'),
(183, 'SMK Negeri 3 Padang', 'smk'),
(184, 'SMK Negeri 3 Pariaman', 'smk'),
(185, 'SMK Negeri 3 Payakumbuh', 'smk'),
(186, 'SMK Negeri 3 Seikayu', 'smk'),
(187, 'SMK Negeri 3 Solok Selatan', 'smk'),
(188, 'SMK Negeri 4 Dumai', 'smk'),
(189, 'SMK Negeri 4 Padang', 'smk'),
(190, 'SMK Negeri 4 Pariaman', 'smk'),
(191, 'SMK Negeri 4 Payakumbuh', 'smk'),
(192, 'SMK Negeri 4 Sijunjung', 'smk'),
(193, 'SMK Negeri 5 Dumai', 'smk'),
(194, 'SMK Negeri 5 Padang', 'smk'),
(195, 'SMK Negeri 5 Solok Selatan', 'smk'),
(196, 'SMK Negeri 6 Dumai', 'smk'),
(197, 'SMK Negeri 6 Lingkung', 'smk'),
(198, 'SMK Negeri 6 Padang', 'smk'),
(199, 'SMK Negeri 6 Pekanbaru', 'smk'),
(200, 'SMK Negeri 8 Padang', 'smk'),
(201, 'SMK Negeri 9 Padang', 'smk'),
(202, 'SMK Nurussalam Dumai', 'smk'),
(203, 'SMK Pembangunan Bukittinggi', 'smk'),
(204, 'SMK Perbankan', 'smk'),
(205, 'SMK Pertanian Pembangunan', 'smk'),
(206, 'SMK Semen Padang', 'smk'),
(207, 'SMK SMAK Bogor', 'smk'),
(208, 'SMK SMAK Padang', 'smk'),
(209, 'SMK SMTI Banda Aceh', 'smk'),
(210, 'SMK SMTI Padang', 'smk'),
(211, 'SMK Taruna 1 Padang', 'smk'),
(212, 'SMK Taruna 2 Padang', 'smk'),
(213, 'SMK Teknologi Lengayang', 'smk'),
(214, 'SMK Teknologi Muhammadiyah Bukittinggi', 'smk'),
(215, 'SMK Telkom Pekanbaru', 'smk'),
(216, 'SMK Tri Dhsrms Kosgoro 2 Padang', 'smk'),
(217, 'SMK Yamatu Tualang', 'smk'),
(218, 'SMKF Ikasari Dumai', 'smk'),
(219, 'SMKS LMC Model Industri', 'smk'),
(220, 'SMTI Makassar', 'pt'),
(221, 'SMTI Padang', 'smk'),
(222, 'STBA', 'pt'),
(223, 'STEI Tazkia', 'pt'),
(224, 'STEMIK AMIK Jayanusa', 'pt'),
(225, 'STI Manajemen Logistik', 'pt'),
(226, 'STIA Adabiah', 'pt'),
(227, 'STIA LPPN Padang', 'pt'),
(228, 'STIM Yogyakarta', 'pt'),
(229, 'STKIP Nasional', 'pt'),
(230, 'STKIP PGRI Sumbar', 'pt'),
(231, 'STMI Bandung', 'pt'),
(232, 'STMIK Amik Riau', 'pt'),
(233, 'STMIK Indonesia', 'pt'),
(234, 'STMM MMTC Yogyakarta', 'pt'),
(235, 'Teknologi Kebumian & Energi', 'smk'),
(236, 'Telkom University', 'pt'),
(237, 'Tu-freiberg', 'pt'),
(238, 'Universitas Ageng Tirtayasa', 'pt'),
(239, 'Universitas Ahmad Dahlan', 'pt'),
(240, 'Universitas Airlangga', 'pt'),
(241, 'Universitas AKPRIND Indonesia', 'pt'),
(242, 'Universitas Alifah Padang', 'pt'),
(243, 'Universitas Andalas', 'pt'),
(244, 'Universitas Atmajaya Yogyakarta', 'pt'),
(245, 'Universitas Baiturrahmah', 'pt'),
(246, 'Universitas Bakrie', 'pt'),
(247, 'Universitas Bangka Belitung', 'pt'),
(248, 'Universitas Bengkulu', 'pt'),
(249, 'Universitas Bina Nusantara', 'pt'),
(250, 'Universitas Bina Sarana Informatika Jakarta', 'pt'),
(251, 'Universitas Brawijaya', 'pt'),
(252, 'Universitas Bung Hatta', 'pt'),
(253, 'Universitas Davis', 'pt'),
(254, 'Universitas Dharma Andalas', 'pt'),
(255, 'Universitas Dharma Indonesia', 'pt'),
(256, 'Universitas Dian Nusantara', 'pt'),
(257, 'Universitas Diponegoro', 'pt'),
(258, 'Universitas Ekasakti', 'pt'),
(259, 'Universitas Fort De Kock', 'pt'),
(260, 'Universitas Gadjah Mada', 'pt'),
(261, 'Universitas Gunadarma', 'pt'),
(262, 'Universitas Harapan Medan', 'pt'),
(263, 'Universitas Hasanuddin', 'pt'),
(264, 'Universitas Indonesia', 'pt'),
(265, 'Universitas International Semen Indonesia', 'pt'),
(266, 'Universitas Islam Bandung', 'pt'),
(267, 'Universitas Islam Indonesia', 'pt'),
(268, 'Universitas Islam Negeri  Imam Bonjol', 'pt'),
(269, 'Universitas Islam Negeri Batusangkar', 'pt'),
(270, 'Universitas Islam Negeri Imam Bonjol', 'pt'),
(271, 'Universitas Islam Negeri Imam Bonjol Padang', 'pt'),
(272, 'Universitas Islam Negeri Islam Negeri Mahmud Yunus Batusangkar', 'pt'),
(273, 'Universitas Islam Negeri Mahmud Yunus Batusangkar', 'pt'),
(274, 'Universitas Islam Negeri Raden Intan Lampung', 'pt'),
(275, 'Universitas Islam Negeri Sjech M Djamil Djambek', 'pt'),
(276, 'Universitas Islam Negeri Sjech M Jamil Djambek', 'pt'),
(277, 'Universitas Islam Negeri Sultan Syarif Kasim Riau', 'pt'),
(278, 'Universitas Islam Negeri Sumatera Utara', 'pt'),
(279, 'Universitas Islam Negeri Sunan Kalijaga', 'pt'),
(280, 'Universitas Islam Negeri Sunan Kalijaga  Yogyaakarta', 'pt'),
(281, 'Universitas Islam Negeri Syarif Hidayatullah', 'pt'),
(282, 'Universitas Islam Negeri Syarif Hidayatullah Jakarta', 'pt'),
(283, 'Universitas Islam Riau', 'pt'),
(284, 'Universitas Jambi', 'pt'),
(285, 'Universitas Jayabaya', 'pt'),
(286, 'Universitas Jenderal Achmad Yani', 'pt'),
(287, 'Universitas Jenderal Soedirman', 'pt'),
(288, 'Universitas Katolik Parahyangan', 'pt'),
(289, 'Universitas Kebangsaan Malaysia', 'pt'),
(290, 'Universitas Komputer indonesia (UNIKOM)', 'pt'),
(291, 'Universitas Kristen Krida Wacana', 'pt'),
(292, 'Universitas Lambung Mangkurat', 'pt'),
(293, 'Universitas Lampung', 'pt'),
(294, 'Universitas Lancang Kuning', 'pt'),
(295, 'Universitas Logistik dan Bisnis International', 'pt'),
(296, 'Universitas Mahaputra Muhammad Yamin', 'pt'),
(297, 'Universitas Malikussaleh', 'pt'),
(298, 'Universitas Merangin', 'pt'),
(299, 'Universitas Mercu Buana', 'pt'),
(300, 'Universitas Mohammad Natsir Bukittinggi', 'pt'),
(301, 'Universitas Muaro Bungo', 'pt'),
(302, 'Universitas Muhammadiyah Jakarta', 'pt'),
(303, 'Universitas Muhammadiyah M Yamin', 'pt'),
(304, 'Universitas Muhammadiyah Malang', 'pt'),
(305, 'Universitas Muhammadiyah Prof DR Hamka', 'pt'),
(306, 'Universitas Muhammadiyah Prof. DR Hamka', 'pt'),
(307, 'Universitas Muhammadiyah Riau', 'pt'),
(308, 'Universitas Muhammadiyah Sumabr', 'pt'),
(309, 'Universitas Muhammadiyah Sumatera Barat', 'pt'),
(310, 'Universitas Muhammadiyah SUMBAR', 'pt'),
(311, 'Universitas Muhammadiyah Surakarta', 'pt'),
(312, 'Universitas Muhammadiyah Yogyakarta', 'pt'),
(313, 'Universitas Mulawarman', 'pt'),
(314, 'Universitas Nahdlatul Ulama Sumbar', 'pt'),
(315, 'Universitas Nasional', 'pt'),
(316, 'Universitas Negeri Jakarta', 'pt'),
(317, 'Universitas Negeri Malang', 'pt'),
(318, 'Universitas Negeri Medan', 'pt'),
(319, 'Universitas Negeri Padang', 'pt'),
(320, 'Universitas Negeri Semarang', 'pt'),
(321, 'Universitas Negeri Surabaya', 'pt'),
(322, 'Universitas Negeri Syarif Hidayatullah', 'pt'),
(323, 'Universitas Negeri Yogyakarta', 'pt'),
(324, 'Universitas Nusa Putra', 'pt'),
(325, 'Universitas Padjadjaran', 'pt'),
(326, 'Universitas Palangka Raya', 'pt'),
(327, 'Universitas Pamulang', 'pt'),
(328, 'Universitas Pancasila', 'pt'),
(329, 'Universitas Pasir Pengaraian', 'pt'),
(330, 'Universitas Pembangunan Nasional', 'pt'),
(331, 'Universitas Pembangunan Nasional Veteran Jakarta', 'pt'),
(332, 'Universitas Pembangunan Nasional Veteran Yogyakarta', 'pt'),
(333, 'Universitas Pembangunan Veteran Jogjakarta', 'pt'),
(334, 'Universitas Pendidikan Indonesia Bandung', 'pt'),
(335, 'Universitas Perintis Indonesia', 'pt'),
(336, 'Universitas Perintis Kemerdekaan', 'pt'),
(337, 'Universitas Pertahanan RI', 'pt'),
(338, 'Universitas Pertamina', 'pt'),
(339, 'Universitas PGRI Sumatera Barat', 'pt'),
(340, 'Universitas Prof.Dr.Hazairin Bengkulu', 'pt'),
(341, 'Universitas Putra Indonesia', 'pt'),
(342, 'Universitas Rerbuka', 'pt'),
(343, 'Universitas Riau', 'pt'),
(344, 'Universitas Sampoerna', 'pt'),
(345, 'Universitas Sebelas Maret', 'pt'),
(346, 'Universitas Semen Indonesia', 'pt'),
(347, 'Universitas Serang', 'pt'),
(348, 'Universitas Singaperbangsa', 'pt'),
(349, 'Universitas Sriwijaya', 'pt'),
(350, 'Universitas Sultan Ageng Tiriayasa', 'pt'),
(351, 'Universitas Sultan Ageng Tirtayasa', 'pt'),
(352, 'Universitas Sumatera Barat', 'pt'),
(353, 'Universitas Sumatera Utara', 'pt'),
(354, 'Universitas Sunan Ampel', 'pt'),
(355, 'Universitas Syedza Saintika', 'pt'),
(356, 'Universitas Syiah Kuala', 'pt'),
(357, 'Universitas Taman Siswa', 'pt'),
(358, 'Universitas Teknologi Bandung', 'pt'),
(359, 'Universitas Teknologi Malaysia', 'pt'),
(360, 'Universitas Teknologi Yogyakarta', 'pt'),
(361, 'Universitas Telkom', 'pt'),
(362, 'Universitas Teuku Umar', 'pt'),
(363, 'Universitas Trisakti', 'pt'),
(364, 'Universitas Udayana', 'pt'),
(365, 'Universitas Universal Batam', 'pt'),
(366, 'Universitas Utara Malaysia', 'pt'),
(367, 'Unviersitas Ekasakti', 'pt'),
(368, 'UPI YPTK Padang', 'pt');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jawaban_safety`
--

CREATE TABLE `jawaban_safety` (
  `jawaban_id` int(11) NOT NULL,
  `magang_id` int(11) NOT NULL,
  `nilai` int(11) NOT NULL,
  `percobaan_ke` tinyint(4) NOT NULL,
  `tanggal_ujian` date NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurusan`
--

CREATE TABLE `jurusan` (
  `jurusan_id` int(11) NOT NULL,
  `nama_jurusan` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurusan`
--

INSERT INTO `jurusan` (`jurusan_id`, `nama_jurusan`) VALUES
(1, 'Administrasi Perkantoran'),
(2, 'Administrasi Publik'),
(3, 'Akuntansi'),
(4, 'Alat Berat'),
(5, 'Biologi'),
(6, 'Desain Komunikasi Visual'),
(7, 'Desain Permodelan Informasi Bangunan'),
(8, 'Fisika'),
(9, 'Fotografi'),
(10, 'Geofisika'),
(11, 'Geologi'),
(12, 'Hiperkes'),
(13, 'Hukum'),
(14, 'Ilmu Komunikasi'),
(15, 'Juru Ukur'),
(16, 'K3'),
(17, 'Kearsipan'),
(18, 'Kehutanan'),
(19, 'Kesehatan Masyarakat'),
(20, 'Kimia'),
(21, 'Manajemen'),
(22, 'Manajemen Informatika'),
(23, 'Manajemen Keuangan'),
(24, 'Manajemen Pajak'),
(25, 'Mekatronika'),
(26, 'Meteorologi'),
(27, 'Otomatisasi dan Tata Kelola Perkantoran'),
(28, 'Otomotif'),
(29, 'Pemasaran'),
(30, 'Pendidikan Teknik Mesin'),
(31, 'Penginderaan Jauh dan Sistem Informasi Geografis'),
(32, 'Perangkat Lunak'),
(33, 'Perikanan'),
(34, 'Pertanian'),
(35, 'Peternakan'),
(36, 'Psikologi'),
(37, 'Sistem Informasi'),
(38, 'Tata Boga'),
(42, 'Teknik Audio Video'),
(43, 'Teknik Biosistem'),
(44, 'Teknik Elektro'),
(45, 'Teknik Fisika'),
(46, 'Teknik Geodesi'),
(47, 'Teknik Industri'),
(48, 'Teknik Informatika'),
(49, 'Teknik Instalasi Tenaga Listrik'),
(50, 'Teknik Kebumian'),
(51, 'Teknik Kendaraan Ringan'),
(52, 'Teknik Kimia'),
(53, 'Teknik Komputer'),
(54, 'Teknik Komputer Jaringan'),
(55, 'Teknik Lingkungan'),
(56, 'Teknik Listrik'),
(57, 'Teknik Logistik'),
(58, 'Teknik Mesin'),
(59, 'Teknik Pendingan dan Tata Udara'),
(60, 'Teknik Pertambangan'),
(61, 'Teknik Sipil'),
(62, 'Teknologi Rekayasa Komputer');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurusan_unit`
--

CREATE TABLE `jurusan_unit` (
  `jurusan_unit_id` int(11) NOT NULL,
  `kuota_unit_id` int(11) NOT NULL,
  `jurusan_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurusan_unit`
--

INSERT INTO `jurusan_unit` (`jurusan_unit_id`, `kuota_unit_id`, `jurusan_id`) VALUES
(1, 1, 1),
(2, 1, 21),
(3, 2, 47),
(4, 3, 58),
(5, 3, 25),
(6, 4, 58),
(7, 4, 47),
(8, 5, 56),
(9, 5, 25),
(10, 6, 56),
(11, 7, 58),
(12, 7, 25),
(13, 8, 58),
(14, 9, 21),
(15, 9, 1),
(16, 9, 27),
(17, 10, 21),
(18, 10, 1),
(19, 11, 21),
(20, 11, 29),
(21, 11, 1),
(22, 11, 27),
(23, 12, 1),
(24, 12, 21),
(25, 13, 21),
(26, 13, 29),
(27, 13, 1),
(28, 13, 27),
(29, 14, 21),
(30, 14, 29),
(31, 14, 1),
(32, 15, 21),
(33, 15, 29),
(34, 15, 1),
(35, 15, 27),
(36, 16, 21),
(37, 16, 29),
(38, 16, 1),
(39, 17, 21),
(40, 17, 1),
(41, 18, 21),
(42, 18, 27),
(43, 18, 1),
(44, 19, 21),
(45, 19, 1),
(46, 19, 57),
(47, 20, 47),
(48, 20, 21),
(49, 20, 57),
(50, 21, 1),
(51, 21, 27),
(52, 22, 21),
(53, 22, 1),
(54, 23, 3),
(55, 23, 1),
(56, 23, 21),
(57, 23, 2),
(58, 23, 14),
(59, 23, 34),
(60, 23, 33),
(61, 24, 13),
(62, 25, 1),
(63, 25, 21),
(64, 25, 42),
(65, 25, 27),
(66, 26, 1),
(67, 26, 21),
(68, 26, 2),
(69, 26, 14),
(70, 26, 42),
(71, 26, 9),
(72, 26, 6),
(73, 26, 38),
(74, 26, 27),
(75, 27, 54),
(76, 27, 32),
(77, 28, 54),
(78, 28, 32),
(79, 28, 48),
(80, 28, 53),
(81, 28, 37),
(82, 28, 62),
(83, 29, 47),
(84, 29, 21),
(85, 29, 52),
(86, 29, 44),
(87, 30, 20),
(88, 30, 8),
(89, 31, 55),
(90, 31, 47),
(91, 32, 47),
(92, 33, 60),
(93, 33, 28),
(94, 33, 4),
(95, 33, 1),
(96, 34, 60),
(97, 34, 28),
(98, 34, 4),
(99, 34, 10),
(100, 34, 11),
(101, 34, 15),
(102, 34, 18),
(103, 34, 50),
(104, 34, 46),
(105, 34, 26),
(106, 34, 31),
(107, 35, 16),
(108, 35, 12),
(109, 35, 55),
(110, 35, 19),
(111, 36, 60),
(112, 37, 49),
(113, 37, 59),
(114, 38, 44),
(115, 38, 56),
(116, 38, 49),
(117, 38, 59),
(118, 39, 52),
(119, 40, 52),
(120, 41, 52),
(121, 42, 58),
(122, 42, 44),
(123, 42, 1),
(124, 42, 25),
(125, 43, 58),
(126, 43, 44),
(127, 43, 1),
(128, 44, 58),
(129, 44, 25),
(130, 45, 58),
(131, 46, 58),
(132, 46, 25),
(133, 47, 58),
(134, 48, 44),
(135, 49, 44),
(136, 49, 45),
(137, 50, 44),
(138, 51, 44),
(139, 51, 45),
(140, 52, 1),
(141, 52, 27),
(142, 53, 7),
(143, 54, 61),
(144, 54, 58),
(145, 54, 44),
(146, 55, 21),
(147, 55, 1),
(148, 55, 27),
(149, 56, 61),
(150, 56, 21),
(151, 56, 1),
(152, 57, 1),
(153, 57, 27),
(154, 58, 21),
(155, 58, 6),
(156, 59, 47),
(157, 59, 37),
(158, 59, 21),
(159, 59, 6),
(160, 60, 7),
(161, 61, 58),
(162, 61, 21),
(163, 62, 58),
(164, 62, 44),
(165, 62, 61),
(166, 63, 20),
(167, 64, 20),
(168, 64, 8),
(169, 65, 55),
(170, 65, 47),
(171, 65, 16),
(172, 65, 19),
(173, 65, 12),
(174, 65, 5),
(175, 65, 43),
(176, 66, 21),
(177, 66, 1),
(178, 66, 27),
(179, 67, 21),
(180, 67, 1),
(181, 67, 36),
(182, 67, 37),
(183, 68, 21),
(184, 68, 1),
(185, 68, 27),
(186, 69, 21),
(187, 69, 1),
(188, 69, 36),
(189, 69, 37),
(190, 69, 17),
(191, 70, 1),
(192, 70, 7),
(193, 70, 27),
(194, 71, 61),
(195, 71, 1),
(196, 72, 37),
(197, 72, 22),
(198, 73, 3),
(199, 73, 23),
(200, 73, 27),
(201, 73, 1),
(202, 74, 3),
(203, 74, 23),
(204, 75, 3),
(205, 75, 23),
(206, 75, 1),
(207, 75, 27),
(208, 76, 3),
(209, 76, 23),
(210, 77, 3),
(211, 77, 23),
(212, 77, 1),
(213, 77, 27),
(214, 78, 3),
(215, 78, 23),
(216, 78, 24),
(217, 79, 21),
(218, 79, 1),
(219, 79, 27),
(220, 79, 17),
(221, 80, 21),
(222, 80, 1),
(223, 80, 17),
(224, 80, 37),
(225, 81, 21),
(226, 81, 1),
(227, 81, 27),
(228, 82, 21),
(229, 82, 1),
(230, 83, 21),
(231, 83, 1),
(232, 83, 27),
(233, 84, 21),
(234, 84, 1),
(235, 85, 21),
(236, 85, 1),
(237, 85, 27),
(238, 85, 29),
(239, 86, 21),
(240, 86, 29),
(241, 86, 1),
(242, 87, 21),
(243, 87, 1),
(244, 88, 58),
(245, 88, 44),
(249, 89, 58),
(251, 89, 44),
(252, 90, 21),
(253, 91, 36),
(254, 91, 21),
(255, 91, 30),
(256, 92, 3),
(257, 93, 21),
(258, 93, 1),
(259, 94, 21),
(260, 94, 3),
(261, 94, 1),
(262, 94, 29),
(263, 94, 51),
(264, 94, 27),
(265, 95, 21),
(266, 95, 3),
(267, 95, 1),
(268, 95, 29),
(269, 95, 27),
(270, 95, 51),
(271, 96, 3),
(272, 97, 21),
(273, 97, 1),
(274, 97, 27),
(275, 98, 21),
(276, 98, 1),
(277, 98, 35),
(278, 99, 21),
(279, 99, 1),
(280, 100, 48),
(281, 100, 37),
(282, 100, 47),
(283, 101, 58),
(284, 102, 58),
(285, 102, 44),
(286, 103, 58),
(287, 104, 58),
(288, 104, 44),
(289, 105, 21),
(290, 105, 55),
(291, 106, 1),
(292, 106, 21),
(293, 106, 27);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kuota_unit`
--

CREATE TABLE `kuota_unit` (
  `kuota_unit_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `tingkat_pendidikan` varchar(100) NOT NULL,
  `kuota` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kuota_unit`
--

INSERT INTO `kuota_unit` (`kuota_unit_id`, `unit_id`, `tingkat_pendidikan`, `kuota`) VALUES
(1, 1, 'SMK', 0),
(2, 1, 'Perguruan Tinggi', 2),
(3, 2, 'SMK', 0),
(4, 2, 'Perguruan Tinggi', 0),
(5, 3, 'SMK', 0),
(6, 3, 'Perguruan Tinggi', 0),
(7, 4, 'SMK', 0),
(8, 4, 'Perguruan Tinggi', 0),
(9, 5, 'SMK', 2),
(10, 5, 'Perguruan Tinggi', 0),
(11, 7, 'SMK', 2),
(12, 7, 'Perguruan Tinggi', 0),
(13, 8, 'SMK', 2),
(14, 8, 'Perguruan Tinggi', 4),
(15, 9, 'SMK', 0),
(16, 9, 'Perguruan Tinggi', 1),
(17, 10, 'Perguruan Tinggi', 0),
(18, 11, 'SMK', 0),
(19, 11, 'Perguruan Tinggi', 2),
(20, 12, 'Perguruan Tinggi', 0),
(21, 13, 'SMK', 1),
(22, 13, 'Perguruan Tinggi', 0),
(23, 14, 'Perguruan Tinggi', 0),
(24, 15, 'Perguruan Tinggi', 1),
(25, 16, 'SMK', 0),
(26, 16, 'Perguruan Tinggi', 0),
(27, 17, 'SMK', 0),
(28, 17, 'Perguruan Tinggi', 0),
(29, 18, 'Perguruan Tinggi', 6),
(30, 19, 'Perguruan Tinggi', 12),
(31, 20, 'Perguruan Tinggi', 2),
(32, 21, 'Perguruan Tinggi', 5),
(33, 22, 'SMK', 0),
(34, 22, 'Perguruan Tinggi', 4),
(35, 23, 'Perguruan Tinggi', 0),
(36, 24, 'Perguruan Tinggi', 3),
(37, 25, 'SMK', 0),
(38, 25, 'Perguruan Tinggi', 0),
(39, 26, 'Perguruan Tinggi', 0),
(40, 27, 'Perguruan Tinggi', 0),
(41, 28, 'Perguruan Tinggi', 0),
(42, 29, 'SMK', 2),
(43, 29, 'Perguruan Tinggi', 0),
(44, 30, 'SMK', 1),
(45, 30, 'Perguruan Tinggi', 8),
(46, 31, 'SMK', 1),
(47, 31, 'SMK', 13),
(48, 32, 'SMK', 0),
(49, 32, 'Perguruan Tinggi', 0),
(50, 33, 'SMK', 0),
(51, 33, 'Perguruan Tinggi', 0),
(52, 34, 'SMK', 2),
(53, 35, 'SMK', 2),
(54, 35, 'Perguruan Tinggi', 4),
(55, 36, 'SMK', 2),
(56, 36, 'Perguruan Tinggi', 5),
(57, 37, 'SMK', 0),
(58, 38, 'SMK', 0),
(59, 38, 'Perguruan Tinggi', 3),
(60, 39, 'SMK', 2),
(61, 39, 'Perguruan Tinggi', 3),
(62, 40, 'Perguruan Tinggi', 4),
(63, 41, 'SMK', 5),
(64, 41, 'Perguruan Tinggi', 7),
(65, 42, 'Perguruan Tinggi', 0),
(66, 43, 'SMK', 0),
(67, 43, 'Perguruan Tinggi', 0),
(68, 44, 'SMK', 0),
(69, 44, 'Perguruan Tinggi', 0),
(70, 45, 'SMK', 0),
(71, 45, 'Perguruan Tinggi', 1),
(72, 46, 'Perguruan Tinggi', 0),
(73, 47, 'SMK', 0),
(74, 47, 'Perguruan Tinggi', 0),
(75, 48, 'SMK', 5),
(76, 48, 'Perguruan Tinggi', 0),
(77, 49, 'SMK', 3),
(78, 49, 'Perguruan Tinggi', 0),
(79, 50, 'SMK', 2),
(80, 50, 'Perguruan Tinggi', 0),
(81, 51, 'SMK', 2),
(82, 51, 'Perguruan Tinggi', 1),
(83, 52, 'SMK', 3),
(84, 52, 'Perguruan Tinggi', 2),
(85, 53, 'SMK', 0),
(86, 53, 'Perguruan Tinggi', 0),
(87, 54, 'Perguruan Tinggi', 5),
(88, 55, 'SMK', 12),
(89, 55, 'Perguruan Tinggi', 0),
(90, 56, 'Perguruan Tinggi', 3),
(91, 57, 'Perguruan Tinggi', 3),
(92, 58, 'Perguruan Tinggi', 1),
(93, 59, 'Perguruan Tinggi', 1),
(94, 60, 'SMK', 3),
(95, 60, 'Perguruan Tinggi', 0),
(96, 61, 'Perguruan Tinggi', 2),
(97, 62, 'SMK', 1),
(98, 62, 'Perguruan Tinggi', 2),
(99, 63, 'Perguruan Tinggi', 0),
(100, 64, 'Perguruan Tinggi', 4),
(101, 65, 'SMK', 6),
(102, 65, 'Perguruan Tinggi', 6),
(103, 66, 'SMK', 0),
(104, 66, 'Perguruan Tinggi', 0),
(105, 67, 'Perguruan Tinggi', 3),
(106, 68, 'SMK', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `magang`
--

CREATE TABLE `magang` (
  `magang_id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `unit_id` int(11) NOT NULL,
  `periode_id` int(11) NOT NULL,
  `durasi` int(11) NOT NULL,
  `tanggal_daftar` datetime NOT NULL,
  `status_seleksi` varchar(50) DEFAULT NULL,
  `tanggal_seleksi` datetime DEFAULT NULL,
  `status_konfirmasi` varchar(50) DEFAULT NULL,
  `tanggal_konfirmasi` datetime DEFAULT NULL,
  `status_validasi_berkas` varchar(50) DEFAULT NULL,
  `tanggal_validasi_berkas` datetime DEFAULT NULL,
  `status_berkas_lengkap` varchar(50) DEFAULT NULL,
  `tanggal_berkas_lengkap` datetime DEFAULT NULL,
  `cttn_berkas_lengkap` varchar(50) DEFAULT NULL,
  `tanggal_setujui_pernyataan` date DEFAULT NULL,
  `alasan_batal` varchar(150) DEFAULT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_akhir` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `magang`
--

INSERT INTO `magang` (`magang_id`, `user_id`, `unit_id`, `periode_id`, `durasi`, `tanggal_daftar`, `status_seleksi`, `tanggal_seleksi`, `status_konfirmasi`, `tanggal_konfirmasi`, `status_validasi_berkas`, `tanggal_validasi_berkas`, `status_berkas_lengkap`, `tanggal_berkas_lengkap`, `cttn_berkas_lengkap`, `tanggal_setujui_pernyataan`, `alasan_batal`, `tanggal_masuk`, `tanggal_selesai`, `status_akhir`) VALUES
(6, 26, 39, 1, 2, '2025-07-27 15:50:38', 'Diterima', '2025-07-31 15:52:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-01', '2025-10-31', 'proses');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2017-11-20-223112', 'App\\Database\\Migrations\\CreateAuthTables', 'default', 'App', 1751518934, 1),
(2, '2017-11-20-223112', 'Myth\\Auth\\Database\\Migrations\\CreateAuthTables', 'default', 'Myth\\Auth', 1751518934, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembimbing`
--

CREATE TABLE `pembimbing` (
  `pembimbing_id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `unit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penelitian`
--

CREATE TABLE `penelitian` (
  `penelitian_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `judul_penelitian` varchar(255) NOT NULL,
  `tanggal_daftar` datetime NOT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_akhir` varchar(50) NOT NULL,
  `deskripsi` text NOT NULL,
  `dosen_pembimbing` varchar(255) NOT NULL,
  `bidang` varchar(255) NOT NULL,
  `status_verifikasi` varchar(20) DEFAULT NULL,
  `tanggal_verifikasi` date DEFAULT NULL,
  `status_konfirmasi` varchar(20) DEFAULT NULL,
  `tanggal_konfirmasi` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penilaian`
--

CREATE TABLE `penilaian` (
  `penilaian_id` int(11) NOT NULL,
  `magang_id` int(11) NOT NULL,
  `nilai_disiplin` int(11) NOT NULL,
  `nilai_kerajinan` int(11) NOT NULL,
  `nilai_tingkahlaku` int(11) NOT NULL,
  `nilai_kerjasama` int(11) NOT NULL,
  `nilai_kreativitas` int(11) NOT NULL,
  `nilai_kemampuankerja` int(11) NOT NULL,
  `nilai_tanggungjawab` int(11) NOT NULL,
  `nilai_penyerapan` int(11) NOT NULL,
  `catatan` varchar(255) DEFAULT NULL,
  `tgl_penilaian` datetime NOT NULL,
  `approve_kaunit` tinyint(1) NOT NULL,
  `tgl_disetujui` datetime DEFAULT NULL,
  `approve_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `periode_magang`
--

CREATE TABLE `periode_magang` (
  `periode_id` int(11) NOT NULL,
  `tanggal_buka` date NOT NULL,
  `tanggal_tutup` date NOT NULL,
  `keterangan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `periode_magang`
--

INSERT INTO `periode_magang` (`periode_id`, `tanggal_buka`, `tanggal_tutup`, `keterangan`) VALUES
(1, '2025-07-02', '2025-07-28', ''),
(3, '2025-08-04', '2025-08-11', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `provinces`
--

CREATE TABLE `provinces` (
  `id` int(11) NOT NULL,
  `province` varchar(26) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `provinces`
--

INSERT INTO `provinces` (`id`, `province`) VALUES
(11, 'Aceh (NAD)'),
(12, 'Sumatera Utara'),
(13, 'Sumatera Barat'),
(14, 'Riau'),
(15, 'Jambi'),
(16, 'Sumatera Selatan'),
(17, 'Bengkulu'),
(18, 'Lampung'),
(19, 'Kepulauan Bangka Belitung'),
(21, 'Kepulauan Riau'),
(31, 'Dki Jakarta'),
(32, 'Jawa Barat'),
(33, 'Jawa Tengah'),
(34, 'Daerah Istimewa Yogyakarta'),
(35, 'Jawa Timur'),
(36, 'Banten'),
(51, 'Bali'),
(52, 'Nusa Tenggara Barat'),
(53, 'Nusa Tenggara Timur'),
(61, 'Kalimantan Barat'),
(62, 'Kalimantan Tengah'),
(63, 'Kalimantan Selatan'),
(64, 'Kalimantan Timur'),
(65, 'Kalimantan Utara'),
(71, 'Sulawesi Utara'),
(72, 'Sulawesi Tengah'),
(73, 'Sulawesi Selatan'),
(74, 'Sulawesi Tenggara'),
(75, 'Gorontalo'),
(76, 'Sulawesi Barat'),
(81, 'Maluku'),
(82, 'Maluku Utara'),
(91, 'Papua'),
(92, 'Papua Barat'),
(93, 'Papua Selatan'),
(94, 'Papua Tengah'),
(95, 'Papua Pegunungan'),
(96, 'Papua Barat Daya');

-- --------------------------------------------------------

--
-- Struktur dari tabel `regencies`
--

CREATE TABLE `regencies` (
  `id` int(11) NOT NULL,
  `province_id` int(11) NOT NULL,
  `regency` varchar(31) NOT NULL,
  `type` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `regencies`
--

INSERT INTO `regencies` (`id`, `province_id`, `regency`, `type`) VALUES
(1101, 11, 'Simeulue', 'Kabupaten'),
(1102, 11, 'Aceh Singkil', 'Kabupaten'),
(1103, 11, 'Aceh Selatan', 'Kabupaten'),
(1104, 11, 'Aceh Tenggara', 'Kabupaten'),
(1105, 11, 'Aceh Timur', 'Kabupaten'),
(1106, 11, 'Aceh Tengah', 'Kabupaten'),
(1107, 11, 'Aceh Barat', 'Kabupaten'),
(1108, 11, 'Aceh Besar', 'Kabupaten'),
(1109, 11, 'Pidie', 'Kabupaten'),
(1110, 11, 'Bireuen', 'Kabupaten'),
(1111, 11, 'Aceh Utara', 'Kabupaten'),
(1112, 11, 'Aceh Barat Daya', 'Kabupaten'),
(1113, 11, 'Gayo Lues', 'Kabupaten'),
(1114, 11, 'Aceh Tamiang', 'Kabupaten'),
(1115, 11, 'Nagan Raya', 'Kabupaten'),
(1116, 11, 'Aceh Jaya', 'Kabupaten'),
(1117, 11, 'Bener Meriah', 'Kabupaten'),
(1118, 11, 'Pidie Jaya', 'Kabupaten'),
(1171, 11, 'Banda Aceh', 'Kota'),
(1172, 11, 'Sabang', 'Kota'),
(1173, 11, 'Lhokseumawe', 'Kota'),
(1174, 11, 'Langsa', 'Kota'),
(1175, 11, 'Subulussalam', 'Kota'),
(1201, 12, 'Nias', 'Kabupaten'),
(1202, 12, 'Mandailing Natal', 'Kabupaten'),
(1203, 12, 'Tapanuli Selatan', 'Kabupaten'),
(1204, 12, 'Tapanuli Tengah', 'Kabupaten'),
(1205, 12, 'Tapanuli Utara', 'Kabupaten'),
(1206, 12, 'Toba Samosir', 'Kabupaten'),
(1207, 12, 'Labuhan Batu', 'Kabupaten'),
(1208, 12, 'Asahan', 'Kabupaten'),
(1209, 12, 'Simalungun', 'Kabupaten'),
(1210, 12, 'Dairi', 'Kabupaten'),
(1211, 12, 'Karo', 'Kabupaten'),
(1212, 12, 'Deliserdang', 'Kabupaten'),
(1213, 12, 'Langkat', 'Kabupaten'),
(1214, 12, 'Nias Selatan', 'Kabupaten'),
(1215, 12, 'Humbang Hasundutan', 'Kabupaten'),
(1216, 12, 'Pakpak Bharat', 'Kabupaten'),
(1217, 12, 'Samosir', 'Kabupaten'),
(1218, 12, 'Serdang Bedagai', 'Kabupaten'),
(1219, 12, 'Batu Bara', 'Kabupaten'),
(1220, 12, 'Padang Lawas Utara', 'Kabupaten'),
(1221, 12, 'Padang Lawas', 'Kabupaten'),
(1222, 12, 'Labuhan Batu Selatan', 'Kabupaten'),
(1223, 12, 'Labuhan Batu Utara', 'Kabupaten'),
(1224, 12, 'Nias Utara', 'Kabupaten'),
(1225, 12, 'Nias Barat', 'Kabupaten'),
(1271, 12, 'Sibolga', 'Kota'),
(1272, 12, 'Tanjung Balai', 'Kota'),
(1273, 12, 'Pematang Siantar', 'Kota'),
(1274, 12, 'Tebing Tinggi', 'Kota'),
(1275, 12, 'Medan', 'Kota'),
(1276, 12, 'Binjai', 'Kota'),
(1277, 12, 'Padangsidimpuan', 'Kota'),
(1278, 12, 'Gunungsitoli', 'Kota'),
(1301, 13, 'Kepulauan Mentawai', 'Kabupaten'),
(1302, 13, 'Pesisir Selatan', 'Kabupaten'),
(1303, 13, 'Solok', 'Kabupaten'),
(1304, 13, 'Sijunjung', 'Kabupaten'),
(1305, 13, 'Tanah Datar', 'Kabupaten'),
(1306, 13, 'Padang Pariaman', 'Kabupaten'),
(1307, 13, 'Agam', 'Kabupaten'),
(1308, 13, 'Lima Puluh Kota', 'Kabupaten'),
(1309, 13, 'Pasaman', 'Kabupaten'),
(1310, 13, 'Solok Selatan', 'Kabupaten'),
(1311, 13, 'Dharmasraya', 'Kabupaten'),
(1312, 13, 'Pasaman Barat', 'Kabupaten'),
(1371, 13, 'Padang', 'Kota'),
(1372, 13, 'Solok', 'Kota'),
(1373, 13, 'Sawah Lunto', 'Kota'),
(1374, 13, 'Padang Panjang', 'Kota'),
(1375, 13, 'Bukittinggi', 'Kota'),
(1376, 13, 'Payakumbuh', 'Kota'),
(1377, 13, 'Pariaman', 'Kota'),
(1401, 14, 'Kuantan Singingi', 'Kabupaten'),
(1402, 14, 'Indragiri Hulu', 'Kabupaten'),
(1403, 14, 'Indragiri Hilir', 'Kabupaten'),
(1404, 14, 'Pelalawan', 'Kabupaten'),
(1405, 14, 'S I A K', 'Kabupaten'),
(1406, 14, 'Kampar', 'Kabupaten'),
(1407, 14, 'Rokan Hulu', 'Kabupaten'),
(1408, 14, 'Bengkalis', 'Kabupaten'),
(1409, 14, 'Rokan Hilir', 'Kabupaten'),
(1410, 14, 'Kepulauan Meranti', 'Kabupaten'),
(1471, 14, 'Pekanbaru', 'Kota'),
(1473, 14, 'Dumai', 'Kota'),
(1501, 15, 'Kerinci', 'Kabupaten'),
(1502, 15, 'Merangin', 'Kabupaten'),
(1503, 15, 'Sarolangun', 'Kabupaten'),
(1504, 15, 'Batang Hari', 'Kabupaten'),
(1505, 15, 'Muaro Jambi', 'Kabupaten'),
(1506, 15, 'Tanjung Jabung Timur', 'Kabupaten'),
(1507, 15, 'Tanjung Jabung Barat', 'Kabupaten'),
(1508, 15, 'Tebo', 'Kabupaten'),
(1509, 15, 'Bungo', 'Kabupaten'),
(1571, 15, 'Jambi', 'Kota'),
(1572, 15, 'Sungai Penuh', 'Kota'),
(1601, 16, 'Ogan Komering Ulu', 'Kabupaten'),
(1602, 16, 'Ogan Komering Ilir', 'Kabupaten'),
(1603, 16, 'Muara Enim', 'Kabupaten'),
(1604, 16, 'Lahat', 'Kabupaten'),
(1605, 16, 'Musi Rawas', 'Kabupaten'),
(1606, 16, 'Musi Banyuasin', 'Kabupaten'),
(1607, 16, 'Banyuasin', 'Kabupaten'),
(1608, 16, 'Ogan Komering Ulu Selatan', 'Kabupaten'),
(1609, 16, 'Ogan Komering Ulu Timur', 'Kabupaten'),
(1610, 16, 'Ogan Ilir', 'Kabupaten'),
(1611, 16, 'Empat Lawang', 'Kabupaten'),
(1612, 16, 'Penukal Abab Lematang Ilir', 'Kabupaten'),
(1613, 16, 'Musi Rawas Utara', 'Kabupaten'),
(1671, 16, 'Palembang', 'Kota'),
(1672, 16, 'Prabumulih', 'Kota'),
(1673, 16, 'Pagar Alam', 'Kota'),
(1674, 16, 'Lubuklinggau', 'Kota'),
(1701, 17, 'Bengkulu Selatan', 'Kabupaten'),
(1702, 17, 'Rejang Lebong', 'Kabupaten'),
(1703, 17, 'Bengkulu Utara', 'Kabupaten'),
(1704, 17, 'Kaur', 'Kabupaten'),
(1705, 17, 'Seluma', 'Kabupaten'),
(1706, 17, 'Mukomuko', 'Kabupaten'),
(1707, 17, 'Lebong', 'Kabupaten'),
(1708, 17, 'Kepahiang', 'Kabupaten'),
(1709, 17, 'Bengkulu Tengah', 'Kabupaten'),
(1771, 17, 'Bengkulu', 'Kota'),
(1801, 18, 'Lampung Barat', 'Kabupaten'),
(1802, 18, 'Tanggamus', 'Kabupaten'),
(1803, 18, 'Lampung Selatan', 'Kabupaten'),
(1804, 18, 'Lampung Timur', 'Kabupaten'),
(1805, 18, 'Lampung Tengah', 'Kabupaten'),
(1806, 18, 'Lampung Utara', 'Kabupaten'),
(1807, 18, 'Way Kanan', 'Kabupaten'),
(1808, 18, 'Tulangbawang', 'Kabupaten'),
(1809, 18, 'Pesawaran', 'Kabupaten'),
(1810, 18, 'Pringsewu', 'Kabupaten'),
(1811, 18, 'Mesuji', 'Kabupaten'),
(1812, 18, 'Tulang Bawang Barat', 'Kabupaten'),
(1813, 18, 'Pesisir Barat', 'Kabupaten'),
(1871, 18, 'Bandar Lampung', 'Kota'),
(1872, 18, 'Metro', 'Kota'),
(1901, 19, 'Bangka', 'Kabupaten'),
(1902, 19, 'Belitung', 'Kabupaten'),
(1903, 19, 'Bangka Barat', 'Kabupaten'),
(1904, 19, 'Bangka Tengah', 'Kabupaten'),
(1905, 19, 'Bangka Selatan', 'Kabupaten'),
(1906, 19, 'Belitung Timur', 'Kabupaten'),
(1971, 19, 'Pangkal Pinang', 'Kota'),
(2101, 21, 'Karimun', 'Kabupaten'),
(2102, 21, 'Bintan', 'Kabupaten'),
(2103, 21, 'Natuna', 'Kabupaten'),
(2104, 21, 'Lingga', 'Kabupaten'),
(2105, 21, 'Kepulauan Anambas', 'Kabupaten'),
(2171, 21, 'Batam', 'Kota'),
(2172, 21, 'Tanjung Pinang', 'Kota'),
(3101, 31, 'Kepulauan Seribu', 'Kabupaten'),
(3171, 31, 'Jakarta Selatan', 'Kota'),
(3172, 31, 'Jakarta Timur', 'Kota'),
(3173, 31, 'Jakarta Pusat', 'Kota'),
(3174, 31, 'Jakarta Barat', 'Kota'),
(3175, 31, 'Jakarta Utara', 'Kota'),
(3201, 32, 'Bogor', 'Kabupaten'),
(3202, 32, 'Sukabumi', 'Kabupaten'),
(3203, 32, 'Cianjur', 'Kabupaten'),
(3204, 32, 'Bandung', 'Kabupaten'),
(3205, 32, 'Garut', 'Kabupaten'),
(3206, 32, 'Tasikmalaya', 'Kabupaten'),
(3207, 32, 'Ciamis', 'Kabupaten'),
(3208, 32, 'Kuningan', 'Kabupaten'),
(3209, 32, 'Cirebon', 'Kabupaten'),
(3210, 32, 'Majalengka', 'Kabupaten'),
(3211, 32, 'Sumedang', 'Kabupaten'),
(3212, 32, 'Indramayu', 'Kabupaten'),
(3213, 32, 'Subang', 'Kabupaten'),
(3214, 32, 'Purwakarta', 'Kabupaten'),
(3215, 32, 'Karawang', 'Kabupaten'),
(3216, 32, 'Bekasi', 'Kabupaten'),
(3217, 32, 'Bandung Barat', 'Kabupaten'),
(3218, 32, 'Pangandaran', 'Kabupaten'),
(3271, 32, 'Bogor', 'Kota'),
(3272, 32, 'Sukabumi', 'Kota'),
(3273, 32, 'Bandung', 'Kota'),
(3274, 32, 'Cirebon', 'Kota'),
(3275, 32, 'Bekasi', 'Kota'),
(3276, 32, 'Depok', 'Kota'),
(3277, 32, 'Cimahi', 'Kota'),
(3278, 32, 'Tasikmalaya', 'Kota'),
(3279, 32, 'Banjar', 'Kota'),
(3301, 33, 'Cilacap', 'Kabupaten'),
(3302, 33, 'Banyumas', 'Kabupaten'),
(3303, 33, 'Purbalingga', 'Kabupaten'),
(3304, 33, 'Banjarnegara', 'Kabupaten'),
(3305, 33, 'Kebumen', 'Kabupaten'),
(3306, 33, 'Purworejo', 'Kabupaten'),
(3307, 33, 'Wonosobo', 'Kabupaten'),
(3308, 33, 'Magelang', 'Kabupaten'),
(3309, 33, 'Boyolali', 'Kabupaten'),
(3310, 33, 'Klaten', 'Kabupaten'),
(3311, 33, 'Sukoharjo', 'Kabupaten'),
(3312, 33, 'Wonogiri', 'Kabupaten'),
(3313, 33, 'Karanganyar', 'Kabupaten'),
(3314, 33, 'Sragen', 'Kabupaten'),
(3315, 33, 'Grobogan', 'Kabupaten'),
(3316, 33, 'Blora', 'Kabupaten'),
(3317, 33, 'Rembang', 'Kabupaten'),
(3318, 33, 'Pati', 'Kabupaten'),
(3319, 33, 'Kudus', 'Kabupaten'),
(3320, 33, 'Jepara', 'Kabupaten'),
(3321, 33, 'Demak', 'Kabupaten'),
(3322, 33, 'Semarang', 'Kabupaten'),
(3323, 33, 'Temanggung', 'Kabupaten'),
(3324, 33, 'Kendal', 'Kabupaten'),
(3325, 33, 'Batang', 'Kabupaten'),
(3326, 33, 'Pekalongan', 'Kabupaten'),
(3327, 33, 'Pemalang', 'Kabupaten'),
(3328, 33, 'Tegal', 'Kabupaten'),
(3329, 33, 'Brebes', 'Kabupaten'),
(3371, 33, 'Magelang', 'Kota'),
(3372, 33, 'Surakarta', 'Kota'),
(3373, 33, 'Salatiga', 'Kota'),
(3374, 33, 'Semarang', 'Kota'),
(3375, 33, 'Pekalongan', 'Kota'),
(3376, 33, 'Tegal', 'Kota'),
(3401, 34, 'Kulon Progo', 'Kabupaten'),
(3402, 34, 'Bantul', 'Kabupaten'),
(3403, 34, 'Gunung Kidul', 'Kabupaten'),
(3404, 34, 'Sleman', 'Kabupaten'),
(3471, 34, 'Yogyakarta', 'Kota'),
(3501, 35, 'Pacitan', 'Kabupaten'),
(3502, 35, 'Ponorogo', 'Kabupaten'),
(3503, 35, 'Trenggalek', 'Kabupaten'),
(3504, 35, 'Tulungagung', 'Kabupaten'),
(3505, 35, 'Blitar', 'Kabupaten'),
(3506, 35, 'Kediri', 'Kabupaten'),
(3507, 35, 'Malang', 'Kabupaten'),
(3508, 35, 'Lumajang', 'Kabupaten'),
(3509, 35, 'Jember', 'Kabupaten'),
(3510, 35, 'Banyuwangi', 'Kabupaten'),
(3511, 35, 'Bondowoso', 'Kabupaten'),
(3512, 35, 'Situbondo', 'Kabupaten'),
(3513, 35, 'Probolinggo', 'Kabupaten'),
(3514, 35, 'Pasuruan', 'Kabupaten'),
(3515, 35, 'Sidoarjo', 'Kabupaten'),
(3516, 35, 'Mojokerto', 'Kabupaten'),
(3517, 35, 'Jombang', 'Kabupaten'),
(3518, 35, 'Nganjuk', 'Kabupaten'),
(3519, 35, 'Madiun', 'Kabupaten'),
(3520, 35, 'Magetan', 'Kabupaten'),
(3521, 35, 'Ngawi', 'Kabupaten'),
(3522, 35, 'Bojonegoro', 'Kabupaten'),
(3523, 35, 'Tuban', 'Kabupaten'),
(3524, 35, 'Lamongan', 'Kabupaten'),
(3525, 35, 'Gresik', 'Kabupaten'),
(3526, 35, 'Bangkalan', 'Kabupaten'),
(3527, 35, 'Sampang', 'Kabupaten'),
(3528, 35, 'Pamekasan', 'Kabupaten'),
(3529, 35, 'Sumenep', 'Kabupaten'),
(3571, 35, 'Kediri', 'Kota'),
(3572, 35, 'Blitar', 'Kota'),
(3573, 35, 'Malang', 'Kota'),
(3574, 35, 'Probolinggo', 'Kota'),
(3575, 35, 'Pasuruan', 'Kota'),
(3576, 35, 'Mojokerto', 'Kota'),
(3577, 35, 'Madiun', 'Kota'),
(3578, 35, 'Surabaya', 'Kota'),
(3579, 35, 'Batu', 'Kota'),
(3601, 36, 'Pandeglang', 'Kabupaten'),
(3602, 36, 'Lebak', 'Kabupaten'),
(3603, 36, 'Tangerang', 'Kabupaten'),
(3604, 36, 'Serang', 'Kabupaten'),
(3671, 36, 'Tangerang', 'Kota'),
(3672, 36, 'Cilegon', 'Kota'),
(3673, 36, 'Serang', 'Kota'),
(3674, 36, 'Tangerang Selatan', 'Kota'),
(5101, 51, 'Jembrana', 'Kabupaten'),
(5102, 51, 'Tabanan', 'Kabupaten'),
(5103, 51, 'Badung', 'Kabupaten'),
(5104, 51, 'Gianyar', 'Kabupaten'),
(5105, 51, 'Klungkung', 'Kabupaten'),
(5106, 51, 'Bangli', 'Kabupaten'),
(5107, 51, 'Karang Asem', 'Kabupaten'),
(5108, 51, 'Buleleng', 'Kabupaten'),
(5171, 51, 'Denpasar', 'Kota'),
(5201, 52, 'Lombok Barat', 'Kabupaten'),
(5202, 52, 'Lombok Tengah', 'Kabupaten'),
(5203, 52, 'Lombok Timur', 'Kabupaten'),
(5204, 52, 'Sumbawa', 'Kabupaten'),
(5205, 52, 'Dompu', 'Kabupaten'),
(5206, 52, 'Bima', 'Kabupaten'),
(5207, 52, 'Sumbawa Barat', 'Kabupaten'),
(5208, 52, 'Lombok Utara', 'Kabupaten'),
(5271, 52, 'Mataram', 'Kota'),
(5272, 52, 'Bima', 'Kota'),
(5301, 53, 'Sumba Barat', 'Kabupaten'),
(5302, 53, 'Sumba Timur', 'Kabupaten'),
(5303, 53, 'Kupang', 'Kabupaten'),
(5304, 53, 'Timor Tengah Selatan', 'Kabupaten'),
(5305, 53, 'Timor Tengah Utara', 'Kabupaten'),
(5306, 53, 'Belu', 'Kabupaten'),
(5307, 53, 'Alor', 'Kabupaten'),
(5308, 53, 'Flores Timur', 'Kabupaten'),
(5309, 53, 'Sikka', 'Kabupaten'),
(5310, 53, 'Ende', 'Kabupaten'),
(5311, 53, 'Ngada', 'Kabupaten'),
(5312, 53, 'Manggarai', 'Kabupaten'),
(5313, 53, 'Rote Ndao', 'Kabupaten'),
(5314, 53, 'Manggarai Barat', 'Kabupaten'),
(5315, 53, 'Sumba Tengah', 'Kabupaten'),
(5316, 53, 'Sumba Barat Daya', 'Kabupaten'),
(5317, 53, 'Nagekeo', 'Kabupaten'),
(5318, 53, 'Manggarai Timur', 'Kabupaten'),
(5319, 53, 'Sabu Raijua', 'Kabupaten'),
(5320, 53, 'Malaka', 'Kabupaten'),
(5371, 53, 'Kupang', 'Kota'),
(6101, 61, 'Sambas', 'Kabupaten'),
(6102, 61, 'Mempawah', 'Kabupaten'),
(6103, 61, 'Sanggau', 'Kabupaten'),
(6104, 61, 'Ketapang', 'Kabupaten'),
(6105, 61, 'Sintang', 'Kabupaten'),
(6106, 61, 'Kapuas Hulu', 'Kabupaten'),
(6107, 61, 'Bengkayang', 'Kabupaten'),
(6108, 61, 'Landak', 'Kabupaten'),
(6109, 61, 'Sekadau', 'Kabupaten'),
(6110, 61, 'Melawi', 'Kabupaten'),
(6111, 61, 'Kayong Utara', 'Kabupaten'),
(6112, 61, 'Kubu Raya', 'Kabupaten'),
(6171, 61, 'Pontianak', 'Kota'),
(6172, 61, 'Singkawang', 'Kota'),
(6201, 62, 'Kotawaringin Barat', 'Kabupaten'),
(6202, 62, 'Kotawaringin Timur', 'Kabupaten'),
(6203, 62, 'Kapuas', 'Kabupaten'),
(6204, 62, 'Barito Selatan', 'Kabupaten'),
(6205, 62, 'Barito Utara', 'Kabupaten'),
(6206, 62, 'Sukamara', 'Kabupaten'),
(6207, 62, 'Lamandau', 'Kabupaten'),
(6208, 62, 'Seruyan', 'Kabupaten'),
(6209, 62, 'Katingan', 'Kabupaten'),
(6210, 62, 'Pulang Pisau', 'Kabupaten'),
(6211, 62, 'Gunung Mas', 'Kabupaten'),
(6212, 62, 'Barito Timur', 'Kabupaten'),
(6213, 62, 'Murung Raya', 'Kabupaten'),
(6271, 62, 'Palangka Raya', 'Kota'),
(6301, 63, 'Tanah Laut', 'Kabupaten'),
(6302, 63, 'Kota Baru', 'Kabupaten'),
(6303, 63, 'Banjar', 'Kabupaten'),
(6304, 63, 'Barito Kuala', 'Kabupaten'),
(6305, 63, 'Tapin', 'Kabupaten'),
(6306, 63, 'Hulu Sungai Selatan', 'Kabupaten'),
(6307, 63, 'Hulu Sungai Tengah', 'Kabupaten'),
(6308, 63, 'Hulu Sungai Utara', 'Kabupaten'),
(6309, 63, 'Tabalong', 'Kabupaten'),
(6310, 63, 'Tanah Bumbu', 'Kabupaten'),
(6311, 63, 'Balangan', 'Kabupaten'),
(6371, 63, 'Banjarmasin', 'Kota'),
(6372, 63, 'Banjarbaru', 'Kota'),
(6401, 64, 'Paser', 'Kabupaten'),
(6402, 64, 'Kutai Barat', 'Kabupaten'),
(6403, 64, 'Kutai Kartanegara', 'Kabupaten'),
(6404, 64, 'Kutai Timur', 'Kabupaten'),
(6405, 64, 'Berau', 'Kabupaten'),
(6409, 64, 'Penajam Paser Utara', 'Kabupaten'),
(6471, 64, 'Balikpapan', 'Kota'),
(6472, 64, 'Samarinda', 'Kota'),
(6474, 64, 'Bontang', 'Kota'),
(6501, 65, 'Malinau', 'Kabupaten'),
(6502, 65, 'Bulungan', 'Kabupaten'),
(6503, 65, 'Tana Tidung', 'Kabupaten'),
(6504, 65, 'Nunukan', 'Kabupaten'),
(6571, 65, 'Tarakan', 'Kota'),
(7101, 71, 'Bolaang Mongondow', 'Kabupaten'),
(7102, 71, 'Minahasa', 'Kabupaten'),
(7103, 71, 'Kepulauan Sangihe', 'Kabupaten'),
(7104, 71, 'Kepulauan Talaud', 'Kabupaten'),
(7105, 71, 'Minahasa Selatan', 'Kabupaten'),
(7106, 71, 'Minahasa Utara', 'Kabupaten'),
(7107, 71, 'Bolaang Mongondow Utara', 'Kabupaten'),
(7108, 71, 'Siau Tagulandang Biaro (Sitaro)', 'Kabupaten'),
(7109, 71, 'Minahasa Tenggara', 'Kabupaten'),
(7110, 71, 'Bolaang Mongondow Selatan', 'Kabupaten'),
(7111, 71, 'Bolaang Mongondow Timur', 'Kabupaten'),
(7171, 71, 'Manado', 'Kota'),
(7172, 71, 'Bitung', 'Kota'),
(7173, 71, 'Tomohon', 'Kota'),
(7174, 71, 'Kotamobagu', 'Kota'),
(7201, 72, 'Banggai', 'Kabupaten'),
(7202, 72, 'Poso', 'Kabupaten'),
(7203, 72, 'Donggala', 'Kabupaten'),
(7204, 72, 'Toli-Toli', 'Kabupaten'),
(7205, 72, 'Buol', 'Kabupaten'),
(7206, 72, 'Morowali', 'Kabupaten'),
(7207, 72, 'Banggai Kepulauan', 'Kabupaten'),
(7208, 72, 'Parigi Moutong', 'Kabupaten'),
(7209, 72, 'Tojo Una-Una', 'Kabupaten'),
(7210, 72, 'Sigi', 'Kabupaten'),
(7271, 72, 'Palu', 'Kota'),
(7301, 73, 'Kepulauan Selayar', 'Kabupaten'),
(7302, 73, 'Bulukumba', 'Kabupaten'),
(7303, 73, 'Bantaeng', 'Kabupaten'),
(7304, 73, 'Jeneponto', 'Kabupaten'),
(7305, 73, 'Takalar', 'Kabupaten'),
(7306, 73, 'Gowa', 'Kabupaten'),
(7307, 73, 'Sinjai', 'Kabupaten'),
(7308, 73, 'Bone', 'Kabupaten'),
(7309, 73, 'Maros', 'Kabupaten'),
(7310, 73, 'Pangkajene Dan Kepulauan', 'Kabupaten'),
(7311, 73, 'Barru', 'Kabupaten'),
(7312, 73, 'Soppeng', 'Kabupaten'),
(7313, 73, 'Wajo', 'Kabupaten'),
(7314, 73, 'Sidenreng Rappang', 'Kabupaten'),
(7315, 73, 'Pinrang', 'Kabupaten'),
(7316, 73, 'Enrekang', 'Kabupaten'),
(7317, 73, 'Luwu', 'Kabupaten'),
(7318, 73, 'Tana Toraja', 'Kabupaten'),
(7322, 73, 'Luwu Utara', 'Kabupaten'),
(7325, 73, 'Luwu Timur', 'Kabupaten'),
(7326, 73, 'Toraja Utara', 'Kabupaten'),
(7371, 73, 'Makassar', 'Kota'),
(7372, 73, 'Parepare', 'Kota'),
(7373, 73, 'Palu', 'Kota'),
(7401, 74, 'Buton', 'Kabupaten'),
(7402, 74, 'Muna', 'Kabupaten'),
(7403, 74, 'Konawe', 'Kabupaten'),
(7404, 74, 'Kolaka', 'Kabupaten'),
(7405, 74, 'Konawe Selatan', 'Kabupaten'),
(7406, 74, 'Bombana', 'Kabupaten'),
(7407, 74, 'Wakatobi', 'Kabupaten'),
(7408, 74, 'Kolaka Utara', 'Kabupaten'),
(7409, 74, 'Buton Utara', 'Kabupaten'),
(7410, 74, 'Konawe Utara', 'Kabupaten'),
(7411, 74, 'Kolaka Timur', 'Kabupaten'),
(7412, 74, 'Konawe Kepulauan', 'Kabupaten'),
(7413, 74, 'Muna Barat', 'Kabupaten'),
(7414, 74, 'Buton Tengah', 'Kabupaten'),
(7415, 74, 'Buton Selatan', 'Kabupaten'),
(7471, 74, 'Kendari', 'Kota'),
(7472, 74, 'Baubau', 'Kota'),
(7501, 75, 'Boalemo', 'Kabupaten'),
(7502, 75, 'Gorontalo', 'Kabupaten'),
(7503, 75, 'Pohuwato', 'Kabupaten'),
(7504, 75, 'Bone Bolango', 'Kabupaten'),
(7505, 75, 'Gorontalo Utara', 'Kabupaten'),
(7571, 75, 'Gorontalo', 'Kota'),
(7601, 76, 'Majene', 'Kabupaten'),
(7602, 76, 'Polewali Mandar', 'Kabupaten'),
(7603, 76, 'Mamasa', 'Kabupaten'),
(7604, 76, 'Mamuju', 'Kabupaten'),
(7605, 76, 'Mamuju Utara', 'Kabupaten'),
(7606, 76, 'Mamuju Tengah', 'Kabupaten'),
(8101, 81, 'Maluku Tenggara Barat', 'Kabupaten'),
(8102, 81, 'Maluku Tenggara', 'Kabupaten'),
(8103, 81, 'Maluku Tengah', 'Kabupaten'),
(8104, 81, 'Buru', 'Kabupaten'),
(8105, 81, 'Kepulauan Aru', 'Kabupaten'),
(8106, 81, 'Seram Bagian Barat', 'Kabupaten'),
(8107, 81, 'Seram Bagian Timur', 'Kabupaten'),
(8108, 81, 'Maluku Barat Daya', 'Kabupaten'),
(8109, 81, 'Buru Selatan', 'Kabupaten'),
(8171, 81, 'Ambon', 'Kota'),
(8172, 81, 'Tual', 'Kota'),
(8201, 82, 'Halmahera Barat', 'Kabupaten'),
(8202, 82, 'Halmahera Tengah', 'Kabupaten'),
(8203, 82, 'Kepulauan Sula', 'Kabupaten'),
(8204, 82, 'Halmahera Selatan', 'Kabupaten'),
(8205, 82, 'Halmahera Utara', 'Kabupaten'),
(8206, 82, 'Halmahera Timur', 'Kabupaten'),
(8207, 82, 'Pulau Morotai', 'Kabupaten'),
(8208, 82, 'Pulau Taliabu', 'Kabupaten'),
(8271, 82, 'Ternate', 'Kota'),
(8272, 82, 'Tidore Kepulauan', 'Kota'),
(9116, 91, 'Sarmi', 'Kabupaten'),
(9117, 91, 'Keerom', 'Kabupaten'),
(9118, 91, 'Waropen', 'Kabupaten'),
(9119, 91, 'Supiori', 'Kabupaten'),
(9120, 91, 'Mamberamo Raya', 'Kabupaten'),
(9171, 91, 'Jayapura', 'Kota'),
(9201, 92, 'Fakfak', 'Kabupaten'),
(9202, 92, 'Kaimana', 'Kabupaten'),
(9203, 92, 'Teluk Wondama', 'Kabupaten'),
(9204, 92, 'Teluk Bintuni', 'Kabupaten'),
(9205, 92, 'Manokwari', 'Kabupaten'),
(9206, 92, 'Sorong Selatan', 'Kabupaten'),
(9207, 92, 'Sorong', 'Kabupaten'),
(9208, 92, 'Raja Ampat', 'Kabupaten'),
(9209, 92, 'Tambrauw', 'Kabupaten'),
(9210, 92, 'Maybrat', 'Kabupaten'),
(9211, 92, 'Manokwari Selatan', 'Kabupaten'),
(9212, 92, 'Pegunungan Arfak', 'Kabupaten'),
(9271, 92, 'Sorong', 'Kota'),
(9301, 93, 'Asmat', 'Kabupaten'),
(9302, 93, 'Boven Digoel', 'Kabupaten'),
(9303, 93, 'Mappi', 'Kabupaten'),
(9304, 93, 'Merauke', 'Kabupaten'),
(9401, 94, 'Deiyai (Deliyai)', 'Kabupaten'),
(9402, 94, 'Dogiyai', 'Kabupaten'),
(9403, 94, 'Intan Jaya', 'Kabupaten'),
(9404, 94, 'Mimika', 'Kabupaten'),
(9408, 94, 'Nabire', 'Kabupaten'),
(9409, 94, 'Paniai', 'Kabupaten'),
(9410, 94, 'Puncak', 'Kabupaten'),
(9411, 94, 'Puncak Jaya', 'Kabupaten'),
(9501, 95, 'Jayawijaya', 'Kabupaten'),
(9502, 95, 'Lanny Jaya', 'Kabupaten'),
(9503, 95, 'Mamberamo Tengah', 'Kabupaten'),
(9504, 95, 'Nduga', 'Kabupaten'),
(9505, 95, 'Pegunungan Bintang', 'Kabupaten'),
(9506, 95, 'Tolikara', 'Kabupaten'),
(9507, 95, 'Yahukimo', 'Kabupaten'),
(9508, 95, 'Yalimo', 'Kabupaten'),
(9601, 96, 'Maybrat', 'Kabupaten'),
(9602, 96, 'Raja Ampat', 'Kabupaten'),
(9603, 96, 'Sorong', 'Kabupaten'),
(9604, 96, 'Sorong Selatan', 'Kabupaten'),
(9605, 96, 'Tambrauw', 'Kabupaten'),
(9606, 96, 'Sorong', 'Kota');

-- --------------------------------------------------------

--
-- Struktur dari tabel `soal_safety`
--

CREATE TABLE `soal_safety` (
  `soal_id` int(11) NOT NULL,
  `pertanyaan` varchar(255) NOT NULL,
  `opsi_a` varchar(255) NOT NULL,
  `opsi_b` varchar(255) NOT NULL,
  `opsi_c` varchar(255) NOT NULL,
  `opsi_d` varchar(255) NOT NULL,
  `jawaban_benar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `soal_safety`
--

INSERT INTO `soal_safety` (`soal_id`, `pertanyaan`, `opsi_a`, `opsi_b`, `opsi_c`, `opsi_d`, `jawaban_benar`) VALUES
(1, 'Yang merupakan definisi BAHAYA adalah', 'Tindakan-tindakan yang tidak aman dan berbahaya bagi para pekerja.', 'Sumber atau keadaan yang berpotensi terjadinya kerugian dalam bentuk cidera atau kerusakan harta benda.', 'Kondisi-kondisi yang tidak aman dan berbahaya bagi para pekerja.', 'Tersandung kabel lalu terjatuh', 'b'),
(2, 'Yang harus diperhatikan dalam penanganan bahan mudah terbakar adalah', 'Memastikan bahan mudah terbakar dalam keadaan penuh', 'Pastikan bahan mudah terbakar dan bisa terbakar mempunyai izin', 'Pastikan bahan mudah dan bisa terbakar ditempatkan jauh dari sumber nyala', 'Pekerjaan panas bisa dilakukan dimana saja tidak hanya di workshop', 'c'),
(3, 'Kecepatan maksimal kendaraan untuk area pabrik dan emplasement adalah ... (km/jam)', '30 dan 40', '25 dan 40', '20 dan 30', '35 dan 45', 'b'),
(4, 'Pemilahan Sampah menurut Warna Tempat Sampahnya ini benar kecuali', 'Sampah Organik – Hijau', 'Sampah Lain-lain – Biru', 'Limbah B3- Kuning', 'Sampah Anorganik - Merah', 'c'),
(5, 'Berikut ini adalah tanggung jawab seorang Pengawas pekerjaan di lapangan, kecuali', 'Melaksanakan pekerjaan yang beresiko tanpa menyediakan pengendalian bahayanya yang memadai', 'Memastikan anggota kelompok kerja telah menggunakan Alat Pelindung Diri yang sesuai', 'Memastikan semua anggota dalam pengawasannya akan selamat dan tidak terpapar oleh bahaya apapun', 'Harus menyiapkan perencanaan pekerjaan dengan memperhatikan keselamatan sebelum memulai pekerjaan', 'a'),
(6, 'Seorang operator sedang membersihkan bagian atas konveyor material saat mesin masih berjalan. Dia hampir tersandung kabel listrik yang tergeletak di dekatnya. Kejadian ini termasuk dalam kategori', 'Kondisi tidak aman', 'Hampir celaka (Nearmiss)', 'Tindakan tidak aman', 'Kecelakaan', 'b'),
(7, 'Berikut adalah salah satu hierarki pengendalian bahaya yang menjadi pembatas antara risiko ke manusia dan merupakan upaya terakhir dari urutan pencegahan kecelakaan. Hierarki kontrol safety tersebut adalah', 'Engineering', 'Administrasi', 'Alat Pelindung Diri (APD)', 'Substitusi', 'c'),
(8, 'Dibawah ini merupakan hal-hal yang harus dilakukan sebelum memasuki confined space/ruang terbatas, kecuali', 'Memeriksa kondisi cuaca di luar ruangan', 'Memastikan peralatan pelindung diri sudah terpasang dengan benar', 'Mengkonfirmasi izin masuk dari atasan', 'Memastikan jalur keluar masuk confined space aman', 'a'),
(9, 'Tujuan dari Safety Induction/Induksi Keselamatan adalah', 'Mengkomunikasikan bahaya-bahaya keselamatan dan kesehatan kerja umum di lingkungan PT Semen Padang', 'Untuk mengetahui profil perusahaan', 'Untuk mengetahui prosedur kerja', 'Memahami proses produksi', 'a'),
(10, 'Untuk bekerja di ketinggian lebih dari 1,8 meter maka pekerja wajib menggunakan', 'full body harness double lanyard', 'Seat harness', 'full body harness', 'safety belt', 'a'),
(11, 'Rata-rata pekerja yang terjatuh dari ketinggian dikarenakan oleh', 'Tidak tersedianya pengaman yang cukup untuk mencegah jatuh', 'Alat pelindung diri yang dipakai tidak layak pakai', 'Kelalaian pekerja tidak memakai alat pelindung diri', 'A, B, C benar', 'd'),
(12, 'Pekerja yang akan mengoperasikan alat angkat angkut wajib memiliki', 'Surat Izin Operator', 'Surat Izin Mengemudi', 'Surat Izin Bekerja', 'Surat Izin Masuk Pabrik', 'a'),
(13, 'Scaffolding/perancah yang telah diinspeksi oleh inspektor scaffolding dan diberi tanda tag hijau, menunjukan bahwa scaffolding tersebut', 'Sudah layak digunakan', 'Belum bisa digunakan', 'Perlu perbaikan segera', 'Segera dihentikan pendiriannya', 'a'),
(14, 'Bila anda menggunakan peralatan kerja, anda harus memastikan alat tersebut', 'Alat harus bisa menyala', 'Alat harus dalam kondisi baru', 'Perlengkapan dalam kondisi ber merk', 'Dalam kondisi aman dan sudah dilakukan inspeksi', 'd'),
(15, 'Setiap bagian mesin yang bergerak harus selalu dalam keadaan terlindungi dari potensi tersentuh, sehingga dibutuhkan pengaman mesin yang berfungsi untuk', 'Menghindari agar mesin tidak dilintasi oleh pekerja', 'Mengurangi risiko, semua mesin harus dipastikan memiliki pelindung untuk mencegah masuknya seluruh atau bagian tubuh kita ke jalur bagian yang berbahaya', 'Melindungi mesin dari faktor fisik lingkungan agar tidak mudah rusak', 'Memenuhi SOP dalam menjalankan mesin bergerak', 'b'),
(16, 'Untuk menghindari kecelakaan kerja terhadap personil yang berada di lokasi atau area kerja pada saat dilakukan aktivitas perbaikan, pemeliharaan dan untuk memutus sumber energi yang tersimpan atau yang dihasilkan oleh peralatan tersebut melalui tahapan is', 'LOTOTO', 'Hasp Lock', 'Lock Box', 'Pad Lock', 'a'),
(17, 'Dibawah ini cara yang benar ketika menemukan kecelakaan kerja, kecuali', 'Mengecek respon kesadaran korban dengan memanggil', 'Memastikan kondisi aman dari segala aspek bahaya', 'Berteriak dan melaporkan kejadian segera', 'Langsung mengangkat korban tanpa keahlian', 'd'),
(18, 'Ketika terjadi keadaan darurat yang harus dilakukan oleh pekerja adalah', 'Melakukan panggilan darurat (emergency call)', 'Melarikan diri dan mencari pintu darurat', 'Mencari tempat yang aman', 'Semua Benar', 'd'),
(19, 'Warna helm khusus untuk kontraktor adalah', 'Biru', 'Kuning', 'Putih', 'Merah', 'b'),
(20, 'Saat akan melakukan pekerjaan di tempat-tempat berbahaya, harus mendapatkan', 'Form HIRADC', 'Surat izin masuk pabrik', 'Izin kerja berbahaya', 'Surat keterangan kesehatan', 'c'),
(21, 'Dibawah ini yang merupakan komitmen manajemen dalam bidang keselamatan dan kesehatan kerja, kecuali', 'Menyediakan kondisi lingkungan kerja yang sehat, selamat dan aman', 'Membuat proses produksi menjadi menyenangkan', 'Menjalankan bisnis kami dengan tujuan zero fatality', 'Mematuhi persyaratan dan peraturan perundang-undangan yang berlaku', 'b'),
(22, 'Prosedur LOTOTO merupakan prosedur penguncian secara sistematis dimana sarana untuk menjalankan mesin dimatikan dan dikunci. Tujuan dari LOTOTO tersebut adalah', 'Memastikan fungsi gembok sesuai dengan warna yang digunakan', 'Untuk memastikan alat tersebut dapat digerakkan dengan bantuan seseorang ataupun bisa digerakkan secara otomatis', 'Memastikan peralatan tersebut bisa sewaktu-waktu dapat dijalankan dengan baik oleh pekerja', 'Untuk memastikan bahwa peralatan tersebut tidak dapat dijalankan oleh seseorang ataupun berjalan sendiri akibat adanya energi yang masih tersisa', 'd'),
(23, 'Penting bagi pekerja untuk melaporkan insiden atau kecelakaan, hal ini perlu dilakukan agar', 'Untuk memperoleh kompensasi finansial', 'Untuk menghindari sangsi dari manajemen', 'Untuk mencegah kecelakaan serupa terjadi di masa depan', 'Untuk menghindari bertanggung jawab atas kejadian tersebut', 'c'),
(24, 'Bekerja menggunakan peralatan listrik atau bekerja di dekat peralatan/instalasi listrik sebelum memulai pekerjaan harus dilakukan inspeksi terlebih dahulu yang dilakukan oleh', 'Pekerja yang akan melakukan pekerjaan', 'Managemen K3 dari PT Semen Padang', 'Kontraktor yang bertanggung jawab di area tersebut', 'Petugas yang ditunjuk manajemen PT Semen Padang dan memiliki keahlian khusus dibidang K3 listrik', 'd'),
(25, 'Dibawah ini yang merupakan sumber bahaya adalah', 'Kondisi Tidak Aman & Tindakan Tidak Aman', 'Kondisi-kondisi yang tidak aman dan berbahaya bagi para pekerja', 'Akibat gangguan kesehatan, kerusakan property atau bahkan kematian', 'Tindakan-tindakan yang tidak aman dan berbahaya bagi para pekerja', 'a');

-- --------------------------------------------------------

--
-- Struktur dari tabel `unit_kerja`
--

CREATE TABLE `unit_kerja` (
  `unit_id` int(11) NOT NULL,
  `unit_kerja` varchar(150) NOT NULL,
  `nama_pimpinan` varchar(100) DEFAULT NULL,
  `email_pimpinan` varchar(150) DEFAULT NULL,
  `safety` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `catatan` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `unit_kerja`
--

INSERT INTO `unit_kerja` (`unit_id`, `unit_kerja`, `nama_pimpinan`, `email_pimpinan`, `safety`, `active`, `catatan`) VALUES
(1, 'Bisnis Inkubasi Non Semen', 'Akhmayanda Nasution', 'akhmayanda.nasution@sig.id', 0, 1, NULL),
(2, 'Bisnis Inkubasi Non Semen/Workshop', 'Febi Rieldi', 'febi.rieldi@sig.id', 1, 1, NULL),
(3, 'Bisnis Inkubasi Non Semen/Bengkel Listrik', 'Febi Rieldi', 'febi.rieldi@sig.id', 1, 1, NULL),
(4, 'Bisnis Inkubasi Non Semen/Bengkel Mesin', 'Febi Rieldi', 'febi.rieldi@sig.id', 1, 1, NULL),
(5, 'Staf Internal Audit', 'Nurma Yanti', 'nurma.yanti@sig.id', 0, 1, NULL),
(6, 'Staf of Portofolio', 'Athari Adi Putra', 'athari.putra@sig.id', 0, 1, NULL),
(7, 'Unit  Penjualan Eksport', 'Henzo Bernando', 'henzo.bernando@sig.id', 0, 1, NULL),
(8, 'Unit  Penjualan Sumatra Bagian Tengah', 'Nanda Kurniawan', 'nanda.kurniawan@sig.id', 0, 1, NULL),
(9, 'Unit Harga dan Promosi', 'Sari Rahmadani', 'sari.ramadhani@sig.id', 0, 1, NULL),
(10, 'Unit Import Managemen', 'Fachrur Roji', 'fachrur.roji@sig.id', 0, 1, NULL),
(11, 'Unit of Service Procurement', 'Yogi Khairi H', 'yogi.hasibuan@sig.id', 1, 1, NULL),
(12, 'Unit Pengelolaan Gudang', 'Hamdi Ayussa', 'hamdi.ayussa@sig.id', 1, 1, NULL),
(13, 'Unit Keamanan', 'Wahyudi', 'wahyudi.8278@sig.id', 0, 1, NULL),
(14, 'Unit CSR', 'Idris', 'idris@sig.id', 0, 1, NULL),
(15, 'Unit Hukum', 'Oxivia', 'oxivia@sig.id', 0, 1, NULL),
(16, 'Unit Komunikasi & Kesekretariatan', 'Ilham Akbar', 'ilham.akbar@sig.id', 0, 1, NULL),
(17, 'Unit ICT', 'Roni Mahmudi', 'roni.mahmudi@sig.id', 0, 1, NULL),
(18, 'Unit Perencanaan & Evaluasi Produksi', 'Agrian Pebi', 'agrian.pebi@sig.id', 1, 1, NULL),
(19, 'Unit Quality Control', 'Ike Evi Wiyana', 'ike.wiyana@sig.id', 1, 1, NULL),
(20, 'Staff AFR', 'Musytaqim Nasra', 'musytaqim.nasra@sig.id', 1, 1, NULL),
(21, 'Unit Penunjang Produksi', 'Suryat Handoko', 'suryat.handoko@sig.id', 1, 1, NULL),
(22, 'Unit Operasi Tambang', 'Hendri Priparis', 'hendri.priparis@sig.id', 1, 1, 'Administrasi Perkantoran Tidak Pakai APD'),
(23, 'Unit Perencanaan dan Pengawasan Tambang', 'Berva Lindo', 'berva.lindo@sig.id', 1, 1, NULL),
(24, 'Unit Produksi Bahan Baku', 'Ricky Aprinaldo', 'ricky.aprinaldo@sig.id', 1, 1, NULL),
(25, 'Unit WHRPG & Utilitas', 'Harri Kurniawan', 'harri.kurniawan@sig.id', 1, 1, NULL),
(26, 'Unit Produksi Terak 1', 'Alfein Rahmad', 'alfein.rahmad@sig.id', 1, 1, NULL),
(27, 'Unit Produksi Terak 2', 'Ujang Friatna', 'ujang.friatna@sig.id', 1, 1, NULL),
(28, 'Unit Produksi Semen', 'Titut Eryanto', 'titut.eryanto@sig.id', 1, 1, NULL),
(29, 'Unit Pabrik Kantong', 'Syafriado', 'syafriado@sig.id', 1, 1, 'Administrasi Perkantoran Tidak Pakai APD'),
(30, 'Unit Pemeliharaan Mesin 1', 'Endi Alta', 'endi.alta@sig.id', 1, 1, NULL),
(31, 'Unit Pemeliharaan Mesin 2', 'Irwan Kartadi P', 'irwan.putra@sig.id', 1, 1, NULL),
(32, 'Unit Pemeliharaan Listrik & Instrumen 1', 'Zulvawarman', 'zulvawarman@sig.id', 1, 1, NULL),
(33, 'Unit Pemeliharaan Listrik & Instrumen 2', 'Santoro', 'santoro@sig.id', 1, 1, NULL),
(34, 'Unit  Capex', 'Rausyan Fikri', 'rausyan.fikri@sig.id', 0, 1, NULL),
(35, 'Unit Site Engineering', 'Andra Novendri', 'andra.novendri@sig.id', 1, 1, NULL),
(36, 'Unit Project Management', 'Rahman Ikhlas', 'rahman.ikhlas@sig.id', 0, 1, NULL),
(37, 'Unit Perencanaan Suku Cadang', 'Azet Putra', 'azet.putra@sig.id', 1, 1, NULL),
(38, 'TPM Officer', 'Hernes', 'hernes@sig.id', 0, 1, NULL),
(39, 'Unit Produksi Mesin & Teknikal Support', 'Rendy Fahlevi', 'rendy.fahlevi@sig.id', 1, 1, 'Manajemen Tidak Pakai APD'),
(40, 'Unit Produksi BIP & Aplikasi', 'Yelmi Arya Putra', 'yelmi.putra@sig.id', 1, 1, NULL),
(41, 'Unit Quality Assurance', 'Febri Maulana', 'febri.maulana@sig.id', 1, 1, NULL),
(42, 'Unit SHE', 'Asrining Sari', 'asrining.sari@sig.id', 1, 1, NULL),
(43, 'Unit Operasional SDM', 'Nur Anita Rahmawati', 'nur.anita@sig.id', 0, 1, NULL),
(44, 'Unit Operasional SDM/Diklat', 'Siska Ayu Soraya', 'siska.soraya@sig.id', 0, 1, NULL),
(45, 'Unit Sarana Umum', 'Deni Zen', 'deni.zen@sig.id', 0, 1, NULL),
(46, 'Unit GRC & Internal Control', 'M Irwan Prasetyo', 'irwan.prasetyo@sig.id', 0, 1, NULL),
(47, 'Unit Akuntansi', 'Rahmat Novriyan', 'rahmat.novriyan@sig.id', 0, 1, NULL),
(48, 'Unit Keuangan', 'Ruky Hendra', 'ruky.hendra@sig.id', 0, 1, NULL),
(49, 'Unit Kinerja & Anggaran', 'Yulia Hidayati', 'yulia.hidayati@sig.id', 0, 1, NULL),
(50, 'Unit Sistem Manajemen', 'Nelvi Irawati', 'nelvi.irawati@sig.id', 0, 1, NULL),
(51, 'Unit Pengantongan Semen', 'Palman', 'palman@sig.id', 0, 1, NULL),
(52, 'Unit Logistik Distribusi/ Transportasi', 'Suryadi Wizar', 'suryadi.wizar@sig.id', 0, 1, NULL),
(53, 'Unit Logistik Distribusi/ Pengelolaan Kantong', 'Suryadi Wizar', 'suryadi.wizar@sig.id', 0, 1, NULL),
(54, 'Unit Operational Logistik/ Silog', 'AM. Reza', 'm.reza@sig.id', 0, 1, NULL),
(55, 'Unit Pabrik Dumai', 'Teguh Soviyanto', 'teguh.soviyanto@sig.id', 1, 1, NULL),
(56, 'Unit Logistik Antar Pabrik', 'Aldri Sonni', 'aldri.sonni@sig.id', 0, 1, NULL),
(57, 'Yayasan Igasar', 'Ridwan Muchtar', 'ridwan.muchtar@sig.id', 0, 1, NULL),
(58, 'Dana Pensiun SP', 'R Nicko Yuda K', 'nicko.yuda@sig.id', 0, 1, NULL),
(59, 'PT Polma Sepa', 'Andanisep', 'andanisep@gmail.com', 0, 1, NULL),
(60, 'Koperasi Karyawan SP', 'Faisal Arif', 'faisal.arif@sig.id', 1, 1, 'Pakai APD untuk Jurusan Teknik Kendaraan Ringan'),
(61, 'PT Pasoka Sumber Karya', 'Erick Reza Alandri', 'erick.alandri@sig.id', 0, 1, NULL),
(62, 'UPZ Semen Padang', 'Akmal', 'akmal.socialentrepreneure@gmail.com', 0, 1, NULL),
(63, 'PT Sepatim Batamtama', 'Satrio Rian Bhakti', 'satrio.rian@sig.id', 0, 1, NULL),
(64, 'Unit Maintenance Reliability/ Perencanaan dan Evaluasi Pemeliharaan', 'Yanuardi', 'yanuardi.6615@sig.id', 1, 1, NULL),
(65, 'Unit Maintenance Reliability/ Inspeksi Pemeliharaan', 'Dani Darma Putra', 'dani.putra@sig.id', 1, 1, NULL),
(66, 'Unit Maintenance Reliability/ PGO', 'Delviyoldi', 'delviyoldi@sig.id', 1, 1, NULL),
(67, 'Yasiga Sarana Utama', 'Irf\'ak Izma', 'irfak.izma@sig.id', 0, 1, NULL),
(68, 'FKIK', NULL, NULL, 0, 1, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `unit_user`
--

CREATE TABLE `unit_user` (
  `unit_user_id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `unit_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `fullname` varchar(200) DEFAULT NULL,
  `eselon` tinyint(2) DEFAULT NULL,
  `user_image` varchar(255) NOT NULL DEFAULT 'default.svg',
  `nisn_nim` varchar(100) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `jenis_kelamin` char(1) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `domisili` varchar(255) DEFAULT NULL,
  `provinceDom_id` int(11) DEFAULT NULL,
  `cityDom_id` int(11) DEFAULT NULL,
  `tingkat_pendidikan` varchar(100) DEFAULT NULL,
  `instansi_id` int(11) DEFAULT NULL,
  `jurusan_id` int(11) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `nilai_ipk` decimal(5,2) DEFAULT NULL,
  `rfid_no` int(11) DEFAULT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `proposal` varchar(255) DEFAULT NULL,
  `surat_permohonan` varchar(255) DEFAULT NULL,
  `tanggal_surat` date DEFAULT NULL,
  `no_surat` varchar(255) DEFAULT NULL,
  `nama_pimpinan` varchar(100) DEFAULT NULL,
  `jabatan` varchar(150) DEFAULT NULL,
  `email_instansi` varchar(150) DEFAULT NULL,
  `bpjs_kes` varchar(255) DEFAULT NULL,
  `bpjs_tk` varchar(255) DEFAULT NULL,
  `buktibpjs_tk` varchar(255) DEFAULT NULL,
  `ktp_kk` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `reset_hash` varchar(255) DEFAULT NULL,
  `reset_at` datetime DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `activate_hash` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_message` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `force_pass_reset` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `fullname`, `eselon`, `user_image`, `nisn_nim`, `no_hp`, `jenis_kelamin`, `alamat`, `province_id`, `city_id`, `domisili`, `provinceDom_id`, `cityDom_id`, `tingkat_pendidikan`, `instansi_id`, `jurusan_id`, `semester`, `nilai_ipk`, `rfid_no`, `cv`, `proposal`, `surat_permohonan`, `tanggal_surat`, `no_surat`, `nama_pimpinan`, `jabatan`, `email_instansi`, `bpjs_kes`, `bpjs_tk`, `buktibpjs_tk`, `ktp_kk`, `password_hash`, `reset_hash`, `reset_at`, `reset_expires`, `activate_hash`, `status`, `status_message`, `active`, `force_pass_reset`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'bordirmotif@gmail.com', 'Admin', 'Admin Master', NULL, 'vira.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$bswnrdHFrT0fWw85p0RZseTxnyHxFnQnWDTnci3p0K1NzU3r2RWcm', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2025-07-03 16:06:28', '2025-07-20 21:54:18', NULL),
(23, 'NUR.ANITA@sig.id', 'anita', 'Nur Anita Rahmawati', 2, 'default.svg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$TyM/Oe.WO9T8J5kPhEeL6OE.0v5YgFQZzPpGmlipYq4yPL4bFwAci', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, '2025-07-22 21:58:44', '2025-07-22 21:58:44', NULL),
(26, 'virayukia1234@gmail.com', NULL, 'Arisa Maharani', NULL, 'arisa-maharani-user-image-5280.jpg', '1911521003', '089991232323', 'L', 'Jl. Bandes Binuang RT 02/ RW 02, Kelurahan Binuang Kampung Dalam, Binuang Kampung Dalam, Pauh', 13, 1371, '', NULL, NULL, 'D4/S1', 5, 17, 7, 3.67, NULL, 'arisa-maharani-cv-7477.pdf', 'arisa-maharani-proposal-4390.pdf', 'arisa-maharani-surat-permohonan-7273.pdf', '2025-07-15', '1212/XII/2025', 'Ahmad', 'Kepala Departemen', 'universitasAndlas@gmail.com', 'arisa-maharani-bpjs-kes-8593.pdf', 'arisa-maharani-bpjs-tk-4653.pdf', 'arisa-maharani-buktibpjs-tk-4866.pdf', 'arisa-maharani-ktp-kk-4499.pdf', '$2y$10$YLcvGpPznL05d1owt6HWB.UeIPDagGl6gJ8UrI0Ly0gkhKckdfi6a', '4be182a4796bada985aabdb4005c7d3d', NULL, '2025-08-06 09:35:20', NULL, NULL, NULL, 1, 0, '2025-07-31 15:18:39', '2025-08-06 08:35:20', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `auth_activation_attempts`
--
ALTER TABLE `auth_activation_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `auth_groups`
--
ALTER TABLE `auth_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `auth_groups_permissions`
--
ALTER TABLE `auth_groups_permissions`
  ADD KEY `auth_groups_permissions_permission_id_foreign` (`permission_id`),
  ADD KEY `group_id_permission_id` (`group_id`,`permission_id`);

--
-- Indeks untuk tabel `auth_groups_users`
--
ALTER TABLE `auth_groups_users`
  ADD KEY `auth_groups_users_user_id_foreign` (`user_id`),
  ADD KEY `group_id_user_id` (`group_id`,`user_id`);

--
-- Indeks untuk tabel `auth_logins`
--
ALTER TABLE `auth_logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `auth_permissions`
--
ALTER TABLE `auth_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `auth_reset_attempts`
--
ALTER TABLE `auth_reset_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auth_tokens_user_id_foreign` (`user_id`),
  ADD KEY `selector` (`selector`);

--
-- Indeks untuk tabel `auth_users_permissions`
--
ALTER TABLE `auth_users_permissions`
  ADD KEY `auth_users_permissions_permission_id_foreign` (`permission_id`),
  ADD KEY `user_id_permission_id` (`user_id`,`permission_id`);

--
-- Indeks untuk tabel `detail_jawaban_safety`
--
ALTER TABLE `detail_jawaban_safety`
  ADD PRIMARY KEY (`detail_jawaban_id`),
  ADD KEY `jawaban_safety_id` (`jawaban_safety_id`),
  ADD KEY `soal_safety_id` (`soal_safety_id`);

--
-- Indeks untuk tabel `instansi`
--
ALTER TABLE `instansi`
  ADD PRIMARY KEY (`instansi_id`);

--
-- Indeks untuk tabel `jawaban_safety`
--
ALTER TABLE `jawaban_safety`
  ADD PRIMARY KEY (`jawaban_id`),
  ADD KEY `magang_id` (`magang_id`);

--
-- Indeks untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`jurusan_id`);

--
-- Indeks untuk tabel `jurusan_unit`
--
ALTER TABLE `jurusan_unit`
  ADD PRIMARY KEY (`jurusan_unit_id`),
  ADD KEY `jurusan_id` (`jurusan_id`),
  ADD KEY `kuota_unit_id` (`kuota_unit_id`);

--
-- Indeks untuk tabel `kuota_unit`
--
ALTER TABLE `kuota_unit`
  ADD PRIMARY KEY (`kuota_unit_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indeks untuk tabel `magang`
--
ALTER TABLE `magang`
  ADD PRIMARY KEY (`magang_id`),
  ADD KEY `periode_id` (`periode_id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pembimbing`
--
ALTER TABLE `pembimbing`
  ADD PRIMARY KEY (`pembimbing_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indeks untuk tabel `penelitian`
--
ALTER TABLE `penelitian`
  ADD PRIMARY KEY (`penelitian_id`);

--
-- Indeks untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  ADD PRIMARY KEY (`penilaian_id`),
  ADD KEY `magang_id` (`magang_id`);

--
-- Indeks untuk tabel `periode_magang`
--
ALTER TABLE `periode_magang`
  ADD PRIMARY KEY (`periode_id`);

--
-- Indeks untuk tabel `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `regencies`
--
ALTER TABLE `regencies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `province_id` (`province_id`);

--
-- Indeks untuk tabel `soal_safety`
--
ALTER TABLE `soal_safety`
  ADD PRIMARY KEY (`soal_id`);

--
-- Indeks untuk tabel `unit_kerja`
--
ALTER TABLE `unit_kerja`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indeks untuk tabel `unit_user`
--
ALTER TABLE `unit_user`
  ADD PRIMARY KEY (`unit_user_id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `auth_activation_attempts`
--
ALTER TABLE `auth_activation_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `auth_groups`
--
ALTER TABLE `auth_groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `auth_logins`
--
ALTER TABLE `auth_logins`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT untuk tabel `auth_permissions`
--
ALTER TABLE `auth_permissions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `auth_reset_attempts`
--
ALTER TABLE `auth_reset_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `detail_jawaban_safety`
--
ALTER TABLE `detail_jawaban_safety`
  MODIFY `detail_jawaban_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `instansi`
--
ALTER TABLE `instansi`
  MODIFY `instansi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=369;

--
-- AUTO_INCREMENT untuk tabel `jawaban_safety`
--
ALTER TABLE `jawaban_safety`
  MODIFY `jawaban_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `jurusan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT untuk tabel `jurusan_unit`
--
ALTER TABLE `jurusan_unit`
  MODIFY `jurusan_unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=294;

--
-- AUTO_INCREMENT untuk tabel `kuota_unit`
--
ALTER TABLE `kuota_unit`
  MODIFY `kuota_unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT untuk tabel `magang`
--
ALTER TABLE `magang`
  MODIFY `magang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pembimbing`
--
ALTER TABLE `pembimbing`
  MODIFY `pembimbing_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penelitian`
--
ALTER TABLE `penelitian`
  MODIFY `penelitian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  MODIFY `penilaian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `periode_magang`
--
ALTER TABLE `periode_magang`
  MODIFY `periode_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `soal_safety`
--
ALTER TABLE `soal_safety`
  MODIFY `soal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `unit_kerja`
--
ALTER TABLE `unit_kerja`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT untuk tabel `unit_user`
--
ALTER TABLE `unit_user`
  MODIFY `unit_user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `auth_groups_permissions`
--
ALTER TABLE `auth_groups_permissions`
  ADD CONSTRAINT `auth_groups_permissions_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_groups_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `auth_groups_users`
--
ALTER TABLE `auth_groups_users`
  ADD CONSTRAINT `auth_groups_users_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `auth_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_groups_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `auth_users_permissions`
--
ALTER TABLE `auth_users_permissions`
  ADD CONSTRAINT `auth_users_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auth_users_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_jawaban_safety`
--
ALTER TABLE `detail_jawaban_safety`
  ADD CONSTRAINT `detail_jawaban_safety_ibfk_1` FOREIGN KEY (`jawaban_safety_id`) REFERENCES `jawaban_safety` (`jawaban_id`),
  ADD CONSTRAINT `detail_jawaban_safety_ibfk_2` FOREIGN KEY (`soal_safety_id`) REFERENCES `soal_safety` (`soal_id`);

--
-- Ketidakleluasaan untuk tabel `jawaban_safety`
--
ALTER TABLE `jawaban_safety`
  ADD CONSTRAINT `jawaban_safety_ibfk_1` FOREIGN KEY (`magang_id`) REFERENCES `magang` (`magang_id`);

--
-- Ketidakleluasaan untuk tabel `jurusan_unit`
--
ALTER TABLE `jurusan_unit`
  ADD CONSTRAINT `jurusan_unit_ibfk_1` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`jurusan_id`),
  ADD CONSTRAINT `jurusan_unit_ibfk_2` FOREIGN KEY (`kuota_unit_id`) REFERENCES `kuota_unit` (`kuota_unit_id`);

--
-- Ketidakleluasaan untuk tabel `kuota_unit`
--
ALTER TABLE `kuota_unit`
  ADD CONSTRAINT `kuota_unit_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`unit_id`);

--
-- Ketidakleluasaan untuk tabel `magang`
--
ALTER TABLE `magang`
  ADD CONSTRAINT `magang_ibfk_1` FOREIGN KEY (`periode_id`) REFERENCES `periode_magang` (`periode_id`),
  ADD CONSTRAINT `magang_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`unit_id`),
  ADD CONSTRAINT `magang_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `pembimbing`
--
ALTER TABLE `pembimbing`
  ADD CONSTRAINT `pembimbing_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`unit_id`);

--
-- Ketidakleluasaan untuk tabel `penilaian`
--
ALTER TABLE `penilaian`
  ADD CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`magang_id`) REFERENCES `magang` (`magang_id`);

--
-- Ketidakleluasaan untuk tabel `regencies`
--
ALTER TABLE `regencies`
  ADD CONSTRAINT `regencies_ibfk_1` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`);

--
-- Ketidakleluasaan untuk tabel `unit_user`
--
ALTER TABLE `unit_user`
  ADD CONSTRAINT `unit_user_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`unit_id`),
  ADD CONSTRAINT `unit_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
