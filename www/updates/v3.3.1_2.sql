CREATE TABLE `lti_providers` (
`id` int(11) unsigned NOT NULL,
  `name` enum('video service') DEFAULT NULL,
  `consumer_key` varchar(255) DEFAULT NULL,
  `secret` varchar(32) DEFAULT NULL,
  `launch_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `lti_providers`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `lti_providers`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;