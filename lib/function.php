<?php
//	Copyright (C) Http://www.phpstcms.com/
//	Author: me@yangdahong.cn
//	All rights reserved

function move_file($path, $tpath) { 
	if(is_file($path)) {
		if(file_exists($tpath)) {
			remove_file($tpath);
		}
		mkdirs(dirname($tpath), 0777);
		rename($path, $tpath);
	} else if(is_dir($path)) {
		mkdirs($tpath);
		$dfp = opendir($path);
		while($tmpfile = readdir($dfp)) {
			if(!in_array($tmpfile, array('.', '..'))) {
				move_file(check_dir($path).$tmpfile, check_dir($tpath).$tmpfile);
			}
		}
		closedir($dfp);
	}
}
function remove_file($path) {
	if (!file_exists($path))return true;
	if (filetype($path) == 'dir') {
		if(!preg_match("/^".preg_quote(check_dir(dirname(STCMS_ROOT)). "attachment/", "/i")."/", $path)) {
			return false;
		}
		$handler = opendir($path);
		while (false !== ($file = readdir($handler))) {
			if (!in_array($file, array('.', '..'))) {
				remove_file(check_dir($path).$file);
			}
		}
		closedir($handler);
		@rmdir($path);
	}
	else if(filetype($path) == 'file') {
		@unlink($path);
	}
	return true;
}
function is_write($path) {
	if (is_writeable($path) && is_dir($path)) {
		$tmp_file = check_dir($path).md5(time()).'.tmp';
		if ($fp = @fopen($tmp_file, 'a')) {
			@fclose($fp);
			unlink($tmp_file);
			return true;
		}
	}
	else if(is_writeable($path) && is_file($path)) {
		if ($fp = @fopen($path, 'a')) {
			@fclose($fp);
			return true;
		}
	}
	return false;
}
function mkdirs($path, $mode=0777) {
	$up_dir = dirname($path);
	if(!is_dir($up_dir)) {
		mkdirs($up_dir);
	}
	if(is_write($up_dir) && !file_exists($path)) {
		mkdir($path, $mode);
	}
}
function check_dir($path) {
	 $path = preg_replace('/([^\:]{1})\/\//', '\\1/', str_replace('\\', '/', $path));
	if(substr($path, -1) != '/') {
		return $path.'/';
	} else {
		return $path;
	}
}

function add_slashes($obj) {
	if(is_array($obj) || is_object($obj)) {
		foreach($obj as $key => $value) {
			$result[$key] = add_slashes($value);
		}
	} else {
		$result = addslashes($obj);
	}
	return $result;
}

function strip_slashes($var) {
	if(is_array($var) && $var) {
		foreach($var as $k=>$v) {
			$result[$k] = strip_slashes($v);
		}
	} else if(is_string($var) && $var) {
		$result = str_replace(array("\\\\", "\\'", "\\\"", "\\r", "\\n"), array('\\', "'", "\"", "\r", "\n"), $var);
	}
	return $result;
}

function sql_filter($arg) {
	$search_array = array("/\bunion\s+/i","/\bselect\s+/i","/\bupdate\s+/i","/\boutfile\s+/i","/\bor\s+/i","/\bdelete\s+/i","/\binsert\s+/i");
	$replace_array = array("union&nbsp;","select&nbsp;","update&nbsp;","outfile&nbsp;","or&nbsp;","delete&nbsp;","insert&nbsp;");
	if(is_array($arg)) {
		foreach($arg as $key => $value) {
			$result[$key] = sql_filter($value);
		}
	} else {
		$result = preg_replace($search_array, $replace_array, $arg);
	}
	return $result;
}

function strip_sql_filter($arg) {
	$search_array = array("/\bunion&nbsp;/i","/\bselect&nbsp;/i","/\bupdate&nbsp;/i","/\boutfile&nbsp;/i","/\bor&nbsp;/i","/\bdelete&nbsp;/i","/\binsert&nbsp;/i");
	$replace_array = array("union ","select ","update ","boutfile ","or ","delete ","insert ");
	if(is_array($arg) && $arg) {
		foreach($arg as $key => $value) {
			$result[$key] = strip_sql_filter($value);
		}
	} else {
		$result = preg_replace($search_array, $replace_array, $arg);
	}
	return $result;
}
	
