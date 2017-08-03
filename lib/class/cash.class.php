<?php
class cash_class {
	var $db = NULL;
	var $tpl_id = "_8BAE_0D78L7orzydR9ue0OJnuGFnFSjOJyG7nSKZkE";
	
	function __construct() {
		$this->db = &$GLOBALS['mysql_class'];
	}
	
	function handle($uid, $money, $tradeno, $flag = true) {
		global $config;
		$thisuser = $this->db->select_one("users", "*", array("id"=>$uid));
		if($relation = $this->db->select_one("users_relation", "*", array("uid"=>$thisuser['id']))) {
			$fuser = $this->db->select_one("users", "*", array("id"=>$relation['pid']));
			if($config['coin_switch'] == 0 && $config['cash_coin'] > 0) {
				$exchange = round($money * $config['cash_coin']);
				$this->db->update("users", "return_coin = return_coin + {$exchange}" , array("id"=>$fuser['id']));
				$this->db->insert("users_coinlog", array(
					"uid"=>$fuser['id'],
					"type"=>0,
					"coin"=>$exchange,
					"tradeno"=>$tradeno,
					"msg"=>addslashes("用户 {$thisuser['nickname']} 现金消费 ¥".$money."元"),
					"time"=>NOW,
				));
				
				// 通知fuser
				if($flag) {
					$notemsg = array(
						"first"=>array("value"=>urlencode("用户 {$thisuser['nickname']} 现金消费 ¥".$money."元，系统已给您返还积分："), "color"=>"#173177"),
						"keyword1"=>array("value"=>urlencode($fuser['nickname']), "color"=>"#173177"),
						"keyword2"=>array("value"=>urlencode("+".$exchange), "color"=>"#173177"),
						"keyword3"=>array("value"=>urlencode($fuser['return_coin'] + $exchange), "color"=>"#173177"),
						"remark"=>array("value"=>urlencode("返还的积分可用于提问消费，如有疑问请与客服取得联系。"), "color"=>"#173177"),
					);
					$noteurl = check_dir($config['web_url'])."u/?ac=coin";
					$notequery = urldecode(json_encode(array("touser"=>$fuser['openid'], "template_id"=>$this->tpl_id, "url"=>$noteurl, "data"=>$notemsg)));
					http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $notequery);
				}
			}
		}
	}
}
?>