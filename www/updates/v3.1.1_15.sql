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