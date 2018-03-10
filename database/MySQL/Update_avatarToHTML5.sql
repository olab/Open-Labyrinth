DROP TABLE IF EXISTS `map_avatars`;

--
-- Table structure for table `map_avatars`
--

CREATE TABLE IF NOT EXISTS `map_avatars` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `skin_1` varchar(6) DEFAULT NULL,
  `skin_2` varchar(6) DEFAULT NULL,
  `cloth` varchar(6) DEFAULT NULL,
  `nose` varchar(20) DEFAULT NULL,
  `hair` varchar(20) DEFAULT NULL,
  `environment` varchar(20) DEFAULT NULL,
  `accessory_1` varchar(20) DEFAULT NULL,
  `bkd` varchar(6) DEFAULT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `mouth` varchar(20) DEFAULT NULL,
  `outfit` varchar(20) DEFAULT NULL,
  `bubble` varchar(20) DEFAULT NULL,
  `bubble_text` varchar(100) DEFAULT NULL,
  `accessory_2` varchar(20) DEFAULT NULL,
  `accessory_3` varchar(20) DEFAULT NULL,
  `age` varchar(2) DEFAULT NULL,
  `eyes` varchar(20) DEFAULT NULL,
  `hair_color` varchar(6) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`),
  KEY `map_id_2` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;