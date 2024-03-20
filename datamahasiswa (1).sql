-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2024 at 09:51 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `datamahasiswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `tanggal_dibuat` datetime DEFAULT current_timestamp(),
  `peran` enum('superadmin','editor','moderator') DEFAULT 'editor',
  `status` enum('aktif','tidak aktif','diblokir') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `email`, `nama_lengkap`, `tanggal_dibuat`, `peran`, `status`) VALUES
(31, 'superadmin01', 'password123', 'superadmin01@example.com', 'Super Admin Satu', '2024-02-28 16:14:08', 'superadmin', 'aktif'),
(32, 'editor01', 'password123', 'editor01@example.com', 'Editor Pertama', '2024-02-28 16:14:08', 'editor', 'aktif'),
(33, 'moderator01', 'password123', 'moderator01@example.com', 'Moderator Satu', '2024-02-28 16:14:08', 'moderator', 'aktif'),
(34, 'admin02', 'password123', 'admin02@example.com', 'Admin Dua', '2024-02-28 16:14:08', 'editor', 'aktif'),
(35, 'admin03', 'password123', 'admin03@example.com', 'Admin Tiga', '2024-02-28 16:14:08', 'editor', 'tidak aktif'),
(36, 'superadmin02', 'password123', 'superadmin02@example.com', 'Super Admin Dua', '2024-02-28 16:14:08', 'superadmin', 'diblokir'),
(37, 'editor02', 'password123', 'editor02@example.com', 'Editor Kedua', '2024-02-28 16:14:08', 'editor', 'aktif'),
(38, 'moderator02', 'password123', 'moderator02@example.com', 'Moderator Dua', '2024-02-28 16:14:08', 'moderator', 'aktif'),
(39, 'admin04', 'password123', 'admin04@example.com', 'Admin Empat', '2024-02-28 16:14:08', 'editor', 'aktif'),
(40, 'admin05', 'password123', 'admin05@example.com', 'Admin Lima', '2024-02-28 16:14:08', 'editor', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `id` int(11) NOT NULL,
  `nama_jurusan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jurusan`
--

INSERT INTO `jurusan` (`id`, `nama_jurusan`) VALUES
(1, 'Pembangunan'),
(2, 'Ekonomi Syariah'),
(3, 'Akuntansi'),
(4, 'Akuntansi Keuangan Publik'),
(5, 'Pariwisata'),
(6, 'Pendidikan Bahasa Dan Sastra Indonesia'),
(7, 'Pendidikan Bahasa Inggris'),
(8, 'Pendidikan Biologi'),
(9, 'Pendidikan Fisika'),
(10, 'Pendidikan Kimia'),
(11, 'Pendidikan Matematika'),
(12, 'Pendidikan Ekonomi'),
(13, 'Pendidikan Pancasila Dan Kewarganegaraan'),
(14, 'Teknologi Pendidikan'),
(15, 'PGSD'),
(16, 'PGPAUD'),
(17, 'PPG'),
(18, 'Statistika'),
(19, 'Matematika'),
(20, 'Biologi'),
(21, 'Teknologi Pangan'),
(22, 'Agribisnis'),
(23, 'Perencanaan Wilayah Dan Kota'),
(24, 'Sistem Informasi'),
(25, 'Kearsipan (D4)'),
(26, 'Perpajakan (D3)'),
(27, 'Perpustakaan'),
(28, 'Administrasi Publik'),
(29, 'Administrasi Bisnis'),
(30, 'Hukum'),
(31, 'Ilmu Pemerintahan'),
(32, 'Ilmu Komunikasi'),
(33, 'Ilmu Perpustakaan'),
(34, 'Sosiologi'),
(35, 'Sastra Inggris');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `No` int(11) NOT NULL,
  `Nim` text NOT NULL,
  `JalurProgram` enum('RPL','Reguler') NOT NULL,
  `NamaLengkap` varchar(100) NOT NULL,
  `TempatLahir` varchar(50) DEFAULT NULL,
  `TanggalLahir` date DEFAULT NULL,
  `NamaIbuKandung` varchar(100) DEFAULT NULL,
  `NIK` varchar(16) DEFAULT NULL,
  `Jurusan` varchar(50) DEFAULT NULL,
  `NomorHP` varchar(15) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Password` varchar(20) NOT NULL,
  `Agama` varchar(20) NOT NULL,
  `JenisKelamin` varchar(20) NOT NULL,
  `StatusPerkawinan` varchar(20) NOT NULL,
  `NomorHPAlternatif` varchar(15) DEFAULT NULL,
  `NomorIjazah` varchar(20) DEFAULT NULL,
  `TahunIjazah` year(4) DEFAULT NULL,
  `NISN` varchar(10) DEFAULT NULL,
  `LayananPaketSemester` varchar(20) NOT NULL,
  `DiInputOleh` varchar(50) DEFAULT NULL,
  `DiInputPada` datetime DEFAULT current_timestamp(),
  `DiEditPada` datetime NOT NULL DEFAULT current_timestamp(),
  `STATUS_INPUT_SIA` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`No`, `Nim`, `JalurProgram`, `NamaLengkap`, `TempatLahir`, `TanggalLahir`, `NamaIbuKandung`, `NIK`, `Jurusan`, `NomorHP`, `Email`, `Password`, `Agama`, `JenisKelamin`, `StatusPerkawinan`, `NomorHPAlternatif`, `NomorIjazah`, `TahunIjazah`, `NISN`, `LayananPaketSemester`, `DiInputOleh`, `DiInputPada`, `DiEditPada`, `STATUS_INPUT_SIA`) VALUES
