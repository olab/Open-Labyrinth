INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (11, 'Turk Talk', 'ttalk', 'lightarea', NULL);
ALTER TABLE `map_node_links` ADD COLUMN `hidden` tinyint(1) DEFAULT '0';
ALTER TABLE `users` ADD COLUMN `settings` text;