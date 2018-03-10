TRUNCATE TABLE `map_question_types`;
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES
(1, 'single line text entry', 'text', 'text', NULL),
(2, 'multi-line text entry', 'area', 'area', NULL),
(3, 'multiple choice', 'mcq', 'choice', '0'),
(4, 'pick choice', 'pcq', 'choice', '0');

ALTER TABLE `map_questions` ADD `type_display` INT NOT NULL DEFAULT '0';