#
# SQL script to update from 2.1.16 version to 2.1.17
#

CREATE TABLE `comments_parsed` (
  `cid` int(10) unsigned NOT NULL DEFAULT '0',
  `text_hash` varchar(32) NOT NULL DEFAULT '',
  `text_parsed` text NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM;

CREATE TABLE `torrents_descr` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `descr_hash` varchar(32) NOT NULL DEFAULT '',
  `descr_parsed` text NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM;