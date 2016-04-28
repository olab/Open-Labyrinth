ALTER TABLE `user_sessions` ADD `reset_at` DECIMAL(18,6) NULL DEFAULT NULL AFTER `notCumulative`;
ALTER TABLE `statistics_user_sessiontraces` ADD `end_date_stamp` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `bookmark_used`;
ALTER TABLE `statistics_user_sessiontraces` ADD `is_redirected` BOOLEAN NOT NULL DEFAULT FALSE AFTER `node_id`;