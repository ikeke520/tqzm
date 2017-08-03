<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");
$thisuser = get_user();
$wxjsapi = get_wxsign();
$thisactivity = $mysql_class->select_one("activity", "*", array("id"=>intval($_GET['id']), "is_complete"=>'1'));

if(empty($thisactivity)) {
	header("Location: ". check_dir($config['web_url']));
	exit;
}
if($_GET['ac'] == "post") {
	$data['aid'] = $thisactivity['id'];
	$data['uid'] = $thisuser['id'];
	$data['uname'] = add_slashes($thisuser['nickname']);
	$data['content'] = htmlspecialchars(trim($_POST['content']));
	$data['time'] = NOW;
	$data['is_hide'] = '1';
	if(empty($data['content'])) {
		exit(json_encode(array("error"=>0, "msg"=>"评加内容不能为空。")));
	}
	$mysql_class->insert("comment", $data);
	exit(json_encode(array("error"=>0)));
} else if($_GET['ac'] == "fav") {
	if($mysql_class->num_table("users_fav", array("uid"=>$thisuser['id'], "aid"=>$thisactivity['id']))) {
		$mysql_class->delete("users_fav", array("uid"=>$thisuser['id'], "aid"=>$thisactivity['id']));
		exit(json_encode(array("error"=>0, "type"=>0)));
	} else {
		$mysql_class->insert("users_fav", array("uid"=>$thisuser['id'], "aid"=>$thisactivity['id']));
		exit(json_encode(array("error"=>0, "type"=>1)));
	}
}

$rmdactivity = $mysql_class->fetch_all("SELECT * FROM {$config['db_prefix']}activity WHERE id != '{$thisactivity['id']}' AND id IN (SELECT aid FROM {$config['db_prefix']}tag_list WHERE tid IN (SELECT tid FROM {$config['db_prefix']}tag_list WHERE aid='{$thisactivity['id']}')) AND is_complete='1' ORDER BY is_rmd DESC, id DESC LIMIT 0, 1");

