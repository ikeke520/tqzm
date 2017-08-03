<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");
$thisuser = get_user();
$wxjsapi = get_wxsign();

if($_GET['ac'] == "check") {
	$hash = trim($_POST['hash']);
	if(empty($hash) || strlen($hash) < 8) {
		exit(json_encode(array("error"=>1, "msg"=>"输入的激活码有误！")));
	}
	$thiscard = $mysql_class->select_one("cardbase", "*", array("hash"=>$hash, "is_use"=>0));
	if(empty($thiscard)) {
		exit(json_encode(array("error"=>1, "msg"=>"输入的激活码有误！")));
	} else {
		$mysql_class->insert("users_card", array("uid"=>$thisuser['id'], "is_real"=>1, "cardno"=>addslashes($thiscard['cardno']), "name"=>addslashes($thiscard['name']), "price"=>$thiscard['price'], "num"=>$thiscard['num'], "time"=>NOW));
		$mysql_class->update("cardbase", array("is_active"=>1, "uid"=>$thisuser['id'], "uname"=>addslashes($thisuser['nickname']), "active_time"=>NOW), array("id"=>$thiscard['id']));
		exit(json_encode(array("error"=>0)));
	}
} else if($_GET['ac'] == "info") {
	include("./info.php");
	exit;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title>童趣卡__<?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/common.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/swiper.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
</head>
<body id="box">
	<div class="f_top">
		<div class="f_top_span">
			<div class="f_top_span_l"><strong>激活实体卡</strong>（在线购卡无需激活码）</div>
			<div class="f_top_span_r"><a href="<?php echo check_dir($config['web_url']);?>i/?ac=faq"><i>常见问题</i><span>?</span></a></div>
		</div>
		<div class="f_top_input">
			<input type="text" name="cardhash" placeholder="请输入实体卡背面的激活码" class="f_top_txt">
			<a href="javascript:check_out();"><input type="button" value="确认" class="f_top_sub"></a>
		</div>	
	</div>
	<div class="a_kongge"></div>
	<div class="f_tit"><p><a href="<?php echo check_dir($config['web_url']);?>">点击查看最新活动（点击后链接到活动页）</a></p><img src="<?php echo check_dir($config['web_url']);?>images/b_zuo.jpg"></div>
	<div class="a_kongge b_kongge"></div>
	<div class="f_main f_xiamian f_qudiao">
		<div class="f_main_t">
			<i></i>
			<h3>购卡专区</h3>
			<i></i>
		</div>
		<ul>
<?php
$cardlist = $mysql_class->select("card", "*", array("is_del"=>0), "index_order ASC");
if($cardlist) {
	foreach($cardlist as $list){
?>
			<li onClick="location.href='<?php echo check_dir($config['web_url']);?>c/?ac=info&id=<?php echo $list['id'];?>'">
				<img src="<?php echo $list['url'];?>">
				<div class="f_main_cc">
                	<div class="f_main_l">
                        <h3><strong>￥<?php echo $list['price'];?></strong></h3>
                        <p><?php if($list['org_price'] > 0) {?>￥<?php echo $list['org_price'];?><?php }?></p>
                    </div>
                </div>
				<div class="f_main_bb">
                    <div class="f_main_r">
                        <span>可参加<i> <?php echo $list['num'];?> </i>次亲子活动</span>
                        <a href="<?php echo check_dir($config['web_url']);?>c/?ac=info&id=<?php echo $list['id'];?>">详情</a>
                    </div>
                </div>
			</li>
<?php
	}
}
?>
		</ul>
	</div>
	<div class="f_bottom f_zengjia">
		<input type="button" onClick="location.href='<?php echo check_dir($config['web_url']);?>'" value="点击查看最新活动">
	</div>
	
	<div class="a_footer">
		<ul>
			<li><a href="<?php echo check_dir($config['web_url']);?>"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer1.jpg"><span>首页</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>d/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer2.jpg"><span>活动日历</span></a></li>
			<li class="a_li1"><a href="<?php echo check_dir($config['web_url']);?>c/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer7.jpg"><span>童趣卡</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>u/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer4.jpg"><span>我的</span></a></li>
		</ul>
	</div>
	<div class="f_tanc_bg dialog">
		<div class="f_tanc">
			<h2>您输入的激活码有误!</h2>
			
			<a href="javascript:closeBg();" class="f_hide">确定</a>
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
    <div class="showmsg" style="display: none;">
        <div class="weui_mask_transparent"></div>
        <div class="weui_toast">
            <i class="weui_icon_toast"></i>
            <p class="weui_toast_content"></p>
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
var _is_posting = false;
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
function check_out() {
	var cardhash = $("input[name=cardhash]").val();
	if(empty(cardhash)) {
		$(".f_tanc h2").html("请填写激活码后再试。");
		showBg();
		return false;
	}
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>c/?ac=check",
		type:"POST",
		data:{"hash": cardhash},
		dataType:"json",
		success: function(data){
			if(data.error > 0) {
				$(".f_tanc h2").html(data.msg);
				showBg();
			} else {
				$(".f_tanc h2").html("恭喜您，激活成功！");
				showBg();
				setTimeout(function() {
					location.href="<?php echo check_dir($config['web_url']);?>u/?ac=card";
				}, 1003);
			}
		}
	});
}
function showBg() { 
	var bh = $("body").height(); 
	var bw = $("body").width(); 
	$(".mask").css({ 
		height:bh, 
		width:bw, 
		display:"block" 
	}); 
	$(".dialog").show(); 
} 
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