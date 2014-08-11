--
-- v3.1.1_2
--

INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES ('7', 'Script Concordance Testing', 'sct', 'choice', NULL);
ALTER TABLE  `webinar_users` ADD  `expert` TINYINT( 1 ) NOT NULL AFTER  `include_4R` ;

--
-- v3.1.1_3
--

CREATE TABLE IF NOT EXISTS `map_node_references` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) unsigned NOT NULL,
  `node_id` int(11) unsigned NOT NULL,
  `element_id` int(11) unsigned NOT NULL,
  `type` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `map_elements` ADD `is_private` tinyint(4) NOT NULL DEFAULT '0';
ALTER TABLE `map_questions` ADD `is_private` INT( 4 ) NOT NULL DEFAULT '0';
ALTER TABLE `map_chats` ADD `is_private` INT( 4 ) NOT NULL DEFAULT '0';
ALTER TABLE `map_dams` ADD `is_private` INT( 4 ) NOT NULL DEFAULT '0';
ALTER TABLE `map_nodes` ADD `is_private` INT( 4 ) NOT NULL DEFAULT '0' AFTER `info` ;
ALTER TABLE `map_avatars` ADD `is_private` INT( 4 ) NOT NULL DEFAULT '0';

--
-- v3.1.1_4
--

ALTER TABLE  `webinar_users` CHANGE  `user_id`  `user_id` INT( 10 ) UNSIGNED NOT NULL ;
ALTER TABLE  `webinar_users` ADD FOREIGN KEY (  `user_id` ) REFERENCES `users` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

--
-- v3.1.1_5
--

UPDATE `map_node_link_stylies` SET `name` = 'hyperlinks' WHERE `map_node_link_stylies`.`id` = 1;

--
-- v3.1.1_6
--

INSERT INTO  `user_types` (`id` , `name` , `description`) VALUES (NULL ,  'Director', NULL);

ALTER TABLE  `maps` ADD  `author_rights` INT NOT NULL ;
CREATE TABLE IF NOT EXISTS `author_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- v3.1.1_7
--

ALTER TABLE  `webinar_maps` ADD  `which` ENUM(  'labyrinth',  'section' ) NOT NULL AFTER  `webinar_id` ;
ALTER TABLE  `webinar_maps` CHANGE  `map_id`  `reference_id` INT NOT NULL ;
ALTER TABLE  `map_node_section_nodes` ADD  `node_type` ENUM(  'regular', 'in', 'out' ) NOT NULL AFTER  `order` ;
ALTER TABLE  `map_node_section_nodes` CHANGE  `node_id`  `node_id` INT( 10 ) UNSIGNED NOT NULL ;
ALTER TABLE  `map_node_section_nodes` ADD FOREIGN KEY (  `node_id` ) REFERENCES  `map_nodes` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

--
-- v3.1.1_8
--

ALTER TABLE  `map_node_section_nodes` CHANGE  `node_type`  `node_type` ENUM(  'regular',  'in',  'out',  'crucial' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

ALTER TABLE  `map_popups_counters` DROP FOREIGN KEY  `map_popups_counters_ibfk_1` ;
ALTER TABLE  `map_popups_counters` ADD CONSTRAINT  `map_popups_counters_ibfk_1` FOREIGN KEY ( `popup_id` ) REFERENCES `map_popups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `map_popups_counters` DROP FOREIGN KEY  `map_popups_counters_ibfk_2` ;

CREATE TABLE IF NOT EXISTS `webinar_node_poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(10) unsigned NOT NULL,
  `webinar_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `node_id` (`node_id`),
  KEY `webinar_id` (`webinar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `webinar_node_poll`
  ADD CONSTRAINT `webinar_node_poll_ibfk_1` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `webinar_node_poll_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `map_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `webinar_poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `on_node` int(10) unsigned NOT NULL,
  `to_node` int(10) unsigned NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `on_node` (`on_node`),
  KEY `to_node` (`to_node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `webinar_poll`
  ADD CONSTRAINT `webinar_poll_ibfk_2` FOREIGN KEY (`to_node`) REFERENCES `map_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `webinar_poll_ibfk_1` FOREIGN KEY (`on_node`) REFERENCES `map_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- v3.1.1_9
--

ALTER TABLE  `map_questions` ADD  `prompt` TEXT NOT NULL AFTER  `feedback` ;

--
-- v3.1.1_11
--

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

--
-- v3.1.1_12
--

ALTER TABLE  `webinar_maps` ADD FOREIGN KEY (  `step` ) REFERENCES  `webinar_steps` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

--
-- v3.1.1_13
--

ALTER TABLE  `map_nodes` CHANGE  `link_style_id`  `link_style_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL ;
ALTER TABLE  `map_nodes` ADD FOREIGN KEY (  `link_style_id` ) REFERENCES  `map_node_link_stylies` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

--
-- v3.1.1_14
--

ALTER TABLE  `map_counter_common_rules` ADD  `lightning` INT NOT NULL AFTER  `rule` ;

--
-- v3.1.1_15
--

CREATE TABLE IF NOT EXISTS `map_question_validation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned NOT NULL,
  `validator` text NOT NULL,
  `second_parameter` text NOT NULL,
  `error_message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `map_question_validation` ADD CONSTRAINT `map_question_validation_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `map_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- v3.1.1_16
--

INSERT INTO  `map_question_types` ( `id` , `title` , `value` , `template_name` , `template_args` ) VALUES ( '8',  'Situational Judgement Testing',  'sjt',  'draganddrop', NULL );

--
-- v3.1.1_17
--

TRUNCATE TABLE `user_bookmarks`;
ALTER TABLE  `user_bookmarks` DROP  `time_stamp` ;
ALTER TABLE  `user_bookmarks` ADD  `user_id` INT( 10 ) UNSIGNED NOT NULL ;
ALTER TABLE  `user_bookmarks` ADD FOREIGN KEY (  `session_id` ) REFERENCES  `user_sessions` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `user_bookmarks` ADD FOREIGN KEY (  `node_id` ) REFERENCES  `map_nodes` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `user_bookmarks` ADD FOREIGN KEY (  `user_id` ) REFERENCES  `users` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

--
-- v3.1.1_20
--

ALTER TABLE  `users` ADD  `modeUI` ENUM(  'easy', 'advanced'  ) NOT NULL ;
