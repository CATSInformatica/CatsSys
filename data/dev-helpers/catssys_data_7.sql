-- Cargos para voluntários
insert into job values (1, 1, 4, null, 'DIRETOR DE RH', 'Diretor de RH...', 1, '2018-03-04 09:43:09', null);
insert into job values (2, 1, 3, 1, 'ENTREVISTADOR', 'Faz entrevistas de processo seletivo de alunos e voluntários', 1, '2018-03-04 09:43:38', null);

-- Processo seletivo de voluntários
INSERT INTO `recruitment` VALUES (7,1,2018,'2018-01-05 00:00:00','2018-02-03 23:59:59','201812.pdf',2,4,5,6,'aaaaaaa',NULL,NULL,'',NULL,'',NULL,'',NULL,'',NULL,NULL,'bbbbbbb','2018-02-10 23:59:59','dddddddd',NULL,NULL,'','cccccccc');

-- cargos requisitados no processo seletivo de voluntários
INSERT INTO `recruitment_open_jobs` VALUES (7,1),(7,2);

-- candidatos
INSERT INTO `person` VALUES
(7,NULL,'AUGUSTO','COMTE',2,'MG-11.111.111','682.364.333-15','a@exemplo.com.br','asdasdasdasd','default-male-profile.png','(12) 31231-2312',NULL,'1991-12-06'),
(8,NULL,'PEDRO','DE LARA',2,'MG-11.111.111','765.692.898-70','b@exemplo.com.br','asdasdasdasd','default-male-profile.png','(12) 31231-2312',NULL,'1991-12-06');

insert into person_has_address VALUES
(7, 26341),
(8, 21774);

INSERT INTO `registration` VALUES
(3,7,NULL,7,NULL,NULL,NULL,'2018-03-04 11:47:38','aaaa','bbbbbbb',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(4,7,NULL,8,NULL,NULL,NULL,'2018-03-04 11:47:38','aaaa','bbbbbbb',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

INSERT INTO `registration_desired_jobs` VALUES (3,1),(3,2),(4,2);

insert into `registration_status` values
(null, 3, 1, '2018-03-04 11:47:38', 1),
(null, 4, 1, '2018-03-04 11:47:38', 1);