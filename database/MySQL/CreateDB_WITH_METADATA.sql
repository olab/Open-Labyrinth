CREATE DATABASE IF NOT EXISTS `openlabyrinth` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Create user (username: ol_user; password: ol_user_pass) 
CREATE USER IF NOT EXISTS 'ol_user'@'localhost' IDENTIFIED BY 'ol_user_pass';

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
  `author_id` int(10) unsigned NOT NULL,
  `abstract` varchar(2000) NOT NULL,
  `startScore` int(11) NOT NULL,
  `threshold` int(11) NOT NULL,
  `keywords` varchar(500) NOT NULL DEFAULT '''''',
  `type_id` int(10) unsigned NOT NULL,
  `units` varchar(10) NOT NULL,
  `security_id` int(10) unsigned NOT NULL,
  `guid` varchar(50) NOT NULL,
  `timing` tinyint(1) NOT NULL,
  `delta_time` int(11) NOT NULL,
  `show_bar` tinyint(1) NOT NULL,
  `show_score` tinyint(1) NOT NULL,
  `skin_id` int(10) unsigned NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned DEFAULT '1',
  `feedback` varchar(2000) NOT NULL,
  `dev_notes` varchar(1000) NOT NULL,
  `source` varchar(50) NOT NULL,
  `source_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `author_id` (`author_id`),
  KEY `author_id_2` (`author_id`,`type_id`,`security_id`,`section_id`,`language_id`),
  KEY `security_id` (`security_id`),
  KEY `type_id` (`type_id`,`skin_id`,`section_id`,`language_id`),
  KEY `skin_id` (`skin_id`),
  KEY `section_id` (`section_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `maps`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_avatars`
--

CREATE TABLE IF NOT EXISTS `map_avatars` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `skin_1` varchar(6) DEFAULT NULL,
  `skin_2` varchar(6) DEFAULT NULL,
  `cloth` varchar(6) DEFAULT NULL,
  `nose` varchar(20) DEFAULT NULL,
  `hair` varchar(20) DEFAULT NULL,
  `environment` varchar(20) DEFAULT NULL,
  `accessory_1` varchar(20) DEFAULT NULL,
  `bkd` varchar(6) DEFAULT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `mouth` varchar(20) DEFAULT NULL,
  `outfit` varchar(20) DEFAULT NULL,
  `bubble` varchar(20) DEFAULT NULL,
  `bubble_text` varchar(100) DEFAULT NULL,
  `accessory_2` varchar(20) DEFAULT NULL,
  `accessory_3` varchar(20) DEFAULT NULL,
  `age` varchar(2) DEFAULT NULL,
  `eyes` varchar(20) DEFAULT NULL,
  `hair_color` varchar(6) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`),
  KEY `map_id_2` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_avatars`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_chats`
--

