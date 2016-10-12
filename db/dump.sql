-- MySQL dump 10.13  Distrib 5.7.9, for linux-glibc2.5 (x86_64)
--
-- Host: localhost    Database: intel
-- ------------------------------------------------------
-- Server version	5.5.50-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `chat_rooms`
--

DROP TABLE IF EXISTS `chat_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_rooms` (
  `chat_room_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chat_room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_rooms`
--

LOCK TABLES `chat_rooms` WRITE;
/*!40000 ALTER TABLE `chat_rooms` DISABLE KEYS */;
INSERT INTO `chat_rooms` VALUES (1,'Material Design','2016-01-06 06:57:40'),(2,'Android Snackbar','2016-01-06 06:57:40'),(3,'Google Cloud Messaging','2016-01-06 06:57:40'),(4,'Android Marshmallow','2016-01-06 06:57:40'),(5,'Wallpapers App','2016-01-06 06:57:40'),(6,'Android Support Design Library','2016-01-06 06:58:46'),(7,'Android Studio','2016-01-06 06:58:46'),(8,'Realtime Chat App','2016-01-06 06:58:46');
/*!40000 ALTER TABLE `chat_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `chat_room_id` (`chat_room_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`chat_room_id`) REFERENCES `chat_rooms` (`chat_room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,1,34,'oi','2016-07-12 18:52:34'),(2,1,34,'oiii','2016-07-12 18:52:42'),(3,1,34,'e ai','2016-07-12 18:52:50'),(4,1,34,'oi','2016-07-12 19:00:40'),(5,1,33,'fd','2016-08-02 00:00:25');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paciente_familiar`
--

DROP TABLE IF EXISTS `paciente_familiar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paciente_familiar` (
  `idPaciente` int(11) NOT NULL,
  `idFamiliar` int(11) NOT NULL,
  PRIMARY KEY (`idPaciente`,`idFamiliar`),
  KEY `fk_fam` (`idFamiliar`),
  CONSTRAINT `fk_fam` FOREIGN KEY (`idFamiliar`) REFERENCES `users` (`user_id`),
  CONSTRAINT `fk_pac` FOREIGN KEY (`idPaciente`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paciente_familiar`
--

LOCK TABLES `paciente_familiar` WRITE;
/*!40000 ALTER TABLE `paciente_familiar` DISABLE KEYS */;
INSERT INTO `paciente_familiar` VALUES (37,32),(32,33),(37,33),(32,34),(37,34),(34,35),(37,36),(32,37),(37,37),(37,39),(37,41);
/*!40000 ALTER TABLE `paciente_familiar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `gcm_registration_id` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (32,'erick','er@gmail.com','','2016-07-08 20:04:52'),(33,'AndroidHive','admin@androidhive.info','','2016-07-08 20:05:55'),(34,'Larissa ','udp@gmail.com','fd6mAfIKmws:APA91bHe_u2M2BxXHrC_ugaJGjl14E4N9QYRPnbeTfH0ytxhsi6NNItfslC7sjp09rrUxAolcrMM8F_cMMIjgmcsFWjfvSYxnWhHjNC-gSY_KeS2vsAhdRPxqDemQQ87N44kNx_xdgm9','2016-07-12 18:50:01'),(35,'lab','lab@gmail.com','f_Xu8iBQXJk:APA91bEsGoaDhMJAGG_U9NSatV-IZ_ht9be7ZkrloCNw2WeJ9yeSuWk2PrYohsGuoRGikFJpKWD8k6HOGACfltbZA7GzS6WUSA21zuHuA4ad7R79CcBV91kB78ZMAQq0zMp0Y8thLErj','2016-08-01 21:39:14'),(36,'teste','teste@gmail.com','f_VwOez0Rjo:APA91bGf_KSY2tvKesCPr0lmBaGLxnX82uOZf4IkXmnT4fz-rSGtXfsiF4TBkNrnunmgQ1-7B3lMJSbO9o0WI5zAqPowj1Y_7JWIlIYNzYJonOEHOXpjQ6KeUzEKVTOvzQnuYzhCldr1','2016-08-02 00:00:08'),(37,'tablet','tablet@gmail.com','drVjN-eOgT8:APA91bEQn0EXfc45uUW-s9X-CvkNKVjHSQx9p0NfInIPl7JLav0dgZDg865HDcdrIA7T1Nh5tSicsZYKmpBY9K5vsv6JtBgGXmkggwYVw3skYcjhLGsu8RyLCRoZBvy067mCGkLpiV7g','2016-08-02 00:17:30'),(38,'Larissa Gisele','larissagisele@gmail.com','eKLKC2d2tVU:APA91bGCDUJIYgHBkVAdpSxTA1Sp8eAvwZiYyp-NhfHLLAwrOyE-TylO9GGXsKA2VatyQeZ4keoN8DZRdZuXfavqmV9x8boybhGSoO_eE7M505-pGXupnnNYasSYxMPxX1qwoqSVxIaD','2016-08-02 00:20:19'),(39,'labse','labse@gmail.com','etYv6MPdaIw:APA91bG1rPduxvGgt8ISQh076gOQnoUZgFdh63GZCOTu-93df4-mmdsjhJgg5um-8L6xcgEj-PC_j-fr34QpanBbizaPVaBgv8Y_mNv31yAJRLnoS5XIoAeJpwtKx84HHPUOZiW6O6J9','2016-08-10 20:46:27'),(40,'labse2','labse2@gmail.com','','2016-08-10 21:48:20'),(41,'betinho','betinho@gmail.com','fAtPYb852Mw:APA91bG_V5LOKym-5Bw1qbmQOaYwAAcy5cN1m0j-mvH1lmjTpUJI9AFdFmjTvrO33GSIBRCiXFm18eqkLCSJqfFqG0j8vH2TIC5v1nkzjtAMs6iwUwK97uRQKLmhSMvACuN4c5j1_a7N','2016-08-10 21:48:53');
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

-- Dump completed on 2016-10-12 14:39:47
