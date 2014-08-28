CREATE TABLE IF NOT EXISTS `conditions_assign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `condition_id` int(11) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `conditions_assign` ADD FOREIGN KEY (  `condition_id` ) REFERENCES  `conditions` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `conditions_assign` ADD FOREIGN KEY (  `scenario_id` ) REFERENCES  `webinars` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

CREATE TABLE IF NOT EXISTS `conditions_change` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `condition_id` int(11) NOT NULL,
  `scenario_id` int(11) NOT NULL,
  `node_id` int(10) unsigned NOT NULL,
  `value` text NOT NULL,
  `appears` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `condition_id` (`condition_id`),
  KEY `node_id` (`node_id`),
  KEY `scenario_id` (`scenario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `conditions_change` ADD FOREIGN KEY (  `condition_id` ) REFERENCES  `conditions` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `conditions_change` ADD FOREIGN KEY (  `node_id` ) REFERENCES  `map_nodes` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `conditions_change` ADD FOREIGN KEY (  `scenario_id` ) REFERENCES  `webinars` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;