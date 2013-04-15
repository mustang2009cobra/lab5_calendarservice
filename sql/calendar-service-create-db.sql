delimiter $$

CREATE DATABASE `calendar_service` /*!40100 DEFAULT CHARACTER SET latin1 */$$

delimiter $$

USE `calendar_service`$$

delimiter $$

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `signalESL` varchar(512) NOT NULL,
  `googleAccessToken` varchar(256),
  `googleRefreshToken` varchar(256),
  `googleExpiresIn` varchar(128),
  `googleTokenType` varchar(128),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `calendars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `googleId` varchar(256) NOT NULL,
  `summary` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1$$

delimiter $$

CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `calendarId` int(11) NOT NULL,
  `googleId` varchar(256) NOT NULL,
  `summary` varchar(512) NOT NULL,
  `htmlLink` varchar(512),
  `created` varchar(128) NOT NULL,
  `updated` varchar(128) NOT NULL,
  `start` varchar(128) NOT NULL,
  `end` varchar(128) NOT NULL,
  `allDayEvent` tinyint(1) NOT NULL,
  `iCalUID` varchar(512) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1$$