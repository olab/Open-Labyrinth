ALTER TABLE `users` ADD `history` VARCHAR(255) NULL;
ALTER TABLE `users` ADD `history_readonly` tinyint(1) NULL;
ALTER TABLE `users` ADD `history_timestamp` int(11) NULL;