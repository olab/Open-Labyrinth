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