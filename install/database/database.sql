/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 100130
Source Host           : localhost:3306
Source Database       : cshield

Target Server Type    : MYSQL
Target Server Version : 100130
File Encoding         : 65001

Date: 2019-02-12 22:15:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for game_generators
-- ----------------------------
DROP TABLE IF EXISTS `game_generators`;
CREATE TABLE `game_generators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instanceID` int(11) NOT NULL,
  `referenceID` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0 offline 1 online',
  PRIMARY KEY (`id`),
  KEY `generator_instanceid` (`instanceID`),
  KEY `generator_ref` (`referenceID`),
  CONSTRAINT `generator_instanceid` FOREIGN KEY (`instanceID`) REFERENCES `instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `generator_ref` FOREIGN KEY (`referenceID`) REFERENCES `model_generators` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of game_generators
-- ----------------------------

-- ----------------------------
-- Table structure for game_settings
-- ----------------------------
DROP TABLE IF EXISTS `game_settings`;
CREATE TABLE `game_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instanceID` int(11) NOT NULL,
  `referenceID` int(11) DEFAULT NULL,
  `settingname` varchar(255) NOT NULL,
  `settingvalue` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settings_ref` (`referenceID`),
  KEY `settings_instanceid` (`instanceID`),
  CONSTRAINT `settings_instanceid` FOREIGN KEY (`instanceID`) REFERENCES `instances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `settings_ref` FOREIGN KEY (`referenceID`) REFERENCES `model_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of game_settings
-- ----------------------------

-- ----------------------------
-- Table structure for instances
-- ----------------------------
DROP TABLE IF EXISTS `instances`;
CREATE TABLE `instances` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Instance ID.',
  `sessionID` varchar(255) NOT NULL,
  `timestarted` datetime NOT NULL,
  `timeactive` datetime NOT NULL,
  `ip` varchar(100) DEFAULT NULL,
  `useragent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `instancesessionid` (`sessionID`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of instances
-- ----------------------------

-- ----------------------------
-- Table structure for model_generators
-- ----------------------------
DROP TABLE IF EXISTS `model_generators`;
CREATE TABLE `model_generators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0 offline 1 online',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of model_generators
-- ----------------------------
INSERT INTO `model_generators` VALUES ('1', 'Generator 1', '0');
INSERT INTO `model_generators` VALUES ('2', 'Generator 2', '0');
INSERT INTO `model_generators` VALUES ('3', 'Generator 3', '0');
INSERT INTO `model_generators` VALUES ('4', 'Generator 4 (backup)', '1');
INSERT INTO `model_generators` VALUES ('5', 'Generator 5 (backup)', '1');

-- ----------------------------
-- Table structure for model_settings
-- ----------------------------
DROP TABLE IF EXISTS `model_settings`;
CREATE TABLE `model_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `settingname` varchar(255) NOT NULL,
  `settingvalue` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of model_settings
-- ----------------------------
INSERT INTO `model_settings` VALUES ('1', 'mainframestatus', '0', '0 - off, 1 - on');
INSERT INTO `model_settings` VALUES ('2', 'mainframeloggedin', '0', '0 - not logged in, 1 - logged in');
