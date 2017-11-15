/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 50538
 Source Host           : localhost:3306
 Source Schema         : traffic

 Target Server Type    : MySQL
 Target Server Version : 50538
 File Encoding         : 65001

 Date: 14/11/2017 20:16:16
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for member
-- ----------------------------
DROP TABLE IF EXISTS `member`;
CREATE TABLE `member`  (
  `uid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `account` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'User Name',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'User Password',
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'User Token',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'User Email',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of member
-- ----------------------------
INSERT INTO `member` VALUES (1, 'ff1f807eb76539c75001be3e2f6c8e5a9ad9d5a8', 'bbc24d02e9cb0c55a75fca27f77bfdf0fe191709', '25066c0f74477708f72e3e28c3d570b1f5c44403', 'ding@dingstudio.cn');

SET FOREIGN_KEY_CHECKS = 1;
