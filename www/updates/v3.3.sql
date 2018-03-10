ALTER TABLE `map_popups_styles` ADD COLUMN `id` INT( 11 ) NOT NULL AUTO_INCREMENT FIRST, DROP PRIMARY KEY , ADD PRIMARY KEY ( `id` );

ALTER TABLE  `webinar_maps` ADD  `cumulative` BOOLEAN NOT NULL ;
INSERT INTO  `map_question_types` ( `id` , `title` , `value` , `template_name` , `template_args` ) VALUES ( NULL ,  'Cumulative',  'area',  'area', NULL );
ALTER TABLE  `user_sessions` ADD  `notCumulative` BOOLEAN NOT NULL ;

ALTER TABLE  `map_presentation_maps` DROP FOREIGN KEY  `map_presentation_maps_ibfk_1` ;
ALTER TABLE  `map_presentation_maps` DROP FOREIGN KEY  `map_presentation_maps_ibfk_2` ;

DROP TABLE  `map_presentation_maps`;
DROP TABLE  `map_presentation_users`;
DROP TABLE  `map_presentations`;

CREATE TABLE IF NOT EXISTS `conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `startValue` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `conditions_assign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `condition_id` int(11) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `conditions_assign` ADD FOREIGN KEY (  `condition_id` ) REFERENCES  `conditions` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `conditions_assign` ADD FOREIGN KEY (  `scenario_id` ) REFERENCES  `webinars` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

CREATE TABLE IF NOT EXISTS `conditions_change` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `condition_id` int(11) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `node_id` int(10) unsigned NOT NULL,
  `value` text NOT NULL,
  `appears` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `condition_id` (`condition_id`),
  KEY `node_id` (`node_id`),
  KEY `scenario_id` (`scenario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `conditions_change` ADD FOREIGN KEY (  `condition_id` ) REFERENCES  `conditions` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `conditions_change` ADD FOREIGN KEY (  `node_id` ) REFERENCES  `map_nodes` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `conditions_change` ADD FOREIGN KEY (  `scenario_id` ) REFERENCES  `webinars` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE  `webinars` ADD  `changeSteps` ENUM(  'manually',  'automatic' ) NOT NULL;

CREATE TABLE IF NOT EXISTS `cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_id` int(11) NOT NULL,
  `activate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rule_id` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `cron`
  ADD CONSTRAINT `cron_ibfk_1` FOREIGN KEY (`rule_id`) REFERENCES `map_counter_common_rules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `twitter_credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `API_key` text NOT NULL,
  `API_secret` text NOT NULL,
  `Access_token` text NOT NULL,
  `Access_token_secret` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `map_node_sections` ADD  `orderBy` ENUM(  'random',  'x',  'y' ) NOT NULL ;

ALTER TABLE `users` ADD `is_lti` TINYINT(1) DEFAULT '0';

CREATE TABLE IF NOT EXISTS `lti_consumer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_key` varchar(255) NOT NULL,
  `name` varchar(45) NOT NULL,
  `secret` varchar(32) NOT NULL,
  `lti_version` varchar(12) DEFAULT NULL,
  `consumer_name` varchar(255) DEFAULT NULL,
  `consumer_version` varchar(255) DEFAULT NULL,
  `consumer_guid` varchar(255) DEFAULT NULL,
  `css_path` varchar(255) DEFAULT NULL,
  `protected` tinyint(1) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `enable_from` datetime DEFAULT NULL,
  `enable_until` datetime DEFAULT NULL,
  `without_end_date` tinyint(1) DEFAULT NULL,
  `last_access` date DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `role` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `consumer_key` (`consumer_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `lti_contexts` (
  `consumer_key` varchar(255) NOT NULL,
  `context_id` varchar(255) NOT NULL,
  `lti_context_id` varchar(255) DEFAULT NULL,
  `lti_resource_id` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `settings` text,
  `primary_consumer_key` varchar(255) DEFAULT NULL,
  `primary_context_id` varchar(255) DEFAULT NULL,
  `share_approved` tinyint(1) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`consumer_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `lti_contexts`
  ADD CONSTRAINT `lti_contexts_ibfk_1` FOREIGN KEY (`consumer_key`) REFERENCES `lti_consumer` (`consumer_key`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `lti_nonces` (
  `consumer_key` varchar(255) NOT NULL,
  `value` varchar(32) NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`consumer_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `lti_nonces`
  ADD CONSTRAINT `lti_nonces_ibfk_1` FOREIGN KEY (`consumer_key`) REFERENCES `lti_consumer` (`consumer_key`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `lti_sharekeys` (
  `share_key_id` varchar(32) NOT NULL,
  `primary_consumer_key` varchar(255) NOT NULL,
  `primary_context_id` varchar(255) NOT NULL,
  `auto_approve` tinyint(1) NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`share_key_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

RENAME TABLE  `lti_consumer` TO  `lti_consumers` ;

CREATE TABLE IF NOT EXISTS `lti_users` (
  `consumer_key` varchar(255) NOT NULL,
  `context_id` varchar(255) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `lti_result_sourcedid` varchar(255) DEFAULT NULL,
  `created` DATETIME NOT NULL ,
  `updated` DATETIME NOT NULL ,
  PRIMARY KEY (`consumer_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE  `lti_users` ADD FOREIGN KEY (  `consumer_key` ) REFERENCES  `lti_contexts` ( `consumer_key` ) ON DELETE CASCADE ON UPDATE CASCADE ;

CREATE TABLE IF NOT EXISTS `vocablets` (
  `guid` varchar(50) NOT NULL,
  `state` varchar(10) NOT NULL,
  `version` varchar(5) NOT NULL,
  `name` varchar(64) NOT NULL,
  `path` varchar(128) NOT NULL,
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guid` (`guid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `metadata` ADD `guid` varchar(50) NULL DEFAULT NULL , ADD `state` TINYINT(1) NULL DEFAULT '1' ;

ALTER TABLE `metadata` ADD UNIQUE(`guid`);

ALTER TABLE  `rdf_terms` CHANGE  `name`  `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ;

CREATE TABLE IF NOT EXISTS `q_cumulative` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `reset` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `q_cumulative`
  ADD CONSTRAINT `q_cumulative_ibfk_2` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `q_cumulative_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `map_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;