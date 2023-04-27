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
-- Table structure for table `timesheet_details`
--

DROP TABLE IF EXISTS `timesheet_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timesheet_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ts_task` varchar(145) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_task_id` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_location` varchar(145) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_mandays` varchar(145) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_submitted` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_status_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RequestTo` varchar(145) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_timesheet` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month_periode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timesheet_workflow_id` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timesheet_details`
--

LOCK TABLES `timesheet_details` WRITE;
/*!40000 ALTER TABLE `timesheet_details` DISABLE KEYS */;
INSERT INTO `timesheet_details` VALUES (1,'haekals',NULL,NULL,NULL,NULL,'2023-04-25','10','Saved','',NULL,'haekals','20231',NULL,'2023-04-24 23:30:00','2023-04-24 23:30:00'),(20,'haekals','Jasa Support','25671','HO','1','2023-04-25','40','Submitted','','pa','haekals','20231',NULL,'2023-04-24 23:39:03','2023-04-24 23:58:12'),(21,'haekals','Implementation TAFIS Migration - Dynamik','2','DK','1','2023-04-25','30','Submitted','','haekals','haekals','20231',NULL,'2023-04-24 23:39:03','2023-04-24 23:41:21'),(22,'haekals','AMS - Petronas','5','DK','2','2023-04-25','30','Submitted','','haekals','haekals','20231',NULL,'2023-04-24 23:39:03','2023-04-24 23:41:21'),(23,'haekals','HO','HO','DK','2','2023-04-25','20','Submitted','','hr','haekals','20231',NULL,'2023-04-24 23:39:03','2023-04-24 23:39:03'),(24,'haekals','Implementation TAFIS Migration - Dynamik','2','DK','1','2023-04-25','40','Approved','','pa','haekals','20231',NULL,'2023-04-24 23:41:21','2023-04-24 23:58:12'),(25,'haekals','AMS - Petronas','5','DK','2','2023-04-25','40','Approved','','pa','haekals','20231',NULL,'2023-04-24 23:41:21','2023-04-24 23:58:12'),(27,'haekals','Implementation TAFIS Migration - Dynamik','2','DK','1','2023-04-25','40','Approved','','service_dir','haekals','20231',NULL,'2023-04-24 23:56:49','2023-04-24 23:56:49'),(28,'haekals','AMS - Petronas','5','DK','2','2023-04-25','40','Approved','','service_dir','haekals','20231',NULL,'2023-04-24 23:56:49','2023-04-24 23:56:49'),(29,'haekals','Jasa Support','25671','HO','1','2023-04-25','40','Approved','','service_dir','haekals','20231',NULL,'2023-04-24 23:58:12','2023-04-24 23:58:12'),(30,'haekals',NULL,NULL,NULL,NULL,'2023-04-27','10','Saved','',NULL,'haekals','20233',NULL,'2023-04-26 21:03:11','2023-04-26 21:03:11'),(31,'haekals',NULL,NULL,NULL,NULL,'2023-04-27','10','Saved','',NULL,'haekals','20234',NULL,'2023-04-26 22:47:22','2023-04-26 22:47:22');
/*!40000 ALTER TABLE `timesheet_details` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-27  5:51:43
