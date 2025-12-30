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
-- Struktur dari tabel `penelitian_keyword`
--

CREATE TABLE `penelitian_keyword` (
  `penelitian_keyword_id` int(11) NOT NULL,
  `penelitian_id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `penelitian_keyword`
--
ALTER TABLE `penelitian_keyword`
  ADD PRIMARY KEY (`penelitian_keyword_id`),
  ADD KEY `penelitian_keyword_ibfk_2` (`keyword_id`),
  ADD KEY `penelitian_keyword_ibfk_1` (`penelitian_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `penelitian_keyword`
--
ALTER TABLE `penelitian_keyword`
  MODIFY `penelitian_keyword_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `penelitian_keyword`
--
ALTER TABLE `penelitian_keyword`
  ADD CONSTRAINT `penelitian_keyword_ibfk_1` FOREIGN KEY (`penelitian_id`) REFERENCES `penelitian` (`penelitian_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penelitian_keyword_ibfk_2` FOREIGN KEY (`keyword_id`) REFERENCES `keyword` (`keyword_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
