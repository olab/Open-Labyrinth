ALTER TABLE `user_sessions` ADD COLUMN `end_time` int(11) DEFAULT NULL;
ALTER TABLE `maps` ADD COLUMN `revisable_answers` tinyint(1) NOT NULL;
ALTER TABLE `user_responses` ADD COLUMN `created_at` int(11) NOT NULL;