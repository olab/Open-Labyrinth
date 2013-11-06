DROP TABLE IF EXISTS `map_popup`, `map_popup_position`, `map_popup_style`;

CREATE TABLE IF NOT EXISTS `map_popups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `text` text NOT NULL,
  `position_type` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `time_before` int(11) NOT NULL DEFAULT '0',
  `time_length` int(11) NOT NULL DEFAULT '0',
  `is_enabled` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `map_popups_styles` (
  `map_popup_id` int(11) NOT NULL,
  `is_default_background_color` tinyint(4) NOT NULL DEFAULT '1',
  `is_background_transparent` TINYINT NOT NULL DEFAULT '0',
  `background_color` varchar(10) DEFAULT NULL,
  `font_color` varchar(10) DEFAULT NULL,
  `border_color` varchar(10) DEFAULT NULL,
  `is_border_transparent` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`map_popup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `map_popups_assign` (
  `map_popup_id` int(11) NOT NULL,
  `assign_type_id` int(11) NOT NULL,
  `assign_to_id` int(11) NOT NULL,
  `redirect_to_id` int(11) NULL,
  `redirect_type_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`map_popup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `map_popup_position_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `map_popup_position_types` (`id`, `title`) VALUES (1, 'Inside Node Area'), (2, 'Outside Node Area');

CREATE TABLE IF NOT EXISTS `map_popup_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `map_popup_positions` (`id`, `title`) VALUES (1, 'Top Left'), (2, 'Top Right'), (3, 'Bottom Left'), (4, 'Bottom Right');

CREATE TABLE IF NOT EXISTS `map_popup_assign_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `map_popup_assign_types` (`id`, `title`) VALUES (1, 'Labyrinth'), (2, 'Node'), (3, 'Section');