<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");
define("BUY_SUCCESS_TPLID", "qtcMzYkmlrC5gPXax7dNoGgVdqoSYLQOle2GCi7W5UE");
define("IN_ACTIVITY_TPLID", "45-0332gyZqGb0tdZ_hbXuZ3OhS0A7xU_BJG_6HU_Po");
$thisuser = get_user();
$wxjsapi = get_wxsign();
$type = intval($_GET['type']);
if($type == 0) {
	$thisprice = $mysql_class->select_one("card", "*", array("id"=>intval($_GET['id']), "is_del"=>0));
	if(empty($thisprice)) {
		header("Location: ".check_dir($config['web_url'])."c/");
		exit;
	}
	// 验证是否可购买亲子卡
	if($thisprice['is_trial'] == 1 && $mysql_class->num_table("users_card", array("uid"=>$thisuser['id'], "cid"=>$thisprice['id'])) > 0) {
		header("Location: ".check_dir($config['web_url'])."c/");
		exit;
	}
	// 验证是否需要支付
	if($thisprice['price'] == 0) {
		$mysql_class->insert("users_card", array("uid"=>$thisuser['id'], "cid"=>$thisprice['id'], "name"=>addslashes($thisprice['name']), "price"=>$thisprice['price'], "url"=>$thisprice['url'], "num"=>$thisprice['num'], "time"=>NOW));
		$tradeno = date("ymdhis").rand(100, 999);
		$mysql_class->insert("users_paylog", array("tradeno"=>$tradeno, "type"=>$type, "uid"=>$thisuser['id'], "uname"=>add_slashes($thisuser['nickname']), "price_id"=>$thisprice['id'], "ctype"=>"0", "money"=>"0.00", "is_payed"=>"1", "time"=>NOW));
		$notemsg = array(
			"first"=>array("value"=>urlencode("您已成功购买一张亲子卡："), "color"=>"#173177"),
			"keyword1"=>array("value"=>urlencode("无须支付"), "color"=>"#173177"),
			"keyword2"=>array("value"=>urlencode(sprintf("%s（%s元）", $thisprice['name'], $thisprice['price'])), "color"=>"#173177"),
			"keyword3"=>array("value"=>urlencode(), "color"=>"#173177"),
			"keyword4"=>array("value"=>urlencode(sprintf("可参加%d次活动", $thisprice['num'])), "color"=>"#173177"),
			"remark"=>array("value"=>urlencode("交易已完成，如有疑问请与客服取得联系。"), "color"=>"#173177"),
		);
		$noteurl = check_dir($config['web_url'])."u/?ac=card";
		$notequery = urldecode(json_encode(array("touser"=>$thisuser['openid'], "template_id"=>BUY_SUCCESS_TPLID, "url"=>$noteurl, "data"=>$notemsg)));
		http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $notequery);
		header("Location: ".check_dir($config['web_url'])."u/?ac=card");
		exit;
	}
} else {
	$thisprice = $mysql_class->select_one("activity", "*", array("id"=>intval($_GET['id'])));
	if(empty($thisprice)) {
		header("Location: ".check_dir($config['web_url']));
		exit;
	}
	// 验证是否符合报名状态
	if($thisprice['status'] == 1 || $thisprice['person_num'] - $thisprice['in_num'] < 1 || $thisprice['date'] <= date("Y-m-d")) {
		header("Location: ".check_dir($config['web_url']));
		exit;
	}
	$thisauth = $mysql_class->select_one("users_auth", "*", array("id"=>intval($_GET['aid']), "uid"=>$thisuser['id'], "aid"=>$thisprice['id']));
	if(empty($thisauth)) {
		header("Location: ".check_dir($config['web_url']));
		exit;
	}
	// 验证是否需要支付
	if($thisauth['is_finish']) {
		// 2017-05-07加模板消息及短信通知
		if($thisprice['price'] == 0) {
			//2017-06-07修改两次通知bug
			$notemsg = array(
				"first"=>array("value"=>urlencode("您已成功报名如下活动："), "color"=>"#173177"),
				"keyword1"=>array("value"=>urlencode($thisprice['title']), "color"=>"#173177"),
				"keyword2"=>array("value"=>urlencode($thisprice['date']."（".$thisprice['timestr']."）"), "color"=>"#173177"),
				"keyword3"=>array("value"=>urlencode($thisprice['address']), "color"=>"#173177"),
				"remark"=>array("value"=>urlencode("请调整好时间、状态，准时参加。"), "color"=>"#173177"),
			);
			$noteurl = check_dir($config['web_url'])."u/?ac=activity";
			$notequery = urldecode(json_encode(array("touser"=>$thisuser['openid'], "template_id"=>IN_ACTIVITY_TPLID, "url"=>$noteurl, "data"=>$notemsg)));
			http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $notequery);
			$sms_class = load_class("sms");
			$content = "温馨提示：您已提交“".$thisprice['title']."”报名预约，请加客服微信号：18229850661，及时了解活动成行动态。";
			$sms_class->sendSMS($thisuser['phone'], $content, "170519");
		}
		header("Location: ".check_dir($config['web_url'])."u/?ac=activity");
		exit;
	}
}
if($_GET['ac'] == "checkcash") {
	if($type > 0) {
		if($thisauth['is_finish']) {
			exit(json_encode(array("error"=>1, "msg"=>"该订单已支付。")));
		}
		$params = get_wxpay(array("id"=>$thisprice['id'], "title"=>$thisprice['title'], "price"=>$thisprice['price']), $type, $thisauth['id']);
	} else {
		$params = get_wxpay(array("id"=>$thisprice['id'], "title"=>$thisprice['name'], "price"=>$thisprice['price']), $type);
	}
	if($params) {
		exit(json_encode(array("error"=>0, "data"=>$params)));
	} else {
		exit(json_encode(array("error"=>1, "msg"=>"未知原因导致系统无法获取支付参数。")));
	}
} else if($_GET['ac'] == "checkcoin") {
	$costcoin = $thisprice['price'] * 100;
	if($thisuser['return_coin'] >= $costcoin) {
		$mysql_class->update("users", "return_coin=return_coin-{$costcoin}", array("id"=>$thisuser['id']));
		$tradeno = date("ymdhis").rand(100, 999);
		if($type < 1) {
			$mysql_class->insert("users_card", array("uid"=>$thisuser['id'], "cid"=>$thisprice['id'], "name"=>addslashes($thisprice['name']), "price"=>$thisprice['price'], "url"=>$thisprice['url'], "num"=>$thisprice['num'], "time"=>NOW));
		} else {
			$mysql_class->update("users_auth", array("is_finish"=>1, "tradeno"=>$tradeno, "time"=>NOW), array("id"=>$thisauth['id']));
			$mysql_class->update("activity", "in_num=in_num+1", array("id"=>$thisprice['id']));
		}
		$mysql_class->insert("users_paylog", array("tradeno"=>$tradeno, "type"=>$type, "uid"=>$thisuser['id'], "uname"=>add_slashes($thisuser['nickname']), "price_id"=>$thisprice['id'], "ctype"=>"1", "coin"=>$costcoin, "is_payed"=>"1", "time"=>NOW));
		$mysql_class->insert("users_coinlog", array(
			"uid"=>$thisuser['id'],
			"type"=>"1",
			"coin"=>$costcoin,
			"tradeno"=>$tradeno,
			"msg"=>($type > 0 ? "单次购买活动《".cn_substr($thisprice['title'], 10)."》" : "购买亲子卡《".$thisprice['name']."》"),
			"time"=>NOW
		));
		// 发送模板消息（购买成功通知）
		if($type < 1) {
			$notemsg = array(
				"first"=>array("value"=>urlencode("您已成功购买一张亲子卡："), "color"=>"#173177"),
				"keyword1"=>array("value"=>urlencode("积分支付（-{$costcoin}）"), "color"=>"#173177"),
				"keyword2"=>array("value"=>urlencode(sprintf("%s（%s元）", $thisprice['name'], $thisprice['price'])), "color"=>"#173177"),
				"keyword3"=>array("value"=>urlencode($tradeno), "color"=>"#173177"),
				"keyword4"=>array("value"=>urlencode(sprintf("可参加%d次活动", $thisprice['num'])), "color"=>"#173177"),
				"remark"=>array("value"=>urlencode("交易已完成，如有疑问请与客服取得联系。"), "color"=>"#173177"),
			);
			$noteurl = check_dir($config['web_url'])."u/?ac=card";
			$notequery = urldecode(json_encode(array("touser"=>$thisuser['openid'], "template_id"=>BUY_SUCCESS_TPLID, "url"=>$noteurl, "data"=>$notemsg)));
			http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $notequery);
		} else {
			// 2017-05-07加模板消息及短信通知
			$notemsg = array(
				"first"=>array("value"=>urlencode("您已成功报名如下活动："), "color"=>"#173177"),
				"keyword1"=>array("value"=>urlencode($thisprice['title']), "color"=>"#173177"),
				"keyword2"=>array("value"=>urlencode($thisprice['date']."（".$thisprice['timestr']."）"), "color"=>"#173177"),
				"keyword3"=>array("value"=>urlencode($thisprice['address']), "color"=>"#173177"),
				"remark"=>array("value"=>urlencode("请调整好时间、状态，准时参加。"), "color"=>"#173177"),
			);
			$noteurl = check_dir($config['web_url'])."u/?ac=activity";
			$notequery = urldecode(json_encode(array("touser"=>$thisuser['openid'], "template_id"=>IN_ACTIVITY_TPLID, "url"=>$noteurl, "data"=>$notemsg)));
			http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $notequery);
			$sms_class = load_class("sms");
			$content = "温馨提示：您已提交“".$thisprice['title']."”报名预约，请加客服微信号：18229850661，及时了解活动成行动态。";
			$sms_class->sendSMS($thisuser['phone'], $content, "170519");
		}
		
		exit(json_encode(array("error"=>0)));
	} else {
		exit(json_encode(array("error"=>1, "msg"=>"您的积分不足，请更换支付方式。")));
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<title>在线支付 - <?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link type="text/css" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" rel="stylesheet">
</head>
<body>
<div class="weui_msg">
    <div class="weui_text_area">
        <h2 class="weui_msg_title">支付金额：<span style="color:red"><?php echo $thisprice['price'];?>元</span></h2>
    </div>
</div>
<div class="weui_panel weui_panel_access">
    <div class="weui_panel_hd">购买对象</div>
<?php
if($type == 0) {
?>
    <div class="weui_panel_bd">
        <div class="weui_media_box weui_media_appmsg">
            <div class="weui_media_hd">
                <img class="weui_media_appmsg_thumb" src="<?php echo $thisprice['url'];?>">
            </div>
            <div class="weui_media_bd">
                <h4 class="weui_media_title"><?php echo $thisprice['name'];?></h4>
                <p class="weui_media_desc"><?php echo $thisprice['name'];?>：共<?php echo $thisprice['price'];?>元，<?php echo $thisprice['num'];?>次活动</p>
            </div>
        </div>
    </div>
<?php
} else {
?>
    <div class="weui_panel_bd">
        <div class="weui_media_box weui_media_appmsg">
            <div class="weui_media_hd">
                <img class="weui_media_appmsg_thumb" style="width:60px;" src="<?php echo $mysql_class->get_field_value("activity_picture", "url", array("aid"=>$thisprice['id']), "index_order ASC");?>">
            </div>
            <div class="weui_media_bd">
                <h4 class="weui_media_title"><?php echo $thisprice['title'];?></h4>
                <p class="weui_media_desc"><?php echo $thisprice['date'];?>（<?php echo $thisprice['timestr'];?>）在 <?php echo $thisprice['address'];?> 的活动</p>
            </div>
        </div>
    </div>
<?php
}
?>
</div>
<div class="weui_cells_title">支付选项</div>
<div class="weui_cells weui_cells_radio">
    <label class="weui_cell weui_check_label" for="x11">
        <div class="weui_cell_bd weui_cell_primary">
            <p>现金支付 <span class="red">¥<?php echo $thisprice['price'];?></span></p>
        </div>
        <div class="weui_cell_ft">
            <input type="radio" class="weui_check" name="way" value="0" id="x11" checked="checked">
            <span class="weui_icon_checked"></span>
        </div>
    </label>
    <label class="weui_cell weui_check_label" for="x12">
        <div class="weui_cell_bd weui_cell_primary">
            <p>积分支付 <span class="red"><?php echo $thisprice['price'] * 100;?>个</span></p>
        </div>
        <div class="weui_cell_ft">
            <input type="radio" name="way" value="1" class="weui_check" id="x12">
            <span class="weui_icon_checked"></span>
        </div>
    </label>
</div>
<div class="weui_btn_area"><a class="weui_btn weui_btn_primary" href="javascript:" onClick="checkout();">结算</a></div>
<div class="showmsg" style="display: none;">
    <div class="weui_mask_transparent"></div>
    <div class="weui_toast">
        <i class="weui_icon_toast"></i>
        <p class="weui_toast_content">支付完成</p>
    </div>
</div>
<div class="showloading weui_loading_toast" style="display:none;">
    <div class="weui_mask_transparent"></div>
    <div class="weui_toast">
        <div class="weui_loading">
            <div class="weui_loading_leaf weui_loading_leaf_0"></div>
            <div class="weui_loading_leaf weui_loading_leaf_1"></div>
            <div class="weui_loading_leaf weui_loading_leaf_2"></div>
            <div class="weui_loading_leaf weui_loading_leaf_3"></div>
            <div class="weui_loading_leaf weui_loading_leaf_4"></div>
            <div class="weui_loading_leaf weui_loading_leaf_5"></div>
            <div class="weui_loading_leaf weui_loading_leaf_6"></div>
            <div class="weui_loading_leaf weui_loading_leaf_7"></div>
            <div class="weui_loading_leaf weui_loading_leaf_8"></div>
            <div class="weui_loading_leaf weui_loading_leaf_9"></div>
            <div class="weui_loading_leaf weui_loading_leaf_10"></div>
            <div class="weui_loading_leaf weui_loading_leaf_11"></div>
        </div>
        <p class="weui_toast_content">数据加载中</p>
    </div>
</div>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/jquery.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/common.js"></script>
<script type="text/javascript">
wx.config({
    debug: false,
    appId: "<?php echo WXAPPID;?>",
    timestamp: "<?php echo $wxjsapi['timestamp'];?>",
    nonceStr: "<?php echo $wxjsapi['noncestr'];?>",
    signature: "<?php echo $wxjsapi['signature'];?>",
    jsApiList: ["startRecord", "stopRecord", "onVoiceRecordEnd", "playVoice", "pauseVoice", "stopVoice",
		"onVoicePlayEnd", "uploadVoice", "downloadVoice", "chooseImage", "previewImage", "uploadImage",
		"downloadImage", "getNetworkType", "chooseWXPay"]
});
$(document).ready(function(e) {
    $(".weui_media_appmsg_thumb").css({
		"vertical-align":"middle",
	});
});
function checkout() {
	$(".showloading").show();
	if(get_radio_value("way") == "0") {
		$.ajax({
			url:"<?php echo check_dir($config['web_url']);?>pay/?ac=checkcash&type=<?php echo $type;?>&id=<?php echo $thisprice['id'];?>&aid=<?php echo $thisauth['id'];?>",
			type:"GET",
			dataType:"json",
			success: function(data) {
				if(data.error > 0) {
					$(".showloading").hide();
					alert(data.msg);
				} else {
					var payauth = data['data'];
					WeixinJSBridge.invoke('getBrandWCPayRequest',{
							"appId":payauth['appId'],
							"timeStamp":payauth['timeStamp'],
							"nonceStr": payauth['nonceStr'],
							"package": payauth['package'],
							"signType":payauth['signType'],
							"paySign": payauth['paySign']
						},
						function(res) {
							if(res.err_msg == "get_brand_wcpay_request:ok"){
								$(".showmsg").show();
								setTimeout(function(){
									if("<?php echo $type;?>" == "0") {
										location.href="<?php echo check_dir($config['web_url']);?>u/?ac=card";
									} else {
										location.href="<?php echo check_dir($config['web_url']);?>u/?ac=activity";
									}
								}, 1500);
							}
						}
					);
				}
			}
		});
	} else if(get_radio_value("way") == "1") {
		$.ajax({
			url:"<?php echo check_dir($config['web_url']);?>pay/?ac=checkcoin&type=<?php echo $type;?>&id=<?php echo $thisprice['id'];?>&aid=<?php echo $thisauth['id'];?>",
			type:"GET",
			dataType:"json",
			success: function(data) {
				if(data.error > 0) {
					$(".showloading").hide();
					alert(data.msg);
				} else {
					$(".showmsg").show();
					setTimeout(function(){
						if("<?php echo $type;?>" == "0") {
							location.href="<?php echo check_dir($config['web_url']);?>u/?ac=card";
						} else {
							location.href="<?php echo check_dir($config['web_url']);?>u/?ac=activity";
						}
					}, 1500);
				}
			}
		});
		
	}
}
</script>
</body>
</html>