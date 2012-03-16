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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `maps`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

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
-- Table structure for table `map_keys`
--

CREATE TABLE IF NOT EXISTS `map_keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

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
  `title` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `content` text NOT NULL,
  `type_id` int(11) NOT NULL,
  `probability` tinyint(1) NOT NULL,
  `conditional` varchar(500) NOT NULL,
  `conditional_message` varchar(1000) NOT NULL,
  `info` varchar(1000) NOT NULL,
  `link_style_id` int(11) NOT NULL,
  `priority_id` int(11) NOT NULL,
  `kfp` tinyint(1) NOT NULL,
  `undo` tinyint(1) NOT NULL,
  `x` double NOT NULL,
  `y` double NOT NULL,
  `rgb` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `map_nodes`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

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
-- Table structure for table `map_node_priorities`
--

CREATE TABLE IF NOT EXISTS `map_node_priorities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `map_node_priorities`
--

INSERT INTO `map_node_priorities` (`id`, `name`, `description`) VALUES
(1, 'normal (default)', ''),
(2, 'must avoid', ''),
(3, 'must visit', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_node_types`
--

CREATE TABLE IF NOT EXISTS `map_node_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `map_node_types`
--

INSERT INTO `map_node_types` (`id`, `name`, `description`) VALUES
(1, 'root', ''),
(2, 'child', '');

-- --------------------------------------------------------

--
-- Table structure for table `map_sections`
--

CREATE TABLE IF NOT EXISTS `map_sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(700) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

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
  `password` varchar(100) NOT NULL,
  `email` varchar(250) NOT NULL,
  `nickname` varchar(120) NOT NULL,
  `language_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`),
  KEY `fk_language_id` (`language_id`),
  KEY `fk_type_id` (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `nickname`, `language_id`, `type_id`) VALUES
(1, 'admin', 'admin', 'admin@admin.com', 'administrator', 1, 4);

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
