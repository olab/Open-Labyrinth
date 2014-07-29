INSERT INTO  `map_question_types` ( `id` , `title` , `value` , `template_name` , `template_args` ) VALUES ( '8',  'Situational Judgement Testing',  'sjt',  'sjt', NULL );

CREATE TABLE IF NOT EXISTS `sjt_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `response_id` int(10) unsigned NOT NULL,
  `position` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `sjt_response` ADD FOREIGN KEY (  `response_id` ) REFERENCES  `map_question_responses` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;