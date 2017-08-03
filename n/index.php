<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");
$thisuser = get_user();
$wxjsapi = get_wxsign();
if($_GET['ac'] == "info") {
	include("./info.php");
	exit;
}
$thiscat = $mysql_class->select_one("newscat", "*", array("id"=>intval($_GET['cid'])));
if(empty($thiscat)) {
	$thiscat = $mysql_class->select_one("newscat");
}
$page = intval($_GET['page']);
$total_num = $mysql_class->num_table("news", array("cid"=>$thiscat['id']));
$per_page = 10;
$maxpage = ceil($total_num / $per_page);
$page = max(1, min($maxpage, $page));
$records = $mysql_class->select("news", "*", array("cid"=>$thiscat['id']), "id DESC", array(($page - 1)*$per_page, $per_page));
if($_GET['is_ajax'] != "true") {
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
	<div class="page_main">
		<div class="swiper-container">   
		    <div class="swiper-wrapper">
<?php
$slider = $mysql_class->select("slider", "*", array("is_hide"=>"0"), "index_order ASC");
if($slider) {
	foreach($slider as $list) {
?>
			    <div class="swiper-slide">
			        <a href="<?php echo $list['url'];?>"><img src="<?php echo $list['picture'];?>"></a>
			        <div class="banner_title"><?php echo $list['intro'];?></div>
			    </div>
<?php
	}
}
?>
			</div>
		    <div class="swiper-pagination"></div>
		</div>
	</div>
	<div class="clear"></div>
	<div class="hd_lines">
		<div class="hd_lines_t">
<?php
$newscat = $mysql_class->select("newscat");
if($newscat) {
	foreach($newscat as $list) {
?>
			<span<?php if($list['id'] == $thiscat['id']){?> class="hd_lines_on"<?php }?> onClick="location.href='<?php echo check_dir($config['web_url']);?>n/?cid=<?php echo $list['id'];?>'"><?php echo $list['name'];?></span>
<?php
	}
}
?>
		</div>
		<div class="hd_ul">
			<ul>
<?php
}
if($_GET['is_ajax'] == "true") {
	ob_start();
}
if($records) {
	foreach($records as $list) {
?>
				<li>
					<a href="<?php echo check_dir($config['web_url']);?>n/?ac=info&id=<?php echo $list['id'];?>">
						<img src="<?php echo $list['url'];?>">
						<h4><?php echo $list['title'];?></h4>
						<p><?php echo cn_substr($list['content'], 20);?></p>
						<span><?php echo substr($list['time'], 0, 10);?></span>
					</a>
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
if($records) {
?>	
        <div class="loader">
            <a class="loadbtn" href="javascript:" onClick="load_activity();" data-page="1" data-maxpage="<?php echo $maxpage;?>">加载更多</a>
        </div>
<?php
}
?>
	</div>
	<div class="a_footer">
		<ul>
			<li><a href="<?php echo check_dir($config['web_url']);?>"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer1.jpg"><span>首页</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>d/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer2.jpg"><span>活动日历</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>c/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer3.jpg"><span>童趣卡</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>u/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer4.jpg"><span>我的</span></a></li>
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
	var swiper = new Swiper('.swiper-container', {
		pagination: '.swiper-pagination',
		paginationClickable: true,
		autoplay: 2500,
		autoplayDisableOnInteraction: false
	});
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
		url:"<?php echo check_dir($config['web_url']);?>n/?is_ajax=true",
		type:"GET",
		data:{"cid":"<?php echo $thiscat['id'];?>", "page":page},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				if(page == 1) {
					$(".hd_ul ul").html(data.html);
				} else if( page <= data.maxpage) {
					$(".hd_ul ul").append(data.html);
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