<?php
$type = intval($_GET['type']);
$page = intval($_GET['page']);

$where = "uid='{$thisuser['id']}'";
$where .= " AND type='{$type}'";

$total_num = $mysql_class->num_table("users_paylog", $where);
$per_page = 10;
$maxpage = ceil($total_num / $per_page);
$page = max(1, min($maxpage, $page));
$records = $mysql_class->select("users_paylog", "*", $where, "id DESC", array(($page -1)*$per_page, $per_page));
if($_GET['is_ajax'] != "true") {
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title>消费记录__<?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link rel="stylesheet" type="text/css" href="<?php echo check_dir($config['web_url']);?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/common.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/swiper.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
</head>
<body id="box">
	<div class="x_main_t">
		<em<?php if($type < 1) {?> class="x_youxiao"<?php }?> onclick="location.href='<?php echo check_dir($config['web_url']);?>u/?ac=pay&type=0'">购卡记录</em>
		<em<?php if($type > 0) {?> class="x_youxiao"<?php }?> onclick="location.href='<?php echo check_dir($config['web_url']);?>u/?ac=pay&type=1'">单买活动</em>
	</div>
<?php
if($type < 1) {
?>
	<div class="f_main f_qudiao x_xiamian">
<?php
} else {
?>
    <div class="a_main_c">
<?php
}
?>
		<ul>
<?php
}
if($_GET['is_ajax'] == "true") {
	ob_start();
}
if($records) {
	foreach($records as $list) {
		if($type < 1) {
			$thiscard = $mysql_class->select_one("users_card", "*", array("uid"=>$thisuser['id'], "cid"=>$list['price_id']));
?>
			<li>
				<img src="<?php echo $thiscard['url'];?>">
				<div class="f_main_cc">
					<div class="f_main_l">
						<h3>购买价格<strong>￥<?php echo $thiscard['price'];?></strong></h3>
					</div>

				</div>
				<div class="f_main_bb">
					<div class="f_main_r">
						<span>可参加<i> <?php echo $thiscard['num'];?> </i>次活动</span>
                        <p style="position:absolute; right:10px; bottom:4px; color:#fff;"><?php echo $list['time'];?></p>
					</div>
				</div>
			</li>
<?php
		} else {
			$thisactivity = $mysql_class->select_one("activity", "*", array("id"=>$list['price_id']));
			if($thisactivity) {
?>
            <li>
                <a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $thisactivity['id'];?>"><img src="<?php echo $mysql_class->get_field_value("activity_picture", "url", array("aid"=>$thisactivity['id']), "index_order ASC");?>"></a>
                <div class="a_main_b">
                    <p><?php echo $list['title'];?></p>
                    <p><img src="<?php echo check_dir($config['web_url']);?>images/a_main3.jpg"><span><?php echo $thisactivity['address'];?></span><img src="<?php echo check_dir($config['web_url']);?>images/a_main4.jpg"><span>仅剩<?php echo $thisactivity['person_num'] - $thisactivity['in_num'];?>个名额</span><img src="<?php echo check_dir($config['web_url']);?>images/a_main5.jpg"><span><?php echo $thisactivity['hit'];?>人已关注</span></p>
                    <p><?php if($thisactivity['status'] == 0 && $thisactivity['person_num'] - $thisactivity['in_num'] > 0 && $thisactivity['date'] > date("Y-m-d")) {?><i class="a_bao">报名中</i><?php }?><?php $thistag = $mysql_class->select("tag_list", "*", array("aid"=>$thisactivity['id'])); if($thistag) {?><i><?php echo $thistag[0]['tname'];?></i><?php }?><span>&nbsp;&nbsp; <!--<?php echo $thisactivity['in_num'];?>人已报名--></span>&nbsp;&nbsp;<em style="color:gray; font-size:14px;"><?php echo substr($list['time'], 5, 11);?></em></p>
                </div>
                <div class="a_kongge"></div>
            </li>
<?php
			}
		}	
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
            <a class="loadbtn" href="javascript:" onClick="load_card();" data-page="1" data-maxpage="<?php echo $maxpage;?>">加载更多</a>
        </div>
<?php
}
?>
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
function load_card(page) {
	if($(".loadbtn").attr("loading") == 'true') {
		return ;
	}
	$(".weui_loading_toast").show();
	$(".loadbtn").html("正在加载……").attr("loading", 'true');
	var page = parseInt(page) ? parseInt(page) : parseInt($(".loadbtn").attr("data-page"))+1;
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>u/?ac=pay&is_ajax=true",
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