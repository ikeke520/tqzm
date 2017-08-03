SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `stcms_activity` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(90) NOT NULL,
  `cid` smallint(5) UNSIGNED NOT NULL,
  `cname` varchar(20) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `person_num` tinyint(3) UNSIGNED NOT NULL,
  `in_num` tinyint(3) UNSIGNED NOT NULL,
  `adult_along` enum('0','1') NOT NULL DEFAULT '0',
  `year_duration` varchar(30) NOT NULL,
  `activity_type` varchar(30) NOT NULL,
  `price` decimal(9,2) NOT NULL,
  `date` date NOT NULL,
  `timestr` varchar(30) NOT NULL,
  `nav_url` varchar(255) NOT NULL,
  `address` varchar(120) NOT NULL,
  `intro` text NOT NULL,
  `notice` text NOT NULL,
  `hit` mediumint(8) UNSIGNED NOT NULL,
  `pubtime` datetime NOT NULL,
  `is_rmd` enum('0','1') NOT NULL DEFAULT '0',
  `is_complete` enum('0','1') NOT NULL DEFAULT '0',
  `payway` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_activity_picture` (
  `id` int(10) UNSIGNED NOT NULL,
  `aid` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `index_order` tinyint(3) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_admin` (
  `id` int(11) NOT NULL,
  `name` varchar(16) NOT NULL,
  `pwd` varchar(32) NOT NULL,
  `last_login` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  `last_ip` varchar(15) NOT NULL,
  `times` mediumint(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `stcms_admin` (`id`, `name`, `pwd`, `last_login`, `ip`, `last_ip`, `times`) VALUES
(1, 'admin', '99b15677ad4a806edf82f4a08375ffe6', '2017-06-16 09:39:07', '222.247.166.170', '', 218),
(2, '老杨', '8b47aa67de9baf09811ad58468a8ad9f', '2017-06-15 17:07:06', '121.12.105.69', '127.0.0.1', 3);

CREATE TABLE `stcms_adminlog` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(18) NOT NULL,
  `msg` varchar(100) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_apply` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `company` varchar(60) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `is_read` enum('0','1') NOT NULL DEFAULT '0',
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_card` (
  `id` int(11) NOT NULL,
  `is_trial` enum('0','1') NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL,
  `url` varchar(255) NOT NULL,
  `price` decimal(9,2) NOT NULL,
  `org_price` decimal(9,2) NOT NULL,
  `num` tinyint(3) UNSIGNED NOT NULL,
  `intro` text NOT NULL,
  `index_order` smallint(5) UNSIGNED NOT NULL,
  `is_del` enum('0','1') DEFAULT '0',
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_cardbase` (
  `id` int(11) NOT NULL,
  `cardno` varchar(16) NOT NULL,
  `hash` varchar(16) NOT NULL,
  `name` varchar(30) NOT NULL,
  `price` decimal(9,2) NOT NULL,
  `num` tinyint(3) UNSIGNED NOT NULL,
  `is_use` enum('0','1') NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL,
  `uname` varchar(16) NOT NULL,
  `wid` int(11) NOT NULL,
  `worker` varchar(12) NOT NULL,
  `is_active` enum('0','1') NOT NULL DEFAULT '0',
  `sell_time` datetime NOT NULL,
  `active_time` datetime NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_category` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(36) NOT NULL,
  `index_order` smallint(5) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_comment` (
  `id` int(10) UNSIGNED NOT NULL,
  `aid` int(10) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL,
  `uname` varchar(30) NOT NULL,
  `content` varchar(256) NOT NULL,
  `is_hide` enum('0','1') NOT NULL DEFAULT '0',
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_config` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `seo_title` varchar(255) NOT NULL,
  `seo_keyword` varchar(255) NOT NULL,
  `seo_desc` varchar(255) NOT NULL,
  `coin_switch` enum('0','1') DEFAULT '0',
  `user_coin` smallint(5) UNSIGNED NOT NULL,
  `cash_coin` smallint(5) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `stcms_config` (`id`, `seo_title`, `seo_keyword`, `seo_desc`, `coin_switch`, `user_coin`, `cash_coin`) VALUES
(1, '童趣周末', '童趣周末，儿童周末，周末活动，儿童活动，亲子互动，', '童趣周末，专注于策划儿童的周末安排，为小朋友们的健康成长营造良好的氛围。', '0', 100, 1);

CREATE TABLE `stcms_message` (
  `id` int(11) NOT NULL,
  `msgid` varchar(64) NOT NULL,
  `type` set('text','image','vedio','voice','event','other') NOT NULL,
  `openid` varchar(64) NOT NULL,
  `uid` int(11) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `content` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_news` (
  `id` int(11) NOT NULL,
  `cid` smallint(5) UNSIGNED NOT NULL,
  `cname` varchar(30) NOT NULL,
  `title` varchar(90) NOT NULL,
  `url` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `time` datetime NOT NULL,
  `hit` mediumint(8) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_newscat` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_setting_info` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `type` enum('faq','intro','corp','link','apply') NOT NULL,
  `content` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `stcms_slider` (
  `id` int(10) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL,
  `intro` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `is_hide` enum('0','1') NOT NULL DEFAULT '0',
  `index_order` tinyint(3) UNSIGNED NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_tag` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(36) NOT NULL,
  `child_num` mediumint(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_tag_list` (
  `id` int(10) UNSIGNED NOT NULL,
  `tid` mediumint(8) UNSIGNED NOT NULL,
  `tname` varchar(30) NOT NULL,
  `aid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `openid` varchar(64) NOT NULL,
  `unionid` varchar(64) NOT NULL,
  `is_reg` enum('0','1') NOT NULL DEFAULT '0',
  `nickname` varchar(30) NOT NULL,
  `sex` set('0','1','2') NOT NULL DEFAULT '0',
  `province` varchar(60) NOT NULL,
  `city` varchar(60) NOT NULL,
  `country` varchar(20) NOT NULL,
  `headimgurl` varchar(256) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `phone_hash` varchar(32) NOT NULL,
  `phone_time` datetime NOT NULL,
  `return_coin` int(11) UNSIGNED NOT NULL,
  `ip` varchar(30) NOT NULL,
  `last_ip` varchar(30) NOT NULL,
  `log_time` datetime NOT NULL,
  `last_time` datetime NOT NULL,
  `reg_time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_users_auth` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `aid` int(10) UNSIGNED NOT NULL,
  `cid` int(10) UNSIGNED NOT NULL,
  `tradeno` varchar(16) NOT NULL,
  `is_finish` enum('0','1') NOT NULL DEFAULT '0',
  `is_cancel` enum('0','1') NOT NULL DEFAULT '0',
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_users_auth_person` (
  `id` int(11) NOT NULL,
  `aid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `is_adult` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_users_card` (
  `id` int(11) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `is_real` enum('0','1') NOT NULL DEFAULT '0',
  `cardno` varchar(16) NOT NULL,
  `cid` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `price` decimal(9,2) NOT NULL,
  `num` tinyint(3) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL,
  `cost_num` tinyint(3) UNSIGNED NOT NULL,
  `is_finish` enum('0','1') NOT NULL DEFAULT '0',
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_users_coinlog` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(11) NOT NULL,
  `type` enum('0','1','2') NOT NULL DEFAULT '0',
  `coin` mediumint(8) UNSIGNED NOT NULL,
  `tradeno` varchar(16) NOT NULL,
  `msg` varchar(30) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE `stcms_users_fav` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `aid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_users_paylog` (
  `id` int(11) NOT NULL,
  `type` enum('0','1') NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL,
  `uname` varchar(30) NOT NULL,
  `price_id` int(10) UNSIGNED NOT NULL,
  `ctype` enum('0','1') NOT NULL DEFAULT '0',
  `coin` mediumint(8) UNSIGNED NOT NULL,
  `money` decimal(9,2) UNSIGNED NOT NULL,
  `tradeno` varchar(30) NOT NULL,
  `msg` varchar(90) NOT NULL,
  `is_payed` enum('0','1') NOT NULL DEFAULT '0',
  `is_refund` enum('0','1') DEFAULT '0',
  `refund_id` varchar(32) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_users_person` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(11) UNSIGNED NOT NULL,
  `is_adult` enum('0','1') NOT NULL DEFAULT '0',
  `name` varchar(16) NOT NULL,
  `sex` enum('0','1') NOT NULL DEFAULT '0',
  `birthday` date NOT NULL,
  `address` varchar(99) NOT NULL,
  `idno` varchar(18) NOT NULL,
  `phone` varchar(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_users_relation` (
  `id` int(10) UNSIGNED NOT NULL,
  `pid` int(10) UNSIGNED NOT NULL,
  `pname` varchar(30) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `uname` varchar(30) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_worker` (
  `id` int(11) UNSIGNED NOT NULL,
  `uid` int(11) UNSIGNED NOT NULL,
  `realname` varchar(30) NOT NULL,
  `idno` varchar(18) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `address` varchar(90) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `stcms_worklog` (
  `id` int(11) UNSIGNED NOT NULL,
  `wid` int(11) UNSIGNED NOT NULL,
  `realname` varchar(16) NOT NULL,
  `uid` int(11) UNSIGNED NOT NULL,
  `nickname` int(16) NOT NULL,
  `is_online` enum('0','1') NOT NULL DEFAULT '0',
  `price` decimal(9,2) NOT NULL,
  `pid` varchar(16) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `stcms_activity`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_activity_picture`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_admin`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_adminlog`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_apply`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_card`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_cardbase`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cardno` (`cardno`),
  ADD UNIQUE KEY `hash` (`hash`),
  ADD KEY `uid` (`uid`,`wid`);

ALTER TABLE `stcms_category`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`aid`);

ALTER TABLE `stcms_config`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_message`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_news`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_newscat`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_setting_info`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_slider`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_tag`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_tag_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tid` (`tid`);

ALTER TABLE `stcms_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `openid` (`openid`);

ALTER TABLE `stcms_users_auth`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`,`aid`);

ALTER TABLE `stcms_users_auth_person`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`),
  ADD KEY `aid` (`aid`);

ALTER TABLE `stcms_users_card`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `stcms_users_coinlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `stcms_users_fav`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `stcms_users_paylog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `stcms_users_person`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `stcms_users_relation`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_worker`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `stcms_worklog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wid` (`wid`);


ALTER TABLE `stcms_activity`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_activity_picture`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_adminlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_apply`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_cardbase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_comment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_config`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_newscat`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_setting_info`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_slider`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_tag`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_tag_list`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_users_auth`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_users_auth_person`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_users_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_users_coinlog`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_users_fav`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_users_paylog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_users_person`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_users_relation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_worker`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `stcms_worklog`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;