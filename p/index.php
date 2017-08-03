<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");
$thisuser = get_user();
$wxjsapi = get_wxsign();
$thisinfo = $mysql_class->select_one("setting_info", "*", array("type"=>"apply"));
if($_GET['ac'] == "post") {
	$data['name'] = htmlspecialchars($_POST['name']);
	$data['phone'] = htmlspecialchars($_POST['phone']);
	$data['company'] = htmlspecialchars($_POST['company']);
	if(empty($data['name']) || empty($data['phone']) || empty($data['company'])) {
		exit(json_encode(array("error"=>1, "msg"=>"姓名、手机号码、公司名称不能为空。")));
	}
	if(!preg_match("/^1\d{10}$/", $data['phone'])) {
		exit(json_encode(array("error"=>1, "msg"=>"手机号码填写错误。")));
	}
	$data['time'] = NOW;
	$mysql_class->insert("apply", $data);
	exit(json_encode(array("error"=>0)));
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title>大客户定制_<?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link rel="stylesheet" type="text/css" href="<?php echo check_dir($config['web_url']);?>css/style.css?ver=20170604">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/common.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/swiper.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
</head>
<body id="box">
	<div class="max_main">
		<?php echo $thisinfo['content'];?>
	</div>
	<div class="max_bottom">
		<form onSubmit="return check_form();" method="post">
			<ul>
				<li><img src="<?php echo check_dir($config['web_url']);?>images/box_m1.jpg"><input type="text" name="name" placeholder="请填写你的真实姓名"></li>
				<li><img src="<?php echo check_dir($config['web_url']);?>images/box_m2.jpg"><input type="text" name="phone" placeholder="请填写你的联系电话"></li>
				<li><img src="<?php echo check_dir($config['web_url']);?>images/box_m3.jpg"><input type="text" name="company" placeholder="请填写你的公司名称"></li>
			</ul>
			<input type="submit" value="立即预约">
		</form>
	</div>

	<div class="max_ft">
		<img src="<?php echo check_dir($config['web_url']);?>images/max_er.jpg">
		<p>长按二维码关注“童趣周末”公众号</p>
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
var __is_requesting = false;
function check_form() {
	if($("input[name=name]").val() == "") {
		alert("请填写您的姓名！");
		return false;
	}
	if($("input[name=phone]").val() == "") {
		alert("请填写您的手机号码！");
		return false;
	}
	if($("input[name=company]").val() == "") {
		alert("请填写您的公司名称！");
		return false;
	}
	if(__is_requesting == false) {
		__is_requesting = true;
		$(".weui_loading_toast").show();
		$.ajax({
			url:"<?php echo check_dir($config['web_url']);?>p/?ac=post",
			type:"POST",
			data:{name:$("input[name=name]").val(), phone:$("input[name=phone]").val(), company:$("input[name=company]").val()},
			dataType:"json",
			success: function(data) {
				__is_requesting = false;
				if(data.error < 1) {
					alert("您的留言已收到，我们将及时给您回复！");
				} else {
					alert(data.msg);
				}
				setTimeout(location.reload(), 1003);
			}
		});
	}
	return false;
}
</script>
</html>