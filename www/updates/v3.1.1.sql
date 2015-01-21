--
-- v3.1.0.3_1.sql
--

ALTER TABLE `maps` ADD `assign_forum_id` INT NULL;
ALTER TABLE `dforum` ADD `verification` text NULL;
ALTER TABLE `dtopic` ADD `node_id` INT NULL;

--
-- v3.1.0.3_2.sql
--

ALTER TABLE  `map_popups_styles`
ADD  `background_transparent` VARCHAR( 4 ) NOT NULL ,
ADD  `border_transparent` VARCHAR( 4 ) NOT NULL ;

ALTER TABLE  `map_popups`
ADD  `title_hide` INT( 11 ) NOT NULL ,
ADD  `annotation` VARCHAR( 50 ) NOT NULL ;

--
-- v3.1.0.3_3.sql
--

CREATE TABLE IF NOT EXISTS `map_popups_counters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `popup_id` int(11) NOT NULL,
  `counter_id` int(10) unsigned NOT NULL,
  `function` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `popup_id` (`popup_id`),
  KEY `counter_id` (`counter_id`),
  KEY `counter_id_2` (`counter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `map_popups_counters`
  ADD CONSTRAINT `map_popups_counters_ibfk_1` FOREIGN KEY (`popup_id`) REFERENCES `map_popups` (`id`),
  ADD CONSTRAINT `map_popups_counters_ibfk_2` FOREIGN KEY (`counter_id`) REFERENCES `map_counters` (`id`);

ALTER TABLE `map_popups_assign` DROP PRIMARY KEY ;
ALTER TABLE  `map_popups_assign` ADD  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;

--
-- v3.1.0.3_4.sql
--

ALTER TABLE  `map_counters` ADD  `status` INT( 1 ) NOT NULL ;

--
-- v3.1.0.3_5.sql
--

ALTER TABLE  `map_popups_counters` CHANGE  `function`  `function` TEXT NOT NULL ;

--
-- v3.1.0.3_6.sql
--

ALTER TABLE `map_skins` ADD `data` TEXT NULL;

--
-- v3.1.0.3_7.sql
--

ALTER TABLE `user_sessiontraces` ADD `end_date_stamp` BIGINT NULL;

--
-- v3.1.0.3_8.sql
--

ALTER TABLE `users` ADD `history` VARCHAR(255) NULL;
ALTER TABLE `users` ADD `history_readonly` tinyint(1) NULL;
ALTER TABLE `users` ADD `history_timestamp` int(11) NULL;