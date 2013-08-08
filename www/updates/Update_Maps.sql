ALTER TABLE `maps` ADD `reminder_msg` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `delta_time` ,
ADD `reminder_time` INT( 11 ) NOT NULL AFTER `reminder_msg`;