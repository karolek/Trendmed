-- phpMyAdmin SQL Dump
-- version 3.4.10.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 02, 2012 at 05:15 PM
-- Server version: 5.5.20
-- PHP Version: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `trendmed`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `tokenValidUntil` datetime DEFAULT NULL,
  `lastLoginTime` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A2E0150FD60322AC` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `role_id`, `login`, `password`, `salt`, `token`, `tokenValidUntil`, `lastLoginTime`, `created`, `modified`) VALUES
(3, 2, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'eef53570d983', NULL, NULL, NULL, '2012-05-01 18:37:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `serviceCount` int(11) NOT NULL,
  `depth` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3AF34668727ACA70` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `clinics`
--

CREATE TABLE IF NOT EXISTS `clinics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `streetaddress` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `repPhone` varchar(255) NOT NULL,
  `repName` varchar(255) NOT NULL,
  `repEmail` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `wantBill` tinyint(1) NOT NULL,
  `country` varchar(255) NOT NULL,
  `geoLat` varchar(255) DEFAULT NULL,
  `getLon` varchar(255) DEFAULT NULL,
  `roleName` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `tokenValidUntil` datetime DEFAULT NULL,
  `lastLoginTime` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D7053B66B98A836A` (`repEmail`),
  KEY `IDX_D7053B66D60322AC` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `clinics`
--

INSERT INTO `clinics` (`id`, `role_id`, `name`, `streetaddress`, `province`, `city`, `postcode`, `repPhone`, `repName`, `repEmail`, `type`, `wantBill`, `country`, `geoLat`, `getLon`, `roleName`, `login`, `password`, `salt`, `token`, `tokenValidUntil`, `lastLoginTime`, `created`, `modified`) VALUES
(1, 1, 'TrendMed', 'Topolowa 2/7', 'Pomorskie', 'Gdańsk', '80-233', '+48 512 129 709', 'Bartosz', 'b@br-design.pl', 'Clinic', 0, 'Poland', '54.377608', '18.595605', 'clinic', 'b@br-design.pl', '0699e4ecbaf51e6672d49fac95c48ae7', '39338', NULL, NULL, NULL, '2012-05-01 16:15:59', NULL),
(5, 1, 'Trendmed', 'Topolowa 2', '1', 'Gdańsk', '80-255', '512129709', 'bartosz', 'bartosz.rychlicki@gmail.com', 'clinic', 0, 'Poland', NULL, NULL, 'clinic', 'bartosz.rychlicki@gmail.com', '0699e4ecbaf51e6672d49fac95c48ae7', '3e97c39b19d3', NULL, NULL, NULL, '2012-05-02 17:13:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `clinic_descriptions`
--

CREATE TABLE IF NOT EXISTS `clinic_descriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `clinic_photos`
--

CREATE TABLE IF NOT EXISTS `clinic_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `group_reservations`
--

CREATE TABLE IF NOT EXISTS `group_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invitations`
--

CREATE TABLE IF NOT EXISTS `invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE IF NOT EXISTS `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `string` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `tokenValidUntil` datetime DEFAULT NULL,
  `lastLoginTime` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2CCC2E2CD60322AC` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rateings`
--

CREATE TABLE IF NOT EXISTS `rateings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'clinic'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `service_photo`
--

CREATE TABLE IF NOT EXISTS `service_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `FK_A2E0150FD60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `FK_3AF34668727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `clinics`
--
ALTER TABLE `clinics`
  ADD CONSTRAINT `FK_D7053B66D60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `FK_2CCC2E2CD60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
