INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES ('13', 'Multiple-choice grid', 'mcq-grid', 'grid', NULL);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES ('14', 'Pick-choice grid', 'pcq-grid', 'grid', NULL);

ALTER TABLE `map_questions` CHANGE `map_id` `map_id` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `map_questions` ADD `parent_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `map_questions` ADD INDEX(`parent_id`);
ALTER TABLE `map_questions` ADD FOREIGN KEY (`parent_id`) REFERENCES `map_questions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `map_questions` ADD `order` INT(10) NULL DEFAULT NULL AFTER `is_private`;

ALTER TABLE `map_question_responses` CHANGE `question_id` `question_id` INT(10) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `map_question_responses` ADD `parent_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `map_question_responses` ADD INDEX(`parent_id`);
ALTER TABLE `map_question_responses` ADD FOREIGN KEY (`parent_id`) REFERENCES `map_question_responses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;