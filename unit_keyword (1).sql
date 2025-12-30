-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Des 2025 pada 05.23
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
-- Struktur dari tabel `unit_keyword`
--

CREATE TABLE `unit_keyword` (
  `unit_keyword_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `unit_keyword`
--

INSERT INTO `unit_keyword` (`unit_keyword_id`, `unit_id`, `keyword_id`) VALUES
(1, 22, 4),
(2, 22, 3),
(3, 22, 1),
(4, 22, 5),
(5, 22, 6),
(6, 22, 7),
(7, 22, 8),
(8, 22, 9),
(9, 47, 13),
(10, 47, 11),
(11, 47, 12),
(12, 47, 14),
(13, 47, 77),
(14, 1, 66),
(15, 1, 51),
(16, 1, 67),
(17, 42, 16),
(18, 42, 17),
(19, 42, 18),
(20, 42, 15),
(21, 42, 19),
(22, 42, 20),
(23, 42, 69),
(24, 42, 70),
(25, 42, 22),
(26, 42, 21),
(27, 42, 23),
(28, 25, 24),
(29, 25, 25),
(30, 25, 26),
(31, 25, 27),
(32, 25, 28),
(33, 24, 10),
(34, 24, 34),
(35, 16, 38),
(36, 16, 39),
(37, 16, 40),
(38, 16, 41),
(39, 16, 79),
(40, 16, 80),
(41, 12, 10),
(42, 18, 30),
(43, 18, 29),
(44, 18, 31),
(45, 18, 32),
(46, 18, 75),
(47, 18, 33),
(48, 11, 47),
(49, 11, 48),
(50, 14, 35),
(51, 14, 36),
(52, 14, 37),
(53, 14, 49),
(54, 43, 45),
(55, 43, 46),
(56, 43, 42),
(57, 43, 43),
(58, 43, 72),
(59, 43, 44),
(60, 20, 55),
(61, 20, 54),
(62, 20, 56),
(63, 64, 51),
(64, 64, 52),
(65, 64, 53),
(66, 31, 50),
(67, 26, 59),
(68, 26, 57),
(69, 26, 58),
(70, 35, 63),
(71, 35, 65),
(72, 35, 62),
(73, 35, 64);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `unit_keyword`
--
ALTER TABLE `unit_keyword`
  ADD PRIMARY KEY (`unit_keyword_id`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `keyword_id` (`keyword_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `unit_keyword`
--
ALTER TABLE `unit_keyword`
  MODIFY `unit_keyword_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `unit_keyword`
--
ALTER TABLE `unit_keyword`
  ADD CONSTRAINT `unit_keyword_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit_kerja` (`unit_id`),
  ADD CONSTRAINT `unit_keyword_ibfk_2` FOREIGN KEY (`keyword_id`) REFERENCES `keyword` (`keyword_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
