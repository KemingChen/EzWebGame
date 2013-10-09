-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- 主機: 127.0.0.1
-- 建立日期: Oct 09, 2013, 04:23 PM
-- 伺服器版本: 6.0.4
-- PHP 版本: 6.0.0-dev

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 資料庫: `gameround`
-- 

-- --------------------------------------------------------

-- 
-- 資料表格式： `command`
-- 

CREATE TABLE `command` (
  `id` int(11) NOT NULL,
  `roomId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `timestamp` date NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `roomId` (`roomId`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `gameName` (`gameName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- 列出以下資料庫的數據： `gameinfo`
-- 


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
-- 資料表格式： `room_to_user`
-- 

CREATE TABLE `room_to_user` (
  `id` int(11) NOT NULL,
  `roomId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `roomId` (`roomId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- 列出以下資料庫的數據： `room_to_user`
-- 


-- --------------------------------------------------------

-- 
-- 資料表格式： `user`
-- 

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `userName` varchar(100) NOT NULL,
  `account` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userName` (`userName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- 列出以下資料庫的數據： `user`
-- 


-- 
-- 備份資料表限制
-- 

-- 
-- 資料表限制 `gameroom`
-- 
ALTER TABLE `gameroom`
  ADD CONSTRAINT `gameroom_ibfk_1` FOREIGN KEY (`gameId`) REFERENCES `gameinfo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- 資料表限制 `room_to_user`
-- 
ALTER TABLE `room_to_user`
  ADD CONSTRAINT `room_to_user_ibfk_1` FOREIGN KEY (`roomId`) REFERENCES `gameroom` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_to_user_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE;
