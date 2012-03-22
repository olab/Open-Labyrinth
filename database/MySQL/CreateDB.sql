-- Drop database
DROP DATABASE `openlabyrinth`;
DROP USER 'ol_user'@'localhost';

-- Create user (username: ol_user; password: ol_user_pass) 
CREATE USER 'ol_user'@'localhost' IDENTIFIED BY 'ol_user_pass';

-- Create database (openlabyrinth)
CREATE DATABASE `openlabyrinth` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Link user with database
GRANT ALL PRIVILEGES ON `openlabyrinth` . * TO 'ol_user'@'localhost' WITH GRANT OPTION;

USE `openlabyrinth`;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `openlabyrinth`
--

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `groups`
--


-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `key` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `key`) VALUES
(1, 'EN', 'en-en'),
(2, 'FR', 'fr-fr');

-- --------------------------------------------------------

--
-- Table structure for table `maps`
--

CREATE TABLE IF NOT EXISTS `maps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `author_id` int(11) NOT NULL,
  `abstract` varchar(2000) NOT NULL,
  `startScore` int(11) NOT NULL,
  `threshold` int(11) NOT NULL,
  `keywords` varchar(500) NOT NULL DEFAULT '''''',
  `type_id` int(11) NOT NULL,
  `units` varchar(10) NOT NULL,
  `security_id` int(11) NOT NULL,
  `guid` varchar(50) NOT NULL,
  `timing` tinyint(1) NOT NULL,
  `delta_time` int(11) NOT NULL,
  `show_bar` tinyint(1) NOT NULL,
  `show_score` tinyint(1) NOT NULL,
  `skin_id` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `section_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `feedback` varchar(2000) NOT NULL,
  `dev_notes` varchar(1000) NOT NULL,
  `source` varchar(50) NOT NULL,
  `source_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `maps`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_avatars`
--

CREATE TABLE IF NOT EXISTS `map_avatars` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `skin_1` varchar(6) DEFAULT NULL,
  `skin_2` varchar(6) DEFAULT NULL,
  `cloth` varchar(6) DEFAULT NULL,
  `nose` varchar(2) DEFAULT NULL,
  `hair` varchar(2) DEFAULT NULL,
  `environment` varchar(2) DEFAULT NULL,
  `accessory_1` varchar(2) DEFAULT NULL,
  `bkd` varchar(6) DEFAULT NULL,
  `sex` varchar(2) DEFAULT NULL,
  `mouth` varchar(2) DEFAULT NULL,
  `outfit` varchar(2) DEFAULT NULL,
  `bubble` varchar(2) DEFAULT NULL,
  `bubble_text` varchar(100) DEFAULT NULL,
  `accessory_2` varchar(2) DEFAULT NULL,
  `accessory_3` varchar(2) DEFAULT NULL,
  `age` varchar(2) DEFAULT NULL,
  `eyes` varchar(2) DEFAULT NULL,
  `weather` varchar(2) DEFAULT NULL,
  `hair_color` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `map_avatars`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_chats`
--

CREATE TABLE IF NOT EXISTS `map_chats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `counter_id` int(11) NOT NULL,
  `stem` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `map_chats`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_chat_elements`
--

CREATE TABLE IF NOT EXISTS `map_chat_elements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `response` text NOT NULL,
  `function` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

--
-- Dumping data for table `map_chat_elements`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_contributors`
--

CREATE TABLE IF NOT EXISTS `map_contributors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `organization` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `map_contributors`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_contributor_roles`
--

CREATE TABLE IF NOT EXISTS `map_contributor_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(700) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `map_contributor_roles`
--

