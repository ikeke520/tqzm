<?php
$thisauth = $mysql_class->select_one("users_auth", "*", array("id"=>intval($_GET['aid']), "uid"=>$thisuser['id'], "aid"=>$thisactivity['id']));
$payway = intval($payway);
if(empty($thisauth)) {
	exit("未找到制定的订单。");
}
if($thisuser['phone'] && intval($_GET['reset']) == 0) {
	if($payway) {
		$url = check_dir($config['web_url'])."pay/?type=1&id=".$thisactivity['id']."&aid=".$thisauth['id'];
	} else {
		$url = check_dir($config['web_url'])."b/?ac=selectcard&id=".$thisactivity['id']."&aid=".$thisauth['id'];
	}
	header("Location: ".$url);
	exit;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title><?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link rel="stylesheet" type="text/css" href="<?php echo check_dir($config['web_url']);?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/common.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/swiper.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
</head>
<body>
	<div class="f_top">
		<p>请输入联系人电话号码，便于我们联系你哦！</p>
		<div class="f_top_input">
			<input type="text" name="phone" placeholder="请输入手机号" class="f_top_txt">
			<input type="button" onClick="send();" value="获取验证码" class="f_top_sub">
		</div>
		<div class="f_top_input">
			<input type="text" name="hash" placeholder="请输入短信验证码" class="f_top_txt p_huoqu" style="width:100%;border-radius:5px;margin-top:1.2rem;">
		</div>
	</div>

	<div class="f_bottom i_bottom">
		<input type="submit" onclick="verify();" value="确定">
	</div>
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
<script type="text/javascript">
var is_sending = false;
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
function send() {
	var phone = $("input[name=phone]").val();
	if(empty(phone)) {
		alert("请输入手机号码。");
		return false;
	}
	if(is_sending == false) {
		is_sending = true;
		$.ajax({
			url:"<?php echo check_dir($config['web_url']);?>b/?ac=send&id=<?php echo $thisactivity['id'];?>",
			data:"phone="+phone,
			type:"POST",
			dataType:"json",
			success: function(data) {
				is_sending = false;
				if(data.error > 0) {
					alert(data.msg);
				} else {
					var time = 60;
					var timer = function() {
						if(time > 0) {
							$(".f_top_sub").attr("disabled", true).val("请等待"+time);
							time--;
							setTimeout(timer, 1000);
						} else {
							$(".f_top_sub").attr("disabled", false).val("获取验证码");
						}
					}
					timer();
				}
			}
		});
	}
}
function verify() {
	var phone = $("input[name=phone]").val();
	if(empty(phone)) {
		alert("请输入手机号码。");
		return false;
	}
	var hash = $("input[name=hash]").val();
	if(empty(hash)) {
		alert("请输入验证码。");
		return false;
	}
	if(is_sending == false) {
		is_sending = true;
		$(".weui_loading_toast").show();
		$.ajax({
			url:"<?php echo check_dir($config['web_url']);?>b/?ac=verify&id=<?php echo $thisactivity['id'];?>",
			data:"phone="+phone+"&hash="+hash,
			type:"POST",
			dataType:"json",
			success: function(data) {
				is_sending = false;
				$(".weui_loading_toast").hide();
				$(".showmsg .weui_toast_content").html("验证成功");
				$(".showmsg").show();
<?php
if($payway < 1 && $thisactivity['payway'] < 1) {
?>
				var url = "<?php echo check_dir($config['web_url']);?>b/?ac=selectcard&id=<?php echo $thisactivity['id'];?>&aid=<?php echo $thisauth['id'];?>";
<?php
} else {
?>
				var url = "<?php echo check_dir($config['web_url']);?>pay/?type=1&id=<?php echo $thisactivity['id'];?>&aid=<?php echo $thisauth['id'];?>";
<?php
}
?>
				setTimeout(function(){location.href=url}, 1003);
			}
		});
	}
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