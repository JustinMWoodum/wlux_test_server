-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 28, 2013 at 07:44 PM
-- Server version: 5.1.70
-- PHP Version: 5.3.2-1ubuntu4.20

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db_test`
--

USE db_test;
-- --------------------------------------------------------

--
-- Table structure for table `study_config`
--

DROP TABLE IF EXISTS `study_config`;
CREATE TABLE IF NOT EXISTS `study_config` (
  `recordSeq` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'record Id used by MySQL',
  `studyId` bigint(20) unsigned DEFAULT NULL COMMENT 'Study ID (optional)',
  `sessionId` bigint(20) unsigned DEFAULT NULL COMMENT 'Session ID (optional)',
  `taskId` int(10) unsigned NOT NULL DEFAULT '0',
  `conditionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Test condition ID',
  `conditionCssUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of CSS that corresponds to this condition',
  `taskBarCssUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'CSS to style task bar',
  `startUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of page to start from when participant clicks the "begin task" button',
  `returnUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of page to return to when participant clicks "end task" button',
  `buttonText` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'End task' COMMENT 'Text in "end task" button',
  `tabShowText` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'Show' COMMENT 'Text in "show" tab',
  `tabHideText` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'Hide' COMMENT 'Text in "hide" tab',
  `taskText` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Task description text (plain-text only)',
  `taskHtml` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Task text, formatted with HTML. If present, overrides taskText field',
  PRIMARY KEY (`recordSeq`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `study_config`
--

INSERT INTO `study_config` (`recordSeq`, `studyId`, `sessionId`, `taskId`, `conditionId`, `conditionCssUrl`, `taskBarCssUrl`, `startUrl`, `returnUrl`, `buttonText`, `tabShowText`, `tabHideText`, `taskText`, `taskHtml`) VALUES
(1, 1234, 0, 1, 1, 'css/style1.css', 'http://localhost/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://localhost/rbwatson/end.php', 'End task', 'Show', 'Hide', 'This is the task to do.', 'NULL'),
(2, 1234, 0, 1, 2, 'css/style2.css', 'http://localhost/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://localhost/rbwatson/end.php', 'End task', 'Show', 'Hide', 'This is the task to do.', 'NULL'),
(3, 1234, 0, 1, 3, 'css/style3.css', 'http://localhost/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://localhost/rbwatson/end.php', 'End task', 'Show', 'Hide', 'This is the task to do.', 'NULL'),
(4, 1234, 0, 1, 4, 'css/style4.css', 'http://localhost/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://localhost/rbwatson/end.php', 'End task', 'Show', 'Hide', 'This is the task to do.', 'NULL');
