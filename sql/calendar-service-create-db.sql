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
  `googleAccessToken` varchar(256),
  `googleRefreshToken` varchar(256),
  `googleExpiresIn` varchar(128),
  `googleTokenType` varchar(128),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1$$