INSERT INTO `map_contributor_roles` (`id`, `name`, `description`) VALUES
(1, 'author', ''),
(2, 'publisher', ''),
(3, 'initiator', ''),
(4, 'validator', ''),
(5, 'editor', ''),
(6, 'graphical designer', ''),
(7, 'technical implementer', ''),
(8, 'content provider', ''),
(9, 'script writer', ''),
(10, 'instructional designer', ''),
(11, 'subject matter expert', ''),
(12, 'unknown', ''),
(13, 'Select', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_counters`
--

CREATE TABLE IF NOT EXISTS `map_counters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` text,
  `start_value` int(11) NOT NULL DEFAULT '0',
  `icon_id` int(11) DEFAULT NULL,
  `prefix` varchar(20) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '0',
  `out_of` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `map_counters`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_counter_rules`
--

CREATE TABLE IF NOT EXISTS `map_counter_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `counter_id` int(11) NOT NULL,
  `relation_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `function` varchar(50) DEFAULT NULL,
  `redirect_node_id` int(11) DEFAULT NULL,
  `message` varchar(500) DEFAULT NULL,
  `counter` int(11) DEFAULT NULL,
  `counter_value` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `map_counter_rules`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_counter_rule_relations`
--

CREATE TABLE IF NOT EXISTS `map_counter_rule_relations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(70) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `map_counter_rule_relations`
--

INSERT INTO `map_counter_rule_relations` (`id`, `title`, `value`) VALUES
(1, 'equal to', 'eq'),
(2, 'not equal to', 'neq'),
(3, 'less than or equal to', 'leq'),
(4, 'less than', 'lt'),
(5, 'greater that oe qual to', 'geq'),
(6, 'greater than', 'qt');

-- --------------------------------------------------------

--
-- Table structure for table `map_elements`
--

CREATE TABLE IF NOT EXISTS `map_elements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `mime` varchar(500) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `path` varchar(300) NOT NULL,
  `args` varchar(100) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `width_type` varchar(2) NOT NULL DEFAULT 'px',
  `height` int(11) DEFAULT NULL,
  `height_type` varchar(2) NOT NULL DEFAULT 'px',
  `h_align` varchar(20) DEFAULT NULL,
  `v_align` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `map_elements`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_feedback_operators`
--

CREATE TABLE IF NOT EXISTS `map_feedback_operators` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `map_feedback_operators`
--

INSERT INTO `map_feedback_operators` (`id`, `title`, `value`) VALUES
(1, 'equal to', 'eq'),
(2, 'not equal to', 'neq'),
(3, 'less than equal to', 'leq'),
(4, 'less than', 'lt'),
(5, 'greater than or equal to', 'geq'),
(6, 'greater than', 'qt');

-- --------------------------------------------------------

--
-- Table structure for table `map_feedback_rules`
--

CREATE TABLE IF NOT EXISTS `map_feedback_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `rule_type_id` int(11) NOT NULL,
  `value` int(11) DEFAULT NULL,
  `operator_id` int(11) DEFAULT NULL,
  `message` text,
  `counter_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `map_feedback_rules`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_feedback_types`
--

CREATE TABLE IF NOT EXISTS `map_feedback_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `map_feedback_types`
--

INSERT INTO `map_feedback_types` (`id`, `name`, `description`) VALUES
(1, 'time taken', NULL),
(2, 'counter value', NULL),
(3, 'node visit', NULL),
(4, 'must visit', NULL),
(5, 'must avoid', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `map_keys`
--

CREATE TABLE IF NOT EXISTS `map_keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `map_keys`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_nodes`
--

CREATE TABLE IF NOT EXISTS `map_nodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `text` text,
  `content` text,
  `type_id` int(11) DEFAULT NULL,
  `probability` tinyint(1) DEFAULT NULL,
  `conditional` varchar(500) DEFAULT NULL,
  `conditional_message` varchar(1000) DEFAULT NULL,
  `info` varchar(1000) DEFAULT NULL,
  `link_style_id` int(11) DEFAULT NULL,
  `link_type_id` int(11) DEFAULT '1',
  `priority_id` int(11) DEFAULT NULL,
  `kfp` tinyint(1) DEFAULT NULL,
  `undo` tinyint(1) DEFAULT NULL,
  `end` tinyint(1) DEFAULT NULL,
  `x` double DEFAULT NULL,
  `y` double DEFAULT NULL,
  `rgb` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `map_nodes`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_node_counters`
--

CREATE TABLE IF NOT EXISTS `map_node_counters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `counter_id` int(11) NOT NULL,
  `function` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `map_node_counters`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_node_links`
--

CREATE TABLE IF NOT EXISTS `map_node_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `node_id_1` int(11) unsigned NOT NULL,
  `node_id_2` int(11) NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `text` varchar(500) DEFAULT NULL,
  `order` int(11) DEFAULT '1',
  `probability` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `map_node_links`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_node_link_stylies`
--

CREATE TABLE IF NOT EXISTS `map_node_link_stylies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `map_node_link_stylies`
--

INSERT INTO `map_node_link_stylies` (`id`, `name`, `description`) VALUES
(1, 'text (default)', ''),
(2, 'dropdown', ''),
(3, 'dropdown + confidence', ''),
(4, 'type in text', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_node_link_types`
--

CREATE TABLE IF NOT EXISTS `map_node_link_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `map_node_link_types`
--

INSERT INTO `map_node_link_types` (`id`, `name`, `description`) VALUES
(1, 'ordered', ''),
(2, 'random order', ''),
(3, 'random select one *', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_node_priorities`
--

CREATE TABLE IF NOT EXISTS `map_node_priorities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `map_node_priorities`
--

INSERT INTO `map_node_priorities` (`id`, `name`, `description`) VALUES
(1, 'normal (default)', ''),
(2, 'must avoid', ''),
(3, 'must visit', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_node_sections`
--

CREATE TABLE IF NOT EXISTS `map_node_sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `map_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `map_node_sections`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_node_section_nodes`
--

CREATE TABLE IF NOT EXISTS `map_node_section_nodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `map_node_section_nodes`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_node_types`
--

CREATE TABLE IF NOT EXISTS `map_node_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `map_node_types`
--

INSERT INTO `map_node_types` (`id`, `name`, `description`) VALUES
(1, 'root', ''),
(2, 'child', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_questions`
--

CREATE TABLE IF NOT EXISTS `map_questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `stem` varchar(500) DEFAULT NULL,
  `entry_type_id` int(11) NOT NULL,
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `feedback` varchar(1000) DEFAULT NULL,
  `show_answer` tinyint(1) NOT NULL DEFAULT '1',
  `counter_id` int(11) DEFAULT NULL,
  `num_tries` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `map_questions`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_question_responses`
--

CREATE TABLE IF NOT EXISTS `map_question_responses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `response` varchar(250) DEFAULT NULL,
  `feedback` text,
  `is_correct` tinyint(1) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `map_question_responses`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_question_types`
--

CREATE TABLE IF NOT EXISTS `map_question_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(70) DEFAULT NULL,
  `value` varchar(20) DEFAULT NULL,
  `template_name` varchar(200) NOT NULL,
  `template_args` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `map_question_types`
--

INSERT INTO `map_question_types` (`id`, `title`, `value`, `template_name`, `template_args`) VALUES
(1, 'single line text entry - not assessd', 'text', 'text', NULL),
(2, 'multi-line text entry - not assessed', 'area', 'area', NULL),
(3, 'multiple choice - two options', 'mcq2', 'response', '2'),
(4, 'multiple choice - three options', 'mcq3', 'response', '3'),
(5, 'multiple choice - five options', 'mcq5', 'response', '5'),
(6, 'multiple choice - nine options', 'mcq9', 'response', '9');

-- --------------------------------------------------------

--
-- Table structure for table `map_sections`
--

CREATE TABLE IF NOT EXISTS `map_sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(700) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `map_sections`
--

INSERT INTO `map_sections` (`id`, `name`, `description`) VALUES
(1, 'don&#039;t show', ''),
(2, 'visible', ''),
(3, 'navigable', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_securities`
--

CREATE TABLE IF NOT EXISTS `map_securities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(700) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `map_securities`
--

INSERT INTO `map_securities` (`id`, `name`, `description`) VALUES
(1, 'open access', ''),
(2, 'closed (only logged in Labyrinth users can see it)', ''),
(3, 'private (only registered authors and users can see it)', ''),
(4, 'keys (a key is required to access this Labyrinth) - <a href=''editKeys''>edit</a>', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_skins`
--

CREATE TABLE IF NOT EXISTS `map_skins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `path` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `map_skins`
--

INSERT INTO `map_skins` (`id`, `name`, `path`) VALUES
(1, 'Basic', ''),
(2, 'Basic Exam', ''),
(3, 'NOSM', ''),
(4, 'PINE', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_types`
--

CREATE TABLE IF NOT EXISTS `map_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(700) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `map_types`
--

INSERT INTO `map_types` (`id`, `name`, `description`) VALUES
(1, 'Labyrinth Skin', ''),
(2, 'Game - scores, 1 startpoint, 1 or more endpoints', ''),
(3, 'Maze - no scores, 1 or more startpoints, no endpoints', ''),
(4, 'Algorithm - no scores, 1 startpoint, 1 or more endpoints', ''),
(5, 'Key Feature Problem', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_users`
--

CREATE TABLE IF NOT EXISTS `map_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `map_users`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `password` varchar(800) NOT NULL,
  `email` varchar(250) NOT NULL,
  `nickname` varchar(120) NOT NULL,
  `language_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`),
  KEY `fk_language_id` (`language_id`),
  KEY `fk_type_id` (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `nickname`, `language_id`, `type_id`) VALUES
(1, 'admin', 'bf7bdf17dad6154e88bf66b9768174a47658e84baa1036c3f6f0cbeae5be1db7', 'admin@admin.com', 'administrator', 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user_groups`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_types`
--

CREATE TABLE IF NOT EXISTS `user_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `user_types`
--

INSERT INTO `user_types` (`id`, `name`, `description`) VALUES
(1, 'learner', NULL),
(2, 'author', NULL),
(3, 'reviewer', NULL),
(4, 'superuser', NULL),
(5, 'remote service', NULL);
