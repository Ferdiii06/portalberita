-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 24, 2020 at 08:24 PM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `berita`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id_berita` int(5) NOT NULL,
  `id_kategori` int(3) UNSIGNED NOT NULL,
  `judul` varchar(100) NOT NULL COMMENT 'Judul Berita',
  `headline` text NOT NULL COMMENT 'Berita Singkat',
  `isi` text NOT NULL COMMENT 'Berita Lengkap',
  `pengirim` varchar(15) NOT NULL COMMENT 'Pengirim Berita',
  `tanggal` datetime NOT NULL COMMENT 'Tanggal dan waktu kirim berita'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(3) NOT NULL,
  `nm_kategori` varchar(30) NOT NULL COMMENT 'Nama Kategori',
  `deskripsi` varchar(200) NOT NULL COMMENT 'Deskripsi Kategori'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nm_kategori`, `deskripsi`) VALUES
(1, 'News', 'Berita terkini'),
(2, 'Tekno', 'Seputar teknologi terbaru'),
(3, 'Ekonomi', 'Keuangan, kondisi makro dan mikro'),
(4, 'Otomotif', 'Manufaktur dalam otomotif'),
(5, 'Life Style', 'Gaya hidup masyarakat'),
(6, 'Edukasi', 'berita seputar pendidikan'),
(7, 'Sains', 'Kekuatan utama peradaban adalah sains');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id_berita`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id_berita` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
