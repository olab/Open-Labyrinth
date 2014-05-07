CREATE TABLE IF NOT EXISTS `patient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `type` enum('Longitudinal same set','Longitudinal different set','Parallel same set','Parallel different set') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `patient_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `patient_condition_change` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_node` int(10) unsigned NOT NULL,
  `id_condition` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `appear` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_node` (`id_node`,`id_condition`),
  KEY `id_condition` (`id_condition`),
  KEY `id_patient` (`id_patient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `patient_condition_change`
  ADD CONSTRAINT `patient_condition_change_ibfk_1` FOREIGN KEY (`id_condition`) REFERENCES `patient_condition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_condition_change_ibfk_2` FOREIGN KEY (`id_node`) REFERENCES `map_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_condition_change_ibfk_3` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `patient_condition_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_patient` int(11) NOT NULL,
  `id_condition` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_patient` (`id_patient`),
  KEY `id_condition` (`id_condition`),
  KEY `id_condition_2` (`id_condition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `patient_condition_relation`
  ADD CONSTRAINT `patient_condition_relation_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_condition_relation_ibfk_2` FOREIGN KEY (`id_condition`) REFERENCES `patient_condition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `patient_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule` text NOT NULL,
  `isCorrect` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `patient_scenario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_patient` int(11) NOT NULL,
  `id_scenario` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_patient` (`id_patient`),
  KEY `id_scenario` (`id_scenario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `patient_scenario`
  ADD CONSTRAINT `patient_scenario_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_scenario_ibfk_2` FOREIGN KEY (`id_scenario`) REFERENCES `webinars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `patient_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_patient` int(11) NOT NULL,
  `path` text NOT NULL,
  `patient_condition` text NOT NULL,
  `deactivateNode` text NOT NULL,
  `whose_id` int(11) DEFAULT NULL,
  `whose` enum('user','group') DEFAULT NULL,
  `scenario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_patient` (`id_patient`),
  KEY `webinar_id` (`scenario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `patient_sessions`
  ADD CONSTRAINT `patient_sessions_ibfk_1` FOREIGN KEY (`id_patient`) REFERENCES `patient` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_sessions_ibfk_2` FOREIGN KEY (`scenario_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;