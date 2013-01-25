CREATE TABLE IF NOT EXISTS `map_counter_common_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `rule` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB ;

--
-- Constraints for table `map_counter_common_rules`
--
ALTER TABLE `map_counter_common_rules`
  ADD CONSTRAINT `map_counter_common_rules_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
