DELETE FROM `map_question_types`;
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (1, 'single line text entry - not assessd',	'text', 'text', NULL);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (2, 'multi-line text entry - not assessed',	'area', 'area', NULL);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (3, 'multiple choice',	'mcq', 'choise', 0);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (4, 'pick choise',	'pqc', 'choise', 0);