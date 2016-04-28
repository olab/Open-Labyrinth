CREATE TABLE `options` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `options`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `options`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`name`, `value`, `autoload`) VALUES
  ('h5p_frame', '1', 'yes'),
  ('h5p_export', '1', 'yes'),
  ('h5p_embed', '1', 'yes'),
  ('h5p_copyright', '1', 'yes'),
  ('h5p_icon', '1', 'yes'),
  ('h5p_track_user', '1', 'yes'),
  ('h5p_library_updates', '1', 'yes'),
  ('h5p_save_content_state', '', 'yes'),
  ('h5p_save_content_frequency', '30', 'yes'),
  ('h5p_version', '1.6.2', 'yes'),
  ('h5p_update_available', '1460717804', 'yes'),
  ('h5p_update_available_path', 'https://h5p.org/sites/default/files/official-h5p-release-20160415.h5p', 'yes'),
  ('h5p_current_update', '1460717804', 'yes'),
  ('h5p_last_info_print', '1.6.2', 'yes');