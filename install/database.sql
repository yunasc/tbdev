#
# Structure for the `avps` table :
#

DROP TABLE IF EXISTS `avps`;

CREATE TABLE `avps` (
  `arg` varchar(20) NOT NULL default '',
  `value_s` text NOT NULL,
  `value_i` int(11) NOT NULL default '0',
  `value_u` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`arg`)
) ENGINE=MyISAM;

#
# Structure for the `bans` table :
#

DROP TABLE IF EXISTS `bans`;

CREATE TABLE `bans` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `addedby` int(10) unsigned NOT NULL default '0',
  `comment` varchar(255) NOT NULL default '',
  `first` bigint(11) default NULL,
  `last` bigint(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `first_last` (`first`,`last`)
) ENGINE=MyISAM;

#
# Structure for the `blocks` table :
#

DROP TABLE IF EXISTS `blocks`;

CREATE TABLE `blocks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `blockid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`blockid`)
) ENGINE=MyISAM;

#
# Structure for the `bonus` table :
#

DROP TABLE IF EXISTS `bonus`;

CREATE TABLE `bonus` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `points` decimal(7,2) NOT NULL default '0.00',
  `description` text NOT NULL,
  `type` varchar(10) NOT NULL default 'traffic',
  `quanity` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

#
# Structure for the `bookmarks` table :
#

DROP TABLE IF EXISTS `bookmarks`;

CREATE TABLE `bookmarks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `torrentid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

#
# Structure for the `captcha` table :
#

DROP TABLE IF EXISTS `captcha`;

