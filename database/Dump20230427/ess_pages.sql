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
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (101,'Timesheet','Timesheet->Main',NULL,NULL),(102,'Timesheet','Timesheet->Entry',NULL,NULL),(103,'Timesheet','Timesheet->Summary',NULL,NULL),(104,'Timesheet','Timesheet->Review',NULL,NULL),(201,'Approval','Approval->Main',NULL,NULL),(202,'Approval','Approval->Management',NULL,NULL),(203,'Approval','Approval->Manage Approval',NULL,NULL),(301,'Leave','Leave->Main',NULL,NULL),(302,'Leave','Leave->History',NULL,NULL),(303,'Leave','Leave->Report',NULL,NULL),(304,'Leave','Leave->Manage',NULL,NULL),(305,'Leave','Leave->Convert',NULL,NULL),(401,'Projects','Projects->Main',NULL,NULL),(402,'Projects','Projects->MyProjects',NULL,NULL),(403,'Projects','Projects->Projects Assignment',NULL,NULL),(404,'Projects','Projects->Projects Organization',NULL,NULL),(405,'Projects','Projects->Projects Monitor',NULL,NULL),(501,'Reimburse','Reimburse->Main',NULL,NULL),(502,'Reimburse','Reimburse->History',NULL,NULL),(503,'Reimburse','Reimburse->Manage',NULL,NULL),(601,'Medical','Medical->Main',NULL,NULL),(602,'Medical','Medical->History',NULL,NULL),(603,'Medical','Medical->Manage',NULL,NULL),(901,'System Management','SM->Main',NULL,NULL),(902,'System Management','SM->Administrator',NULL,NULL),(999,'HR Tools','HR->Main',NULL,NULL);
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-27  5:51:46
