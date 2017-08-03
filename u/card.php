<?php
$type = intval($_GET['type']);
$page = intval($_GET['page']);

$where = "uid='{$thisuser['id']}'";
if($type < 1) {
	$where .= " AND is_finish='0'";
} else {
	$where .= " AND is_finish='1'";
}

$total_num = $mysql_class->num_table("users_card", $where);
$per_page = 10;
$maxpage = ceil($total_num / $per_page);
$page = max(1, min($maxpage, $page));
$cardlist = $mysql_class->select("users_card", "*", $where, "id DESC", array(($page -1)*$per_page, $per_page));
if($_GET['is_ajax'] != "true") {
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title>我的童趣卡__<?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link rel="stylesheet" type="text/css" href="<?php echo check_dir($config['web_url']);?>css/style.css">
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
	<div class="x_main_t">
		<em<?php if($type < 1) {?> class="x_youxiao"<?php }?> onclick="location.href='<?php echo check_dir($config['web_url']);?>u/?ac=card&type=0'">有效卡</em>
		<em<?php if($type > 0) {?> class="x_youxiao"<?php }?> onclick="location.href='<?php echo check_dir($config['web_url']);?>u/?ac=card&type=1'">无效卡</em>
	</div>
	<div class="f_main f_qudiao x_xiamian">
		<ul>
<?php
}
if($_GET['is_ajax'] == "true") {
	ob_start();
}
if($cardlist) {
	foreach($cardlist as $list) {
?>
			<li>
				<img<?php if($list['cid'] == 0) {?> style="margin-top:40px;"<?php }?> src="<?php if($list['cid']) {?><?php echo $list['url'];?><?php } else {?><?php echo check_dir($config['web_url']);?>images/f_secai.jpg<?php }?>">
				<div class="f_main_cc">
					<div class="f_main_l">
						<h3>购买价格<strong>￥<?php echo $list['price'];?></strong></h3>
					</div>

				</div>
				<div class="f_main_bb">
					<div class="f_main_r">
						<span>参加活动情况：<i><?php echo $list['cost_num'];?> / <?php echo $list['num'];?></i></span>
                        <p style="position:absolute; right:10px; bottom:4px; color:#fff;"><?php echo $list['time'];?></p>
					</div>
				</div>
			</li>
<?php
	}
}
if($_GET['is_ajax'] == "true") {
	$html = ob_get_contents();
	ob_end_clean();
	exit(json_encode(array("error"=>0, "maxpage"=>$maxpage, "html"=>$html)));
}
?>
		</ul>
	</div>
<?php
if($cardlist) {
?>	
        <div class="loader">
            <a class="loadbtn" href="javascript:" onClick="load_card();" data-page="1" data-maxpage="<?php echo $maxpage;?>">加载更多</a>
        </div>
<?php
}
?>
	<div class="f_bottom">
		<input type="button" onClick="location.href='<?php echo check_dir($config['web_url']);?>'" value="点击查看最新活动">
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
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/swiper.min.js"></script>
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
function load_card(page) {
	if($(".loadbtn").attr("loading") == 'true') {
		return ;
	}
	$(".weui_loading_toast").show();
	$(".loadbtn").html("正在加载……").attr("loading", 'true');
	var page = parseInt(page) ? parseInt(page) : parseInt($(".loadbtn").attr("data-page"))+1;
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>u/?ac=card&is_ajax=true",
		type:"GET",
		data:{"type":"<?php echo $type;?>", "page":page},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				if(page == 1) {
					$(".f_main ul").html(data.html);
				} else if( page <= data.maxpage) {
					$(".f_main ul").append(data.html);
				}
				if(page >= data.maxpage) {
					$(".loadbtn").html("已经没有更多内容了").attr("data-page", data.maxpage).attr("loading", 'false');
				} else {
					$(".loadbtn").html("加载更多").attr("loading", 'false').attr("data-page", page);
				}
				$(".weui_loading_toast").hide();
			}
		}
	});
}
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
	var bh = Math.max($("body").height(), $(window).height()); 
	var bw = Math.max($("body").width(), $(window).width()); 
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