TRUNCATE TABLE `user_bookmarks`;
ALTER TABLE  `user_bookmarks` DROP  `time_stamp` ;
ALTER TABLE  `user_bookmarks` ADD  `user_id` INT( 10 ) UNSIGNED NOT NULL ;
ALTER TABLE  `user_bookmarks` ADD FOREIGN KEY (  `session_id` ) REFERENCES  `user_sessions` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `user_bookmarks` ADD FOREIGN KEY (  `node_id` ) REFERENCES  `map_nodes` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE  `user_bookmarks` ADD FOREIGN KEY (  `user_id` ) REFERENCES  `users` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;
