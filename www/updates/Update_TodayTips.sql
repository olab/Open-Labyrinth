CREATE TABLE IF NOT EXISTS `today_tips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(300) NOT NULL,
  `text` text NOT NULL,
  `start_date` datetime NOT NULL,
  `weight` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '0',
  `is_archived` tinyint(4) NOT NULL DEFAULT '0',
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;