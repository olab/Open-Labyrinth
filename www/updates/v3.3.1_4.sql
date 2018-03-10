INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (11, 'Turk Talk', 'ttalk', 'lightarea', NULL);
ALTER TABLE `map_node_links` ADD COLUMN `hidden` tinyint(1) DEFAULT '0';
ALTER TABLE `users` ADD COLUMN `settings` text;

CREATE TABLE `webinar_macros` (
`id` int(11) unsigned NOT NULL,
  `text` varchar(255) DEFAULT NULL,
  `hot_keys` varchar(255) DEFAULT NULL,
  `webinar_id` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `webinar_macros`
 ADD PRIMARY KEY (`id`), ADD KEY `webinar_id` (`webinar_id`);

ALTER TABLE `webinar_macros`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;