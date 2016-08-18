LOCK TABLES `resource` WRITE;
INSERT INTO `resource` VALUES 
(1,'Authentication\\Controller\\Login'),
(2,'Authentication\\Controller\\User'),
(3,'Authorization\\Controller\\Index'),
(4,'Authorization\\Controller\\Privilege'),
(5,'Authorization\\Controller\\Resource'),
(6,'Authorization\\Controller\\Role'),
(7,'Documents\\Controller\\StudentBgConfig'),
(8,'Documents\\Controller\\GeneratePdf'),
(9,'Recruitment\\Controller\\Captcha'),
(10,'Recruitment\\Controller\\Interview'),
(11,'Recruitment\\Controller\\PreInterview'),
(12,'Recruitment\\Controller\\Recruitment'),
(13,'Recruitment\\Controller\\Registration'),
(14,'Recruitment\\Controller\\CsvViewer'),
(15,'SchoolManagement\\Controller\\Enrollment'),
(16,'SchoolManagement\\Controller\\SchoolWarning'),
(17,'SchoolManagement\\Controller\\StudentClass'),
(18,'Site\\Controller\\Index'),
(19,'UMS\\Controller\\Index'),
(20,'Recruitment\\Controller\\Address'),
(21,'SchoolManagement\\Controller\\SchoolAttendance'),
(22,'AdministrativeStructure\\Controller\\Department'),
(23,'SchoolManagement\\Controller\\StudyResources'),
(24,'Version\\Controller\\VersionInfo'),
(25,'SchoolManagement\\Controller\\SchoolSubject'),
(26,'SchoolManagement\\Controller\\SchoolExam'),
(27,'AdministrativeStructure\\Controller\\Job'),
(NULL,'SchoolManagement\\Controller\\SchoolExamPreview'),
(NULL,'Documents\\Controller\\StudentAnswersSheets'),
(NULL,'FinancialManagement\\Controller\\CashFlow'),
(NULL,'FinancialManagement\\Controller\\MonthlyPayment')
;
UNLOCK TABLES;

LOCK TABLES `role` WRITE;
INSERT INTO `role` VALUES (5,'admin'),(3,'guest'),(4,'member');
UNLOCK TABLES;

LOCK TABLES `role_parent` WRITE;
INSERT INTO `role_parent` VALUES (4,3);
UNLOCK TABLES;

LOCK TABLES `user` WRITE;
INSERT INTO `user` VALUES 
(2,'fcadmin','$2y$10$JDJ5JDEwJEd2aHRFUnJ3N.3GAusoQDlpCMaJ9Bp.uLPAyhECLyrEe','$2y$10$GvhtERrw4T.CRjZn3HjNBuK.qahSBQahtVQc9gow151c16mkrJIje',1,'2015-12-12 11:08:24'),
(3,'fcmember','$2y$10$JDJ5JDEwJDFHdTBXTDZlM.bNrNv0hRukZgy8DnqxTgHq71RoEg8hi','$2y$10$1Gu0WL6e0FNwVMXU38VtT.KlSGlFtsu6adEUOr9ZdwFqroxCgt4/O',1,'2016-01-04 19:49:56');
UNLOCK TABLES;

LOCK TABLES `user_has_role` WRITE;
INSERT INTO `user_has_role` VALUES (4,3),(5,2);
UNLOCK TABLES;

LOCK TABLES `privilege` WRITE;
INSERT INTO `privilege` VALUES 
(9,1,3,NULL,1),
(10,2,3,NULL,1),
(11,3,3,'index',1),
(12,4,4,NULL,1),
(18,10,3,NULL,1),
(19,11,3,'registrationForm',1),
(20,16,3,'generate',1);
UNLOCK TABLES;

LOCK TABLES `recruitment` WRITE;
INSERT INTO `recruitment` VALUES (1,2,2016,'2015-12-23 00:00:00','2016-12-18 00:00:00','201621.pdf',1, 3, 2, 3);
INSERT INTO `recruitment` VALUES (2,1,2016,'2016-01-23 00:00:00','2016-12-18 00:00:00','201612.pdf',2, null, null, null);
UNLOCK TABLES;

