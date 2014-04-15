ALTER TABLE  `map_node_section_nodes` CHANGE  `node_type`  `node_type` ENUM(  'regular',  'in',  'out',  'crucial' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

ALTER TABLE  `map_popups_counters` DROP FOREIGN KEY  `map_popups_counters_ibfk_1` ;
ALTER TABLE  `map_popups_counters` ADD CONSTRAINT  `map_popups_counters_ibfk_1` FOREIGN KEY ( `popup_id` ) REFERENCES `map_popups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `map_popups_counters` DROP FOREIGN KEY  `map_popups_counters_ibfk_2` ;

CREATE TABLE IF NOT EXISTS `webinar_node_poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(10) unsigned NOT NULL,
  `webinar_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `node_id` (`node_id`),
  KEY `webinar_id` (`webinar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `webinar_node_poll`
  ADD CONSTRAINT `webinar_node_poll_ibfk_1` FOREIGN KEY (`webinar_id`) REFERENCES `webinars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `webinar_node_poll_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `map_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `webinar_poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `on_node` int(10) unsigned NOT NULL,
  `to_node` int(10) unsigned NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `on_node` (`on_node`),
  KEY `to_node` (`to_node`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `webinar_poll`
  ADD CONSTRAINT `webinar_poll_ibfk_2` FOREIGN KEY (`to_node`) REFERENCES `map_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `webinar_poll_ibfk_1` FOREIGN KEY (`on_node`) REFERENCES `map_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
