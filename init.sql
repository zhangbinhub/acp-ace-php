/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50623
Source Host           : localhost:3306
Source Database       : acp

Target Server Type    : MYSQL
Target Server Version : 50623
File Encoding         : 65001

Date: 2018-05-04 09:16:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for t_application
-- ----------------------------
DROP TABLE IF EXISTS `t_application`;
CREATE TABLE `t_application` (
  `id` varchar(36) NOT NULL,
  `appname` varchar(255) NOT NULL,
  `copyright_begin` varchar(4) NOT NULL,
  `copyright_end` varchar(4) DEFAULT NULL,
  `copyright_owner` varchar(255) NOT NULL,
  `dbno` int(11) NOT NULL,
  `defaultapp` int(11) NOT NULL,
  `language` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `version` varchar(255) NOT NULL,
  `webroot` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_application
-- ----------------------------
INSERT INTO `t_application` VALUES ('f80136dc-49de-11e8-addd-00155d1c0117', 'Acp-admin', '2017', '', 'zb', '0', '1', 'zh-CN', '0', '0', '3.0', 'admin');
INSERT INTO `t_application` VALUES ('f8325f0c-49de-11e8-addd-00155d1c0117', 'Acp', '2017', '', 'zb', '0', '1', 'zh-CN', '1', '1', '3.0', 'portal');

-- ----------------------------
-- Table structure for t_application_info
-- ----------------------------
DROP TABLE IF EXISTS `t_application_info`;
CREATE TABLE `t_application_info` (
  `id` varchar(36) NOT NULL,
  `appid` varchar(36) NOT NULL,
  `info_name` varchar(500) NOT NULL,
  `info_value` varchar(500) NOT NULL,
  `isenabled` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_application_info
-- ----------------------------

-- ----------------------------
-- Table structure for t_application_link
-- ----------------------------
DROP TABLE IF EXISTS `t_application_link`;
CREATE TABLE `t_application_link` (
  `id` varchar(36) NOT NULL,
  `appid` varchar(36) NOT NULL,
  `isenabled` varchar(2) NOT NULL,
  `link_image_url` varchar(500) DEFAULT NULL,
  `link_name` varchar(500) NOT NULL,
  `link_type` int(11) NOT NULL,
  `link_url` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_application_link
-- ----------------------------

-- ----------------------------
-- Table structure for t_department
-- ----------------------------
DROP TABLE IF EXISTS `t_department`;
CREATE TABLE `t_department` (
  `id` varchar(36) NOT NULL,
  `code` varchar(100) NOT NULL,
  `levels` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parentid` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_department
-- ----------------------------

-- ----------------------------
-- Table structure for t_menu
-- ----------------------------
DROP TABLE IF EXISTS `t_menu`;
CREATE TABLE `t_menu` (
  `id` varchar(36) NOT NULL,
  `appid` varchar(36) NOT NULL,
  `dialogh` int(11) DEFAULT NULL,
  `dialogw` int(11) DEFAULT NULL,
  `icon_class` varchar(255) DEFAULT NULL,
  `icon_color` varchar(255) DEFAULT NULL,
  `model` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `opentype` int(11) NOT NULL,
  `page_url` varchar(255) DEFAULT NULL,
  `parentid` varchar(36) DEFAULT NULL,
  `sort` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_menu
-- ----------------------------
INSERT INTO `t_menu` VALUES ('f83d46bf-49de-11e8-addd-00155d1c0117', 'f80136dc-49de-11e8-addd-00155d1c0117', '0', '0', 'fa-cogs', '#1b992f', '0', '系统配置', '0', '', 'f80136dc-49de-11e8-addd-00155d1c0117', '0', '1', '0');
INSERT INTO `t_menu` VALUES ('f83d9666-49de-11e8-addd-00155d1c0117', 'f80136dc-49de-11e8-addd-00155d1c0117', '0', '0', 'fa-users', '#354ab8', '0', '用户配置', '0', '/view/page/user/user', 'f83d46bf-49de-11e8-addd-00155d1c0117', '1', '1', '0');
INSERT INTO `t_menu` VALUES ('f83df5a6-49de-11e8-addd-00155d1c0117', 'f80136dc-49de-11e8-addd-00155d1c0117', '0', '0', 'fa-deviantart', '#354ab8', '0', '机构配置', '0', '/view/page/department/department', 'f83d46bf-49de-11e8-addd-00155d1c0117', '2', '1', '0');
INSERT INTO `t_menu` VALUES ('f83e3e77-49de-11e8-addd-00155d1c0117', 'f8325f0c-49de-11e8-addd-00155d1c0117', '0', '0', 'fa-cogs', '#1b992f', '0', 'demo', '0', '', 'f8325f0c-49de-11e8-addd-00155d1c0117', '0', '1', '1');
INSERT INTO `t_menu` VALUES ('f83e87ea-49de-11e8-addd-00155d1c0117', 'f8325f0c-49de-11e8-addd-00155d1c0117', '0', '0', 'fa-users', '#354ab8', '0', '上传', '0', '/view/page/demo/upload?_type=0', 'f83e3e77-49de-11e8-addd-00155d1c0117', '1', '1', '1');

-- ----------------------------
-- Table structure for t_module
-- ----------------------------
DROP TABLE IF EXISTS `t_module`;
CREATE TABLE `t_module` (
  `id` varchar(36) NOT NULL,
  `appid` varchar(36) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parentid` varchar(36) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDXt4wp8k6cty0w2cyttvthofxmx` (`code`,`appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_module
-- ----------------------------
INSERT INTO `t_module` VALUES ('f83b2d7b-49de-11e8-addd-00155d1c0117', 'f80136dc-49de-11e8-addd-00155d1c0117', 'systemadmin', '系统管理', 'f80136dc-49de-11e8-addd-00155d1c0117', '0');

-- ----------------------------
-- Table structure for t_module_func
-- ----------------------------
DROP TABLE IF EXISTS `t_module_func`;
CREATE TABLE `t_module_func` (
  `id` varchar(36) NOT NULL,
  `appid` varchar(36) NOT NULL,
  `code` varchar(100) NOT NULL,
  `islog` int(11) NOT NULL,
  `moduleid` varchar(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX9ssl4rarb3jupvp8hsmvspf39` (`code`,`appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_module_func
-- ----------------------------
INSERT INTO `t_module_func` VALUES ('f84081c4-49de-11e8-addd-00155d1c0117', 'f80136dc-49de-11e8-addd-00155d1c0117', 'allonlineuser', '1', 'f83b2d7b-49de-11e8-addd-00155d1c0117', '查看所有系统在线用户统计', '0');
INSERT INTO `t_module_func` VALUES ('f840fce2-49de-11e8-addd-00155d1c0117', 'f80136dc-49de-11e8-addd-00155d1c0117', 'sysparamconfig', '1', 'f83b2d7b-49de-11e8-addd-00155d1c0117', '系统参数配置', '0');
INSERT INTO `t_module_func` VALUES ('f8413ecc-49de-11e8-addd-00155d1c0117', 'f80136dc-49de-11e8-addd-00155d1c0117', 'appconfig', '1', 'f83b2d7b-49de-11e8-addd-00155d1c0117', '应用配置', '0');
INSERT INTO `t_module_func` VALUES ('f84183ea-49de-11e8-addd-00155d1c0117', 'f80136dc-49de-11e8-addd-00155d1c0117', 'roleconfig', '1', 'f83b2d7b-49de-11e8-addd-00155d1c0117', '角色配置', '0');
INSERT INTO `t_module_func` VALUES ('f8420a00-49de-11e8-addd-00155d1c0117', 'f80136dc-49de-11e8-addd-00155d1c0117', 'powerconfig', '1', 'f83b2d7b-49de-11e8-addd-00155d1c0117', '权限配置', '0');

-- ----------------------------
-- Table structure for t_online_user
-- ----------------------------
DROP TABLE IF EXISTS `t_online_user`;
CREATE TABLE `t_online_user` (
  `id` varchar(36) NOT NULL,
  `appid` varchar(36) NOT NULL,
  `last_active_time` varchar(19) NOT NULL,
  `last_login_ip` varchar(255) NOT NULL,
  `userid` varchar(36) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDXeiah317ncm255brcvysgvqwo5` (`last_active_time`,`appid`,`userid`),
  KEY `IDXkybqineexvu8blny0cgaxybas` (`last_active_time`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_online_user
-- ----------------------------

-- ----------------------------
-- Table structure for t_role
-- ----------------------------
DROP TABLE IF EXISTS `t_role`;
CREATE TABLE `t_role` (
  `id` varchar(36) NOT NULL,
  `appid` varchar(36) NOT NULL,
  `levels` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_role
-- ----------------------------
INSERT INTO `t_role` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f80136dc-49de-11e8-addd-00155d1c0117', '0', '超级管理员', '0');

-- ----------------------------
-- Table structure for t_role_menu_set
-- ----------------------------
DROP TABLE IF EXISTS `t_role_menu_set`;
CREATE TABLE `t_role_menu_set` (
  `roleid` varchar(36) NOT NULL,
  `menuid` varchar(36) NOT NULL,
  PRIMARY KEY (`roleid`,`menuid`),
  KEY `FKldn8uarenwtudvb2fud6f890k` (`menuid`),
  CONSTRAINT `FK517qmqt55bonsxvloiy8n4h5u` FOREIGN KEY (`roleid`) REFERENCES `t_role` (`id`),
  CONSTRAINT `FKldn8uarenwtudvb2fud6f890k` FOREIGN KEY (`menuid`) REFERENCES `t_menu` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_role_menu_set
-- ----------------------------
INSERT INTO `t_role_menu_set` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f83d46bf-49de-11e8-addd-00155d1c0117');
INSERT INTO `t_role_menu_set` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f83d9666-49de-11e8-addd-00155d1c0117');
INSERT INTO `t_role_menu_set` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f83df5a6-49de-11e8-addd-00155d1c0117');
INSERT INTO `t_role_menu_set` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f83e3e77-49de-11e8-addd-00155d1c0117');
INSERT INTO `t_role_menu_set` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f83e87ea-49de-11e8-addd-00155d1c0117');

-- ----------------------------
-- Table structure for t_role_module_func_set
-- ----------------------------
DROP TABLE IF EXISTS `t_role_module_func_set`;
CREATE TABLE `t_role_module_func_set` (
  `roleid` varchar(36) NOT NULL,
  `funcid` varchar(36) NOT NULL,
  PRIMARY KEY (`roleid`,`funcid`),
  KEY `FKnxig2b2oxps8t6yw2l44lu2wh` (`funcid`),
  CONSTRAINT `FKbq6g4qr4fxa9hdufr2ygvhgpc` FOREIGN KEY (`roleid`) REFERENCES `t_role` (`id`),
  CONSTRAINT `FKnxig2b2oxps8t6yw2l44lu2wh` FOREIGN KEY (`funcid`) REFERENCES `t_module_func` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_role_module_func_set
-- ----------------------------
INSERT INTO `t_role_module_func_set` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f84081c4-49de-11e8-addd-00155d1c0117');
INSERT INTO `t_role_module_func_set` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f840fce2-49de-11e8-addd-00155d1c0117');
INSERT INTO `t_role_module_func_set` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f8413ecc-49de-11e8-addd-00155d1c0117');
INSERT INTO `t_role_module_func_set` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f84183ea-49de-11e8-addd-00155d1c0117');
INSERT INTO `t_role_module_func_set` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f8420a00-49de-11e8-addd-00155d1c0117');

-- ----------------------------
-- Table structure for t_role_module_set
-- ----------------------------
DROP TABLE IF EXISTS `t_role_module_set`;
CREATE TABLE `t_role_module_set` (
  `roleid` varchar(36) NOT NULL,
  `moduleid` varchar(36) NOT NULL,
  PRIMARY KEY (`roleid`,`moduleid`),
  KEY `FKmpnam9u7777sio8j2s7j6938i` (`moduleid`),
  CONSTRAINT `FKmpnam9u7777sio8j2s7j6938i` FOREIGN KEY (`moduleid`) REFERENCES `t_module` (`id`),
  CONSTRAINT `FKrng6j7kgqi2mur1mo7vo3buh5` FOREIGN KEY (`roleid`) REFERENCES `t_role` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_role_module_set
-- ----------------------------
INSERT INTO `t_role_module_set` VALUES ('f8443708-49de-11e8-addd-00155d1c0117', 'f83b2d7b-49de-11e8-addd-00155d1c0117');

-- ----------------------------
-- Table structure for t_runtimeconfig
-- ----------------------------
DROP TABLE IF EXISTS `t_runtimeconfig`;
CREATE TABLE `t_runtimeconfig` (
  `id` varchar(36) NOT NULL,
  `confdes` varchar(255) DEFAULT NULL,
  `confname` varchar(100) NOT NULL,
  `confvalue` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX17wyhgvcxh7u9mc5vfo37cysa` (`confname`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_runtimeconfig
-- ----------------------------

-- ----------------------------
-- Table structure for t_user
-- ----------------------------
DROP TABLE IF EXISTS `t_user`;
CREATE TABLE `t_user` (
  `id` varchar(36) NOT NULL,
  `levels` int(11) NOT NULL,
  `loginno` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_2krxdnc6og7p36rgmy8vexkkb` (`loginno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_user
-- ----------------------------
INSERT INTO `t_user` VALUES ('f892c635-49de-11e8-addd-00155d1c0117', '0', 'admin', '超级管理员', 'fa9e8849b827fe3aa97d4673a4687391', '0', '1');

-- ----------------------------
-- Table structure for t_user_configuration
-- ----------------------------
DROP TABLE IF EXISTS `t_user_configuration`;
CREATE TABLE `t_user_configuration` (
  `id` varchar(36) NOT NULL,
  `appid` varchar(36) NOT NULL,
  `settings_add_container` int(11) NOT NULL,
  `settings_breadcrumbs` int(11) NOT NULL,
  `settings_compact` int(11) NOT NULL,
  `settings_highlight` int(11) NOT NULL,
  `settings_hover` int(11) NOT NULL,
  `settings_navbar` int(11) NOT NULL,
  `settings_sidebar` int(11) NOT NULL,
  `settings_use_tabs` int(11) NOT NULL,
  `skin_colorpicker` varchar(255) DEFAULT NULL,
  `userid` varchar(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_user_configuration
-- ----------------------------
INSERT INTO `t_user_configuration` VALUES ('f894c49b-49de-11e8-addd-00155d1c0117', 'f80136dc-49de-11e8-addd-00155d1c0117', '0', '1', '0', '0', '0', '1', '1', '1', 'skin-1', 'f892c635-49de-11e8-addd-00155d1c0117');

-- ----------------------------
-- Table structure for t_user_department_set
-- ----------------------------
DROP TABLE IF EXISTS `t_user_department_set`;
CREATE TABLE `t_user_department_set` (
  `userid` varchar(36) NOT NULL,
  `departmentid` varchar(36) NOT NULL,
  PRIMARY KEY (`userid`,`departmentid`),
  KEY `FK1emwybu8cf4ea9ataxkwhgy3n` (`departmentid`),
  CONSTRAINT `FK1emwybu8cf4ea9ataxkwhgy3n` FOREIGN KEY (`departmentid`) REFERENCES `t_department` (`id`),
  CONSTRAINT `FK9rfna2ynyaqoxcmrki4vo1u7e` FOREIGN KEY (`userid`) REFERENCES `t_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_user_department_set
-- ----------------------------

-- ----------------------------
-- Table structure for t_user_info
-- ----------------------------
DROP TABLE IF EXISTS `t_user_info`;
CREATE TABLE `t_user_info` (
  `id` varchar(36) NOT NULL,
  `last_login_ip` varchar(255) DEFAULT NULL,
  `last_login_time` varchar(20) DEFAULT NULL,
  `portrait` text,
  `userid` varchar(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_user_info
-- ----------------------------

-- ----------------------------
-- Table structure for t_user_loginrecord
-- ----------------------------
DROP TABLE IF EXISTS `t_user_loginrecord`;
CREATE TABLE `t_user_loginrecord` (
  `id` varchar(36) NOT NULL,
  `appid` varchar(36) NOT NULL,
  `login_date` varchar(255) DEFAULT NULL,
  `login_ip` varchar(255) DEFAULT NULL,
  `login_time` varchar(255) DEFAULT NULL,
  `userid` varchar(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_user_loginrecord
-- ----------------------------

-- ----------------------------
-- Table structure for t_user_role_set
-- ----------------------------
DROP TABLE IF EXISTS `t_user_role_set`;
CREATE TABLE `t_user_role_set` (
  `userid` varchar(36) NOT NULL,
  `roleid` varchar(36) NOT NULL,
  PRIMARY KEY (`userid`,`roleid`),
  KEY `FKcoqdldhluyygama1b3ol8iqyn` (`roleid`),
  CONSTRAINT `FK56qhyoonc4xtq1prlhwb3ry2f` FOREIGN KEY (`userid`) REFERENCES `t_user` (`id`),
  CONSTRAINT `FKcoqdldhluyygama1b3ol8iqyn` FOREIGN KEY (`roleid`) REFERENCES `t_role` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_user_role_set
-- ----------------------------
INSERT INTO `t_user_role_set` VALUES ('f892c635-49de-11e8-addd-00155d1c0117', 'f8443708-49de-11e8-addd-00155d1c0117');
SET FOREIGN_KEY_CHECKS=1;