function get_ip() {
	if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$IP = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$IP = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$IP = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$IP = $_SERVER['REMOTE_ADDR'];
	}
	if(preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $IP) || preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $IP)){
		return $IP;
	} else {
		return 'unknow';
	}
}

function filter_array($array, $condition) {
	if (!$array) {
		return false;
	}
	$keys = array_keys($condition);
	$key_num = count($keys);
	foreach($array as $array_tmp) {
		$eq_num = 0;
		foreach($keys as $key_tmp) {
			if ($array_tmp[$key_tmp] == $condition[$key_tmp]) {
				$eq_num++;
			}
		}
		if ($eq_num == $key_num) {
			$result[] = $array_tmp;
		}
	}
	return $result;
}

function get_id_array($array = array(), $field = 'id') {
	while ($tmp = @current($array)) {
		$id[] = $tmp[$field];
		next($array);
	}
	return $id;
}

function serialize_id($str, $exp=',', $imp="','") {
	return implode($imp, array_map('intval', explode($exp, $str)));
}

function get_full_host() {
	if(preg_match('/https/i', $_SERVER['SERVER_PROTOCOL'])) {
		$protocol = 'https';
	} else {
		$protocol = 'http';
	}
	return $protocol.'://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80' ? '' : ':'.$_SERVER['SERVER_PORT']);
}

