CREATE TABLE IF NOT EXISTS `lti_users` (
  `consumer_key` varchar(255) NOT NULL,
  `context_id` varchar(255) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `lti_result_sourcedid` varchar(255) DEFAULT NULL,
  `created` DATETIME NOT NULL ,
  `updated` DATETIME NOT NULL ,
  PRIMARY KEY (`consumer_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE  `lti_users` ADD FOREIGN KEY (  `consumer_key` ) REFERENCES  `openlabyrinth`.`lti_contexts` ( `consumer_key` ) ON DELETE CASCADE ON UPDATE CASCADE ;