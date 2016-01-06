--
-- Dumping data for table `resource`
--

LOCK TABLES `resource` WRITE;
INSERT INTO `resource` VALUES (2,'Authentication\\Controller\\Login'),(5,'Authentication\\Controller\\User'),(3,'Authorization\\Controller\\Index'),(7,'Authorization\\Controller\\Privilege'),(8,'Authorization\\Controller\\Resource'),(6,'Authorization\\Controller\\Role'),(10,'DoctrineModule\\Controller\\Cli'),(12,'Documents\\Controller\\StudentBgConfig'),(16,'Recruitment\\Controller\\Captcha'),(9,'Recruitment\\Controller\\Recruitment'),(11,'Recruitment\\Controller\\Registration'),(14,'SchoolManagement\\Controller\\Enrollment'),(15,'SchoolManagement\\Controller\\SchoolWarning'),(13,'SchoolManagement\\Controller\\StudentClass'),(1,'Site\\Controller\\Index'),(4,'UMS\\Controller\\Index');
UNLOCK TABLES;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
INSERT INTO `role` VALUES (5,'admin'),(3,'guest'),(4,'member');
UNLOCK TABLES;


--
-- Dumping data for table `role_parent`
--

LOCK TABLES `role_parent` WRITE;
INSERT INTO `role_parent` VALUES (4,3);
UNLOCK TABLES;

--
-- Dumping data for table `privilege`
--

LOCK TABLES `privilege` WRITE;
INSERT INTO `privilege` VALUES (9,1,3,NULL,1),(10,2,3,NULL,1),(11,3,3,'index',1),(12,4,4,NULL,1),(18,10,3,NULL,1),(19,11,3,'studentRegistration',1),(20,16,3,'generate',1);
UNLOCK TABLES;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
INSERT INTO `user` VALUES (2,'fcadmin','$2y$10$JDJ5JDEwJEd2aHRFUnJ3N.3GAusoQDlpCMaJ9Bp.uLPAyhECLyrEe','$2y$10$GvhtERrw4T.CRjZn3HjNBuK.qahSBQahtVQc9gow151c16mkrJIje',1,'2015-12-12 11:08:24'),(3,'fcmember','$2y$10$JDJ5JDEwJDFHdTBXTDZlM.bNrNv0hRukZgy8DnqxTgHq71RoEg8hi','$2y$10$1Gu0WL6e0FNwVMXU38VtT.KlSGlFtsu6adEUOr9ZdwFqroxCgt4/O',1,'2016-01-04 19:49:56');
UNLOCK TABLES;

--
-- Dumping data for table `user_has_role`
--

LOCK TABLES `user_has_role` WRITE;
INSERT INTO `user_has_role` VALUES (4,3),(5,2);
UNLOCK TABLES;

--
-- Dumping data for table `recruitment`
--

LOCK TABLES `recruitment` WRITE;
INSERT INTO `recruitment` VALUES (1,2,2016,'2015-12-23 00:00:00','2016-06-18 00:00:00','201621.pdf',1);
UNLOCK TABLES;

--
-- Dumping data for table `registration`
--

LOCK TABLES `registration` WRITE;
INSERT INTO `registration` VALUES (1,1,NULL,1,'2015-12-24 00:36:39','2015-12-30 12:38:15','2015-12-30 12:40:21','2015-12-30 13:04:14','Alunos da UNIFEI;Amigos;Rádio, Televisão ou Jornais'),(2,1,NULL,2,'2015-12-28 16:18:51',NULL,NULL,NULL,'Alunos do CATS'),(3,1, NULL, 3,'2015-12-29 19:54:30','2015-12-30 16:49:23','2015-12-30 16:49:32','2015-12-30 16:49:38','Familiares;Alunos da UNIFEI;Amigos;Internet;Divulgação em sua escola;Voluntários do CATS');
UNLOCK TABLES;

--
-- Dumping data for table `class`
--

LOCK TABLES `class` WRITE;
INSERT INTO `class` VALUES (1,'2016-02-01','2016-12-01','Turma de 2016');
UNLOCK TABLES;


--
-- Dumping data for table `enrollment`
--

LOCK TABLES `enrollment` WRITE;
INSERT INTO `enrollment` VALUES (1,1,1,'2016-01-03 12:16:45',NULL);
UNLOCK TABLES;

--
-- Dumping data for table `warning_type`
--

LOCK TABLES `warning_type` WRITE;
INSERT INTO `warning_type` VALUES (1,'INDISCIPLINA [ORAL]','Advertência dada oralmente aos alunos que não se comportam bem durante as aulas.');
UNLOCK TABLES;


-- Dump completed on 2016-01-05  2:24:01