#
# SQL script to update from 2.1.15 version to 2.1.16
#

CREATE TABLE `torrents_scrape` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `info_hash` varbinary(40) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `seeders` int(10) unsigned NOT NULL DEFAULT '0',
  `leechers` int(10) unsigned NOT NULL DEFAULT '0',
  `completed` int(10) unsigned NOT NULL DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `state` enum('ok','error') NOT NULL DEFAULT 'ok',
  `error` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`info_hash`,`url`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM;

ALTER TABLE `torrents` ADD COLUMN `multitracker` enum('yes','no') NOT NULL DEFAULT 'no';
ALTER TABLE `torrents` ADD COLUMN `last_mt_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `last_action`;
ALTER TABLE `torrents` ADD COLUMN `remote_leechers` int(10) unsigned NOT NULL DEFAULT '0' AFTER `leechers`;
ALTER TABLE `torrents` ADD COLUMN `remote_seeders` int(10) unsigned NOT NULL DEFAULT '0' AFTER `seeders`;