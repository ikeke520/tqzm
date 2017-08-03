<?php
$page = intval($_GET['page']);
$total_num = $mysql_class->num_table("users_coinlog", array("uid"=>$thisuser['id']));
$per_page = 10;
$maxpage = ceil($total_num / $per_page);
$page = max(1, min($maxpage, $page));
$records = $mysql_class->select("users_coinlog", "*", array("uid"=>$thisuser['id']), "id DESC", array(($page - 1)*$per_page, $per_page));
if($_GET['is_ajax'] != "true") {
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title>积分记录__<?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link rel="stylesheet" type="text/css" href="<?php echo check_dir($config['web_url']);?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/common.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/swiper.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
</head>
<body id="box">
	
	<div class="s_banner">
		<div class="s_banner_dom">
			<img src="<?php echo check_dir($config['web_url']);?>images/s_banner.jpg">
		</div>
		<div class="s_banner_tou">
			<img src="<?php echo $thisuser['headimgurl'];?>" style="border-radius:50%;">
			<p><span><?php echo $thisuser['return_coin'];?> 个积分</span></p>
		</div>	
	</div>
	
	<div class="clear"></div>
    <h1 style="padding:10px; background:#f1f1f1;">积分记录</h1>
	<div style="padding:0 10px;" class="coincont">
		<ul>
<?php
}
if($_GET['is_ajax'] == "true") {
	ob_start();
}
if($records) {
	foreach($records as $list) {
?>
				<li style="border-bottom:solid 1px #ececec;">
					<p style="padding:10px 0;">
						<i style=" float:left; width:28%;"><?php if($list['type'] == 0){?>积分奖励<?php } else if($list['type'] == 1){?>消费积分<?php } else {?>积分兑换<?php }?></i>
						<i style=" float:left; width:20%;"><?php if($list['type'] == 0){?><font color="green"><b>+<?php echo $list['coin'];?></b></font><?php } else if($list['type'] == 1){?><font color="red"><b>-<?php echo $list['coin'];?></b></font><?php } else {?><font color="red"><b>-<?php echo $list['coin'];?></b></font><?php }?></i>
                    	<i style=" float:left; width:50%; color:gray;"><?php echo $list['time'];?></i>
                        
                       </p>
                    <p style="padding:20px 0px; color:gray;">详情：<?php echo $list['msg'];?></p>
				</li>
<?php
	}
	if($_GET['is_ajax'] == "true") {
		$html = ob_get_contents();
		ob_end_clean();
		exit(json_encode(array("error"=>0, "maxpage"=>$maxpage, "html"=>$html)));
	}
}
?>
        </ul>
    </div>
<?php
if($records) {
?>	
    <div class="loader">
        <a class="loadbtn" href="javascript:" onClick="load_activity();" data-page="1" data-maxpage="<?php echo $maxpage;?>">加载更多</a>
    </div>
<?php
}
?>
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
function load_activity(page) {
	if($(".loadbtn").attr("loading") == 'true') {
		return ;
	}
	$(".weui_loading_toast").show();
	$(".loadbtn").html("正在加载……").attr("loading", 'true');
	var page = parseInt(page) ? parseInt(page) : parseInt($(".loadbtn").attr("data-page"))+1;
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>u/?ac=coin&is_ajax=true",
		type:"GET",
		data:{"page":page},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				if(page == 1) {
					$(".coincont ul").html(data.html);
				} else if( page <= data.maxpage) {
					$(".coincont ul").append(data.html);
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
</script>
</html>