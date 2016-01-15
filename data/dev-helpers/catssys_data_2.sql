LOCK TABLES `resource` WRITE;
INSERT INTO `resource` VALUES (2,'Authentication\\Controller\\Login'),(5,'Authentication\\Controller\\User'),(3,'Authorization\\Controller\\Index'),(7,'Authorization\\Controller\\Privilege'),(8,'Authorization\\Controller\\Resource'),(6,'Authorization\\Controller\\Role'),(10,'DoctrineModule\\Controller\\Cli'),(12,'Documents\\Controller\\StudentBgConfig'),(16,'Recruitment\\Controller\\Captcha'),(18,'Recruitment\\Controller\\Interview'),(17,'Recruitment\\Controller\\PreInterview'),(9,'Recruitment\\Controller\\Recruitment'),(11,'Recruitment\\Controller\\Registration'),(14,'SchoolManagement\\Controller\\Enrollment'),(15,'SchoolManagement\\Controller\\SchoolWarning'),(13,'SchoolManagement\\Controller\\StudentClass'),(1,'Site\\Controller\\Index'),(4,'UMS\\Controller\\Index');
UNLOCK TABLES;

LOCK TABLES `role` WRITE;
INSERT INTO `role` VALUES (5,'admin'),(3,'guest'),(4,'member');
UNLOCK TABLES;

LOCK TABLES `role_parent` WRITE;
INSERT INTO `role_parent` VALUES (4,3);
UNLOCK TABLES;

LOCK TABLES `user` WRITE;
INSERT INTO `user` VALUES (2,'fcadmin','$2y$10$JDJ5JDEwJEd2aHRFUnJ3N.3GAusoQDlpCMaJ9Bp.uLPAyhECLyrEe','$2y$10$GvhtERrw4T.CRjZn3HjNBuK.qahSBQahtVQc9gow151c16mkrJIje',1,'2015-12-12 11:08:24'),(3,'fcmember','$2y$10$JDJ5JDEwJDFHdTBXTDZlM.bNrNv0hRukZgy8DnqxTgHq71RoEg8hi','$2y$10$1Gu0WL6e0FNwVMXU38VtT.KlSGlFtsu6adEUOr9ZdwFqroxCgt4/O',1,'2016-01-04 19:49:56');
UNLOCK TABLES;

LOCK TABLES `user_has_role` WRITE;
INSERT INTO `user_has_role` VALUES (4,3),(5,2);
UNLOCK TABLES;

LOCK TABLES `privilege` WRITE;
INSERT INTO `privilege` VALUES (9,1,3,NULL,1),(10,2,3,NULL,1),(11,3,3,'index',1),(12,4,4,NULL,1),(18,10,3,NULL,1),(19,11,3,'studentRegistration',1),(20,16,3,'generate',1);
UNLOCK TABLES;

LOCK TABLES `recruitment` WRITE;
INSERT INTO `recruitment` VALUES (1,2,2016,'2015-12-23 00:00:00','2016-06-18 00:00:00','201621.pdf',1);
UNLOCK TABLES;

LOCK TABLES `recruitment_know_about` WRITE;
INSERT INTO `recruitment_know_about` VALUES (2,'Alunos da UNIFEI'),(3,'Alunos do CATS'),(4,'Amigos'),(7,'Divulgação em sua escola'),(1,'Familiares'),(5,'Internet'),(6,'Rádio, Televisão ou Jornais'),(8,'Voluntários do CATS');
UNLOCK TABLES;

LOCK TABLES `recruitment_live_with_you` WRITE;
INSERT INTO `recruitment_live_with_you` VALUES (1, 'Moro sozinho.'), (2, 'Filhos.'), (3, 'Moro com pai e/ou mãe.'), (4, 'Irmãos.'), (5, 'Esposa, marido, companheiro(a).'), (6, 'Outro.');
UNLOCK TABLES;

LOCK TABLES `registration` WRITE;
INSERT INTO `registration` VALUES (1,1,NULL,1,'2016-01-12 19:14:09','2016-01-12 19:42:35','2016-01-12 19:42:38','2016-01-12 20:24:01'),(2,1,NULL,4,'2016-01-12 19:31:44',NULL,NULL,NULL);
UNLOCK TABLES;

LOCK TABLES `registration_recruitment_know_about` WRITE;
INSERT INTO `registration_recruitment_know_about` VALUES (1,2),(1,4),(1,5),(1,8),(2,3),(2,5);
UNLOCK TABLES;

LOCK TABLES `class` WRITE;
INSERT INTO `class` VALUES (1,'2016-02-01','2016-12-01','Turma de 2016');
UNLOCK TABLES;

LOCK TABLES `warning_type` WRITE;
/*!40000 ALTER TABLE `warning_type` DISABLE KEYS */;
INSERT INTO `warning_type` VALUES (1,'INDISCIPLINA [ORAL]','Advertência dada oralmente aos alunos que não se comportam bem durante as aulas.');
/*!40000 ALTER TABLE `warning_type` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `enrollment` WRITE;
INSERT INTO `enrollment` VALUES (1,1,1,'2016-01-12 20:27:36',NULL);
UNLOCK TABLES;