CREATE TABLE IF NOT EXISTS `map_chats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `counter_id` int(10) unsigned DEFAULT NULL,
  `stem` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`,`counter_id`),
  KEY `counter_id` (`counter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_chats`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_chat_elements`
--

CREATE TABLE IF NOT EXISTS `map_chat_elements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chat_id` int(10) unsigned NOT NULL,
  `question` text NOT NULL,
  `response` text NOT NULL,
  `function` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_id` (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_chat_elements`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_collectionMaps`
--

CREATE TABLE IF NOT EXISTS `map_collectionMaps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `collection_id` (`collection_id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_collectionMaps`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_collections`
--

CREATE TABLE IF NOT EXISTS `map_collections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_collections`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_contributors`
--

CREATE TABLE IF NOT EXISTS `map_contributors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `role_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `organization` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `map_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` text,
  `start_value` DOUBLE NOT NULL DEFAULT '0',
  `icon_id` int(11) DEFAULT NULL,
  `prefix` varchar(20) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `visible` tinyint(1) DEFAULT '0',
  `out_of` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_counters`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_counter_rules`
--

CREATE TABLE IF NOT EXISTS `map_counter_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `counter_id` int(10) unsigned NOT NULL,
  `relation_id` int(11) NOT NULL,
  `value` DOUBLE NOT NULL DEFAULT '0',
  `function` varchar(50) DEFAULT NULL,
  `redirect_node_id` int(11) DEFAULT NULL,
  `message` varchar(500) DEFAULT NULL,
  `counter` int(11) DEFAULT NULL,
  `counter_value` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `counter_id` (`counter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
(6, 'greater than', 'gt');

-- --------------------------------------------------------

--
-- Table structure for table `map_dams`
--

CREATE TABLE IF NOT EXISTS `map_dams` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `name` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_dams`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_dam_elements`
--

CREATE TABLE IF NOT EXISTS `map_dam_elements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dam_id` int(10) unsigned NOT NULL,
  `element_type` varchar(20) DEFAULT NULL,
  `order` int(11) DEFAULT '0',
  `display` varchar(20) NOT NULL,
  `element_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dam_id` (`dam_id`),
  KEY `element_id` (`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_dam_elements`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_elements`
--

CREATE TABLE IF NOT EXISTS `map_elements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
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
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `map_id` int(10) unsigned NOT NULL,
  `rule_type_id` int(11) NOT NULL,
  `value` int(11) DEFAULT NULL,
  `operator_id` int(11) DEFAULT NULL,
  `message` text,
  `counter_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `map_id` int(10) unsigned NOT NULL,
  `key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`key`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_keys`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_nodes`
--

CREATE TABLE IF NOT EXISTS `map_nodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
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
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `map_nodes`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_node_counters`
--

CREATE TABLE IF NOT EXISTS `map_node_counters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `node_id` int(10) unsigned NOT NULL,
  `counter_id` int(11) NOT NULL,
  `function` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `node_id` (`node_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_node_counters`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_node_links`
--

CREATE TABLE IF NOT EXISTS `map_node_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `node_id_1` int(10) unsigned NOT NULL,
  `node_id_2` int(10) unsigned NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `text` varchar(500) DEFAULT NULL,
  `order` int(11) DEFAULT '1',
  `probability` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`),
  KEY `node_id_1` (`node_id_1`),
  KEY `node_id_2` (`node_id_2`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

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
  `map_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_node_sections`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_node_section_nodes`
--

CREATE TABLE IF NOT EXISTS `map_node_section_nodes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(10) unsigned NOT NULL,
  `node_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  KEY `section_id_2` (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
-- Table structure for table `map_presentations`
--

CREATE TABLE IF NOT EXISTS `map_presentations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `header` varchar(3000) DEFAULT NULL,
  `footer` varchar(3000) DEFAULT NULL,
  `skin_id` int(10) unsigned DEFAULT NULL,
  `access` int(11) DEFAULT NULL,
  `login` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `start_date` bigint(20) DEFAULT NULL,
  `end_date` bigint(20) DEFAULT NULL,
  `tries` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_presentations`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_presentation_maps`
--

CREATE TABLE IF NOT EXISTS `map_presentation_maps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `presentation_id` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `presentation_id` (`presentation_id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_presentation_maps`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_presentation_users`
--

CREATE TABLE IF NOT EXISTS `map_presentation_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `presentation_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `presentation_id` (`presentation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_presentation_users`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_questions`
--

CREATE TABLE IF NOT EXISTS `map_questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `stem` varchar(500) DEFAULT NULL,
  `entry_type_id` int(11) NOT NULL,
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `feedback` varchar(1000) DEFAULT NULL,
  `show_answer` tinyint(1) NOT NULL DEFAULT '1',
  `counter_id` int(11) DEFAULT NULL,
  `num_tries` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_questions`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_question_responses`
--

CREATE TABLE IF NOT EXISTS `map_question_responses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned NOT NULL,
  `response` varchar(250) DEFAULT NULL,
  `feedback` text,
  `is_correct` tinyint(1) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
(1, 'Basic', 'basic/basic'),
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
(5, 'Key Feature Problem', ''),
(6, 'Leniar', ''),
(7, 'HEIDR', ''),
(8, 'Semi-leniar', ''),
(9, 'Branched', ''),
(10, 'Make My Own', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_users`
--

CREATE TABLE IF NOT EXISTS `map_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_users`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_vpds`
--

CREATE TABLE IF NOT EXISTS `map_vpds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `vpd_type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `vpd_type_id` (`vpd_type_id`),
  KEY `vpd_type_id_2` (`vpd_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_vpds`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_vpd_elements`
--

CREATE TABLE IF NOT EXISTS `map_vpd_elements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vpd_id` int(10) unsigned NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `vpd_id` (`vpd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_vpd_elements`
--


-- --------------------------------------------------------

--
-- Table structure for table `map_vpd_types`
--

CREATE TABLE IF NOT EXISTS `map_vpd_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `label` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `map_vpd_types`
--

INSERT INTO `map_vpd_types` (`id`, `name`, `label`) VALUES
(1, 'VPDText', 'Text'),
(2, 'PatientDiagnoses', 'Patient Demographics'),
(3, 'AuthorDiagnoses', 'Author Diagnosis'),
(4, 'Medication', 'Medication'),
(5, 'InterviewItem', 'Question'),
(6, 'PhysicalExam', 'Physical Exam'),
(7, 'DiagnosticTest', 'Diagnostic Test'),
(8, 'DifferentialDiagnostic', 'Differintial Diagnostsis'),
(9, 'Intervention', 'Intervention');

-- --------------------------------------------------------

--
-- Table structure for table `remoteMaps`
--

CREATE TABLE IF NOT EXISTS `remoteMaps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `remoteMaps`
--


-- --------------------------------------------------------

--
-- Table structure for table `remoteServices`
--

CREATE TABLE IF NOT EXISTS `remoteServices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(1) NOT NULL,
  `ip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `remoteServices`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `nickname`, `language_id`, `type_id`) VALUES
(1, 'admin', 'bf7bdf17dad6154e88bf66b9768174a47658e84baa1036c3f6f0cbeae5be1db7', 'admin@admin.com', 'administrator', 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `user_bookmarks`
--

CREATE TABLE IF NOT EXISTS `user_bookmarks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `time_stamp` bigint(20) NOT NULL,
  `node_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `user_bookmarks`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id_2` (`user_id`),
  KEY `group_id_2` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user_groups`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_responses`
--

CREATE TABLE IF NOT EXISTS `user_responses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned NOT NULL,
  `session_id` int(10) unsigned NOT NULL,
  `response` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user_responses`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `start_time` int(11) NOT NULL,
  `user_ip` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `user_id` (`user_id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `user_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_sessiontraces`
--

CREATE TABLE IF NOT EXISTS `user_sessiontraces` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `map_id` int(10) unsigned NOT NULL,
  `node_id` int(10) unsigned NOT NULL,
  `counters` varchar(700) DEFAULT NULL,
  `date_stamp` bigint(20) DEFAULT NULL,
  `confidence` smallint(6) DEFAULT NULL,
  `dams` varchar(700) DEFAULT NULL,
  `bookmark_made` int(11) DEFAULT NULL,
  `bookmark_used` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `user_id` (`user_id`),
  KEY `map_id` (`map_id`),
  KEY `node_id` (`node_id`),
  KEY `session_id_2` (`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `user_sessiontraces`
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

--
-- Constraints for dumped tables
--

--
-- Constraints for table `maps`
--
ALTER TABLE `maps`
  ADD CONSTRAINT `maps_ibfk_2` FOREIGN KEY (`security_id`) REFERENCES `map_securities` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `maps_ibfk_3` FOREIGN KEY (`type_id`) REFERENCES `map_types` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `maps_ibfk_4` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `maps_ibfk_5` FOREIGN KEY (`skin_id`) REFERENCES `map_skins` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `maps_ibfk_6` FOREIGN KEY (`section_id`) REFERENCES `map_sections` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `maps_ibfk_7` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `map_avatars`
--
ALTER TABLE `map_avatars`
  ADD CONSTRAINT `map_avatars_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_chats`
--
ALTER TABLE `map_chats`
  ADD CONSTRAINT `map_chats_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_chat_elements`
--
ALTER TABLE `map_chat_elements`
  ADD CONSTRAINT `map_chat_elements_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `map_chats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_collectionMaps`
--
ALTER TABLE `map_collectionMaps`
  ADD CONSTRAINT `map_collectionmaps_ibfk_1` FOREIGN KEY (`collection_id`) REFERENCES `map_collections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `map_collectionmaps_ibfk_2` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_contributors`
--
ALTER TABLE `map_contributors`
  ADD CONSTRAINT `map_contributors_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_counters`
--
ALTER TABLE `map_counters`
  ADD CONSTRAINT `map_counters_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_counter_rules`
--
ALTER TABLE `map_counter_rules`
  ADD CONSTRAINT `map_counter_rules_ibfk_1` FOREIGN KEY (`counter_id`) REFERENCES `map_counters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_dams`
--
ALTER TABLE `map_dams`
  ADD CONSTRAINT `map_dams_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_dam_elements`
--
ALTER TABLE `map_dam_elements`
  ADD CONSTRAINT `map_dam_elements_ibfk_1` FOREIGN KEY (`dam_id`) REFERENCES `map_dams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_elements`
--
ALTER TABLE `map_elements`
  ADD CONSTRAINT `map_elements_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_feedback_rules`
--
ALTER TABLE `map_feedback_rules`
  ADD CONSTRAINT `map_feedback_rules_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_keys`
--
ALTER TABLE `map_keys`
  ADD CONSTRAINT `map_keys_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_nodes`
--
ALTER TABLE `map_nodes`
  ADD CONSTRAINT `map_nodes_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_node_counters`
--
ALTER TABLE `map_node_counters`
  ADD CONSTRAINT `map_node_counters_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `map_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_node_links`
--
ALTER TABLE `map_node_links`
  ADD CONSTRAINT `map_node_links_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `map_node_links_ibfk_4` FOREIGN KEY (`node_id_1`) REFERENCES `map_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `map_node_links_ibfk_5` FOREIGN KEY (`node_id_2`) REFERENCES `map_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_node_sections`
--
ALTER TABLE `map_node_sections`
  ADD CONSTRAINT `map_node_sections_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_node_section_nodes`
--
ALTER TABLE `map_node_section_nodes`
  ADD CONSTRAINT `map_node_section_nodes_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `map_node_sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_presentation_maps`
--
ALTER TABLE `map_presentation_maps`
  ADD CONSTRAINT `map_presentation_maps_ibfk_1` FOREIGN KEY (`presentation_id`) REFERENCES `map_presentations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `map_presentation_maps_ibfk_2` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_presentation_users`
--
ALTER TABLE `map_presentation_users`
  ADD CONSTRAINT `map_presentation_users_ibfk_1` FOREIGN KEY (`presentation_id`) REFERENCES `map_presentations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `map_presentation_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_questions`
--
ALTER TABLE `map_questions`
  ADD CONSTRAINT `map_questions_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_question_responses`
--
ALTER TABLE `map_question_responses`
  ADD CONSTRAINT `map_question_responses_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `map_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_users`
--
ALTER TABLE `map_users`
  ADD CONSTRAINT `map_users_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `map_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_vpds`
--
ALTER TABLE `map_vpds`
  ADD CONSTRAINT `map_vpds_ibfk_1` FOREIGN KEY (`vpd_type_id`) REFERENCES `map_vpd_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_vpd_elements`
--
ALTER TABLE `map_vpd_elements`
  ADD CONSTRAINT `map_vpd_elements_ibfk_1` FOREIGN KEY (`vpd_id`) REFERENCES `map_vpds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD CONSTRAINT `user_groups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_responses`
--
ALTER TABLE `user_responses`
  ADD CONSTRAINT `user_responses_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `map_questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_responses_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `user_sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_2` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_sessiontraces`
--
ALTER TABLE `user_sessiontraces`
  ADD CONSTRAINT `user_sessiontraces_ibfk_3` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_sessiontraces_ibfk_5` FOREIGN KEY (`session_id`) REFERENCES `user_sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_sessiontraces_ibfk_6` FOREIGN KEY (`node_id`) REFERENCES `map_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  
--
-- Table structure for table `dictionary`
--

CREATE TABLE IF NOT EXISTS `dictionary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
  
ALTER TABLE `map_node_counters` ADD `display` INT NOT NULL DEFAULT '1';

ALTER TABLE `users` ADD `resetHashKey` VARCHAR( 255 ) NULL ,
ADD `resetHashKeyTime` DATETIME NULL ,
ADD `resetAttempt` INT NULL ,
ADD `resetTimestamp` DATETIME NULL;


ALTER TABLE `map_skins` ADD `user_id` INT NULL AFTER `path`
ALTER TABLE `map_skins` ADD `enabled` TINYINT( 1 ) NOT NULL DEFAULT '1';

UPDATE `map_securities` SET `name` = 'keys (a key is required to access this Labyrinth)' WHERE `id` = 4 LIMIT 1 ;

CREATE TABLE IF NOT EXISTS `map_counter_common_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `rule` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`)
) ENGINE=InnoDB ;

--
-- Constraints for table `map_counter_common_rules`
--
ALTER TABLE `map_counter_common_rules`
  ADD CONSTRAINT `map_counter_common_rules_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `map_counters` CHANGE `start_value` `start_value` DOUBLE NOT NULL DEFAULT '0';

ALTER TABLE `map_counter_rules` CHANGE `value` `value` DOUBLE NOT NULL DEFAULT '0';


CREATE TABLE IF NOT EXISTS `metadata` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `comment` varchar(500) NOT NULL,
  `cardinality` varchar(10) NOT NULL DEFAULT '1',
  `options` varchar(5000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

INSERT INTO `metadata` (`id`, `name`, `model`, `type`, `label`, `comment`, `cardinality`, `options`) VALUES
(1, 'disciplines', 'map', 'skosrecord', 'Scientific Disciplines', 'The MESH occupations that are involved in the case', 'n', '{"source":"rdf\\/occupations.rdf"}'),
(2, 'license', 'map', 'referencerecord', 'License', 'The labyrinth''s usage rights', '1', '{"source":"rdf\\/licenses.rdf","type":"http:\\/\\/purl.org\\/meducator\\/ns\\/IPRType","label":"http:\\/\\/www.w3.org\\/2000\\/01\\/rdf-schema#label"}'),
(3, 'creationDate', 'map', 'daterecord', 'Date Created', 'The date this case was created', '1', ''),
(4, 'assessment', 'map', 'textrecord', 'Assessment Methods', 'Assessment Methods', 'n', ''),
(5, 'learningObjectives', 'map', 'stringrecord', 'Learning Objectives', 'The anticipated learning objectives', 'n', ''),
(6, 'educationalOutcomes', 'map', 'skosrecord', 'Educational Outcomes', 'The general educational outcomes of the case', 'n', '{"source":"rdf\\/educationalOutcome.rdf"}'),
(7, 'educationalLevel', 'map', 'skosrecord', 'Educational Level', 'The expected educational level of the case''s audience', '1', '{"source":"rdf\\/educationalLevel.rdf"}'),
(8, 'language', 'map', 'referencerecord', 'Language', 'The language in which the content is expressed', '1', '{"source":"rdf\\/languages.rdf","type":"http:\\/\\/downlode.org\\/rdf\\/iso-639\\/schema#Language","label":"http:\\/\\/downlode.org\\/rdf\\/iso-639\\/schema#name_en"}');


CREATE TABLE IF NOT EXISTS `metadata_date_fields` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `field_id` int(10) NOT NULL,
  `object_id` int(10) NOT NULL,
  `value` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;



CREATE TABLE IF NOT EXISTS `metadata_inlineobject_fields` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `field_id` int(10) NOT NULL,
  `object_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;



CREATE TABLE IF NOT EXISTS `metadata_list_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  ;



CREATE TABLE IF NOT EXISTS `metadata_list_fields_terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `value` varchar(2000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  ;



CREATE TABLE IF NOT EXISTS `metadata_ref_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `uri` varchar(2000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;



CREATE TABLE IF NOT EXISTS `metadata_skos_fields` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `field_id` int(10) NOT NULL,
  `object_id` int(10) NOT NULL,
  `uri` varchar(2000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `metadata_string_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `value` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;



CREATE TABLE IF NOT EXISTS `metadata_text_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8  ;



CREATE TABLE IF NOT EXISTS `rdf_mappings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `metadata_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

INSERT INTO `rdf_mappings` (`id`, `metadata_id`, `term_id`, `type`) VALUES
(1, 7, 30, 'rel'),
(2, 1, 220, 'rel'),
(3, 2, 16, 'rel'),
(4, 3, 17, 'property'),
(5, 4, 21, 'property'),
(6, 5, 23, 'property'),
(7, 6, 22, 'rel'),
(8, 7, 30, 'rel'),
(9, 8, 15, 'rel');


CREATE TABLE IF NOT EXISTS `rdf_mappings_classes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `class` varchar(100) NOT NULL,
  `term_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

INSERT INTO `rdf_mappings_classes` (`id`, `class`, `term_id`) VALUES
(1, 'user', 51),
(2, 'map', 55),
(3, 'map_node', 305),
(4, 'map', 314),
(5, 'map_node_link', 319),
(6, 'user_session', 483),
(7, 'user_sessiontrace', 460);

CREATE TABLE IF NOT EXISTS `rdf_mappings_legacy_properties` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `class` varchar(100) NOT NULL,
  `property` varchar(100) NOT NULL,
  `term_id` int(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `rdf_mappings_legacy_properties` (`id`, `class`, `property`, `term_id`, `type`) VALUES
(1, 'map_node', 'map', 199, 'rel'),
(2, 'user', 'nickname', 74, 'property'),
(3, 'map_node', 'links', 335, 'rel'),
(4, 'map_node_link', 'node_1', 336, 'rel'),
(5, 'map_node_link', 'node_2', 308, 'rel'),
(6, 'user_session', 'user', 350, 'rel'),
(7, 'user', 'language', 104, 'property'),
(8, 'user', 'groups', 123, 'rel'),
(9, 'user', 'email', 66, 'property'),
(10, 'map', 'author', 12, 'rel'),
(11, 'map', 'nodes', 311, 'rel'),
(12, 'map', 'contributors', 181, 'rel'),
(13, 'map_node_link', 'map', 331, 'rev'),
(14, 'map_node', 'title', 223, 'property'),
(15, 'map_node', 'text', 46, 'property'),
(16, 'map', 'name', 223, 'property'),
(17, 'user_session', 'traces', 362, 'rel'),
(18, 'user_sessiontrace', 'node', 355, 'rel'),
(19, 'user_sessiontrace', 'date_stamp', 185, 'property');

CREATE TABLE IF NOT EXISTS `rdf_terms` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `vocab_id` int(10) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(2000) NOT NULL,
  `term_label` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vocab_id` (`vocab_id`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `rdf_terms` (`id`, `vocab_id`, `name`, `type`, `term_label`) VALUES
(1, 1, 'Resource', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Resource'),
(2, 1, 'IPRType', 'http://www.w3.org/2000/01/rdf-schema#Class', 'IPR License '),
(3, 1, 'RepurposingHistory', 'http://www.w3.org/2000/01/rdf-schema#Class', ' Repurposing History'),
(4, 1, 'RepurposingChild', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Repurposing Child'),
(5, 1, 'RepurposedTo', 'http://www.w3.org/2000/01/rdf-schema#Class', 'repurposed To'),
(6, 1, 'RepurposingParent', 'http://www.w3.org/2000/01/rdf-schema#Class', ' Repurposing Parent'),
(7, 1, 'RepurposedFrom', 'http://www.w3.org/2000/01/rdf-schema#Class', ' repurposed From'),
(8, 1, 'Subject', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Subject'),
(9, 1, 'Discipline', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Discipline'),
(10, 1, 'DisciplineSpeciality', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Discipline Speciality'),
(11, 1, 'ExternalTerm', 'http://www.w3.org/2000/01/rdf-schema#Class', 'External Term'),
(12, 1, 'creator', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Creator '),
(13, 1, 'identifier', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Identifier'),
(14, 1, 'description', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Description'),
(15, 1, 'language', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Language'),
(16, 1, 'rights', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Rights'),
(17, 1, 'created', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Created'),
(18, 1, 'metadataCreated', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Metadata Creation Date'),
(19, 1, 'memberOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Member Of'),
(20, 1, 'profileURI', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Profile URI'),
(21, 1, 'assessmentMethods', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Assessment Methods'),
(22, 1, 'educationalOutcomes', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Educational Outcomes'),
(23, 1, 'educationalObjectives', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Educational Objectives'),
(24, 1, 'teachingLearningInstructions', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Teaching Learning Instructions '),
(25, 1, 'citation', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Citation'),
(26, 1, 'externalSource', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'External Source'),
(27, 1, 'conceptID', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Concept ID'),
(28, 1, 'educationalPrerequisites', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Educational Prerequisites'),
(29, 1, 'educationalContext', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Educational Context'),
(30, 1, 'educationalLevel', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Educational Level'),
(31, 1, 'title', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'A descriptive (short) title of the resource'),
(32, 1, 'hasRepurposingHistory', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'The history of the repurposing process '),
(33, 1, 'isAccompaniedBy', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Is Accompanied By'),
(34, 1, 'repurposingContext', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Repurposing Context'),
(35, 1, 'resourceType', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Resource Type'),
(36, 1, 'mediaType', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Media Type'),
(37, 1, 'technicalDescription', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Technical Description'),
(38, 1, 'metadataCreator', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Metadata Creator'),
(39, 1, 'metadataLanguage', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Metadata Language'),
(40, 1, 'quality', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Quality'),
(41, 1, 'repurposingRelation', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Repurposing Relation'),
(42, 1, 'repurposingDescription', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Repurposing Description'),
(43, 2, 'assurance', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'assurance'),
(44, 2, 'src_assurance', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'src_assurance'),
(45, 3, 'term_status', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'term_status'),
(46, 4, 'description', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'description'),
(47, 4, 'title', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'title'),
(48, 4, 'date', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'date'),
(49, 5, 'Class', 'http://www.w3.org/2002/07/owl#Class', 'Class'),
(50, 6, 'LabelProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Label Property'),
(51, 6, 'Person', 'http://www.w3.org/2002/07/owl#Class', 'Person'),
(52, 6, 'Agent', 'http://www.w3.org/2002/07/owl#Class', 'Agent'),
(53, 7, 'Person', 'http://www.w3.org/2002/07/owl#Class', 'Person'),
(54, 8, 'SpatialThing', 'http://www.w3.org/2002/07/owl#Class', 'Spatial Thing'),
(55, 6, 'Document', 'http://www.w3.org/2002/07/owl#Class', 'Document'),
(56, 6, 'Organization', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Organization'),
(57, 6, 'Group', 'http://www.w3.org/2002/07/owl#Class', 'Group'),
(58, 6, 'Project', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Project'),
(59, 6, 'Image', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Image'),
(60, 6, 'PersonalProfileDocument', 'http://www.w3.org/2000/01/rdf-schema#Class', 'PersonalProfileDocument'),
(61, 6, 'OnlineAccount', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Online Account'),
(62, 9, 'Thing', 'http://www.w3.org/2002/07/owl#Class', 'Thing'),
(63, 6, 'OnlineGamingAccount', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Online Gaming Account'),
(64, 6, 'OnlineEcommerceAccount', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Online E-commerce Account'),
(65, 6, 'OnlineChatAccount', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Online Chat Account'),
(66, 6, 'mbox', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'personal mailbox'),
(67, 6, 'mbox_sha1sum', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'sha1sum of a personal mailbox URI name'),
(68, 6, 'gender', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'gender'),
(69, 6, 'geekcode', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'geekcode'),
(70, 6, 'dnaChecksum', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'DNA checksum'),
(71, 6, 'sha1', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'sha1sum (hex)'),
(72, 6, 'based_near', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'based near'),
(73, 6, 'title', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'title'),
(74, 6, 'nick', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'nickname'),
(75, 6, 'jabberID', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'jabber ID'),
(76, 6, 'aimChatID', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'AIM chat ID'),
(77, 6, 'skypeID', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Skype ID'),
(78, 6, 'icqChatID', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'ICQ chat ID'),
(79, 6, 'yahooChatID', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Yahoo chat ID'),
(80, 6, 'msnChatID', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'MSN chat ID'),
(81, 6, 'name', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'name'),
(82, 6, 'firstName', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'firstName'),
(83, 6, 'lastName', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'lastName'),
(84, 6, 'givenName', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Given name'),
(85, 6, 'surname', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Surname'),
(86, 6, 'family_name', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'family_name'),
(87, 6, 'familyName', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'familyName'),
(88, 6, 'phone', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'phone'),
(89, 6, 'homepage', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'homepage'),
(90, 6, 'weblog', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'weblog'),
(91, 6, 'openid', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'openid'),
(92, 6, 'tipjar', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'tipjar'),
(93, 6, 'plan', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'plan'),
(94, 6, 'made', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'made'),
(95, 6, 'maker', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'maker'),
(96, 6, 'img', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'image'),
(97, 6, 'depiction', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'depiction'),
(98, 6, 'depicts', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'depicts'),
(99, 6, 'thumbnail', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'thumbnail'),
(100, 6, 'myersBriggs', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'myersBriggs'),
(101, 6, 'workplaceHomepage', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'workplace homepage'),
(102, 6, 'workInfoHomepage', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'work info homepage'),
(103, 6, 'schoolHomepage', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'schoolHomepage'),
(104, 6, 'knows', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'knows'),
(105, 6, 'interest', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'interest'),
(106, 6, 'topic_interest', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'topic_interest'),
(107, 6, 'publications', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'publications'),
(108, 6, 'currentProject', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'current project'),
(109, 6, 'pastProject', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'past project'),
(110, 6, 'fundedBy', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'funded by'),
(111, 6, 'logo', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'logo'),
(112, 6, 'topic', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'topic'),
(113, 6, 'primaryTopic', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'primary topic'),
(114, 6, 'focus', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'focus'),
(115, 10, 'Concept', '[NULL]', 'Concept'),
(116, 6, 'isPrimaryTopicOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'is primary topic of'),
(117, 6, 'page', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'page'),
(118, 6, 'theme', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'theme'),
(119, 6, 'account', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'account'),
(120, 6, 'holdsAccount', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'account'),
(121, 6, 'accountServiceHomepage', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'account service homepage'),
(122, 6, 'accountName', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'account name'),
(123, 6, 'member', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'member'),
(124, 6, 'membershipClass', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'membershipClass'),
(125, 6, 'birthday', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'birthday'),
(126, 6, 'age', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'age'),
(127, 6, 'status', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'status'),
(128, 11, 'Agent', 'http://purl.org/dc/terms/AgentClass', 'Agent'),
(129, 11, 'AgentClass', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Agent Class'),
(130, 11, 'BibliographicResource', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Bibliographic Resource'),
(131, 11, 'Box', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'DCMI Box'),
(132, 11, 'DCMIType', 'http://purl.org/dc/dcam/VocabularyEncodingScheme', 'DCMI Type Vocabulary'),
(133, 11, 'DDC', 'http://purl.org/dc/dcam/VocabularyEncodingScheme', 'DDC'),
(134, 11, 'FileFormat', 'http://www.w3.org/2000/01/rdf-schema#Class', 'File Format'),
(135, 11, 'Frequency', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Frequency'),
(136, 11, 'IMT', 'http://purl.org/dc/dcam/VocabularyEncodingScheme', 'IMT'),
(137, 11, 'ISO3166', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'ISO 3166'),
(138, 11, 'ISO639-2', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'ISO 639-2'),
(139, 11, 'ISO639-3', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'ISO 639-3'),
(140, 11, 'Jurisdiction', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Jurisdiction'),
(141, 11, 'LCC', 'http://purl.org/dc/dcam/VocabularyEncodingScheme', 'LCC'),
(142, 11, 'LCSH', 'http://purl.org/dc/dcam/VocabularyEncodingScheme', 'LCSH'),
(143, 11, 'LicenseDocument', 'http://www.w3.org/2000/01/rdf-schema#Class', 'License Document'),
(144, 11, 'LinguisticSystem', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Linguistic System'),
(145, 11, 'Location', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Location'),
(146, 11, 'LocationPeriodOrJurisdiction', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Location, Period, or Jurisdiction'),
(147, 11, 'MESH', 'http://purl.org/dc/dcam/VocabularyEncodingScheme', 'MeSH'),
(148, 11, 'MediaType', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Media Type'),
(149, 11, 'MediaTypeOrExtent', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Media Type or Extent'),
(150, 11, 'MethodOfAccrual', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Method of Accrual'),
(151, 11, 'MethodOfInstruction', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Method of Instruction'),
(152, 11, 'NLM', 'http://purl.org/dc/dcam/VocabularyEncodingScheme', 'NLM'),
(153, 11, 'Period', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'DCMI Period'),
(154, 11, 'PeriodOfTime', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Period of Time'),
(155, 11, 'PhysicalMedium', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Physical Medium'),
(156, 11, 'PhysicalResource', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Physical Resource'),
(157, 11, 'Point', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'DCMI Point'),
(158, 11, 'Policy', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Policy'),
(159, 11, 'ProvenanceStatement', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Provenance Statement'),
(160, 11, 'RFC1766', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'RFC 1766'),
(161, 11, 'RFC3066', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'RFC 3066'),
(162, 11, 'RFC4646', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'RFC 4646'),
(163, 11, 'RFC5646', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'RFC 5646'),
(164, 11, 'RightsStatement', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Rights Statement'),
(165, 11, 'SizeOrDuration', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Size or Duration'),
(166, 11, 'Standard', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Standard'),
(167, 11, 'TGN', 'http://purl.org/dc/dcam/VocabularyEncodingScheme', 'TGN'),
(168, 11, 'UDC', 'http://purl.org/dc/dcam/VocabularyEncodingScheme', 'UDC'),
(169, 11, 'URI', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'URI'),
(170, 11, 'W3CDTF', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'W3C-DTF'),
(171, 11, 'abstract', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Abstract'),
(172, 11, 'accessRights', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Access Rights'),
(173, 11, 'accrualMethod', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Accrual Method'),
(174, 11, 'accrualPeriodicity', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Accrual Periodicity'),
(175, 11, 'accrualPolicy', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Accrual Policy'),
(176, 11, 'alternative', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Alternative Title'),
(177, 11, 'audience', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Audience'),
(178, 11, 'available', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Date Available'),
(179, 11, 'bibliographicCitation', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Bibliographic Citation'),
(180, 11, 'conformsTo', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Conforms To'),
(181, 11, 'contributor', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Contributor'),
(182, 11, 'coverage', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Coverage'),
(183, 11, 'created', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Date Created'),
(184, 11, 'creator', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Creator'),
(185, 11, 'date', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Date'),
(186, 11, 'dateAccepted', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Date Accepted'),
(187, 11, 'dateCopyrighted', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Date Copyrighted'),
(188, 11, 'dateSubmitted', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Date Submitted'),
(189, 11, 'description', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Description'),
(190, 11, 'educationLevel', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Audience Education Level'),
(191, 11, 'extent', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Extent'),
(192, 11, 'format', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Format'),
(193, 11, 'hasFormat', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Has Format'),
(194, 11, 'hasPart', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Has Part'),
(195, 11, 'hasVersion', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Has Version'),
(196, 11, 'identifier', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Identifier'),
(197, 11, 'instructionalMethod', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Instructional Method'),
(198, 11, 'isFormatOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Is Format Of'),
(199, 11, 'isPartOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Is Part Of'),
(200, 11, 'isReferencedBy', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Is Referenced By'),
(201, 11, 'isReplacedBy', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Is Replaced By'),
(202, 11, 'isRequiredBy', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Is Required By'),
(203, 11, 'isVersionOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Is Version Of'),
(204, 11, 'issued', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Date Issued'),
(205, 11, 'language', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Language'),
(206, 11, 'license', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'License'),
(207, 11, 'mediator', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Mediator'),
(208, 11, 'medium', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Medium'),
(209, 11, 'modified', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Date Modified'),
(210, 11, 'provenance', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Provenance'),
(211, 11, 'publisher', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Publisher'),
(212, 11, 'references', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'References'),
(213, 11, 'relation', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Relation'),
(214, 11, 'replaces', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Replaces'),
(215, 11, 'requires', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Requires'),
(216, 11, 'rights', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Rights'),
(217, 11, 'rightsHolder', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Rights Holder'),
(218, 11, 'source', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Source'),
(219, 11, 'spatial', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Spatial Coverage'),
(220, 11, 'subject', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Subject'),
(221, 11, 'tableOfContents', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Table Of Contents'),
(222, 11, 'temporal', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Temporal Coverage'),
(223, 11, 'title', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Title'),
(224, 11, 'type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Type'),
(225, 11, 'valid', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'Date Valid'),
(226, 12, 'owl', 'http://www.w3.org/2002/07/owl#Ontology', 'The OWL 2 Schema vocabulary (OWL 2)'),
(227, 9, 'AllDifferent', 'http://www.w3.org/2000/01/rdf-schema#Class', 'AllDifferent'),
(228, 9, 'AllDisjointClasses', 'http://www.w3.org/2000/01/rdf-schema#Class', 'AllDisjointClasses'),
(229, 9, 'AllDisjointProperties', 'http://www.w3.org/2000/01/rdf-schema#Class', 'AllDisjointProperties'),
(230, 9, 'Annotation', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Annotation'),
(231, 9, 'AnnotationProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'AnnotationProperty'),
(232, 9, 'AsymmetricProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'AsymmetricProperty'),
(233, 9, 'Axiom', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Axiom'),
(234, 9, 'Class', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Class'),
(235, 9, 'DataRange', 'http://www.w3.org/2000/01/rdf-schema#Class', 'DataRange'),
(236, 9, 'DatatypeProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'DatatypeProperty'),
(237, 9, 'DeprecatedClass', 'http://www.w3.org/2000/01/rdf-schema#Class', 'DeprecatedClass'),
(238, 9, 'DeprecatedProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'DeprecatedProperty'),
(239, 9, 'FunctionalProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'FunctionalProperty'),
(240, 9, 'InverseFunctionalProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'InverseFunctionalProperty'),
(241, 9, 'IrreflexiveProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'IrreflexiveProperty'),
(242, 9, 'NamedIndividual', 'http://www.w3.org/2000/01/rdf-schema#Class', 'NamedIndividual'),
(243, 9, 'NegativePropertyAssertion', 'http://www.w3.org/2000/01/rdf-schema#Class', 'NegativePropertyAssertion'),
(244, 9, 'Nothing', 'http://www.w3.org/2002/07/owl#Class', 'Nothing'),
(245, 9, 'ObjectProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'ObjectProperty'),
(246, 9, 'Ontology', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Ontology'),
(247, 9, 'OntologyProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'OntologyProperty'),
(248, 9, 'ReflexiveProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'ReflexiveProperty'),
(249, 9, 'Restriction', 'http://www.w3.org/2000/01/rdf-schema#Class', 'Restriction'),
(250, 9, 'SymmetricProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'SymmetricProperty'),
(251, 9, 'TransitiveProperty', 'http://www.w3.org/2000/01/rdf-schema#Class', 'TransitiveProperty'),
(252, 9, 'allValuesFrom', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'allValuesFrom'),
(253, 9, 'annotatedProperty', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'annotatedProperty'),
(254, 9, 'annotatedSource', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'annotatedSource'),
(255, 9, 'annotatedTarget', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'annotatedTarget'),
(256, 9, 'assertionProperty', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'assertionProperty'),
(257, 9, 'backwardCompatibleWith', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'backwardCompatibleWith'),
(258, 9, 'bottomDataProperty', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'bottomDataProperty'),
(259, 9, 'bottomObjectProperty', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'bottomObjectProperty'),
(260, 9, 'cardinality', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'cardinality'),
(261, 9, 'complementOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'complementOf'),
(262, 9, 'datatypeComplementOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'datatypeComplementOf'),
(263, 9, 'deprecated', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'deprecated'),
(264, 9, 'differentFrom', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'differentFrom'),
(265, 9, 'disjointUnionOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'disjointUnionOf'),
(266, 9, 'disjointWith', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'disjointWith'),
(267, 9, 'distinctMembers', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'distinctMembers'),
(268, 9, 'equivalentClass', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'equivalentClass'),
(269, 9, 'equivalentProperty', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'equivalentProperty'),
(270, 9, 'hasKey', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'hasKey'),
(271, 9, 'hasSelf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'hasSelf'),
(272, 9, 'hasValue', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'hasValue'),
(273, 9, 'imports', 'http://www.w3.org/2002/07/owl#OntologyProperty', 'imports'),
(274, 9, 'incompatibleWith', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'incompatibleWith'),
(275, 9, 'intersectionOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'intersectionOf'),
(276, 9, 'inverseOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'inverseOf'),
(277, 9, 'maxCardinality', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'maxCardinality'),
(278, 9, 'maxQualifiedCardinality', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'maxQualifiedCardinality'),
(279, 9, 'members', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'members'),
(280, 9, 'minCardinality', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'minCardinality'),
(281, 9, 'minQualifiedCardinality', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'minQualifiedCardinality'),
(282, 9, 'onClass', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'onClass'),
(283, 9, 'onDataRange', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'onDataRange'),
(284, 9, 'onDatatype', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'onDatatype'),
(285, 9, 'oneOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'oneOf'),
(286, 9, 'onProperties', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'onProperties'),
(287, 9, 'onProperty', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'onProperty'),
(288, 9, 'priorVersion', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'priorVersion'),
(289, 9, 'propertyChainAxiom', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'propertyChainAxiom'),
(290, 9, 'propertyDisjointWith', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'propertyDisjointWith'),
(291, 9, 'qualifiedCardinality', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'qualifiedCardinality'),
(292, 9, 'sameAs', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'sameAs'),
(293, 9, 'someValuesFrom', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'someValuesFrom'),
(294, 9, 'sourceIndividual', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'sourceIndividual'),
(295, 9, 'targetIndividual', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'targetIndividual'),
(296, 9, 'targetValue', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'targetValue'),
(297, 9, 'topDataProperty', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'topDataProperty'),
(298, 9, 'topObjectProperty', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'topObjectProperty'),
(299, 9, 'unionOf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'unionOf'),
(300, 9, 'versionInfo', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'versionInfo'),
(301, 9, 'versionIRI', 'http://www.w3.org/2002/07/owl#OntologyProperty', 'versionIRI'),
(302, 9, 'withRestrictions', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', 'withRestrictions'),
(303, 13, 'Networks.owl', 'http://www.w3.org/2002/07/owl#Ontology', 'Networks.owl'),
(304, 14, 'DirectedNode', 'http://www.w3.org/2002/07/owl#Class', 'DirectedNode'),
(305, 14, 'Node', 'http://www.w3.org/2002/07/owl#Class', 'Node'),
(307, 14, 'DirectedLink', 'http://www.w3.org/2002/07/owl#Class', 'DirectedLink'),
(308, 14, 'linkedTo', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'linkedTo'),
(309, 14, 'DirectedNetwork', 'http://www.w3.org/2002/07/owl#Class', 'DirectedNetwork'),
(311, 14, 'hasNode', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'hasNode'),
(313, 14, 'hasLink', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'hasLink'),
(314, 14, 'Network', 'http://www.w3.org/2002/07/owl#Class', 'Network'),
(316, 14, 'originNode', 'http://www.w3.org/2002/07/owl#FunctionalProperty', 'originNode'),
(318, 14, 'destinationNode', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'destinationNode'),
(319, 14, 'Link', 'http://www.w3.org/2002/07/owl#Class', 'Link'),
(320, 14, 'UndirectedNetwork', 'http://www.w3.org/2002/07/owl#Class', 'UndirectedNetwork'),
(322, 14, 'UndirectedNode', 'http://www.w3.org/2002/07/owl#Class', 'UndirectedNode'),
(324, 14, 'UndirectedLink', 'http://www.w3.org/2002/07/owl#Class', 'UndirectedLink'),
(325, 14, 'NodeTrait', 'http://www.w3.org/2002/07/owl#Class', 'NodeTrait'),
(326, 14, 'NetworkThing', 'http://www.w3.org/2002/07/owl#Class', 'NetworkThing'),
(328, 14, 'linkedNode', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'linkedNode'),
(329, 14, 'NetworkTrait', 'http://www.w3.org/2002/07/owl#Class', 'NetworkTrait'),
(330, 14, 'LinkTrait', 'http://www.w3.org/2002/07/owl#Class', 'LinkTrait'),
(331, 14, 'networkOf', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'networkOf'),
(335, 14, 'hasConnections', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'hasConnections'),
(336, 14, 'linkedFrom', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'linkedFrom'),
(337, 14, 'LinkCertainty', 'http://wow.sfsu.edu/ontology/rich/Networks.owl#LinkTrait', 'certainty'),
(338, 16, 'ns', 'http://www.w3.org/2002/07/owl#Ontology', 'Activities Ontology'),
(339, 4, 'creator', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'creator'),
(340, 5, 'label', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'label'),
(341, 5, 'comment', 'http://www.w3.org/2002/07/owl#AnnotationProperty', 'comment'),
(342, 17, 'loggingStateType', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'loggingStateType'),
(346, 17, 'subscriptionStateType', 'http://www.w3.org/2000/01/rdf-schema#Datatype', 'subscriptionStateType'),
(350, 17, 'achievedBy', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'achieved by'),
(351, 17, 'achievement', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'achievement'),
(352, 17, 'addedTo', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'addedTo'),
(353, 17, 'annotationRef', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'annotationRef'),
(354, 17, 'collaborationMode', 'http://www.w3.org/2002/07/owl#Class', 'CollaborationMode'),
(355, 17, 'contentRef', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'contentRef'),
(356, 17, 'detachedFrom', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'detached from'),
(357, 17, 'exchangedMessage', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'exchangedMessage'),
(361, 17, 'hasAttachment', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'hasAttachment'),
(362, 17, 'hasEvent', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'hasEvent'),
(363, 17, 'helpForResource', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'help for resource'),
(364, 17, 'helpRequest', 'http://www.w3.org/2002/07/owl#Class', 'Help Request'),
(365, 17, 'inChannel', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'inChannel'),
(366, 17, 'joinedGroup', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'joined group'),
(367, 17, 'leftGroup', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'left group'),
(368, 17, 'occursIn', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'The online environment where the activity has taken place'),
(369, 17, 'participant', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'participant'),
(370, 17, 'performedBy', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'performedBy'),
(371, 17, 'progressScaleUsed', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'progressScaleUsed'),
(372, 17, 'quizResult', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'quizResult'),
(373, 17, 'recommendedBy', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'recommended by'),
(374, 17, 'recommendedResource', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'recommended resource'),
(375, 17, 'recommendedTo', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'recommended to'),
(376, 17, 'relatedToGoal', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'related to goal'),
(377, 17, 'resourceRef', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'resourceRef'),
(378, 17, 'responseToRequest', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'response to request'),
(379, 17, 'rootPosting', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'root post'),
(380, 17, 'sentBy', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'sent by'),
(381, 17, 'sharedResource', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'shared resource'),
(382, 17, 'sharedWith', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'shared with'),
(386, 17, 'startedFollowing', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'started following'),
(387, 17, 'stoppedFollowing', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'stopped following'),
(388, 17, 'submittedBy', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'submitted by'),
(392, 17, 'usingChannel', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'usingChannel'),
(393, 17, 'visibilitySetTo', 'http://www.w3.org/2002/07/owl#ObjectProperty', 'visibility set to'),
(394, 17, 'active', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'active'),
(395, 17, 'browser', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'browser'),
(396, 17, 'browserVersion', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'browserVersion'),
(397, 17, 'dateTimeEnd', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'dateTimeEnd'),
(398, 17, 'dateTimeSent', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'dateTimeSent'),
(399, 17, 'dateTimeStart', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'dateTimeStart'),
(400, 17, 'href', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'href'),
(401, 17, 'loggingState', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'loggingState'),
(402, 17, 'newProgressValue', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'newProgressValue'),
(403, 17, 'os', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'OS'),
(404, 17, 'query', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'query'),
(405, 17, 'requestTxt', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'request text'),
(406, 17, 'screenHeight', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'screenHeight'),
(407, 17, 'screenWidth', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'screenWidth'),
(408, 17, 'subscriptionState', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'subscriptionState'),
(409, 17, 'timestamp', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'timestamp'),
(410, 17, 'viewportHeight', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'viewportHeight'),
(411, 17, 'viewportWidth', 'http://www.w3.org/2002/07/owl#DatatypeProperty', 'viewportWidth'),
(412, 19, 'ProgressScale', 'http://www.w3.org/2002/07/owl#Class', 'ProgressScale'),
(413, 20, 'ContentUnit', 'http://www.w3.org/2002/07/owl#Class', 'ContentUnit'),
(414, 21, 'Forum', 'http://www.w3.org/2002/07/owl#Class', 'Forum'),
(415, 21, 'Post', 'http://www.w3.org/2002/07/owl#Class', 'Post'),
(416, 17, 'RecognitionEvent', 'http://www.w3.org/2002/07/owl#Class', 'Recognition Event'),
(417, 17, 'AcceptRecommendationEvent', 'http://www.w3.org/2002/07/owl#Class', 'AcceptRecommendationEvent'),
(418, 17, 'AchievementEvent', 'http://www.w3.org/2002/07/owl#Class', 'AchievementEvent'),
(419, 17, 'Activity', 'http://www.w3.org/2002/07/owl#Class', 'Activity'),
(420, 17, 'Add', 'http://www.w3.org/2002/07/owl#Class', 'Add'),
(421, 17, 'AddAnnotationEvent', 'http://www.w3.org/2002/07/owl#Class', 'AddAnnotationEvent'),
(422, 17, 'AnnotationEvent', 'http://www.w3.org/2002/07/owl#Class', 'AnnotationEvent'),
(423, 17, 'AssessmentEvent', 'http://www.w3.org/2002/07/owl#Class', 'Assessment Event'),
(424, 17, 'Authoring', 'http://www.w3.org/2002/07/owl#Class', 'Authoring'),
(425, 17, 'BookmarkEvent', 'http://www.w3.org/2002/07/owl#Class', 'Bookmark Event'),
(426, 17, 'ChangeProgressEvent', 'http://www.w3.org/2002/07/owl#Class', 'ChangeProgressEvent'),
(427, 17, 'Channel', 'http://www.w3.org/2002/07/owl#Class', 'Channel'),
(428, 17, 'ChatMessage', 'http://www.w3.org/2002/07/owl#Class', 'ChatMessage'),
(430, 17, 'ChatRoom', 'http://www.w3.org/2002/07/owl#Class', 'chatroom'),
(432, 17, 'Click', 'http://www.w3.org/2002/07/owl#Class', 'Click'),
(437, 17, 'Create', 'http://www.w3.org/2002/07/owl#Class', 'Create'),
(438, 17, 'Delete', 'http://www.w3.org/2002/07/owl#Class', 'Delete'),
(439, 17, 'DetachEvent', 'http://www.w3.org/2002/07/owl#Class', 'Detach Event'),
(440, 17, 'DiscussAsynchronously', 'http://www.w3.org/2002/07/owl#Class', 'DiscussAsynchronously'),
(449, 17, 'DiscussSynchronously', 'http://www.w3.org/2002/07/owl#Class', 'DiscussSynchronously'),
(452, 17, 'Discussing', 'http://www.w3.org/2002/07/owl#Class', 'Discussing'),
(454, 17, 'DiscussionEvent', 'http://www.w3.org/2002/07/owl#Class', 'DiscussionEvent'),
(456, 17, 'DiscussionForum', 'http://www.w3.org/2002/07/owl#Class', 'Discussion Forum'),
(458, 17, 'Edit', 'http://www.w3.org/2002/07/owl#Class', 'Edit'),
(459, 17, 'Environment', 'http://www.w3.org/2002/07/owl#Class', 'Environment'),
(460, 17, 'Event', 'http://www.w3.org/2002/07/owl#Class', 'Event'),
(461, 17, 'FlaggingMsg', 'http://www.w3.org/2002/07/owl#Class', 'FlaggingMsg'),
(462, 17, 'FollowEvent', 'http://www.w3.org/2002/07/owl#Class', 'FollowEvent'),
(463, 17, 'FollowedLinkEvent', 'http://www.w3.org/2002/07/owl#Class', 'FollowedLinkEvent'),
(464, 17, 'ForumPost', 'http://www.w3.org/2002/07/owl#Class', 'Post'),
(466, 17, 'HelpRequestEvent', 'http://www.w3.org/2002/07/owl#Class', 'Help Request Event'),
(467, 17, 'HelpResponse', 'http://www.w3.org/2002/07/owl#Class', 'Help Response'),
(468, 17, 'IgnoreRecommendationEvent', 'http://www.w3.org/2002/07/owl#Class', 'IgnoreRecommendationEvent'),
(469, 17, 'ImportEvent', 'http://www.w3.org/2002/07/owl#Class', 'Import Event'),
(470, 17, 'InterpretedActivity', 'http://www.w3.org/2002/07/owl#Class', 'InterpretedActivity'),
(471, 17, 'JoinGroupEvent', 'http://www.w3.org/2002/07/owl#Class', 'Join Group Event'),
(472, 17, 'LCMS', 'http://www.w3.org/2002/07/owl#Class', 'Learning Content Management System'),
(473, 17, 'LeaveGroupEvent', 'http://www.w3.org/2002/07/owl#Class', 'Leave Group Event'),
(474, 17, 'LikeEvent', 'http://www.w3.org/2002/07/owl#Class', 'LikeEvent'),
(475, 17, 'Listening', 'http://www.w3.org/2002/07/owl#Class', 'Listening'),
(476, 17, 'Logging', 'http://www.w3.org/2002/07/owl#Class', 'Logging'),
(477, 17, 'MarkAsFavourite', 'http://www.w3.org/2002/07/owl#Class', 'Mark as Favourite'),
(478, 17, 'Message', 'http://www.w3.org/2002/07/owl#Class', 'Message'),
(479, 17, 'Microblog', 'http://www.w3.org/2002/07/owl#Class', 'Microblog'),
(481, 17, 'MicroblogPost', 'http://www.w3.org/2002/07/owl#Class', 'MicroblogPost'),
(483, 17, 'MonitoredActivity', 'http://www.w3.org/2002/07/owl#Class', 'MonitoredActivity'),
(484, 17, 'MonitoredEvent', 'http://www.w3.org/2002/07/owl#Class', 'MonitoredEvent'),
(485, 17, 'Quizzing', 'http://www.w3.org/2002/07/owl#Class', 'Quizzing'),
(486, 17, 'Reading', 'http://www.w3.org/2002/07/owl#Class', 'Reading'),
(491, 17, 'ReadingMsg', 'http://www.w3.org/2002/07/owl#Class', 'ReadingMsg'),
(492, 17, 'Recommendation', 'http://www.w3.org/2002/07/owl#Class', 'Recommendation'),
(493, 17, 'RecommendationEvent', 'http://www.w3.org/2002/07/owl#Class', 'Recommendation Event'),
(494, 17, 'Restore', 'http://www.w3.org/2002/07/owl#Class', 'Restore'),
(495, 17, 'Search', 'http://www.w3.org/2002/07/owl#Class', 'Search'),
(496, 17, 'Select', 'http://www.w3.org/2002/07/owl#Class', 'Select'),
(497, 17, 'SetVisibilityEvent', 'http://www.w3.org/2002/07/owl#Class', 'Set visibility event'),
(498, 17, 'SharingEvent', 'http://www.w3.org/2002/07/owl#Class', 'Sharing Event'),
(499, 17, 'Submit', 'http://www.w3.org/2002/07/owl#Class', 'Submit'),
(500, 17, 'SubmittingMsg', 'http://www.w3.org/2002/07/owl#Class', 'SubmittingMsg'),
(501, 17, 'SubscribeToChanges', 'http://www.w3.org/2002/07/owl#Class', 'SubscribeToChanges'),
(502, 17, 'TaggingEvent', 'http://www.w3.org/2002/07/owl#Class', 'TaggingEvent'),
(503, 17, 'UnfollowEvent', 'http://www.w3.org/2002/07/owl#Class', 'Unfollow Event'),
(504, 17, 'Upload', 'http://www.w3.org/2002/07/owl#Class', 'Upload'),
(505, 17, 'Viewing', 'http://www.w3.org/2002/07/owl#Class', 'Viewing'),
(506, 17, 'Website', 'http://www.w3.org/2002/07/owl#Class', 'Website'),
(507, 17, 'Wiki', 'http://www.w3.org/2002/07/owl#Class', 'Wiki'),
(508, 22, 'Annotation', 'http://www.w3.org/2002/07/owl#Class', 'Annotation'),
(509, 23, 'Competence', 'http://www.w3.org/2002/07/owl#Class', 'Competence'),
(510, 24, 'IntelLEO', 'http://www.w3.org/2002/07/owl#Class', 'IntelLEO'),
(511, 24, 'Organization', 'http://www.w3.org/2002/07/owl#Class', 'Organization'),
(512, 24, 'Visibility', 'http://www.w3.org/2002/07/owl#Class', 'Visibility'),
(513, 25, 'QuizResult', 'http://www.w3.org/2002/07/owl#Class', 'QuizResult'),
(514, 26, 'LearningGoal', 'http://www.w3.org/2002/07/owl#Class', 'LearningGoal'),
(515, 26, 'TargetCompetence', 'http://www.w3.org/2002/07/owl#Class', 'TargetCompetence'),
(516, 26, 'User', 'http://www.w3.org/2002/07/owl#Class', 'User'),
(517, 27, 'LearningTask', 'http://www.w3.org/2002/07/owl#Class', 'LearningTask'),
(518, 28, 'Resource', 'http://www.w3.org/2002/07/owl#Class', 'Resource'),
(519, 17, 'Asynchronous', 'http://www.w3.org/2002/07/owl#Thing', 'Asynchronous'),
(520, 17, 'MultiSynchronous', 'http://www.w3.org/2002/07/owl#Thing', 'Multi-synchronous'),
(521, 17, 'Synchronous', 'http://www.w3.org/2002/07/owl#Thing', 'Synchronous');


CREATE TABLE IF NOT EXISTS `rdf_vocabularies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `namespace` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `prefix` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `alternative_source_uri` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

INSERT INTO `rdf_vocabularies` (`id`, `namespace`, `prefix`, `alternative_source_uri`) VALUES
(1, 'http://purl.org/meducator/ns/', '', 'http://purl.org/meducator/ns'),
(2, 'http://xmlns.com/wot/0.1/', '', 'http://xmlns.com/foaf/0.1/'),
(3, 'http://www.w3.org/2003/06/sw-vocab-status/ns#', '', 'http://xmlns.com/foaf/0.1/'),
(4, 'http://purl.org/dc/elements/1.1/', '', 'rdf/intelleo.rdf'),
(5, 'http://www.w3.org/2000/01/rdf-schema#', '', 'rdf/intelleo.rdf'),
(6, 'http://xmlns.com/foaf/0.1/', '', 'rdf/intelleo.rdf'),
(7, 'http://www.w3.org/2000/10/swap/pim/contact#', '', 'http://xmlns.com/foaf/0.1/'),
(8, 'http://www.w3.org/2003/01/geo/wgs84_pos#', '', 'http://xmlns.com/foaf/0.1/'),
(9, 'http://www.w3.org/2002/07/owl#', '', 'rdf/intelleo.rdf'),
(10, 'http://www.w3.org/2004/02/skos/core#', '', 'http://xmlns.com/foaf/0.1/'),
(11, 'http://purl.org/dc/terms/', '', 'http://purl.org/dc/terms/'),
(12, 'http://www.w3.org/2002/07/', '', 'http://www.w3.org/2002/07/owl'),
(13, 'http://wow.sfsu.edu/ontology/rich/', '', 'rdf/Networks.owl'),
(14, 'http://wow.sfsu.edu/ontology/rich/Networks.owl#', '', 'rdf/Networks.owl'),
(16, 'http://www.intelleo.eu/ontologies/activities/', '', 'rdf/intelleo.rdf'),
(17, 'http://www.intelleo.eu/ontologies/activities/ns#', '', 'rdf/intelleo.rdf'),
(19, 'http://intelleo.eu/ontologies/workflow/ns/', '', 'rdf/intelleo.rdf'),
(20, 'http://jelenajovanovic.net/ontologies/loco/alocom-core/ns/', '', 'rdf/intelleo.rdf'),
(21, 'http://rdfs.org/sioc/ns#', '', 'rdf/intelleo.rdf'),
(22, 'http://www.intelleo.eu/ontologies/annotations/ns/', '', 'rdf/intelleo.rdf'),
(23, 'http://www.intelleo.eu/ontologies/competences/ns/', '', 'rdf/intelleo.rdf'),
(24, 'http://www.intelleo.eu/ontologies/organization/ns/', '', 'rdf/intelleo.rdf'),
(25, 'http://www.intelleo.eu/ontologies/quiz/ns/', '', 'rdf/intelleo.rdf'),
(26, 'http://www.intelleo.eu/ontologies/user-model/ns/', '', 'rdf/intelleo.rdf'),
(27, 'http://www.intelleo.eu/ontologies/workflow/ns/', '', 'rdf/intelleo.rdf'),
(28, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', '', 'rdf/intelleo.rdf');
