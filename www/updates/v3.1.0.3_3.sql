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
