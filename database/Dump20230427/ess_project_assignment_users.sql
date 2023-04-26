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
-- Table structure for table `project_assignment_users`
--

DROP TABLE IF EXISTS `project_assignment_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_assignment_users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `responsibility` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `periode_start` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `periode_end` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project_assignment_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_project_id` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_assignment_users`
--

LOCK TABLES `project_assignment_users` WRITE;
/*!40000 ALTER TABLE `project_assignment_users` DISABLE KEYS */;
INSERT INTO `project_assignment_users` VALUES (3,'adhi','MT','123','3234-12-12','0003-03-12','7','99','2023-04-08 10:05:07','2023-04-08 10:05:07'),(4,'haekals','PM','BASIS','2023-04-25','2023-04-29','5','5','2023-04-08 10:15:31','2023-04-08 10:15:31'),(7,'haekals','TL','BASIS','2023-04-06','2023-04-29','551','11','2023-04-24 12:53:21','2023-04-24 12:53:21'),(8,'haekals','PM','PM','2023-04-06','2023-04-28','2','1','2023-04-24 16:28:26','2023-04-24 16:28:26'),(9,'haekals','CO','BASIS','2023-04-12','2023-04-29','25671','11','2023-04-24 16:29:33','2023-04-24 16:29:33');
/*!40000 ALTER TABLE `project_assignment_users` ENABLE KEYS */;
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
