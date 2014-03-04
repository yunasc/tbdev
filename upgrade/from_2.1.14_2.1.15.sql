#
# SQL script to update from 2.1.14 version to 2.1.15
#

ALTER TABLE `torrents` ADD COLUMN `not_sticky` ENUM('yes','no') NOT NULL DEFAULT 'yes' AFTER `sticky`;
UPDATE `torrents` SET `not_sticky` = 'no' WHERE `sticky` = 'yes';
ALTER TABLE `torrents` ADD  INDEX `vnsi` (`visible`, `not_sticky`, `id`);
ALTER TABLE `torrents` DROP COLUMN `sticky`;