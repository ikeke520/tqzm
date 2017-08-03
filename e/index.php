<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");
define("CREDIT_NOTE_TPLID", "_8BAE_0D78L7orzydR9ue0OJnuGFnFSjOJyG7nSKZkE");
$thisuser = get_user();
$wxjsapi = get_wxsign();
$fuid = intval($_GET['fuid']);
if($fuser = $mysql_class->select_one("users", "*", array("id"=>$fuid))) {
	if($fuser['id'] != $thisuser['id'] && strtotime($thisuser['reg_time']) > strtotime("-300 seconds")) {
		if($mysql_class->num_table("users_relation", array("uid"=>$thisuser['id'])) < 1) {
			$mysql_class->insert("users_relation", array(
				"pid"=>$fuser['id'],
				"pname"=>add_slashes($fuser['nickname']),
				"uid"=>$thisuser['id'],
				"uname"=>add_slashes($thisuser['nickname']),
				"time"=>NOW
			));
			// 给fuser加积分
			
			if($config['user_coin'] > 0 && $config['coin_switch'] == 0) {
				$mysql_class->update("users", "return_coin = return_coin + {$config['user_coin']}", array("id"=>$fuser['id']));
				$mysql_class->insert("users_coinlog", array("uid"=>$fuser['id'], "type"=>0, "coin"=>$config['user_coin'], "msg"=>addslashes("推荐了网友 ".$thisuser['nickname']), "time"=>NOW));
				$notemsg = array(
					"first"=>array("value"=>urlencode("网友通过扫描您的二维码加入，系统已给您返还积分："), "color"=>"#173177"),
					"keyword1"=>array("value"=>urlencode($fuser['nickname']), "color"=>"#173177"),
					"keyword2"=>array("value"=>urlencode("+".$config['user_coin']), "color"=>"#173177"),
					"keyword3"=>array("value"=>urlencode($fuser['return_coin'] + $config['user_coin']), "color"=>"#173177"),
					"remark"=>array("value"=>urlencode("返还的积分可用于提问消费，如有疑问请与客服取得联系。"), "color"=>"#173177"),
				);
				$noteurl = check_dir($config['web_url'])."u/?ac=coin";
				$notequery = urldecode(json_encode(array("touser"=>$fuser['openid'], "template_id"=>CREDIT_NOTE_TPLID, "url"=>$noteurl, "data"=>$notemsg)));
				http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $notequery);
			}
		}
	}
}
header("Location: ".check_dir($config['web_url']));
?>