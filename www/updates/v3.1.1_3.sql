--
-- Add reference table
--

CREATE TABLE IF NOT EXISTS `map_node_references` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) unsigned NOT NULL,
  `node_id` int(11) unsigned NOT NULL,
  `element_id` int(11) unsigned NOT NULL,
  `type` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Add private field to the same tables
--

ALTER TABLE `map_elements` ADD `is_private` tinyint(4) NOT NULL DEFAULT '0';
ALTER TABLE `map_questions` ADD `is_private` INT( 4 ) NOT NULL DEFAULT '0';
ALTER TABLE `map_chats` ADD `is_private` INT( 4 ) NOT NULL DEFAULT '0';
ALTER TABLE `map_dams` ADD `is_private` INT( 4 ) NOT NULL DEFAULT '0';
ALTER TABLE `map_nodes` ADD `is_private` INT( 4 ) NOT NULL DEFAULT '0' AFTER `info` ;
ALTER TABLE `map_avatars` ADD `is_private` INT( 4 ) NOT NULL DEFAULT '0';