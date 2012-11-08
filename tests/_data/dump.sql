-- phpMyAdmin SQL Dump
-- version 3.4.10.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 14, 2012 at 09:58 AM
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `role_id`, `login`, `password`, `salt`, `token`, `tokenValidUntil`, `lastLoginTime`, `created`, `modified`) VALUES
(1, 4, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'f11ded6f0b2b', NULL, NULL, NULL, '2012-05-14 09:58:36', NULL);

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
  `slug` varchar(128) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `serviceCount` int(11) NOT NULL,
  `lft` int(11) NOT NULL,
  `lvl` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `root` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3AF34668989D9B62` (`slug`),
  KEY `IDX_3AF34668727ACA70` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `created`, `serviceCount`, `lft`, `lvl`, `rgt`, `root`) VALUES
(1, NULL, 'root', 'root', NULL, '2012-05-14 09:58:36', 0, 1, 0, 8, 1),
(2, 1, 'Zabiegi chirugiczne', 'zabiegi-chirugiczne', NULL, '2012-05-14 09:58:36', 0, 2, 1, 3, 1),
(3, 1, 'Pobyty SPA', 'pobyty-spa', NULL, '2012-05-14 09:58:36', 0, 4, 1, 5, 1),
(4, 1, 'Pobyty w sanatoriach', 'pobyty-w-sanatoriach', NULL, '2012-05-14 09:58:36', 0, 6, 1, 7, 1);

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
  `description` varchar(255) DEFAULT NULL,
  `customPromos` varchar(255) DEFAULT NULL,
  `roleName` varchar(255) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `logoDir` varchar(255) DEFAULT NULL,
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
  UNIQUE KEY `UNIQ_D7053B66989D9B62` (`slug`),
  KEY `IDX_D7053B66D60322AC` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `clinics`
--

INSERT INTO `clinics` (`id`, `role_id`, `name`, `streetaddress`, `province`, `city`, `postcode`, `repPhone`, `repName`, `repEmail`, `type`, `wantBill`, `country`, `geoLat`, `getLon`, `description`, `customPromos`, `roleName`, `slug`, `logoDir`, `login`, `password`, `salt`, `token`, `tokenValidUntil`, `lastLoginTime`, `created`, `modified`) VALUES
(1, 2, 'Trendmed', 'Rubinowa 4', 'Pomorskie', 'Gdańsk', '80-033', '+48512129709', 'Bartosz Rychlicki', 'b@br-design.pl', 'Klinika', 0, 'Poland', NULL, NULL, 'To jest pierwsza klinika testowa w systemie', NULL, 'clinic', 'trendmed-gdansk', NULL, 'b@br-design.pl', '0699e4ecbaf51e6672d49fac95c48ae7', '2bd09c6f1caa', NULL, NULL, NULL, '2012-05-14 09:58:36', NULL);

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
  `clinic_id` int(11) DEFAULT NULL,
  `photoDir` varchar(255) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B72F0566CC22AD4` (`clinic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ext_translations`
--

CREATE TABLE IF NOT EXISTS `ext_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` varchar(8) NOT NULL,
  `object_class` varchar(255) NOT NULL,
  `field` varchar(32) NOT NULL,
  `foreign_key` varchar(64) NOT NULL,
  `content` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lookup_unique_idx` (`locale`,`object_class`,`foreign_key`,`field`),
  KEY `translations_lookup_idx` (`locale`,`object_class`,`foreign_key`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `ext_translations`
--

INSERT INTO `ext_translations` (`id`, `locale`, `object_class`, `field`, `foreign_key`, `content`) VALUES
(1, 'en_GB', 'Trendmed\\Entity\\Category', 'name', '2', 'English name'),
(2, 'de_DE', 'Trendmed\\Entity\\Category', 'name', '2', 'Germanishe version'),
(3, 'en_GB', 'Trendmed\\Entity\\Category', 'name', '3', 'English name'),
(4, 'de_DE', 'Trendmed\\Entity\\Category', 'name', '3', 'Germanishe version'),
(5, 'en_GB', 'Trendmed\\Entity\\Category', 'name', '4', 'English name'),
(6, 'de_DE', 'Trendmed\\Entity\\Category', 'name', '4', 'Germanishe version');

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
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE IF NOT EXISTS `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'guest'),
(2, 'clinic'),
(3, 'patient'),
(4, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `pricemin` varchar(255) NOT NULL,
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
  ADD CONSTRAINT `FK_3AF34668727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `clinics`
--
ALTER TABLE `clinics`
  ADD CONSTRAINT `FK_D7053B66D60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `clinic_photos`
--
ALTER TABLE `clinic_photos`
  ADD CONSTRAINT `FK_B72F0566CC22AD4` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `FK_2CCC2E2CD60322AC` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
