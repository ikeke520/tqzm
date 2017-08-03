<?php
$thisworker = $mysql_class->select_one("worker", "*", array("uid"=>$thisuser['id']));
if(empty($thisworker)) {
	exit(json_encode(array("error"=>1, "msg"=>"非业务员无权操作。")));
}
switch($_GET['do']) {
	case 'check':
		$cardno = trim($_POST['cardno']);
		$thiscard = $mysql_class->select_one("cardbase", "*", array("cardno"=>$cardno, "is_use"=>0, "is_active"=>0));
		if(empty($thiscard)) {
			exit(json_encode(array("error"=>1, "msg"=>"输入的实体卡号有误。")));
		}
		$mysql_class->update("cardbase", array("wid"=>$thisworker['id'], "worker"=>addslashes($thisworker['realname']), "is_use"=>'1', "sell_time"=>NOW), array("id"=>$thiscard['id']));
		exit(json_encode(array("error"=>0)));
	break;
}

$page = intval($_GET['page']);
$total_num = $mysql_class->num_table("cardbase", array("wid"=>$thisworker['id'], "is_use"=>1));
$per_page = 10;
$maxpage = ceil($total_num / $per_page);
$page = max(1, min($maxpage, $page));
$records = $mysql_class->select("cardbase", "*", array("wid"=>$thisworker['id'], "is_use"=>1), "id DESC", array(($page - 1)*$per_page, $per_page));
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
	<div class="f_top">
		<div class="f_top_span">
			<div class="f_top_span_l"><strong>销售录入</strong>（请录入已销售的实体卡号）</div>
		</div>
		<div class="f_top_input">
			<input type="text" name="cardno" placeholder="请输入实体卡号" class="f_top_txt">
			<a href="javascript:check_out();"><input type="button" value="确认" class="f_top_sub"></a>
		</div>	
	</div>
    <h1 style="padding:10px; background:#f1f1f1; margin-top:10px;">销售记录</h1>
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
						<i style=" float:left; width:28%;"><?php echo $list['name'];?></i>
						<i style=" float:left; width:20%;"><font color="red"><b>¥<?php echo $list['price'];?></b></font></i>
                    	<i style=" float:left; width:50%; color:gray;"><?php echo $list['sell_time'];?></i>
                        
                       </p>
                    <p style="padding:20px 0px; color:gray;">实体卡号：<?php echo $list['cardno'];?></p>
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
	<div class="f_tanc_bg dialog">
		<div class="f_tanc">
			<h2>您输入的激活码有误!</h2>
			<a href="javascript:closeBg();" class="f_hide">确定</a>
		</div>
	</div>
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
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/phone.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/swiper.min.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/qiu_phone.js"></script>
<script type="text/javascript">
var is_sending = false;
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
		url:"<?php echo check_dir($config['web_url']);?>u/?ac=import&is_ajax=true",
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
function check_out() {
	var cardno = $("input[name=cardno]").val();
	if(empty(cardno)) {
		$(".f_tanc h2").html("请填写实体卡后再试。");
		showBg();
		return false;
	}
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>u/?ac=import&do=check",
		type:"POST",
		data:{"cardno": cardno},
		dataType:"json",
		success: function(data){
			if(data.error > 0) {
				$(".f_tanc h2").html(data.msg);
				showBg();
			} else {
				$(".f_tanc h2").html("恭喜您，录入成功！");
				showBg();
				setTimeout(function() {
					location.reload();
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