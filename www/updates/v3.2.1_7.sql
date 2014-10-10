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