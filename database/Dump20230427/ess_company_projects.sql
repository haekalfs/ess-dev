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
-- Table structure for table `company_projects`
--

DROP TABLE IF EXISTS `company_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_code` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alias` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `periode_start` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `periode_end` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_projects`
--

LOCK TABLES `company_projects` WRITE;
/*!40000 ALTER TABLE `company_projects` DISABLE KEYS */;
INSERT INTO `company_projects` VALUES (1,'P.DTS.23.01','DK','Implementation TAFIS Migration - Dynamik','DKI Jakarta','01-Feb-2023','31-Jul-2024','2',NULL,NULL,NULL),(2,'-','DK','Modulo - LPS','DKI Jakarta','16-Aug-2022','	31-Mar-2023','3',NULL,NULL,NULL),(3,'P.JOB.22.01','DK','JOB Tomori Pertamina-Medco E&P 2022-2025','DKI Jakarta','19-Sep-2022','18-Sep-2025','1',NULL,NULL,NULL),(4,'P.PLB.22.02','DK','CR Aplikasi SAP Modul HR u Konsolidasi HR PLNBatam','DKI Jakarta','05-Dec-2021','28-Feb-2023','4',NULL,NULL,NULL),(5,'P.PCK.21.01','DK','AMS - Petronas','DKI Jakarta','01-Jul-2020','01-Jul-2024','5',NULL,NULL,NULL),(6,'P.EXM.22.01.03','DK','Re-Engineering Apl Intranet-LPEI','DKI Jakarta','14-Oct-2022','10-Mar-2023','6',NULL,NULL,NULL),(7,'-','DK','Digitalization & Automation - XL','DKI Jakarta','01-Nov-2022','30-Apr-2023','7',NULL,NULL,NULL),(8,'-','DK','ETL and Power BI -XL','DKI Jakarta','01-Aug-2022','28-Feb-2023','7',NULL,NULL,NULL),(9,'P.MRZ.22.01','DK','TMS Maintenance Support 2021-2023-Mercedez Benz','DKI Jakarta','01-May-2021','30-Apr-2023','8',NULL,NULL,NULL),(10,'1','1','1','2','2023-04-23','2023-04-28','1',NULL,'2023-04-22 17:43:03','2023-04-22 17:43:03'),(11,'23','LN','Jasa Support','serads','2023-04-23','2023-04-28','4',NULL,'2023-04-22 17:47:39','2023-04-22 17:47:39'),(12,'123','DK','123','123','2023-04-02','2023-04-05','8',NULL,'2023-04-24 13:09:29','2023-04-24 13:09:29');
/*!40000 ALTER TABLE `company_projects` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-27  5:51:47
