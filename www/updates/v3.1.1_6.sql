UPDATE  `user_types` SET  `name` =  'Director' WHERE  `user_types`.`id` =6;
ALTER TABLE  `maps` ADD  `author_rights` INT NOT NULL ;
CREATE TABLE IF NOT EXISTS `author_rights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;