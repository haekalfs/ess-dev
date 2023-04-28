-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2023 at 05:35 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ess`
--

-- --------------------------------------------------------

--
-- Table structure for table `users_details`
--

CREATE TABLE `users_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` varchar(25) DEFAULT NULL,
  `position_id` varchar(200) DEFAULT NULL,
  `department_id` varchar(50) DEFAULT NULL,
  `employee_status` varchar(25) DEFAULT NULL,
  `usr_dob` varchar(90) DEFAULT NULL,
  `usr_birth_place` varchar(90) DEFAULT NULL,
  `usr_gender` varchar(90) DEFAULT NULL,
  `usr_npwp` varchar(255) DEFAULT NULL,
  `usr_religion` varchar(45) DEFAULT NULL,
  `usr_merital_status` varchar(45) DEFAULT NULL,
  `usr_children` varchar(45) DEFAULT NULL,
  `usr_id_type` varchar(45) DEFAULT NULL,
  `usr_id_no` varchar(255) DEFAULT NULL,
  `usr_id_expiration` varchar(90) DEFAULT NULL,
  `status_active` varchar(25) DEFAULT NULL,
  `hired_date` varchar(150) DEFAULT NULL,
  `resignation_date` varchar(150) DEFAULT NULL,
  `usr_address` varchar(255) NOT NULL,
  `usr_address_city` varchar(45) DEFAULT NULL,
  `usr_address_postal` varchar(45) DEFAULT NULL,
  `usr_phone_home` varchar(90) DEFAULT NULL,
  `usr_phone_mobile` varchar(90) DEFAULT NULL,
  `usr_bank_name` varchar(90) DEFAULT NULL,
  `usr_bank_branch` varchar(90) DEFAULT NULL,
  `usr_bank_account` varchar(255) DEFAULT NULL,
  `user_id` varchar(100) DEFAULT NULL,
  `current_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_details`
--

INSERT INTO `users_details` (`id`, `employee_id`, `position_id`, `department_id`, `employee_status`, `usr_dob`, `usr_birth_place`, `usr_gender`, `usr_npwp`, `usr_religion`, `usr_merital_status`, `usr_children`, `usr_id_type`, `usr_id_no`, `usr_id_expiration`, `status_active`, `hired_date`, `resignation_date`, `usr_address`, `usr_address_city`, `usr_address_postal`, `usr_phone_home`, `usr_phone_mobile`, `usr_bank_name`, `usr_bank_branch`, `usr_bank_account`, `user_id`, `current_address`, `created_at`, `updated_at`) VALUES
(1, '1048', '10', NULL, NULL, '2002-07-29', 'Bogor', 'M', '10210382753', 'Islam', 'S', '0', 'KTP', '120391023', '2099-10-29', NULL, '2020-07-07', '-', 'Jln. Cipinang Kalimalang No.2 Kalimalang Timur, Jakarta Timur', 'Jakarta Timur', '12093', '0812313112', '09012380123', 'Mandiri', 'Bintara', '120390131', 'haekals', 'Jln. Cipinang Kalimalang No.2 Kalimalang Timur, Jakarta Timur', NULL, NULL),
(2, '1048', NULL, NULL, NULL, '2002-07-27', 'Bogor', 'M', '10210382753', 'Islam', 'S', '0', 'KTP', '120391023', '2099-10-29', NULL, '2020-07-07', '-', 'Jln. Cipinang Kalimalang No.2 Kalimalang Timur, Jakarta Timur', 'Jakarta Timur', '12093', '0812313112', '09012380123', 'Mandiri', 'Bintara', '120390131', 'haekals2', 'Jln. Cipinang Kalimalang No.2 Kalimalang Timur, Jakarta Timur', NULL, NULL),
(3, '1066', NULL, NULL, NULL, '1976-07-18 00:00:00', 'Bandung', 'M', '48.718.275.0-404.000', NULL, 'M', '1', 'KTP', '3,27105E+15', '2099-12-31 00:00:00', '', NULL, NULL, 'Jl. Janaka I No.9 RT 002 / RW 015 Kelurahan Tegal Gundil, Kecamatan Kota Bogor Utara', NULL, NULL, '0', '0', 'Bank Mandiri', NULL, '123-00-0460945-1', 'adhi', 'Jl. Janaka I No.9 RT 002 / RW 015 Kelurahan Tegal Gundil, Kecamatan Kota Bogor Utara', NULL, NULL),
(4, '1063', NULL, NULL, NULL, '1984-11-17 00:00:00', 'Tegal', 'M', '88.157.211.9-501.000', NULL, 'M', '1', 'KTP', '3,3281E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Tegalharjo RT 002 RW 004 Kel. Tegalharjo  Kec. Jebres', NULL, NULL, '+62 817-279-510', '+62 817-279-510', 'Bank Mandiri', NULL, '1020006599465 ', 'adip', 'Tegalharjo RT 002 RW 004 Kel. Tegalharjo  Kec. Jebres', NULL, NULL),
(5, '111', NULL, NULL, NULL, '2013-12-05 00:00:00', ' ', ' ', ' ', NULL, ' ', '0', 'KTP', ' ', '2013-12-05 00:00:00', 'Active', NULL, NULL, ' ', NULL, NULL, ' ', ' ', ' ', NULL, ' ', 'admin', '', NULL, NULL),
(6, '1024', NULL, NULL, NULL, '1980-05-02 00:00:00', 'Gunung Marisi', 'M', '26.371.654.0-411.000', NULL, 'M', '0', 'KTP', '1,31202E+15', '2017-05-02 00:00:00', 'Active', NULL, NULL, 'Jl Sulawesi Jorong Tanjung Damai, Ujung Gading, Pasaman Barat, Sumatera Barat', NULL, NULL, '087775051210', '087775051210', 'BCA', NULL, '4971503496', 'ahabror', 'Cipondoh Makmur Blok C 14/8 Rt 009 Rw 004, Cipondoh, Tangerang', NULL, NULL),
(7, '1091', NULL, NULL, NULL, '1998-04-09 00:00:00', 'Jakarta', 'M', '0000000000', NULL, 'S', '0', 'KTP', '3,17409E+15', '9999-12-31 00:00:00', 'Active', NULL, NULL, 'Jl. Kemenyan KP. Setu, RT 007/RW 005, Kelurahan Ciganjur, Kecamatan Jagakarsa, Jakarta', NULL, NULL, '081211179499', '081211179499', 'BNI', NULL, '1449501986 ', 'akbar', 'Jl. Kemenyan KP. Setu, RT 007/RW 005, Kelurahan Ciganjur, Kecamatan Jagakarsa, Jakarta', NULL, NULL),
(8, '1033', NULL, NULL, NULL, '1996-06-03 00:00:00', 'Sleman', 'M', '93.570.920.4-542.000', NULL, 'S', '0', 'KTP', '3,40408E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Klodangan RT 001 RW 026 Kel. Sendangtirto Kec. Berbah Kab. Sleman', NULL, NULL, '085103244443', '085103244443', 'Bank BTN', NULL, '00005-01-56-002567-7', 'akmal', 'Klodangan RT 001 RW 026 Kel. Sendangtirto Kec. Berbah Kab. Sleman', NULL, NULL),
(9, '1079', NULL, NULL, NULL, '1998-12-20 00:00:00', 'Cilacap', 'M', '41.599.485.4-542.000', NULL, 'S', '0', 'KTP', '3,30123E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Jalan Dr. Rajiman No.21 RT 001 rw 005, Kebon Manis, Cilacap Utara, Kabupaten Cilacap', NULL, NULL, '+62 821-3378-7266', '+62 821-3378-7266', 'BSI Syariah Indonesia', NULL, '7193388300', 'aldi', 'Jalan Dr. Rajiman No.21 RT 001 rw 005, Kebon Manis, Cilacap Utara, Kabupaten Cilacap', NULL, NULL),
(10, '781', NULL, NULL, NULL, '1982-11-30 00:00:00', 'Banyumas', 'M', '47.698.918.1-404.000', NULL, 'M', '3', 'KTP', '3,20104E+15', '2016-11-30 00:00:00', 'Active', NULL, NULL, 'Pesona Cilebut 2 Blok IB05 No.12', NULL, NULL, '0818-02903916', '0818-02903916', 'BCA', NULL, '0952483640', 'amiens', '', NULL, NULL),
(11, '1084', NULL, NULL, NULL, '1995-03-22 00:00:00', 'Jakarta', 'M', '83.102.893.1-403.000', NULL, 'M', '1', 'KTP', '3,20102E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Taman Rempoa Indah Blok M No. 6 \nRempoa, Ciputat, Tangerang Selatan', NULL, NULL, '+62 813-8075-9522', '+62 813-8075-9522', ' Mandiri', NULL, '1,02002E+12', 'ansyar', 'Taman Rempoa Indah Blok M No. 6 \nRempoa, Ciputat, Tangerang Selatan', NULL, NULL),
(12, '817', NULL, NULL, NULL, '1959-09-27 00:00:00', 'Bandung', 'M', '07.766.343.3-428.000', NULL, 'M', '3', 'KTP', '3,27307E+15', '2011-09-27 00:00:00', 'Active', NULL, NULL, 'Jl. Sukajadi No. 192', NULL, NULL, '0812-9919161', '0812-9919161', 'BCA', NULL, '2331606431', 'ardonop', '', NULL, NULL),
(13, '1088', NULL, NULL, NULL, '1995-06-26 00:00:00', 'Sidenreng Rappang', 'M', '92.971.660.3-802.000', NULL, 'S', '0', 'KTP', '7,3141E+15', '9999-12-31 00:00:00', 'Active', NULL, NULL, 'DSN IV Bulucenrana RT 002 / RW 002, Desa\nBulucenrana, Kecamatan Pitu Riawa,', NULL, NULL, '0822-9240-5066', '0822-9240-5066', 'BCA', NULL, '2210083459', 'arfan', '', NULL, NULL),
(14, '1105', NULL, NULL, NULL, '2000-04-03 00:00:00', 'Palembang', 'F', '6,52666E+14', NULL, 'S', '0', 'KTP', '1,67106E+15', '9999-01-01 00:00:00', 'Active', NULL, NULL, 'Jl. Supersemar No. 1133 RT. 015/RW. 003, Kel/Desa Pipa Reja, Kec. Kemuning, Kota Palembang, Provinsi Sumatera Selatan', NULL, NULL, '082177586977', '082177586977', 'Mandiri', NULL, '1,12001E+12', 'arizki', 'Kalibata City Apartment, Tulip Tower, Jl. Raya Kalibata No.60, RT.9/RW.4, Rawajati, Kec. Pancoran, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12750', NULL, NULL),
(15, '1019', NULL, NULL, NULL, '1996-01-11 00:00:00', 'Jakarta', 'F', '73.757.447.5-023.000', NULL, 'S', '0', 'KTP', '3,17105E+15', '2099-01-01 00:00:00', 'Active', NULL, NULL, 'JL. Kramat Sentiong I No. D92 RT 011 RW 005 Kelurahan Kramat Kecamatan Senen', NULL, NULL, '081310734013', '081310734013', 'BCA', NULL, '5285077076', 'atikr', 'JL. Kramat Sentiong I No. D92 RT 011 RW 005 Kelurahan Kramat Kecamatan Senen', NULL, NULL),
(16, '1075', NULL, NULL, NULL, '1997-07-20 00:00:00', 'Cilacap', 'F', ' ', NULL, 'S', '0', 'KTP', '3,30104E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Sinar Street No. 55 Adipala District, Karangbenda Regency', NULL, NULL, '085870867110', '085870867110', ' ', NULL, ' ', 'ayu', 'Sinar Street No. 55 Adipala District, Karangbenda Regency', NULL, NULL),
(17, '746', NULL, NULL, NULL, '1963-07-22 00:00:00', 'Yogyakarta', 'M', '69.759.924.9-402.000', NULL, 'M', '3', 'KTP', '3,67109E+15', '2013-07-22 00:00:00', 'Active', NULL, NULL, 'Jl. Vanda V No. 10 Palem Semi Rt 01 Rw 011', NULL, NULL, ' ', ' ', 'BCA', NULL, '681-500-208-7', 'banuw', '', NULL, NULL),
(18, '1022', NULL, NULL, NULL, '1975-04-02 00:00:00', 'Kebumen', 'M', '08.582.874.9-009.000', NULL, 'M', '2', 'KTP', '3,17404E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Jl. Setu Indah VI D No. 2 Setu, Cipayung', NULL, NULL, '081806492826', '081806492826', 'Bank BCA', NULL, '3191972111', 'bs', 'Jl. Setu Indah VI D No. 2 Setu, Cipayung', NULL, NULL),
(19, '1036', NULL, NULL, NULL, '1989-04-09 00:00:00', 'Jakarta', 'M', '44.433.675.4-008.000', NULL, 'M', '1', 'KTP', '3,17507E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'JL. Rifadin No. 1 RT 009 RT 014 Kel. Duren Sawit', NULL, NULL, '087775040655', '087775040655', 'Bank BCA', NULL, '6330664311', 'daf', 'Tria Adara Residence 1 No. 38 Sawangan, Depok', NULL, NULL),
(20, '1046', NULL, NULL, NULL, '1972-01-25 00:00:00', 'Pekanbaru', 'M', '47.181.714.8-044.000', NULL, 'M', '2', 'KTP', '3,17205E+15', '2020-01-25 00:00:00', 'Active', NULL, NULL, 'Komplek Gading Depok Residence Blok A No. 10\nJl. Raya Curug, Bojongsari, Sawangan, Depok', NULL, NULL, '081388119940', '081388119940', 'Mandiri', NULL, '1,02001E+12', 'daniel', 'Komplek Gading Depok Residence Blok A No. 10\nJl. Raya Curug, Bojongsari, Sawangan, Depok', NULL, NULL),
(21, '1103', NULL, NULL, NULL, '1995-12-01 00:00:00', 'Jakarta', 'F', '9,28358E+14', NULL, 'M', '0', 'KTP', '3,17494E+15', '9999-01-01 00:00:00', 'Active', NULL, NULL, 'Jl. Masjid Al Fajri No. 6B RT.012/RW.001 Kel. Pejaten Barat, Kec. Pasar Minggu, Jakarta Selatan', NULL, NULL, '081382666112', '081382666112', 'Mandiri', NULL, '1,24001E+12', 'desy', 'Jl. Masjid Al Fajri No. 6B RT.012/RW.001 Kel. Pejaten Barat, Kec. Pasar Minggu, Jakarta Selatan', NULL, NULL),
(22, '1077', NULL, NULL, NULL, '1998-06-29 00:00:00', 'Brebes', 'F', ' ', NULL, 'S', '0', 'KTP', ' 3329086906980003', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Jl. Taiman Raya No. 10, RT. 07/ RW. 10 Kel. Gedong, Kec. Pasar Rebo.\nJakarta Timur 13760', NULL, NULL, '08232955127', '08232955127', ' ', NULL, ' ', 'dinda', 'Perumahan kota wisata cluster orlando RA No. 34 Kel. Ciangsang, Kb. Bogor, Gunung Putri Jawa Barat', NULL, NULL),
(23, '1099', NULL, NULL, NULL, '1996-02-17 00:00:00', 'Sukabumi', 'M', '000000', NULL, 'M', '0', 'KTP', '3,20201E+15', '9999-12-31 00:00:00', 'Active', NULL, NULL, 'Kp Sumur No 81 RT 002 RW 010, Kelurahan Klender, Kecamatan Duren Sawit, Jakarta Timur ', NULL, NULL, '083818250752', '083818250752', 'BCA', NULL, '2610142390', 'dira', 'Kp Sumur No 81 RT 002 RW 010, Kelurahan Klender, Kecamatan Duren Sawit, Jakarta Timur ', NULL, NULL),
(24, '1017', NULL, NULL, NULL, '1988-01-20 00:00:00', 'Purworejo', 'F', '88.250.259.4-414.000', NULL, 'S', '0', 'KTP', '3,30611E+15', '2017-01-20 00:00:00', 'Active', NULL, NULL, 'Binangun RT 002 RW 001 Kel. Binangun Kec. Butuh, Purworejo Jawa Tengah', NULL, NULL, '081296555452', '081296555452', 'KCP Metro Jababeka', NULL, '8730255081', 'djayanti', 'Binangun RT 002 RW 001 Kel. Binangun Kec. Butuh, Purworejo - Jawa Tengah', NULL, NULL),
(25, '1073', NULL, NULL, NULL, '1998-05-05 00:00:00', 'Serang', 'F', ' ', NULL, 'S', '0', 'KTP', '3604034505980400													3604034505980400', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Kp. Margaluyu RT 022/ RW 001 Kec. Kasemen Serang Banten', NULL, NULL, '082124626773', '082124626773', ' ', NULL, ' ', 'erina', 'Kp. Margaluyu RT 022/ RW 001 Kec. Kasemen Serang Banten', NULL, NULL),
(26, '9999', NULL, NULL, NULL, '2021-09-13 00:00:00', ' ``', 'M', ' ', NULL, 'S', '0', 'KTP', ' ', '2021-09-13 00:00:00', 'Active', NULL, NULL, '  ', NULL, NULL, '  ', '  ', ' ', NULL, ' ', 'essadmin', '   ', NULL, NULL),
(27, '1062', NULL, NULL, NULL, '1998-03-12 00:00:00', 'Cilacap', 'M', ' ', NULL, 'S', '0', 'KTP', '3,37411E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Pondok Bukit Agung Blok E/40 Sumurboto, Banyumantik, Semarang', NULL, NULL, '082233695080', '082233695080', 'Bank BNI', NULL, '0427175477 ', 'faiq', 'Pondok Bukit Agung Blok E/40 Sumurboto, Banyumantik, Semarang', NULL, NULL),
(28, '1101', NULL, NULL, NULL, '1989-12-20 00:00:00', 'Jakarta', 'M', '7,76536E+14', NULL, 'M', '0', 'KTP', '3,17408E+15', '9999-01-01 00:00:00', 'Active', NULL, NULL, 'Jl. Masjid Baru RT. 006/RW. 001 Kel. Pejaten Timur, Kec. Pasar Minggu, Jakarta Selatan, DKI Jakarta', NULL, NULL, '6,28382E+12', '6,28382E+12', 'BCA', NULL, '0650747879', 'firillah', 'Jl. Masjid Baru RT. 006/RW. 001 Kel. Pejaten Timur, Kec. Pasar Minggu, Jakarta Selatan, DKI Jakarta', NULL, NULL),
(29, '1048', NULL, NULL, NULL, '2002-07-29 00:00:00', 'Bogor', 'M', '0', NULL, 'S', '0', 'KTP', '3,27105E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Jl. Kalibaru Timur dalam GG 3/254 Kel. Bungur, Kec. Senen, Jakarta Pusat', NULL, NULL, '083818333672', '083818333672', 'Mandiri', NULL, ' 1030007732361', 'haekals', 'Jl. Kalibaru Timur dalam GG 3/254 Kel. Bungur, Kec. Senen, Jakarta Pusat', NULL, NULL),
(30, '1078', NULL, NULL, NULL, '1990-02-26 00:00:00', 'Yogyakarta', 'M', '74.652.758.9-541.000', NULL, 'M', '1', 'KTP', '3,47104E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Bausasran DN. 3/650 RT 033 RW 09\nKel. Bausasran Kec. Danurejan Kota Yogyakarta', NULL, NULL, ' 62 812-8337-2126', ' 62 812-8337-2126', 'Bank Mandiri', NULL, '1,24001E+12', 'hageng', 'Bausasran DN. 3/650 RT 033 RW 09\nKel. Bausasran Kec. Danurejan Kota Yogyakarta', NULL, NULL),
(31, '1097', NULL, NULL, NULL, '1982-04-21 00:00:00', 'Jakarta', 'F', '099734949024000', NULL, 'M', '3', 'KTP', '3,17106E+15', '9999-12-31 00:00:00', 'Active', NULL, NULL, 'KP. Kapitan No. 2, RT 007/RW 004, Kel. Klender, Kec, Duren Sawit, Jakarta Timur', NULL, NULL, '08111386622', '08111386622', 'BCA', NULL, '0050070352', 'hartini', 'KP. Kapitan No. 2, RT 007/RW 004, Kel. Klender, Kec, Duren Sawit, Jakarta Timur', NULL, NULL),
(32, '945', NULL, NULL, NULL, '1987-01-20 00:00:00', 'Jakarta', 'M', '69.759.931.4-009.000', NULL, 'M', '1', 'KTP', '3,1751E+15', '2017-01-20 00:00:00', 'Active', NULL, NULL, 'Jl. Mandor Hasan No. 26 Rt 004 Rw 006,Cipayung', NULL, NULL, '0815-14089331', '0815-14089331', 'Bank Mandiri', NULL, '1,03001E+12', 'ilhamap', 'Green Grass Townhouse Ciracas unit B9. \nJl. Raya Kelapa Dua Wetan. Kel Kelapa Dua Wetan. Kec  Ciracas.\n\n', NULL, NULL),
(33, '914', NULL, NULL, NULL, '1986-09-02 00:00:00', 'Jakarta', 'F', ' 88.792.156.7-001.000', NULL, 'M', '1', 'KTP', '3,17501E+15', '2015-09-02 00:00:00', 'Active', NULL, NULL, 'Jl. Kesatrian VIII H.40 Rt 007 Rw 003, Kebon Manggis, Matraman', NULL, NULL, '088218307852', '088218307852', ' BCA', NULL, ' 3423226061', 'irna', '', NULL, NULL),
(34, '1104', NULL, NULL, NULL, '1983-05-21 00:00:00', 'Cirebon', 'M', '4,91478E+14', NULL, 'M', '1', 'KTP', '3,21608E+15', '9999-01-01 00:00:00', 'Active', NULL, NULL, 'Perum Taman Sentosa Blok C 7/5 RT. 026/RW. 007 Kel. Pasir Sari, Kec. Cikarang Selatan, Kab. Bekasi, Jawa Barat', NULL, NULL, '', '', 'Mandiri', NULL, '1,56002E+12', 'irsyad', 'Perum Taman Sentosa Blok C 7/5 RT. 026/RW. 007 Kel. Pasir Sari, Kec. Cikarang Selatan, Kab. Bekasi, Jawa Barat', NULL, NULL),
(35, '1096', NULL, NULL, NULL, '1988-12-18 00:00:00', 'Cirebon', 'M', '5,50985E+14', NULL, 'M', '2', 'KTP', '3,20924E+15', '9999-12-31 00:00:00', 'Active', NULL, NULL, 'Jl. Matraman Dalam 2 No. 23, RT 009/RW 008, Kel. Pegangsaan, Kec. Menteng', NULL, NULL, '082112343216', '082112343216', 'BCA', NULL, '1341836102', 'islam akbar', 'Jl. Matraman Dalam 2 No. 23, RT 009/RW 008, Kel. Pegangsaan, Kec. Menteng', NULL, NULL),
(36, '1043', NULL, NULL, NULL, '1995-10-30 00:00:00', 'Serang', 'M', '90.147.538.4-417.000', NULL, 'S', '0', 'KTP', '3,67207E+15', '2019-10-30 00:00:00', 'Active', NULL, NULL, 'Jl. Besi IV No. 33 Komp. KS Rt 004 Rw 004, Kotabumi, Purwakarta', NULL, NULL, '085217286934', '085217286934', 'BCA', NULL, '4500328679', 'isnank', 'Jl. Besi IV No. 33 Komp. KS Rt 004 Rw 004, Kotabumi, Purwakarta', NULL, NULL),
(37, '1092', NULL, NULL, NULL, '2022-09-16 00:00:00', 'Jakarta', 'M', '0000000000', NULL, 'S', '0', 'KTP', '3,17108E+15', '9999-12-31 00:00:00', 'Active', NULL, NULL, 'KP Rawa Selatan II No. 21, RT 009/RW 007, Kelurahan/Desa Kampung Rawa, Kecamatan Johar Baru, Kota/Kabupaten Jakarta Pusat', NULL, NULL, '085714476120', '085714476120', 'Mandiri', NULL, '1,65E+12', 'julyansyah', 'KP Rawa Selatan II No. 21, RT 009/RW 007, Kelurahan/Desa Kampung Rawa, Kecamatan Johar Baru, Kota/Kabupaten Jakarta Pusat', NULL, NULL),
(38, '1030', NULL, NULL, NULL, '1987-01-21 00:00:00', 'Jakarta', 'M', '66.954.442.1-411.000', NULL, 'M', '2', 'KTP', '3,1741E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Jl. Kebon Kopi RT 005 RW 004 Kel. Pondok Betung Kec. Pondok Aren ', NULL, NULL, '083808490258', '083808490258', 'Bank Mandiri Syariah', NULL, '7106152025', 'murdim', 'Jl. Kebon Kopi RT 005 RW 004 Kel. Pondok Betung Kec. Pondok Aren Tangerang Selatan', NULL, NULL),
(39, '1069', NULL, NULL, NULL, '1996-03-28 00:00:00', ' Sukoharjo', 'F', ' ', NULL, 'S', '0', 'KTP', '3,31113E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Jl. Pondok Baru Asri I Rt 005 Rw 003, Gumpang, Kartasura, Sukoharjo, Jawa Tengah', NULL, NULL, '+62 813-9300-5027', '+62 813-9300-5027', 'Bank BCA', NULL, '5005256221 ', 'najla', 'Jl. Pondok Baru Asri I Rt 005 Rw 003, Gumpang, Kartasura, Sukoharjo, Jawa Tengah', NULL, NULL),
(40, '1102', NULL, NULL, NULL, '1997-11-19 00:00:00', 'Serang', 'M', '7,76536E+14', NULL, 'S', '0', 'KTP', '3,17402E+15', '9999-01-01 00:00:00', 'Active', NULL, NULL, 'Jl. Munawaroh Raya Blok K. 10/6 RT. 002/RW. 014 Kel. Panunggangan Barat, Kec. Cibodas, Kota Tangerang, Banten', NULL, NULL, '081212225413', '081212225413', 'BCA', NULL, '0650747879', 'nova', '', NULL, NULL),
(41, '783', NULL, NULL, NULL, '1970-03-19 00:00:00', 'Semarang', 'M', '48.467.028.6-005.000', NULL, 'M', '3', 'KTP', '3,17509E+15', '2016-03-19 00:00:00', 'Active', NULL, NULL, 'Jl. H. Abdurrahman No. 32 Rt 003 Rw 005', NULL, NULL, ' 0818-749441', ' 0818-749441', 'BCA', NULL, '607-025-872-1', 'nursaid', '', NULL, NULL),
(42, '1060', NULL, NULL, NULL, '1995-03-31 00:00:00', 'Jakarta', 'F', ' ', NULL, 'M', '1', 'KTP', '3,27506E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Cluster Oriana Permata Bintaro Jl. Oriana XII F12/12, Jombang, CiputatKota Tangerang Selatan, Banten, 15414', NULL, NULL, '08117081995', '08117081995', ' ', NULL, ' ', 'poppy', 'Cluster Oriana Permata Bintaro Jl. Oriana XII F12/12, Jombang, Ciputat Kota Tangerang Selatan, Banten, 15414', NULL, NULL),
(43, '1100', NULL, NULL, NULL, '2023-01-12 00:00:00', 'Medan', 'F', '0000000000', NULL, 'S', '0', 'KTP', '1,40117E+15', '9999-01-01 00:00:00', 'Active', NULL, NULL, 'Gg. Mulia 2 No. 37, Plemburan Tegal RT. 005/RW. 025, Kel. Sariharjo, Kec. Ngaglik, Kab. Sleman, Daerah Istimewa Yogyakarta', NULL, NULL, '081390357268', '081390357268', 'BCA', NULL, '8610425675', 'poppyw', 'The Mansion At Kemang. Jl. Kemang Raya No.3-5, RT.1/RW.7, Bangka, Kec. Mampang Prpt., Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12730', NULL, NULL),
(44, '1042', NULL, NULL, NULL, '1990-11-25 00:00:00', 'Bantul', 'M', '74.312.403.4-543.000', NULL, 'M', '0', 'KTP', '3,40212E+15', '2019-11-25 00:00:00', 'Active', NULL, NULL, 'Karang Jambe Rt 004/- Banguntapan, Bantul', NULL, NULL, '08111507277', '08111507277', 'BCA', NULL, '4500282288', 'radityaa', 'Karang Jambe Rt 004/- Banguntapan, Bantul', NULL, NULL),
(45, '1095', NULL, NULL, NULL, '2000-10-11 00:00:00', 'Tg. Pinang', 'M', '0000000000', NULL, 'S', '0', 'KTP', '1,27106E+15', '9999-12-31 00:00:00', 'Active', NULL, NULL, 'Jl. Marelan V Gg. Argo Psr. 2 Barat Lingk 02, RT 000/RW 000, Kel/Desa Terjun, Kecamatan Medan Marelan, Sumatera Utara', NULL, NULL, '0812-2495-2643', '0812-2495-2643', 'BRI', NULL, '069401021236500', 'rafi', 'Jl. Marelan V Gg. Argo Psr. 2 Barat Lingk 02, RT 000/RW 000, Kel/Desa Terjun, Kecamatan Medan Marelan, Sumatera Utara', NULL, NULL),
(46, '1067', NULL, NULL, NULL, '1984-06-20 00:00:00', 'Surabaya', 'M', '49.570.396.9-432.000', NULL, 'M', '1', 'KTP', '3,27508E+15', '2017-06-20 00:00:00', 'Active', NULL, NULL, 'Jl. Karmila 4 Blok F5 No. 10 RT/RW 012/013 Kel. Jatiwaringin Kec. Pondok Gede ', NULL, NULL, '082111087711', '082111087711', 'Bank BCA', NULL, '6805016080', 'rherawan', 'Jl. Karmila 4 Blok F5 No. 10 RT/RW 012/013 Kel. Jatiwaringin Kec. Pondok Gede ', NULL, NULL),
(47, '807', NULL, NULL, NULL, '1957-12-18 00:00:00', 'Cirebon', 'M', '07.582.865.7-015.000', NULL, 'M', '2', 'KTP', '3,17401E+15', '2016-12-18 00:00:00', 'Active', NULL, NULL, 'Jl. Tebet Timur Dalam IV-B/10 Rt 003 Rw 011, Tebet Timur', NULL, NULL, '0816-903359', '0816-903359', 'CIMB Niaga', NULL, '058-01-04904-009', 'riyantog', 'Jl. Tebet Timur Dalam 3 No.8, Tebet', NULL, NULL),
(48, '800', NULL, NULL, NULL, '1957-12-18 00:00:00', 'Cirebon', 'M', '18-12-1957', NULL, 'M', '2', 'KTP', '3,17401E+15', '2016-12-18 00:00:00', 'Active', NULL, NULL, 'Jl. Tebet Timur Dalam IV-B/10 Rt 003 Rw 011, Tebet Timur', NULL, NULL, '0816-903359', '0816-903359', 'CIMB Niaga', NULL, '058-01-04904-009', 'riyantogo', 'Jl. Tebet Timur Dalam 3 No.8, Tebet', NULL, NULL),
(49, '788', NULL, NULL, NULL, '1981-12-20 00:00:00', 'Purwokerto', 'M', '47.698.908.2-404.000', NULL, 'M', '2', 'KTP', '3,27103E+15', '2016-12-20 00:00:00', 'Active', NULL, NULL, 'Bukit Cimanggu City Blok S-6/1 Rt 001 Rw 014', NULL, NULL, ' ', ' ', 'BCA', NULL, '6815005205', 'ronnyp', '', NULL, NULL),
(50, '956', NULL, NULL, NULL, '1990-01-29 00:00:00', 'Bandung', 'F', '77.772.226.5-401.000', NULL, 'M', '1', 'KTP', '3,60402E+15', '2018-01-29 00:00:00', 'Active', NULL, NULL, 'JL. Kencana Raya Blok H 3 / 21 Sekt. XII BSD RT 005 RW 014 Rawabuntu, Serpong', NULL, NULL, '081291558622', '081291558622', 'BCA', NULL, '2450486534', 'sarraa', 'JL. Kencana Raya Blok H 3 / 21 Sekt. XII BSD RT 005 RW 014 Rawabuntu, Serpong', NULL, NULL),
(51, '1093', NULL, NULL, NULL, '1986-01-04 00:00:00', 'Jakarta', 'M', '8,9365E+14', NULL, 'M', '2', 'KTP', '3,27503E+15', '9999-12-31 00:00:00', 'Active', NULL, NULL, 'Bulak Macan Permai Jalan Berlian IV Blok B No. 101 RT 008 RW 013, Kelurahan Harapan Jaya, Kecamatan Bekasi Utara, Kota Bekasi ', NULL, NULL, '0811-247-411', '0811-247-411', 'Bank Central Asia ', NULL, '5210549686', 'singgih', 'Bulak Macan Permai Jalan Berlian IV Blok B No. 101 RT 008 RW 013, Kelurahan Harapan Jaya, Kecamatan Bekasi Utara, Kota Bekasi ', NULL, NULL),
(52, '916', NULL, NULL, NULL, '1985-08-29 00:00:00', 'Tegal', 'F', '16.951.185.4-024.000', NULL, 'M', '2', 'KTP', '3,17109E+15', '2016-08-29 00:00:00', 'Active', NULL, NULL, 'Jl. Percetakan Negara II No. 11 Rt 017/Rw 007, Johar Baru', NULL, NULL, '082111116784', '082111116784', 'BCA', NULL, '7000384938', 'sundari', '', NULL, NULL),
(53, '761', NULL, NULL, NULL, '1970-02-05 00:00:00', 'Karawang', 'M', '47.698.920.7-404.000', NULL, 'M', '2', 'KTP', '32.03.07.050270.04161', '2013-02-05 00:00:00', 'Active', NULL, NULL, 'Laladon Permai Blok G/10 No. 10', NULL, NULL, '0812-9938526', '0812-9938526', 'BCA', NULL, '681-500-106-4', 'suryadi', '', NULL, NULL),
(54, '1026', NULL, NULL, NULL, '1972-03-08 00:00:00', 'Jakarta', 'M', '24.441.031.2-004.000', NULL, 'M', '3', 'KTP', '3,17506E+15', '2016-03-08 00:00:00', 'Active', NULL, NULL, 'Jl. P. Komarudin I No. 50 Rt 012 Rw 005', NULL, NULL, '081990339000', '081990339000', 'BCA', NULL, '8720401151', 'taufiki', '', NULL, NULL),
(55, '760', NULL, NULL, NULL, '1975-07-06 00:00:00', 'Jakarta', 'F', '47.698.919.9-412.000', NULL, 'S', '0', 'KTP', '3,27604E+15', '2016-07-06 00:00:00', 'Active', NULL, NULL, 'Jl. Ibnu Armah I Rt 001 Rw 004, No 71 Pangkalan Jati Baru, Cinere', NULL, NULL, '', '', 'BCA', NULL, '681-5000-114', 'yuliyanti', 'Jl. Ibnu Armah I Rt 001 Rw 004, No 71 Pangkalan Jati Baru, Cinere', NULL, NULL),
(56, '1083', NULL, NULL, NULL, '1999-09-13 00:00:00', 'Kolaka', 'M', '65.183.431.9-815.000', NULL, 'S', '0', 'KTP', '7,40104E+15', '2099-12-31 00:00:00', 'Active', NULL, NULL, 'Jln Tanjung Duren Utara no 212\nJakarta Barat', NULL, NULL, '+62 821-3525-2117', '+62 821-3525-2117', 'Bank Mandiri', NULL, '1,37002E+12', 'zulfikar', 'Jln Tanjung Duren Utara no 212\nJakarta Barat', NULL, NULL),
(57, '11072', '10', '3', 'Probation', '2002-02-11', 'Jakarta', 'M', '-', 'Islam', 'S', '0', 'KTP', '3172051102021001', '2099', 'Active', '2023-02-20', NULL, 'Jl. Ampera I', 'Jakarta Utara', '14420', '-', '085775889531', 'BCA', 'KCP Gunung Sahari', '2120342587', 'dio', 'Jl. Ampera I', '2023-04-27 07:03:15', '2023-04-28 07:22:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users_details`
--
ALTER TABLE `users_details`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users_details`
--
ALTER TABLE `users_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
