<?php
$thiscard = $mysql_class->select_one("card", "*", array("id"=>intval($_GET['id']), "is_del"=>0));
if(empty($thiscard)) {
	header("Location: ".check_dir($config['web_url']));
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
<body id="box">
	
	
	<div class="f_main h_main">
		<ul>
			<li>
				<img src="<?php echo $thiscard['url'];?>">
				<div class="f_main_cc">
					<div class="f_main_l">
						<h3><strong>￥<?php echo $thiscard['price'];?></strong></h3>
						<p><?php if($thiscard['org_price'] > 0) {?>￥<?php echo $thiscard['org_price'];?><?php }?></p>
					</div>

				</div>
				<div class="f_main_bb">
					<div class="f_main_r">
						<span>可参加<i> <?php echo $thiscard['num'];?> </i>次亲子活动</span>
						<a href="<?php echo check_dir($config['web_url']);?>pay/?type=0&id=<?php echo $thiscard['id'];?>" onClick="return checkout();">购买亲子卡</a>
					</div>
				</div>
			</li>
		</ul>
	</div>
	<div class="a_kongge n_kongge"></div>
	<div class="f_main f_qudiao">
		<div class="f_main_t">
			<i></i>
			<h3>使用规则</h3>
			<i></i>
		</div>
	</div>

	<div class="h_main01" style="padding:0 12px; width:auto !important; margin-bottom:50px;">
		<?php echo nl2br($thiscard['intro']);?>
	</div>
	
	<div class="b_footer h_footer">
		<ul>
			<li><span>￥<?php echo $thiscard['price'];?> </span></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>pay/?type=0&id=<?php echo $thiscard['id'];?>" onClick="return checkout();"><span>立即购买</span></a></li>
		</ul>
	</div>
	<div class="k_tanc_bg dialog1">
		<div class="k_tanc">
			<h2>购卡提示</h2>
			
			<a href="javascript:closeBg1();" class="d_hide"><span></span></a>
		</div>
		<div class="k_wenben">
			<div class="k_wenben_fb" style="width:96%; text-align:center;">
				您已购买了一次体验卡，不能再次购买。
			</div>	
		</div>
		<div class="k_wenben1">
			<a href="javascript:" class="d_hide"><input type="button" onClick="closeBg1();" value="确定" class="k_tijiao"></a>
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
function showBg1() { 
	var bh = Math.max($("body").height(), $(window).height()); 
	var bw = Math.max($("body").width(), $(window).width()); 
	$(".mask").css({ 
		height:bh, 
		width:bw, 
		display:"block" 
	}); 
	$(".dialog1").show(); 
} 
function closeBg1() { 
	$(".mask,.dialog1").hide(); 
}
function checkout() {
<?php if($thiscard['is_trial'] == 1 && $mysql_class->num_table("users_card", array("uid"=>$thisuser['id'], "cid"=>$thiscard['id'])) > 0) {?>
	showBg1();
	return false;
<?php
} else {
?>
	return true;
<?php
}
?>
}
</script>
</html>