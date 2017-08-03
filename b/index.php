<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");
define("IN_ACTIVITY_TPLID", "45-0332gyZqGb0tdZ_hbXuZ3OhS0A7xU_BJG_6HU_Po");
$thisuser = get_user();
$wxjsapi = get_wxsign();
$thisactivity = $mysql_class->select_one("activity", "*", array("id"=>intval($_GET['id']), "is_complete"=>'1'));
if(empty($thisactivity)) {
	header("Location: ". check_dir($config['web_url']));
	exit;
}
if($thisactivity['status'] > 0 || date("Y-m-d") >= $thisactivity['date'] || $thisactivity['in_num'] >= $thisactivity['person_num']) {
	header("Location: ". check_dir($config['web_url'])."a/?id=".$thisactivity['id']);
	exit;
}

$thisauth = $mysql_class->select_one("users_auth", "*", array("uid"=>$thisuser['id'], "aid"=>$thisactivity['id'], "is_finish"=>0));
if($thisauth) {
	$thischild = $mysql_class->fetch($mysql_class->query("SELECT * FROM {$config['db_prefix']}users_person AS p LEFT JOIN {$config['db_prefix']}users_auth_person AS a ON p.id=a.pid WHERE a.aid={$thisauth['id']} AND a.is_adult='0'"));
	$thisadult = $mysql_class->fetch($mysql_class->query("SELECT * FROM {$config['db_prefix']}users_person AS p LEFT JOIN {$config['db_prefix']}users_auth_person AS a ON p.id=a.pid WHERE a.aid={$thisauth['id']} AND a.is_adult='1'"));
}
$payway = intval($_GET['payway']);
switch($_GET['ac']) {
	case 'post_person':
		$data['uid'] = $thisuser['id'];
		$data['is_adult'] = intval($_POST['is_adult']);
		$data['name'] = htmlspecialchars($_POST['name']);
		$data['sex'] = intval($_POST['sex']);
		$data['birthday'] = htmlspecialchars($_POST['birthday']);
		$data['idno'] = htmlspecialchars($_POST['idno']);
		$data['phone'] = htmlspecialchars($_POST['phone']);
		$data['address'] = htmlspecialchars($_POST['address']);
		if(empty($data['name'])) {
			exit(json_encode(array("error"=>1, "msg"=>"姓名不能为空。")));
		}
		if($data['is_adult'] > 0) {
			if(empty($data['phone'])) {
				exit(json_encode(array("error"=>1, "msg"=>"家长手机号码不能为空。")));
			}
		} else {
			if($data['birthday'] && !preg_match("/^20\d{2}-\d{1,2}-\d{1,2}$/", $data['birthday'])) {
				exit(json_encode(array("error"=>1, "msg"=>"宝宝生日格式不正确：2010-05-01")));
			}
		}
		$mysql_class->insert("users_person", $data);
		$data['id'] = $mysql_class->insert_id();
		exit(json_encode(array("error"=>0, "data"=>$data)));
	break;
	case 'init':
		if($thisactivity['status'] == 1 || $thisactivity['person_num'] - $thisactivity['in_num'] < 1 && $thisactivity['date'] <= date("Y-m-d")) {
			exit(json_encode(array("error"=>1, "msg"=>"报名已满或已超过报名截止时间，请您报名其他活动。")));
		}
		$child_person = $mysql_class->select_one("users_person", "*", array("id"=>intval($_POST['child_person']), "is_adult"=>0, "uid"=>$thisuser['id']));
		if(empty($child_person)) {
			exit(json_encode(array("error"=>1, "msg"=>"请选择一名小孩。")));
		}
		$tmpauth = $mysql_class->select("users_auth", "id", array("uid"=>$thisuser['id'], "aid"=>$thisactivity['id'], "is_finish"=>1, "is_cancel"=>0));
		if($mysql_class->num_table("users_auth_person", "aid IN ('".implode("','", get_id_array($tmpauth))."') AND pid='{$child_person['id']}'")) {
			exit(json_encode(array("error"=>1, "msg"=>"该小朋友已经报名了，请不要重复报名。")));
		}
		$adult_person = $mysql_class->select_one("users_person", "*", array("id"=>intval($_POST['adult_person']), "is_adult"=>1, "uid"=>$thisuser['id']));
		if(empty($adult_person) && $thisactivity['adult_along']) {
			exit(json_encode(array("error"=>1, "msg"=>"请选择家长。")));
		}
		if($thisauth) {
			$thisid = $thisauth['id'];
		} else {
			$mysql_class->insert("users_auth", array("aid"=>$thisactivity['id'], "uid"=>$thisuser['id']));
			$thisid = $mysql_class->insert_id();
		}
		if($thischild) {
			$mysql_class->update("users_auth_person", array("pid"=>$child_person['id']), array("aid"=>$thisid, "is_adult"=>0));
		} else {
			$mysql_class->insert("users_auth_person", array("aid"=>$thisid, "pid"=>$child_person['id'], "is_adult"=>0));
		}
		if($thisauth) {
			$mysql_class->update("users_auth_person", array("pid"=>$adult_person['id']), array("aid"=>$thisid, "is_adult"=>1));
		} else {
			$mysql_class->insert("users_auth_person", array("aid"=>$thisid, "pid"=>$adult_person['id'], "is_adult"=>1));
		}
		// 验证是否需要支付
		if($thisactivity['price'] == 0) {
			$updatedata = "in_num=in_num+1";
			$mysql_class->update("users_auth", array("is_finish"=>1, "time"=>NOW), array("id"=>$thisid));
			$mysql_class->update("activity", $updatedata, array("id"=>$thisactivity['id']));
		}
		exit(json_encode(array("error"=>0, "id"=>$thisid)));		
	break;
	case 'phone':
		include("./phone.php");
		exit;
	break;
	case 'send':
		$phone = trim($_POST['phone']);
		if($phone == $thisuser['phone']) {
			exit(json_encode(array("error"=>1, "msg"=>"您已经绑定该号码。")));
		}
		if(preg_match("/^1\d{10}$/", $phone)) {
			$sms_class = load_class("sms");
			$hash = rand(100000, 999999);
			$content = "您的短信验证码是：{$hash}，请勿将验证码提供给他人，有效期30分钟。";
			$response = $sms_class->sendSMS($phone, $content, "676767");
			if(substr($response, 0, 2) == "1,") {
				$mysql_class->update("users", array("phone_hash"=>md5($phone.$hash), "phone_time"=>NOW), array("id"=>$thisuser['id']));
				exit(json_encode(array("error"=>0)));
			} else {
				exit(json_encode(array("error"=>1, "msg"=>"错误代码：".$response)));
			}
		} else {
			exit(json_encode(array("error"=>1, "msg"=>"手机号码不合法")));
		}
	break;
	case 'verify':
		$phone = trim($_POST['phone']);
		$hash = trim($_POST['hash']);
		if(preg_match("/^1\d{10}$/", $phone) && preg_match("/^\d{6}$/", $hash)) {
			if($thisuser['phone_hash'] == md5($phone.$hash) && strtotime($thisuser['phone_time']) > strtotime("-30 minutes")) {
				$mysql_class->update("users", array("phone"=>$phone, "phone_hash"=>"", "phone_time"=>""), array("id"=>$thisuser['id']));
				exit(json_encode(array("error"=>0)));
			} else {
				exit(json_encode(array("error"=>1, "msg"=>"手机号码或验证码不正确")));
			}
		} else {
			exit(json_encode(array("error"=>1, "msg"=>"手机号码或验证码不正确")));
		}
		
	break;
	case 'selectcard':
		include("./card.php");
		exit;
	break;
	case 'apply':
		if($thisactivity['status'] == 1 ||  $thisactivity['person_num'] - $thisactivity['in_num'] < 1) {
			exit(json_encode(array("error"=>1, "msg"=>"报名已满，请您报名其他活动。")));
		}
		$thiscard = $mysql_class->select_one("users_card", "*", array("id"=>intval($_POST['card']), "uid"=>$thisuser['id'], "is_finish"=>0));
		if(empty($thiscard)) {
			exit(json_encode(array("error"=>1, "msg"=>"请选择有效的亲子卡。")));
		}
		if($thisauth['is_finish']) {
			exit(json_encode(array("error"=>1, "msg"=>"已报名成功，请勿重复操作。")));
		}
		$data = "cost_num=cost_num+1";
		if($thiscard['cost_num'] + 1 == $thiscard['num']) {
			$data .= ", is_finish='1'";
		}
		$mysql_class->update("users_card", $data, array("id"=>$thiscard['id']));
		$mysql_class->update("activity", "in_num=in_num+1", array("id"=>$thisactivity['id']));
		$mysql_class->update("users_auth", "is_finish='1', cid='{$thiscard['id']}', time='".NOW."'", array("id"=>$thisauth['id']));
		// 2017-05-07加模板消息及短信通知
		$notemsg = array(
			"first"=>array("value"=>urlencode("您已成功报名如下活动："), "color"=>"#173177"),
			"keyword1"=>array("value"=>urlencode($thisactivity['title']), "color"=>"#173177"),
			"keyword2"=>array("value"=>urlencode($thisactivity['date']."（".$thisactivity['timestr']."）"), "color"=>"#173177"),
			"keyword3"=>array("value"=>urlencode($thisactivity['address']), "color"=>"#173177"),
			"remark"=>array("value"=>urlencode("请调整好时间、状态，准时参加。"), "color"=>"#173177"),
		);
		$noteurl = check_dir($config['web_url'])."u/?ac=activity";
		$notequery = urldecode(json_encode(array("touser"=>$thisuser['openid'], "template_id"=>IN_ACTIVITY_TPLID, "url"=>$noteurl, "data"=>$notemsg)));
		http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $notequery);
		$sms_class = load_class("sms");
		$content = "温馨提示：您已提交“".$thisactivity['title']."”报名预约，请加客服微信号：18229850661，及时了解活动成行动态。";
		$sms_class->sendSMS($thisuser['phone'], $content, "170519");
		exit(json_encode(array("error"=>0)));
	break;
	case 'refund':
		$thisauth = $mysql_class->select_one("users_auth", "*", array("id"=>intval($_GET['aid']), "uid"=>$thisuser['id'], "aid"=>$thisactivity['id']));
		if(empty($thisauth)) {
			exit(json_encode(array("error"=>1, "msg"=>"未找到制定的订单。")));
		}
		if($thisauth['is_cancel'] == '0') {
			if($thisauth['cid'] > 0) {
				$thiscard = $mysql_class->select_one("users_card", "*", array("id"=>$thisauth['cid']));
				if($thiscard) {
					$data = "cost_num=cost_num-1";
					if($thiscard['is_finish']) {
						$data .= ", is_finish='0'";
					}
					$mysql_class->update("users_card", $data, array("id"=>$thiscard['id']));
				}
			} else if($thisauth['tradeno']) {
				pay_refund($thisauth['tradeno']);
			}
			$mysql_class->update("users_auth", "is_cancel='1'", array("id"=>$thisauth['id']));
			$mysql_class->update("activity", "in_num=in_num-1", array("id"=>$thisactivity['id']));
			//20170512
			$sms_class = load_class("sms");
			if($thisauth['cid'] > 0) {
				$content = "您已取消『".$thisactivity['title']."』活动报名，活动次数将返还至您卡内。欢迎选择其他精彩好玩的亲子体验！";
			} else if($thisauth['tradeno']) {
				$content = "您已取消『".$thisactivity['title']."』活动报名，报名费用将原路返还至您的付款账户。欢迎选择其他精彩好玩的亲子体验！";
			} else {
				$content = "您已取消『".$thisactivity['title']."』活动报名，活动次数将返还至您卡内。欢迎选择其他精彩好玩的亲子体验！";
			}
			$sms_class->sendSMS($thisuser['phone'], $content, "170519");
		}
		exit(json_encode(array("error"=>0)));
	break;
}
if($payway < 1 && $mysql_class->num_table("users_card", array("uid"=>$thisuser['id'], "is_finish"=>0)) < 1) {
	header("Location: ".check_dir($config['web_url'])."c/");
	exit;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;">
<title>报名参加__<?php echo $thisactivity['title'];?>__<?php echo $config['seo_title'];?></title>
<meta name="keywords" content="<?php echo $config['seo_keyword'];?>">
<meta name="description" content="<?php echo $config['seo_desc'];?>">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/style.css">
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
</head>
<body id="box">
	<div class="i_top">
		<div class="i_jindu">
			<img src="<?php echo check_dir($config['web_url']);?>images/i_tu5.png">
			<img src="<?php echo check_dir($config['web_url']);?>images/i_next.jpg">
			<img src="<?php echo check_dir($config['web_url']);?>images/i_tu1.png">
			<img src="<?php echo check_dir($config['web_url']);?>images/i_next.jpg">
			<img src="<?php echo check_dir($config['web_url']);?>images/i_tu3.png">
		</div>
		<ul>
			<li>选场次人员</li>
			<li>选卡/支付</li>
			<li>完成</li>
		</ul>
	</div>

	<div class="a_kongge b_kongge"></div>

	<div class="i_main">
		<img src="<?php echo $mysql_class->get_field_value("activity_picture", "url", array("aid"=>$thisactivity['id']), "index_order ASC");?>">
		<p><?php echo cn_substr($thisactivity['title'], 12);?></p>
		<span>活动时间：<?php echo $thisactivity['date'];?>（<?php echo $thisactivity['timestr'];?>）</span>
	</div>

	<div class="a_kongge b_kongge"></div>

	<div class="k_ka f_main_r f_main_r1">
		<h4>选择活动场次</h4>
		<ul>
			<li>
				<p><?php echo $thisactivity['date'];?></p>
				<p>（<?php echo $thisactivity['timestr'];?>）</p>
				<b><img src="<?php echo check_dir($config['web_url']);?>images/k_gou2.jpg"></b>
			</li>
		</ul>
	</div>
	
	<div class="a_kongge"></div>

	<div class="k_main">
		<div class="k_main_t">
			<h4>添加人数</h4>
			<p>本次活动适合 <?php echo $thisactivity['year_duration'];?> 儿童参加，每名儿童占用1次，每名家长占用0次。活动报名采取实名制，请仔细核对姓名。</p>
		</div>
		<div class="k_main_c">
			<ul>
				<li>
					<p class="childcont"><span>小孩</span><br>
<?php
$child = $mysql_class->select("users_person", "*", array("uid"=>$thisuser['id'], "is_adult"=>"0"));
if($child) {
	foreach($child as $list) {
?>
                    	<label><input type="radio" name="child_person" value="<?php echo $list['id'];?>"><?php echo $list['name'];?></label>
<?php
	}
}
?>
                    </p>
					<a href="javascript:showBg();">手动添加一位</a>
				</li>
				<li>
					<p class="adultcont"><span>大人</span><br>
<?php
$child = $mysql_class->select("users_person", "*", array("uid"=>$thisuser['id'], "is_adult"=>"1"));
if($child) {
	foreach($child as $list) {
?>
                    	<label><input type="radio" name="adult_person" value="<?php echo $list['id'];?>"><?php echo $list['name'];?></label>
<?php
	}
}
?>
                    </p>
					<a href="javascript:showBg1();">手动添加一位</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="b_footer h_footer k_footer">
		<ul>
			<li><a href="javascript:" onClick="history.back();"><span>返回上一页</span></a></li>
			<li><a href="javascript:" onClick="next();"><span>下一步</span></a></li>
		</ul>
	</div>

	<div class="k_tanc_bg dialog">
		<div class="k_tanc">
			<h2>购买童趣卡享受更多优惠</h2>
			
			<a href="javascript:closeBg();" class="d_hide"><span></span></a>
		</div>
		<div class="k_wenben">
			<div class="k_wenben_fb">
				<p><label><input type="radio" name="child_sex" value="0" checked>男宝</label></p>
				<p><label><input type="radio" name="child_sex" value="1" class="k_nvbao">女宝</label></p>
			</div>	
		</div>
		<div class="k_wenben1">
			<ul>
				<li>
					<span><i>*</i>宝宝姓名：</span>
					<input type="text" name="child_name" placeholder="请输入宝宝姓名">
				</li>
				<li>
					<span><i></i>宝宝生日：</span>
					<input type="text" name="child_birthday" placeholder="2010-05-01">
				</li>
				<li>
					<span><i></i>所在区域：</span>
					<input type="text" name="child_address" placeholder="如四方坪、伍家岭等">
				</li>
				<li>
					<span><i></i>身份证号：</span>
					<input type="text" name="child_idno" placeholder="请填写宝宝身份证号码">
				</li>
				<li>
					<p style="width:100%;">填写身份证号则购买保险（保险免费）</p>
				</li>
			</ul>
			<a href="javascript:"><input type="button" onClick="post_child();" value="添加" class="k_tijiao"></a>
		</div>
	</div>

	<div class="k_tanc_bg dialog1">
		<div class="k_tanc">
			<h2>购买童趣卡享受更多优惠</h2>
			
			<a href="javascript:closeBg1();" class="d_hide"><span></span></a>
		</div>
		<div class="k_wenben">
			<div class="k_wenben_fb">
				<p><label><input type="radio" name="adult_sex" value="1" checked>辣妈</label></p>
				<p><label><input type="radio" name="adult_sex" value="0" class="k_nvbao">帅爸</label></p>
			</div>	
		</div>
		<div class="k_wenben1">
			<ul>
				<li>
					<span><i>*</i>家长姓名：</span>
					<input type="text" name="adult_name" placeholder="请输入家长姓名">
				</li>
				<li>
					<span><i>*</i>联系电话：</span>
					<input type="text" maxlength="11" name="adult_phone" placeholder="请输入家长手机号码">
				</li>
				<li>
					<span><i></i>所在区域：</span>
					<input type="text" name="adult_address" placeholder="如四方坪、伍家岭等">
				</li>
				<li>
					<span><i></i>身份证号：</span>
					<input type="text" name="adult_idno" placeholder="请填写大人身份证号码">
				</li>
				<li>
					<p style="width:100%;">填写身份证号则购买保险（保险免费）</p>
				</li>
			</ul>
			<a href="javascript:" class="d_hide"><input type="button" onClick="post_adult();" value="添加" class="k_tijiao"></a>
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
    <div class="hidden"><img src="<?php echo check_dir($config['web_url']);?>images/k_gou2.jpg"><img src="<?php echo check_dir($config['web_url']);?>images/share.jpg"></div>
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
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/common.js"></script>
<script type="text/javascript">
var WEB_URL = "<?php echo check_dir($config['web_url']);?>";
var _is_requesting = false;
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
<?php
if($thisauth) {
?>
	init_radio("child_person", "<?php echo $thischild['pid'];?>");
	init_radio("adult_person", "<?php echo $thisadult['pid'];?>");
<?php
}
?>
	if("<?php echo $thisuser['is_reg'];?>" === "0") {
		$(".weui_dialog_alert").show();
	}
});
function next() {
	var child_person = get_radio_value("child_person");
	var adult_person = get_radio_value("adult_person");
	if(child_person == "") {
		alert("请选择一名儿童。");
		return false;
	}
	if(adult_person == "" && "<?php echo $thisactivity['adult_along'];?>" == "1") {
		alert("请选择一名家长。");
		return false;
	}
	$(".weui_loading_toast").show();
	if(_is_requesting) {
		return false;
	} else {
		_is_requesting = true;
	}
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>b/?ac=init&id=<?php echo $thisactivity['id'];?>",
		type:"POST",
		data:{"child_person":child_person, "adult_person":adult_person},
		dataType:"json",
		success: function(data) {
			$(".weui_loading_toast").hide();
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$(".showmsg .weui_toast_content").html("正在处理…");
				$(".showmsg").show();
				setTimeout(function(){location.href="<?php echo check_dir($config['web_url']);?>b/?ac=phone&id=<?php echo $thisactivity['id'];?>&payway=<?php echo max($payway, $thisactivity['payway']);?>&aid="+data.id}, 1003);
			}
			_is_requesting = false;
		}
	});
}
function post_child() {
	if(empty($("input[name=child_name]").val())) {
		alert("宝宝姓名不能为空。");
		return false;
	}
	if(!empty($("input[name=child_birthday]").val())) {
		if(!/^20\d{2}-\d{1,2}-\d{1,2}$/.test($("input[name=child_birthday]").val())) {
			alert("宝宝生日格式不正确。");
			return false;
		}
	}
	if(!empty($("input[name=child_idno]").val())) {
		if(!/^\d{18}$/.test($("input[name=child_idno]").val())) {
			alert("宝宝生日格式不正确：2010-05-01");
			return false;
		}
	}
	if(_is_requesting) {
		return false;
	} else {
		_is_requesting = true;
	}
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>b/?id=<?php echo $thisactivity['id'];?>&ac=post_person",
		type:"POST",
		data:{"is_adult":0, name:$("input[name=child_name]").val(), "sex":get_radio_value("child_sex"), "birthday":$("input[name=child_birthday]").val(), "address":$("input[name=child_address]").val(), "idno":$("input[name=child_idno]").val()},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$(".showmsg .weui_toast_content").html("添加成功");
				$(".showmsg").show();
				setTimeout(function(){location.reload();}, 1003);
			}
			_is_requesting = false;
		}
	});
}
function post_adult() {
	if(empty($("input[name=adult_name]").val())) {
		alert("家长姓名不能为空。");
		return false;
	}
	if(empty($("input[name=adult_phone]").val())) {
		alert("家长电话不能为空。");
		return false;
	}
	if(!/^1\d{10}$/.test($("input[name=adult_phone]").val())) {
		alert("家长电话号码不正确。");
		return false;
	}
	if(!empty($("input[name=adult_idno]").val())) {
		if(!/^\d{18}$/.test($("input[name=adult_idno]").val())) {
			alert("大人身份证号码不正确。");
			return false;
		}
	}
	if(_is_requesting) {
		return false;
	} else {
		_is_requesting = true;
	}
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>b/?id=<?php echo $thisactivity['id'];?>&ac=post_person",
		type:"POST",
		data:{"is_adult":1, name:$("input[name=adult_name]").val(), "sex":get_radio_value("adult_sex"), "phone":$("input[name=adult_phone]").val(), "address":$("input[name=child_address]").val(), "idno":$("input[name=adult_idno]").val()},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$(".showmsg .weui_toast_content").html("添加成功");
				$(".showmsg").show();
				setTimeout(function(){location.reload();}, 1003);
			}
			_is_requesting = false;
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
//关闭灰色 jQuery 遮罩 
function closeBg() { 
	$(".mask,.dialog").hide(); 
}
function showBg1() { 
	var bh1 = Math.max($("body").height(), $(window).height()); 
	var bw1 = Math.max($("body").width(), $(window).width()); 
	$(".mask").css({ 
		height:bh1, 
		width:bw1, 
		display:"block" 
	}); 
	$(".dialog1").show(); 
} 
	//关闭灰色 jQuery 遮罩 
function closeBg1() { 
	$(".mask,.dialog1").hide(); 
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