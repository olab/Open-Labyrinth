CREATE TABLE IF NOT EXISTS `vocablets` (
  `guid` varchar(50) NOT NULL,
  `state` varchar(10) NOT NULL,
  `version` varchar(5) NOT NULL,
  `name` varchar(64) NOT NULL,
  `path` varchar(128) NOT NULL,
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guid` (`guid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;



ALTER TABLE `metadata` ADD `guid` varchar(50) NULL DEFAULT NULL , ADD `state` TINYINT(1) NULL DEFAULT '1' ;

ALTER TABLE `metadata` ADD UNIQUE(`guid`);

ALTER TABLE  `rdf_terms` CHANGE  `name`  `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ;