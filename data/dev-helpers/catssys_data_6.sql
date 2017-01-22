INSERT INTO `person` VALUES 
(5,NULL,'JOE','DOE',2,'MG-77.777.777','947.184.310-34','joed@email.com.br',NULL,'default-male-profile.png','(11) 11111-1111',NULL,'1990-10-10'),
(6,NULL,'JOAN','SQUIRREL',2,'MG-88.888.888','479.213.840-05','joans@email.com.br',NULL,'default-male-profile.png','(22) 22222-2222',NULL,'2000-06-20');

INSERT INTO `person_has_address` VALUES (5,26341),(6,21774);

INSERT INTO `registration` VALUES 
(1,1,NULL,5,NULL,NULL,'2016-06-12 13:58:22',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(2,1,NULL,6,NULL,NULL,'2016-06-12 14:01:25',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

INSERT INTO `registration_recruitment_know_about` VALUES 
(1,1),
(1,5),
(1,7),
(2,3),
(2,4);

INSERT INTO `registration_status` VALUES 
(1,1,1,'2016-06-12 13:58:22',0),
(2,2,1,'2016-06-12 14:01:25',0),
(3,1,13,'2016-06-12 14:03:01',1),
(4,2,12,'2016-06-12 14:03:11',1);

LOCK TABLES `resource` WRITE;
INSERT INTO `resource` VALUES 
(NULL,'SchoolManagement\\Controller\\SchoolExamResult')
;
UNLOCK TABLES;