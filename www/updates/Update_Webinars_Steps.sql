CREATE TABLE IF NOT EXISTS `webinar_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webinar_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;