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
-- Table structure for table `project_assignments`
--

DROP TABLE IF EXISTS `project_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `assignment_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `req_date` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `req_by` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `reference_doc` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `notes` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_project_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `task_id` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1682366237 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_assignments`
--

LOCK TABLES `project_assignments` WRITE;
/*!40000 ALTER TABLE `project_assignments` DISABLE KEYS */;
INSERT INTO `project_assignments` VALUES (2,'RA 2234','2023-02-15','sundari','xx','xx','1','23425',NULL,NULL),(3,'aer','2023-04-05','haekals','123','123','2','34545','2023-04-05 06:28:59','2023-04-05 06:28:59'),(4,'RA 014-23','2023-04-05','haekals','123','123','2','56765','2023-04-05 06:29:42','2023-04-05 06:29:42'),(5,'123','2023-04-06','haekals','234','123123','5','47456','2023-04-06 01:43:13','2023-04-06 01:43:13'),(25671,'123','2023-04-24','haekals','123','123','11','25671','2023-04-24 16:28:44','2023-04-24 16:28:44');
/*!40000 ALTER TABLE `project_assignments` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-27  5:51:44
