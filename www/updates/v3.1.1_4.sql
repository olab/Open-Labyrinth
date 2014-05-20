ALTER TABLE  `webinar_users` CHANGE  `user_id`  `user_id` INT( 10 ) UNSIGNED NOT NULL ;
ALTER TABLE  `webinar_users` ADD FOREIGN KEY (  `user_id` ) REFERENCES  `users` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;