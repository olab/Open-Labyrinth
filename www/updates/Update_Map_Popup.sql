CREATE TABLE IF NOT EXISTS `map_popup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `height` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `position_type` tinyint(1) NOT NULL DEFAULT '0',
  `position` tinyint(1) NOT NULL DEFAULT '1',
  `time_before` int(11) NOT NULL,
  `time_length` int(11) NOT NULL,
  `color` int(11) NOT NULL DEFAULT '1',
  `color_custom` varchar(255) NOT NULL,
  `map_id` int(11) NOT NULL,
  `assign_to_node` tinyint(1) NOT NULL DEFAULT '0',
  `node_id` int(11) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `map_popup_position` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;


INSERT INTO `map_popup_position` (`id`, `name`) VALUES
(1, 'Top left'),
(2, 'Top right'),
(3, 'Bottom left'),
(4, 'Bottom right');


CREATE TABLE IF NOT EXISTS `map_popup_style` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `desc` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;


INSERT INTO `map_popup_style` (`id`, `name`, `desc`) VALUES
(1, 'yellow', 'alert'),
(2, 'red', 'alert-error'),
(3, 'green', 'alert-success'),
(4, 'blue', 'alert-info');



