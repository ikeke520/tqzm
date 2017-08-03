<?php
//	Copyright (C) Http://www.phpstcms.com/
//	Author: me@yangdahong.cn
//	All rights reserved

header('Content-type: text/html; charset=utf-8');
@error_reporting(E_ALL ^E_NOTICE ^E_WARNING);
@set_magic_quotes_runtime(0);
if(function_exists("date_default_timezone_set")) {
	@date_default_timezone_set("PRC");
}
define('STCMS_ROOT', str_replace('\\', '/', dirname(__FILE__)).'/');
define('NOW', date('Y-m-d H:i:s'));
// init session
session_start();
define('SID', md5(session_id()));
require(STCMS_ROOT.'function.php');
// init gpc
$item = array('_GET', '_POST', '_COOKIE', '_SESSION', '_SERVER', '_ENV');
if (!get_magic_quotes_gpc()) {
	for($n = 0; $n < count($item); $n++) {
		$GLOBALS[$item[$n]] = sql_filter(add_slashes($GLOBALS[$item[$n]]));
	}
}
$config = include(STCMS_ROOT.'config.php');
$mysql_class = load_class('mysql');
$mysql_class->init($config['db_host'], $config['db_port'], $config['db_user'], $config['db_pwd'], $config['db_name'], $config['db_prefix'], $config['db_charset']);
$setting = $mysql_class->select_one("config", "*");
$config = array_merge($config, $setting);
?>