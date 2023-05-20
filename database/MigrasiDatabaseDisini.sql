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
-- Table structure for table `additional_fares`
--

DROP TABLE IF EXISTS `additional_fares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `additional_fares` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fare` varchar(145) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_role_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `additional_fares`
--

LOCK TABLES `additional_fares` WRITE;
/*!40000 ALTER TABLE `additional_fares` DISABLE KEYS */;
INSERT INTO `additional_fares` VALUES (1,'MT','Management Trainee','50000','4',NULL,NULL),(2,'MT','Management Trainee','75000','4',NULL,NULL),(3,'MT','Management Trainee','100000','4',NULL,NULL);
/*!40000 ALTER TABLE `additional_fares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approval_status`
--

DROP TABLE IF EXISTS `approval_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `approval_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `approval_status_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_desc` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_status`
--

LOCK TABLES `approval_status` WRITE;
/*!40000 ALTER TABLE `approval_status` DISABLE KEYS */;
INSERT INTO `approval_status` VALUES (1,'10','Saved',NULL,NULL),(2,'20','Waiting for Approval',NULL,NULL),(3,'30','Waiting for Approval',NULL,NULL),(6,'29','All Approved',NULL,NULL),(7,'404','Rejected',NULL,NULL),(8,'15','Submitted',NULL,NULL);
/*!40000 ALTER TABLE `approval_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cutoffdate`
--

DROP TABLE IF EXISTS `cutoffdate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cutoffdate` (
  `id` bigint unsigned NOT NULL,
  `date` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cutoffdate`
--

LOCK TABLES `cutoffdate` WRITE;
/*!40000 ALTER TABLE `cutoffdate` DISABLE KEYS */;
INSERT INTO `cutoffdate` VALUES (1,7,NULL,NULL);
/*!40000 ALTER TABLE `cutoffdate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave`
--

DROP TABLE IF EXISTS `leave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `leave_type` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leave_quota` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave`
--

LOCK TABLES `leave` WRITE;
/*!40000 ALTER TABLE `leave` DISABLE KEYS */;
INSERT INTO `leave` VALUES (10,'annual_leave','ANNUAL',12,NULL,NULL),(20,'five_year_term','FIVE YEAR-TERM',20,NULL,NULL),(30,'marriage','MARRIAGE',3,NULL,NULL),(40,'pilgrimage','PILGRIMAGE',0,NULL,NULL),(50,'haji','HAJI',0,NULL,NULL),(60,'child_marriage','CHILD MARRIAGE',2,NULL,NULL),(70,'maternity','MATERNITY',90,NULL,NULL),(80,'circumcision','CIRCUMCISION',2,NULL,NULL),(90,'bereavement','BEREAVEMENT',2,NULL,NULL);
/*!40000 ALTER TABLE `leave` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_request`
--

DROP TABLE IF EXISTS `leave_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_request` (
  `id` varchar(90) COLLATE utf8mb4_unicode_ci NOT NULL,
  `req_date` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `req_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leave_dates` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_days` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leave_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_request`
--

LOCK TABLES `leave_request` WRITE;
/*!40000 ALTER TABLE `leave_request` DISABLE KEYS */;
INSERT INTO `leave_request` VALUES ('15ab91f6-a9a5-43b9-9578-d9bcbbf86122','2023-05-20','haekals','05/16/2023,05/17/2023,05/18/2023,05/19/2023','4','123','10','213',NULL,'2023-05-20 04:01:20','2023-05-20 04:01:20'),('6b1bb0e4-8b0c-4448-8790-18b62e844be4','2023-05-20','haekals','05/31/2023,05/30/2023,05/29/2023','3','sda','10','asd',NULL,'2023-05-20 07:33:28','2023-05-20 07:33:28'),('d697c9b3-c652-4f36-bc66-9331aff75919','2023-05-20','haekals','05/31/2023,05/25/2023','2','123','10','123',NULL,'2023-05-20 07:58:39','2023-05-20 07:58:39');
/*!40000 ALTER TABLE `leave_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_request_approval`
--

DROP TABLE IF EXISTS `leave_request_approval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_request_approval` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RequestTo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '-',
  `leave_request_id` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_request_approval`
--

LOCK TABLES `leave_request_approval` WRITE;
/*!40000 ALTER TABLE `leave_request_approval` DISABLE KEYS */;
INSERT INTO `leave_request_approval` VALUES (10,'29','haekals','asd','15ab91f6-a9a5-43b9-9578-d9bcbbf86122','2023-05-20 04:01:20','2023-05-20 11:03:29'),(11,'20','ahabror','-','15ab91f6-a9a5-43b9-9578-d9bcbbf86122','2023-05-20 04:01:20','2023-05-20 04:01:20'),(24,'15','haekals','-','6b1bb0e4-8b0c-4448-8790-18b62e844be4','2023-05-20 07:33:28','2023-05-20 07:33:28'),(25,'29','ahabror','-','6b1bb0e4-8b0c-4448-8790-18b62e844be4','2023-05-20 07:33:28','2023-05-20 07:33:28'),(32,'15','haekals','-','d697c9b3-c652-4f36-bc66-9331aff75919','2023-05-20 07:58:39','2023-05-20 07:58:39'),(33,'15','haekals','-','d697c9b3-c652-4f36-bc66-9331aff75919','2023-05-20 07:58:39','2023-05-20 07:58:39');
/*!40000 ALTER TABLE `leave_request_approval` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_alerts`
--

DROP TABLE IF EXISTS `notification_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `importance` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_alerts`
--

LOCK TABLES `notification_alerts` WRITE;
/*!40000 ALTER TABLE `notification_alerts` DISABLE KEYS */;
INSERT INTO `notification_alerts` VALUES (2,'haekals','You have assigned to an assignment of Modulo - LPS!','1','2023-05-17 04:43:51','2023-05-17 04:43:51'),(3,'admin','You have assigned to an assignment of Modulo - LPS!','1','2023-05-17 04:43:51','2023-05-17 04:43:51'),(4,'adip','You have assigned to an assignment of AMS - Petronas!','1','2023-05-18 21:04:55','2023-05-18 21:04:55'),(5,'haekals','You have assigned to an assignment of AMS - Petronas!','1','2023-05-18 21:04:55','2023-05-18 21:04:55'),(6,'haekals','You have assigned to an assignment of Modulo - LPS!','1','2023-05-18 21:05:20','2023-05-18 21:05:20'),(7,'adhi','You have assigned to an assignment of Modulo - LPS!','1','2023-05-18 21:05:20','2023-05-18 21:05:20'),(8,'haekals','Your Leave Request has been rejected!','1','2023-05-20 11:01:34','2023-05-20 11:01:34'),(9,'haekals','Your Leave Request has been rejected!','1','2023-05-20 11:03:02','2023-05-20 11:03:02'),(10,'haekals','Your Leave Request has been rejected!','1','2023-05-20 11:03:29','2023-05-20 11:03:29'),(11,'haekals','Your Leave Request has been rejected!','1','2023-05-20 11:48:34','2023-05-20 11:48:34'),(12,'[\"haekals','You have assigned to an assignment of Implementation TAFIS Migration - Dynamik!','1','2023-05-20 16:49:40','2023-05-20 16:49:40'),(14,'haekals','Your Assignment Request is Rejected!','1','2023-05-20 17:01:11','2023-05-20 17:01:11'),(15,'haekals','Your Timesheet of May - 2023 has been Approved! by Haekal Sastradilaga','1','2023-05-20 20:35:56','2023-05-20 20:35:56'),(16,'erina','Your Timesheet of May - 2023 has been Approved! by Erina Juwita','1','2023-05-20 20:40:59','2023-05-20 20:40:59');
/*!40000 ALTER TABLE `notification_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timesheet_approver`
--

DROP TABLE IF EXISTS `timesheet_approver`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timesheet_approver` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `approver_level` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approver` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timesheet_approver`
--

LOCK TABLES `timesheet_approver` WRITE;
/*!40000 ALTER TABLE `timesheet_approver` DISABLE KEYS */;
INSERT INTO `timesheet_approver` VALUES (10,'Level HR','desy',NULL,NULL),(15,'Level FM','suryadi',NULL,NULL),(20,'Level PC','sundari',NULL,NULL),(40,'Level Dir Service','banuw',NULL,NULL),(45,'Level Dir Fin & GA','bs',NULL,NULL),(50,'Level Manager Sales & Marketing','daf',NULL,NULL),(55,'Level Dir Sales & Marketing','bs',NULL,NULL),(60,'Level Dir Tech & HCM','riyanto',NULL,NULL);
/*!40000 ALTER TABLE `timesheet_approver` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-05-21  4:30:18
