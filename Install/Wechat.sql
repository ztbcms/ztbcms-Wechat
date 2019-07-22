DROP TABLE IF EXISTS `cms_wechat_offices`;
CREATE TABLE `cms_wechat_offices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT '' COMMENT '公众号名称',
  `account_type` varchar(16) DEFAULT '' COMMENT '公众号类型',
  `app_id` varchar(64) DEFAULT '' COMMENT '公众号app_id',
  `secret` varchar(128) DEFAULT '' COMMENT '公众号secret',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `cms_wechat_mini_users`;
CREATE TABLE `cms_wechat_mini_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(64) DEFAULT NULL COMMENT '公众号app_id',
  `open_id` varchar(128) DEFAULT NULL COMMENT '用户openid',
  `union_id` varchar(128) NOT NULL DEFAULT '' COMMENT '开发平台unionid',
  `nick_name` varchar(32) DEFAULT '' COMMENT '昵称',
  `gender` tinyint(4) DEFAULT NULL COMMENT '性别1男2女',
  `language` varchar(16) DEFAULT '' COMMENT '所用语音',
  `city` varchar(32) DEFAULT '' COMMENT '城市',
  `province` varchar(32) DEFAULT '' COMMENT '省份',
  `country` varchar(32) DEFAULT '' COMMENT '国家',
  `avatar_url` varchar(255) DEFAULT '' COMMENT '头像',
  `access_token` varchar(128) DEFAULT '' COMMENT '登录凭证',
  `create_time` int(10) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;