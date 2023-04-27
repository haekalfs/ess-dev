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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','employee','director','hr','fm','approval') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'employee',
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
INSERT INTO `users` VALUES ('adhi','Adhi Dharmawan Riady','adhi@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('adip','Adi Prabowo','adip@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('admin','Administrator','ess@perdana-consulting.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('ahabror','Abdullah Hafizul Abror','abror@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('akbar','Ahmad Sulton Akbar','akbar@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('akmal','Akmal Abdi','akmal@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('aldi','Aldi Ramadhani','aldi@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('amiens','Dwi Amien Subhan','amiens@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('ansyar','Ansyar Prawiraputera','ansyar@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('ardonop','R. Ardono Poerwanto','ardono@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('arfan','Arfan Al Mukaddas','arfan@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('arizki','Arizki Apriana','arizki@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('atikr','Atik Rachmawati','atik@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('ayu','Dhawuh Rahayu','ayu@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('banuw','Banu Wimbadi','banu@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('bs','Bambang Saptowinarno','bs@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','admin',NULL,NULL,NULL),('daf','Dicky Afrizal','daf@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('daniel','Daniel','daniel@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('desy','Desy Andirawati Basri','desy@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('dinda','Dinda Rizka Tami Maunisah','dinda@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('dira','Dira Arisman','dira@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('djayanti','Dian Jayanti','dian@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('erina','Erina Juwita','erina@perdana.co.id',NULL,'$2y$10$G1.bN5LfEbgpxk2QFwOWuet55viPYfIWL/PAxNOZYtTkZS2oNhXpO','employee',NULL,NULL,NULL),('faiq','Muhammad Faiq Jauhar','faiq@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('firillah','Muhammad Mustaghfirillah','firillah@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('haekals','Haekal Sastradilaga','haekal@perdana.co.id',NULL,'$2y$10$G1.bN5LfEbgpxk2QFwOWuet55viPYfIWL/PAxNOZYtTkZS2oNhXpO','employee','aoQSlA7SIFhQoNo7JibgHhM6MR30IDWEIZ7geWII5Blij9QRXW33ADV2aSOF','2023-02-22 15:02:41','2023-02-22 15:02:41'),('haekals2','asd','admin@perdana.co.id',NULL,'$2y$10$G1.bN5LfEbgpxk2QFwOWuet55viPYfIWL/PAxNOZYtTkZS2oNhXpO','employee',NULL,'2023-02-12 15:02:41','2023-02-21 15:02:41'),('hageng','Hageng Razansah Hasibuan','hageng@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('hartini','Hartini Rahmawati','ade@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('ilhamap','Ilham Aji Pratomo','ilhamap@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('irna','Irna Mela Puspita','ela@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('irsyad','Irsyad Haryanto','irsyad@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('islam akbar','Muhammad Islam Akbar','islam akbar@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('isnank','Isnan Kashaeli','isnan@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('julyansyah','Julyansyah','julyansyah@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('murdim','Murdi Mantama','murdi@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('najla','Najla Ulfah Salsabila','najla@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('nova','Nova Dwi Arianto','nova@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('nursaid','Nursaid','nursaid@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('poppy','Poppy Chairina Almaida','poppy@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('poppyw','Poppy Widya Sari','poppyw@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('radityaa','Raditya Ardiyanto','radityaa@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('rafi','Rafi Hafizh Siregar','rafi@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('rherawan','Rizki Harris Erawan','rherawan@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('riyantog','Riyanto Gozali','riyanto@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('ronnyp','Ronny Wijaya Prasetya','ronny@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('sarraa','Sarra Annisa','sarraa@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('singgih','Singgih Suciawan','singgih@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('sundari','Sundari','sundari@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('suryadi','Suryadi','suryadi@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('taufiki','Taufik Ismail','taufiki@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('yuliyanti','Yuliyanti','yuli@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL),('zulfikar','Muhammad Zulfikar Ismail','zulfikar@perdana.co.id',NULL,'$2y$10$UzdXxI4e6vEUBeXXtbxRvu3/G9uYtqRCvh7ANiMP7Y1h44HC7wgES','employee',NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
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
