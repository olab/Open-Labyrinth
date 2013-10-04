--
-- Table structure for table `map_visual_displays`
--

CREATE TABLE IF NOT EXISTS `map_visual_displays` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=121 ;

-- --------------------------------------------------------

--
-- Table structure for table `map_visual_display_counters`
--

CREATE TABLE IF NOT EXISTS `map_visual_display_counters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `visual_id` int(10) unsigned NOT NULL,
  `counter_id` int(10) unsigned NOT NULL,
  `label_x` int(11) DEFAULT '0',
  `label_y` int(11) DEFAULT '0',
  `label_angle` int(11) DEFAULT '0',
  `label_font_style` varchar(300) DEFAULT NULL,
  `label_text` text,
  `label_z_index` int(10) unsigned DEFAULT '0',
  `value_x` int(11) DEFAULT '0',
  `value_y` int(11) DEFAULT '0',
  `value_angle` int(11) DEFAULT '0',
  `value_font_style` varchar(300) DEFAULT NULL,
  `value_z_index` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `visual_id` (`visual_id`),
  KEY `counter_id` (`counter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `map_visual_display_images`
--

CREATE TABLE IF NOT EXISTS `map_visual_display_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `visual_id` int(10) unsigned NOT NULL,
  `name` varchar(400) NOT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `angle` int(11) DEFAULT '0',
  `z_index` int(10) unsigned DEFAULT '0',
  `x` int(11) DEFAULT '0',
  `y` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `visual_id` (`visual_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Table structure for table `map_visual_display_panels`
--

CREATE TABLE IF NOT EXISTS `map_visual_display_panels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `visual_id` int(10) unsigned NOT NULL,
  `x` int(11) DEFAULT '0',
  `y` int(11) DEFAULT '0',
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `background_color` varchar(40) DEFAULT NULL,
  `border_size` int(10) unsigned DEFAULT '0',
  `border_color` varchar(40) DEFAULT NULL,
  `border_radius` int(10) unsigned DEFAULT '0',
  `angle` int(11) DEFAULT '0',
  `z_index` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `visual_id` (`visual_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `map_visual_display_counters`
--
ALTER TABLE `map_visual_display_counters`
  ADD CONSTRAINT `map_visual_display_counters_ibfk_1` FOREIGN KEY (`visual_id`) REFERENCES `map_visual_displays` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `map_visual_display_counters_ibfk_2` FOREIGN KEY (`counter_id`) REFERENCES `map_counters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_visual_display_images`
--
ALTER TABLE `map_visual_display_images`
  ADD CONSTRAINT `map_visual_display_images_ibfk_1` FOREIGN KEY (`visual_id`) REFERENCES `map_visual_displays` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `map_visual_display_panels`
--
ALTER TABLE `map_visual_display_panels`
  ADD CONSTRAINT `map_visual_display_panels_ibfk_1` FOREIGN KEY (`visual_id`) REFERENCES `map_visual_displays` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
