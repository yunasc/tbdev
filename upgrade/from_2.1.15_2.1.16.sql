#
# SQL script to update from 2.1.15 version to 2.1.16
#

ALTER TABLE `torrents` ADD COLUMN `multitracker` enum('yes','no') NOT NULL DEFAULT 'no';