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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

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
