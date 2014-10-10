ALTER TABLE  `webinar_maps` ADD  `cumulative` BOOLEAN NOT NULL ;
INSERT INTO  `map_question_types` ( `id` , `title` , `value` , `template_name` , `template_args` ) VALUES ( NULL ,  'Cumulative',  'area',  'area', NULL );
ALTER TABLE  `user_sessions` ADD  `notCumulative` BOOLEAN NOT NULL ;