function halt($msg, $url = false, $time = 5, $is_top = 0, $is_close = 0) {
	global $config;
	if ($url === false) {
		$url = $url ? $url :
		($_SERVER['HTTP_REFERER'] == 'http://www.jingqinlaw.com/' ? 'javascript:history.back();' : $_SERVER['HTTP_REFERER']);
		$url = $url ? $url : 'javascript:history.back()';
	}
	echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>提示信息__'.$config['seo_title'].'</title><meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/><script type="text/javascript">(function(){var t='.$time.';var u=\''.$url.'\';var c='.$is_close.';var p='.$is_top.';if(c){window.opener=null;window.close();return;}setTimeout(function(){if(u)if(p){top.location.href=u}else{window.location.href=u}}, t*1000);})();</script><style type="text/css">a{text-decoration:underline;color:#000;}</style></head><body><div style=" border:solid 1px #ccc; border-radius:5px; margin:30px auto; width:80%; max-width:600px; font-size:12px; color:#000; box-shadow:0 0 5px #333;"><div style="background:#eee; padding-left:20px; height:30px; line-height:30px; font-weight:bold; border-bottom:solid 1px #ddd;">信息提示_'.$config['seo_title'].'</div><div style=" padding:30px 10px; line-height:150%; text-align:center; font-size:20px;">'.$msg.'</div><div style="height:30px; line-height:20px; text-align:center;"><a style="text-decoration:underline; color:#000;" href="'.$url.'">如果未自动跳转，请点击这里...</a></div></div></body></html>';
	exit();
}

function get_file_ext($name) {
	return strtolower(trim(substr(strrchr($name, '.'), 1)));
}

function get_file_name($name) {
	return substr($name, 0, strrpos($name, '.'));
}

function param($val) {
	if(is_array($val)) {
		foreach($val as $k=>$v) {
			$param[] = $k.'='.rawurlencode($v);
		}
	} else if(is_string($val)) {
		$param[] = rawurlencode($val);
	}
	return implode('&', $param);
}

function load_class($name) {
	$path = STCMS_ROOT.'class/'.$name.'.class.php';
	if(file_exists($path)) {
		require_once($path);
		$class_name = $name.'_class';
		return new $class_name();
	}
	return false;
}

function _set_cookie($name, $value, $time=false) {
	global $config;
	$exp = time();
	if($time == false) $time = $exp + 900;
	$value = sprintf("%s:%s:%d", md5($value . $config['syscode'] . $exp), rawurlencode($value), $exp);
	setcookie($name, $value, $time, '/', '');
}

function _get_cookie($name) {
	global $config;
	list($hash, $value, $exp) = explode(":", $_COOKIE[$name]);
	$value = rawurldecode($value);
	if($hash == md5($value . $config['syscode'] . $exp)) {
		return $value;
	}
}

function cn_substr($str, $length, $start=0, $strip_tag=0, $ender='...') {
	preg_match_all("/./us", strip_tags($str), $arr);
	$strlen = count($arr[0]);
	$now = 0;
	$result = '';
	$bum = 0;
	for($n=$start; $n<$strlen; $n++) {
		$tmp = $arr[0][$n];
		if($now < $length) {
			if(!in_array($tmp, array("\r", "\n"))) {
				$result .= $tmp;
				if(preg_match('/^[，。？；：“‘’”——【】（）￥！·……、《》　\x{4e00}-\x{9fa5}]$/u', $tmp)) {
					$now = $now + 1;
				} else {
					$now = $now + 0.5;
				}
			}
		} else {
			if($n+1 < $strlen) {
				$result .= $ender;
			}
			return $result;
		}
	}
	return $result;
}
function get_size($size) {
	if ($size >= 1073741824) {
		$size = round($size / 1073741824 * 100) / 100 . ' GB';
	} elseif($size >= 1048576) {
		$size = round($size / 1048576 * 100) / 100 . ' MB';
	} elseif($size >= 1024) {
		$size = round($size / 1024 * 100) / 100 . ' KB';
	} else {
		$size = $size . ' bytes';
	}
	return $size;
}

function remove_attachment($url) {
	global $config;
	$upload_root = check_dir(check_dir(dirname(STCMS_ROOT)). "attachment/");
	$url_root = check_dir($config['web_url']) ."attachment/";
	return @unlink(preg_replace("/^".preg_quote($url_root, "/")."/", $upload_root, $url));
}

function move_attachment($url, $path) {
	global $config;
	$upload_root = check_dir(check_dir(dirname(STCMS_ROOT)). "attachment/");
	$url_root = check_dir($config['web_url']) ."attachment/";
	@mkdirs(dirname($upload_root.$path));
	$realpath = preg_replace("/^".preg_quote($url_root, "/")."/", $upload_root, $url);
	if(!file_exists($realpath) || !is_file($realpath)) {
		exit("{$realpath}不存在。");
	}
	if(rename($realpath, $upload_root.$path)) {
		return $url_root.$path;
	} else {
		exit("目的文件夹没有相应的权限（<i>{$realpath}</i>，<i>{$upload_root}{$path}</i>）。");
	}
}

function autolink($str) {
if($str=='' or !preg_match('/(http|www\.|@)/i', $str)) { return $str; }
    $lines = explode("\n", $str); $new_text = '';
    while (list($k,$l) = each($lines)) {
        // replace links:
        $l = preg_replace("/([ \t]|^)www\./i", "\\1http://www.", $l);
        $l = preg_replace("/([ \t]|^)ftp\./i", "\\1ftp://ftp.", $l);
        $l = preg_replace("/(http:\/\/[^ )\r\n!]+)/i",
            "<a href=\"\\1\">\\1</a>", $l);
        $l = preg_replace("/(https:\/\/[^ )\r\n!]+)/i",
            "<a href=\"\\1\">\\1</a>", $l);
        $l = preg_replace("/(ftp:\/\/[^ )\r\n!]+)/i",
            "<a href=\"\\1\">\\1</a>", $l);
        $l = preg_replace(
            "/([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))/i",
            "<a href=\"mailto:\\1\">\\1</a>", $l);
        $new_text .= $l."\n";
    }
    return $new_text;
}

function get_user($returl = '') {
	global $mysql_class, $config;
	$wc_uid = intval(_get_cookie("wc_uid"));
	$user = $mysql_class->select_one("users", "*", array("id"=>$wc_uid));
	if(empty($user)) {
		if(empty($returl)) {
			$returl = base64_encode(get_full_host().$_SERVER['REQUEST_URI']);
		}
		if(preg_match("/MicroMessenger/i", $_SERVER['HTTP_USER_AGENT'])) {
			header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=".WXAPPID."&redirect_uri=".urlencode(check_dir($config['web_url'])."login/?returl={$returl}")."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect");
		} else {
			exit("系统当前仅支持使用微信打开本页面。");
		}
		exit;
	}
	return $user;
}

function get_access_token() {
	$access_token_file = STCMS_ROOT."~token.php";
	if(file_exists($access_token_file)) {
		$access_data = include($access_token_file);
		if($access_data['time'] > time()) {
			return $access_data['token'];
		}
	}
	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".WXAPPID."&secret=".WXAPPSECRET;
	$response = http_request($url);
	if(empty($response)) {
		exit("获取access_token时服务器未响应。");
	}
	$jsondata = @json_decode($response);
	if(empty($jsondata->access_token) || empty($jsondata->expires_in)) {
		exit("获取access_token时返回的数据不合法。".print_r($jsondata, true));
	}
	$data = array("token"=>$jsondata->access_token, "time"=>time()+$jsondata->expires_in);
	file_put_contents($access_token_file, "<?php\r\nreturn ".var_export($data, true).";\r\n?>");
	return $data['token'];
}

function get_jsapi_ticket() {
	$jsapi_file = STCMS_ROOT."~ticket.php";
	if(file_exists($jsapi_file)) {
		$ticket_data = include($jsapi_file);
		if($ticket_data['time'] > time()) {
			return $ticket_data['ticket'];
		}
	}
	$access_token = get_access_token();
	$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
	$response = http_request($url);
	if(empty($response)) {
		exit("获取jsapi_ticket时服务器未响应。");
	}
	$jsondata = @json_decode($response);
	if(empty($jsondata->ticket) || empty($jsondata->expires_in)) {
		exit("获取jsapi_ticket时返回的数据不合法，请刷新重试。");
	}
	$data = array("ticket"=>$jsondata->ticket, "time"=>time() + $jsondata->expires_in);
	file_put_contents($jsapi_file, "<?php\r\nreturn ".var_export($data, true).";\r\n?>");
	return $data['ticket'];
}

function get_wxsign() {
	$data['jsapi_ticket'] = get_jsapi_ticket();
	$data['noncestr'] = md5(time());
	$data['timestamp'] = time();
	$data['url'] = get_full_host().$_SERVER['REQUEST_URI'];
	$data['signature'] = sha1(sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%d&url=%s", $data['jsapi_ticket'], $data['noncestr'], $data['timestamp'], $data['url']));
	return $data;
}

function get_wxpay($pricedata, $type = 0, $attach="") {
	global $mysql_class, $config;
	// 删除10分钟任未支付的超期订单
	$mysql_class->delete("users_paylog", "time < '".date("Y-m-d H:i:s", time() - 600). "' AND is_payed='0'");
	$thisuser = get_user();
	$data['uid'] = $thisuser['id'];
	$data['uname'] = addslashes($thisuser['nickname']);
	$data['tradeno'] = date("ymdhis").rand(100, 999);
	$data['time'] = NOW;
	switch($type) {
		case 0:
			$data['type'] = "0";
			$data['price_id'] = $pricedata['id'];
			$data['money'] = $pricedata['price'];
			$data['msg'] = "购买亲子卡【".addslashes($pricedata['title']) ."】，ID：".$pricedata['id'];
		break;
		case 1:
			$data['type'] = "1";
			$data['price_id'] = $pricedata['id'];
			$data['money'] = $pricedata['price'];
			$data['msg'] = "单独购买活动【".addslashes($pricedata['title']) ."】，ID：".$pricedata['id'];
		break;
	}

	$mysql_class->insert("users_paylog", $data);
	$data['id'] = $mysql_class->insert_id();
	$timestamp = strtotime(NOW);
	
	require_once(STCMS_ROOT."wxpay/config.php");
	require_once(STCMS_ROOT."wxpay/Utils.class.php");
	require_once(STCMS_ROOT."wxpay/RequestHandler.class.php");
	require_once(STCMS_ROOT."wxpay/ClientResponseHandler.class.php");
	require_once(STCMS_ROOT."wxpay/PayHttpClient.class.php");
	$response = new ClientResponseHandler();
	$request = new RequestHandler();
	$payclient = new PayHttpClient();
	$paycnf = new Config();
	$request->setGateUrl($paycnf->C('url'));
	$request->setKey($paycnf->C('key'));
	$request->setParameter('service', 'pay.weixin.jspay');
	$request->setParameter('mch_id', $paycnf->C('mchId'));
	$request->setParameter('version', $paycnf->C('version'));
	$request->setParameter('out_trade_no', $data['tradeno']);
	$request->setParameter('body', $data['msg']);
	$request->setParameter('attach', $attach);
	$request->setParameter('is_raw', 1);
	$request->setParameter('sub_appid', WXAPPID);
	$request->setParameter('sub_openid', $thisuser['openid']);
	$request->setParameter('total_fee', floatval($data['money']) * 100);
	$request->setParameter('mch_create_ip', "122.114.162.137");
	$request->setParameter('notify_url', check_dir($config['web_url'])."pay/notify.php");
	$request->setParameter('nonce_str', md5($timestamp));
	$request->setParameter('time_start', date("YmdHis", $timestamp));
	$request->setParameter('time_expire', date("YmdHis", $timestamp+600));
	$request->createSign();
	$paydata = Utils::toXml($request->getAllParameters());
	//print_r($paydata);
	
	$payclient->setReqContent($request->getGateURL(), $paydata);
	if($payclient->call()){
		$response->setContent($payclient->getResContent());
		$response->setKey($request->getKey());
		if($response->isTenpaySign()){
			if($response->getParameter('status') == 0 && $response->getParameter('result_code') == 0){
				return json_decode($response->getParameter("pay_info"));
			}
		}
	}
}

function pay_refund($tradeno) {
	global $mysql_class, $config;
	$paylog = $mysql_class->select_one("users_paylog", "*", array("tradeno"=>$tradeno));
	if(empty($paylog) || $paylog['is_refund'] > 0) {
		return false;
	}
	if($paylog['ctype'] == 0) {
		$refundno = date("ymdhis").rand(100, 999);
		
		require_once(STCMS_ROOT."wxpay/config.php");
		require_once(STCMS_ROOT."wxpay/Utils.class.php");
		require_once(STCMS_ROOT."wxpay/RequestHandler.class.php");
		require_once(STCMS_ROOT."wxpay/ClientResponseHandler.class.php");
		require_once(STCMS_ROOT."wxpay/PayHttpClient.class.php");
		$response = new ClientResponseHandler();
		$request = new RequestHandler();
		$payclient = new PayHttpClient();
		$paycnf = new Config();
		$request->setGateUrl($paycnf->C('url'));
		$request->setKey($paycnf->C('key'));
		$request->setParameter('service', 'unified.trade.refund');
		$request->setParameter('mch_id', $paycnf->C('mchId'));
		$request->setParameter('version', $paycnf->C('version'));
		$request->setParameter('out_trade_no', $tradeno);
		$request->setParameter('out_refund_no', $refundno);
		$request->setParameter('total_fee', $paylog['money']*100);
		$request->setParameter('refund_fee', $paylog['money']*100);
		$request->setParameter('op_user_id', $paycnf->C('mchId'));
		$request->setParameter('nonce_str', md5($timestamp));
		$request->createSign();
		$paydata = Utils::toXml($request->getAllParameters());
		//print_r($paydata);exit;
		
		$payclient->setReqContent($request->getGateURL(), $paydata);
		if($payclient->call()){
			$response->setContent($payclient->getResContent());
			$response->setKey($request->getKey());
			if($response->isTenpaySign()){
				if($response->getParameter('status') == 0 && $response->getParameter('result_code') == 0){
					$refund_id = $response->getParameter("refund_id");
				}
			}
		}
	} else {
		$mysql_class->update("users", "return_coin = return_coin + ".$paylog['coin'], array("id"=>$paylog['uid']));
		$mysql_class->insert("users_coinlog", array("uid"=>$paylog['uid'], "type"=>0, "coin"=>$paylog['coin'], "msg"=>"退单积分（".$paylog['tradeno']."）", "time"=>NOW));
	}
	$mysql_class->update("users_paylog", array("is_refund"=>1, "refund_id"=>$refund_id), array("id"=>$paylog['id']));
}

function get_sns_access_token() {
	$access_token_file = STCMS_ROOT."~dingsnstoken.php";
	if(file_exists($access_token_file)) {
		$access_data = include($access_token_file);
		if($access_data['time'] > time()) {
			return $access_data['token'];
		}
	}
	$url = "https://oapi.dingtalk.com/sns/gettoken?appid=".DAPPID."&appsecret=".DAPPSECRET;
	$response = http_request($url);
	if(empty($response)) {
		exit("获取access_token时服务器未响应。");
	}
	$jsondata = @json_decode($response);
	if($jsondata->errcode != 0) {
		exit("获取access_token失败，可能是APPID或APPSECRET配置有误。");
	}
	$data = array("token"=>$jsondata->access_token, "time"=>time()+7200);
	file_put_contents($access_token_file, "<?php\r\nreturn ".var_export($data, true).";\r\n?>");
	return $data['token'];
}

function get_duration($filename) {
	require_once(STCMS_ROOT."getid3/getid3/getid3.php");
	$getid3 = new getID3();
	$getid3->setOption(array("encoding", "UTF-8"));
	$return = $getid3->analyze($filename);
	return round($return['playtime_seconds']);
}

function imgresize($filename, $value, $type=0) {
	if(!file_exists($filename)) return false;
	if(in_array(get_file_ext($filename), array("jpg", "png", "gif", "jpeg"))) {
		$image_info = getimagesize($filename);
		switch($image_info[2]) {
			case 1:
				$image_src = imagecreatefromgif($filename);
			break;
			case 2:
				$image_src = imagecreatefromjpeg($filename);
			break;
			case 3:
				$image_src = imagecreatefrompng($filename);
			break;
		}
		if($type == 0) {
			$width = $value;
			$height = ($width / $image_info[0]) * $image_info[1];
		} else if($type == 1) {
			$height = $value;
			$width = ($height / $image_info[1]) * $image_info[0];
		} else {
			$width = $value;
			$height = $type;
		}
		$tmp_image_src = imagecreatetruecolor($width, $height);
		imagecopyresampled($tmp_image_src, $image_src, 0, 0, 0, 0, $width, $height, $image_info[0], $image_info[1]);
		switch($image_info[2]) {
			case 1:
				imagegif($tmp_image_src, $filename);
			break;
			case 2:
				imagejpeg($tmp_image_src, $filename, 100);
			break;
			case 3:
				imagepng($tmp_image_src, $filename);
			break;
		}
		imagedestroy($tmp_image_src);
	}
}

function id3_conv($data) {
	$type = ord(substr($data, 0, 1));
	switch($type) {
		case 0:
			return iconv('', 'UTF-8', substr($data, 1));
		break;
		case 1:
			return iconv('UTF-16', 'UTF-8', substr($data, 1));
		break;
		case 2:
		case 255:
			return iconv('UTF-16BE', 'UTF-8', substr($data, 1));
		break;
		case 3:
			return iconv('', 'UTF-8', substr($data, 1));
		break;
		default:
			return iconv('', 'UTF-8', substr($data, 1));
		break;
	}
}

function ffmpeg2mp3($filename) {
	if(!@function_exists("system")) {
		exit(json_encode(array("error"=>1, "msg"=>"系统没有权限使用system函数转换音频格式。")));
	} else {
		$bin = STCMS_ROOT."ffmpeg/ffmpeg.exe";
		$path = check_dir(dirname(STCMS_ROOT))."attachment/temp/wxrecord/".date("ymdhis").rand(100, 999).".mp3";
		mkdirs(dirname($path));
		$exec = sprintf("%s -i %s %s", $bin, $filename, $path);
		@system($exec);
		if(file_exists($path)) {
			@unlink($filename);
			return $path;
		} else {
			exit(json_encode(array("error"=>1, "msg"=>"音频转化失败。")));
		}
	}
}

function http_request($url, $data=NULL, $headers=NULL) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	if (!empty($data)){
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	if($headers) {
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}
function init_text($data) {
	return '<div style="font-family:'.$data['fontfamily'].'; font-size:'.$data['fontsize'].'; color:'.$data['fontcolor'].'; line-height:150%;">'.$data['content'].'</div>';
}
function init_pic($data) {
	return '<div style="text-align:'.$data['picalign'].'"><img class="imgpreivew" src="'.$data['content'].'"></div>';
}
function init_hit($num) {
	$num = intval($num);
	if($num < 100) {
		$num = rand(50, 99);
	}
	if($num >= 10000) {
		return round($num/10000,2)."万";
	} else if($num >= 1000) {
		return intval($num/1000).",".substr((string)$num, 1);
	} else {
		return $num;
	}
}
function adminlog($msg) {
	global $mysql_class, $admin;
	if($admin) {
		return $mysql_class->insert("adminlog", array("uid"=>$admin['id'], "uname"=>addslashes($admin['name']), "msg"=>addslashes($msg), "ip"=>get_ip(), "time"=>NOW));
	}
}
?>