CREATE TABLE `captcha` (
  `imagehash` varchar(32) NOT NULL default '',
  `imagestring` varchar(8) NOT NULL default '',
  `dateline` bigint(30) NOT NULL default '0',
  KEY `imagehash` (`imagehash`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM;

#
# Structure for the `categories` table :
#

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sort` int(10) NOT NULL default '0',
  `name` varchar(30) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

#
# Structure for the `checkcomm` table :
#

DROP TABLE IF EXISTS `checkcomm`;

CREATE TABLE `checkcomm` (
  `id` int(11) NOT NULL auto_increment,
  `checkid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `torrent` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

#
# Structure for the `comments` table :
#

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `torrent` int(10) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `text` text NOT NULL,
  `ori_text` text NOT NULL,
  `editedby` int(10) unsigned NOT NULL default '0',
  `editedat` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM;

#
# Structure for the `comments_parsed` table :
#

DROP TABLE IF EXISTS `comments_parsed`;

CREATE TABLE `comments_parsed` (
  `cid` int(10) unsigned NOT NULL DEFAULT '0',
  `text_hash` varchar(32) NOT NULL DEFAULT '',
  `text_parsed` text NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM;

#
# Structure for the `countries` table :
#

DROP TABLE IF EXISTS `countries`;

CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `flagpic` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

#
# Structure for the `faq` table :
#

DROP TABLE IF EXISTS `faq`;

CREATE TABLE `faq` (
  `id` int(10) NOT NULL auto_increment,
  `type` set('categ','item') NOT NULL default 'item',
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `flag` tinyint(1) NOT NULL default '1',
  `categ` int(10) NOT NULL default '0',
  `order` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

#
# Structure for the `files` table :
#

DROP TABLE IF EXISTS `files`;

CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `size` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM;

#
# Structure for the `friends` table :
#

DROP TABLE IF EXISTS `friends`;

CREATE TABLE `friends` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `friendid` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `userfriend` (`userid`,`friendid`)
) ENGINE=MyISAM;

#
# Structure for the `indexreleases` table :
#

DROP TABLE IF EXISTS `indexreleases`;

CREATE TABLE `indexreleases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `torrentid` int(10) NOT NULL DEFAULT '0',
  `name` text NOT NULL,
  `cat` int(10) NOT NULL DEFAULT '0',
  `poster` text NOT NULL,
  `imdb` text NOT NULL,
  `top` text NOT NULL,
  `center` text NOT NULL,
  `bottom` text NOT NULL,
  `added` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

#
# Structure for the `invites` table :
#

DROP TABLE IF EXISTS `invites`;

CREATE TABLE `invites` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `inviter` int(10) unsigned NOT NULL default '0',
  `inviteid` int(10) NOT NULL default '0',
  `invite` varchar(32) NOT NULL default '',
  `time_invited` datetime NOT NULL default '0000-00-00 00:00:00',
  `confirmed` char(3) NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `inviter` (`id`)
) ENGINE=MyISAM;

#
# Structure for the `messages` table :
#

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` int(10) unsigned NOT NULL default '0',
  `receiver` int(10) unsigned NOT NULL default '0',
  `added` datetime default NULL,
  `subject` varchar(255) NOT NULL default '',
  `msg` text,
  `unread` enum('yes','no') NOT NULL default 'yes',
  `poster` int(10) unsigned NOT NULL default '0',
  `location` tinyint(1) NOT NULL default '1',
  `saved` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `receiver` (`receiver`),
  KEY `sender` (`sender`),
  KEY `poster` (`poster`)
) ENGINE=MyISAM;

#
# Structure for the `news` table :
#

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `body` text NOT NULL,
  `subject` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM;

#
# Structure for the `notconnectablepmlog` table :
#

DROP TABLE IF EXISTS `notconnectablepmlog`;

CREATE TABLE `notconnectablepmlog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` int(10) unsigned NOT NULL default '0',
  `date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

#
# Structure for the `orbital_blocks` table :
#

DROP TABLE IF EXISTS `orbital_blocks`;

CREATE TABLE `orbital_blocks` (
  `bid` int(10) NOT NULL auto_increment,
  `bkey` varchar(15) NOT NULL default '',
  `title` varchar(60) NOT NULL default '',
  `content` text NOT NULL,
  `bposition` char(1) NOT NULL default '',
  `weight` int(10) NOT NULL default '1',
  `active` int(1) NOT NULL default '1',
  `time` varchar(14) NOT NULL default '0',
  `blockfile` varchar(255) NOT NULL default '',
  `view` int(1) NOT NULL default '0',
  `expire` varchar(14) NOT NULL default '0',
  `action` char(1) NOT NULL default '',
  `which` varchar(255) NOT NULL default '',
  `allow_hide` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`bid`),
  KEY `title` (`title`),
  KEY `weight` (`weight`),
  KEY `active` (`active`)
) ENGINE=MyISAM;

#
# Structure for the `peers` table :
#

DROP TABLE IF EXISTS `peers`;

CREATE TABLE `peers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `torrent` int(10) unsigned NOT NULL default '0',
  `peer_id` varchar(20) NOT NULL default '',
  `ip` varchar(64) NOT NULL default '',
  `port` smallint(5) unsigned NOT NULL default '0',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `uploadoffset` bigint(20) unsigned NOT NULL default '0',
  `downloadoffset` bigint(20) unsigned NOT NULL default '0',
  `to_go` bigint(20) unsigned NOT NULL default '0',
  `seeder` enum('yes','no') NOT NULL default 'no',
  `started` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `prev_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `connectable` enum('yes','no') NOT NULL default 'yes',
  `userid` int(10) unsigned NOT NULL default '0',
  `agent` varchar(60) NOT NULL default '',
  `finishedat` int(10) unsigned NOT NULL default '0',
  `passkey` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `torrent_peer_id` (`torrent`,`peer_id`),
  KEY `torrent` (`torrent`),
  KEY `torrent_seeder` (`torrent`,`seeder`),
  KEY `last_action` (`last_action`),
  KEY `connectable` (`connectable`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM;

#
# Structure for the `pollanswers` table :
#

DROP TABLE IF EXISTS `pollanswers`;

CREATE TABLE `pollanswers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pollid` int(10) unsigned NOT NULL default '0',
  `userid` int(10) unsigned NOT NULL default '0',
  `selection` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pollid` (`pollid`),
  KEY `selection` (`selection`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM;

#
# Structure for the `polls` table :
#

DROP TABLE IF EXISTS `polls`;

CREATE TABLE `polls` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `question` varchar(255) NOT NULL default '',
  `option0` varchar(40) NOT NULL default '',
  `option1` varchar(40) NOT NULL default '',
  `option2` varchar(40) NOT NULL default '',
  `option3` varchar(40) NOT NULL default '',
  `option4` varchar(40) NOT NULL default '',
  `option5` varchar(40) NOT NULL default '',
  `option6` varchar(40) NOT NULL default '',
  `option7` varchar(40) NOT NULL default '',
  `option8` varchar(40) NOT NULL default '',
  `option9` varchar(40) NOT NULL default '',
  `option10` varchar(40) NOT NULL default '',
  `option11` varchar(40) NOT NULL default '',
  `option12` varchar(40) NOT NULL default '',
  `option13` varchar(40) NOT NULL default '',
  `option14` varchar(40) NOT NULL default '',
  `option15` varchar(40) NOT NULL default '',
  `option16` varchar(40) NOT NULL default '',
  `option17` varchar(40) NOT NULL default '',
  `option18` varchar(40) NOT NULL default '',
  `option19` varchar(40) NOT NULL default '',
  `sort` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

#
# Structure for the `ratings` table :
#

DROP TABLE IF EXISTS `ratings`;

CREATE TABLE `ratings` (
  `id` int(6) NOT NULL auto_increment,
  `torrent` int(10) NOT NULL default '0',
  `user` int(6) NOT NULL default '0',
  `rating` int(1) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

#
# Structure for the `readtorrents` table :
#

DROP TABLE IF EXISTS `readtorrents`;

CREATE TABLE `readtorrents` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `torrentid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `read` (`userid`,`torrentid`)
) ENGINE=MyISAM;

#
# Structure for the `sessions` table :
#

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `sid` varchar(32) NOT NULL default '',
  `uid` int(10) NOT NULL default '0',
  `username` varchar(40) NOT NULL default '',
  `class` tinyint(4) NOT NULL default '0',
  `ip` varchar(40) NOT NULL default '',
  `time` bigint(30) NOT NULL default '0',
  `url` varchar(150) NOT NULL default '',
  `useragent` text,
  PRIMARY KEY  (`sid`),
  KEY `time` (`time`),
  KEY `uid` (`uid`),
  KEY `url` (`url`)
) ENGINE=MyISAM;

#
# Structure for the `simpaty` table :
#

DROP TABLE IF EXISTS `simpaty`;

CREATE TABLE `simpaty` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `touserid` int(10) unsigned NOT NULL default '0',
  `fromuserid` int(10) unsigned NOT NULL default '0',
  `fromusername` varchar(40) NOT NULL default '',
  `bad` tinyint(1) unsigned NOT NULL default '0',
  `good` tinyint(1) unsigned NOT NULL default '0',
  `type` varchar(60) NOT NULL default '',
  `respect_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `description` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `touserid` (`touserid`),
  KEY `fromuserid` (`fromuserid`),
  KEY `fromusername` (`fromusername`)
) ENGINE=MyISAM;

#
# Structure for the `sitelog` table :
#

DROP TABLE IF EXISTS `sitelog`;

CREATE TABLE `sitelog` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `added` datetime default NULL,
  `color` varchar(11) NOT NULL default 'transparent',
  `txt` text,
  `type` varchar(8) NOT NULL default 'tracker',
  PRIMARY KEY  (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM;

#
# Structure for the `snatched` table :
#

DROP TABLE IF EXISTS `snatched`;

CREATE TABLE `snatched` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) default '0',
  `torrent` int(10) unsigned NOT NULL default '0',
  `port` smallint(5) unsigned NOT NULL default '0',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `to_go` bigint(20) unsigned NOT NULL default '0',
  `seeder` enum('yes','no') NOT NULL default 'no',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `startdat` datetime NOT NULL default '0000-00-00 00:00:00',
  `completedat` datetime NOT NULL default '0000-00-00 00:00:00',
  `connectable` enum('yes','no') NOT NULL default 'yes',
  `finished` enum('yes','no') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `snatch` (`torrent`,`userid`)
) ENGINE=MyISAM;

#
# Structure for the `thanks` table :
#

DROP TABLE IF EXISTS `thanks`;

CREATE TABLE `thanks` (
  `id` int(11) NOT NULL auto_increment,
  `torrentid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `thank` (`torrentid`,`userid`)
) ENGINE=MyISAM;

#
# Structure for the `torrents` table :
#

DROP TABLE IF EXISTS `torrents`;

CREATE TABLE `torrents` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `info_hash` varbinary(40) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `keywords` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `filename` varchar(255) NOT NULL default '',
  `save_as` varchar(255) NOT NULL default '',
  `descr` text NOT NULL,
  `ori_descr` text NOT NULL,
  `image1` text NOT NULL,
  `image2` text NOT NULL,
  `image3` text NOT NULL,
  `image4` text NOT NULL,
  `image5` text NOT NULL,
  `category` int(10) unsigned NOT NULL default '0',
  `size` bigint(20) unsigned NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `type` enum('single','multi') NOT NULL default 'single',
  `numfiles` int(10) unsigned NOT NULL default '0',
  `comments` int(10) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `hits` int(10) unsigned NOT NULL default '0',
  `times_completed` int(10) unsigned NOT NULL default '0',
  `leechers` int(10) unsigned NOT NULL default '0',
  `remote_leechers` int(10) unsigned NOT NULL DEFAULT '0',
  `seeders` int(10) unsigned NOT NULL default '0',
  `remote_seeders` int(10) unsigned NOT NULL DEFAULT '0',
  `last_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_mt_update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_reseed` datetime NOT NULL default '0000-00-00 00:00:00',
  `visible` enum('yes','no') NOT NULL default 'yes',
  `banned` enum('yes','no') NOT NULL default 'no',
  `owner` int(10) unsigned NOT NULL default '0',
  `numratings` int(10) unsigned NOT NULL default '0',
  `ratingsum` int(10) unsigned NOT NULL default '0',
  `free` enum('yes','silver','no') default 'no',
  `not_sticky` enum('yes','no') NOT NULL DEFAULT 'yes',
  `moderated` enum('yes','no') NOT NULL default 'no',
  `moderatedby` int(10) unsigned default '0',
  `multitracker` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `info_hash` (`info_hash`),
  KEY `owner` (`owner`),
  KEY `visible` (`visible`),
  KEY `category_visible` (`category`,`visible`),
  KEY `vnsi` (`visible`, `not_sticky`, `id`)
) ENGINE=MyISAM;

#
# Structure for the `torrents_descr` table :
#

DROP TABLE IF EXISTS `torrents_descr`;

CREATE TABLE `torrents_descr` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0',
  `descr_hash` varchar(32) NOT NULL DEFAULT '',
  `descr_parsed` text NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM;

#
# Structure for the `torrents_scrape` table :
#

DROP TABLE IF EXISTS `torrents_scrape`;

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

#
# Structure for the `users` table :
#

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(40) NOT NULL default '',
  `old_password` varchar(40) NOT NULL default '',
  `passhash` varchar(32) NOT NULL default '',
  `secret` varchar(20) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `status` enum('pending','confirmed') NOT NULL default 'pending',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_access` datetime NOT NULL default '0000-00-00 00:00:00',
  `editsecret` varchar(20) NOT NULL default '',
  `privacy` enum('strong','normal','low') NOT NULL default 'normal',
  `theme` varchar(40) NOT NULL default '',
  `info` text,
  `acceptpms` enum('yes','friends','no') NOT NULL default 'yes',
  `ip` varchar(15) NOT NULL default '',
  `class` tinyint(3) unsigned NOT NULL default '0',
  `override_class` tinyint(3) unsigned NOT NULL default '255',
  `support` enum('no','yes') NOT NULL default 'no',
  `supportfor` text,
  `avatar` varchar(100) NOT NULL default '',
  `icq` varchar(255) NOT NULL default '',
  `msn` varchar(255) NOT NULL default '',
  `aim` varchar(255) NOT NULL default '',
  `yahoo` varchar(255) NOT NULL default '',
  `skype` varchar(255) NOT NULL default '',
  `mirc` varchar(255) NOT NULL default '',
  `website` varchar(50) NOT NULL default '',
  `uploaded` bigint(20) unsigned NOT NULL default '0',
  `downloaded` bigint(20) unsigned NOT NULL default '0',
  `bonus` decimal(7,2) NOT NULL default '0.00',
  `title` varchar(30) NOT NULL default '',
  `country` int(10) unsigned NOT NULL default '0',
  `notifs` varchar(100) NOT NULL default '',
  `modcomment` text,
  `enabled` enum('yes','no') NOT NULL default 'yes',
  `parked` enum('yes','no') NOT NULL default 'no',
  `avatars` enum('yes','no') NOT NULL default 'yes',
  `donor` enum('yes','no') NOT NULL default 'no',
  `simpaty` int(10) NOT NULL default '0',
  `warned` enum('yes','no') NOT NULL default 'no',
  `warneduntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `torrentsperpage` int(3) unsigned NOT NULL default '0',
  `topicsperpage` int(3) unsigned NOT NULL default '0',
  `postsperpage` int(3) unsigned NOT NULL default '0',
  `deletepms` enum('yes','no') NOT NULL default 'yes',
  `savepms` enum('yes','no') NOT NULL default 'no',
  `gender` enum('1','2','3') NOT NULL default '1',
  `birthday` date default '0000-00-00',
  `passkey` varchar(32) NOT NULL default '',
  `language` varchar(255) NOT NULL default 'russian',
  `invites` int(10) NOT NULL default '0',
  `invitedby` int(10) NOT NULL default '0',
  `invitedroot` int(10) NOT NULL default '0',
  `passkey_ip` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `status_added` (`status`,`added`),
  KEY `ip` (`ip`),
  KEY `uploaded` (`uploaded`),
  KEY `downloaded` (`downloaded`),
  KEY `country` (`country`),
  KEY `last_access` (`last_access`),
  KEY `enabled` (`enabled`),
  KEY `warned` (`warned`),
  KEY `passkey` (`passkey`),
  KEY `user` (`id`,`status`,`enabled`)
) ENGINE=MyISAM;

#
# Structure for the `users_ban` table :
#

DROP TABLE IF EXISTS `users_ban`;

CREATE TABLE `users_ban` (
  `userid` int(10) unsigned NOT NULL default '0',
  `disuntil` datetime NOT NULL default '0000-00-00 00:00:00',
  `disby` int(10) unsigned NOT NULL default '0',
  `reason` text NOT NULL,
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM;

#
# Data for the `bonus` table  (LIMIT 0,100)
#

INSERT INTO `bonus` (`id`, `name`, `points`, `description`, `type`, `quanity`) VALUES
  (1,'1.0GB Uploaded',75,'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.','traffic','1073741824'),
  (2,'2.5GB Uploaded',150,'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.','traffic','2684354560'),
  (3,'5GB Uploaded',250,'With enough bonus points acquired, you are able to exchange them for an Upload Credit. The points are then removed from your Bonus Bank and the credit is added to your total uploaded amount.','traffic','5368709120'),
  (4,'3 Invites',20,'With enough bonus points acquired, you are able to exchange them for a few invites. The points are then removed from your Bonus Bank and the invitations are added to your invites amount.','invite','3');

COMMIT;

#
# Data for the `categories` table  (LIMIT 0,100)
#

INSERT INTO `categories` (`id`, `sort`, `name`, `image`) VALUES
  (1,10,'Приложения ISO','appzpciso.gif'),
  (2,20,'Приложения PDA','appzpda.gif'),
  (3,30,'Приложения AUDIO','appzaudio.gif'),
  (4,40,'Приложения MISC','appzmisc.gif'),
  (5,50,'Игры PC','gamespc.gif'),
  (6,60,'Игры PS2','gamesps2.gif'),
  (7,70,'Игры X-Box','gamesxbox.gif'),
  (8,80,'Игры PSP','gamespsp.gif'),
  (9,90,'Документация','docs.gif'),
  (10,100,'Музыка','music.gif'),
  (11,110,'Сериалы','tv.gif'),
  (12,120,'Аниме','anime.gif'),
  (13,130,'Фильмы XviD','moviesxvid.gif'),
  (14,140,'Фильмы HDTV','movieshdtv.gif'),
  (15,150,'Фильмы DVD','moviesdvd.gif'),
  (16,160,'Книги','ebooks.gif'),
  (17,170,'XXX','xxx.gif');

COMMIT;

#
# Data for the 'countries' table  (Records 1 - 100)
#

INSERT INTO `countries` (`id`, `name`, `flagpic`) VALUES
  (87, 'Антигуа и Барбуда', 'antiguabarbuda.gif'),
  (33, 'Белиз', 'belize.gif'),
  (59, 'Буркина Фасо', 'burkinafaso.gif'),
  (10, 'Дания', 'denmark.gif'),
  (91, 'Сенегал', 'senegal.gif'),
  (76, 'Тринидад и Тобаго', 'trinidadandtobago.gif'),
  (20, 'Австралия', 'australia.gif'),
  (36, 'Австрия', 'austria.gif'),
  (27, 'Албания', 'albania.gif'),
  (34, 'Алжир', 'algeria.gif'),
  (12, 'Великобритания', 'uk.gif'),
  (35, 'Ангола', 'angola.gif'),
  (66, 'Андорра', 'andorra.gif'),
  (19, 'Аргентина', 'argentina.gif'),
  (53, 'Афганистан', 'afghanistan.gif'),
  (80, 'Багамы', 'bahamas.gif'),
  (83, 'Барбадос', 'barbados.gif'),
  (16, 'Бельгия', 'belgium.gif'),
  (84, 'Бангладеш', 'bangladesh.gif'),
  (101, 'Болгария', 'bulgaria.gif'),
  (65, 'Босния и Герцеговина', 'bosniaherzegovina.gif'),
  (18, 'Бразилия', 'brazil.gif'),
  (74, 'Вануату', 'vanuatu.gif'),
  (72, 'Венгрия', 'hungary.gif'),
  (71, 'Венесуэла', 'venezuela.gif'),
  (75, 'Вьетнам', 'vietnam.gif'),
  (7, 'Германия', 'germany.gif'),
  (77, 'Гондурас', 'honduras.gif'),
  (32, 'Гонконг', 'hongkong.gif'),
  (41, 'Греция', 'greece.gif'),
  (42, 'Гватемала', 'guatemala.gif'),
  (40, 'Доминиканская Республика', 'dominicanrep.gif'),
  (100, 'Египет', 'egypt.gif'),
  (43, 'Израиль', 'israel.gif'),
  (26, 'Индия', 'india.gif'),
  (13, 'Ирландия', 'ireland.gif'),
  (61, 'Исландия', 'iceland.gif'),
  (102, 'Исла де Муерто', 'jollyroger.gif'),
  (22, 'Испания', 'spain.gif'),
  (9, 'Италия', 'italy.gif'),
  (82, 'Камбоджа', 'cambodia.gif'),
  (5, 'Канада', 'canada.gif'),
  (78, 'Кыргызстан', 'kyrgyzstan.gif'),
  (57, 'Кирибати', 'kiribati.gif'),
  (8, 'Китай', 'china.gif'),
  (52, 'Конго', 'congo.gif'),
  (96, 'Колумбия', 'colombia.gif'),
  (99, 'Коста-Рика', 'costarica.gif'),
  (51, 'Куба', 'cuba.gif'),
  (85, 'Лаос', 'laos.gif'),
  (98, 'Латвия', 'latvia.gif'),
  (97, 'Ливан', 'lebanon.gif'),
  (67, 'Литва', 'lithuania.gif'),
  (31, 'Люксембург', 'luxembourg.gif'),
  (68, 'Македония', 'macedonia.gif'),
  (39, 'Малайзия', 'malaysia.gif'),
  (24, 'Мексика', 'mexico.gif'),
  (62, 'Науру', 'nauru.gif'),
  (60, 'Нигерия', 'nigeria.gif'),
  (69, 'Нидерландские Антиллы', 'nethantilles.gif'),
  (15, 'Нидерланды', 'netherlands.gif'),
  (21, 'Новая Зеландия', 'newzealand.gif'),
  (11, 'Норвегия', 'norway.gif'),
  (44, 'Пакистан', 'pakistan.gif'),
  (88, 'Парагвай', 'paraguay.gif'),
  (81, 'Перу', 'peru.gif'),
  (14, 'Польша', 'poland.gif'),
  (23, 'Португалия', 'portugal.gif'),
  (49, 'Пуэрто-Рико', 'puertorico.gif'),
  (3, 'Россия', 'russia.gif'),
  (73, 'Румыния', 'romania.gif'),
  (93, 'Северная Корея', 'northkorea.gif'),
  (47, 'Сейшельские Острова', 'seychelles.gif'),
  (46, 'Сербия', 'serbia.gif'),
  (25, 'Сингапур', 'singapore.gif'),
  (63, 'Словакия', 'slovenia.gif'),
  (90, 'СССР', 'ussr.gif'),
  (2, 'США', 'usa.gif'),
  (48, 'Тайвань', 'taiwan.gif'),
  (89, 'Таиланд', 'thailand.gif'),
  (92, 'Того', 'togo.gif'),
  (64, 'Туркменистан', 'turkmenistan.gif'),
  (54, 'Турция', 'turkey.gif'),
  (55, 'Узбекистан', 'uzbekistan.gif'),
  (70, 'Украина', 'ukraine.gif'),
  (86, 'Уругвай', 'uruguay.gif'),
  (58, 'Филиппины', 'philippines.gif'),
  (4, 'Финляндия', 'finland.gif'),
  (6, 'Франция', 'france.gif'),
  (94, 'Хорватия', 'croatia.gif'),
  (45, 'Чехия', 'czechrep.gif'),
  (50, 'Чили', 'chile.gif'),
  (56, 'Швейцария', 'switzerland.gif'),
  (1, 'Швеция', 'sweden.gif'),
  (79, 'Эквадор', 'ecuador.gif'),
  (95, 'Эстония', 'estonia.gif'),
  (37, 'Югославия', 'yugoslavia.gif'),
  (28, 'ЮАР', 'southafrica.gif'),
  (29, 'Южная Корея', 'southkorea.gif'),
  (103, 'Молдова', 'moldova.gif');

COMMIT;

#
# Data for the 'countries' table  (Records 101 - 109)
#

INSERT INTO `countries` (`id`, `name`, `flagpic`) VALUES
  (38, 'Самоа', 'westernsamoa.gif'),
  (30, 'Ямайка', 'jamaica.gif'),
  (17, 'Япония', 'japan.gif'),
  (104, 'Беларусь', 'belarus.gif'),
  (105, 'Казахстан', 'kazakhstan.gif'),
  (106, 'Таджикистан', 'tajikistan.gif'),
  (107, 'Грузия', 'georgia.gif'),
  (108, 'Армения', 'armenia.gif'),
  (109, 'Азербайджан', 'azerbaijan.gif');

COMMIT;

#
# Data for the `faq` table  (LIMIT 0,100)
#

INSERT INTO `faq` (`id`, `type`, `question`, `answer`, `flag`, `categ`, `order`) VALUES
  (1,'categ','О сайте','',1,0,1),
  (2,'categ','User information','',1,0,2),
  (3,'categ','Статистика','',1,0,3),
  (4,'categ','Заливка','',1,0,4),
  (5,'categ','Закачка','',1,0,5),
  (6,'categ','How can I improve my download speed?','',1,0,6),
  (7,'categ','My ISP uses a transparent proxy. What should I do?','',1,0,7),
  (8,'categ','Why can''t I connect? Is the site blocking me?','',1,0,8),
  (9,'categ','What if I can''t find the answer to my problem here?','',1,0,9),
  (10,'item','Что такое торрент (bittorrent)? Как скачивать файлы?','Check out <a class=altlink href=\"http://www.btfaq.com/\">Brian''s BitTorrent FAQ and Guide</a>',1,1,1),
  (11,'item','На что расходуются деньги от пожертвований?','Мы хотим приобрести выделенный сервер для комфортных и очень быстрых раздач. На данный момент деньги идут на оплату хостинга.',1,1,2),
  (12,'item','Где я могу скачать исходники этого движка?','Вы можете взять их на <a href=\"http://bit-torrent.kiev.ua/\" class=altlink_white>Проект TBDev</a>. Имейте ввиду: мы не осуществляем тех поддержку любого рода, поэтому не присылайте нам пожалуйста баги. Если оно работает - великолепно, если нет - очень плохо. Используйте их только на свой страх и риск.',1,1,3),
  (13,'item','I registered an account but did not receive the confirmation e-mail!','You can use <a class=altlink href=delacct.php>this form</a> to delete the account so you can re-register.\r\nNote though that if you didn''t receive the email the first time it will probably not\r\nsucceed the second time either so you should really try another email address.',1,2,1),
  (14,'item','I''ve lost my user name or password! Can you send it to me?','Please use <a class=altlink href=recover.php>this form</a> to have the login details mailed back to you.',1,2,2),
  (15,'item','Can you rename my account?','We do not rename accounts. Please create a new one. (Use <a href=delacct.php class=altlink>this form</a> to\r\ndelete your present account.)',1,2,3),
  (16,'item','Can you delete my (confirmed) account?','You can do it yourself by using <a href=delacct.php class=altlink>this form</a>.',1,2,4),
  (17,'item','So, what''s MY ratio?','Click on your <a class=altlink href=my.php>profile</a>, then on your user name (at the top).<br>\r\n<br>\r\nIt''s important to distinguish between your overall ratio and the individual ratio on each torrent\r\nyou may be seeding or leeching. The overall ratio takes into account the total uploaded and downloaded\r\nfrom your account since you joined the site. The individual ratio takes into account those values for each torrent.<br>\r\n<br>\r\nYou may see two symbols instead of a number: \"Inf.\", which is just an abbreviation for Infinity, and\r\nmeans that you have downloaded 0 bytes while uploading a non-zero amount (ul/dl becomes infinity); \"---\",\r\nwhich should be read as \"non-available\", and shows up when you have both downloaded and uploaded 0 bytes\r\n(ul/dl = 0/0 which is an indeterminate amount).',1,2,5),
  (18,'item','Why is my IP displayed on my details page?','Only you and the site moderators can view your IP address and email. Regular users do not see that information.',1,2,6),
  (19,'item','Help! I cannot login!? (a.k.a. Login of Death)','This problem sometimes occurs with MSIE. Close all Internet Explorer windows and open Internet Options in the control panel. Click the Delete Cookies button. You should now be able to login.\r\n',1,2,7),
  (20,'item','My IP address is dynamic. How do I stay logged in?','You do not have to anymore. All you have to do is make sure you are logged in with your actual\r\nIP when starting a torrent session. After that, even if the IP changes mid-session,\r\nthe seeding or leeching will continue and the statistics will update without any problem.',1,2,8),
  (21,'item','Why is my port number reported as \"---\"? (And why should I care?)','The tracker has determined that you are firewalled or NATed and cannot accept incoming connections.\r\n<br>\r\n<br>\r\nThis means that other peers in the swarm will be unable to connect to you, only you to them. Even worse,\r\nif two peers are both in this state they will not be able to connect at all. This has obviously a\r\ndetrimental effect on the overall speed.\r\n<br>\r\n<br>\r\nThe way to solve the problem involves opening the ports used for incoming connections\r\n(the same range you defined in your client) on the firewall and/or configuring your\r\nNAT server to use a basic form of NAT\r\nfor that range instead of NAPT (the actual process differs widely between different router models.\r\nCheck your router documentation and/or support forum. You will also find lots of information on the\r\nsubject at <a class=altlink href=\"http://portforward.com/\">PortForward</a>).',1,2,9),
  (22,'item','What are the different user classes?','<table cellspacing=3 cellpadding=0>\r\n<tr>\r\n<td class=embedded width=100 bgcolor=\"#F5F4EA\">&nbsp; <b>User</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>The default class of new members.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\">&nbsp; <b>Power User</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Can download DOX over 1MB and view NFO files.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\">&nbsp; <b><img src=\"pic/star.gif\" alt=\"Star\"></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Has donated money to TBDev Yuna Scatari Edition . </td>\r\n</tr>\r\n<tr>\r\n<td class=embedded valign=top bgcolor=\"#F5F4EA\">&nbsp; <b>VIP</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded valign=top>Same privileges as Power User and is considered an Elite Member of TBDev Yuna Scatari Edition. Immune to automatic demotion.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\">&nbsp; <b>Other</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Customised title.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\">&nbsp; <b><font color=\"#4040c0\">Uploader</font></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Same as PU except with upload rights and immune to automatic demotion.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded valign=top bgcolor=\"#F5F4EA\">&nbsp; <b><font color=\"#A83838\">Moderator</font></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded valign=top>Can edit and delete any uploaded torrents. Can also moderate usercomments and disable accounts.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\">&nbsp; <b><font color=\"#A83838\">Administrator</color></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Can do just about anything.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\">&nbsp; <b><font color=\"#A83838\">SysOp</color></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Redbeard (site owner).</td>\r\n</tr>\r\n</table>',1,2,10),
  (23,'item','How does this promotion thing work anyway?','<table cellspacing=3 cellpadding=0>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\" valign=top width=100>&nbsp; <b>Power User</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded valign=top>Must have been be a member for at least 4 weeks, have uploaded at least 25GB and\r\nhave a ratio at or above 1.05.<br>\r\nThe promotion is automatic when these conditions are met. Note that you will be automatically demoted from<br>\r\nthis status if your ratio drops below 0.95 at any time.</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\">&nbsp; <b><img src=\"pic/star.gif\" alt=\"Star\"></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Just donate, and send a message to <a class=altlink href=sendmessage.php?receiver=1>Admin</a></td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\" valign=top>&nbsp; <b>VIP</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded valign=top>Assigned by mods at their discretion to users they feel contribute something special to the site.<br>\r\n(Anyone begging for VIP status will be automatically disqualified.)</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\">&nbsp; <b>Other</b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Conferred by mods at their discretion (not available to Users or Power Users).</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\">&nbsp; <b><font color=\"#4040c0\">Uploader</font></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>Appointed by Admins/SysOp (see the ''Uploading'' section for conditions).</td>\r\n</tr>\r\n<tr>\r\n<td class=embedded bgcolor=\"#F5F4EA\">&nbsp; <b><font color=\"#A83838\">Moderator</font></b></td>\r\n<td class=embedded width=5>&nbsp;</td>\r\n<td class=embedded>You don''t ask us, we''ll ask you!</td>\r\n</tr>\r\n</table>',1,2,11),
  (24,'item','Hey! I''ve seen Power Users with less than 25GB uploaded!','The PU limit used to be 10GB and we didn''t demote anyone when we raised it to 25GB.',1,2,12),
  (25,'item','Why can''t my friend become a member?','There is a 75.000 users limit. When that number is reached we stop accepting new members. Accounts inactive for more than 42 days are automatically deleted, so keep trying. (There is no reservation or queuing system, don''t ask for that.)',1,2,13),
  (26,'item','How do I add an avatar to my profile?','First, find an image that you like, and that is within the\r\n<a class=altlink href=rules.php>rules</a>. Then you will have\r\nto find a place to host it, such as our own <a class=altlink href=bitbucket-upload.php>BitBucket</a>.\r\n(Other popular choices are <a class=\"altlink\" href=\"http://photobucket.com/\">Photobucket</a>,\r\n<a class=\"altlink\" href=\"http://uploadit.org/\">Upload-It!</a> or\r\n<a class=\"altlink\" href=\"http://www.imageshack.us/\">ImageShack</a>).\r\nAll that is left to do is copy the URL you were given when\r\nuploading it to the avatar field in your <a class=\"altlink\" href=\"usercp.php\">profile</a>.<br>\r\n<br>\r\nPlease do not make a post just to test your avatar. If everything is allright you''ll see it\r\nin your details page.',1,2,14),
  (27,'item','Most common reason for stats not updating','<ul>\r\n<li>The user is cheating. (a.k.a. \"Summary Ban\")</li>\r\n<li>The server is overloaded and unresponsive. Just try to keep the session open until the server responds again. (Flooding the server with consecutive manual updates is not recommended.)</li>\r\n<li>You are using a faulty client. If you want to use an experimental or CVS version you do it at your own risk.</li>\r\n</ul>',1,3,1),
  (28,'item','Best practices','<ul>\r\n<li>If a torrent you are currently leeching/seeding is not listed on your profile, just wait or force a manual update.</li>\r\n<li>Make sure you exit your client properly, so that the tracker receives \"event=completed\".</li>\r\n<li>If the tracker is down, do not stop seeding. As long as the tracker is back up before you exit the client the stats should update properly.</li>\r\n</ul>',1,3,2),
  (29,'item','May I use any bittorrent client?','Yes. The tracker now updates the stats correctly for all bittorrent clients. However, we still recommend\r\nthat you <b>avoid</b> the following clients:<br>\r\n<ul>\r\n<li>BitTorrent++</li>\r\n<li>Nova Torrent</li>\r\n<li>TorrentStorm</li>\r\n</ul>\r\nThese clients do not report correctly to the tracker when canceling/finishing a torrent session.\r\nIf you use them then a few MB may not be counted towards\r\nthe stats near the end, and torrents may still be listed in your profile for some time after you have closed the client.<br>\r\n<br>\r\nAlso, clients in alpha or beta version should be avoided.',1,3,3),
  (30,'item','Why is a torrent I''m leeching/seeding listed several times in my profile?','If for some reason (e.g. pc crash, or frozen client) your client exits improperly and you restart it,\r\nit will have a new peer_id, so it will show as a new torrent. The old one will never receive a \"event=completed\"\r\nor \"event=stopped\" and will be listed until some tracker timeout. Just ignore it, it will eventually go away.',1,3,4),
  (31,'item','I''ve finished or cancelled a torrent. Why is it still listed in my profile?','Some clients, notably TorrentStorm and Nova Torrent, do not report properly to the tracker when canceling or finishing a torrent.\r\nIn that case the tracker will keep waiting for some message - and thus listing the torrent as seeding or leeching - until some\r\ntimeout occurs. Just ignore it, it will eventually go away.',1,3,5),
  (32,'item','Why do I sometimes see torrents I''m not leeching in my profile!?','When a torrent is first started, the tracker uses the IP to identify the user. Therefore the torrent will\r\nbecome associated with the user <i>who last accessed the site</i> from that IP. If you share your IP in some\r\nway (you are behind NAT/ICS, or using a proxy), and some of the persons you share it with are also users,\r\nyou may occasionally see their torrents listed in your profile. (If they start a torrent session from that\r\nIP and you were the last one to visit the site the torrent will be associated with you). Note that now\r\ntorrents listed in your profile will always count towards your total stats.',1,3,6),
  (33,'item','Multiple IPs (Can I login from different computers?)','Yes, the tracker is now capable of following sessions from different IPs for the same user. A torrent is associated with the user when it starts, and only at that moment is the IP relevant. So if you want to seed/leech from computer A and computer B with the same account you should access the site from computer A, start the torrent there, and then repeat both steps from computer B (not limited to two computers or to a single torrent on each, this is just the simplest example). You do not need to login again when closing the torrent.\r\n',1,3,7),
  (34,'item','How does NAT/ICS change the picture?','This is a very particular case in that all computers in the LAN will appear to the outside world as having the same IP. We must distinguish\r\nbetween two cases:<br>\r\n<br>\r\n<b>1.</b> <i>You are the single TBDev Yuna Scatari Edition users in the LAN</i><br>\r\n<br>\r\nYou should use the same TBDev Yuna Scatari Edition account in all the computers.<br>\r\n<br>\r\nNote also that in the ICS case it is preferable to run the BT client on the ICS gateway. Clients running on the other computers\r\nwill be unconnectable (their ports will be listed as \"---\", as explained elsewhere in the FAQ) unless you specify\r\nthe appropriate services in your ICS configuration (a good explanation of how to do this for Windows XP can be found\r\n<a class=altlink href=\"redirector.php?url=http://www.microsoft.com/downloads/details.aspx?FamilyID=1dcff3ce-f50f-4a34-ae67-cac31ccd7bc9&displaylang=en\">here</a>).\r\nIn the NAT case you should configure different ranges for clients on different computers and create appropriate NAT rules in the router. (Details vary widely from router to router and are outside the scope of this FAQ. Check your router documentation and/or support forum.)<br>\r\n<br>\r\n<br>\r\n<b>2.</b> <i>There are multiple TBDev Yuna Scatari Edition users in the LAN</i><br>\r\n<br>\r\nAt present there is no way of making this setup always work properly with Template Shares.\r\nEach torrent will be associated with the user who last accessed the site from within\r\nthe LAN before the torrent was started.\r\nUnless there is cooperation between the users mixing of statistics is possible.\r\n(User A accesses the site, downloads a .torrent file, but does not start the torrent immediately.\r\nMeanwhile, user B accesses the site. User A then starts the torrent. The torrent will count\r\ntowards user B''s statistics, not user A''s.)\r\n<br>\r\n<br>\r\nIt is your LAN, the responsibility is yours. Do not ask us to ban other users\r\nwith the same IP, we will not do that. (Why should we ban <i>him</i> instead of <i>you</i>?)',1,3,8),
  (36,'item','Why can''t I upload torrents?','Only specially authorized users (<font color=\"#4040c0\"><b>Uploaders</b></font>) have permission to upload torrents.',1,4,1),
  (37,'item','What criteria must I meet before I can join the <font color=\"#4040c0\">Uploader</font> team?','You must be able to provide releases that:\r\n<li>include a proper NFO</li>\r\n<li>are genuine scene releases. If it''s not on <a class=altlink <href=\"redirector.php?url=http://www.nforce.nl\">NFOrce</a> then forget it! (except music)</li>\r\n<li>are not older than seven (7) days</li>\r\n<li>have all files in original format (usually 14.3 MB RARs)</li>\r\n<li>you''ll be able to seed, or make sure are well-seeded, for at least 24 hours.</li>\r\n<li>you should have atleast 2MBit upload bandwith.</li>\r\n</ul>\r\nIf you think you can match these criteria do not hesitate to <a class=altlink href=staff.php>contact</a> one of the administrators.<br>\r\n<b>Remember!</b> Write your application carefully! Be sure to include your UL speed and what kind of stuff you''re planning to upload.<br>\r\nOnly well written letters with serious intent will be considered.',1,4,2),
  (38,'item','Can I upload your torrents to other trackers?','No. We are a closed, limited-membership community. Only registered users can use the TB tracker.\r\nPosting our torrents on other trackers is useless, since most people who attempt to download them will\r\nbe unable to connect with us. This generates a lot of frustration and bad-will against us at TBDev Yuna Scatari Edition,\r\nand will therefore not be tolerated.<br>\r\n<br>\r\nComplaints from other sites'' administrative staff about our torrents being posted on their sites will\r\nresult in the banning of the users responsible.<br>\r\n<br>\r\n(However, the files you download from us are yours to do as you please. You can always create another\r\ntorrent, pointing to some other tracker, and upload it to the site of your choice.)',1,4,3),
  (39,'item','How do I use the files I''ve downloaded?','Check out <a class=altlink href=videoformats.php>this guide</a>.',1,5,1),
  (40,'item','Downloaded a movie and don''t know what CAM/TS/TC/SCR means?','Check out <a class=altlink href=videoformats.php>this</a> guide.',1,5,2),
  (41,'item','Why did an active torrent suddenly disappear?','There may be three reasons for this:<br>\r\n(<b>1</b>) The torrent may have been out-of-sync with the site\r\n<a class=altlink href=rules.php>rules</a>.<br>\r\n(<b>2</b>) The uploader may have deleted it because it was a bad release.\r\nA replacement will probably be uploaded to take its place.<br>\r\n(<b>3</b>) Torrents are automatically deleted after 28 days.',1,5,3),
  (42,'item','How do I resume a broken download or reseed something?','Open the .torrent file. When your client asks you for a location, choose the location of the existing file(s) and it will resume/reseed the torrent.\r\n',1,5,4),
  (43,'item','Why do my downloads sometimes stall at 99%?','The more pieces you have, the harder it becomes to find peers who have pieces you are missing. That is why downloads sometimes slow down or even stall when there are just a few percent remaining. Just be patient and you will, sooner or later, get the remaining pieces.\r\n',1,5,5),
  (44,'item','What are these \"a piece has failed an hash check\" messages?','Bittorrent clients check the data they receive for integrity. When a piece fails this check it is\r\nautomatically re-downloaded. Occasional hash fails are a common occurrence, and you shouldn''t worry.<br>\r\n<br>\r\nSome clients have an (advanced) option/preference to ''kick/ban clients that send you bad data'' or\r\nsimilar. It should be turned on, since it makes sure that if a peer repeatedly sends you pieces that\r\nfail the hash check it will be ignored in the future.',1,5,6),
  (45,'item','The torrent is supposed to be 100MB. How come I downloaded 120MB?','See the hash fails topic. If your client receives bad data it will have to redownload it, therefore\r\nthe total downloaded may be larger than the torrent size. Make sure the \"kick/ban\" option is turned on\r\nto minimize the extra downloads.',1,5,7),
  (46,'item','Why do I get a \"Not authorized (xx h) - READ THE FAQ\" error?','From the time that each <b>new</b> torrent is uploaded to the tracker, there is a period of time that\r\nsome users must wait before they can download it.<br>\r\nThis delay in downloading will only affect users with a low ratio, and users with low upload amounts.<br>\r\n<br>\r\nThis applies to new users as well, so opening a new account will not help. Note also that this\r\nworks at tracker level, you will be able to grab the .torrent file itself at any time.<br>\r\n<br>\r\n<!--The delay applies only to leeching, not to seeding. If you got the files from any other source and\r\nwish to seed them you may do so at any time irrespectively of your ratio or total uploaded.<br>-->\r\nN.B. Due to some users exploiting the ''no-delay-for-seeders'' policy we had to change it. The delay\r\nnow applies to both seeding and leeching. So if you are subject to a delay and get the files from\r\nsome other source you will not be able to seed them until the delay has elapsed.',2,5,8),
  (47,'item','Why do I get a \"rejected by tracker - Port xxxx is blacklisted\" error?','Your client is reporting to the tracker that it uses one of the default bittorrent ports\r\n(6881-6889) or any other common p2p port for incoming connections.<br>\r\n<br>\r\nTBDev Yuna Scatari Edition does not allow clients to use ports commonly associated with p2p protocols.\r\nThe reason for this is that it is a common practice for ISPs to throttle those ports\r\n(that is, limit the bandwidth, hence the speed). <br>\r\n<br>\r\nThe blocked ports list include, but is not neccessarily limited to, the following:<br>\r\n<br>\r\n<table cellspacing=3 cellpadding=0>\r\n  <tr>\r\n    <td class=embedded width=\"80\">Direct Connect</td>\r\n    <td class=embedded width=\"80\" bgcolor=\"#F5F4EA\"><div align=\"center\">411 - 413</div></td>\r\n  </tr>\r\n  <tr>\r\n    <td class=embedded width=\"80\">Kazaa</td>\r\n    <td class=embedded width=\"80\" bgcolor=\"#F5F4EA\"><div align=\"center\">1214</div></td>\r\n  </tr>\r\n  <tr>\r\n    <td class=embedded width=\"80\">eDonkey</td>\r\n    <td class=embedded width=\"80\" bgcolor=\"#F5F4EA\"><div align=\"center\">4662</div></td>\r\n  </tr>\r\n  <tr>\r\n    <td class=embedded width=\"80\">Gnutella</td>\r\n    <td class=embedded width=\"80\" bgcolor=\"#F5F4EA\"><div align=\"center\">6346 - 6347</div></td>\r\n  </tr>\r\n  <tr>\r\n    <td class=embedded width=\"80\">BitTorrent</td>\r\n    <td class=embedded width=\"80\" bgcolor=\"#F5F4EA\"><div align=\"center\">6881 - 6889</div></td>\r\n </tr>\r\n</table>\r\n<br>\r\nIn order to use use our tracker you must  configure your client to use\r\nany port range that does not contain those ports (a range within the region 49152 through 65535 is preferable,\r\ncf. <a class=altlink href=\"http://www.iana.org/assignments/port-numbers\">IANA</a>). Notice that some clients,\r\nlike Azureus 2.0.7.0 or higher, use a single port for all torrents, while most others use one port per open torrent. The size\r\nof the range you choose should take this into account (typically less than 10 ports wide. There\r\nis no benefit whatsoever in choosing a wide range, and there are possible security implications). <br>\r\n<br>\r\nThese ports are used for connections between peers, not client to tracker.\r\nTherefore this change will not interfere with your ability to use other trackers (in fact it\r\nshould <i>increase</i> your speed with torrents from any tracker, not just ours). Your client\r\nwill also still be able to connect to peers that are using the standard ports.\r\nIf your client does not allow custom ports to be used, you will have to switch to one that does.<br>\r\n<br>\r\nDo not ask us, or in the forums, which ports you should choose. The more random the choice is the harder\r\nit will be for ISPs to catch on to us and start limiting speeds on the ports we use.\r\nIf we simply define another range ISPs will start throttling that range also. <br>\r\n<br>\r\nFinally, remember to forward the chosen ports in your router and/or open them in your\r\nfirewall, should you have them.',1,5,9),
  (48,'item','What''s this \"IOError - [Errno13] Permission denied\" error?','If you just want to fix it reboot your computer, it should solve the problem.\r\nOtherwise read on.<br>\r\n<br>\r\nIOError means Input-Output Error, and that is a file system error, not a tracker one.\r\nIt shows up when your client is for some reason unable to open the partially downloaded\r\ntorrent files. The most common cause is two instances of the client to be running\r\nsimultaneously:\r\nthe last time the client was closed it somehow didn''t really close but kept running in the\r\nbackground, and is therefore still\r\nlocking the files, making it impossible for the new instance to open them.<br>\r\n<br>\r\nA more uncommon occurrence is a corrupted FAT. A crash may result in corruption\r\nthat makes the partially downloaded files unreadable, and the error ensues. Running\r\nscandisk should solve the problem. (Note that this may happen only if you''re running\r\nWindows 9x - which only support FAT - or NT/2000/XP with FAT formatted hard drives.\r\nNTFS is much more robust and should never permit this problem.)',1,5,10),
  (49,'item','What''s this \"TTL\" in the browse page?','The torrent''s Time To Live, in hours. It means the torrent will be deleted\r\nfrom the tracker after that many hours have elapsed (yes, even if it is still active).\r\nNote that this a maximum value, the torrent may be deleted at any time if it''s inactive.',1,5,11),
  (50,'item','Do not immediately jump on new torrents','The download speed mostly depends on the seeder-to-leecher ratio (SLR). Poor download speed is\r\nmainly a problem with new and very popular torrents where the SLR is low.<br>\r\n<br>\r\n(Proselytising sidenote: make sure you remember that you did not enjoy the low speed.\r\n<b>Seed</b> so that others will not endure the same.)<br>\r\n<br>\r\nThere are a couple of things that you can try on your end to improve your speed:<br>\r\n<br>In particular, do not do it if you have a slow connection. The best speeds will be found around the\r\nhalf-life of a torrent, when the SLR will be at its highest. (The downside is that you will not be able to seed\r\nso much. It''s up to you to balance the pros and cons of this.)',1,6,1),
  (51,'item','Limit your upload speed','The upload speed affects the download speed in essentially two ways:<br>\r\n<ul>\r\n    <li>Bittorrent peers tend to favour those other peers that upload to them. This means that if A and B\r\n    are leeching the same torrent and A is sending data to B at high speed then B will try to reciprocate.\r\n    So due to this effect high upload speeds lead to high download speeds.</li>\r\n\r\n    <li>Due to the way TCP works, when A is downloading something from B it has to keep telling B that\r\n        it received the data sent to him. (These are called acknowledgements - ACKs -, a sort of \"got it!\" messages).\r\n        If A fails to do this then B will stop sending data and wait. If A is uploading at full speed there may be no\r\n        bandwidth left for the ACKs and they will be delayed. So due to this effect excessively high upload speeds lead\r\n        to low download speeds.</li>\r\n</ul>\r\n\r\nThe full effect is a combination of the two. The upload should be kept as high as possible while allowing the\r\nACKs to get through without delay. <b>A good thumb rule is keeping the upload at about 80% of the theoretical\r\nupload speed.</b> You will have to fine tune yours to find out what works best for you. (Remember that keeping the\r\nupload high has the additional benefit of helping with your ratio.) <br>\r\n<br>\r\nIf you are running more than one instance of a client it is the overall upload speed that you must take into account.\r\nSome clients (e.g. Azureus) limit global upload speed, others (e.g. Shad0w''s) do it on a per torrent basis.\r\nKnow your client. The same applies if you are using your connection for anything else (e.g. browsing or ftp),\r\nalways think of the overall upload speed.',1,6,2),
  (52,'item','Limit the number of simultaneous connections','Some operating systems (like Windows 9x) do not deal well with a large number of connections, and may even crash.\r\nAlso some home routers (particularly when running NAT and/or firewall with stateful inspection services) tend to become\r\nslow or crash when having to deal with too many connections. There are no fixed values for this, you may try 60 or 100\r\nand experiment with the value. Note that these numbers are additive, if you have two instances of\r\na client running the numbers add up.',1,6,3),
  (53,'item','Limit the number of simultaneous uploads','Isn''t this the same as above? No. Connections limit the number of peers your client is talking to and/or\r\ndownloading from. Uploads limit the number of peers your client is actually uploading to. The ideal number is\r\ntypically much lower than the number of connections, and highly dependent on your (physical) connection.',1,6,4),
  (54,'item','Just give it some time','As explained above peers favour other peers that upload to them. When you start leeching a new torrent you have\r\nnothing to offer to other peers and they will tend to ignore you. This makes the starts slow, in particular if,\r\nby change, the peers you are connected to include few or no seeders. The download speed should increase as soon\r\nas you have some pieces to share.',1,6,5),
  (55,'item','Why is my browsing so slow while leeching?','Your download speed is always finite. If you are a peer in a fast torrent it will almost certainly saturate your\r\ndownload bandwidth, and your browsing will suffer. At the moment there is no client that allows you to limit the\r\ndownload speed, only the upload. You will have to use a third-party solution,\r\nsuch as <a class=altlink href=\"redirector.php?url=http://www.netlimiter.com/\">NetLimiter</a>.<br>\r\n<br>\r\nBrowsing was used just as an example, the same would apply to gaming, IMing, etc...',1,6,6),
  (56,'item','What is a proxy?','Basically a middleman. When you are browsing a site through a proxy your requests are sent to the proxy and the proxy\r\nforwards them to the site instead of you connecting directly to the site. There are several classifications\r\n(the terminology is far from standard):<br>\r\n<br>\r\n\r\n\r\n<table cellspacing=3 cellpadding=0>\r\n <tr>\r\n    <td class=embedded valign=\"top\" bgcolor=\"#F5F4EA\" width=\"100\">&nbsp;Transparent</td>\r\n    <td class=embedded width=\"10\">&nbsp;</td>\r\n    <td class=embedded valign=\"top\">A transparent proxy is one that needs no configuration on the clients. It works by automatically redirecting all port 80 traffic to the proxy. (Sometimes used as synonymous for non-anonymous.)</td>\r\n </tr>\r\n <tr>\r\n    <td class=embedded valign=\"top\" bgcolor=\"#F5F4EA\">&nbsp;Explicit/Voluntary</td>\r\n    <td class=embedded width=\"10\">&nbsp;</td>\r\n    <td class=embedded valign=\"top\">Clients must configure their browsers to use them.</td>\r\n </tr>\r\n <tr>\r\n    <td class=embedded valign=\"top\" bgcolor=\"#F5F4EA\">&nbsp;Anonymous</td>\r\n    <td class=embedded width=\"10\">&nbsp;</td>\r\n    <td class=embedded valign=\"top\">The proxy sends no client identification to the server. (HTTP_X_FORWARDED_FOR header is not sent; the server does not see your IP.)</td>\r\n </tr>\r\n <tr>\r\n    <td class=embedded valign=\"top\" bgcolor=\"#F5F4EA\">&nbsp;Highly Anonymous</td>\r\n    <td class=embedded width=\"10\">&nbsp;</td>\r\n    <td class=embedded valign=\"top\">The proxy sends no client nor proxy identification to the server. (HTTP_X_FORWARDED_FOR, HTTP_VIA and HTTP_PROXY_CONNECTION headers are not sent; the server doesn''t see your IP and doesn''t even know you''re using a proxy.)</td>\r\n </tr>\r\n <tr>\r\n    <td class=embedded valign=\"top\" bgcolor=\"#F5F4EA\">&nbsp;Public</td>\r\n    <td class=embedded width=\"10\">&nbsp;</td>\r\n    <td class=embedded valign=\"top\">(Self explanatory)</td>\r\n </tr>\r\n</table>\r\n<br>\r\nA transparent proxy may or may not be anonymous, and there are several levels of anonymity.',1,7,1),
  (57,'item','How do I find out if I''m behind a (transparent/anonymous) proxy?','Try <a href=http://proxyjudge.org class=\"altlink\">ProxyJudge</a>. It lists the HTTP headers that the server where it is running\r\nreceived from you. The relevant ones are HTTP_CLIENT_IP, HTTP_X_FORWARDED_FOR and REMOTE_ADDR.<br>\r\n<br>\r\n<br>\r\n<b>Why is my port listed as \"---\" even though I''m not NAT/Firewalled?</b><a name=\"prox3\"></a><br>\r\n<br>\r\nThe TBDev Yuna Scatari Edition tracker is quite smart at finding your real IP, but it does need the proxy to send the HTTP header\r\nHTTP_X_FORWARDED_FOR. If your ISP''s proxy does not then what happens is that the tracker will interpret the proxy''s IP\r\naddress as the client''s IP address. So when you login and the tracker tries to connect to your client to see if you are\r\nNAT/firewalled it will actually try to connect to the proxy on the port your client reports to be using for\r\nincoming connections. Naturally the proxy will not be listening on that port, the connection will fail and the\r\ntracker will think you are NAT/firewalled.',1,7,2),
  (58,'item','Can I bypass my ISP''s proxy?','If your ISP only allows HTTP traffic through port 80 or blocks the usual proxy ports then you would need to use something\r\nlike <a href=http://www.socks.permeo.com>socks</a> and that is outside the scope of this FAQ.<br>\r\n<br>\r\nThe site accepts connections on port 81 besides the usual 80, and using them may be enough to fool some proxies. So the first\r\nthing to try should be connecting to www.templateshares.net:81. Note that even if this works your bt client will still try\r\nto connect to port 80 unless you edit the announce url in the .torrent file.<br>\r\n<br>\r\nOtherwise you may try the following:<br>\r\n<ul>\r\n    <li>Choose any public <b>non-anonymous</b> proxy that does <b>not</b> use port 80\r\n    (e.g. from <a href=http://tools.rosinstrument.com/proxy  class=\"altlink\">this</a>,\r\n    <a href=http://www.proxy4free.com/index.html  class=\"altlink\">this</a> or\r\n    <a href=http://www.samair.ru/proxy  class=\"altlink\">this</a> list).</li>\r\n\r\n    <li>Configure your computer to use that proxy. For Windows XP, do <i>Start</i>, <i>Control Panel</i>, <i>Internet Options</i>,\r\n    <i>Connections</i>, <i>LAN Settings</i>, <i>Use a Proxy server</i>, <i>Advanced</i> and type in the IP and port of your chosen\r\n    proxy. Or from Internet Explorer use <i>Tools</i>, <i>Internet Options</i>, ...<br></li>\r\n\r\n    <li>(Facultative) Visit <a href=http://proxyjudge.org  class=\"altlink\">ProxyJudge</a>. If you see an HTTP_X_FORWARDED_FOR in\r\n    the list followed by your IP then everything should be ok, otherwise choose another proxy and try again.<br></li>\r\n\r\n    <li>Visit Template Shares. Hopefully the tracker will now pickup your real IP (check your profile to make sure).</li>\r\n</ul>\r\n<br>\r\nNotice that now you will be doing all your browsing through a public proxy, which are typically quite slow.\r\nCommunications between peers do not use port 80 so their speed will not be affected by this, and should be better than when\r\nyou were \"unconnectable\".',1,7,3),
  (59,'item','How do I make my bittorrent client use a proxy?','Just configure Windows XP as above. When you configure a proxy for Internet Explorer you''re actually configuring a proxy for\r\nall HTTP traffic (thank Microsoft and their \"IE as part of the OS policy\" ). On the other hand if you use another\r\nbrowser (Opera/Mozilla/Firefox) and configure a proxy there you''ll be configuring a proxy just for that browser. We don''t\r\nknow of any BT client that allows a proxy to be specified explicitly.',1,7,4),
  (60,'item','Why can''t I signup from behind a proxy?','It is our policy not to allow new accounts to be opened from behind a proxy.',1,7,5),
  (61,'item','Does this apply to other torrent sites?','This section was written for Template Shares, a closed, port 80-81 tracker. Other trackers may be open or closed, and many listen\r\non e.g. ports 6868 or 6969. The above does <b>not</b> necessarily apply to other trackers.',1,7,6),
  (62,'item','Maybe my address is blacklisted?','The site blocks addresses listed in the (former) <a class=altlink href=\"http://methlabs.org/\">PeerGuardian</a>\r\ndatabase, as well as addresses of banned users. This works at Apache/PHP level, it''s just a script that\r\nblocks <i>logins</i> from those addresses. It should not stop you from reaching the site. In particular\r\nit does not block lower level protocols, you should be able to ping/traceroute the server even if your\r\naddress is blacklisted. If you cannot then the reason for the problem lies elsewhere.<br>\r\n<br>\r\nIf somehow your address is indeed blocked in the PG database do not contact us about it, it is not our\r\npolicy to open <i>ad hoc</i> exceptions. You should clear your IP with the database maintainers instead.',1,8,1),
  (63,'item','Your ISP blocks the site''s address','(In first place, it''s unlikely your ISP is doing so. DNS name resolution and/or network problems are the usual culprits.)\r\n<br>\r\nThere''s nothing we can do.\r\nYou should contact your ISP (or get a new one). Note that you can still visit the site via a proxy, follow the instructions\r\nin the relevant section. In this case it doesn''t matter if the proxy is anonymous or not, or which port it listens to.<br>\r\n<br>\r\nNotice that you will always be listed as an \"unconnectable\" client because the tracker will be unable to\r\ncheck that you''re capable of accepting incoming connections.',1,8,2),
  (64,'item','Alternate port (81)','Some of our torrents use ports other than the usual HTTP port 80. This may cause problems for some users,\r\nfor instance those behind some firewall or proxy configurations.\r\n\r\nYou can easily solve this by editing the .torrent file yourself with any torrent editor, e.g.\r\n<a href=\"http://sourceforge.net/projects/burst/\" class=\"altlink\">MakeTorrent</a>,\r\nand replacing the announce url bit-torrent.kiev.ua:81 with bit-torrent.kiev.ua:80 or just templateshares.net.<br>\r\n<br>\r\nEditing the .torrent with Notepad is not recommended. It may look like a text file, but it is in fact\r\na bencoded file. If for some reason you must use a plain text editor, change the announce url to\r\nbit-torrent.kiev.ua:80, not bit-torrent.kiev.ua. (If you''re thinking about changing the number before the\r\nannounce url instead, you know too much to be reading this.)',1,8,3),
  (65,'item','You can try these:','Post in the <a class=\"altlink\" href=\"forums.php\">Forums</a>, by all means. You''ll find they\r\nare usually a friendly and helpful place,\r\nprovided you follow a few basic guidelines:\r\n<ul>\r\n<li>Make sure your problem is not really in this FAQ. There''s no point in posting just to be sent\r\nback here.\r\n<li>Before posting read the sticky topics (the ones at the top). Many times new information that\r\nstill hasn''t been incorporated in the FAQ can be found there.</li>\r\n<li>Help us in helping you. Do not just say \"it doesn''t work!\". Provide details so that we don''t\r\nhave to guess or waste time asking. What client do you use? What''s your OS? What''s your network setup? What''s the exact\r\nerror message you get, if any? What are the torrents you are having problems with? The more\r\nyou tell the easiest it will be for us, and the more probable your post will get a reply.</li>\r\n<li>And needless to say: be polite. Demanding help rarely works, asking for it usually does\r\nthe trick.',1,9,1),
  (67,'item','What is the passkey System? How does it work? ','The passkey system has been implemented in order to substitute the ip checking system. This means that the tracker doesnt check anymore your logged ip in order to verify if you are logged in or registered with the tracker. Every user has a personal passkey, a random key generated by the system. When a user tries to download a torrent, its personal passkey is imprinted in the tracker url of the torrent, allowing to the tracker to identify any source connected on it. In this way, you can seed a torrent for example, at home and at your office simultaneously without any problem with the 2 different ips. Per torrent 3 simultaneous connections are permitted per user, and in case of leeching only 1 (That means you can leech a torrent from one location only at a time.',1,5,13),
  (68,'item','Why do i get a \"Unknown Passkey\" error? ','You will get this error, firstly if you are not registered on our tracker, or if you havent downloaded the torrent to use from our webpage, when you were logged in. In this case, just register or log in and redownload the torrent.\r\n\r\nThere is a chance to get this error also, at the first time you download anything as a new user, or at the first download after you reset your passkey. The reason is simply that the tracker reviews the changes in the passkeys every few minutes and not instantly. For that reason just leave the torrent running for a few minutes, and you will get eventually an OK message from the tracker.',1,5,14),
  (69,'item','When do i need to reset my passkey? ','<ul><li> If your passkey has been leeched and other user(s) uses it to download torrents using your account. In this case, you will see torrents stated in your account that you are not leeching or seeding .</li>\r\n<li> When your clients hangs up or your connection is terminated without pressing the stop button of your client. In this case, in your account you will see that you are still leeching/seeding the torrents even that your client has been closed. Normally these \"ghost peers\" will be cleaned automatically within 30 minutes, but if you want to resume your downloads and the tracker denied that due to the fact that you \"already are downloading the same torrents - Connection limit error\" then you should reset your passkey and redownload the torrent, then resume it. </li></ul>',1,5,15),
  (70,'item','What is DHT and Why must i turn it off?','DHT must be disabled in your client, DHT can cause your stats to be recorded incorrectly and could be seen as cheating also disable PEX (peer exchange) Anyone using this will be banned for cheating the system. Check your snatchlist regularly to ensure stats are being recorded correctly, allow 30mins for the tracker to update your stats. ',1,5,15),
  (71,'item','Recommended Clients','<b>Cross-Platform:</b><br>\r\nAzureus<br>\r\nBitTornado<br>\r\n<br>\r\n<b>Window Users:</b><br>\r\nµTorrent<br>\r\nABC<br>\r\n<br>\r\n<b>Mac Users:</b><br>\r\nTomato Torrent<br>\r\nBitRocket (lastest version)<br>\r\nrtorrent<br>\r\n<br>\r\n<b>Linux Users:</b><br>\r\nrtorrent<br>\r\nktorrent<br>\r\ndeluge<br>',1,1,4);

COMMIT;

#
# Data for the `orbital_blocks` table  (LIMIT 0,100)
#

INSERT INTO `orbital_blocks` (`bid`, `bkey`, `title`, `content`, `bposition`, `weight`, `active`, `time`, `blockfile`, `view`, `expire`, `action`, `which`) VALUES
  (1,'','Администрация','<table border=\"0\"><tr>\r\n<td class=\"block\"><a href=\"admincp.php\">Админка</a></td>\r\n</tr><tr>\r\n<td class=\"block\"><a href=\"users.php\">Список пользователей</a></td>\r\n</tr><tr>\r\n<td class=\"block\"><a href=\"staffmess.php\">Массовое ЛС</a></td>\r\n</tr><tr>\r\n<td class=\"block\"><a href=\"ipcheck.php\">Двойники по IP</a></td>\r\n</tr><tr>\r\n<td class=\"block\"><a href=\"logout.php\">Выйти</a></td>\r\n</tr></table>','r',1,1,'','',2,'0','d','all'),
  (8,'','Статистика','','c',7,1,'','block-stats.php',0,'0','d','ihome,'),
  (9,'','Релизы, которым нужны раздающие','','c',6,1,'','block-helpseed.php',0,'0','d','ihome,'),
  (10,'','Напоминание о правилах','<p align=\"jsutify\">Администрация данного сайта - прирожденные садисты и кровопийцы, которые только и ищут повод помучать и поиздеваться над пользователями, используя для этого самые изощренные пытки. Единственный способ избежать этого - не попадаться нам на глаза, то есть спокойно качать и раздавать, поддерживая свой рейтинг как можно ближе к 1, и не делать глупых комментариев к торрентам. И не говорите, что мы вас не предупреждали! (шутка)</p>','c',1,1,'','',0,'0','d','rules,'),
  (2,'','Новости','','c',3,1,'','block-news.php',0,'0','d','ihome,'),
  (3,'','Пользователи','','r',2,1,'','block-online.php',0,'0','d','all'),
  (4,'','Поиск','','r',3,1,'','block-search.php',0,'0','d','all'),
  (5,'','Опрос','','c',4,1,'','block-polls.php',1,'0','d','ihome,'),
  (6,'','Релизы','','c',5,1,'','block-releases.php',0,'0','d','ihome,'),
  (11,'','Загрузка сервера','','c',8,1,'','block-server_load.php',0,'0','d','ihome,');

COMMIT;