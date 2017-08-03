<?php

include("./lib/include.php");
include(STCMS_ROOT."wxconfig.php");
//$thisuser = get_user();
//$wxjsapi = get_wxsign();
$thiscat = $mysql_class->select_one("category", "*", array("id"=>intval($_GET['cid'])));

$where = "1";
if($thiscat) {
	$where .= " AND cid='{$thiscat['id']}'";
} else {
	$where .= " AND is_rmd = '1'";
}

$where .= " AND is_complete = '1'";
$page = intval($_GET['page']);
$total_num = $mysql_class->num_table("activity", $where);
$per_page = 10;
$maxpage = ceil($total_num / $per_page);
$page = max(1, min($maxpage, $page));
$activitylist = $mysql_class->select("activity", "*", $where, "id DESC", array(($page - 1)*$per_page, $per_page));
if($_GET['is_ajax'] != "true") {
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title><?php if($thiscat) {?><?php echo $thiscat['name'];?>__<?php }?><?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link rel="stylesheet" type="text/css" href="<?php echo check_dir($config['web_url']);?>css/style.css?ver=201706041050">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/common.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/swiper.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
</head>
<body id="box">
<div class="page_box">
<?php
if($thiscat) {
?>
	<div class="b_top">
		<b onclick="javascript:history.back(-1);"><img src="<?php echo check_dir($config['web_url']);?>images/a_fanghui.png">返回</b>
		<h2><?php echo $thiscat['name'];?></h2>
	</div>
	<div class="a_kongge"></div>
<?php
} else {
?>
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
	<div class="a_title">
		<ul>
<?php
$category = $mysql_class->select("category", "*", false, "index_order ASC");
if($category) {
	foreach($category as $list) {
?>
			<li><a href="<?php echo check_dir($config['web_url']);?>?cid=<?php echo $list['id'];?>"><img src="<?php echo $list['url'];?>"><span><?php echo $list['name'];?></span></a></li>
<?php
	}
}
?>
		</ul>
	</div>
<?php
$adconfig = @include(STCMS_ROOT."adconfig.php");
if($adconfig['is_hide'] < 1) {
?>
	<div class="a_kongge"></div>
	<div class="a_advertising"><a href="<?php echo $adconfig['link'];?>"><img src="<?php echo $adconfig['picture'];?>"></a></div>
<?php
}
?>
	<div class="a_kongge"></div>
	<div class="a_main">
		<div class="a_main_hot">
			<img src="<?php echo check_dir($config['web_url']);?>images/a_title6.png">
			<p>
				<i></i>
				<span><?php if($thiscat) {?><?php echo $thiscat['name'];?><?php } else {?>热门活动<?php }?></span>
				<i></i>
			</p>
			<u>和孩子一起探索世界</u>
		</div>
<?php
}
?>
		<div class="a_main_c">
			<ul>
<?php
}
if($_GET['is_ajax'] == "true") {
	ob_start();
}
if($activitylist) {
	foreach($activitylist as $list) {
?>
				<li>
					<a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $list['id'];?>"><img src="<?php echo $mysql_class->get_field_value("activity_picture", "url", array("aid"=>$list['id']), "index_order ASC");?>"></a>
					<div class="a_main_b">
						<p><a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $list['id'];?>"><?php echo $list['title'];?></a></p>
						<p><img src="<?php echo check_dir($config['web_url']);?>images/a_main3.jpg"><span><?php echo $list['address'];?></span><img src="<?php echo check_dir($config['web_url']);?>images/a_main4.jpg"><span>仅剩<?php echo $list['person_num'] - $list['in_num'];?>个名额</span><img src="<?php echo check_dir($config['web_url']);?>images/a_main5.jpg"><span><?php echo $list['hit'];?>人已关注</span></p>
						<p><?php if($list['status'] == 0 && ($list['person_num'] - $list['in_num'] > 0 && $list['person_num'] > 0) && $list['date'] > date("Y-m-d")) {?><i class="a_bao">报名中</i><?php }?><?php $thistag = $mysql_class->select("tag_list", "*", array("aid"=>$list['id'])); if($thistag) {?><i><?php echo $thistag[0]['tname'];?></i><?php }?><span>&nbsp;&nbsp; <?php echo $list['year_duration'];?><!--&nbsp;&nbsp; <?php echo $list['in_num'];?>人已参与--></span><?php if($list['status'] == 0 && $list['person_num'] - $list['in_num'] > 0 && $list['date'] > date("Y-m-d")) {?><a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $list['id'];?>"><u>我要报名</u></a><?php } else {?><u>报名截止</u><?php }?></p>
					</div>
					<div class="a_kongge"></div>
					<div class="a_box_l"><span>￥<?php echo $list['price'];?>元</span></div>
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
        <div class="loader">
            <a class="loadbtn" href="javascript:" onClick="load_activity();" data-page="1" data-maxpage="<?php echo $maxpage;?>">加载更多</a>
        </div>
	</div>
	<div id="to_top"></div>
	<div class="a_footer">
		<ul>
			<li class="a_li1"><a href="<?php echo check_dir($config['web_url']);?>"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer5.jpg"><span>首页</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>d/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer2.jpg"><span>活动日历</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>c/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer3.jpg"><span>童趣卡</span></a></li>
			<li><a href="<?php echo check_dir($config['web_url']);?>u/"><img src="<?php echo check_dir($config['web_url']);?>images/a_footer4.jpg"><span>我的</span></a></li>
		</ul>
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
</body>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/phone.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/swiper.min.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/qiu_phone.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>layer_mobile/layer.js"></script>
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
		url:"<?php echo check_dir($config['web_url']);?>?is_ajax=true",
		type:"GET",
		data:{"cid":"<?php echo intval($thiscat['id']);?>", "page":page},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				if(page == 1) {
					$(".a_main_c ul").html(data.html);
				} else if( page <= data.maxpage) {
					$(".a_main_c ul").append(data.html);
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