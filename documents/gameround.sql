-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- 主機: localhost:2819
-- 建立日期: Oct 18, 2013, 11:25 AM
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- 
-- 列出以下資料庫的數據： `auth`
-- 

INSERT INTO `auth` VALUES (1, 1, 1, 'iOCqq7ILpG_1_1_1');
INSERT INTO `auth` VALUES (7, 2, 1, '9hH49rwJec_2_1_1');
INSERT INTO `auth` VALUES (8, 14, 1, 'oWzQHqaxTs_14_1_1');

-- --------------------------------------------------------

-- 
-- 資料表格式： `event`
-- 

CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('message') NOT NULL,
  `receiverId` int(11) NOT NULL,
  `roomId` int(11) NOT NULL,
  `param` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `receiverId` (`receiverId`),
  KEY `roomId` (`roomId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 列出以下資料庫的數據： `event`
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

INSERT INTO `gameinfo` VALUES (1, 'EzWebCheckers', 'KlfQcRgxmNzzrjZRtH', '1234');

-- --------------------------------------------------------

-- 
-- 資料表格式： `gameroom`
-- 

CREATE TABLE `gameroom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameId` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `turn` int(11) DEFAULT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  `status` enum('wait','start') NOT NULL,
  `playingList` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gameId` (`gameId`),
  KEY `turn` (`turn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- 列出以下資料庫的數據： `gameroom`
-- 

INSERT INTO `gameroom` VALUES (1, 1, 'test', 14, 2, 2, 'start', '1-14-2');
INSERT INTO `gameroom` VALUES (2, 1, 'test', NULL, 2, 2, 'wait', NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- 
-- 列出以下資料庫的數據： `gauth`
-- 

INSERT INTO `gauth` VALUES (1, 1, 'lDjP7JWzzozD_1');
INSERT INTO `gauth` VALUES (2, 1, 'PjJfmnJHFApr_1');
INSERT INTO `gauth` VALUES (3, 1, 'vfZoIdST6Oyd_1');
INSERT INTO `gauth` VALUES (4, 1, 'MN6pkisWwGdk_1');
INSERT INTO `gauth` VALUES (5, 1, 'kuPQnJXR55e2_1');
INSERT INTO `gauth` VALUES (6, 1, 'yu9NMGh1S5s7_1');
INSERT INTO `gauth` VALUES (7, 1, 'oXnw2d8DPXjm_1');
INSERT INTO `gauth` VALUES (8, 1, 'njV3eGSimJxu_1');
INSERT INTO `gauth` VALUES (9, 1, '18ZCFeM5tUuU_1');
INSERT INTO `gauth` VALUES (10, 1, 'm6u0DuCjkWSa_1');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- 
-- 列出以下資料庫的數據： `room_to_user`
-- 

INSERT INTO `room_to_user` VALUES (5, 1, 1);
INSERT INTO `room_to_user` VALUES (8, 1, 14);
INSERT INTO `room_to_user` VALUES (9, 1, 2);
INSERT INTO `room_to_user` VALUES (10, 2, 14);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- 
-- 列出以下資料庫的數據： `user`
-- 

INSERT INTO `user` VALUES (1, 'keming', 'keming', '1234');
INSERT INTO `user` VALUES (2, 'gary', 'gary', '1234');
INSERT INTO `user` VALUES (14, '123', '123', '123');

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
-- 資料表限制 `event`
-- 
ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_2` FOREIGN KEY (`roomId`) REFERENCES `gameroom` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`receiverId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- 資料表限制 `gameroom`
-- 
ALTER TABLE `gameroom`
  ADD CONSTRAINT `gameroom_ibfk_1` FOREIGN KEY (`gameId`) REFERENCES `gameinfo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gameroom_ibfk_2` FOREIGN KEY (`turn`) REFERENCES `user` (`id`);

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
