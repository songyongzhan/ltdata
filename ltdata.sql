/*
Navicat MySQL Data Transfer

Source Server         : ltdata
Source Server Version : 50540
Source Host           : 119.29.78.116:3306
Source Database       : ltdata

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2019-01-05 13:53:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lt_ciq
-- ----------------------------
DROP TABLE IF EXISTS `lt_ciq`;
CREATE TABLE `lt_ciq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `title` varchar(200) DEFAULT NULL COMMENT '//海关名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '//状态',
  `updatetime` int(10) DEFAULT '0' COMMENT '//更新时间',
  `createtime` int(10) DEFAULT '0' COMMENT '//创建时间',
  PRIMARY KEY (`id`),
  KEY `title` (`status`,`title`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8 COMMENT='//出口关区';

-- ----------------------------
-- Records of lt_ciq
-- ----------------------------
INSERT INTO `lt_ciq` VALUES ('1', '苹果', '1', '1546214256', '1546214256');
INSERT INTO `lt_ciq` VALUES ('2', '关区', '1', '0', '0');
INSERT INTO `lt_ciq` VALUES ('4', '出口关区', '1', '1546659809', '1546659809');
INSERT INTO `lt_ciq` VALUES ('5', '1', '1', '1546659809', '1546659809');
INSERT INTO `lt_ciq` VALUES ('6', '2', '1', '1546659810', '1546659810');
INSERT INTO `lt_ciq` VALUES ('7', '出口关区', '1', '1546661080', '1546661080');
INSERT INTO `lt_ciq` VALUES ('8', '1', '1', '1546661081', '1546661081');
INSERT INTO `lt_ciq` VALUES ('9', '2', '1', '1546661081', '1546661081');

-- ----------------------------
-- Table structure for lt_country
-- ----------------------------
DROP TABLE IF EXISTS `lt_country`;
CREATE TABLE `lt_country` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `title` varchar(200) DEFAULT NULL COMMENT '//国家',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '//状态',
  `updatetime` int(10) DEFAULT '0' COMMENT '//更新时间',
  `createtime` int(10) DEFAULT '0' COMMENT '//创建时间',
  PRIMARY KEY (`id`),
  KEY `title` (`status`,`title`)
) ENGINE=InnoDB AUTO_INCREMENT=301 DEFAULT CHARSET=utf8 COMMENT='//国家';

-- ----------------------------
-- Records of lt_country
-- ----------------------------
INSERT INTO `lt_country` VALUES ('5', '美国', '1', '0', '0');
INSERT INTO `lt_country` VALUES ('7', '目的国', '1', '1546659809', '1546659809');
INSERT INTO `lt_country` VALUES ('8', '1', '1', '1546659809', '1546659809');
INSERT INTO `lt_country` VALUES ('9', '5', '1', '1546659810', '1546659810');
INSERT INTO `lt_country` VALUES ('10', '目的国', '1', '1546661080', '1546661080');
INSERT INTO `lt_country` VALUES ('11', '1', '1', '1546661081', '1546661081');
INSERT INTO `lt_country` VALUES ('12', '5', '1', '1546661081', '1546661081');

-- ----------------------------
-- Table structure for lt_exportdata
-- ----------------------------
DROP TABLE IF EXISTS `lt_exportdata`;
CREATE TABLE `lt_exportdata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `export_date` int(11) DEFAULT '0' COMMENT '//日期',
  `export_ciq` int(11) DEFAULT '0' COMMENT '//出口关区',
  `dist_country` smallint(6) DEFAULT '0' COMMENT '//目的国',
  `goods_code` smallint(6) DEFAULT '0' COMMENT '//商品编码',
  `transaction_mode` varchar(50) DEFAULT '' COMMENT '//交易方式',
  `specification_title` varchar(200) DEFAULT NULL COMMENT '//规格型号',
  `specification` varchar(200) DEFAULT '' COMMENT '//提取的规格',
  `total_amount` decimal(18,2) unsigned DEFAULT '0.00' COMMENT '//美元总价',
  `weight` int(11) DEFAULT '0' COMMENT '//法定重量',
  `price_amount` decimal(18,2) unsigned DEFAULT '0.00' COMMENT '//美元单价',
  `trade_mode` tinyint(4) DEFAULT '0' COMMENT '//贸易方式',
  `transport_mode` tinyint(4) DEFAULT '0' COMMENT '//运输方式',
  `madein` int(11) DEFAULT '0' COMMENT '//原产地',
  `shipper` varchar(200) NOT NULL COMMENT '//货主单位',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '//状态',
  `updatetime` int(10) DEFAULT '0' COMMENT '//更新时间',
  `createtime` int(10) DEFAULT '0' COMMENT '//创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15245 DEFAULT CHARSET=utf8 COMMENT='//出口分析表';

-- ----------------------------
-- Records of lt_exportdata
-- ----------------------------
INSERT INTO `lt_exportdata` VALUES ('1', '0', '0', '0', '0', '', '规格型号', '规格型号', '0.00', '0', '0.00', '0', '0', '0', '货主单位', '1', '1546001029', '1546001029');
INSERT INTO `lt_exportdata` VALUES ('2', '1451577600', '1', '1', '32767', '', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '1561.00', '192', '8.10', '55', '1', '7', '北京城建集团有限责任公司', '1', '1546001030', '1546001030');
INSERT INTO `lt_exportdata` VALUES ('3', '1452096000', '2', '5', '32767', '', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '1060.00', '540', '2.00', '33', '5', '6', '山东万达宝通轮胎有限公司', '1', '1546001030', '1546001030');
INSERT INTO `lt_exportdata` VALUES ('4', '1452096000', '2', '5', '32767', '', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '2950.00', '1550', '1.90', '34', '11', '6', '山东万达宝通轮胎有限公司', '1', '1546001030', '1546001030');
INSERT INTO `lt_exportdata` VALUES ('5', '1452096000', '2', '5', '32767', '', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '22470.00', '11330', '2.00', '35', '2', '6', '山东万达宝通轮胎有限公司', '1', '1546001031', '1546001031');
INSERT INTO `lt_exportdata` VALUES ('6', '1452096000', '2', '5', '32767', '', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '3120.00', '1560', '2.00', '36', '44', '6', '山东万达宝通轮胎有限公司', '1', '1546001031', '1546001031');
INSERT INTO `lt_exportdata` VALUES ('7', '1452096000', '2', '5', '32767', '', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '18204.00', '9180', '2.00', '37', '3', '889', '山东万达宝通轮胎有限公司', '1', '1546001031', '1546001031');
INSERT INTO `lt_exportdata` VALUES ('8', '1452096000', '2', '5', '32767', '', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '5840.00', '2720', '2.10', '38', '33', '8', '山东万达宝通轮胎有限公司', '1', '1546001032', '1546001032');
INSERT INTO `lt_exportdata` VALUES ('9', '0', '0', '0', '0', '', '规格型号', '规格型号', '0.00', '0', '0.00', '0', '0', '0', '货主单位', '1', '1546001351', '1546001351');
INSERT INTO `lt_exportdata` VALUES ('10', '1451577600', '1', '1', '32767', '', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '1561.00', '192', '8.10', '55', '1', '7', '北京城建集团有限责任公司', '1', '1546001352', '1546001352');
INSERT INTO `lt_exportdata` VALUES ('11', '1452096000', '2', '5', '32767', '', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '1060.00', '540', '2.00', '33', '5', '6', '山东万达宝通轮胎有限公司', '1', '1546001353', '1546001353');
INSERT INTO `lt_exportdata` VALUES ('12', '1452096000', '2', '5', '32767', '', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '2950.00', '1550', '1.90', '34', '11', '6', '山东万达宝通轮胎有限公司', '1', '1546001354', '1546001354');
INSERT INTO `lt_exportdata` VALUES ('13', '1452096000', '2', '5', '32767', '', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '22470.00', '11330', '2.00', '35', '2', '6', '山东万达宝通轮胎有限公司', '1', '1546001354', '1546001354');
INSERT INTO `lt_exportdata` VALUES ('14', '1452096000', '2', '5', '32767', '', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '3120.00', '1560', '2.00', '36', '44', '6', '山东万达宝通轮胎有限公司', '1', '1546001355', '1546001355');
INSERT INTO `lt_exportdata` VALUES ('15', '1452096000', '2', '5', '32767', '', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '18204.00', '9180', '2.00', '37', '3', '889', '山东万达宝通轮胎有限公司', '1', '1546001356', '1546001356');
INSERT INTO `lt_exportdata` VALUES ('16', '1452096000', '2', '5', '32767', '', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '5840.00', '2720', '2.10', '38', '33', '8', '山东万达宝通轮胎有限公司', '1', '1546001356', '1546001356');
INSERT INTO `lt_exportdata` VALUES ('17', '0', '0', '0', '0', '', '规格型号', '规格型号', '0.00', '0', '0.00', '0', '0', '0', '货主单位', '1', '1546001786', '1546001786');
INSERT INTO `lt_exportdata` VALUES ('18', '1451577600', '1', '1', '32767', '', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '1561.00', '192', '8.10', '55', '1', '7', '北京城建集团有限责任公司', '1', '1546001787', '1546001787');
INSERT INTO `lt_exportdata` VALUES ('19', '1452096000', '2', '5', '32767', '', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '1060.00', '540', '2.00', '33', '5', '6', '山东万达宝通轮胎有限公司', '1', '1546001788', '1546001788');
INSERT INTO `lt_exportdata` VALUES ('20', '1452096000', '2', '5', '32767', '', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '2950.00', '1550', '1.90', '34', '11', '6', '山东万达宝通轮胎有限公司', '1', '1546001788', '1546001788');
INSERT INTO `lt_exportdata` VALUES ('21', '1452096000', '2', '5', '32767', '', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '22470.00', '11330', '2.00', '35', '2', '6', '山东万达宝通轮胎有限公司', '1', '1546001789', '1546001789');
INSERT INTO `lt_exportdata` VALUES ('22', '1452096000', '2', '5', '32767', '', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '3120.00', '1560', '2.00', '36', '44', '6', '山东万达宝通轮胎有限公司', '1', '1546001789', '1546001789');
INSERT INTO `lt_exportdata` VALUES ('23', '1452096000', '2', '5', '32767', '', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '18204.00', '9180', '2.00', '37', '3', '889', '山东万达宝通轮胎有限公司', '1', '1546001790', '1546001790');
INSERT INTO `lt_exportdata` VALUES ('24', '1452096000', '2', '5', '32767', '', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '5840.00', '2720', '2.10', '38', '33', '8', '山东万达宝通轮胎有限公司', '1', '1546001801', '1546001801');
INSERT INTO `lt_exportdata` VALUES ('25', '0', '0', '0', '0', '', '规格型号', '规格型号', '0.00', '0', '0.00', '0', '0', '0', '货主单位', '1', '1546001898', '1546001898');
INSERT INTO `lt_exportdata` VALUES ('26', '1451577600', '1', '1', '32767', '', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '1561.00', '192', '8.10', '55', '1', '7', '北京城建集团有限责任公司', '1', '1546001899', '1546001899');
INSERT INTO `lt_exportdata` VALUES ('27', '1452096000', '2', '5', '32767', '', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '1060.00', '540', '2.00', '33', '5', '6', '山东万达宝通轮胎有限公司', '1', '1546001900', '1546001900');
INSERT INTO `lt_exportdata` VALUES ('28', '1452096000', '2', '5', '32767', '', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '2950.00', '1550', '1.90', '34', '11', '6', '山东万达宝通轮胎有限公司', '1', '1546001900', '1546001900');
INSERT INTO `lt_exportdata` VALUES ('29', '1452096000', '2', '5', '32767', '', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '22470.00', '11330', '2.00', '35', '2', '6', '山东万达宝通轮胎有限公司', '1', '1546001901', '1546001901');
INSERT INTO `lt_exportdata` VALUES ('30', '1452096000', '2', '5', '32767', '', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '3120.00', '1560', '2.00', '36', '44', '6', '山东万达宝通轮胎有限公司', '1', '1546001901', '1546001901');
INSERT INTO `lt_exportdata` VALUES ('31', '1452096000', '2', '5', '32767', '', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '18204.00', '9180', '2.00', '37', '3', '889', '山东万达宝通轮胎有限公司', '1', '1546001903', '1546001903');
INSERT INTO `lt_exportdata` VALUES ('32', '1452096000', '2', '5', '32767', '', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '5840.00', '2720', '2.10', '38', '33', '8', '山东万达宝通轮胎有限公司', '1', '1546001903', '1546001903');
INSERT INTO `lt_exportdata` VALUES ('33', '0', '0', '0', '0', '', '规格型号', '规格型号', '0.00', '0', '0.00', '0', '0', '0', '货主单位', '1', '1546045937', '1546045937');
INSERT INTO `lt_exportdata` VALUES ('34', '1451577600', '1', '1', '32767', '', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '1561.00', '192', '8.10', '55', '1', '7', '北京城建集团有限责任公司', '1', '1546045937', '1546045937');
INSERT INTO `lt_exportdata` VALUES ('35', '1452096000', '2', '5', '32767', '', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '1060.00', '540', '2.00', '33', '5', '6', '山东万达宝通轮胎有限公司', '1', '1546045937', '1546045937');
INSERT INTO `lt_exportdata` VALUES ('36', '1452096000', '2', '5', '32767', '', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '2950.00', '1550', '1.90', '34', '11', '6', '山东万达宝通轮胎有限公司', '1', '1546045937', '1546045937');
INSERT INTO `lt_exportdata` VALUES ('37', '1452096000', '2', '5', '32767', '', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '22470.00', '11330', '2.00', '35', '2', '6', '山东万达宝通轮胎有限公司', '1', '1546045937', '1546045937');
INSERT INTO `lt_exportdata` VALUES ('38', '1452096000', '2', '5', '32767', '', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '3120.00', '1560', '2.00', '36', '44', '6', '山东万达宝通轮胎有限公司', '1', '1546045937', '1546045937');
INSERT INTO `lt_exportdata` VALUES ('39', '1452096000', '2', '5', '32767', '', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '18204.00', '9180', '2.00', '37', '3', '889', '山东万达宝通轮胎有限公司', '1', '1546045937', '1546045937');
INSERT INTO `lt_exportdata` VALUES ('40', '1452096000', '2', '5', '32767', '', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '5840.00', '2720', '2.10', '38', '33', '8', '山东万达宝通轮胎有限公司', '1', '1546045937', '1546045937');
INSERT INTO `lt_exportdata` VALUES ('41', '0', '4', '7', '0', '', '规格型号', '', '0.00', '0', '0.00', '40', '35', '10', '货主单位', '1', '1546659809', '1546659809');
INSERT INTO `lt_exportdata` VALUES ('42', '1451577600', '5', '8', '32767', '', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '', '1561.00', '192', '8.10', '41', '36', '11', '北京城建集团有限责任公司', '1', '1546659810', '1546659810');
INSERT INTO `lt_exportdata` VALUES ('43', '1452096000', '6', '9', '32767', '', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '215/75R17.5', '1060.00', '540', '2.00', '42', '37', '12', '山东万达宝通轮胎有限公司', '1', '1546659810', '1546659810');
INSERT INTO `lt_exportdata` VALUES ('44', '1452096000', '6', '9', '32767', '', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '235/75R17.5', '2950.00', '1550', '1.90', '43', '38', '12', '山东万达宝通轮胎有限公司', '1', '1546659811', '1546659811');
INSERT INTO `lt_exportdata` VALUES ('45', '1452096000', '6', '9', '32767', '', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '295/80R22.5', '22470.00', '11330', '2.00', '44', '39', '12', '山东万达宝通轮胎有限公司', '1', '1546659811', '1546659811');
INSERT INTO `lt_exportdata` VALUES ('46', '1452096000', '6', '9', '32767', '', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '315/70R22.5', '3120.00', '1560', '2.00', '45', '40', '12', '山东万达宝通轮胎有限公司', '1', '1546659812', '1546659812');
INSERT INTO `lt_exportdata` VALUES ('47', '1452096000', '6', '9', '32767', '', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '315/80R22.5', '18204.00', '9180', '2.00', '46', '41', '13', '山东万达宝通轮胎有限公司', '1', '1546659812', '1546659812');
INSERT INTO `lt_exportdata` VALUES ('48', '1452096000', '6', '9', '32767', '', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '13R22.5', '5840.00', '2720', '2.10', '47', '42', '14', '山东万达宝通轮胎有限公司', '1', '1546659812', '1546659812');
INSERT INTO `lt_exportdata` VALUES ('49', '0', '7', '10', '0', '成交方式', '规格型号', '', '0.00', '0', '0.00', '48', '43', '15', '货主单位', '1', '1546661081', '1546661081');
INSERT INTO `lt_exportdata` VALUES ('50', '1451577600', '8', '11', '32767', 'FOB', '车|12.5-18|R-4|人|12.5-18|品牌：华鲁', '', '1561.00', '192', '8.10', '49', '44', '16', '北京城建集团有限责任公司', '1', '1546661081', '1546661081');
INSERT INTO `lt_exportdata` VALUES ('51', '1452096000', '9', '12', '32767', 'FOB', '卡车用|215/75R17.5|6英寸|BT957|211MM/212MM|BOTO牌', '215/75R17.5', '1060.00', '540', '2.00', '50', '45', '17', '山东万达宝通轮胎有限公司', '1', '1546661082', '1546661082');
INSERT INTO `lt_exportdata` VALUES ('52', '1452096000', '9', '12', '32767', 'FOB', '卡车用|235/75R17.5|6.75英寸|BT926|233MM|BOTO牌', '235/75R17.5', '2950.00', '1550', '1.90', '51', '46', '17', '山东万达宝通轮胎有限公司', '1', '1546661082', '1546661082');
INSERT INTO `lt_exportdata` VALUES ('53', '1452096000', '9', '12', '32767', 'FOB', '卡车用|295/80R22.5|9英寸|BT968|298MM|BOTO牌', '295/80R22.5', '22470.00', '11330', '2.00', '52', '47', '17', '山东万达宝通轮胎有限公司', '1', '1546661083', '1546661083');
INSERT INTO `lt_exportdata` VALUES ('54', '1452096000', '9', '12', '32767', 'FOB', '卡车用|315/70R22.5|9英寸|BT388|312MM|BOTO牌', '315/70R22.5', '3120.00', '1560', '2.00', '53', '48', '17', '山东万达宝通轮胎有限公司', '1', '1546661083', '1546661083');
INSERT INTO `lt_exportdata` VALUES ('55', '1452096000', '9', '12', '32767', 'FOB', '卡车用|315/80R22.5|9英寸|BT968|312MM|BOTO牌', '315/80R22.5', '18204.00', '9180', '2.00', '54', '49', '18', '山东万达宝通轮胎有限公司', '1', '1546661083', '1546661083');
INSERT INTO `lt_exportdata` VALUES ('56', '1452096000', '9', '12', '32767', 'FOB', '卡车用|13R22.5|9.75英寸|BT188|320MM|BOTO牌', '13R22.5', '5840.00', '2720', '2.10', '55', '50', '19', '山东万达宝通轮胎有限公司', '1', '1546661084', '1546661084');

-- ----------------------------
-- Table structure for lt_logs
-- ----------------------------
DROP TABLE IF EXISTS `lt_logs`;
CREATE TABLE `lt_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `controller` varchar(200) NOT NULL COMMENT '//控制器名称',
  `method` varchar(200) NOT NULL COMMENT '//执行方法',
  `ip` int(11) DEFAULT '0' COMMENT '//ip地址',
  `detail` text NOT NULL COMMENT '//操作数据',
  `exe_sql` text NOT NULL COMMENT '//执行的sql',
  `exe_type` tinyint(4) NOT NULL COMMENT '//执行类型',
  `manage_id` int(11) NOT NULL COMMENT '//用户id',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '//状态-1删除，0禁用 1正常',
  `updatetime` int(11) DEFAULT '0' COMMENT '//更新时间',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '//添加时间',
  PRIMARY KEY (`id`),
  KEY `manage_id_ip` (`manage_id`,`ip`),
  KEY `manage_id_type` (`manage_id`,`exe_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lt_logs
-- ----------------------------

-- ----------------------------
-- Table structure for lt_made
-- ----------------------------
DROP TABLE IF EXISTS `lt_made`;
CREATE TABLE `lt_made` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `title` varchar(200) DEFAULT NULL COMMENT '//产地',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '//状态',
  `updatetime` int(10) DEFAULT '0' COMMENT '//更新时间',
  `createtime` int(10) DEFAULT '0' COMMENT '//创建时间',
  PRIMARY KEY (`id`),
  KEY `title` (`status`,`title`)
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=utf8 COMMENT='//原产地';

-- ----------------------------
-- Records of lt_made
-- ----------------------------
INSERT INTO `lt_made` VALUES ('8', '山东', '1', '0', '0');
INSERT INTO `lt_made` VALUES ('10', '原产地', '1', '1546659809', '1546659809');
INSERT INTO `lt_made` VALUES ('11', '7', '1', '1546659810', '1546659810');
INSERT INTO `lt_made` VALUES ('12', '6', '1', '1546659810', '1546659810');
INSERT INTO `lt_made` VALUES ('13', '889', '1', '1546659812', '1546659812');
INSERT INTO `lt_made` VALUES ('14', '8', '1', '1546659812', '1546659812');
INSERT INTO `lt_made` VALUES ('15', '原产地', '1', '1546661080', '1546661080');
INSERT INTO `lt_made` VALUES ('16', '7', '1', '1546661081', '1546661081');
INSERT INTO `lt_made` VALUES ('17', '6', '1', '1546661082', '1546661082');
INSERT INTO `lt_made` VALUES ('18', '889', '1', '1546661083', '1546661083');
INSERT INTO `lt_made` VALUES ('19', '8', '1', '1546661084', '1546661084');

-- ----------------------------
-- Table structure for lt_manage
-- ----------------------------
DROP TABLE IF EXISTS `lt_manage`;
CREATE TABLE `lt_manage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `username` varchar(30) NOT NULL COMMENT '//username',
  `password` char(40) NOT NULL COMMENT '//密码',
  `fullname` varchar(50) NOT NULL COMMENT '姓名',
  `token` varchar(200) NOT NULL COMMENT '//验证token',
  `timeout` int(10) unsigned DEFAULT '0' COMMENT '//token的过期时间',
  `email` varchar(100) DEFAULT '' COMMENT '邮箱',
  `mobile` varchar(11) DEFAULT '' COMMENT '手机',
  `status` tinyint(4) NOT NULL COMMENT '//用户的状态 1正常 0禁用 -1删除',
  `updatetime` int(11) DEFAULT '0' COMMENT '//更新时间',
  `department` varchar(50) NOT NULL DEFAULT '' COMMENT '//部门',
  `ext` text NOT NULL COMMENT '扩展字段',
  `isadmin` tinyint(1) unsigned NOT NULL COMMENT '//是否管理员',
  `last_logintime` int(11) NOT NULL COMMENT '//最后登录时间',
  `remarks` varchar(255) NOT NULL,
  `role_access` text NOT NULL COMMENT '//用户权限',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '//创建时间 时间戳的方式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='//后台管理用户表';

-- ----------------------------
-- Records of lt_manage
-- ----------------------------
INSERT INTO `lt_manage` VALUES ('1', '123', '', '123', '', '0', '', '', '1', '1546619369', '', '', '0', '0', '', '', '0');
INSERT INTO `lt_manage` VALUES ('2', 'song', '57e61e0b306480dd177b64d4735ca1417043a965', 'test', '', '0', '', '', '1', '1546619460', 'tetw', '', '0', '0', '', '', '1545903703');
INSERT INTO `lt_manage` VALUES ('3', 'song366', '57e61e0b306480dd177b64d4735ca1417043a965', 'test', '', '0', '', '', '0', '1546620163', 'tetw', '', '0', '0', '', '', '1545904558');
INSERT INTO `lt_manage` VALUES ('4', 'song676', '57e61e0b306480dd177b64d4735ca1417043a965', 'test9999', '', '0', '', '', '1', '1546619656', 'tetw', '', '0', '0', '', '', '1545904579');
INSERT INTO `lt_manage` VALUES ('5', 'song125', '57e61e0b306480dd177b64d4735ca1417043a965', 'test098方法', '', '0', '', '13811299405', '1', '1546620175', 'tetw9999', '', '0', '0', 'ppp', '', '1545904625');
INSERT INTO `lt_manage` VALUES ('6', 'song625', '57e61e0b306480dd177b64d4735ca1417043a965', 'test', '', '0', '', '', '1', '1546619621', 'tetw', '', '0', '0', '', '', '1545904731');
INSERT INTO `lt_manage` VALUES ('7', 'song805', '57e61e0b306480dd177b64d4735ca1417043a965', 'test', '', '0', '', '', '1', '1546619270', 'tetw', '', '0', '0', '', '', '1545904813');
INSERT INTO `lt_manage` VALUES ('8', 'song671', '57e61e0b306480dd177b64d4735ca1417043a965', 'test', '', '0', '', '', '1', '1546619221', 'tetw', '', '0', '0', '', '', '1545904894');
INSERT INTO `lt_manage` VALUES ('9', 'song570', '57e61e0b306480dd177b64d4735ca1417043a965', 'test', '', '0', '', '', '1', '1546619138', 'tetw', '', '0', '0', '', '', '1545904989');
INSERT INTO `lt_manage` VALUES ('10', 'song342', '57e61e0b306480dd177b64d4735ca1417043a965', 'test', '', '0', '', '', '1', '1546619053', 'tetw', '', '0', '0', '', '', '1545905020');
INSERT INTO `lt_manage` VALUES ('11', 'song799', '57e61e0b306480dd177b64d4735ca1417043a965', 'test', '', '0', '', '', '1', '1546619286', 'tetw', '', '0', '0', '', '', '1545905030');
INSERT INTO `lt_manage` VALUES ('12', 'song558', '4329aa9092671f3e429faec84415eb48c13eca90', 'test', '', '0', 'jj@sohu.com1', '13811299405', '0', '1546647380', '技术部876543', '', '0', '0', '我是备注---更新了一下9sssssssssssss', '3,3133', '1545905241');
INSERT INTO `lt_manage` VALUES ('13', 'song954', '57e61e0b306480dd177b64d4735ca1417043a965', 'test-098765', '', '0', 'haha@qianba.com', '13811299405', '0', '1546647349', 'tetw', '', '0', '0', 'fffffffffffffff', '', '1545912192');
INSERT INTO `lt_manage` VALUES ('14', 'song665', '57e61e0b306480dd177b64d4735ca1417043a965', 'test334343434343', '10deb7a9d9cf8ceaccd2922c584cb8af46cf9d04', '1546318236', 'song@aianba.com', '13811299405', '1', '1546613281', 'tetwcccccc999', '', '0', '1546316436', 'fffffffffffb---asdfasdfasfasdf-', '', '1545912219');
INSERT INTO `lt_manage` VALUES ('15', 'admin', '57e61e0b306480dd177b64d4735ca1417043a965', '2是', 'f5faf3f0eafdedb142b52c088f786a24af1fbaa1', '1546658728', '574482856@qq.com', '13811299506', '1', '1546656928', '678', '', '1', '1546656877', '', '', '1546051678');
INSERT INTO `lt_manage` VALUES ('16', 'xiaosong', '91c6d3d86aa4690f4f546065ac5ca351eeb8196c', 'test-098765', '', '0', 'jj@sohu.com', '13811299405', '1', '1546618833', '123', '', '0', '0', '12312312dfdfdfdf', '', '1546561930');

-- ----------------------------
-- Table structure for lt_manage_role
-- ----------------------------
DROP TABLE IF EXISTS `lt_manage_role`;
CREATE TABLE `lt_manage_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `manage_id` int(11) NOT NULL COMMENT '//用户ID',
  `role_id` int(11) NOT NULL COMMENT '//用户角色id',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '//状态 -1 删除 0 禁用  1正常',
  `updatetime` int(11) DEFAULT '0' COMMENT '//更新时间',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '//创建时间 时间戳的方式',
  PRIMARY KEY (`id`),
  KEY `platform_role` (`manage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='//用户可以role_id 操作某个平台';

-- ----------------------------
-- Records of lt_manage_role
-- ----------------------------

-- ----------------------------
-- Table structure for lt_menu
-- ----------------------------
DROP TABLE IF EXISTS `lt_menu`;
CREATE TABLE `lt_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `title` varchar(30) NOT NULL COMMENT '//栏目名称',
  `pid` int(11) NOT NULL COMMENT '//父子关系',
  `relation_url` text NOT NULL COMMENT '//扩展权限 验证权限 使用分号分割',
  `url` varchar(200) NOT NULL COMMENT '//控制器 controller/action',
  `ext` text NOT NULL COMMENT '//项目自定义存储数据',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '//状态 -1 删除 0 禁用  1正常',
  `type_id` tinyint(4) DEFAULT NULL COMMENT '//栏目类型 1 栏目  2方法',
  `sort_id` int(11) unsigned DEFAULT '0' COMMENT '排序id',
  `updatetime` int(11) NOT NULL,
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '//创建时间 时间戳的方式',
  PRIMARY KEY (`id`),
  KEY `getmenumethod` (`id`,`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='//栏目表';

-- ----------------------------
-- Records of lt_menu
-- ----------------------------
INSERT INTO `lt_menu` VALUES ('1', ' 网站管理', '0', '', 'export/data', '', '1', '1', '1', '1546050314', '1546050314');
INSERT INTO `lt_menu` VALUES ('2', '栏目管理', '1', '', 'index/menu/getlist', '', '1', '1', '2', '1546050339', '1546050339');
INSERT INTO `lt_menu` VALUES ('3', '用户管理', '1', '', 'index/manage/getlist', '', '1', '1', '2', '1546050355', '1546050355');
INSERT INTO `lt_menu` VALUES ('4', '角色管理', '1', '', 'export/data', '', '1', '1', '2', '1546050372', '1546050372');
INSERT INTO `lt_menu` VALUES ('5', '数据管理', '0', '', 'export/data', '', '1', '1', '2', '1546050591', '1546050591');
INSERT INTO `lt_menu` VALUES ('6', '数据', '5', '', '', '', '1', '1', '6', '1546050591', '1546050591');
INSERT INTO `lt_menu` VALUES ('7', '关区管理', '5', '', '', '', '1', '1', '7', '0', '0');
INSERT INTO `lt_menu` VALUES ('8', '出口管家', '5', '', '', '', '1', '1', '8', '0', '0');
INSERT INTO `lt_menu` VALUES ('9', '数据分析', '0', '', '', '', '1', '1', '9', '0', '0');
INSERT INTO `lt_menu` VALUES ('10', '价格分析', '9', '', '', '', '1', '1', '10', '0', '0');

-- ----------------------------
-- Table structure for lt_role
-- ----------------------------
DROP TABLE IF EXISTS `lt_role`;
CREATE TABLE `lt_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `title` varchar(30) NOT NULL COMMENT '//角色名称',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '//状态 -1 删除 0 禁用  1正常',
  `updatetime` int(11) DEFAULT '0' COMMENT '//更新时间',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '//创建时间 时间戳的方式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='//角色表';

-- ----------------------------
-- Records of lt_role
-- ----------------------------

-- ----------------------------
-- Table structure for lt_role_access
-- ----------------------------
DROP TABLE IF EXISTS `lt_role_access`;
CREATE TABLE `lt_role_access` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `menu_id` int(11) NOT NULL COMMENT '//管理的平台ID ',
  `role_id` int(11) NOT NULL COMMENT '//角色ID',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '//状态 -1 删除 0 禁用  1正常',
  `updatetime` int(11) DEFAULT '0' COMMENT '//更新时间',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '//创建时间 时间戳的方式',
  PRIMARY KEY (`id`),
  KEY `get_menu_ids` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='//角色可操作权限';

-- ----------------------------
-- Records of lt_role_access
-- ----------------------------

-- ----------------------------
-- Table structure for lt_status_code
-- ----------------------------
DROP TABLE IF EXISTS `lt_status_code`;
CREATE TABLE `lt_status_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `code` int(11) NOT NULL COMMENT '//状态码',
  `info` varchar(200) NOT NULL COMMENT '//生产环境提示消息',
  `message` varchar(200) NOT NULL COMMENT '//状态码对应消息',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '//状态 -1 删除 0 禁用  1正常',
  `updatetime` int(11) DEFAULT '0' COMMENT '//更新时间',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '//创建时间 时间戳的方式',
  PRIMARY KEY (`id`),
  KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='//后台管理用户表';

-- ----------------------------
-- Records of lt_status_code
-- ----------------------------

-- ----------------------------
-- Table structure for lt_trade
-- ----------------------------
DROP TABLE IF EXISTS `lt_trade`;
CREATE TABLE `lt_trade` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `title` varchar(200) DEFAULT NULL COMMENT '//国家',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '//状态',
  `updatetime` int(10) DEFAULT '0' COMMENT '//更新时间',
  `createtime` int(10) DEFAULT '0' COMMENT '//创建时间',
  PRIMARY KEY (`id`),
  KEY `title` (`status`,`title`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COMMENT='//贸易方式';

-- ----------------------------
-- Records of lt_trade
-- ----------------------------
INSERT INTO `lt_trade` VALUES ('38', '海运', '1', '0', '0');
INSERT INTO `lt_trade` VALUES ('40', '贸易方式', '1', '1546659809', '1546659809');
INSERT INTO `lt_trade` VALUES ('41', '55', '1', '1546659809', '1546659809');
INSERT INTO `lt_trade` VALUES ('42', '33', '1', '1546659810', '1546659810');
INSERT INTO `lt_trade` VALUES ('43', '34', '1', '1546659811', '1546659811');
INSERT INTO `lt_trade` VALUES ('44', '35', '1', '1546659811', '1546659811');
INSERT INTO `lt_trade` VALUES ('45', '36', '1', '1546659811', '1546659811');
INSERT INTO `lt_trade` VALUES ('46', '37', '1', '1546659812', '1546659812');
INSERT INTO `lt_trade` VALUES ('47', '38', '1', '1546659812', '1546659812');
INSERT INTO `lt_trade` VALUES ('48', '贸易方式', '1', '1546661080', '1546661080');
INSERT INTO `lt_trade` VALUES ('49', '55', '1', '1546661081', '1546661081');
INSERT INTO `lt_trade` VALUES ('50', '33', '1', '1546661082', '1546661082');
INSERT INTO `lt_trade` VALUES ('51', '34', '1', '1546661082', '1546661082');
INSERT INTO `lt_trade` VALUES ('52', '35', '1', '1546661082', '1546661082');
INSERT INTO `lt_trade` VALUES ('53', '36', '1', '1546661083', '1546661083');
INSERT INTO `lt_trade` VALUES ('54', '37', '1', '1546661083', '1546661083');
INSERT INTO `lt_trade` VALUES ('55', '38', '1', '1546661083', '1546661083');

-- ----------------------------
-- Table structure for lt_transport
-- ----------------------------
DROP TABLE IF EXISTS `lt_transport`;
CREATE TABLE `lt_transport` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '//id',
  `title` varchar(200) DEFAULT NULL COMMENT '//运输方式',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '//状态',
  `updatetime` int(10) DEFAULT '0' COMMENT '//更新时间',
  `createtime` int(10) DEFAULT '0' COMMENT '//创建时间',
  PRIMARY KEY (`id`),
  KEY `title` (`status`,`title`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COMMENT='//运输方式';

-- ----------------------------
-- Records of lt_transport
-- ----------------------------
INSERT INTO `lt_transport` VALUES ('33', 'transport', '1', '0', '0');
INSERT INTO `lt_transport` VALUES ('35', '运输方式', '1', '1546659809', '1546659809');
INSERT INTO `lt_transport` VALUES ('36', '1', '1', '1546659810', '1546659810');
INSERT INTO `lt_transport` VALUES ('37', '5', '1', '1546659810', '1546659810');
INSERT INTO `lt_transport` VALUES ('38', '11', '1', '1546659811', '1546659811');
INSERT INTO `lt_transport` VALUES ('39', '2', '1', '1546659811', '1546659811');
INSERT INTO `lt_transport` VALUES ('40', '44', '1', '1546659811', '1546659811');
INSERT INTO `lt_transport` VALUES ('41', '3', '1', '1546659812', '1546659812');
INSERT INTO `lt_transport` VALUES ('42', '33', '1', '1546659812', '1546659812');
INSERT INTO `lt_transport` VALUES ('43', '运输方式', '1', '1546661080', '1546661080');
INSERT INTO `lt_transport` VALUES ('44', '1', '1', '1546661081', '1546661081');
INSERT INTO `lt_transport` VALUES ('45', '5', '1', '1546661082', '1546661082');
INSERT INTO `lt_transport` VALUES ('46', '11', '1', '1546661082', '1546661082');
INSERT INTO `lt_transport` VALUES ('47', '2', '1', '1546661082', '1546661082');
INSERT INTO `lt_transport` VALUES ('48', '44', '1', '1546661083', '1546661083');
INSERT INTO `lt_transport` VALUES ('49', '3', '1', '1546661083', '1546661083');
INSERT INTO `lt_transport` VALUES ('50', '33', '1', '1546661083', '1546661083');
