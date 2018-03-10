--
-- Update_Discussion_Forums.sql
--

CREATE TABLE IF NOT EXISTS `dforum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `closed` tinyint(1) NOT NULL,
  `date` datetime NOT NULL,
  `author_id` int(11) NOT NULL,
  `settings` text NOT NULL,
  `security_id` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `dforum_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_group` int(11) NOT NULL,
  `id_forum` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `dforum_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `type` tinyint(1) NOT NULL,
  `isEdit` tinyint(1) NOT NULL DEFAULT '0',
  `forum_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `dforum_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_forum` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Update_User_Responses_Node_Id.sql
--

ALTER TABLE  `user_responses` ADD  `node_id` INT UNSIGNED NOT NULL;

--
-- Update_User_Session_WebinarId.sql
--

ALTER TABLE  `user_sessions` ADD  `webinar_id` INT NULL;
ALTER TABLE  `user_sessions` ADD  `webinar_step` INT NULL;

--
-- Update_Webinars.sql
--

CREATE TABLE IF NOT EXISTS `webinars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `current_step` int(11) DEFAULT NULL,
  `forum_id` int(11) NOT NULL,
  `publish` varchar(100) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `webinar_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webinar_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `webinar_maps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webinar_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `step` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `webinar_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webinar_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Update_Maps.sql
--

ALTER TABLE `maps` ADD `reminder_msg` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `delta_time` , 
ADD `reminder_time` INT( 11 ) NOT NULL AFTER `reminder_msg`;

--
-- Update_Map_Counter_Common_Rules.sql
--

ALTER TABLE `map_counter_common_rules` ADD `isCorrect` TINYINT( 1 ) NOT NULL DEFAULT '1';

--
-- Update_Map_Popup.sql
--

CREATE TABLE IF NOT EXISTS `map_popup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `height` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `position_type` tinyint(1) NOT NULL DEFAULT '0',
  `position` tinyint(1) NOT NULL DEFAULT '1',
  `time_before` int(11) NOT NULL,
  `time_length` int(11) NOT NULL,
  `color` int(11) NOT NULL DEFAULT '1',
  `color_custom` varchar(255) NOT NULL,
  `map_id` int(11) NOT NULL,
  `assign_to_node` tinyint(1) NOT NULL DEFAULT '0',
  `node_id` int(11) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `map_popup_position` (
  `id` tinyint(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;


INSERT INTO `map_popup_position` (`id`, `name`) VALUES
(1, 'Top left'),
(2, 'Top right'),
(3, 'Bottom left'),
(4, 'Bottom right');


CREATE TABLE IF NOT EXISTS `map_popup_style` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `desc` varchar(50) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;


INSERT INTO `map_popup_style` (`id`, `name`, `desc`) VALUES
(1, 'yellow', 'alert'),
(2, 'red', 'alert-error'),
(3, 'green', 'alert-success'),
(4, 'blue', 'alert-info');

--
-- Update_Webinar_User.sql
--

ALTER TABLE `webinar_users` ADD `include_4R` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `user_id`;

--
-- Update_Webinars_Steps.sql
--

CREATE TABLE IF NOT EXISTS `webinar_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webinar_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Update_Webinar_Statistics.sql
--

CREATE TABLE IF NOT EXISTS `statistics_user_datesave` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_save` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `statistics_user_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `response` varchar(700) CHARACTER SET utf8 NOT NULL,
  `node_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `statistics_user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `user_ip` varchar(50) CHARACTER SET utf8 NOT NULL,
  `webinar_id` int(11) NOT NULL,
  `webinar_step` int(11) NOT NULL,
  `date_save_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `statistics_user_sessiontraces` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `map_id` int(11) unsigned NOT NULL,
  `node_id` int(11) unsigned NOT NULL,
  `counters` varchar(700) CHARACTER SET utf8 DEFAULT NULL,
  `date_stamp` int(11) DEFAULT NULL,
  `confidence` smallint(6) DEFAULT NULL,
  `dams` varchar(700) CHARACTER SET utf8 DEFAULT NULL,
  `bookmark_made` int(11) DEFAULT NULL,
  `bookmark_used` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Update_Rules_Map.sql
--

ALTER TABLE `map_counter_common_rules` CHANGE `rule` `rule` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `maps` CHANGE `reminder_msg` `reminder_msg` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `reminder_time` `reminder_time` INT( 11 ) NOT NULL DEFAULT '0';

--
-- Update_Map_Nodes_DropContent.sql
--

ALTER TABLE `map_nodes` DROP `content`;

--
-- Update_Map_Nodes_ShowInfo.sql
--

ALTER TABLE `map_nodes` ADD `show_info` TINYINT NOT NULL DEFAULT '0';

--
-- Update_Map_Nodes_UpdateInfoFieldType.sql
--

ALTER TABLE  `map_nodes` CHANGE  `info`  `info` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

--
-- Update_DTopics.sql
--

CREATE TABLE IF NOT EXISTS `dtopic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date` datetime NOT NULL,
  `author_id` int(11) NOT NULL,
  `settings` text NOT NULL,
  `security_id` tinyint(1) NOT NULL,
  `forum_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dtopic_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_group` int(11) NOT NULL,
  `id_topic` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dtopic_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `type` tinyint(1) NOT NULL,
  `isEdit` tinyint(1) NOT NULL,
  `topic_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dtopic_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_topic` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dforum_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `desc` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `dforum_status` (`id`, `name`, `desc`) VALUES
(1, 'Open', ''),
(2, 'Close', ''),
(3, 'Archived', '');

ALTER TABLE `webinars` ADD `isForum` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `forum_id`;

ALTER TABLE `dforum` DROP `closed`;

ALTER TABLE `dforum` ADD `status` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `name`;

--
-- Update_Map_Nodes_Annotation.sql
--

ALTER TABLE `map_nodes` ADD `annotation` TEXT NULL;

--
-- Update_Discussion_Forums_Notification.sql
--

ALTER TABLE `dforum_users` ADD `is_notificate` TINYINT NOT NULL DEFAULT '1';
ALTER TABLE `dtopic_users` ADD `is_notificate` TINYINT NOT NULL DEFAULT '1';

--
-- Update_Map_Question_Response_Order.sql
--

ALTER TABLE `map_question_responses` ADD `order` INT UNSIGNED NOT NULL DEFAULT '0';

--
-- Update_Map_Question_DragAndDrop_Type.sql
--

INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES (NULL, 'Drag and Drop', 'dd', 'draganddrop', NULL);