LOCK TABLES `recruitment_know_about` WRITE;
INSERT INTO `recruitment_know_about` VALUES 
(2,'Alunos da UNIFEI'),
(3,'Alunos do CATS'),
(4,'Amigos'),
(7,'Divulgação em sua escola'),
(1,'Familiares'),
(5,'Internet'),
(6,'Rádio, Televisão ou Jornais'),
(8,'Voluntários do CATS');
UNLOCK TABLES;

LOCK TABLES `class` WRITE;
INSERT INTO `class` VALUES (1,'2016-02-01','2016-12-01','Turma de 2016');
UNLOCK TABLES;

LOCK TABLES `warning_type` WRITE;
/*!40000 ALTER TABLE `warning_type` DISABLE KEYS */;
INSERT INTO `warning_type` VALUES (1,'INDISCIPLINA [ORAL]','Advertência dada oralmente aos alunos que não se comportam bem durante as aulas.');
/*!40000 ALTER TABLE `warning_type` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `recruitment_status` WRITE;
INSERT INTO `recruitment_status` VALUES 
(1, 0),
(2, 1),
(3, 2),
(4, 3),
(5, 4),
(6, 5),
(7, 6),
(8, 7),
(9, 8),
(10, 9),
(11, 10),
(12, 11),
(13, 12),
(14, 13);
UNLOCK TABLES;

LOCK TABLES `infrastructure_element` WRITE;
INSERT INTO `infrastructure_element` VALUES
(1, 'Rede de esgoto'),
(2, 'Água tratada'),
(3, 'Iluminação pública'),
(4, 'Lixo recolhido'),
(5, 'Rua pavimentadas'),
(6, 'Internet');
UNLOCK TABLES;

LOCK TABLES `attendance_type` WRITE;
INSERT INTO `attendance_type` VALUES
(1, 'FREQUÊNCIA INÍCIO DO DIA'),
(2, 'FREQUÊNCIA FINAL DO DIA'),
(3, 'ABONO INÍCIO DO DIA'),
(4, 'ABONO FINAL DO DIA'),
(5, 'ABONO INTEGRAL');
UNLOCK TABLES;

LOCK TABLES `subject` WRITE;
/*!40000 ALTER TABLE `subject` DISABLE KEYS */;
INSERT INTO `subject` VALUES 
(42,NULL,'MATEMÁTICA E SUAS TECNOLOGIAS','MATEMÁTICA E SUAS TECNOLOGIAS'),
(43,NULL,'CIÊNCIAS HUMANAS E SUAS TECNOLOGIAS','CIÊNCIAS HUMANAS E SUAS TECNOLOGIAS'),
(44,NULL,'LINGUAGENS, CÓDIGOS E SUAS TECNOLOGIAS','LINGUAGENS, CÓDIGOS E SUAS TECNOLOGIAS'),
(45,NULL,'CIÊNCIAS DA NATUREZA E SUAS TECNOLOGIAS','CIÊNCIAS DA NATUREZA E SUAS TECNOLOGIAS'),
(40,NULL,'REDAÇÃO','REDAÇÃO'),(41,40,'TEMAS PARA REDAÇÃO','TEMAS PARA REDAÇÃO'),
(46,42,'ÁLGEBRA','Álgebra'),(47,42,'GEOMETRIA','Geometria'),(48,43,'GEOGRAFIA','Geografia'),
(49,43,'HISTÓRIA','História'),(50,44,'ESPANHOL','Espanhol'),(51,44,'GRAMÁTICA','Gramática'),
(52,44,'INGLÊS','Inglês'),(53,44,'LITERATURA','Literatura'),(54,45,'BIOLOGIA ANIMAL','Biologia Animal'),
(55,45,'BIOLOGIA VEGETAL','Biologia Vegetal'),(56,45,'FÍSICA - ELÉTRICA','Física - Elétrica'),
(57,45,'FÍSICA - MECÂNICA','Física - Mecânica'),(58,45,'FÍSICA - ÓTICA','Física - Ótica'),
(59,45,' QUÍMICA INORGÂNICA','Química Inorgânica'),
(60,45,'QUÍMICA ORGÂNICA','Química Orgânica'),
(61,46,'POLINÔMIOS','Polinômios');
/*!40000 ALTER TABLE `subject` ENABLE KEYS */;
UNLOCK TABLES;

