DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `uid` int(10) unsigned NOT NULL,
  `sid` varchar(64) NOT NULL DEFAULT '',
  `hostname` varchar(128) NOT NULL DEFAULT '',
  `timestamp` int(11) unsigned NOT NULL DEFAULT '0',
  `session` longtext,
  PRIMARY KEY (`sid`),
  KEY `timestamp` (`timestamp`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL,
  `pass` varchar(40) NOT NULL,
  `mail` varchar(128) NOT NULL,
  `language` varchar(3) DEFAULT NULL,
  `timezone` varchar(8) DEFAULT NULL,
  `data` longtext,
  `created` int(11) unsigned DEFAULT NULL,
  `access` int(11) unsigned DEFAULT NULL,
  `login` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `mail` (`mail`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `users` (`uid`, `rid`, `name`, `pass`, `mail`, `language`, `timezone`, `data`, `created`, `access`, `login`, `status`) VALUES
(0, 0, 'guest', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 0);
UPDATE `users` SET `uid` = 0;
INSERT INTO `users` (`uid`, `rid`, `name`, `pass`, `mail`, `language`, `timezone`, `data`, `created`, `access`, `login`, `status`) VALUES
(1, 1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin@example.com', 'fr', '0', 'a:0:{}', 1303911530, 1303911538, 1303918952, 1);

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `data` text NOT NULL,
  `created` int(11) unsigned NOT NULL DEFAULT '0',
  `expire` int(11) unsigned NOT NULL DEFAULT '0',
  `serialized` smallint(1) NOT NULL DEFAULT '0',

  `iid` int(11) unsigned DEFAULT NULL,
  `uid` int(11) unsigned DEFAULT NULL,
  `rid` int(11) unsigned DEFAULT NULL,
  `class` varchar(128) DEFAULT NULL,
  `language` varchar(3) DEFAULT NULL,
  `url` varchar(128) DEFAULT NULL,

  KEY `iid` (`iid`),
  KEY `uid` (`uid`),
  KEY `rid` (`rid`),
  KEY `class` (`class`),
  KEY `language` (`language`),
  KEY `url` (`url`),
  UNIQUE KEY `unique_cache_item` (`iid`, `uid`, `rid`, `class`, `language`, `url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;