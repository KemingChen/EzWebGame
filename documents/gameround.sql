-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- 主機: localhost:2819
-- 建立日期: Oct 14, 2013, 07:56 PM
-- 伺服器版本: 6.0.4
-- PHP 版本: 6.0.0-dev

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 資料庫: `gameround`
-- 

-- --------------------------------------------------------

-- 
-- 資料表格式： `auth`
-- 

CREATE TABLE `auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `gameId` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `gameId` (`gameId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 列出以下資料庫的數據： `auth`
-- 


-- --------------------------------------------------------

-- 
-- 資料表格式： `command`
-- 

CREATE TABLE `command` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `timestamp` date NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `roomId` (`roomId`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 列出以下資料庫的數據： `command`
-- 


-- --------------------------------------------------------

-- 
-- 資料表格式： `gameinfo`
-- 

CREATE TABLE `gameinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameName` varchar(100) NOT NULL,
  `gKey` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gameName` (`gameName`),
  UNIQUE KEY `gKey` (`gKey`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- 列出以下資料庫的數據： `gameinfo`
-- 

INSERT INTO `gameinfo` VALUES (1, 'EzWebCheckers', 'dRh5vBMbnh', '12345');

-- --------------------------------------------------------

-- 
-- 資料表格式： `gameroom`
-- 

CREATE TABLE `gameroom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameId` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `turn` int(11) NOT NULL,
  `limitTime` int(11) NOT NULL,
  `timestamp` date NOT NULL,
  `mode` int(11) NOT NULL DEFAULT '0',
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gameId` (`gameId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 列出以下資料庫的數據： `gameroom`
-- 


-- --------------------------------------------------------

-- 
-- 資料表格式： `gauth`
-- 

CREATE TABLE `gauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameId` int(11) NOT NULL,
  `key` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cKey` (`key`),
  KEY `gameId` (`gameId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 列出以下資料庫的數據： `gauth`
-- 


-- --------------------------------------------------------

-- 
-- 資料表格式： `room_to_user`
-- 

CREATE TABLE `room_to_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roomId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `roomId` (`roomId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 列出以下資料庫的數據： `room_to_user`
-- 


-- --------------------------------------------------------

-- 
-- 資料表格式： `user`
-- 

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(100) NOT NULL,
  `account` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userName` (`userName`),
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- 
-- 列出以下資料庫的數據： `user`
-- 

INSERT INTO `user` VALUES (1, 'keming', 'keming', '1234');
INSERT INTO `user` VALUES (2, 'gary', 'gary62107', '123');

-- 
-- 備份資料表限制
-- 

-- 
-- 資料表限制 `auth`
-- 
ALTER TABLE `auth`
  ADD CONSTRAINT `auth_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_ibfk_2` FOREIGN KEY (`gameId`) REFERENCES `gameinfo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- 資料表限制 `gameroom`
-- 
ALTER TABLE `gameroom`
  ADD CONSTRAINT `gameroom_ibfk_1` FOREIGN KEY (`gameId`) REFERENCES `gameinfo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- 資料表限制 `gauth`
-- 
ALTER TABLE `gauth`
  ADD CONSTRAINT `gauth_ibfk_1` FOREIGN KEY (`gameId`) REFERENCES `gameinfo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- 資料表限制 `room_to_user`
-- 
ALTER TABLE `room_to_user`
  ADD CONSTRAINT `room_to_user_ibfk_1` FOREIGN KEY (`roomId`) REFERENCES `gameroom` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_to_user_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE;
