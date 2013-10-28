ALTER TABLE `map_elements` ADD `is_shared` TINYINT NOT NULL DEFAULT '1';

CREATE TABLE IF NOT EXISTS `map_elements_metadata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_id` int(11) NOT NULL,
  `description` text,
  `originURL` varchar(300) DEFAULT NULL,
  `copyright` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;