(1, '', 'Reguler', 'MELIANI TANDO\'', 'TO\' LEMO', '2004-06-16', 'MARIANA SULE', '7317185606040003', 'Pembangunan', '082145230857', 'f.ann.y.ka.rl.i.n.d.ab.nc@gmail.com', '@16062004Ut', 'Islam', 'Perempuan', 'Belum Kawin', '', 'DN-19/M-SMA/K13/23/0', '2023', '0045045799', 'SIPAS', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(2, '', 'Reguler', 'EVOKMAR LIKU MANGAYUK', 'TANA TORAJA', '1992-10-29', 'EPIYANTI PALANGIRAN', '6408136910920004', 'PGSD', '085397393202', 'fanny.karli.n.d.a.b.n.c@gmail.com', '@29101992Ut', 'Protestan', 'Perempuan', 'Kawin', '085241833590', 'DN-19 Ma 0013646', '2011', '', '', 'Steven Arif', '2029-01-24 21:24:00', '2024-03-19 11:52:21', ''),
(3, '', 'Reguler', 'ENJEL', 'BUNTU SUSAN', '2003-01-13', 'KURMA', '7326075301030001', 'MANAJEMEN', '082280906390', 'f.ann.y.ka.rl.i.n.d.abnc@gmail.com', '@13/01/2003Ut', 'Protestan', 'Perempuan', '', '', 'M-SMK/K13-3/1369265', '2021', '0036281888', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(4, '', 'Reguler', 'MARGARETA OSE GELI', 'SANGATA\'', '1999-08-24', 'MARTA REMBON', '7318386408990001', 'PAUD', '081244518380', 'f.ann.y.ka.rl.i.n.da.b.nc@gmail.com', '@24081999Ut', 'Protestan', 'Perempuan', 'Kawin', '', 'DN-Mk/06 0872449', '2018', '9993774352', '', 'Steven Arif', '2017-02-24 13:13:00', '2024-03-19 11:52:21', ''),
(5, '', 'Reguler', 'LISTIONO', 'GRUBONGAN', '1988-03-03', 'SUSI', '7371100303880016', 'SISTEM INFORMASI', '082192064998', 'f.ann.y.ka.rl.i.n.da.bn.c@gmail.com', '@03/03/1988Ut', 'Islam', 'Laki-laki', 'Kawin', '081343734831', 'DN-03 Ma 0003801', '2007', '', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(6, '', 'Reguler', 'MELIATI SUKA\'', 'MAPPA\'', '1989-05-21', 'OKTOPINA BARU', '7318036105890004', 'PGSD', '081247918116', 'f.ann.y.ka.rl.i.n.da.bnc@gmail.com', '@21051989Ut', 'Protestan', 'Perempuan', 'Kawin', '085657037441/08', 'DN-19 Ma 0383893', '2008', '', '', 'Steven Arif', '2023-02-24 10:20:00', '2024-03-19 11:52:21', ''),
(7, '', 'RPL', 'NOVITA RIFIN PALAYUK', 'RANTEPAKU', '2000-11-18', 'MARGARETHA PALAYUK', '7326115811990005', 'PKN', '085340386127', 'f.ann.y.ka.rl.in.da.b.n.c@gmail.com', '@10112000Ut', 'Protestan', 'Perempuan', 'Belum Kawin', '085394806165', '542312022000582', '2018', '0002910295', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(8, '', 'RPL', 'STEPANUS PALLUNAN', 'SANGALLA\'', '1991-06-20', 'AGUSTINA BIRI', '7371112006910013', 'PENDIDIKAN EKONOMI', '081222223686', 'f.ann.y.ka.rl.in.d.abnc@gmail.com', '@20061991Ut', 'Katolik', 'Laki-laki', 'Belum Kawin', '081343734831', '100112/STIE-PB/Ma/20', '2017', '', '', 'Steven Arif', '2017-02-24 13:21:00', '2024-03-19 11:52:21', ''),
(9, '', 'Reguler', 'TRIONITA', 'BAU', '1995-06-06', 'GATTUNGAN', '7318024606940002', 'PAUD', '085741878948', 'f.ann.y.ka.rl.in.d.abn.c@gmail.com', '@06061995Ut', 'Protestan', 'Perempuan', 'Kawin', '', 'DN-32 PC 0001162', '2016', '', '', 'Steven Arif', '2017-02-24 13:49:00', '2024-03-19 11:52:21', ''),
(10, '', 'Reguler', 'RUNI', 'SIMBUANG', '1988-08-20', 'DODO', '7318096008880001', 'PAUD', '082112240126', 'f.ann.y.ka.r.li.n.d.ab.nc@gmail.com', '@20081988Ut', 'Protestan', 'Perempuan', '', '', 'DN-19 Ma 0019080', '2010', '', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(11, '', 'Reguler', 'MARTINUS PAKALLUNGAN', 'TALION', '1997-12-03', 'ERNA TA\'BI RAPPO', '7318201203970002', 'MANAJEMEN', '082343249554', 'f.ann.y.ka.r.lin.da.b.nc@gmail.com', '@12031997Ut', 'Protestan', 'Laki-laki', '', '082291373140', 'DN-19 Mk/06 00249982', '2016', '', '', 'Steven Arif', '2029-01-24 21:44:00', '2024-03-19 11:52:21', ''),
(12, '', 'Reguler', 'RISNA TOLAN LA\'BI\'', 'PALI', '1999-02-14', 'DINA MALIMBONG', '7318025702010001', 'PGSD', '085292330300', 'f.ann.y.ka.rl.i.nd.ab.n.c@gmail.com', '@12021999Ut', 'Protestan', 'Perempuan', 'Belum Kawin', '082347223202', 'DN-19/M-SMA/06/00368', '2019', '9992811245', '', 'Steven Arif', '2023-02-24 10:41:00', '2024-03-19 11:52:21', ''),
(13, '', 'RPL', 'ALFRIDA SESA', 'AMBON', '1991-02-04', 'BERTHA BANNE', '8101144402910005', 'PENDIDIKAN EKONOMI', '082148820202', 'f.ann.y.ka.rl.in.d.ab.n.c@gmail.com', '@04021991Ut', 'Protestan', 'Perempuan', 'Kawin', '', '3214/R/K25/D3/2011', '2011', '', '', 'Steven Arif', '2030-01-24 11:04:00', '2024-03-19 11:52:21', ''),
(14, '', 'Reguler', 'ELMIATI', 'PALOPO', '1995-01-10', 'MARTINA S', '7318295001950001', 'MANAJEMEN', '085399011178', 'f.ann.y.ka.rl.in.d.a.bnc@gmail.com', '@10/01/1995Ut', 'Protestan', 'Perempuan', 'Kawin', '', 'DN/PC/0334542', '2022', '3956271383', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(15, '', 'RPL', 'JULSIN ROMBE', 'RANTETAYO', '1990-07-30', 'HERMIN TANGDIKAMMA', '7318053007900005', 'PGSD', '085333001063', 'f.ann.y.ka.rl.i.n.dabn.c@gmail.com', '@30071990Ut', 'Protestan', 'Laki-laki', 'Kawin', '085394974982', '6,52012E+14', '0000', '', '', 'Steven Arif', '2023-02-24 10:42:00', '2024-03-19 11:52:21', ''),
(16, '', 'RPL', 'MARLINA MARSI', 'LIMBONG', '1987-03-28', 'ESTER MINGGU', '7318196803870002', 'PERPUSTAKAAN', '081244415583', 'f.ann.y.ka.rl.i.n.dab.nc@gmail.com', '@28031987Ut', 'Protestan', 'Perempuan', 'Kawin', '', 'CB 014586/3201310512', '2013', '', '', 'Steven Arif', '2030-01-24 10:55:00', '2024-03-19 11:52:21', ''),
(17, '', 'Reguler', 'ALBERTIN RANTE LA\'BI', 'KAYUOSING', '1991-10-10', 'MARTA BANNE BUA\'', '7318175010910001', 'PGPAUD', '082333004946', 'f.ann.y.ka.rl.i.nd.a.bn.c@gmail.com', '@10101991Ut', 'Islam', 'Perempuan', 'Kawin', '081355480974', 'DN-19 mK 0015335', '2010', '9914705478', '', 'Steven Arif', '2023-02-24 10:43:00', '2024-03-19 11:52:21', ''),
(18, '', 'RPL', 'IMELDA BURARA\'', 'ULUSALU', '1993-05-30', 'IDAYANI BURARA\'', '7318017005390002', 'PGSD', '085249914651', 'f.ann.y.ka.rl.i.n.dabnc@gmail.com', '@30051993Ut', 'Katolik', 'Perempuan', 'Kawin', '', '1226/1.23.9A/2017', '2017', '', '', 'Steven Arif', '2016-02-24 18:00:00', '2024-03-19 11:52:21', ''),
(19, '', 'Reguler', 'JUNITA AYU LESTARI', 'LAUANG', '1996-06-12', 'TAMAR PALONDONGAN', '7318375206950001', 'PGSD', '082315260681', 'f.ann.y.ka.rl.i.nd.a.b.n.c@gmail.com', '@12061996Ut', 'Protestan', 'Perempuan', 'Kawin', '082245280762', 'DN-PC 0227532', '2019', '9969524764', '', 'Steven Arif', '2023-02-24 10:46:00', '2024-03-19 11:52:21', ''),
(20, '', 'Reguler', 'KRISTIANI RANGGALAEN', 'MASANDA', '1994-03-05', 'MEWANG', '7603034503940001', 'PGPAUD', '082293724303', 'f.ann.y.ka.rl.i.nd.a.b.nc@gmail.com', '@05031994Ut', 'Protestan', 'Perempuan', 'Kawin', '', 'DN-32 Ma 0002685', '2013', '', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(21, '', 'RPL', 'LENY PICKY', 'UJUNG PANDANG', '1988-07-26', 'CHRISTINA K.', '7371136607870006', 'PGSD', '082163557763', 'f.ann.y.ka.rl.i.nda.b.nc@gmail.com', '@26071988Ut', 'Protestan', 'Perempuan', 'Kawin', '085397633234', '05242.3342.01.03.201', '2011', '', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(22, '', 'Reguler', 'NOVITHA PITER SALEMPANG', 'UJUNG PANDANG', '1982-11-28', 'AGUS TAMBING', '6471056811820003', 'HUKUM', '089509699060', 'f.ann.y.ka.rl.i.nd.ab.nc@gmail.com', '@28/11/1982Ut', 'Protestan', 'Perempuan', 'Belum Kawin', '', '06 Mu 0356668', '2001', '', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(23, '', 'Reguler', 'SELPINA ONGAN', 'MAMASA', '2003-09-19', 'ATTUK', '7603045510070001', 'EKONOMI PEMBANGUNAN', '085397393468', 'f.ann.y.ka.rl.i.nd.abn.c@gmail.com', '@19092003Ut', 'Protestan', 'Perempuan', 'Belum Kawin', '', 'DN/PC/0346008', '2022', '3035868339', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(24, '', 'Reguler', 'SRIWANI', 'TOMBANG', '1995-11-16', 'HERMIN LAI', '7318375611950002', 'PGSD', '082292349668', 'f.ann.y.ka.rl.i.nd.abn.c@gmail.com', '@16111995Ut', 'Protestan', 'Perempuan', 'Belum Kawin', '', 'DN-19 Mk 0033930', '2014', '', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(25, '', 'Reguler', 'YULPRIANI', 'TANDUNG', '1992-02-02', 'YULIANA BANNE LOLA\'', '7318314202920001', 'PGPAUD', '081341667239', 'f.ann.y.ka.rl.i.nd.abnc@gmail.com', '@02021992Ut', 'Protestan', 'Perempuan', 'Kawin', '', 'DN-PC 0270421', '2020', '9921067750', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(26, '', 'RPL', 'ALFRIANI CAMELIA PALA\'LANGAN', 'MAKALE', '1988-04-15', 'MARIA S. RUNDUPADANG', '7318055504880004', 'PENDIDIKAN PANCASILA DAN KEWARGANEGARAAN', '081342098600', 'f.ann.y.ka.rl.i.nda.b.n.c@gmail.com', '@15041988Ut', 'Protestan', 'Perempuan', 'Kawin', '081328298549', '0210231690/1.23.2A/2', '2010', '', '', 'Steven Arif', '2015-02-24 21:04:00', '2024-03-19 11:52:21', ''),
(27, '', 'Reguler', 'AGUSTINA RARA\' BULAWAN', 'MASANDA', '1995-08-08', 'BUNGAN', '7318314808050002', 'PENDIDIKAN PANCASILA DAN KEWARGANEGARAAN', '085342979199', 'f.ann.y.ka.rl.i.nda.bn.c@gmail.com', '@08081995Ut', 'Protestan', 'Perempuan', 'Kawin', '085299116767', 'DN-19 Ma 0025006', '2015', '9979908511', '', 'Steven Arif', '2026-02-24 16:22:00', '2024-03-19 11:52:21', ''),
(28, '', 'RPL', 'ANGREANI PATINARAN', 'MAROS', '1987-08-18', 'BERTA EMBONG BULAN', '7326115808870001', 'PENDIDIKAN PANCASILA DAN KEWARGANEGARAAN', '085255711190', 'f.ann.y.ka.rl.i.nda.bnc@gmail.com', '@18081987Ut', 'Protestan', 'Perempuan', 'Kawin', '', '032.02.01.03.09.2010', '2010', '', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(29, '', 'Reguler', 'SALVIANUS LOLOK GOROLANGI', 'TANA TORAJA', '1991-06-22', 'YULIANA PARANTE', '7318132206910002', 'PERPUSTAKAAN', '085213790168', 'f.ann.y.ka.rl.i.ndab.n.c@gmail.com', '@22061991Ut', 'Protestan', 'Laki-laki', 'Belum Kawin', '', 'DN-19 Ma 0014115', '2011', '', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(30, '', 'RPL', 'RUBEN PANGLOLI', 'SIMBUANG', '1987-08-19', 'MESING', '7318091908870001', 'PGSD', '085395637526', 'f.ann.y.ka.rl.i.ndab.nc@gmail.com', '@19081987Ut', 'Protestan', 'Laki-laki', 'Kawin', '', 'Sth.02/PP.09/VIII/54', '2011', '', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(31, '', 'Reguler', 'RITAWATI', 'PONGO\'', '1992-09-19', 'SALAMBA\'', '7318315809920001', 'PGSD', '081241731445', 'f.ann.y.ka.rl.i.ndabn.c@gmail.com', '@19091992Ut', 'Protestan', 'Perempuan', 'Kawin', '085390822211', 'DN-19 Ma 0020181', '2012', '9920587362', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(32, '', 'Reguler', 'ERNI BANGA\'', 'LIMBONG', '2000-12-29', 'YULI', '7318386912000001', 'PGSD', '082262352644', 'f.ann.y.ka.rl.ind.abn.c@gmail.com', '@29122000Ut', 'Protestan', 'Perempuan', 'Kawin', '', 'DN-Ma/06 190069359', '2018', '0008784552', '', 'Steven Arif', '2023-02-24 17:49:00', '2024-03-19 11:52:21', ''),
(33, '', 'RPL', 'SUSSA', 'SIMBUANG', '1986-12-01', 'DATU', '7318090112860001', 'PGSD', '082191840755', 'f.ann.y.ka.rl.in.d.a.b.nc@gmail.com', '@01121986Ut', 'Protestan', 'Laki-laki', 'Kawin', '081341121242', 'Sth.02/PP.09/VIII/52', '2011', '', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(34, '', 'Reguler', 'FITRIYANI', '', '2005-01-22', '', '', 'AKUNTANSI', '', '', '', '', '', '', '', '', '0000', '', '', 'Steven Arif', '0000-00-00 00:00:00', '2024-03-19 11:52:21', ''),
(35, '', 'RPL', 'DEFRI PONGSAPAN', 'LABOKKE', '2001-12-08', 'NATALIA', '7317080812010001', 'ADMINISTRASI NEGARA', '082343647580', 'f.ann.y.ka.rl.in.d.a.bn.c@gmail.com', '@08122001Ut', 'Protestan', 'Laki-laki', 'Belum Kawin', '081315319968', '', '0000', '', '', 'Steven Arif', '2015-02-24 21:10:00', '2024-03-19 11:52:21', ''),
(36, '', 'RPL', 'RISMA BOROTODING', 'PALI', '1991-09-06', 'RAHEL', '7318024906910002', 'PGSD', '082312225880', 'f.ann.y.ka.rl.in.d.ab.nc@gmail.com', '@09061991Ut', 'Islam', 'Perempuan', 'Kawin', '', '', '0000', '', '', 'Steven Arif', '2019-02-24 11:30:00', '2024-03-19 11:52:21', ''),
(37, '', 'RPL', 'ALPIANI RUMANIK MALLO', 'BAU', '1988-04-14', 'BERTA MONNA', '7318025404880002', 'PGSD', '082296527261', 'f.ann.y.ka.rl.in.d.abn.c@gmail.com', '@14041988UT', 'Islam', 'Perempuan', 'Kawin', '', '', '0000', '', 'SIPAS', 'Steven Arif', '2019-02-24 11:32:00', '2024-03-19 11:52:21', ''),
(38, '', 'Reguler', 'AMSAL TAULANGI', 'BONGGAKARADENG', '1984-09-09', 'YOHANA LIMBONG', '9109010909840015', 'ILMU PEMERINTAHAN', '085344268987', 'f.ann.y.ka.rl.in.da.bn.c@gmail.com', '@09001984Ut', 'Protestan', 'Laki-laki', 'Kawin', '', '', '0000', '', '', 'Steven Arif', '2027-02-24 16:21:00', '2024-03-19 11:52:21', ''),
(39, '', 'Reguler', 'NOVITA MARNI', 'SALU', '2000-02-12', 'DORKAS SEBO\'', '7318035202000001', 'MANAJEMEN', '082329188481', 'f.ann.y.ka.rl.in.da.bnc@gmail.com', '@12022000Ut', 'Protestan', 'Perempuan', 'Kawin', '', '', '0000', '', '', 'Hermanto Steven Lisu Allo Arif', '2022-02-24 23:25:00', '2024-03-19 11:52:21', ''),
(40, '', 'Reguler', 'NUR HIDAYAT RAHMAT RANI', 'PALI', '2000-07-22', 'MARSITA DATULIMBONG', '7318022207000001', 'ILMU PEMERINTAHAN', '081217501530', 'f.ann.y.ka.rl.in.dab.n.c@gmail.com', '@22072000Ut', 'Islam', 'Laki-laki', 'Belum Kawin', '085256305663', '', '0000', '', '', 'Hermanto Steven Lisu Allo Arif', '2022-02-24 23:27:00', '2024-03-19 11:52:21', ''),
(41, '', 'Reguler', 'ORPA PERO', 'MAKALE', '1993-08-08', 'ESTER LUMELE', '7318054810930004', 'HUKUM', '085326620147', 'f.ann.y.ka.rl.in.dab.nc@gmail.com', '@08081992Ut', 'Protestan', 'Perempuan', 'Kawin', 'SURAT PENGUNDUR', '', '0000', '', '', 'Hermanto Steven Lisu Allo Arif', '2022-02-24 23:29:00', '2024-03-19 11:52:21', ''),
(42, '', 'Reguler', 'NOBRIANTO DANTA', 'POMDINGAO\'', '2000-11-13', 'LIMBONG BINTOEN', '7318311211802001', 'PGSD', '081241883083', 'f.ann.y.ka.rl.in.dabn.c@gmail.com', '@13112000Ut', 'Protestan', 'Laki-laki', 'Belum Kawin', '', '', '0000', '', '', 'Hermanto Steven Lisu Allo Arif', '2022-02-24 23:47:00', '2024-03-19 11:52:21', ''),
(43, '', 'Reguler', 'ANTI PONGTANDI', 'KURRA', '2000-04-17', 'MERI PONGTANDI', '7318385704000001', 'PGSD', '082296689186', 'f.ann.y.ka.rl.in.dabnc@gmail.com', '@17042000Ut', 'Islam', 'Perempuan', 'Belum Kawin', '', '', '0000', '', 'SIPAS', 'Steven Arif', '2027-02-24 17:07:00', '2024-03-19 11:52:21', ''),
(44, '', 'Reguler', 'NUR AGNI BAHARUDDIN', 'MAKALE', '2005-03-26', 'SITTI HAJAR', '7318056603050002', 'SISTEM INFORMASI', '085654867223', 'f.ann.y.ka.rl.ind.a.b.n.c@gmail.com', '@26032005Ut', 'Islam', 'Perempuan', 'Belum Kawin', '', '', '0000', '', '', 'Steven Arif', '2027-02-24 17:13:00', '2024-03-19 11:52:21', ''),
(45, '', 'Reguler', 'AMELIA MANGI&#39;', 'MAKASSAR', '1995-08-16', 'SELFBERTIN ISUNG DATU PINDAN', '7318015608950001', 'Pembangunan', '082325992034', 'f.ann.y.ka.rl.ind.a.b.nc@gmail.com', '@16081995Ut', 'Islam', 'Perempuan', 'Kawin', '', '', '2000', '', 'SIPAS', 'Super Admin Satu', NULL, '2024-03-19 11:52:21', ''),
(47, '', 'Reguler', 'JULIVA RANGAN', 'PA\'GASINGAN', '2001-06-08', 'HERLINA PARE', '7318224806010004', 'MANAJEMEN', '085343843409', 'f.ann.y.ka.rl.ind.a.bnc@gmail.com', '@08062001Ut', 'Katolik', 'Perempuan', 'Kawin', '', '', '0000', '', '', 'Hermanto Steven Lisu Allo Arif', '2023-02-24 00:58:00', '2024-03-19 11:52:21', ''),
(48, '', 'Reguler', 'DUYUNG SIAMA', 'SANGALLA&#39;', '1999-12-30', 'RUTH SAMPE DAUN', '7318137012990003', 'Pembangunan', '082188030429', 'f.ann.y.ka.rl.ind.ab.n.c@gmail.com', '', '', 'Perempuan', 'Kawin', '082235187261', '', '2000', '', 'SIPAS', 'Super Admin Satu', NULL, '2024-03-20 14:45:33', ''),
(49, '', 'Reguler', 'MINA YUYU&#39;', 'BITTUANG', '2000-02-22', 'SARA LIKU', '7318026202000001', 'Pembangunan', '082291401435', 'f.ann.y.ka.rl.ind.abnc@gmail.com', '@22022000Ut', 'Islam', 'Perempuan', 'Belum Kawin', '085299995787', '', '2000', '', 'SIPAS', 'Super Admin Satu', NULL, '2024-03-19 11:52:21', ''),
(351, '', 'RPL', 'Erick Florensto Lisu Allo Arifa', 'Maumere', '2024-03-29', 'Yusfina Lisu Allo', '987456321', 'Pembangunan', '082271631094', 'stevenarif123@gmail.com', '', 'Kristen', 'Laki-laki', 'Kawin', '', '', '2000', '', 'SIPAS', 'Super Admin Satu', '2024-03-19 11:37:43', '2024-03-20 15:33:21', 'Belum Terdaftar'),
(356, '', 'Reguler', 'Hermanto Steven Lisu Allo Arif', 'Maumerehehe', '2024-02-29', 'Yusfina Lisu', '0123', 'Pembangunan', '082293924242', 'stevenarif123@gmail.com', '', 'Kristen', 'Perempuan', 'Belum Kawin', '', '', '2000', '', 'SIPAS', 'Super Admin Satu', '2024-03-19 11:49:57', '2024-03-20 15:31:14', 'Belum Terdaftar');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`No`),
  ADD UNIQUE KEY `NIK` (`NIK`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `No` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=358;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
