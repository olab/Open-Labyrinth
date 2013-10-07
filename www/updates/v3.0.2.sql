--
-- Update_LinkStyle
--

TRUNCATE TABLE `map_node_link_stylies`;

INSERT INTO `map_node_link_stylies` (`id`, `name`, `description`) VALUES
(1, 'hyperlinks (default)', ''),
(2, 'dropdown', ''),
(3, 'dropdown + confidence', ''),
(4, 'type in text', ''),
(5, 'buttons', '');

--
-- Update_Map_Question_Settings
--

ALTER TABLE `map_questions` ADD `settings` TEXT DEFAULT NULL;

--
-- Update_Map_Question_Responses
--

ALTER TABLE `map_question_responses` ADD `from` varchar(200) DEFAULT NULL;
ALTER TABLE `map_question_responses` ADD `to` varchar(200) DEFAULT NULL;

--
-- Update_OAuth_Provider
--

CREATE TABLE IF NOT EXISTS `oauth_providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `version` varchar(200) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `appId` varchar(300) DEFAULT NULL,
  `secret` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

INSERT INTO `oauth_providers` (`id`, `name`, `version`, `icon`, `appId`, `secret`) VALUES
(1, 'github', 'v2', 'github_icon.png', '', ''),
(3, 'facebook', 'v2', 'facebook_icon.png', '', ''),
(4, 'twitter', 'v1', 'twitter_icon.png', '', ''),
(5, 'linkedin', 'v2', 'linkedin_icon.png', '', ''),
(6, 'google', 'v2', 'google_icon.png', '', ''),
(7, 'flickr', 'v1', 'flickr_icon.png', '', ''),
(8, 'tumblr', 'v1', 'tumblr_icon.png', '', '');

ALTER TABLE `users` ADD `oauth_provider_id` int(11) DEFAULT NULL;
ALTER TABLE `users` ADD `oauth_id` varchar(300) DEFAULT NULL;

--
-- Update_Map_Questions_Types
--

TRUNCATE TABLE `map_question_types`;
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (1, 'single line text entry',	'text', 'text', NULL);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (2, 'multi-line text entry',	'area', 'area', NULL);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (3, 'multiple choice',	'mcq', 'choice', 0);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (4, 'pick choice',	'pcq', 'choice', 0);
INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (5, 'slider',	'slr', 'slider', NULL);

--
-- Update_TodayTips
--

CREATE TABLE IF NOT EXISTS `today_tips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(300) NOT NULL,
  `text` text NOT NULL,
  `start_date` datetime NOT NULL,
  `weight` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '0',
  `is_archived` tinyint(4) NOT NULL DEFAULT '0',
  `end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Update_Users_VisualEditor_Settings
--

ALTER TABLE `users` ADD `visualEditorAutosaveTime` int(11) DEFAULT '50000';

--
-- Table structure for table `map_visual_displays`
--

CREATE TABLE IF NOT EXISTS `map_visual_displays` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `is_all_page_show` TINYINT NOT NULL DEFAULT  '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=121 ;

-- --------------------------------------------------------

--
-- Table structure for table `map_visual_display_counters`
--

CREATE TABLE IF NOT EXISTS `map_visual_display_counters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `visual_id` int(10) unsigned NOT NULL,
  `counter_id` int(10) unsigned NOT NULL,
  `label_x` int(11) DEFAULT '0',
  `label_y` int(11) DEFAULT '0',
  `label_angle` int(11) DEFAULT '0',
  `label_font_style` varchar(300) DEFAULT NULL,
  `label_text` text,
  `label_z_index` int(10) unsigned DEFAULT '0',
  `value_x` int(11) DEFAULT '0',
  `value_y` int(11) DEFAULT '0',
  `value_angle` int(11) DEFAULT '0',
  `value_font_style` varchar(300) DEFAULT NULL,
  `value_z_index` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `visual_id` (`visual_id`),
  KEY `counter_id` (`counter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `map_visual_display_images`
--

CREATE TABLE IF NOT EXISTS `map_visual_display_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `visual_id` int(10) unsigned NOT NULL,
  `name` varchar(400) NOT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `angle` int(11) DEFAULT '0',
  `z_index` int(10) unsigned DEFAULT '0',
  `x` int(11) DEFAULT '0',
  `y` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `visual_id` (`visual_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Table structure for table `map_visual_display_panels`
--

CREATE TABLE IF NOT EXISTS `map_visual_display_panels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `visual_id` int(10) unsigned NOT NULL,
  `x` int(11) DEFAULT '0',
  `y` int(11) DEFAULT '0',
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `background_color` varchar(40) DEFAULT NULL,
  `border_size` int(10) unsigned DEFAULT '0',
  `border_color` varchar(40) DEFAULT NULL,
  `border_radius` int(10) unsigned DEFAULT '0',
  `angle` int(11) DEFAULT '0',
  `z_index` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `visual_id` (`visual_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `map_visual_display_counters`
--
ALTER TABLE `map_visual_display_counters`
  ADD CONSTRAINT `map_visual_display_counters_ibfk_1` FOREIGN KEY (`visual_id`) REFERENCES `map_visual_displays` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `map_visual_display_counters_ibfk_2` FOREIGN KEY (`counter_id`) REFERENCES `map_counters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_visual_display_images`
--
ALTER TABLE `map_visual_display_images`
  ADD CONSTRAINT `map_visual_display_images_ibfk_1` FOREIGN KEY (`visual_id`) REFERENCES `map_visual_displays` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_visual_display_panels`
--
ALTER TABLE `map_visual_display_panels`
  ADD CONSTRAINT `map_visual_display_panels_ibfk_1` FOREIGN KEY (`visual_id`) REFERENCES `map_visual_displays` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

