 ************************************************************
# Sequel Pro SQL dump
# Versão 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.1.38-MariaDB-0+deb9u1)
# Base de Dados: asterisk
# Tempo de Geração: 2019-05-28 18:18:55 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump da tabela agent_status
# ------------------------------------------------------------

DROP TABLE IF EXISTS `agent_status`;

CREATE TABLE `agent_status` (
	  `agentId` varchar(40) NOT NULL DEFAULT '',
	  `agentName` varchar(40) DEFAULT NULL,
	  `agentStatus` varchar(30) DEFAULT NULL,
	  `timestamp` timestamp NULL DEFAULT NULL,
	  `callid` double(18,6) unsigned DEFAULT '0.000000',
	  `queue` varchar(20) DEFAULT NULL,
	  PRIMARY KEY (`agentId`),
	  KEY `agentName` (`agentName`),
	  KEY `agentStatus` (`agentStatus`,`timestamp`,`callid`),
	  KEY `queue` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump da tabela call_status
# ------------------------------------------------------------

DROP TABLE IF EXISTS `call_status`;

CREATE TABLE `call_status` (
	  `callId` double(18,6) NOT NULL,
	  `callerId` varchar(13) NOT NULL,
	  `status` varchar(30) NOT NULL,
	  `timestamp` timestamp NULL DEFAULT NULL,
	  `queue` varchar(25) NOT NULL,
	  `agent` varchar(32) NOT NULL DEFAULT '''''',
	  `position` varchar(11) NOT NULL,
	  `originalPosition` varchar(11) NOT NULL,
	  `holdtime` varchar(11) NOT NULL,
	  `keyPressed` varchar(11) NOT NULL,
	  `callduration` int(11) NOT NULL,
	  PRIMARY KEY (`callId`),
	  KEY `callerId` (`callerId`),
	  KEY `status` (`status`),
	  KEY `timestamp` (`timestamp`),
	  KEY `queue` (`queue`),
	  KEY `position` (`position`,`originalPosition`,`holdtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump da tabela cdr
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cdr`;

CREATE TABLE `cdr` (
	  `calldate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `clid` varchar(80) NOT NULL DEFAULT '',
	  `src` varchar(80) NOT NULL DEFAULT '',
	  `dst` varchar(80) NOT NULL DEFAULT '',
	  `dcontext` varchar(80) NOT NULL DEFAULT '',
	  `channel` varchar(80) NOT NULL DEFAULT '',
	  `dstchannel` varchar(80) NOT NULL DEFAULT '',
	  `lastapp` varchar(80) NOT NULL DEFAULT '',
	  `lastdata` varchar(80) NOT NULL DEFAULT '',
	  `duration` int(11) NOT NULL DEFAULT '0',
	  `billsec` int(11) NOT NULL DEFAULT '0',
	  `disposition` varchar(45) NOT NULL DEFAULT '',
	  `amaflags` int(11) NOT NULL DEFAULT '0',
	  `accountcode` varchar(20) NOT NULL DEFAULT '',
	  `uniqueid` varchar(32) NOT NULL DEFAULT '',
	  `userfield` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;



# Dump da tabela queue_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `queue_log`;

CREATE TABLE `queue_log` (
	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `time` char(26) NOT NULL DEFAULT '',
	  `callid` varchar(32) NOT NULL DEFAULT '',
	  `queuename` varchar(32) NOT NULL DEFAULT '',
	  `agent` varchar(32) NOT NULL DEFAULT '',
	  `event` varchar(32) NOT NULL DEFAULT '',
	  `data` varchar(255) NOT NULL DEFAULT '',
	  `data1` varchar(255) NOT NULL DEFAULT '',
	  `data2` varchar(255) NOT NULL DEFAULT '',
	  `data3` varchar(255) NOT NULL DEFAULT '',
	  `data4` varchar(255) NOT NULL DEFAULT '',
	  `data5` varchar(255) NOT NULL DEFAULT '',
	  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`asterisk`@`localhost` */ /*!50003 TRIGGER `bi_queueEvents` BEFORE INSERT ON `queue_log` FOR EACH ROW BEGIN
IF NEW.event = 'ADDMEMBER' THEN
INSERT INTO agent_status (agentId,agentStatus,timestamp,callid,queue) VALUES (NEW.agent,'READY',NEW.time,NULL,NEW.queuename) ON DUPLICATE KEY UPDATE agentStatus = "READY", timestamp = NEW.time, callid = NULL, queue = NEW.queuename;
ELSEIF NEW.event = 'REMOVEMEMBER' THEN
INSERT INTO `agent_status` (agentId,agentStatus,timestamp,callid,queue) VALUES (NEW.agent,'LOGGEDOUT',NEW.time,NULL,NEW.queuename) ON DUPLICATE KEY UPDATE agentStatus = "LOGGEDOUT", timestamp = NEW.time, callid = NULL, queue = NEW.queuename;
ELSEIF NEW.event = 'AGENTLOGIN' THEN
INSERT INTO `agent_status` (agentId,agentStatus,timestamp,callid,queue) VALUES (NEW.agent,'LOGGEDIN',NEW.time,NULL,NEW.queuename) ON DUPLICATE KEY UPDATE agentStatus = "LOGGEDIN", timestamp = NEW.time, callid = NULL, queue = NEW.queuename;
ELSEIF NEW.event = 'AGENTLOGOFF' THEN
INSERT INTO `agent_status` (agentId,agentStatus,timestamp,callid,queue) VALUES (NEW.agent,'LOGGEDOUT',NEW.time,NULL,NEW.queuename) ON DUPLICATE KEY UPDATE agentStatus = "LOGGEDOUT", timestamp = NEW.time, callid = NULL, queue = NEW.queuename;
ELSEIF NEW.event = 'PAUSE' THEN
INSERT INTO agent_status (agentId,agentStatus,timestamp,callid,queue) VALUES (NEW.agent,'PAUSE',NEW.time,NULL,NEW.queuename) ON DUPLICATE KEY UPDATE agentStatus = "PAUSE", timestamp = NEW.time, callid = NULL, queue = NEW.queuename;
ELSEIF NEW.event = 'UNPAUSE' THEN
INSERT INTO `agent_status` (agentId,agentStatus,timestamp,callid,queue) VALUES (NEW.agent,'READY',NEW.time,NULL,NEW.queuename) ON DUPLICATE KEY UPDATE agentStatus = "READY", timestamp = NEW.time, callid = NULL, queue = NEW.queuename;
ELSEIF NEW.event = 'ENTERQUEUE' THEN
REPLACE INTO `call_status` VALUES
(NEW.callid,NEW.data2,
'inQue',
NEW.time,
NEW.queuename,
'',
'',
'',
'',
'',
0);
ELSEIF NEW.event = 'CONNECT' THEN
UPDATE `call_status` SET
callid = NEW.callid,
status = NEW.event,
timestamp = NEW.time,
queue = NEW.queuename,
holdtime = NEW.data1,
agent = NEW.agent
where callid = NEW.callid;
INSERT INTO agent_status (agentId,agentStatus,timestamp,callid,queue) VALUES
(NEW.agent,NEW.event,
NEW.time,
NEW.callid,
NEW.queuename)
ON DUPLICATE KEY UPDATE
agentStatus = NEW.event,
timestamp = NEW.time,
callid = NEW.callid,
queue = NEW.queuename;
ELSEIF NEW.event in ('COMPLETECALLER','COMPLETEAGENT') THEN
UPDATE `call_status` SET
callid = NEW.callid,
status = NEW.event,
timestamp = NEW.time,
queue = NEW.queuename,
originalPosition = NEW.data3,
holdtime = NEW.data1,
callduration = NEW.data2,
agent = NEW.agent
where callid = NEW.callid;
INSERT INTO agent_status (agentId,agentStatus,timestamp,callid,queue) VALUES (NEW.agent,NEW.event,NEW.time,NULL,NEW.queuename) ON DUPLICATE KEY UPDATE agentStatus = "READY", timestamp = NEW.time, callid = NULL, queue = NEW.queuename;
ELSEIF NEW.event in ('TRANSFER') THEN
UPDATE `call_status` SET
callid = NEW.callid,
status = NEW.event,
timestamp = NEW.time,
queue = NEW.queuename,
holdtime = NEW.data1,
callduration = NEW.data3,
agent = NEW.agent
where callid = NEW.callid;
INSERT INTO agent_status (agentId,agentStatus,timestamp,callid,queue) VALUES
(NEW.agent,'READY',NEW.time,NULL,NEW.queuename)
ON DUPLICATE KEY UPDATE
agentStatus = "READY",
timestamp = NEW.time,
callid = NULL,
queue = NEW.queuename;
ELSEIF NEW.event in ('ABANDON','EXITEMPTY') THEN
UPDATE `call_status` SET
callid = NEW.callid,
status = NEW.event,
timestamp = NEW.time,
queue = NEW.queuename,
position = NEW.data1,
originalPosition = NEW.data2,
holdtime = NEW.data3,
agent = NEW.agent
where callid = NEW.callid;
ELSEIF NEW.event = 'EXITWITHKEY' THEN
UPDATE `call_status` SET
callid = NEW.callid,
status = NEW.event,
timestamp = NEW.time,
queue = NEW.queuename,
position = NEW.data2,
keyPressed = NEW.data1,
agent = NEW.agent
where callid = NEW.callid;
ELSEIF NEW.event = 'EXITWITHTIMEOUT' THEN
UPDATE `call_status` SET
callid = NEW.callid,
status = NEW.event,
timestamp = NEW.time,
queue = NEW.queuename,
position = NEW.data1,
agent = NEW.agent
where callid = NEW.callid;
END IF;
END */;;
/*!50003 SET SESSION SQL_MODE="NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`asterisk`@`localhost` */ /*!50003 TRIGGER `update_processed` AFTER INSERT ON `queue_log` FOR EACH ROW BEGIN
INSERT INTO queue_log_processed (callid,queuename,agentdev,event,data1,data2,data3,datetime)
VALUES (NEW.callid,NEW.queuename,NEW.agent,NEW.event,NEW.data1,NEW.data2,NEW.data3,NEW.time);
END */;;
DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE */;


# Dump da tabela queue_log_processed
# ------------------------------------------------------------

DROP TABLE IF EXISTS `queue_log_processed`;

CREATE TABLE `queue_log_processed` (
	  `recid` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `origid` int(10) unsigned NOT NULL,
	  `callid` varchar(32) NOT NULL DEFAULT '',
	  `queuename` varchar(32) NOT NULL DEFAULT '',
	  `agentdev` varchar(32) NOT NULL,
	  `event` varchar(32) NOT NULL DEFAULT '',
	  `data1` varchar(128) NOT NULL,
	  `data2` varchar(128) NOT NULL,
	  `data3` varchar(128) NOT NULL,
	  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	  PRIMARY KEY (`recid`),
	  KEY `data1` (`data1`),
	  KEY `data2` (`data2`),
	  KEY `data3` (`data3`),
	  KEY `event` (`event`),
	  KEY `queuename` (`queuename`),
	  KEY `callid` (`callid`),
	  KEY `datetime` (`datetime`),
	  KEY `agentdev` (`agentdev`),
	  KEY `origid` (`origid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
