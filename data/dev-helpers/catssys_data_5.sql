-- MySQL dump 10.13  Distrib 5.7.12, for Linux (x86_64)
--
-- Host: localhost    Database: catssys
-- ------------------------------------------------------
-- Server version	5.7.12-0ubuntu1

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
-- Dumping data for table `cash_flow`
--

LOCK TABLES `cash_flow` WRITE;
/*!40000 ALTER TABLE `cash_flow` DISABLE KEYS */;
INSERT INTO `cash_flow` VALUES (1,1,1,'2016-05-12',1000,'Descrição','Observação',1),(2,1,2,'2016-05-06',900,'Descrição','Observação',1),(3,1,1,'2016-05-14',700,'Descrição','Observação',2),(4,1,1,'2016-05-28',850,'Descrição','Observação',2),(5,1,2,'2016-05-28',850,'Descrição','Observação',2),(6,1,1,'2016-05-02',950,'Observação','Observação',3),(7,1,2,'2016-05-02',700,'Observação','cObservação',3),(8,1,1,'2016-05-04',600,'Observação','Observação',4),(9,1,2,'2016-05-28',900,'Observação','Observação',4),(10,1,1,'2016-05-01',789,'Observação','Observação',5),(11,1,2,'2016-02-02',456,'Observação','Observação',5),(12,1,1,'2016-05-06',598,'Observação','Observação',6),(13,1,2,'2016-05-12',486,'Observação','Observação',6),(14,1,2,'2016-05-04',610,'projectedExpenseData','projectedExpenseData',13),(15,1,1,'2016-05-12',298,'projectedExpenseData','projectedExpenseData',13),(16,1,1,'2016-05-13',698,'projectedExpenseData','projectedExpenseData',14),(17,1,2,'2016-06-02',523,'projectedExpenseData','projectedExpenseData',14),(18,2,1,'2016-05-12',333,'Descrição','Observação',17),(19,2,2,'2016-05-19',444,'Observação','Observação',17),(20,1,1,'2016-05-26',489,'Observação','Observação',18),(21,1,2,'2016-05-20',694,'Observação','Observação',18),(22,1,1,'2016-05-21',554,'monthlyBalanceOpen','monthlyBalanceOpen',26),(23,2,2,'2016-05-04',777,'monthlyBalanceOpen','monthlyBalanceOpen',26),(24,1,1,'2016-05-12',777,'monthlyBalanceOpen','monthlyBalanceOpen',27),(25,1,2,'2016-05-10',888,'monthlyBalanceOpen','monthlyBalanceOpen',27),(26,1,1,'2016-05-03',951,'monthlyBalanceOpen','monthlyBalanceOpen',28),(27,1,2,'2016-05-12',468,'monthlyBalanceOpen','monthlyBalanceOpen',28),(28,1,1,'2016-05-09',666,'\"paging\": false','\"paging\": false',29),(29,1,2,'2016-05-02',777,'Descrição','Descrição',29),(30,1,1,'2016-05-04',756,'Descrição','Descrição',30),(31,1,2,'2016-05-18',865,'Descrição','Descrição',30),(32,1,1,'2016-05-04',555,'Descrição','Descrição',31),(33,1,2,'2016-05-18',556,'Descrição','Descrição',31),(34,1,1,'2016-05-30',777,'Descrição','Descrição',32),(35,1,2,'2016-05-03',888,'Descrição','Descrição',32);
/*!40000 ALTER TABLE `cash_flow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `cash_flow_type`
--

LOCK TABLES `cash_flow_type` WRITE;
/*!40000 ALTER TABLE `cash_flow_type` DISABLE KEYS */;
INSERT INTO `cash_flow_type` VALUES 
(1,'COMIDA','COMIDA',0),
(0, 'MENSALIDADE','Valor de entrada relativo à mensalidade paga por um aluno. Quando o valor for negativo indica que a mensalidade foi alterada ou removida. Este tipo de receita é inserida automaticamente por meio da funcionalidade de manipulação de mensalidades.', 1),
(2,'DOAÇÃO','COMIDA',1);
/*!40000 ALTER TABLE `cash_flow_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `monthly_balance`
--

LOCK TABLES `monthly_balance` WRITE;
/*!40000 ALTER TABLE `monthly_balance` DISABLE KEYS */;
INSERT INTO `monthly_balance` VALUES (1,'2013-03-01','2016-05-07',900,800,900,1000,0,'Observação'),(2,'2015-09-01','2016-05-07',800,700,850,700,0,''),(3,'2016-03-01','2016-05-07',750,890,700,950,0,''),(4,'2015-11-01','2016-05-07',1000,500,900,600,0,''),(5,'2016-12-01','2016-05-07',610,987,456,789,0,''),(6,'2016-01-01','2016-05-07',687,459,486,598,0,''),(13,'2016-02-01','2016-05-07',500,360,610,298,0,''),(14,'2016-03-01','2016-05-07',600,800,523,698,0,''),(17,'2016-04-01','2016-05-07',500,400,444,333,0,''),(18,'2015-12-01','2016-05-07',666,777,694,489,0,''),(26,'2015-05-01','2016-05-07',455,655,777,554,0,''),(27,'2016-06-01','2016-05-07',999,888,888,777,0,''),(28,'2016-07-01','2016-05-07',456,654,468,951,0,''),(29,'2015-08-01','2016-05-07',777,666,777,666,0,''),(30,'2015-10-01','2016-05-07',456,852,865,756,0,''),(31,'2015-06-01','2016-05-07',555,555,556,555,0,''),(32,'2015-07-01','2016-05-07',999,999,888,777,0,''),(33,'2017-06-01',NULL,900,800,0,0,1,NULL);
/*!40000 ALTER TABLE `monthly_balance` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-05-07 18:30:20
