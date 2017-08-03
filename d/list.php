<?php
$date = trim($_GET['date']);
if(!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
	header("Location:".check_dir($config['web_url'])."d/");
	exit($date);
	exit;
}
$activitylist = $mysql_class->select("activity", "*", array("date"=>$date, "is_complete"=>"1"));
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
<link rel="stylesheet" type="text/css" href="<?php echo check_dir($config['web_url']);?>css/common.css">
<link rel="stylesheet" type="text/css" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css">
</head>
<body>
	<div class="b_top">
		<b onclick="javascript:window.history.back(-1);"><img src="<?php echo check_dir($config['web_url']);?>images/a_fanghui.png"></b>
		<h2><?php echo $date;?></h2>
	</div>
	
	<div class="z_rili">
		<a href="<?php echo check_dir($config['web_url']);?>d/">
			<img src="<?php echo check_dir($config['web_url']);?>images/z_rili.png">
			<p>所有日期</p>
			<img src="<?php echo check_dir($config['web_url']);?>images/z_sanjiao.png">
		</a>
	</div>
	<div class="a_kongge b_kongge"></div>
	<div class="a_main u_main">
<?php
if($activitylist) {
	foreach($activitylist as $list) {
?>
		<div class="a_main_c z_main_c">
			<ul>
				<li>
					<a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $list['id'];?>"><img src="<?php echo $mysql_class->get_field_value("activity_picture", "url", array("aid"=>$list['id']), "index_order ASC");?>"></a>
					<div class="a_main_b">
						<p><a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $list['id'];?>"><?php echo $list['title'];?></a></p>
						<p><img src="<?php echo check_dir($config['web_url']);?>images/a_main3.jpg"><span><?php echo $list['address'];?></span><img src="<?php echo check_dir($config['web_url']);?>images/a_main4.jpg"><span>仅剩<?php echo $list['person_num'] - $list['in_num'];?>个名额</span><img src="<?php echo check_dir($config['web_url']);?>images/a_main5.jpg"><span><?php echo $list['hit'];?>人已关注</span></p>
						<p><?php if($list['status'] == 0 && ($list['person_num'] - $list['in_num'] > 0 && $list['person_num'] > 0) && $list['date'] > date("Y-m-d")) {?><i class="a_bao">报名中</i><?php }?><?php $thistag = $mysql_class->select("tag_list", "*", array("aid"=>$list['id'])); if($thistag) {?><i><?php echo $thistag[0]['tname'];?></i><?php }?><span><?php echo $list['year_duration'];?> &nbsp;&nbsp; <!--<?php echo $list['in_num'];?>人已参与--></span><?php if($list['status'] == 0 && $list['person_num'] - $list['in_num'] > 0 && $list['date'] > date("Y-m-d")) {?><a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $list['id'];?>"><u>我要报名</u></a><?php } else {?><u>报名截止</u><?php }?></p>
					</div>
					<div class="a_kongge"></div>
					<div class="a_box_l"><span>￥<?php echo $list['price'];?>元</span></div>
				</li>
				
			</ul>
		</div>
<?php
	}
}
?>
	</div>
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
</script>
</html>