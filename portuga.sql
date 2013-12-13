CREATE DATABASE  IF NOT EXISTS `uirapuru_portuga` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `uirapuru_portuga`;
-- MySQL dump 10.13  Distrib 5.1.40, for Win32 (ia32)
--
-- Host: 127.0.0.1    Database: uirapuru_portuga
-- ------------------------------------------------------
-- Server version	5.1.54-community

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
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_translations` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notes`
--

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;
INSERT INTO `notes` VALUES (1,8,'127.0.0.1','2011-03-05 16:51:03'),(2,9,'127.0.0.1','2011-03-05 16:53:10'),(3,10,'127.0.0.1','2011-03-05 17:02:26'),(4,11,'127.0.0.1','2011-03-05 17:02:44'),(5,12,'127.0.0.1','2011-03-05 17:03:03'),(6,13,'127.0.0.1','2011-03-05 17:03:31'),(7,14,'127.0.0.1','2011-03-05 17:04:09'),(8,15,'127.0.0.1','2011-03-05 17:51:04'),(9,16,'127.0.0.1','2011-03-05 17:51:49'),(10,20,'127.0.0.1','2011-03-05 18:15:28'),(11,21,'127.0.0.1','2011-03-05 18:17:44');
/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translations`
--

DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pl` varchar(255) DEFAULT NULL,
  `pt` varchar(255) DEFAULT NULL,
  `filtered_pt` varchar(45) DEFAULT NULL,
  `filtered_pl` varchar(255) DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT NULL,
  `date_removed` timestamp NULL DEFAULT NULL,
  `autor` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `moderated` int(11) DEFAULT NULL,
  `note` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translations`
--

LOCK TABLES `translations` WRITE;
/*!40000 ALTER TABLE `translations` DISABLE KEYS */;
INSERT INTO `translations` VALUES (1,'krzesło','cadeira','cadeira','krzeslo','2011-03-02 11:00:00',NULL,'uirapuru','uirapuru@tlen.pl',NULL,1,0),(2,'duże krzesło','cadeira grande','cadeira grande','duze krzeslo','2011-03-02 11:00:00',NULL,'uirapuru','uirapuru@tlen.pl','',1,0),(3,'kwiat','floreio','kwiat','floreio','2010-03-02 11:00:00',NULL,'uirapuru','uirapuru@tlen.pl',NULL,1,0),(4,'każdy','cada','cada','kazdy','2010-03-02 11:00:00',NULL,'uirapuru','uirapuru@tlen.pl',NULL,1,0),(7,'pieprzyć','caralho','caralho','pieprzyc','2011-03-05 15:18:21',NULL,NULL,NULL,'127.0.0.1',0,1),(8,'dom','casa','casa','dom',NULL,NULL,NULL,NULL,NULL,NULL,3),(9,'samochód','carro','carro','samochod','2011-03-05 15:53:10',NULL,NULL,NULL,'127.0.0.1',0,1),(10,'dzień dobry','bom dia','bom dia','dzien dobry','2011-03-05 16:02:26',NULL,NULL,NULL,'127.0.0.1',0,1),(11,'wp.pl','wp.pl','wp.pl','wp.pl','2011-03-05 16:02:44',NULL,NULL,NULL,'127.0.0.1',0,1),(12,'dupa','burro','burro','dupa','2011-03-05 16:03:03',NULL,NULL,NULL,'127.0.0.1',0,1),(13,'kupa','pilha','pilha','kupa','2011-03-05 16:03:31',NULL,NULL,NULL,'127.0.0.1',0,1),(14,'haba','Haba','haba','haba','2011-03-05 16:04:09',NULL,NULL,NULL,'127.0.0.1',0,1),(15,'dorby Dzień','dzień dorby','dzien dorby','dorby dzien','2011-03-05 16:51:04',NULL,NULL,NULL,'127.0.0.1',0,1),(16,'portek','calças','calcas','portek','2011-03-05 16:51:49',NULL,NULL,NULL,'127.0.0.1',0,1),(17,'prostituta','kurwa','kurwa','prostituta','2011-03-05 17:11:19',NULL,NULL,NULL,'127.0.0.1',0,0),(18,'prostituta','kurwa','kurwa','prostituta','2011-03-05 17:11:36',NULL,NULL,NULL,'127.0.0.1',0,0),(19,'prostituta','kurwa','kurwa','prostituta','2011-03-05 17:13:53',NULL,NULL,NULL,'127.0.0.1',0,0),(20,'cześć','e ai!','e ai!','czesc','2011-03-05 17:14:53',NULL,NULL,NULL,'127.0.0.1',0,1),(21,'kurwa aaa','prostituta aaa','prostituta aaa','kurwa aaa','2011-03-05 17:17:15',NULL,NULL,NULL,'127.0.0.1',0,1),(22,'kurwa','prostituta','prostituta','kurwa','2011-03-05 21:23:28',NULL,NULL,NULL,'127.0.0.1',0,0),(23,'kurwagg','prostituta','prostituta','kurwagg','2011-03-05 21:23:43',NULL,NULL,NULL,'127.0.0.1',0,0),(24,'kurwa','puta','puta','kurwa','2011-03-05 21:56:38',NULL,NULL,NULL,'127.0.0.1',0,0);
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-03-05 23:10:45
