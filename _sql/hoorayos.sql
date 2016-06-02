/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50524
Source Host           : localhost:3306
Source Database       : hoorayos

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2013-06-08 09:16:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tb_app
-- ----------------------------
DROP TABLE IF EXISTS `tb_app`;
CREATE TABLE `tb_app` (
  `tbid` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '图标名称',
  `icon` tinytext COLLATE utf8_unicode_ci COMMENT '图标图片',
  `url` tinytext COLLATE utf8_unicode_ci COMMENT '图标链接',
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '应用类型，app（窗口应用）widget（挂件应用）',
  `width` int(11) DEFAULT NULL COMMENT '窗口宽度',
  `height` int(11) DEFAULT NULL COMMENT '窗口高度',
  `isresize` tinyint(1) DEFAULT NULL COMMENT '是否能对窗口进行拉伸，1（是）0（否）',
  `isopenmax` tinyint(1) DEFAULT NULL COMMENT '是否打开直接最大化，1（是）0（否）',
  `issetbar` tinyint(1) DEFAULT NULL COMMENT '窗口是否有评分和介绍按钮，1（是）0（否）',
  `isflash` tinyint(1) DEFAULT NULL COMMENT '是否为flash应用，1（是）0（否）',
  `remark` longtext COLLATE utf8_unicode_ci COMMENT '备注',
  `usecount` bigint(20) DEFAULT '0' COMMENT '使用人数',
  `starnum` double DEFAULT '0' COMMENT '评分',
  `dt` datetime DEFAULT NULL COMMENT '添加时间',
  `isrecommend` tinyint(1) DEFAULT '0' COMMENT '是否推荐，1（是）0（否）',
  `verifytype` tinyint(1) DEFAULT '0' COMMENT '审核状态，0（未提交审核）1（审核通过）2（审核中）3（审核不通过）',
  `verifyinfo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '审核提示信息',
  `app_category_id` bigint(20) DEFAULT '0' COMMENT '应用类目id',
  `member_id` bigint(20) DEFAULT '0' COMMENT '用户id',
  PRIMARY KEY (`tbid`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tb_app
-- ----------------------------
INSERT INTO `tb_app` VALUES ('1', '应用管理', 'uploads/shortcut/20130505/13677343655235.png', 'sysapp/appmanage/index.php', 'window', '900', '650', '0', '0', '0', '0', '', '0', '0', '2012-02-26 22:51:53', '0', '1', null, '1', '0');
INSERT INTO `tb_app` VALUES ('2', '网站设置', 'img/ui/system-gear.png', 'sysapp/websitesetting/index.php', 'window', '900', '550', '0', '0', '0', '0', '', '0', '0', '2012-02-26 22:52:40', '0', '1', null, '1', '0');
INSERT INTO `tb_app` VALUES ('3', '会员管理', 'img/ui/system-users.png', 'sysapp/member/index.php', 'window', '900', '550', '0', '0', '0', '0', '', '0', '0', '2012-07-19 10:57:28', '0', '1', null, '1', '0');
INSERT INTO `tb_app` VALUES ('4', '权限管理', 'img/ui/system-puzzle.png', 'sysapp/permission/index.php', 'window', '900', '550', '0', '0', '0', '0', '', '0', '0', '2012-07-19 10:59:41', '0', '1', null, '1', '0');
INSERT INTO `tb_app` VALUES ('5', '类目管理', 'img/ui/system-puzzle.png', 'sysapp/category/index.php', 'window', '900', '550', '0', '0', '0', '0', '', '0', '0', '2012-07-19 11:11:41', '0', '1', null, '1', '0');
INSERT INTO `tb_app` VALUES ('6', '豆瓣FM', 'uploads/shortcut/20130505/13677359545258.png', 'http://douban.fm/partner/webqq?fromhoorayos', 'window', '420', '240', '0', '0', '1', '0', '豆瓣FM', '0', '0', '2012-02-26 22:52:03', '0', '1', null, '3', '0');
INSERT INTO `tb_app` VALUES ('7', '百度地图', 'uploads/shortcut/20130505/136773428052.png', 'http://map.baidu.com/', 'window', '1050', '550', '1', '0', '1', '1', '百度地图', '0', '0', '2012-02-26 22:52:55', '0', '1', null, '6', '0');
INSERT INTO `tb_app` VALUES ('8', '美图秀秀', 'uploads/shortcut/20130505/13677342371591.png', 'http://xiuxiu.web.meitu.com/main.html', 'window', '900', '620', '1', '0', '1', '1', '美图秀秀', '0', '0', '2012-02-26 22:52:58', '0', '1', null, '6', '0');
INSERT INTO `tb_app` VALUES ('9', 'AcFun弹幕视频网', 'uploads/shortcut/20130505/13677343281639.png', 'http://m.acfun.tv', 'window', '800', '480', '1', '0', '1', '1', 'AcFun弹幕视频网', '0', '0', '2012-02-26 22:52:24', '0', '1', null, '3', '0');
INSERT INTO `tb_app` VALUES ('10', '搜狐视频', 'uploads/shortcut/20130505/13677343162518.png', 'http://tv.sohu.com/upload/sohuapp/index.html', 'window', '800', '646', '1', '0', '1', '1', '搜狐视频', '0', '0', '2012-02-26 22:52:26', '0', '1', null, '3', '0');
INSERT INTO `tb_app` VALUES ('11', '哔哩哔哩弹幕视频网', 'uploads/shortcut/20130505/13677342975360.png', 'http://www.bilibili.com/mobile/index.html', 'window', '960', '650', '0', '0', '1', '1', '迅雷看看', '0', '0', '2012-02-26 22:52:29', '0', '1', null, '3', '0');
INSERT INTO `tb_app` VALUES ('12', '时钟', 'img/ui/system-shapes.png', 'extapp/clock/index.php', 'widget', '130', '160', '0', '0', '1', '0', '时钟', '0', '0', '2012-08-05 23:01:51', '0', '1', null, '6', '0');
INSERT INTO `tb_app` VALUES ('13', '天气预报', 'img/ui/system-shapes.png', 'extapp/weather/index.php', 'widget', '200', '330', '0', '0', '1', '0', '天气预报', '0', '0', '2012-08-05 23:02:28', '0', '1', null, '6', '0');
INSERT INTO `tb_app` VALUES ('14', '日历', 'uploads/shortcut/20130505/13677342114330.png', 'sysapp/calendar/index.php', 'window', '800', '720', '1', '0', '1', '0', '', '0', '0', '2012-10-16 03:06:17', '0', '1', null, '6', '0');
INSERT INTO `tb_app` VALUES ('15', '愤怒的小鸟', 'uploads/shortcut/20130505/13677340436355.png', 'http://www.3366.com/swf.html?gid=63298&type=0&needframe=0&open=1&FlashParam=&IFrameName=&basedirexp=base20130108095114&adid=3366_1_12413&apiflag=1&NeedOutLink=0', 'window', '800', '512', '1', '0', '1', '1', '鼠标点击拉动弹弓射杀绿猪，滚轮滑动缩放场景。', '0', '0', '2013-05-05 14:07:28', '0', '1', null, '2', '0');
INSERT INTO `tb_app` VALUES ('16', '保卫萝卜', 'uploads/shortcut/20130505/13677346104517.png', 'http://www.3366.com/swf.html?gid=95658&type=0&needframe=0&open=0&FlashParam=&IFrameName=game53.htm&basedirexp=&adid=&apiflag=0&NeedOutLink=0', 'window', '800', '638', '1', '0', '1', '1', '游戏加载完毕点击CONTINUE-选择ADVENTURE(冒险模式)-点击中间的图片-点击PLAY-点击SKIP即可开始游戏', '0', '0', '2013-05-05 14:17:14', '0', '1', null, '2', '0');
INSERT INTO `tb_app` VALUES ('17', '植物大战僵尸', 'uploads/shortcut/20130505/13677346716160.png', 'http://www.3366.com/swf.html?gid=27261&type=0&needframe=1&open=0&FlashParam=&IFrameName=&basedirexp=&adid=', 'window', '640', '542', '0', '0', '1', '1', '鼠标点击不同的植物，根据僵尸行径的路线将放到地图网格上，收集游戏画面中的阳光，布置不同的属性植物发挥不同的技能来阻挡僵尸侵入家园。', '0', '0', '2013-05-05 14:18:09', '0', '1', null, '2', '0');
INSERT INTO `tb_app` VALUES ('18', '小鳄鱼爱洗澡', 'uploads/shortcut/20130505/13677347227549.png', 'http://www.4399.com/360game/93551.htm', 'window', '800', '663', '0', '0', '1', '1', '鼠标拖动消除泥土引导水进鳄鱼的浴缸，某些关卡需点击水管喷水，再消除泥土引导水进浴缸。\r\n提示：要收集三个收藏品才能解锁收藏中的隐藏关卡哦。\r\n注意：游戏如果蓝屏, 刷新即可继续游戏。', '0', '0', '2013-05-05 14:19:48', '1', '1', null, '2', '0');
INSERT INTO `tb_app` VALUES ('19', '水果忍者', 'uploads/shortcut/20130505/13677349543577.png', 'http://www.3366.com/swf.html?gid=94690&type=0&needframe=0&open=1&FlashParam=wmode%3Ddirect&IFrameName=&basedirexp=base20130905193213&adid=3366_1_23255&apiflag=241&NeedOutLink=1', 'window', '640', '542', '0', '0', '1', '1', '经典游戏模仿秀之水果忍者。游戏中包含有经典模式、街机模式和禅模式三种游戏模式等你来玩，各种各样的新鲜水果等你来切！文件较大如果出现白屏，请耐心等待。', '0', '0', '2013-05-05 14:23:02', '0', '1', null, '2', '0');
INSERT INTO `tb_app` VALUES ('20', '三国杀', 'uploads/shortcut/20130505/13677350213580.png', 'http://web.sanguosha.com/', 'window', '1200', '762', '1', '0', '1', '1', '三国杀是一款风靡中国的智力卡牌桌游,以三国为背景、以身份为线索、以武将为角色,构建起一个集历史、文学、美术、游戏等元素于一身的桌面游戏世界', '0', '0', '2013-05-05 14:24:01', '0', '1', null, '2', '0');
INSERT INTO `tb_app` VALUES ('21', '全民斗地主', 'uploads/shortcut/20130505/1367735129442.png', 'http://www.4399.com/360game/117945.htm', 'window', '800', '663', '1', '0', '1', '1', '根据界面提示鼠标点击出牌即可。', '0', '0', '2013-05-05 14:26:00', '0', '1', null, '2', '0');
INSERT INTO `tb_app` VALUES ('22', '神庙逃亡2', 'uploads/shortcut/20130505/13677351957517.png', 'http://www.4399.com/360game/112909.htm', 'window', '800', '663', '0', '0', '1', '1', '方向键←→/AD键控制人物左右移动，按←键或→键控制转向，↑/W键跳跃，↓/S下滑。(鼠标点击拖动也可控制人物跳跃/下滑等)\r\n注意：游戏结束后不要点击main meun哦！', '0', '0', '2013-05-05 14:27:00', '0', '1', null, '2', '0');
INSERT INTO `tb_app` VALUES ('23', '黑八传奇', 'uploads/shortcut/20130505/13677353369336.png', 'http://www.4399.com/360game/33239.htm', 'window', '800', '663', '0', '0', '1', '1', '鼠标控制球杆击球方向，点击左键击球，要注意击球力度哦。', '0', '0', '2013-05-05 14:29:18', '0', '1', null, '2', '0');
INSERT INTO `tb_app` VALUES ('24', '割绳子H5版', 'uploads/shortcut/20130505/13677353829381.png', 'http://www.4399.com/360game/142014.htm', 'window', '800', '663', '0', '0', '1', '1', '鼠标点击拖动割断绳子。', '0', '0', '2013-05-05 14:30:07', '0', '1', null, '2', '0');
INSERT INTO `tb_app` VALUES ('25', '优酷视频', 'uploads/shortcut/20130505/13677356058228.png', 'http://api.youku.com/widget/360box/index.html', 'window', '820', '680', '1', '0', '1', '1', '优酷专注发展主流大气的内容平台定位和专业化的频道运营战略，海量视频库实现垂直定向分类检索，引领互联网视频时代资讯、影视、娱乐综艺潮流。', '0', '0', '2013-05-05 14:33:48', '0', '1', null, '3', '0');
INSERT INTO `tb_app` VALUES ('26', '虾米音乐', 'uploads/shortcut/20130505/13677356703430.png', 'http://m.xiami.com/', 'window', '400', '700', '0', '0', '1', '1', '虾米音乐', '0', '0', '2013-05-05 14:35:28', '0', '1', null, '3', '0');
INSERT INTO `tb_app` VALUES ('27', '爱奇艺', 'uploads/shortcut/20130505/13677357888501.png', 'http://www.qiyi.com/mini/qplus.html', 'window', '1030', '720', '1', '0', '1', '1', '国内首家专注于提供免费、高清网络视频服务的大型专业网站。奇艺影视内容丰富多元，涵盖电影、电视剧、综艺、纪录片、动画片等热门剧目；视频播放清晰流畅，操作界面简单友好，真正为', '0', '0', '2013-05-05 14:37:16', '0', '1', null, '3', '0');
INSERT INTO `tb_app` VALUES ('28', '土豆视频', 'uploads/shortcut/20130505/13677358555620.png', 'http://2010.tudou.com/360api/index.php', 'window', '1030', '800', '1', '0', '1', '1', '大型视频分享网站土豆网的桌面版，具有原创、电视剧、电影、综艺、排行等栏目，用户可以在该网站上传、观看、分享视频短片。', '0', '0', '2013-05-05 14:38:25', '0', '1', null, '3', '0');
INSERT INTO `tb_app` VALUES ('29', '音悦台', 'uploads/shortcut/20130505/13677359969968.png', 'http://www.yinyuetai.com/webqq', 'window', '830', '520', '1', '0', '1', '1', '最新最全最高清的MV尽在音悦台，可以按照您的音乐喜好选择收看华语、日韩、欧美以及新歌首发频道。', '0', '0', '2013-05-05 14:40:33', '0', '1', null, '3', '0');
INSERT INTO `tb_app` VALUES ('30', '酷狗电台', 'uploads/shortcut/20130505/13677361014197.png', 'http://topic.kugou.com/radio/', 'window', '780', '580', '1', '0', '1', '1', '基于酷狗音乐的网页音乐电台。具有海量的音乐资源以及各类超红榜单，在这里，酷狗的各个精彩音乐榜单为您带来前所未有的丰富音乐收听感受。高质量的音质让您重新感受音乐的魅力。', '0', '0', '2013-05-05 14:42:19', '0', '1', null, '3', '0');
INSERT INTO `tb_app` VALUES ('31', 'ONE · 一个', 'uploads/shortcut/20130505/13677362198177.png', 'http://www.wufazhuce.com', 'window', '700', '650', '1', '0', '1', '0', 'ONE · 一个', '0', '0', '2013-05-05 14:44:27', '0', '1', null, '4', '0');
INSERT INTO `tb_app` VALUES ('32', '36氪', 'uploads/shortcut/20130505/1367736387213.png', 'http://36kr.com/', 'window', '1020', '650', '1', '0', '1', '0', '36氪为创业者提供最好的产品和服务', '0', '0', '2013-05-05 14:46:48', '0', '1', null, '4', '0');
INSERT INTO `tb_app` VALUES ('33', '新华字典', 'uploads/shortcut/20130505/13677364305280.png', 'http://xh.5156edu.com/', 'window', '840', '650', '1', '0', '1', '0', '最强近卫是由幻剑书盟提供的一款小说类的web app', '0', '0', '2013-05-05 14:47:28', '0', '1', null, '4', '0');
INSERT INTO `tb_app` VALUES ('34', '校花保镖', 'uploads/shortcut/20130505/13677364692734.png', 'http://book.hjsm.tom.com/106665/360catelog.html', 'window', '1000', '650', '1', '0', '1', '0', '校花保镖是由幻剑书盟提供的一款小说类的web app', '0', '0', '2013-05-05 14:48:11', '0', '1', null, '4', '0');
INSERT INTO `tb_app` VALUES ('35', '我的制服女友', 'uploads/shortcut/20130505/13677365159802.png', 'http://book.hjsm.tom.com/110407/360catelog.html', 'window', '1000', '650', '1', '0', '1', '0', '我的制服女友是由幻剑书盟提供的一款小说类的web app', '0', '0', '2013-05-05 14:48:53', '0', '1', null, '4', '0');
INSERT INTO `tb_app` VALUES ('36', '被妞泡的日子', 'uploads/shortcut/20130505/1367736560985.png', 'http://book.hjsm.tom.com/108669/360catelog.html', 'window', '1000', '650', '1', '0', '1', '0', '被妞泡的日子是由幻剑书盟提供的一款小说类的web app', '0', '0', '2013-05-05 14:49:39', '0', '1', null, '4', '0');
INSERT INTO `tb_app` VALUES ('37', '极品小员工', 'uploads/shortcut/20130505/13677366172043.png', 'http://book.hjsm.tom.com/109038/360catelog.html', 'window', '1000', '650', '1', '0', '1', '0', '极品小员工是由幻剑书盟提供的一款小说类的web app', '0', '0', '2013-05-05 14:50:41', '0', '1', null, '4', '0');
INSERT INTO `tb_app` VALUES ('38', '异世御龙', 'uploads/shortcut/20130505/13677366722127.png', 'http://book.hjsm.tom.com/105977/360catelog.html', 'window', '1000', '650', '1', '0', '1', '0', '异世御龙是由幻剑书盟提供的一款小说类的web app', '0', '0', '2013-05-05 14:51:32', '0', '1', null, '4', '0');
INSERT INTO `tb_app` VALUES ('39', '冷酷校草恋上我', 'uploads/shortcut/20130505/13677367421113.png', 'http://book.hjsm.tom.com/105522/360catelog.html', 'window', '1000', '650', '1', '0', '1', '0', '冷酷校草恋上我是由幻剑书盟提供的一款小说类的web app', '0', '0', '2013-05-05 14:52:43', '0', '1', null, '4', '0');
INSERT INTO `tb_app` VALUES ('40', '赵子龙穿越到都市', 'uploads/shortcut/20130505/1367736867676.png', 'http://book.hjsm.tom.com/109405/360catelog.html', 'window', '1000', '650', '1', '0', '1', '0', '赵子龙穿越到都市是由幻剑书盟提供的一款小说类的web app', '0', '0', '2013-05-05 14:54:49', '0', '1', null, '4', '0');
INSERT INTO `tb_app` VALUES ('41', '天气预报', 'uploads/shortcut/20130505/13677369315643.png', 'http://shenghuo.360.cn/tq/yb?360src=360se', 'window', '420', '680', '1', '0', '1', '0', '精美天气预报应用', '0', '0', '2013-05-05 14:56:07', '0', '1', null, '5', '0');
INSERT INTO `tb_app` VALUES ('42', '快递查询', 'uploads/shortcut/20130505/13677370021209.png', 'http://www.kuaidi100.com/frame/qq/webqq/?canvas_pos=2016', 'window', '560', '460', '1', '0', '1', '0', '快递查询是友商网快递100开发的，提供海量的快递查询服务，涵盖近百家常用快递公司，查询无需验 证码，支持手机查快递，并为B2C等网络应用提供免费的快递查询接口API。', '0', '0', '2013-05-05 14:57:14', '0', '1', null, '5', '0');
INSERT INTO `tb_app` VALUES ('43', '万年历', 'uploads/shortcut/20130505/1367737151617.png', 'http://www.saturdaysoft.com/prod/360apc/calendar.html', 'window', '540', '450', '0', '0', '1', '0', '方便易用的万年历应用，具有日历浏览方式，可以快速切换到相邻月份、跳转到指定年月或回到当天，具有详细准确的节日和节气信息，支持黄历、天气等功能。', '0', '0', '2013-05-05 14:59:42', '0', '1', null, '5', '0');
INSERT INTO `tb_app` VALUES ('44', '城市间路程油费路桥费查询', 'uploads/shortcut/20130505/13677372407307.png', 'http://www.pcwcn.com/city_search?type=alone&bd_user=0&bd_sig=bb7229df73013a1a18f947b4c929e2b7', 'window', '560', '400', '0', '0', '1', '0', '两个城市之间的路程、开车时间、油费、路桥费查询工具。输入出发城市和目的城市，即可查询哦！本应用为拼车网（www.pcwcn.com）提供的一个实用生活类查询工具。我们的数据来源于拼车网，', '0', '0', '2013-05-05 15:01:22', '0', '1', null, '5', '0');
INSERT INTO `tb_app` VALUES ('45', '手机充值', 'uploads/shortcut/20130505/13677373176723.png', 'http://shenghuo.360.cn/dapp', 'window', '540', '350', '0', '0', '1', '0', '具有省时、便捷、不受地域缴费充值的限制等优点。用户通过网上银行缴费，即可实现为手机号码充入话费，使用户不受银行营业时间、网点的限制，真正实现“随时随地”支付交易，享受轻松', '0', '0', '2013-05-05 15:02:36', '0', '1', null, '5', '0');
INSERT INTO `tb_app` VALUES ('46', '身份证查询', 'uploads/shortcut/20130505/13677373916735.png', 'http://360app.45451.com/sfz', 'window', '580', '380', '0', '0', '1', '0', '身份证号码查询验证工具。可自动识别发证地、出生年月日、性别等信息，如果为15位身份证号，该软件可自动生成18位身份证号。', '0', '0', '2013-05-05 15:03:56', '0', '1', null, '5', '0');
INSERT INTO `tb_app` VALUES ('47', '酷讯火车票', 'uploads/shortcut/20130505/13677375795763.png', 'http://n.huoche.kuxun.cn/?fromid=Kapc360cn-S1205011-T1183881', 'window', '790', '630', '0', '0', '1', '0', '酷讯火车票、特价机票查询平台。酷讯火车票平台，每年有2亿人使用，数据准确，更新及时，是国内最早，用户量最大的火车票转让平台之一。', '0', '0', '2013-05-05 15:07:01', '0', '1', null, '5', '0');
INSERT INTO `tb_app` VALUES ('48', '减肥食谱', 'uploads/shortcut/20130505/1367737648336.png', 'http://www.pcbaby.com.cn/360/diet/', 'window', '820', '640', '0', '0', '1', '0', '太平洋亲子网制作的减肥食谱，在奉承以瘦为美的时代，推荐多种具有减肥功效的食谱，也提醒广大女性健康的减肥方法：“民以食为天”，减肥是靠饮食调理，而不是靠节食。', '0', '0', '2013-05-05 15:08:04', '0', '1', null, '5', '0');
INSERT INTO `tb_app` VALUES ('49', '眼睛保健操', 'uploads/shortcut/20130505/13677377979610.png', 'http://apps.qiyigoo.com/yanbaojiancao/', 'window', '570', '600', '0', '0', '1', '0', '眼睛保健操，让眼镜休息一会。提供多种眼睛运动方式，根据自己的运动程度调节运动速度，还提供了一些常用的眼睛保健方法，包含：提高视力的方法，改善视力的食物，保护眼睛的方法。', '0', '0', '2013-05-05 15:10:24', '0', '1', null, '5', '0');
INSERT INTO `tb_app` VALUES ('50', 'KTV大全', 'uploads/shortcut/20130505/13677378833444.png', 'http://www.51huoban.com/baidu/ktv/index.php', 'window', '560', '540', '0', '0', '1', '0', '最新最实用的ktv大全，想去哪唱K这里都有好介绍。根据自身不同的城市需要，点击选择不同的城市。查看城市KTV的相关信息。', '0', '0', '2013-05-05 15:11:58', '0', '1', null, '5', '0');
INSERT INTO `tb_app` VALUES ('51', '语音计算器', 'uploads/shortcut/20130505/13677380592462.png', 'http://app.qiqi.cc/apps/video_calc/', 'window', '560', '530', '0', '0', '1', '1', '语音计算器除了实现普通的计算器功能之外，还具备了独特的语音发声技术；支持鼠标、键盘输入的功能，有助于加快计算时所需要的时间；支持计算出结果后继续计算的功能，操作方法跟系统', '0', '0', '2013-05-05 15:15:05', '0', '1', null, '6', '0');
INSERT INTO `tb_app` VALUES ('52', '有道词典', 'uploads/shortcut/20130505/1367738149614.png', 'http://dict.youdao.com/app/360', 'window', '554', '552', '0', '0', '1', '0', '有道在线词典web版本。有道词典结合了互联网在线词典和桌面词典的优势，具备中英、英中、英英翻译、汉语词典功能。同时，创新的\"网络释义\"功能将各类新兴词汇和英文缩写收录其中。依托', '0', '0', '2013-05-05 15:16:30', '0', '1', null, '6', '0');
INSERT INTO `tb_app` VALUES ('53', '交通标志', 'uploads/shortcut/20130505/13677384429066.png', 'http://cnzaobao.com/360se/jiaotongbiaozhi.html', 'window', '600', '700', '0', '0', '1', '1', '交通标志大全，安全出行好助手！ 熟记交通标志大全，为你考证，为你出行，为你安全提供极大的帮助。', '0', '0', '2013-05-05 15:21:17', '0', '1', null, '6', '0');
INSERT INTO `tb_app` VALUES ('54', '秒表计时器', 'uploads/shortcut/20130505/13677387064869.png', 'http://5.app100672505.twsapp.com/bd_miaodao.php', 'window', '580', '440', '0', '0', '1', '0', '秒表功能精确到毫秒，在计时过程中可以计次，最多可计100次，点击复位重新初始化秒表；倒计时可以设置最长时间单位为小时的时间倒数，具有到点铃声提醒功能', '0', '0', '2013-05-05 15:25:53', '0', '1', null, '6', '0');
INSERT INTO `tb_app` VALUES ('55', '阴历阳历转换', 'uploads/shortcut/20130505/13677388786.png', 'http://5.app100672505.twsapp.com/ada_yrzh/', 'window', '570', '470', '0', '0', '1', '0', '阴历阳历转换器，从你所知道的公历或者阳历转换成对应的农历或阴历；一键转换，方便快捷；阴历阳历转换器，程序自动判断年份中是否包含了闰月', '0', '0', '2013-05-05 15:28:30', '0', '1', null, '6', '0');

-- ----------------------------
-- Table structure for tb_app_category
-- ----------------------------
DROP TABLE IF EXISTS `tb_app_category`;
CREATE TABLE `tb_app_category` (
  `tbid` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '名称',
  `issystem` tinyint(1) DEFAULT '0' COMMENT '是否为系统类目，1（是）0（否）',
  PRIMARY KEY (`tbid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tb_app_category
-- ----------------------------
INSERT INTO `tb_app_category` VALUES ('1', '系统', '1');
INSERT INTO `tb_app_category` VALUES ('2', '游戏', '0');
INSERT INTO `tb_app_category` VALUES ('3', '影音', '0');
INSERT INTO `tb_app_category` VALUES ('4', '图书', '0');
INSERT INTO `tb_app_category` VALUES ('5', '生活', '0');
INSERT INTO `tb_app_category` VALUES ('6', '工具', '0');

-- ----------------------------
-- Table structure for `tb_app_star`
-- ----------------------------
DROP TABLE IF EXISTS `tb_app_star`;
CREATE TABLE `tb_app_star` (
  `tbid` bigint(20) NOT NULL AUTO_INCREMENT,
  `app_id` bigint(20) DEFAULT NULL COMMENT '应用id',
  `member_id` bigint(20) DEFAULT NULL COMMENT '用户id',
  `starnum` int(1) DEFAULT '0' COMMENT '评分',
  `dt` datetime DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`tbid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tb_app_star
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_calendar`
-- ----------------------------
DROP TABLE IF EXISTS `tb_calendar`;
CREATE TABLE `tb_calendar` (
  `tbid` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '标题',
  `content` longtext COLLATE utf8_unicode_ci COMMENT '详细内容',
  `url` text COLLATE utf8_unicode_ci COMMENT '超链接',
  `startdt` datetime DEFAULT NULL COMMENT '开始时间',
  `enddt` datetime DEFAULT NULL COMMENT '结束时间',
  `isallday` tinyint(1) DEFAULT '1' COMMENT '是否属于全天任务',
  `member_id` bigint(20) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`tbid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tb_calendar
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_member`
-- ----------------------------
DROP TABLE IF EXISTS `tb_member`;
CREATE TABLE `tb_member` (
  `tbid` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '用户名',
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '密码',
  `lockpassword` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '锁屏密码',
  `type` tinyint(1) DEFAULT '0' COMMENT '用户类型，0（普通用户）1（管理员）',
  `permission_id` bigint(20) DEFAULT NULL COMMENT '权限id',
  `dock` longtext COLLATE utf8_unicode_ci COMMENT '[应用码头]应用id，用","相连',
  `desk1` longtext COLLATE utf8_unicode_ci COMMENT '[桌面1]应用id，用","相连',
  `desk2` longtext COLLATE utf8_unicode_ci COMMENT '[桌面2]应用id，用","相连',
  `desk3` longtext COLLATE utf8_unicode_ci COMMENT '[桌面3]应用id，用","相连',
  `desk4` longtext COLLATE utf8_unicode_ci COMMENT '[桌面4]应用id，用","相连',
  `desk5` longtext COLLATE utf8_unicode_ci COMMENT '[桌面5]应用id，用","相连',
  `appxy` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'x' COMMENT '图标排列方式，x（横向排列）y（纵向排列）',
  `desk` tinyint(1) DEFAULT '1' COMMENT '默认桌面',
  `dockpos` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'top' COMMENT '应用码头位置，top（顶部）left（左侧）right（右侧）none（隐藏）',
  `appsize` int(11) DEFAULT '48' COMMENT '图标尺寸',
  `appverticalspacing` int(11) DEFAULT '50' COMMENT '图标垂直间距',
  `apphorizontalspacing` int(11) DEFAULT '50' COMMENT '图标水平间距',
  `wallpaper_id` int(11) DEFAULT '1' COMMENT '壁纸id',
  `wallpaperwebsite` text COLLATE utf8_unicode_ci COMMENT '壁纸网址',
  `wallpaperstate` tinyint(4) DEFAULT '1' COMMENT '1（系统壁纸）2（自定义壁纸）3（网络地址）',
  `wallpapertype` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'juzhong' COMMENT '壁纸显示方式，tianchong（填充）shiying（适应）pingpu（平铺）lashen（拉伸）juzhong（居中）',
  `skin` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'default' COMMENT '窗口皮肤',
  `regdt` datetime DEFAULT NULL COMMENT '注册时间',
  `lastlogindt` datetime DEFAULT NULL COMMENT '上次登录时间',
  `lastloginip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '上次登录IP',
  `thislogindt` datetime DEFAULT NULL COMMENT '本次登陆时间',
  `thisloginip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '本次登录IP',
  `openid_qq` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `openname_qq` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `openavatar_qq` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `openurl_qq` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `openid_weibo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `openname_weibo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `openavatar_weibo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `openurl_weibo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`tbid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tb_member
-- ----------------------------
INSERT INTO `tb_member` VALUES ('1', 'hoorayos', 'c5e9fe42f061fa6102857db920734c33ec7b0816', 'c5e9fe42f061fa6102857db920734c33ec7b0816', '1', '1', '', '', '', '', '', '', 'x', '1', 'top', '48', '50', '50', '1', null, '1', 'lashen', 'default', '2012-02-29 00:00:00', '2012-02-29 00:00:00', '0.0.0.0', '2012-02-29 00:00:00', '0.0.0.0', null, null, null, null, null, null, null, null);

-- ----------------------------
-- Table structure for `tb_member_app`
-- ----------------------------
DROP TABLE IF EXISTS `tb_member_app`;
CREATE TABLE `tb_member_app` (
  `tbid` bigint(20) NOT NULL AUTO_INCREMENT,
  `realid` bigint(20) DEFAULT '0' COMMENT '真实id',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '图标名称',
  `icon` tinytext COLLATE utf8_unicode_ci COMMENT '图标图片',
  `url` tinytext COLLATE utf8_unicode_ci COMMENT '应用地址',
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '应用类型',
  `width` int(11) DEFAULT NULL COMMENT '窗口宽度',
  `height` int(11) DEFAULT NULL COMMENT '窗口高度',
  `isresize` tinyint(11) DEFAULT NULL COMMENT '是否能对窗口进行拉伸',
  `isopenmax` tinyint(4) DEFAULT NULL COMMENT '是否打开直接最大化',
  `issetbar` tinyint(4) DEFAULT NULL COMMENT '窗口是否有评分和介绍按钮',
  `isflash` tinyint(4) DEFAULT NULL COMMENT '是否为flash应用',
  `ext` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '扩展名',
  `size` int(11) DEFAULT NULL COMMENT '文件大小',
  `dt` datetime DEFAULT NULL COMMENT '创建时间',
  `lastdt` datetime DEFAULT NULL COMMENT '最后修改时间',
  `folder_id` bigint(20) DEFAULT '0' COMMENT '文件夹id',
  `member_id` bigint(20) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`tbid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tb_member_app
-- ----------------------------

-- ----------------------------
-- Table structure for tb_permission
-- ----------------------------
DROP TABLE IF EXISTS `tb_permission`;
CREATE TABLE `tb_permission` (
  `tbid` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '权限名称',
  `apps_id` longtext COLLATE utf8_unicode_ci COMMENT '权限关联应用id，用","相连',
  PRIMARY KEY (`tbid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tb_permission
-- ----------------------------
INSERT INTO `tb_permission` VALUES ('1', '平台管理员', '1,2,3,4,5');
INSERT INTO `tb_permission` VALUES ('2', '应用管理员', '1');
INSERT INTO `tb_permission` VALUES ('3', '会员管理员', '3');
INSERT INTO `tb_permission` VALUES ('4', '网站设置管理员', '2');
INSERT INTO `tb_permission` VALUES ('5', '类目管理员', '5');

-- ----------------------------
-- Table structure for `tb_pwallpaper`
-- ----------------------------
DROP TABLE IF EXISTS `tb_pwallpaper`;
CREATE TABLE `tb_pwallpaper` (
  `tbid` bigint(20) NOT NULL AUTO_INCREMENT,
  `url` tinytext COLLATE utf8_unicode_ci COMMENT '壁纸地址',
  `width` int(11) DEFAULT NULL COMMENT '壁纸宽度',
  `height` int(11) DEFAULT NULL COMMENT '壁纸高度',
  `member_id` bigint(20) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`tbid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tb_pwallpaper
-- ----------------------------

-- ----------------------------
-- Table structure for `tb_setting`
-- ----------------------------
DROP TABLE IF EXISTS `tb_setting`;
CREATE TABLE `tb_setting` (
  `tbid` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` text COMMENT '网站标题',
  `description` text COMMENT '网站描述',
  `keywords` text COMMENT '网站关键字',
  `dock` longtext COMMENT '[应用码头]应用id，用","相连',
  `desk1` longtext COMMENT '[桌面1]应用id，用","相连',
  `desk2` longtext COMMENT '[桌面2]应用id，用","相连',
  `desk3` longtext COMMENT '[桌面3]应用id，用","相连',
  `desk4` longtext COMMENT '[桌面4]应用id，用","相连',
  `desk5` longtext COMMENT '[桌面5]应用id，用","相连',
  `desk` tinyint(1) DEFAULT '1' COMMENT '默认显示第几桌面',
  `appxy` varchar(255) DEFAULT 'x' COMMENT '图标排列方式，x（横向排列）y（纵向排列）',
  `appsize` int(11) DEFAULT '48' COMMENT '图标尺寸',
  `appverticalspacing` int(11) DEFAULT '50' COMMENT '图标垂直间距',
  `apphorizontalspacing` int(11) DEFAULT '50' COMMENT '图标水平间距',
  `dockpos` varchar(255) DEFAULT 'top' COMMENT '应用码头位置，top（顶部）left（左侧）right（右侧）none（隐藏）',
  `skin` varchar(255) DEFAULT 'default' COMMENT '窗口皮肤',
  `wallpaper_id` int(11) DEFAULT '1',
  `wallpapertype` varchar(255) DEFAULT 'juzhong' COMMENT '壁纸显示方式，tianchong（填充）shiying（适应）pingpu（平铺）lashen（拉伸）juzhong（居中）',
  `isforcedlogin` tinyint(1) DEFAULT '1' COMMENT '是否开启强制登录，1开启0不开启',
  PRIMARY KEY (`tbid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tb_setting
-- ----------------------------
INSERT INTO `tb_setting` VALUES ('1', 'HoorayOS桌面应用框架', 'HoorayOS是一套web桌面应用框架，你可以用它开发出类似于Q+web这类的桌面应用网站，也可以在它的基础上二次开发出适合项目的桌面式管理系统。', 'HoorayOS,web桌面,免费开源,桌面管理系统', '14', '55,54,53,52,51', '50,49,48,47,46', '45,44,43,41,42', '30,29,28,27,26', '20,19,18,17,16', '1', 'x', '32', '50', '50', 'top', 'default', '1', 'juzhong', '0');

-- ----------------------------
-- Table structure for `tb_wallpaper`
-- ----------------------------
DROP TABLE IF EXISTS `tb_wallpaper`;
CREATE TABLE `tb_wallpaper` (
  `tbid` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '壁纸名称',
  `url` tinytext COLLATE utf8_unicode_ci COMMENT '壁纸地址',
  `width` int(11) DEFAULT NULL COMMENT '壁纸宽度',
  `height` int(11) DEFAULT NULL COMMENT '壁纸高度',
  PRIMARY KEY (`tbid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of tb_wallpaper
-- ----------------------------
INSERT INTO `tb_wallpaper` VALUES ('1', '壁纸1', 'img/wallpaper/wallpaper1.jpg', '1920', '1080');
INSERT INTO `tb_wallpaper` VALUES ('2', '壁纸2', 'img/wallpaper/wallpaper2.jpg', '1920', '1080');
INSERT INTO `tb_wallpaper` VALUES ('3', '壁纸3', 'img/wallpaper/wallpaper3.jpg', '1920', '1080');
INSERT INTO `tb_wallpaper` VALUES ('4', '壁纸4', 'img/wallpaper/wallpaper4.jpg', '1920', '1080');
INSERT INTO `tb_wallpaper` VALUES ('5', '壁纸5', 'img/wallpaper/wallpaper5.jpg', '1920', '1080');
INSERT INTO `tb_wallpaper` VALUES ('6', '壁纸6', 'img/wallpaper/wallpaper6.jpg', '1920', '1080');
INSERT INTO `tb_wallpaper` VALUES ('7', '壁纸7', 'img/wallpaper/wallpaper7.jpg', '1920', '1080');
INSERT INTO `tb_wallpaper` VALUES ('8', '壁纸8', 'img/wallpaper/wallpaper8.jpg', '1920', '1080');
INSERT INTO `tb_wallpaper` VALUES ('9', '壁纸9', 'img/wallpaper/wallpaper9.jpg', '1920', '1080');
