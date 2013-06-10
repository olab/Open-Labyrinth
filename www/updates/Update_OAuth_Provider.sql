CREATE TABLE IF NOT EXISTS `oauth_providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `version` varchar(200) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO `oauth_providers` (`id`, `name`, `version`, `icon`) VALUES
(1, 'github', 'v2', 'github_icon.png'),
(3, 'facebook', 'v2', 'facebook_icon.png'),
(4, 'twitter', 'v1', 'twitter_icon.png');