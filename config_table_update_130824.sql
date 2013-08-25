-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 24, 2013 at 07:27 PM
-- Server version: 5.1.70
-- PHP Version: 5.3.2-1ubuntu4.20

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `db_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `log_transition`
--

DROP TABLE IF EXISTS `log_transition`;
CREATE TABLE IF NOT EXISTS `log_transition` (
  `recordSeq` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Record Id',
  `serverTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'time record was added to the database',
  `clientTimestamp` bigint(20) DEFAULT NULL COMMENT 'record date and time sent from client',
  `recordType` enum('open','transition') NOT NULL,
  `sessionId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `taskId` int(10) unsigned NOT NULL DEFAULT '0',
  `conditionId` int(10) unsigned NOT NULL DEFAULT '0',
  `fromUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of page with link',
  `toUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of link destination',
  `linkClass` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'class attribute of link',
  `linkId` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ID attribute of link',
  `linkTag` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'class attribute of link',
  PRIMARY KEY (`recordSeq`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=197 ;


-- --------------------------------------------------------

--
-- Table structure for table `session_config`
--

DROP TABLE IF EXISTS `session_config`;
CREATE TABLE IF NOT EXISTS `session_config` (
  `recordSeq` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'record Id used by MySQL',
  `studyId` bigint(20) unsigned DEFAULT NULL COMMENT 'Study ID (optional)',
  `sessionId` bigint(20) unsigned DEFAULT NULL COMMENT 'Session ID (optional)',
  `taskId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'session task ID',
  `conditionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Test condition ID',
  `conditionCssUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of CSS that corresponds to this condition',
  `taskBarCssUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'CSS to style task bar',
  `startUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of page to start from when participant clicks "begin task" button',
  `returnUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of page to return to when participant clicks "end task" button',
  `buttonText` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'End task' COMMENT 'Text in "end task" button',
  `tabShowText` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'Show' COMMENT 'Text in "show" tab',
  `tabHideText` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'Hide' COMMENT 'Text in "hide" tab',
  `taskText` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Task description text (plain-text only)',
  `taskHtml` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'Task text, formatted with HTML. If present, overrides taskText field',
  `startPageHtml` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `finishPageHtml` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `startPageNextUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `finishPageNextUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `measuredTask` int(10) unsigned NOT NULL DEFAULT '1',
  `taskType` enum('single','external') NOT NULL DEFAULT 'external',
  `autoConditionId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '*test flag* to auto increment conditionId value instead of using condition set by WebLabUX',
  PRIMARY KEY (`recordSeq`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=72 ;


-- --------------------------------------------------------

--
-- Table structure for table `session_log`
--

DROP TABLE IF EXISTS `session_log`;
CREATE TABLE IF NOT EXISTS `session_log` (
  `recordSeq` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `studyId` bigint(20) unsigned DEFAULT NULL,
  `sessionId` bigint(20) unsigned DEFAULT NULL,
  `taskId` int(10) unsigned NOT NULL DEFAULT '0',
  `conditionId` bigint(20) unsigned DEFAULT NULL,
  `startTime` datetime DEFAULT NULL,
  `endTime` datetime DEFAULT NULL,
  PRIMARY KEY (`recordSeq`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=160 ;

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
  `startPageHtml` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `finishPageHtml` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `startPageNextUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `finishPageNextUrl` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `measuredTask` int(10) unsigned NOT NULL DEFAULT '1',
  `taskType` enum('single','external') NOT NULL DEFAULT 'external',
  PRIMARY KEY (`recordSeq`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `study_config`
--

INSERT INTO `study_config` (`recordSeq`, `studyId`, `sessionId`, `taskId`, `conditionId`, `conditionCssUrl`, `taskBarCssUrl`, `startUrl`, `returnUrl`, `buttonText`, `tabShowText`, `tabHideText`, `taskText`, `taskHtml`, `startPageHtml`, `finishPageHtml`, `startPageNextUrl`, `finishPageNextUrl`, `measuredTask`, `taskType`) VALUES
(5, 1234, 0, 1, 1, 'css/style1.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<p>These are the instructions for Task 1, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 1, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external'),
(6, 1234, 0, 1, 2, 'css/style2.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<p>These are the instructions for Task 1, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 1, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external'),
(7, 1234, 0, 1, 3, 'css/style3.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<p>These are the instructions for Task 1, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 1, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external'),
(8, 1234, 0, 1, 4, 'css/style4.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<p>These are the instructions for Task 1, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 1, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external'),
(9, 1234, 0, 2, 1, 'css/style1.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the second task to do.', NULL, '<p>These are the instructions for Task 2, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 2, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external'),
(10, 1234, 0, 2, 2, 'css/style2.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the second task to do.', NULL, '<p>These are the instructions for Task 2, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 2, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external'),
(11, 1234, 0, 2, 3, 'css/style3.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the second task to do.', NULL, '<p>These are the instructions for Task 2, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 2, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external'),
(12, 1234, 0, 2, 4, 'css/style4.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the second task to do.', NULL, '<p>These are the instructions for Task 2, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 2, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external'),
(13, 1234, 0, 3, 1, 'css/style1.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the third task to do.', NULL, '<p>These are the instructions for Task 3, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 3, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external'),
(14, 1234, 0, 3, 2, 'css/style2.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the third task to do.', NULL, '<p>These are the instructions for Task 3, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 3, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external'),
(15, 1234, 0, 3, 3, 'css/style3.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the third task to do.', NULL, '<p>These are the instructions for Task 3, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 3, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external'),
(16, 1234, 0, 3, 4, 'css/style4.css', 'http://wlux.uw.edu/rbwatson/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-finish.php', 'End task', 'Show', 'Hide', 'This is the third task to do.', NULL, '<p>These are the instructions for Task 3, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 3, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/task-start.php', 1, 'external');
