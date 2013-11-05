-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 27, 2013 at 02:34 AM
-- Server version: 5.5.25
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `db_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `gratuity_log`
--

DROP TABLE IF EXISTS `gratuity_log`;
CREATE TABLE `gratuity_log` (
  `recordSeq` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Record Id',
  `studyId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of page with link',
  `comments` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of link destination',
  PRIMARY KEY (`recordSeq`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