$page = intval($_GET['page']);
$where = "aid = '{$thisactivity['id']}' AND is_hide='0'";
$total_num = $mysql_class->num_table("comment", $where);
$per_page = 5;
$maxpage = ceil($total_num / $per_page);
$page = max(1, min($maxpage, $page));
$commentlist = $mysql_class->select("comment", "*", $where, "id DESC", array(($page - 1)*$per_page, $per_page));
if($_GET['is_ajax'] != "true") {
$mysql_class->update("activity", "hit=hit+1", array("id"=>$thisactivity['id']));
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title><?php echo $thisactivity['title'];?>__<?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/common.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/swiper.css" />
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
</head>
<body id="box">
	<div class="b_top">
		<b onclick="javascript:history.back(-1);"><img src="<?php echo check_dir($config['web_url']);?>images/a_fanghui.png">返回</b>
		<h2>活动详情</h2>
		<span onClick="fav();">
<?php
if($mysql_class->num_table("users_fav", array("uid"=>$thisuser['id'], "aid"=>$thisactivity['id'])) < 1) {
?>
			<img src="<?php echo check_dir($config['web_url']);?>images/a_xin1.png">
<?php
} else {
?>
			<img src="<?php echo check_dir($config['web_url']);?>images/b_xin.png">
<?php
}
?>
		</span>
	</div>


	<div class="page_main">
		<div class="swiper-container">   
		    <ul class="swiper-wrapper">
<?php
$piclist = $mysql_class->select("activity_picture", "*", array("aid"=>$thisactivity['id']), "index_order ASC");
if($piclist) {
	foreach($piclist as $list) {
?>
			    <li class="swiper-slide">
			        <img src="<?php echo $list['url'];?>">
			        
			    </li>
<?php
	}
}
?>
			</ul>
		    <div class="swiper-pagination"></div>
		</div>
	</div>

	<div class="a_main_c">
			<ul>
				<li>
					
					<div class="a_main_b">
						<p><?php echo $thisactivity['title'];?></p>
						<p><img src="<?php echo check_dir($config['web_url']);?>images/a_main3.jpg"><span><?php echo $thisactivity['address'];?></span><img src="<?php echo check_dir($config['web_url']);?>images/a_main4.jpg"><span>仅剩<?php echo $thisactivity['person_num'] - $thisactivity['in_num']; ?>个名额</span><img src="<?php echo check_dir($config['web_url']);?>images/a_main5.jpg"><span><?php echo $thisactivity['hit'];?>人已关注</span></p>
						<p><?php if($thisactivity['status'] == 0 && ($thisactivity['person_num'] - $thisactivity['in_num'] > 0 && $thisactivity['person_num'] > 0) && $thisactivity['date'] > date("Y-m-d")) {?><i class="a_bao">报名中</i><?php }?><?php $thistag = $mysql_class->select("tag_list", "*", array("aid"=>$thisactivity['id'])); if($thistag) {?><i><?php echo $thistag[0]['tname'];?></i><?php }?><span>&nbsp;&nbsp; <?php echo $thisactivity['year_duration'];?>&nbsp;&nbsp; <!--<?php echo $thisactivity['in_num'];?>人已参与--></span><?php if($thisactivity['status'] == 0 && $thisactivity['person_num'] - $thisactivity['in_num'] > 0 && $thisactivity['date'] > date("Y-m-d")) {?><a href="<?php echo check_dir($config['web_url']);?>b/?id=<?php echo $thisactivity['id'];?>"><u>我要报名</u></a><?php } else {?><u>报名截止</u><?php }?></p>
					</div>
					<div class="a_kongge"></div>
				</li>
			</ul>
	</div>
	<div class="b_tit">
		<ul>
			<li><img src="<?php echo check_dir($config['web_url']);?>images/b_tubiao1.jpg"><span>活动类型：<strong><?php echo $thisactivity['cname'];?></strong></span></li>
			<li><img src="<?php echo check_dir($config['web_url']);?>images/b_tubiao2.jpg"><span>适合年龄：<strong><?php echo $thisactivity['year_duration'];?></strong></span></li>
			<li><img src="<?php echo check_dir($config['web_url']);?>images/b_tubiao3.jpg"><span>场地类型：<strong><?php echo $thisactivity['activity_type'];?></strong></span></li>
			<li><img src="<?php echo check_dir($config['web_url']);?>images/b_tubiao4.jpg"><span>大人陪同：<strong><?php if($thisactivity['adult_along']) {?>是<?php } else {?>否<?php }?></strong></span></li>
			<li><img src="<?php echo check_dir($config['web_url']);?>images/b_tubiao5.jpg"><span>单次卡价格：<strong><?php echo $thisactivity['price'];?>元</strong></span></li>
			<li><img src="<?php echo check_dir($config['web_url']);?>images/b_tubiao6.jpg"><span>小孩报名限制：<strong>每场最多<?php echo $thisactivity['person_num'];?>个小孩报名</strong></span></li>
		</ul>
	</div>
	<div class="a_kongge"></div>
	<div class="b_tit b_tit1">
		<ul>
			<li><img src="<?php echo check_dir($config['web_url']);?>images/b_tubiao7.jpg"><span>童趣卡使用说明：<strong>每名小孩扣卡1次，大人不扣</strong></span></li>
			<li><img src="<?php echo check_dir($config['web_url']);?>images/b_tubiao8.jpg"><span>活动时间：<strong><?php echo $thisactivity['date'];?>（<?php echo $thisactivity['timestr'];?>）</strong></span></li>			
		</ul>
	</div>
	<div class="b_tit b_tit1 b_tit2">
		<ul>
			<li<?php if($thisactivity['nav_url']){?> onClick="location.href='<?php echo $thisactivity['nav_url'];?>';"<?php }?>><img src="<?php echo check_dir($config['web_url']);?>images/b_tubiao9.jpg"><span>活动地点：<strong><?php echo $thisactivity['address'];?></strong></span><i><img src="<?php echo check_dir($config['web_url']);?>images/b_zuo.jpg"></i></li>	
		</ul>
	</div>
	<div class="a_kongge b_kongge"></div>
	<div class="b_main">
		<div class="b_main_t">
			<ul>
				<li class="b_main_tuwen" data-var="info"><a href="javascript:">图文详情</a></li>
				<li data-var="notice"><a href="javascript:">报名须知</a></li>
				<li data-var="comment"><a href="javascript:">评价</a></li>
			</ul>
		</div>
		<div class="b_main_c" id="continfo">
			<div style="padding:10px; line-height: 2rem;font-size: 1.1rem;"><?php echo $thisactivity['intro'];?></div>
		</div>
		<div class="b_main_c" id="contnotice">
			<div style="padding:10px; line-height: 2rem;font-size: 1.1rem;"><?php echo nl2br($thisactivity['notice']);?></div>
		</div>
        <div class="b_main_c c_main_d" id="contcomment">
<?php
if($commentlist) {
?>
			<ul>
<?php
}
}
if($_GET['is_ajax'] == "true") {
	ob_start();
}
if($commentlist) {
	foreach($commentlist as $list) {
?>
				<li>
					<p><img src="<?php echo $mysql_class->get_field_value("users", "headimgurl", array("id"=>$list['uid']));?>"><i><?php echo $list['uname'];?></i><span><?php echo substr($list['time'], 0, 10);?></span></p>
					<p><?php echo nl2br($list['content']);?></p>
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
<?php
if($commentlist){
?>
			</ul>
            <div class="loader">
                <a class="loadbtn" href="javascript:" onClick="load_comment();" data-page="1" data-maxpage="<?php echo $maxpage;?>">加载更多</a>
            </div>
<?php
} else {
?>
            <div class="c_main_c">
                <img src="<?php echo check_dir($config['web_url']);?>images/c_img.jpg">
                <p>还没有人评价哦~</p>
            </div>
<?php
}
?>
        	<div style="background:#d7d7d7; height:1px; margin:10px 0;"></div>
            <div style="padding:10px; line-height: 2rem;font-size: 1.1rem;">
            	<textarea name="content" style="width:94%; margin:0 2%; padding:1%; background:#f1f1f1; border-radius:4px; height:75px; line-height:25px; font-size:14px;" placeholder="我要评价……"></textarea>
                <div class="weui_btn_area"><a class="weui_btn weui_btn_default" href="javascript:" onClick="post();">发表评价</a> </div>
            </div>
        </div>
	</div>
	<div class="a_kongge"></div>
	<div class="a_main">
		<div class="a_main_hot b_main_hot">
			
			<p>
				<i></i>
				<span>相关推荐</span>
				<i></i>
			</p>
			
		</div>
		<div class="a_main_c">
			<ul>
<?php
if($rmdactivity) {
	foreach($rmdactivity as $list) {
?>
				<li>
					<a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $list['id'];?>"><img src="<?php echo $mysql_class->get_field_value("activity_picture", "url", array("aid"=>$list['id']), "index_order ASC");?>"></a>
					<div class="a_main_b">
						<p><a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $list['id'];?>"><?php echo $list['title'];?></a></p>
						<p><img src="<?php echo check_dir($config['web_url']);?>images/a_main3.jpg"><span><?php echo $list['address'];?></span><img src="<?php echo check_dir($config['web_url']);?>images/a_main4.jpg"><span>仅剩<?php echo $list['person_num'] - $list['in_num'];?>个名额</span><img src="<?php echo check_dir($config['web_url']);?>images/a_main5.jpg"><span><?php echo $list['hit'];?>人已关注</span></p>
						<p><?php if($list['status'] == 0 && ($list['person_num'] - $list['in_num'] > 0 && $list['person_num'] > 0) && $list['date'] > date("Y-m-d")) {?><i class="a_bao">报名中</i><?php }?><?php $thistag = $mysql_class->select("tag_list", "*", array("aid"=>$list['id'])); if($thistag) {?><i><?php echo $thistag[0]['tname'];?></i><?php }?><span>&nbsp;&nbsp; <?php echo $list['year_duration'];?>&nbsp;&nbsp; <?php echo $list['in_num'];?>人已参与</span><?php if($list['status'] == 0 && $list['person_num'] - $list['in_num'] > 0 && $list['date'] > date("Y-m-d")) {?><a href="<?php echo check_dir($config['web_url']);?>a/?id=<?php echo $list['id'];?>"><u>我要报名</u></a><?php } else {?><u>报名截止</u><?php }?></p>
					</div>
					<div class="a_kongge"></div>
				</li>
<?php
	}
}
?>
			</ul>
		</div>	
	</div>
	<div class="a_kongge"></div>
	<div class="b_footer">
		<ul>
			<li><a href="<?php echo check_dir($config['web_url']);?>i/?ac=link"><img src="<?php echo check_dir($config['web_url']);?>images/b_kefu.jpg"><span>客服</span></a></li>
<?php
if($thisactivity['payway'] > 0) {
?>
			<li style="width:75% !important;"><a href="<?php echo check_dir($config['web_url']);?>b/?id=<?php echo $thisactivity['id'];?>&payway=1"><span>单次购买本次活动</span></a></li>
<?php
} else {
?>
			<li><a href="javascript:showBg();" onClick="return checkout();"><span>单次购买本次活动</span></a></li>
<?php
$hascard = $mysql_class->num_table("users_card", array("uid"=>$thisuser['id'], "is_finish"=>0));
if($hascard > 0) {
?>
			<li><a href="<?php echo check_dir($config['web_url']);?>b/?id=<?php echo $thisactivity['id'];?>" onClick="return checkout();"><span>立即报名</span></a></li>
<?php
} else {
?>
			<li><a href="<?php echo check_dir($config['web_url']);?>c/"><span>购买童趣卡</span></a></li>
<?php
}
?>
<?php
}
?>
		</ul>
	</div>
	<div class="d_tanc_bg dialog">
		<div class="d_tanc">
			<h2>购买童趣卡享受更多优惠</h2>
			<a href="<?php echo check_dir($config['web_url']);?>b/?id=<?php echo $thisactivity['id'];?>&payway=1">确定单次购买</a>
			<a href="<?php echo check_dir($config['web_url']);?>c/">购买童趣卡</a>
			<a href="javascript:closeBg();" class="d_hide"></a>
		</div>
	</div>
	<div class="k_tanc_bg dialog1" style="top:30%;">
		<div class="k_tanc">
			<h2>该活动报名已经截止。</h2>
			
			<a href="javascript:closeBg1();" class="d_hide"><span></span></a>
		</div>
		<div class="k_wenben1">
			<a href="javascript:" class="d_hide"><input type="button" onClick="closeBg1();" value="确定" class="k_tijiao"></a>
		</div>
	</div>
	<div class="k_tanc_bg dialog2" style="top:30%;">
		<div class="k_tanc">
			<h2>您的评论已提交，稍后将显示出来。</h2>
			
			<a href="javascript:closeBg2();" class="d_hide"><span></span></a>
		</div>
		<div class="k_wenben1">
			<a href="javascript:" class="d_hide"><input type="button" onClick="closeBg2();" value="确定" class="k_tijiao"></a>
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
	var swiper = new Swiper('.swiper-container', {
		pagination: '.swiper-pagination',
		paginationClickable: true,
		autoplay: 2500,
		autoplayDisableOnInteraction: false
	});
	init_tab();
	$(".b_main_t ul li").click(function() {
		var that = this;
		$(".b_main_t ul li").removeClass("b_main_tuwen");
		$(that).addClass("b_main_tuwen");
		init_tab();
	});
	if("<?php echo $thisuser['is_reg'];?>" === "0") {
		$(".weui_dialog_alert").show();
	}
});
function init_tab() {
	var handle = $(".b_main_t ul li.b_main_tuwen").data("var");
	$(".b_main_c").hide();
	$("#cont"+handle).show();
}
function post() {
	if($("textarea[name=content]").val() == "") {
		$("textarea[name=content]").focus();
		return false;
	} else {
		if(_is_posting == false) {
			_is_posting = true;
			$.ajax({
				url:"<?php echo check_dir($config['web_url']);?>a/?ac=post&id=<?php echo $thisactivity['id'];?>",
				data:{"content":$("textarea[name=content]").val()},
				type:"POST",
				dataType:"json",
				success: function(data) {
					if(data.error > 0) {
						alert(data.msg);
					} else {
						showBg2();
						load_comment(1);
						$("textarea[name=content]").val("");
						_is_posting = false;
					}
				}
			});
		}
	}
}
function load_comment(page) {
	if($(".loadbtn").attr("loading") == 'true') {
		return ;
	}
	$(".weui_loading_toast").show();
	$(".loadbtn").html("正在加载……").attr("loading", 'true');
	var page = parseInt(page) ? parseInt(page) : parseInt($(".loadbtn").attr("data-page"))+1;
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>a/?is_ajax=true",
		type:"GET",
		data:{"id":"<?php echo intval($thisactivity['id']);?>", "page":page},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				if(page == 1) {
					$(".c_main_d ul").html(data.html);
				} else if( page <= data.maxpage) {
					$(".c_main_d ul").append(data.html);
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
function fav() {
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>a/?ac=fav&id=<?php echo $thisactivity['id'];?>",
		type:"GET",
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				if(data.type == 0) {
					$(".showmsg .weui_toast_content").html("已移除收藏");
					$(".showmsg").show();
					$(".b_top span img").attr("src","<?php echo check_dir($config['web_url']);?>images/a_xin1.png");
				} else {
					$(".showmsg .weui_toast_content").html("已收藏");
					$(".showmsg").show();
					$(".b_top span img").attr("src","<?php echo check_dir($config['web_url']);?>images/b_xin.png");
				}
				setTimeout(function(){$(".showmsg").hide();}, 1003);
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
function showBg2() { 
	var bh = Math.max($("body").height(), $(window).height()); 
	var bw = Math.max($("body").width(), $(window).width()); 
	$(".mask").css({ 
		height:bh, 
		width:bw, 
		display:"block" 
	}); 
	$(".dialog2").show(); 
} 
function closeBg2() { 
	$(".mask,.dialog2").hide(); 
}
function checkout() {
<?php
if($thisactivity['status'] == 0 && $thisactivity['person_num'] - $thisactivity['in_num'] > 0 && $thisactivity['date'] > date("Y-m-d")) {
?>
	return true;
<?php
} else {
?>
	showBg1();
	return false;
<?php
}
?>
}
</script>
</html>