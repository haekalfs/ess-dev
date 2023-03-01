-- MySQL dump 10.13  Distrib 8.0.27, for Win64 (x86_64)
--
-- Host: localhost    Database: ess
-- ------------------------------------------------------
-- Server version	8.0.19

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `timesheet`
--

DROP TABLE IF EXISTS `timesheet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timesheet` (
  `ts_id` int unsigned NOT NULL AUTO_INCREMENT,
  `ts_date` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_task` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_activity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_from_time` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_to_time` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_status_id` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_user_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ts_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timesheet`
--

LOCK TABLES `timesheet` WRITE;
/*!40000 ALTER TABLE `timesheet` DISABLE KEYS */;
INSERT INTO `timesheet` VALUES (1,'2023-01-26','HO','Dalam Kota','123','12:13','12:23','20','haekals','2023-02-27 18:52:42','2023-02-28 06:06:09',NULL),(2,'2023-01-05','HO','HO','22222222222222222222','12:03','22:02','20','haekals','2023-02-28 06:03:34','2023-02-28 06:06:09',NULL),(3,'2023-01-17','Project','Luar Kota','123','12:03','03:54','20','haekals','2023-02-28 06:03:44','2023-02-28 06:06:09',NULL),(4,'2023-01-24','HO','Outer Ring','sads','12:31','23:02','20','haekals','2023-02-28 06:03:54','2023-02-28 06:06:09',NULL),(5,'2023-01-18','HO','WFH','qwe','12:33','04:53','20','haekals','2023-02-28 06:04:12','2023-02-28 06:06:09',NULL),(6,'2023-02-15','HO','Dalam Kota','2323','12:03','12:13','10','haekals','2023-02-28 07:53:42','2023-02-28 07:53:42',NULL),(7,'2023-01-02','HO','Dalam Kota','wqeasd','12:32','03:04','20','haekals2','2023-03-01 08:46:42','2023-03-01 08:46:59',NULL),(8,'2023-01-11','HO','Dalam Kota','234234','03:42','23:04','20','haekals2','2023-03-01 08:46:48','2023-03-01 08:46:59',NULL);
/*!40000 ALTER TABLE `timesheet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timesheet_workflows`
--

DROP TABLE IF EXISTS `timesheet_workflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timesheet_workflows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_submitted` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_approved` varchar(105) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_status_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_timesheet` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month_periode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timesheet_workflows`
--

LOCK TABLES `timesheet_workflows` WRITE;
/*!40000 ALTER TABLE `timesheet_workflows` DISABLE KEYS */;
INSERT INTO `timesheet_workflows` VALUES (1,'haekals','2023-02-28',NULL,'20','Submitted','','haekals','20231','2023-02-27 16:20:44','2023-02-28 06:06:09'),(3,'haekals2','2023-03-01',NULL,'20','Submitted','','haekals2','20231','2023-03-01 08:46:59','2023-03-01 08:46:59');
/*!40000 ALTER TABLE `timesheet_workflows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','employee','approval') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'employee',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('haekals','Haekal Sastradilaga','haekals','haekal@perdana.co.id',NULL,'$2y$10$G1.bN5LfEbgpxk2QFwOWuet55viPYfIWL/PAxNOZYtTkZS2oNhXpO','admin',NULL,'2023-02-22 15:02:41','2023-02-22 15:02:41'),('haekals2','asd','1231','admin@perdana.co.id',NULL,'$2y$10$G1.bN5LfEbgpxk2QFwOWuet55viPYfIWL/PAxNOZYtTkZS2oNhXpO','employee',NULL,'2023-02-12 15:02:41','2023-02-21 15:02:41');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_details`
--

DROP TABLE IF EXISTS `users_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_dob` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_birth_place` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_gender` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_npwp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_religion` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_merital_status` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_children` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_id_type` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_id_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_id_expiration` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_status` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hired_date` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `resignation_date` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_address_city` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_address_postal` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_phone_home` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_phone_mobile` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_bank_name` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_bank_branch` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usr_bank_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_details`
--

LOCK TABLES `users_details` WRITE;
/*!40000 ALTER TABLE `users_details` DISABLE KEYS */;
INSERT INTO `users_details` VALUES (1,'1048','IT','active','2002-07-29','Bogor','Male','10210382753','Islam','Single','0','KTP','120391023','2099-10-29','active','2020-07-07','-','Jln. Cipinang Kalimalang No.2 Kalimalang Timur, Jakarta Timur','Jakarta Timur','12093','0812313112','09012380123','Mandiri','Bintara','120390131','haekals','Jln. Cipinang Kalimalang No.2 Kalimalang Timur, Jakarta Timur',NULL,NULL),(2,'1048','IT','active','2002-07-27','Bogor','Male','10210382753','Islam','Single','0','KTP','120391023','2099-10-29','active','2020-07-07','-','Jln. Cipinang Kalimalang No.2 Kalimalang Timur, Jakarta Timur','Jakarta Timur','12093','0812313112','09012380123','Mandiri','Bintara','120390131','haekals2','Jln. Cipinang Kalimalang No.2 Kalimalang Timur, Jakarta Timur',NULL,NULL);
/*!40000 ALTER TABLE `users_details` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-03-01 17:58:15
