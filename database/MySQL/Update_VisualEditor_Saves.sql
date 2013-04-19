CREATE TABLE IF NOT EXISTS `visual_editor_autosaves` (
  `map_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `json` text NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;