CREATE TABLE IF NOT EXISTS `webinars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `current_step` int(11) DEFAULT NULL,
  `forum_id` int(11) NOT NULL,
  `publish` varchar(100) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `webinar_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webinar_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `webinar_maps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webinar_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `step` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=152 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `webinar_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webinar_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=94 ;