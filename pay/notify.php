<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");
require_once(STCMS_ROOT."wxpay/config.php");
require_once(STCMS_ROOT."wxpay/ClientResponseHandler.class.php");
define("BUY_SUCCESS_TPLID", "qtcMzYkmlrC5gPXax7dNoGgVdqoSYLQOle2GCi7W5UE");
define("IN_ACTIVITY_TPLID", "45-0332gyZqGb0tdZ_hbXuZ3OhS0A7xU_BJG_6HU_Po");
$paycnf = new Config();
$payresponse = new ClientResponseHandler();
$postxml = file_get_contents('php://input');
if(empty($postxml)) {
	exit("fail");
}
$payresponse->setKey($paycnf->C('key'));
$payresponse->setContent($postxml);
if($payresponse->isTenpaySign()){
	if($payresponse->getParameter('status') == 0 && $payresponse->getParameter('result_code') == 0){
		$tradeno = $payresponse->getParameter('out_trade_no');
		$paylog = $mysql_class->select_one("users_paylog", "*", array("tradeno"=>$tradeno));
		if(empty($paylog)) {
			file_put_contents("log.txt", $postxml."'\r\n未找到订单");
			exit('fail');
		}
		if($paylog['is_payed'] == "1") {
			exit('fail');
		}
		// 现金消费奖励
		if($config['coin_switch'] == 0) {
			if($config['cash_coin'] > 0) {
				$cash_class = load_class('cash');
				$cash_class->handle($paylog['uid'], $paylog['money'], $paylog['tradeno']);
			}
		}
		switch($paylog['type']) {
			case 0:
				$thiscard = $mysql_class->select_one("card", "*", array("id"=>$paylog['price_id']));
				if(empty($thiscard)) {
					file_put_contents("log.txt", $postxml."'\r\n未找到亲子卡");
					exit("fail");
				}
				$mysql_class->insert("users_card", array("uid"=>$paylog['uid'], "cid"=>$thiscard['id'], "name"=>addslashes($thiscard['name']), "price"=>$thiscard['price'], "url"=>$thiscard['url'], "num"=>$thiscard['num'], "time"=>NOW));
				
				// 发送模板消息（购买成功通知）
				$notemsg = array(
					"first"=>array("value"=>urlencode("您已成功购买一张亲子卡："), "color"=>"#173177"),
					"keyword1"=>array("value"=>urlencode("现金支付（-{$paylog['money']}）"), "color"=>"#173177"),
					"keyword2"=>array("value"=>urlencode(sprintf("%s（%s元）", $thiscard['name'], $thiscard['price'])), "color"=>"#173177"),
					"keyword3"=>array("value"=>urlencode($paylog['tradeno']), "color"=>"#173177"),
					"keyword4"=>array("value"=>urlencode(sprintf("可参加%d次活动", $thiscard['num'])), "color"=>"#173177"),
					"remark"=>array("value"=>urlencode("交易已完成，如有疑问请与客服取得联系。"), "color"=>"#173177"),
				);
				$noteurl = check_dir($config['web_url'])."u/?ac=card";
				$notequery = urldecode(json_encode(array("touser"=>$mysql_class->get_field_value("users", "openid", array("id"=>$paylog['uid'])), "template_id"=>BUY_SUCCESS_TPLID, "url"=>$noteurl, "data"=>$notemsg)));
				http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $notequery);
			break;
			case 1:
				$thisactivity = $mysql_class->select_one("activity", "*", array("id"=>$paylog['price_id']));
				$thisuser = $mysql_class->select_one("users", "*", array("id"=>$paylog['uid']));
				if(empty($thisactivity) || empty($thisuser)) {
					file_put_contents("log.txt", $postxml."'\r\n未找到活动");
					exit("fail");
				}
				$thisauth = $mysql_class->select_one("users_auth", "*", array("id"=>intval($payresponse->getParameter('attach')), "uid"=>$paylog['uid'], "aid"=>$thisactivity['id'], "is_finish"=>0));
				$mysql_class->update("users_auth", array("is_finish"=>1, "tradeno"=>$paylog['tradeno'], "time"=>NOW), array("id"=>$thisauth['id']));
				$mysql_class->update("activity", "in_num=in_num+1", array("id"=>$thisactivity['id']));
				// 2017-05-07加模板消息及短信通知
				$notemsg = array(
					"first"=>array("value"=>urlencode("您已成功报名如下活动："), "color"=>"#173177"),
					"keyword1"=>array("value"=>urlencode($thisactivity['title']), "color"=>"#173177"),
					"keyword2"=>array("value"=>urlencode($thisactivity['date']."（".$thisactivity['timestr']."）"), "color"=>"#173177"),
					"keyword3"=>array("value"=>urlencode($thisactivity['address']), "color"=>"#173177"),
					"remark"=>array("value"=>urlencode("请调整好时间、状态，准时参加。"), "color"=>"#173177"),
				);
				$noteurl = check_dir($config['web_url'])."u/?ac=activity";
				$notequery = urldecode(json_encode(array("touser"=>$thisuser['openid'], "template_id"=>IN_ACTIVITY_TPLID, "url"=>$noteurl, "data"=>$notemsg)));
				http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $notequery);
				$sms_class = load_class("sms");
				$content = "温馨提示：您已提交“".$thisactivity['title']."”报名预约，请加客服微信号：18229850661，及时了解活动成行动态。";
				$sms_class->sendSMS($thisuser['phone'], $content, "170519");
			break;
		}
		
		$mysql_class->update("users_paylog", array("is_payed"=>"1"), array("id"=>$paylog['id']));
		$mysql_class->delete("users_paylog", "is_payed = '0' AND time < '".date("Y-m-d H:i：:s", strtotime("-10 minutes", time()))."'");
		exit('success');
	} else{
		file_put_contents("log.txt", $postxml."'\r\n支付未成功");
		exit("fail");
	}
} else{
	file_put_contents("log.txt", $postxml."'\r\n签名错误");
	exit("fail");
}
?>