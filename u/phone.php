<?php
switch($_GET['do']) {
	case 'send':
		$phone = trim($_POST['phone']);
		if($phone == $thisuser['phone']) {
			exit(json_encode(array("error"=>1, "msg"=>"您已经绑定该号码。")));
		}
		if(preg_match("/^1\d{10}$/", $phone)) {
			$hash = rand(100000, 999999);
			$content = "您的短信验证码是：{$hash}，请勿将验证码提供给他人，有效期30分钟。";
			$sms_class = load_class("sms");
			$response = $sms_class->sendSMS($phone, $content, "676767");
			if(substr($response, 0, 2) == "1,") {
				$mysql_class->update("users", array("phone_hash"=>md5($phone.$hash), "phone_time"=>NOW), array("id"=>$thisuser['id']));
				exit(json_encode(array("error"=>0)));
			} else {
				exit(json_encode(array("error"=>1, "msg"=>"错误代码：".$response)));
			}
		} else {
			exit(json_encode(array("error"=>1, "msg"=>"手机号码不合法")));
		}
	break;
	case 'verify':
		$phone = trim($_POST['phone']);
		$hash = trim($_POST['hash']);
		if(preg_match("/^1\d{10}$/", $phone) && preg_match("/^\d{6}$/", $hash)) {
			if($thisuser['phone_hash'] == md5($phone.$hash) && strtotime($thisuser['phone_time']) > strtotime("-30 minutes")) {
				$mysql_class->update("users", array("phone"=>$phone, "phone_hash"=>"", "phone_time"=>""), array("id"=>$thisuser['id']));
				exit(json_encode(array("error"=>0)));
			} else {
				exit(json_encode(array("error"=>1, "msg"=>"手机号码或验证码不正确")));
			}
		} else {
			exit(json_encode(array("error"=>1, "msg"=>"手机号码或验证码不正确")));
		}
		
	break;
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
			url:"<?php echo check_dir($config['web_url']);?>u/?ac=phone&do=send&id=<?php echo $thisactivity['id'];?>",
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
			url:"<?php echo check_dir($config['web_url']);?>u/?ac=phone&do=verify&id=<?php echo $thisactivity['id'];?>",
			data:"phone="+phone+"&hash="+hash,
			type:"POST",
			dataType:"json",
			success: function(data) {
				is_sending = false;
				$(".weui_loading_toast").hide();
				$(".showmsg .weui_toast_content").html("验证成功");
				$(".showmsg").show();
				var url = "<?php echo check_dir($config['web_url']);?>u/?ac=profile";
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