-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Des 2025 pada 05.24
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
-- Struktur dari tabel `penelitian`
--

CREATE TABLE `penelitian` (
  `penelitian_id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `judul_penelitian` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `tanggal_daftar` datetime NOT NULL,
  `dosen_pembimbing` varchar(255) NOT NULL,
  `rencana_masuk` date NOT NULL,
  `durasi` int(11) NOT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status_seleksi` varchar(20) DEFAULT NULL,
  `tanggal_seleksi` date DEFAULT NULL,
  `approve_unit` varchar(4) DEFAULT NULL,
  `tanggal_approve_unit` date DEFAULT NULL,
  `cttn_approve_unit` varchar(255) DEFAULT NULL,
  `status_konfirmasi` varchar(20) DEFAULT NULL,
  `tanggal_konfirmasi` date DEFAULT NULL,
  `status_validasi_konfirmasi` varchar(50) DEFAULT NULL,
  `tanggal_validasi_konfirmasi` datetime DEFAULT NULL,
  `status_berkas_lengkap` varchar(50) DEFAULT NULL,
  `tanggal_berkas_lengkap` datetime DEFAULT NULL,
  `cttn_berkas_lengkap` varchar(100) DEFAULT NULL,
  `tanggal_setujui_pernyataan` date DEFAULT NULL,
  `pembimbing_id` int(11) DEFAULT NULL,
  `formulir_penelitian` varchar(255) DEFAULT NULL,
  `status_pembimbing` varchar(10) DEFAULT NULL,
  `catatan_pembimbing` varchar(255) DEFAULT NULL,
  `catatan_formulir` varchar(255) DEFAULT NULL,
  `absensi` varchar(255) DEFAULT NULL,
  `catatan_absensi` varchar(255) DEFAULT NULL,
  `finalisasi` datetime DEFAULT NULL,
  `ka_unit_approve` tinyint(4) DEFAULT NULL,
  `tanggal_approve` date DEFAULT NULL,
  `status_akhir` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `penelitian`
--
ALTER TABLE `penelitian`
  ADD PRIMARY KEY (`penelitian_id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `penelitian`
--
ALTER TABLE `penelitian`
  MODIFY `penelitian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `penelitian`
--
ALTER TABLE `penelitian`
  ADD CONSTRAINT `penelitian_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`unit_id`),
  ADD CONSTRAINT `penelitian_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
