DROP TABLE IF EXISTS `files`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `users_actions`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `watchdog`;

CREATE TABLE `files` (
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `hash` varchar(32) NOT NULL,
  `posted` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(15) NOT NULL,
  `size` bigint(20) NOT NULL,
  `content` longblob NOT NULL,
  `tmp` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`fid`),
  UNIQUE KEY `hash` (`hash`),
  KEY `user_id` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sessions` (
  `uid` int(10) unsigned NOT NULL COMMENT 'The users.uid corresponding to a session, or 0 for anonymous user.',
  `sid` varchar(128) NOT NULL COMMENT 'A session ID. The value is generated by Drupal’s session handlers.',
  `hostname` varchar(128) NOT NULL DEFAULT '' COMMENT 'The IP address that last used this session ID (sid).',
  `timestamp` int(11) NOT NULL DEFAULT '0' COMMENT 'The Unix timestamp when this session last requested a page. Old records are purged by PHP automatically.',
  `session` longblob COMMENT 'The serialized contents of $_SESSION, an array of name/value pairs that persists across page requests by this session ID. Drupal loads $_SESSION from here at the start of each request and saves it at the end.',
  PRIMARY KEY (`sid`),
  KEY `timestamp` (`timestamp`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) unsigned NOT NULL,
  `mail` varchar(128) NOT NULL,
  `pass` varchar(40) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `language` varchar(5) DEFAULT NULL,
  `timezone` varchar(32) DEFAULT NULL,
  `data` longtext,
  `created` int(11) unsigned DEFAULT NULL,
  `access` int(11) unsigned DEFAULT NULL,
  `login` int(11) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `users_actions` (
  `uid` int(10) unsigned NOT NULL,
  `action` varchar(32) NOT NULL,
  `salt` varchar(32) NOT NULL,
  `time` int(11) NOT NULL,
  UNIQUE KEY `user_action` (`uid`,`action`),
  UNIQUE KEY `unique_salt` (`salt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `watchdog` (
  `id` varchar(128) DEFAULT NULL,
  `hostname` varchar(128) NOT NULL DEFAULT '',
  `count` int(10) unsigned NOT NULL DEFAULT '1',
  `timestamp` varchar(11) NOT NULL DEFAULT '',
  KEY `action_source` (`id`,`hostname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`uid`, `rid`, `mail`, `pass`, `name`, `language`, `timezone`, `data`, `created`, `access`, `login`, `status`) VALUES
(0, 0, '', '', 'Guest', NULL, '', NULL, NULL, NULL, NULL, 0),
(1, 1, 'admin@example.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrator', 'en', 'Europe/Brussels', NULL, NULL, NULL, NULL, 1);

ALTER TABLE `files`
  ADD CONSTRAINT `file_owner` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sessions`
  ADD CONSTRAINT `user_session` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `users_actions`
  ADD CONSTRAINT `user_action` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
