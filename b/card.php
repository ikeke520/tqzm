<?php
$thisauth = $mysql_class->select_one("users_auth", "*", array("id"=>intval($_GET['aid']), "uid"=>$thisuser['id'], "aid"=>$thisactivity['id']));
if(empty($thisauth)) {
	exit("未找到制定的订单。");
}
if($thisactivity['payway'] > 0) {
	header("Location: ".check_dir($config['web_url'])."pay/?type=1&id=".$thisactivity['id']."&aid=".$thisauth['id']);
	exit;
}
if($thisauth['is_finish']) {
	// 2017-05-12加模板消息及短信通知
	if($thisactivity['price'] == 0) {
		//2017-06-07增加发两条短信bug
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
	}
	header("Location: ".check_dir($config['web_url'])."u/?ac=activity");
	exit;
}
$child_person = $mysql_class->fetch($mysql_class->query("SELECT * FROM {$config['db_prefix']}users_person AS p LEFT JOIN {$config['db_prefix']}users_auth_person AS a ON p.id=a.pid WHERE a.aid={$thisauth['id']} AND a.is_adult='0'"));
$adult_person = $mysql_class->fetch($mysql_class->query("SELECT * FROM {$config['db_prefix']}users_person AS p LEFT JOIN {$config['db_prefix']}users_auth_person AS a ON p.id=a.pid WHERE a.aid={$thisauth['id']} AND a.is_adult='1'"));
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title>选择要使用的童趣卡__<?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link rel="stylesheet" type="text/css" href="<?php echo check_dir($config['web_url']);?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
</head>
<body id="box">
	<div class="i_top">
		<div class="i_jindu">
			<img src="<?php echo check_dir($config['web_url']);?>images/i_tu5.png">
			<img src="<?php echo check_dir($config['web_url']);?>images/i_next.jpg">
			<img src="<?php echo check_dir($config['web_url']);?>images/i_tu4.png">
			<img src="<?php echo check_dir($config['web_url']);?>images/i_next.jpg">
			<img src="<?php echo check_dir($config['web_url']);?>images/i_tu3.png">
		</div>
		<ul>
			<li>选场次人员</li>
			<li>选卡</li>
			<li>完成</li>
		</ul>
	</div>

	<div class="a_kongge b_kongge"></div>

	<div class="q_main">
		<h3>请仔细确认报名信息</h3>
		<p>活动名称：<?php echo cn_substr($thisactivity['title'], 16);?></p>
		<p>出行时间：<?php echo $thisactivity['date'];?>（<?php echo $thisactivity['timestr'];?>）</p>
		<p>出行人员：小孩 <i><?php echo $mysql_class->num_table("users_auth_person", array("aid"=>$thisauth['id'], "is_adult"=>0));?></i> 人、大人 <i><?php echo $mysql_class->num_table("users_auth_person", array("aid"=>$thisauth['id'], "is_adult"=>1));?></i> 人</p>
	</div>

	<div class="a_kongge b_kongge"></div>

	<div class="q_main q_dibu q_cc">
		
		<p>请选择童趣卡（本次活动将消耗 <i>1</i> 次活动额）</p>
        <ul>
<?php
$mycard = $mysql_class->select("users_card", "*", array("uid"=>$thisuser['id'], "is_finish"=>0));
if($mycard) {
	foreach($mycard as $list) {
?>
        	<li><p><label><input type="radio" name="card" value="<?php echo $list['id'];?>">&nbsp;<b style="color:#de127a"><?php echo $list['name'];?></b>（总 <i><?php echo $list['num'];?></i> 次，剩 <i><?php echo $list['num'] - $list['cost_num'];?></i> 次）</label></p></li>
<?php
	}
}
?>
        </ul>
		
	</div>

	<div class="a_kongge b_kongge"></div>

	<div class="q_main q_main_top">
		
		<p>小孩信息：<?php echo $child_person['name'];?>（<?php echo $child_person['birthday'];?>）<?php if($child_person['idno']) {?><em>已购买保险</em><?php }?></p>
<?php
if($adult_person) {
?>
		<p>家长信息：<?php echo $adult_person['name'];?>（<?php echo $adult_person['phone'];?>）</p>
<?php
}
?>
	</div>

	<div class="a_kongge b_kongge"></div>

	<div class="q_main q_main_top">
		
		<p>联系人：<?php echo $thisuser['nickname'];?>（<?php echo $thisuser['phone'];?>）</p>
		<p>绑定手机：<?php echo $thisuser['phone'];?>    <span onClick="location.href='<?php echo check_dir($config['web_url']);?>b/?ac=phone&id=<?php echo $thisactivity['id'];?>&aid=<?php echo $thisauth['id'];?>&reset=1'">重新绑定</span></p>
		
	</div>
	
	<div class="a_kongge b_kongge q_kongge"></div>
	<div class="b_footer h_footer k_footer">
		<ul>
			<li><a href="<?php echo check_dir($config['web_url']);?>b/?id=<?php echo $thisactivity['id'];?>&aid=<?php echo $thisauth['id'];?>"><span>上一步</span></a></li>
			<li><a href="javascript:" onClick="apply()"><span>确认提交</span></a></li>
		</ul>
	</div>

	<div class="f_tanc_bg dialog">
		<div class="f_tanc">
			<p>您的报名信息已经提交！</p>
			<p>该活动需达到相应人数方可成行，</p>
			<p>欢迎呼朋唤友来参加哦！</p>
			<a href="javascript:" onClick="location.href='<?php echo check_dir($config['web_url']);?>u/?ac=activity'" class="f_hide">确定</a>
		</div>
	</div>
	<div class="mask"></div>
	<div id="to_top"></div>
    <div class="weui_loading_toast" style="display:none;">
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
    <div class="hidden"><img src="<?php echo check_dir($config['web_url']);?>images/share.jpg"></div>
    <div class="hidden weui_dialog_alert">
        <div class="weui_mask"></div>
        <div class="weui_dialog">
            <div class="weui_dialog_hd"><strong class="weui_dialog_title" style="font-size:14px;">您还未关注，将无法使用某些功能</strong></div>
            <div class="weui_dialog_bd">
                <img style="width:200px;" src="<?php echo check_dir($config['web_url']);?>images/qrcode.jpg">
            </div>
            <div class="weui_dialog_ft">
                <a href="javascript:" onClick="$('.weui_dialog_alert').hide();" class="weui_btn_dialog primary">长按二维码关注微信公众号</a>
            </div>
        </div>
    </div>
</body>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/phone.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/swiper.min.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/qiu_phone.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/common.js"></script>
<script type="text/javascript">
var _is_requesting = false;
wx.config({
    debug: false,
    appId: "<?php echo WXAPPID;?>",
    timestamp: "<?php echo $wxjsapi['timestamp'];?>",
    nonceStr: "<?php echo $wxjsapi['noncestr'];?>",
    signature: "<?php echo $wxjsapi['signature'];?>",
    jsApiList: ["startRecord", "stopRecord", "onVoiceRecordEnd", "playVoice", "pauseVoice", "stopVoice",
		"onVoicePlayEnd", "uploadVoice", "downloadVoice", "chooseImage", "previewImage", "uploadImage",
		"downloadImage", "getNetworkType", "chooseWXPay", "onMenuShareTimeline", "onMenuShareAppMessage"]
});
wx.ready(function() {
	wx.onMenuShareTimeline({
		title:document.title,
		link:location.href,
		imgUrl:"<?php echo check_dir($config['web_url']);?>images/share.jpg"
	});
	wx.onMenuShareAppMessage({
		title:document.title,
		link:location.href,
		imgUrl:"<?php echo check_dir($config['web_url']);?>images/share.jpg",
		desc:"<?php echo $config['seo_desc'];?>"	});
});
$(document).ready(function(e) {
    if("<?php echo $thisuser['is_reg'];?>" === "0") {
		$(".weui_dialog_alert").show();
	}
});
function apply() {
	if(!get_radio_value("card")) {
		alert("请选择一张亲子卡");
		return false;
	}
	$(".weui_loading_toast").show();
	if(_is_requesting) {
		return false;
	} else {
		_is_requesting = true;
	}
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>b/?ac=apply&id=<?php echo $thisactivity['id'];?>&aid=<?php echo $thisauth['id'];?>",
		data:{"card":get_radio_value("card")},
		type:"POST",
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$(".weui_loading_toast").hide();
				showBg();
			}
			_is_requesting = false;
		}
	});
}
function showBg() { 
	var bh = Math.max($("body").height(), $(window).height()); 
	var bw = Math.max($("body").width(), $(window).width()); 
	$(".mask").css({ 
		height:bh, 
		width:bw, 
		display:"block" 
	}); 
	$(".dialog").show(); 
} 
//关闭灰色 jQuery 遮罩 
function closeBg() { 
	$(".mask,.dialog").hide(); 
} 
function empty(str, zero) {
	if(typeof str =="undefined") {
		return true;
	}
	str = str.replace(/^[\t\r\n\s]*/, '').replace(/[\r\t\s\n]*$/, '');
	if(str == '' || (str == '0' && zero == false)) {
		return true;
	} else {
		return false;
	}
}
</script>
</html>