ALTER TABLE `map_counter_common_rules` CHANGE `rule` `rule` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `maps` CHANGE `reminder_msg` `reminder_msg` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '', CHANGE `reminder_time` `reminder_time` INT( 11 ) NOT NULL DEFAULT '0';