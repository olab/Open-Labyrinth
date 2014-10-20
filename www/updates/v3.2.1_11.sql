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