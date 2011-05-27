-- phpMyAdmin SQL Dump
-- version 3.4.0
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Ven 27 Mai 2011 à 11:08
-- Version du serveur: 5.5.8
-- Version de PHP: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `mmmb_user`
--

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `uid` int(10) unsigned NOT NULL,
  `sid` varchar(64) NOT NULL DEFAULT '',
  `hostname` varchar(128) NOT NULL DEFAULT '',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `session` longtext,
  PRIMARY KEY (`sid`),
  KEY `timestamp` (`timestamp`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `sessions`
--

INSERT INTO `sessions` (`uid`, `sid`, `hostname`, `timestamp`, `session`) VALUES
(0, '1bfklr0tkdcnag0pqbb52476k6', '::1', 1306172237, ''),
(0, '1t6qi62mg9s03bpr0mp24b7ki5', '::1', 1305559080, ''),
(0, 'ipl3i81q28m7qfj17d0svcqod7', '::1', 1305560169, ''),
(0, '3ssvrhkjfpbb4gnm2qk29sgfl7', '::1', 1305559158, '');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `mail` varchar(128) NOT NULL,
  `language` varchar(3) DEFAULT NULL,
  `timezone` varchar(8) DEFAULT NULL,
  `data` longtext,
  `created` int(11) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL DEFAULT '0',
  `login` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `Unique username` (`name`),
  UNIQUE KEY `mail` (`mail`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`uid`, `rid`, `name`, `pass`, `mail`, `language`, `timezone`, `data`, `created`, `access`, `login`, `status`) VALUES
(0, 0, 'guest', '', '', NULL, NULL, NULL, 0, 0, 0, 0),
(1, 1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@example.com', 'fr', '0', 'a:0:{}', 0, 1303911538, 1303918952, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
