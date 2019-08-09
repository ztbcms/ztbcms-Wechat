DROP TABLE IF EXISTS `cms_wechat_offices`;
CREATE TABLE `cms_wechat_offices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT '' COMMENT '公众号名称',
  `account_type` varchar(16) DEFAULT '' COMMENT '公众号类型',
  `app_id` varchar(64) DEFAULT '' COMMENT '公众号app_id',
  `secret` varchar(128) DEFAULT '' COMMENT '公众号secret',
  `mch_id` varchar(64) DEFAULT '' COMMENT '微信支付mch_id',
  `key` varchar(128) DEFAULT '' COMMENT '微信支付key',
  `cert_path` varchar(512) DEFAULT '' COMMENT '微信支付公钥',
  `key_path` varchar(512) DEFAULT '' COMMENT '微信支付私钥',
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

DROP TABLE IF EXISTS `cms_wechat_mini_template_list`;
CREATE TABLE `cms_wechat_mini_template_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(64) NOT NULL DEFAULT '',
  `template_id` varchar(128) DEFAULT NULL COMMENT '模板id',
  `title` varchar(32) DEFAULT '' COMMENT '模板消息标题',
  `example` varchar(512) DEFAULT NULL COMMENT '模板消息示例',
  `content` varchar(512) DEFAULT '' COMMENT '模板消息内容',
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `cms_wechat_mini_phone_number`;
CREATE TABLE `cms_wechat_mini_phone_number` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(64) DEFAULT '',
  `open_id` varchar(128) DEFAULT NULL,
  `country_code` varchar(12) DEFAULT '' COMMENT '国家代码',
  `phone_number` varchar(32) DEFAULT '' COMMENT '电话号码',
  `pure_phone_number` varchar(32) DEFAULT '' COMMENT '不知道是什么',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `cms_wechat_mini_form_id`;
CREATE TABLE `cms_wechat_mini_form_id` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(64) DEFAULT NULL,
  `open_id` varchar(128) DEFAULT '' COMMENT '用户openid',
  `form_id` varchar(128) DEFAULT '' COMMENT '发送模板消息使用的form_id',
  `used` tinyint(1) DEFAULT '0' COMMENT '是否使用0否1是',
  `result` varchar(512) DEFAULT '' COMMENT '发送结果',
  `send_time` int(11) DEFAULT '0' COMMENT '发送时间',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `cms_wechat_mini_code`;
CREATE TABLE `cms_wechat_mini_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(64) DEFAULT '' COMMENT 'appid',
  `type` varchar(16) DEFAULT '' COMMENT '小程序码类型',
  `path` varchar(128) DEFAULT '' COMMENT '二维码路径',
  `scene` varchar(64) DEFAULT '' COMMENT '场景值（不限制二维码需要传）',
  `file_name` varchar(128) DEFAULT '' COMMENT '图片名称',
  `file_url` varchar(258) DEFAULT '' COMMENT '图片URL访问地址',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `cms_wechat_wxpay_mchpay`;
CREATE TABLE `cms_wechat_wxpay_mchpay` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(64) DEFAULT '',
  `partner_trade_no` varchar(128) DEFAULT '' COMMENT '商户订单号',
  `open_id` varchar(128) DEFAULT '0' COMMENT '用户openid',
  `amount` int(11) DEFAULT '0' COMMENT '付款金额',
  `description` varchar(512) DEFAULT '' COMMENT '付款描述',
  `refund_result` varchar(1024) DEFAULT '' COMMENT '付款结果',
  `status` tinyint(1) DEFAULT '0' COMMENT '处理状态',
  `next_process_time` int(11) DEFAULT '0' COMMENT '下次处理时间',
  `process_count` int(11) DEFAULT '0' COMMENT '处理次数',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `cms_wechat_wxpay_order`;
CREATE TABLE `cms_wechat_wxpay_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(64) DEFAULT '',
  `mch_id` varchar(64) DEFAULT NULL COMMENT '微信支付商户号',
  `nonce_str` varchar(64) DEFAULT '' COMMENT '随机字符串',
  `sign` varchar(128) DEFAULT '' COMMENT '签名',
  `result_code` varchar(128) DEFAULT '' COMMENT '业务结果',
  `err_code` varchar(128) DEFAULT '' COMMENT '返回错误信息',
  `err_code_des` varchar(128) DEFAULT '' COMMENT '错误信息描述',
  `open_id` varchar(128) DEFAULT NULL COMMENT '用户openid',
  `is_subscribe` varchar(128) DEFAULT '' COMMENT '是否关注',
  `trade_type` varchar(32) DEFAULT '' COMMENT '支付类型',
  `bank_type` varchar(32) DEFAULT '' COMMENT '银行类型',
  `total_fee` int(11) DEFAULT '0' COMMENT '支付金额',
  `cash_fee` int(11) DEFAULT '0' COMMENT '现金支付金额',
  `transaction_id` varchar(128) DEFAULT '' COMMENT '微信支付单号',
  `out_trade_no` varchar(128) DEFAULT '' COMMENT '商户单号',
  `time_end` varchar(64) DEFAULT '' COMMENT '支付时间',
  `notify_url` varchar(512) DEFAULT '' COMMENT '回调地址',
  `create_time` int(11) DEFAULT '0' COMMENT '支付时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `cms_wechat_wxpay_redpack`;
CREATE TABLE `cms_wechat_wxpay_redpack` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(64) DEFAULT '',
  `mch_billno` varchar(128) DEFAULT '' COMMENT '商户订单号',
  `open_id` varchar(128) DEFAULT '0' COMMENT '用户openid',
  `total_amount` int(11) DEFAULT '0' COMMENT '发送金额',
  `send_name` varchar(32) DEFAULT '' COMMENT '发送者名称',
  `wishing` varchar(128) DEFAULT '' COMMENT '祝福语',
  `act_name` varchar(512) DEFAULT '' COMMENT '活动名称',
  `remark` varchar(128) DEFAULT '' COMMENT '备注',
  `send_result` varchar(1024) DEFAULT '' COMMENT '发送结果',
  `status` tinyint(1) DEFAULT '0' COMMENT '处理状态',
  `next_process_time` int(11) DEFAULT '0' COMMENT '下次处理时间',
  `process_count` int(11) DEFAULT '0' COMMENT '处理次数',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `cms_wechat_wxpay_refund`;
CREATE TABLE `cms_wechat_wxpay_refund` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` varchar(64) DEFAULT '',
  `out_trade_no` varchar(128) DEFAULT '' COMMENT '支付订单号',
  `out_refund_no` varchar(128) DEFAULT '' COMMENT '退款单号',
  `total_fee` int(11) DEFAULT '0' COMMENT '订单总金额',
  `refund_fee` int(11) DEFAULT '0' COMMENT '退款金额',
  `refund_description` varchar(512) DEFAULT '' COMMENT '退款理由',
  `refund_result` varchar(1024) DEFAULT '' COMMENT '退款结果',
  `status` tinyint(1) DEFAULT '0' COMMENT '处理状态',
  `next_process_time` int(11) DEFAULT '0' COMMENT '下次处理时间',
  `process_count` int(11) DEFAULT '0' COMMENT '处理次数',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;