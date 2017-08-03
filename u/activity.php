<?php
$type = intval($_GET['type']);
$page = intval($_GET['page']);
$tmp = $mysql_class->fetch($mysql_class->query("SELECT DISTINCT COUNT(*) AS num FROM {$config['db_prefix']}activity AS a LEFT JOIN {$config['db_prefix']}users_auth AS u ON a.id=u.aid WHERE u.uid='{$thisuser['id']}' AND u.is_finish='1' AND a.is_complete='1' AND ".($type ? "a.date <= '".date("Y-m-d")."'" : "a.date > '".date("Y-m-d")."'")));
$total_num = $tmp['num'];
$per_page = 10;
$maxpage = ceil($total_num / $per_page);
$page = max(1, min($maxpage, $page));
$activitylist = $mysql_class->fetch_all("SELECT a.*, u.is_cancel, u.id AS authid FROM {$config['db_prefix']}activity AS a RIGHT JOIN {$config['db_prefix']}users_auth AS u ON a.id=u.aid WHERE u.uid='{$thisuser['id']}' AND u.is_finish='1' AND a.is_complete='1' AND ".($type ? "a.date <= '".date("Y-m-d")."'" : "a.date > '".date("Y-m-d")."'")." ORDER BY u.id DESC LIMIT ".($page - 1)*$per_page.", {$per_page}");
if($_GET['is_ajax'] != "true") {
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title>我参加的活动__<?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link rel="stylesheet" type="text/css" href="<?php echo check_dir($config['web_url']);?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/common.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/swiper.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
</head>
<body id="box">
	<div class="v_main">
		<div class="v_main_t">
			<ul>
				<li<?php if($type < 1) {?> class="v_on"<?php }?> onclick="location.href='<?php echo check_dir($config['web_url']);?>u/?ac=activity&type=0'">未开始</li>
				<li<?php if($type > 0) {?> class="v_on"<?php }?> onClick="location.href='<?php echo check_dir($config['web_url']);?>u/?ac=activity&type=1'">已结束</li>
			</ul>
		</div>
        <div class="a_kongge"></div>
		<div class="<?php if($activitylist) {?>a_main_c<?php } else {?>v_main_c<?php }?>">
<?php
}
if($activitylist) {
	if($_GET['is_ajax'] == "true") {
		ob_start();
	}
?>
<?php
	if($_GET['is_ajax'] != "true") {
?>
        	<ul>
<?php
	}
?>
<?php
	foreach($activitylist as $list) {
		if($list['is_cancel'] == 0) {
			$thischild = $mysql_class->fetch($mysql_class->query("SELECT * FROM {$config['db_prefix']}users_person AS p LEFT JOIN {$config['db_prefix']}users_auth_person AS a ON p.id=a.pid WHERE a.aid={$list['authid']} AND a.is_adult='0'"));
		}
?>
				<li>
					<a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $list['id'];?>"><img src="<?php echo $mysql_class->get_field_value("activity_picture", "url", array("aid"=>$list['id']), "index_order ASC");?>"></a>
					<div class="a_main_b">
						<p><a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $list['id'];?>"><?php echo $list['title'];?></a></p>
						<p><img src="<?php echo check_dir($config['web_url']);?>images/a_main3.jpg"><span><?php echo $list['address'];?></span><img src="<?php echo check_dir($config['web_url']);?>images/a_main4.jpg"><span>仅剩<?php echo $list['person_num'] - $list['in_num'];?>个名额</span><img src="<?php echo check_dir($config['web_url']);?>images/a_main5.jpg"><span><?php echo $list['hit'];?>人已关注</span></p>
						<p><?php if($list['status'] == 0 && ($list['person_num'] - $list['in_num'] > 0 && $list['person_num'] > 0) && $list['date'] > date("Y-m-d")) {?><i class="a_bao">报名中</i><?php }?><?php $thistag = $mysql_class->select("tag_list", "*", array("aid"=>$list['id'])); if($thistag) {?><i><?php echo $thistag[0]['tname'];?></i><?php }?><span>&nbsp;&nbsp; <?php if($list['is_cancel'] == 0) {?><?php echo $thischild['name'];?> 报名<?php } else {?>报名已取消并返还<?php }?></span><?php if($list['date'] > date("Y-m-d")) {?><?php if($list['is_cancel']){?><u>已经取消报名</u><?php } else { ?><a href="javascript:" onClick="refund('<?php echo $list['id'];?>', '<?php echo $list['authid'];?>');"><u>取消报名</u></a><?php }?><?php } else {?><u>报名截止</u><?php }?></p>
					</div>
					<div class="a_kongge"></div>
					<div class="a_box_l"><span>￥<?php echo $list['price'];?>元</span></div>
				</li>
<?php
	}
	if($_GET['is_ajax'] == "true") {
		$html = ob_get_contents();
		ob_end_clean();
		exit(json_encode(array("error"=>0, "maxpage"=>$maxpage, "html"=>$html)));
	}
?>
			</ul>
<?php
} else {
?>
			<ul>
				<li>
					<img src="<?php echo check_dir($config['web_url']);?>images/c_img.jpg">
					<p>没有任何活动哦~</p>
				</li>
			</ul>
<?php
}
?>
        </div>
<?php
if($activitylist) {
?>	
        <div class="loader">
            <a class="loadbtn" href="javascript:" onClick="load_activity();" data-page="1" data-maxpage="<?php echo $maxpage;?>">加载更多</a>
        </div>
<?php
}
?>
	</div>
	<div class="u_bottom">
		<a href="<?php echo check_dir($config['web_url']);?>">点击查看最新活动</a>
	</div>
	<div class="d_tanc_bg dialog">
		<div class="d_tanc">
			<h2>您确定要取消报名吗？</h2>
			<a href="javascript:" onClick="dorefund();">确定</a>
			<a href="javascript:" onClick="closeBg();">取消</a>
			<a href="javascript:closeBg();" class="d_hide"></a>
		</div>
	</div>
    <div class="k_tanc_bg dialog1">
		<div class="k_tanc">
			<h2>报名取消成功。</h2>
			
			<a href="javascript:closeBg1();" class="d_hide"><span></span></a>
		</div>
		<div class="k_wenben1">
			<a href="javascript:" class="d_hide"><input type="button" onClick="closeBg1();" value="确定" class="k_tijiao"></a>
		</div>
	</div>
	<div id="to_top"></div>
	<div class="mask"></div>
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
var _refund_id = 0;
var _refund_aid = 0;
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
		url:"<?php echo check_dir($config['web_url']);?>u/?ac=activity&is_ajax=true",
		type:"GET",
		data:{"type":"<?php echo $type;?>", "page":page},
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
function refund(id, aid) {
	_refund_id = id;
	_refund_aid = aid;
	showBg();
}
function dorefund() {
	closeBg();
	if(_refund_id, _refund_aid) {
		$(".weui_loading_toast").show();
		$.ajax({
			url:"<?php echo check_dir($config['web_url']);?>b/?ac=refund",
			type:"GET",
			data:{"id":_refund_id, "aid":_refund_aid},
			dataType:"json",
			success: function(data) {
				if(data.error >0) {
					alert(data.msg);
				} else {
					$(".weui_loading_toast").hide();
					showBg1();
					setTimeout(function(){location.reload();}, 2003);
				}
			}
		});
	}
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
</script>
</html>