ALTER TABLE `map_skins` ADD `user_id` INT NOT NULL AFTER `path`;
ALTER TABLE `map_skins` ADD `enabled` TINYINT( 1 ) NOT NULL DEFAULT '1'