--DELETE FROM `map_question_types` WHERE `id`='11' LIMIT 1;
ALTER TABLE `map_node_links` DROP COLUMN `hidden`;
ALTER TABLE `users` DROP COLUMN `settings`;
--DROP TABLE `webinar_macros`;