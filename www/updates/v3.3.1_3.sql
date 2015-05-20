CREATE TABLE `map_groups` (
`id` int(11) unsigned NOT NULL,
  `map_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `map_groups`
 ADD PRIMARY KEY (`id`), ADD KEY `map_id` (`map_id`), ADD KEY `group_id` (`group_id`);

ALTER TABLE `map_groups`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;