-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Des 2025 pada 05.22
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
-- Struktur dari tabel `keyword`
--

CREATE TABLE `keyword` (
  `keyword_id` int(11) NOT NULL,
  `keyword_nama` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `is_suggested` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `keyword`
--

INSERT INTO `keyword` (`keyword_id`, `keyword_nama`, `status`, `is_suggested`, `created_at`) VALUES
(1, 'lereng', 'approved', 0, '2025-10-24 15:16:59'),
(2, 'stabilitas', 'approved', 0, '2025-10-25 12:06:00'),
(3, 'penambangan', 'approved', 0, '2025-10-26 12:06:00'),
(4, 'tambang', 'approved', 0, '2025-10-27 12:06:00'),
(5, 'geoteknik', 'approved', 0, '2025-10-28 12:06:00'),
(6, 'batuan', 'approved', 0, '2025-10-29 12:06:00'),
(7, 'alat bor', 'approved', 0, '2025-10-30 12:06:00'),
(8, 'geologi', 'approved', 0, '2025-10-31 12:06:00'),
(9, 'pasir silika', 'approved', 0, '2025-11-01 12:06:00'),
(10, 'bahan baku', 'approved', 0, '2025-11-02 12:06:00'),
(11, 'harga pokok', 'approved', 0, '2025-11-03 12:06:00'),
(12, 'biaya produksi', 'approved', 0, '2025-11-04 12:06:00'),
(13, 'akuntansi', 'approved', 0, '2025-11-05 12:06:00'),
(14, 'HPP', 'approved', 0, '2025-11-06 12:06:00'),
(15, 'APD', 'approved', 0, '2025-11-07 12:06:00'),
(16, 'keselamatan', 'approved', 0, '2025-11-08 12:06:00'),
(17, 'kesehatan kerja', 'approved', 0, '2025-11-09 12:06:00'),
(18, 'lingkungan', 'approved', 0, '2025-11-10 12:06:00'),
(19, 'risiko kerja', 'approved', 0, '2025-11-11 12:06:00'),
(20, 'limbah', 'approved', 0, '2025-11-12 12:06:00'),
(21, 'B3', 'approved', 0, '2025-11-13 12:06:00'),
(22, 'ergonomi', 'approved', 0, '2025-11-14 12:06:00'),
(23, 'kecelakaan kerja', 'approved', 0, '2025-11-15 12:06:00'),
(24, 'listrik', 'approved', 0, '2025-11-16 12:06:00'),
(25, 'transformator', 'approved', 0, '2025-11-17 12:06:00'),
(26, 'relay', 'approved', 0, '2025-11-18 12:06:00'),
(27, 'proteksi', 'approved', 0, '2025-11-19 12:06:00'),
(28, 'utilitas', 'approved', 0, '2025-11-20 12:06:00'),
(29, 'produksi', 'approved', 0, '2025-11-21 12:06:00'),
(30, 'perencanaan', 'approved', 0, '2025-11-22 12:06:00'),
(31, 'forecasting', 'approved', 0, '2025-11-23 12:06:00'),
(32, 'MRP', 'approved', 0, '2025-11-24 12:06:00'),
(33, 'efisiensi', 'approved', 0, '2025-11-25 12:06:00'),
(34, 'geokimia', 'approved', 0, '2025-11-26 12:06:00'),
(35, 'CSR', 'approved', 0, '2025-11-27 12:06:00'),
(36, 'masyarakat', 'approved', 0, '2025-11-28 12:06:00'),
(37, 'air bersih', 'approved', 0, '2025-11-29 12:06:00'),
(38, 'komunikasi', 'approved', 0, '2025-11-30 12:06:00'),
(39, 'kesekretariatan', 'approved', 0, '2025-12-01 12:06:00'),
(40, 'perilaku organisasi', 'approved', 0, '2025-12-02 12:06:00'),
(41, 'leadership', 'approved', 0, '2025-12-03 12:06:00'),
(42, 'motivasi', 'approved', 0, '2025-12-04 12:06:00'),
(43, 'kepuasan kerja', 'approved', 0, '2025-12-05 12:06:00'),
(44, 'penggajian', 'approved', 0, '2025-12-06 12:06:00'),
(45, 'SDM', 'approved', 0, '2025-12-07 12:06:00'),
(46, 'karyawan', 'approved', 0, '2025-12-08 12:06:00'),
(47, 'pengadaan', 'approved', 0, '2025-12-09 12:06:00'),
(48, 'supplier', 'approved', 0, '2025-12-10 12:06:00'),
(49, 'partisipasi', 'approved', 0, '2025-12-11 12:06:00'),
(50, 'perawatan mesin', 'approved', 0, '2025-12-12 12:06:00'),
(51, 'maintenance', 'approved', 0, '2025-12-13 12:06:00'),
(52, 'reliability', 'approved', 0, '2025-12-14 12:06:00'),
(53, 'preventive maintenance', 'approved', 0, '2025-12-15 12:06:00'),
(54, 'energi terbarukan', 'approved', 0, '2025-12-16 12:06:00'),
(55, 'AFR', 'approved', 0, '2025-12-17 12:06:00'),
(56, 'sustainability', 'approved', 0, '2025-12-18 12:06:00'),
(57, 'neraca massa', 'approved', 0, '2025-12-19 12:06:00'),
(58, 'neraca energi', 'approved', 0, '2025-12-20 12:06:00'),
(59, 'cement mill', 'approved', 0, '2025-12-21 12:06:00'),
(60, 'rotary kiln', 'approved', 0, '2025-12-22 12:06:00'),
(61, 'optimasi', 'approved', 0, '2025-12-23 12:06:00'),
(62, 'engineering', 'approved', 0, '2025-12-24 12:06:00'),
(63, '3D scanner', 'approved', 0, '2025-12-25 12:06:00'),
(64, 'impeller', 'approved', 0, '2025-12-26 12:06:00'),
(65, 'pemindaian', 'approved', 0, '2025-12-27 12:06:00'),
(66, 'workshop', 'approved', 0, '2025-12-28 12:06:00'),
(67, 'produksi non semen', 'approved', 0, '2025-12-29 12:06:00'),
(68, 'limbah industri', 'approved', 0, '2025-12-30 12:06:00'),
(69, 'PM2.5', 'approved', 0, '2025-12-31 12:06:00'),
(70, 'JSA', 'approved', 0, '2026-01-01 12:06:00'),
(71, 'psychological safety', 'approved', 0, '2026-01-02 12:06:00'),
(72, 'gamifikasi', 'approved', 0, '2026-01-03 12:06:00'),
(73, 'work engagement', 'approved', 0, '2026-01-04 12:06:00'),
(74, 'determinasi diri', 'approved', 0, '2026-01-05 12:06:00'),
(75, 'evaluasi', 'approved', 0, '2026-01-06 12:06:00'),
(76, 'program sosial', 'approved', 0, '2026-01-07 12:06:00'),
(77, 'audit', 'approved', 0, '2026-01-08 12:06:00'),
(78, 'pengendalian internal', 'approved', 0, '2026-01-09 12:06:00'),
(79, 'voice behavior', 'approved', 0, '2026-01-10 12:06:00'),
(80, 'OCB', 'approved', 0, '2026-01-11 12:06:00'),
(81, 'kepemimpinan', 'approved', 1, '2025-10-27 09:54:59'),
(82, 'bor', 'waiting', 1, '2025-10-27 10:54:59'),
(83, 'alat tambang', 'waiting', 1, '2025-10-27 10:54:59'),
(85, 'kepemimpinan', 'waiting', 1, '2025-11-06 15:09:16'),
(86, 'kinerja', 'waiting', 1, '2025-11-06 15:09:16');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `keyword`
--
ALTER TABLE `keyword`
  ADD PRIMARY KEY (`keyword_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `keyword`
--
ALTER TABLE `keyword`
  MODIFY `keyword_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
