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