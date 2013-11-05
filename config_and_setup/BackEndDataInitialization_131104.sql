-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 04, 2013 at 06:07 PM
-- Server version: 5.1.72
-- PHP Version: 5.3.2-1ubuntu4.21

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `db_test`
--

-- 

--
-- Table structure for table `gratuity_log`
--

DROP TABLE IF EXISTS `gratuity_log`;
CREATE TABLE IF NOT EXISTS `gratuity_log` (
  `recordSeq` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Unique Record Id',
  `studyId` bigint(20) unsigned NOT NULL DEFAULT '0',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of page with link',
  `comments` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'URL of link destination',
  PRIMARY KEY (`recordSeq`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Initial data for table `gratuity_log`
--

INSERT INTO `gratuity_log` (`recordSeq`, `studyId`, `email`, `comments`) VALUES
(2, 1234, 'you@me.com', 'I hope this really works!!!!'),
(3, 1234, 'you@me.com', 'I hope this really works!!!!'),
(4, 1234, 'you@me2.com', 'I hope this really works!!!!');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=525 ;

--
-- Sample data for table `log_transition`
--

INSERT INTO `log_transition` (`recordSeq`, `serverTimestamp`, `clientTimestamp`, `recordType`, `sessionId`, `taskId`, `conditionId`, `fromUrl`, `toUrl`, `linkClass`, `linkId`, `linkTag`) VALUES
(327, '2013-10-01 17:04:56', 1380672382815, 'transition', 1380672271, 1, 3, 'http://students.washington.edu/rbwatson/hearts.html', 'http://students.washington.edu/rbwatson/hearts.html#home', NULL, NULL, NULL),
(328, '2013-10-01 17:04:57', 1380672384014, 'transition', 1380672271, 1, 3, 'http://students.washington.edu/rbwatson/hearts.html#home', 'http://students.washington.edu/rbwatson/hearts.html#l1', NULL, NULL, NULL),
(329, '2013-10-01 17:05:01', 1380672387332, 'transition', 1380672271, 1, 3, 'http://students.washington.edu/rbwatson/hearts.html#l1', 'http://wlux.uw.edu/rbwatson/task-finish.php?wlux_session=1380672271&wlux_task=1', 'wlux_button_link', NULL, NULL);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=233 ;

--
-- Dumping data for table `session_config`
--

INSERT INTO `session_config` (`recordSeq`, `studyId`, `sessionId`, `taskId`, `conditionId`, `conditionCssUrl`, `taskBarCssUrl`, `startUrl`, `returnUrl`, `buttonText`, `tabShowText`, `tabHideText`, `taskText`, `taskHtml`, `startPageHtml`, `finishPageHtml`, `startPageNextUrl`, `finishPageNextUrl`, `measuredTask`, `taskType`, `autoConditionId`) VALUES
(232, 1234, 1383605381, 2, 3, 'css/style3.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the second task to do.', NULL, '<p>These are the instructions for Task 2, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 2, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=457 ;

--
-- Dumping data for table `session_log`
--

INSERT INTO `session_log` (`recordSeq`, `studyId`, `sessionId`, `taskId`, `conditionId`, `startTime`, `endTime`) VALUES
(455, 1234, 1383605381, 1, 3, '2013-11-04 14:50:08', '2013-11-04 14:50:13'),
(456, 1234, 1383605381, 2, 3, '2013-11-04 14:50:15', NULL);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `study_config`
--

INSERT INTO `study_config` (`recordSeq`, `studyId`, `sessionId`, `taskId`, `conditionId`, `conditionCssUrl`, `taskBarCssUrl`, `startUrl`, `returnUrl`, `buttonText`, `tabShowText`, `tabHideText`, `taskText`, `taskHtml`, `startPageHtml`, `finishPageHtml`, `startPageNextUrl`, `finishPageNextUrl`, `measuredTask`, `taskType`) VALUES
(1, 1234, 0, 1, 1, 'css/style1.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<p>These are the instructions for Task 1, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 1, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(2, 1234, 0, 1, 2, 'css/style2.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<p>These are the instructions for Task 1, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 1, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(3, 1234, 0, 1, 3, 'css/style3.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<p>These are the instructions for Task 1, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 1, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(4, 1234, 0, 1, 4, 'css/style4.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<p>These are the instructions for Task 1, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 1, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(5, 1234, 0, 2, 1, 'css/style1.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the second task to do.', NULL, '<p>These are the instructions for Task 2, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 2, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(6, 1234, 0, 2, 2, 'css/style2.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the second task to do.', NULL, '<p>These are the instructions for Task 2, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 2, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(7, 1234, 0, 2, 3, 'css/style3.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the second task to do.', NULL, '<p>These are the instructions for Task 2, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 2, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(8, 1234, 0, 2, 4, 'css/style4.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the second task to do.', NULL, '<p>These are the instructions for Task 2, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 2, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/spades.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(9, 1234, 0, 3, 1, 'css/style1.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the third task to do.', NULL, '<p>These are the instructions for Task 3, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 3, Condition 1.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(10, 1234, 0, 3, 2, 'css/style2.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the third task to do.', NULL, '<p>These are the instructions for Task 3, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 3, Condition 2.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(11, 1234, 0, 3, 3, 'css/style3.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the third task to do.', NULL, '<p>These are the instructions for Task 3, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 3, Condition 3.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(12, 1234, 0, 3, 4, 'css/style4.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the third task to do.', NULL, '<p>These are the instructions for Task 3, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', '<p>You just finished Task 3, Condition 4.<br/>However, we wouldn''t show the condition to the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(13, 2525, 0, 1, 1, 'css/style1.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/end-task.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<script type="text/javascript">		\nfunction listener(event){\n  if ( event.origin !== "http://www.surveygizmo.com" ) {\n	return;\n  }	  \n  if ("surveyStarted" == event.data) {\n	  // hide the continue button\n	  var d = document.getElementById("continueFormDiv");\n	  d.style.display = "none";\n  } else if ("surveyComplete" == event.data) {\n	  // show the continue button\n	  var i = document.getElementById("surveyFrame");\n	  i.height = "400";\n	  var d = document.getElementById("continueFormDiv");\n	  d.style.display = "block";\n  } else {\n	  // do nothing\n  }\n}\n</script>\n<p>Please answer the questions below and then press continue.</p>\n<div id="surveyDiv" style="margin-left:auto; margin-right:auto; width:700px;"> </div>\n<script type="text/javascript">\n	document.getElementById("surveyDiv").innerHTML = "<iframe id=\\"surveyFrame\\" src=\\"http://www.surveygizmo.com/s3/1350906/pretest?studyid=" + sessionInfo.studyId + "&conditionid=" + sessionInfo.conditionId + "&sessionid=" + sessionInfo.sessionId + "\\" frameborder=\\"0\\" width=\\"700\\" height=\\"2400\\" ></iframe>";	\n	if (window.addEventListener){\n	  addEventListener("message", listener, false);\n	} else {\n	  attachEvent("onmessage", listener);\n	}\n</script>', '<p>You just finished Task 1, Condition 1.', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 0, 'single'),
(14, 2525, 0, 1, 4, 'css/style1.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/end-task.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<script type="text/javascript">		\nfunction listener(event){\n  if ( event.origin !== "http://www.surveygizmo.com" ) {\n	return;\n  }	  \n  if ("surveyStarted" == event.data) {\n	  // hide the continue button\n	  var d = document.getElementById("continueFormDiv");\n	  d.style.display = "none";\n  } else if ("surveyComplete" == event.data) {\n	  // show the continue button\n	  var i = document.getElementById("surveyFrame");\n	  i.height = "400";\n	  var d = document.getElementById("continueFormDiv");\n	  d.style.display = "block";\n  } else {\n	  // do nothing\n  }\n}\n</script>\n<p>Please answer the questions below and then press continue.</p>\n<div id="surveyDiv" style="margin-left:auto; margin-right:auto; width:700px;"> </div>\n<script type="text/javascript">\n	document.getElementById("surveyDiv").innerHTML = "<iframe id=\\"surveyFrame\\" src=\\"http://www.surveygizmo.com/s3/1350906/pretest?studyid=" + sessionInfo.studyId + "&conditionid=" + sessionInfo.conditionId + "&sessionid=" + sessionInfo.sessionId + "\\" frameborder=\\"0\\" width=\\"700\\" height=\\"2400\\" ></iframe>";	\n	if (window.addEventListener){\n	  addEventListener("message", listener, false);\n	} else {\n	  attachEvent("onmessage", listener);\n	}\n</script>\n', '<p>You just finished Task 1, Condition 1.', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 0, 'single'),
(15, 2525, 0, 1, 3, 'css/style1.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/end-task.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<script type="text/javascript">		\nfunction listener(event){\n  if ( event.origin !== "http://www.surveygizmo.com" ) {\n	return;\n  }	  \n  if ("surveyStarted" == event.data) {\n	  // hide the continue button\n	  var d = document.getElementById("continueFormDiv");\n	  d.style.display = "none";\n  } else if ("surveyComplete" == event.data) {\n	  // show the continue button\n	  var i = document.getElementById("surveyFrame");\n	  i.height = "400";\n	  var d = document.getElementById("continueFormDiv");\n	  d.style.display = "block";\n  } else {\n	  // do nothing\n  }\n}\n</script>\n<p>Please answer the questions below and then press continue.</p>\n<div id="surveyDiv" style="margin-left:auto; margin-right:auto; width:700px;"> </div>\n<script type="text/javascript">\n	document.getElementById("surveyDiv").innerHTML = "<iframe id=\\"surveyFrame\\" src=\\"http://www.surveygizmo.com/s3/1350906/pretest?studyid=" + sessionInfo.studyId + "&conditionid=" + sessionInfo.conditionId + "&sessionid=" + sessionInfo.sessionId + "\\" frameborder=\\"0\\" width=\\"700\\" height=\\"2400\\" ></iframe>";	\n	if (window.addEventListener){\n	  addEventListener("message", listener, false);\n	} else {\n	  attachEvent("onmessage", listener);\n	}\n</script>\n', '<p>You just finished Task 1, Condition 1.', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 0, 'single'),
(16, 2525, 0, 1, 2, 'css/style1.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/rbwatson/end-task.php', 'End task', 'Show', 'Hide', 'This is the first task to do.', NULL, '<script type="text/javascript">		\nfunction listener(event){\n  if ( event.origin !== "http://www.surveygizmo.com" ) {\n	return;\n  }	  \n  if ("surveyStarted" == event.data) {\n	  // hide the continue button\n	  var d = document.getElementById("continueFormDiv");\n	  d.style.display = "none";\n  } else if ("surveyComplete" == event.data) {\n	  // show the continue button\n	  var i = document.getElementById("surveyFrame");\n	  i.height = "400";\n	  var d = document.getElementById("continueFormDiv");\n	  d.style.display = "block";\n  } else {\n	  // do nothing\n  }\n}\n</script>\n<p>Please answer the questions below and then press continue.</p>\n<div id="surveyDiv" style="margin-left:auto; margin-right:auto; width:700px;"> </div>\n<script type="text/javascript">\n	document.getElementById("surveyDiv").innerHTML = "<iframe id=\\"surveyFrame\\" src=\\"http://www.surveygizmo.com/s3/1350906/pretest?studyid=" + sessionInfo.studyId + "&conditionid=" + sessionInfo.conditionId + "&sessionid=" + sessionInfo.sessionId + "\\" frameborder=\\"0\\" width=\\"700\\" height=\\"2400\\" ></iframe>";	\n	if (window.addEventListener){\n	  addEventListener("message", listener, false);\n	} else {\n	  attachEvent("onmessage", listener);\n	}\n</script>\n', '<p>You just finished Task 1, Condition 1.', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 0, 'single'),
(17, 2001, 0, 1, 1, 'css/style1.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the first and only task to do.', NULL, '<h1>Minimal Study Template - Protocol Step 1</h1>\r\n<p>These are the instructions for the study task.</p>\r\n\r\n<p>For this task, you will be shown the rules for a couple of card games. Read the rules until you feel that you understand the games, and then press <b>End task</b> to finish the task.</p>\r\n\r\n<p>Press <b>Continue</b> to start the task.</p>', '<p>You just finished the study task.</p>\r\n<p>You could put a post-task questionnaire, here, if you wanted to capture some feedback from the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external'),
(18, 1999, 0, 3, 1, 'about:blank', 'about:blank', 'about:blank', 'about:blank', 'Not used', 'Not used', 'Not used', 'Not used. The fields that are null, contain "about:blank" or "Not used" are ignored in a single-page task.', NULL, '<h1>Simple Study Template - Protocol Step 3</h1>\r\n<p>There''s nothing to do on this page, but you could take a survey, if you wanted. Press continue to finish this study.</p>', 'Not used', 'about:blank', 'http://wlux.uw.edu/demo/task-start.php', 0, 'single'),
(19, 1999, 0, 1, 1, 'about:blank', 'about:blank', 'about:blank', 'about:blank', 'Not used', 'Not used', 'Not used', 'Not used. The fields that are null, contain "about:blank" or "Not used" are ignored in a single-page task.', NULL, '<h1>Simple Study Template - Protocol Step 1</h1>\r\n<p>Welcome to the simple study.</p>\r\n<p>Press continue to go to the next step.</p>', 'Not used', 'about:blank', 'http://wlux.uw.edu/demo/task-start.php', 0, 'single'),
(20, 1999, 0, 2, 1, 'css/style1.css', 'http://wlux.uw.edu/common/wluxTaskBar.css', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-finish.php', 'End task', 'Show', 'Hide', 'This is the first and only task to do.', NULL, '<h1>Simple Study Template - Protocol Step 2</h1>\r\n<p>These are the instructions for the study task.</p>\r\n\r\n<p>For this task, you will be shown the rules for a couple of card games. Read the rules until you feel that you understand the games, and then press <b>End task</b> to finish the task.</p>\r\n\r\n<p>Press <b>Continue</b> to start the task.</p>', '<p>You just finished the study task.</p>\r\n<p>You could put a post-task questionnaire, here, if you wanted to capture some feedback from the participant.</p>', 'http://students.washington.edu/rbwatson/hearts.html', 'http://wlux.uw.edu/demo/task-start.php', 1, 'external');
