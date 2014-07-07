-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2014 at 03:17 PM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forcremation`
--

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE IF NOT EXISTS `billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `monthly_fee` float NOT NULL,
  `lead_fee` float NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `monthly_fee`, `lead_fee`, `created_at`, `updated_at`) VALUES
(1, 90, 0, '2014-04-21 17:05:38', '2014-04-21 14:58:29');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deceased_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `relationship` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `street_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `apartment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remains_plan` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cremate_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `groups_name_unique` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'Admin', '{"admin":1}', '2014-04-06 15:53:47', '2014-04-06 15:53:47'),
(2, 'Provider', '{"provider":1}', '2014-04-06 15:53:47', '2014-04-06 15:53:47');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2012_12_06_225921_migration_cartalyst_sentry_install_users', 1),
('2012_12_06_225929_migration_cartalyst_sentry_install_groups', 1),
('2012_12_06_225945_migration_cartalyst_sentry_install_users_groups_pivot', 1),
('2012_12_06_225988_migration_cartalyst_sentry_install_throttle', 1),
('2014_04_13_085226_create_customers_table', 2),
('2014_04_21_053900_create_provider_table', 2),
('2014_04_21_075708_create_providerzips_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE IF NOT EXISTS `providers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provider` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` text COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `zip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `service_radius` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `user_id`, `provider`, `address`, `city`, `state`, `zip`, `website`, `phone`, `fax`, `service_radius`, `created_at`, `updated_at`) VALUES
(1, 16, 'Test', 'lsdkjfalksdfj', 'aslkdf', 'sd', '34', 'http://www.aaa.com', '238947', '234', '5', '0000-00-00 00:00:00', '2014-04-21 02:55:30'),
(2, 20, 'ForCremation3', 'jalksdfj', 'askdljf', 'asdfkj', '34', 'http://www.aaa.com', '238947', '234', '10', '0000-00-00 00:00:00', '2014-04-21 07:55:31'),
(3, 21, 'Platinum', 'lkasdjflkasdf j', 'jsdkfjsd', '234', '3437', 'http://www.abc.com', '238947', '234', '5', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `providerzips`
--

CREATE TABLE IF NOT EXISTS `providerzips` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `zip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `providerzips`
--

INSERT INTO `providerzips` (`id`, `provider_id`, `zip`, `created_at`, `updated_at`) VALUES
(1, 1, '1234', '2014-04-21 07:59:29', '2014-04-21 07:59:29'),
(2, 1, '2345', '2014-04-21 07:59:38', '2014-04-21 07:59:38');

-- --------------------------------------------------------

--
-- Table structure for table `throttle`
--

CREATE TABLE IF NOT EXISTS `throttle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `ip_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attempts` int(11) NOT NULL DEFAULT '0',
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `last_attempt_at` timestamp NULL DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `banned_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `throttle_user_id_index` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `throttle`
--

INSERT INTO `throttle` (`id`, `user_id`, `ip_address`, `attempts`, `suspended`, `banned`, `last_attempt_at`, `suspended_at`, `banned_at`) VALUES
(1, 1, '::1', 0, 0, 0, '2014-04-20 20:03:01', '2014-04-20 20:03:01', NULL),
(2, 2, '::1', 0, 0, 0, NULL, NULL, NULL),
(3, 20, '::1', 0, 0, 0, NULL, NULL, NULL),
(4, 16, '::1', 0, 0, 0, '2014-05-04 20:21:46', '2014-05-04 20:21:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `activation_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `persist_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reset_password_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` enum('admin','provider','customer') COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_activation_code_index` (`activation_code`),
  KEY `users_reset_password_code_index` (`reset_password_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=22 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `permissions`, `activated`, `activation_code`, `activated_at`, `last_login`, `persist_code`, `reset_password_code`, `first_name`, `last_name`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'fikri.desertlion@gmail.com', '$2y$10$axa1vbECRmMHVrvZpQOzQujgtuTfVhdKVKnLf.bA8bPiGO3XTsVBC', NULL, 1, NULL, NULL, '2014-05-04 15:59:11', '$2y$10$YJJliqFxi.MDK1O7ebeBzuLcqq1OtzlUo3eZ5OLTCGQZ.6VlsNNOm', NULL, 'Rijalul', 'Fikri', 'admin', NULL, '2014-04-20 20:28:22', '2014-05-04 20:13:50'),
(5, 'test2@test.com', '$2y$10$axa1vbECRmMHVrvZpQOzQujgtuTfVhdKVKnLf.bA8bPiGO3XTsVBC', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'provider', NULL, '2014-04-20 23:09:22', '2014-04-20 23:09:22'),
(12, 'test3@test.com', '$2y$10$WPX1HvqvWl9EwbCuqCkAT.jogeD/oDnNPW.FVwRosd4tS37KFNIXG', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'provider', NULL, '2014-04-20 23:20:53', '2014-04-20 23:20:53'),
(14, 'tes4t@test.com', '$2y$10$pqO4LHDYJ7Fq4qtzKBXhRuTfKaJXs3WhcmJbYFYgxXM7njfWUSLZO', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'provider', NULL, '2014-04-20 23:22:48', '2014-04-20 23:22:48'),
(16, 'test@test.com', '$2y$10$axa1vbECRmMHVrvZpQOzQujgtuTfVhdKVKnLf.bA8bPiGO3XTsVBC', NULL, 1, NULL, NULL, '2014-05-04 20:22:49', '$2y$10$3C3JFmHL/Hoz0gUCviaN5.ySeKk8OU9HDU/3GDxB/TfqPbz1mbO8K', NULL, NULL, NULL, 'provider', NULL, '2014-04-20 23:24:48', '2014-05-04 20:22:49'),
(18, 'test5@test.com', '$2y$10$ZyZ1bhX0gmvs36HjQLYTvexZiJj/q1nMTIEIBJcmSYkGkCHaxtz6K', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'provider', NULL, '2014-04-20 23:29:12', '2014-04-20 23:29:12'),
(19, 'test6@test.com', '$2y$10$Ohq4NYGBzhMt98xvZhG0.u6tkhQaxnuL2FoOsdm3lC2wTnC5IqGKO', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'provider', NULL, '2014-04-20 23:30:54', '2014-04-20 23:30:54'),
(20, 'john@doe.com', '$2y$10$FelvAx7S7meNvEvpQtCPwOcJQIe.4pJrlxK6fmuFN7aPi/2mZaDaW', NULL, 1, NULL, NULL, '2014-04-21 08:00:26', '$2y$10$vArdPWtfcyHEvXqJoJlM0uL115Vt291wSlFcqIEXM6x.nCcTzRcAS', NULL, NULL, NULL, 'provider', NULL, '2014-04-20 23:54:17', '2014-04-21 08:00:26'),
(21, 'platinum@test.com', '$2y$10$egwTHYH7dxmdd.bzzZbdkeicH5XyXlTvvbTiHOY5sqrOSjpZFP10.', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'provider', NULL, '2014-04-21 07:57:01', '2014-04-21 07:57:01');

-- --------------------------------------------------------

--
-- Table structure for table `users_groups`
--

CREATE TABLE IF NOT EXISTS `users_groups` (
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_groups`
--

INSERT INTO `users_groups` (`user_id`, `group_id`) VALUES
(2, 1),
(5, 2),
(12, 2),
(14, 2),
(16, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
