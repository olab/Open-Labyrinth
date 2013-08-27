CREATE TABLE IF NOT EXISTS `statistics_user_datesave` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_save` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `statistics_user_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `response` varchar(700) CHARACTER SET utf8 NOT NULL,
  `node_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `statistics_user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `user_ip` varchar(50) CHARACTER SET utf8 NOT NULL,
  `webinar_id` int(11) NOT NULL,
  `webinar_step` int(11) NOT NULL,
  `date_save_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `statistics_user_sessiontraces` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `map_id` int(11) unsigned NOT NULL,
  `node_id` int(11) unsigned NOT NULL,
  `counters` varchar(700) CHARACTER SET utf8 DEFAULT NULL,
  `date_stamp` int(11) DEFAULT NULL,
  `confidence` smallint(6) DEFAULT NULL,
  `dams` varchar(700) CHARACTER SET utf8 DEFAULT NULL,
  `bookmark_made` int(11) DEFAULT NULL,
  `bookmark_used` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;