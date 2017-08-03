<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");

$code = trim($_GET['code']);
if(empty($code)) {
	exit("参数错误：无法获取code。");
}
$httpurl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".WXAPPID."&secret=".WXAPPSECRET."&code=".$code."&grant_type=authorization_code";
$response = http_request($httpurl);
if(empty($response)) {
	exit("请求access_token时服务器未响应。");
}
$jsondadta = @json_decode($response);
if(empty($jsondadta)) {
	exit("请求access_token时服务器返回不合法数据。");
}
if(empty($jsondadta->access_token) || empty($jsondadta->openid)) {
	exit("服务器未返回合法的access_token与openid。".print_r($jsondadta, true));
}
$userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$jsondadta->access_token."&openid=".$jsondadta->openid."&lang=zh_CN";
$user_response = http_request($userinfo_url);
if(empty($user_response)) {
	exit("请求用户信息时服务器未响应。");
}
$user_jsondata = @json_decode($user_response);
if(empty($user_jsondata)) {
	exit("请求用户信息是服务器返回不合法的数据。");
}
if($thisuser = $mysql_class->select_one("users", "*", array("openid"=>$user_jsondata->openid))) {
	// 20161029 验证用户是否关注
	$check_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".get_access_token()."&openid=".$thisuser['openid']."&lang=zh_CN";
	$check_response = http_request($check_url);
	if(empty($check_response)) {
		exit("请求用户信息时服务器未响应。");
	}
	$check_jsondata = @json_decode($check_response);
	if(empty($check_jsondata)) {
		exit("请求用户信息是服务器返回不合法的数据。");
	}
	if($check_jsondata->subscribe > 0) {	
		$mysql_class->update("users", "headimgurl='".$check_jsondata->headimgurl."', is_reg = '1', last_time=log_time, last_ip=ip, ip='".get_ip()."', log_time='".NOW."'", array("id"=>$thisuser['id']));
	} else {
		$mysql_class->update("users", "is_reg = '0', last_time=log_time, last_ip=ip, ip='".get_ip()."', log_time='".NOW."'", array("id"=>$thisuser['id']));
	}
} else {
	$thisuser = array(
		"openid"=>$user_jsondata->openid,
		"unionid"=>$user_jsondata->unionid,
		"nickname"=>$user_jsondata->nickname,
		"sex"=>$user_jsondata->sex,
		"city"=>$user_jsondata->city,
		"province"=>$user_jsondata->province,
		"country"=>$user_jsondata->country,
		"headimgurl"=>$user_jsondata->headimgurl,
		"reg_time"=>NOW,
		"ip"=>get_ip(),
		"log_time"=>NOW
	);
	$mysql_class->insert("users", $thisuser);
	$thisuser['id'] = $mysql_class->insert_id();
}
_set_cookie("wc_uid", $thisuser['id'], time() + 30*24*3600);
if($_GET['returl']) {
	exit('<script type="text/javascript">location.href="'.base64_decode($_GET['returl']).'";</script>');
} else {
	exit('<script type="text/javascript">location.href="'.$config['web_url'].'";</script>');
}
?>