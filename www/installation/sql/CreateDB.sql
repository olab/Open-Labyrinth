SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `display` INT NOT NULL DEFAULT '1',
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
  `show_submit` tinyint(4) NOT NULL DEFAULT '0',
  `redirect_node_id` int(10) unsigned DEFAULT NULL,
  `submit_text` varchar(200) DEFAULT NULL,
  `type_display` INT NOT NULL DEFAULT '0',
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
(1, 'single line text entry', 'text', 'text', NULL),
(2, 'multi-line text entry', 'area', 'area', NULL),
(3, 'multiple choice', 'mcq', 'choice', '0'),
(4, 'pick choice', 'pcq', 'choice', '0');

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
(4, 'keys (a key is required to access this Labyrinth)', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_skins`
--

CREATE TABLE IF NOT EXISTS `map_skins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `path` varchar(200) NOT NULL,
  `user_id` INT NULL,
  `enabled` TINYINT( 1 ) NOT NULL DEFAULT '1',
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
  `resetHashKey` VARCHAR( 255 ) NULL,
  `resetHashKeyTime` DATETIME NULL,
  `resetAttempt` INT NULL,
  `resetTimestamp` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`),
  KEY `fk_language_id` (`language_id`),
  KEY `fk_type_id` (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

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

CREATE TABLE IF NOT EXISTS `rdf_mappings_classes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `class` varchar(100) NOT NULL,
  `term_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `rdf_mappings_legacy_properties` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `class` varchar(100) NOT NULL,
  `property` varchar(100) NOT NULL,
  `term_id` int(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rdf_terms` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `vocab_id` int(10) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(2000) NOT NULL,
  `term_label` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vocab_id` (`vocab_id`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `rdf_vocabularies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `namespace` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `prefix` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `alternative_source_uri` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

