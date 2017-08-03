<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");
$thisuser = get_user();
$wxjsapi = get_wxsign();
switch($_GET['ac']) {
	case 'activity':
		include("./activity.php");
		exit;
	break;
	case 'card':
		include("./card.php");
		exit;
	break;
	case 'profile':
		include("./profile.php");
		exit;
	break;
	case 'phone':
		include("./phone.php");
		exit;
	break;
	case 'pay':
		include("./pay.php");
		exit;
	break;
	case 'fav':
		include("./fav.php");
		exit;
	break;
	case 'coin':
		include("./coin.php");
		exit;
	break;
	case 'extend':
		include("./extend.php");
		exit;
	break;
	case 'relation':
		include("./relation.php");
		exit;
	break;
	case 'import':
		include("./import.php");
		exit;
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
	
	<div class="s_banner">
		<div class="s_banner_dom">
			<img src="<?php echo check_dir($config['web_url']);?>images/s_banner.jpg">
		</div>
		<div class="s_banner_tou">
			<img src="<?php echo $thisuser['headimgurl'];?>" style="border-radius:50%;">
			<p><span><?php echo $thisuser['nickname'];?></span></p>
		</div>	
	</div>
	
	<div class="clear"></div>
	
	<div class="s_me1">
		<ul>
			<li>
				<a href="<?php echo check_dir($config['web_url']);?>u/?ac=activity">
					<img src="<?php echo check_dir($config['web_url']);?>images/s_tu01.jpg">
					<span>参加的活动</span>
				</a>
			</li>
			<li>
				<a href="<?php echo check_dir($config['web_url']);?>u/?ac=card">
					<img src="<?php echo check_dir($config['web_url']);?>images/s_tu02.jpg">
					<span>我的童趣卡</span>
				</a>
			</li>
		</ul>
	</div>
	<div class="clear"></div>
	<div class="a_kongge b_kongge"></div>
	<div class="s_me2">
		<ul>
			<li>
				<a href="<?php echo check_dir($config['web_url']);?>u/?ac=profile">
					<img src="<?php echo check_dir($config['web_url']);?>images/s_tu03.jpg">
					<span>个人资料</span>
					<img src="<?php echo check_dir($config['web_url']);?>images/b_zuo.jpg">
				</a>
			</li>
			<li>
				<a href="<?php echo check_dir($config['web_url']);?>u/?ac=pay">
					<img src="<?php echo check_dir($config['web_url']);?>images/s_tu04.jpg">
					<span>消费日志</span>
					<img src="<?php echo check_dir($config['web_url']);?>images/b_zuo.jpg">
				</a>
			</li>
			<li>
				<a href="<?php echo check_dir($config['web_url']);?>u/?ac=fav">
					<img src="<?php echo check_dir($config['web_url']);?>images/s_tu05.jpg">
					<span>我的收藏</span>
					<img src="<?php echo check_dir($config['web_url']);?>images/b_zuo.jpg">
				</a>
			</li>
			<li>
				<a href="<?php echo check_dir($config['web_url']);?>u/?ac=coin">
					<img src="<?php echo check_dir($config['web_url']);?>images/s_tu06.jpg">
					<span>我的积分<i>查看我的积分</i></span>
					<img src="<?php echo check_dir($config['web_url']);?>images/b_zuo.jpg">
				</a>
			</li>
			<li>
				<a href="<?php echo check_dir($config['web_url']);?>u/?ac=extend">
					<img src="<?php echo check_dir($config['web_url']);?>images/s_tu07.jpg">
					<span>我的二维码<i>推广您的二维码，获得更多积分!</i></span>
					<img src="<?php echo check_dir($config['web_url']);?>images/b_zuo.jpg">
				</a>
			</li>
			<li>
				<a href="<?php echo check_dir($config['web_url']);?>u/?ac=relation">
					<img src="<?php echo check_dir($config['web_url']);?>images/s_tu08.jpg">
					<span>我的推广会员</span>
					<img src="<?php echo check_dir($config['web_url']);?>images/b_zuo.jpg">
				</a>
			</li>
<?php
$worker = $mysql_class->select_one("worker", "*", array("uid"=>$thisuser['id']));
if($worker) {
?>
			<li>
				<a href="<?php echo check_dir($config['web_url']);?>u/?ac=import">
					<img src="<?php echo check_dir($config['web_url']);?>images/s_tu01.jpg">
					<span>业务录入<i>我是业务员</i></span>
					<img src="<?php echo check_dir($config['web_url']);?>images/b_zuo.jpg">
				</a>
			</li>
<?php
}
?>            
		</ul>
	</div>
	<div class="clear"></div>
	<div class="s_me_dibu">
		<p><a href="<?php echo check_dir($config['web_url']);?>u/">会员中心</a>  |  <a href="<?php echo check_dir($config['web_url']);?>n/">最新资讯</a>  |  <a href="<?php echo check_dir($config['web_url']);?>i/">关于我们</a></p>
	</div>
	
	<div class="a_kongge b_kongge"></div>
	<div class="a_footer">
		<ul>
			<li><a href="<?php echo check_dir($config['web_url']);?>"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer1.jpg"><span>首页</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>d/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer2.jpg"><span>活动日历</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>c/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer3.jpg"><span>童趣卡</span></a></li>
			<li class="a_li1"><a href="<?php echo check_dir($config['web_url']);?>u/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer8.jpg"><span>我的</span></a></li>
		</ul>
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