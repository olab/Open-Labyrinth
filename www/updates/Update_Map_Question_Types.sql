TRUNCATE TABLE `map_question_types`;
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (1, 'single line text entry',	'text', 'text', NULL);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (2, 'multi-line text entry',	'area', 'area', NULL);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (3, 'multiple choice',	'mcq', 'choice', 0);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (4, 'pick choise',	'pqc', 'choise', 0);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (5, 'slider',	'slr', 'slider', NULL);