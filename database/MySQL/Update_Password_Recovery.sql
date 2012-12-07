ALTER TABLE `users` ADD `resetHashKey` VARCHAR( 255 ) NULL ,
ADD `resetHashKeyTime` DATETIME NULL ,
ADD `resetAttempt` INT NULL ,
ADD `resetTimestamp` DATETIME NULL;
