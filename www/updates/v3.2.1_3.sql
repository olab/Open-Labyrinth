ALTER TABLE  `map_presentation_maps` DROP FOREIGN KEY  `map_presentation_maps_ibfk_1` ;
ALTER TABLE  `map_presentation_maps` DROP FOREIGN KEY  `map_presentation_maps_ibfk_2` ;

DROP TABLE  `map_presentation_maps`;
DROP TABLE  `map_presentation_users`;
DROP TABLE  `map_presentations`;

CREATE TABLE IF NOT EXISTS `conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `startValue` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
