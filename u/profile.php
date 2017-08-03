<?php
switch($_GET['do']) {
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
		exit(json_encode(array("error"=>0, "data"=>$data)));
	break;
	case 'getperson':
		$thisperson = $mysql_class->select_one("users_person", "*", array("id"=>intval($_GET['id']), "uid"=>$thisuser['id']));
		if(empty($thisperson)) {
			exit(json_encode(array("error"=>1, "msg"=>"未找到指定的信息。")));
		}
		exit(json_encode(array("error"=>0, "data"=>$thisperson)));
	break;
	case 'edit_person':
		$thisperson = $mysql_class->select_one("users_person", "*", array("id"=>intval($_GET['id']), "uid"=>$thisuser['id']));
		if(empty($thisperson)) {
			exit(json_encode(array("error"=>1, "data"=>"未找到指定的信息。")));
		}
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
		$mysql_class->update("users_person", $data, array("id"=>$thisperson['id']));
		exit(json_encode(array("error"=>0)));
	break;
	case 'del_person':
		$mysql_class->delete("users_person", array("uid"=>$thisuser['id'], "id"=>intval($_GET['id'])));
		exit(json_encode(array("error"=>0)));
	break;
}
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
<link type="text/css" rel="stylesheet" href="<?php echo check_dir($config['web_url']);?>css/weui.min.css" />
</head>
<body>
	<div class="b_top">
		<b onclick="javascript:window.history.back(-1);"><img src="<?php echo check_dir($config['web_url']);?>images/a_fanghui.png">返回</b>
		<h2>我的资料</h2>
		<span>
		</span>
	</div>
	<form>
		<table>
			<div class="t_yonghu">
				<span></span>
				<h4>用户信息</h4>
				<span></span>
			</div>
			<div class="t_tel">
				<span>手机：</span>
				<input type="text" name="phone" value="<?php echo $thisuser['phone'];?>" readonly>
                
			</div>
            <div class="data_main1">
                <b onClick="location.href='<?php echo check_dir($config['web_url']);?>u/?ac=phone'">绑定手机</b>
            </div>
			<div class="a_kongge"></div>
			<div class="t_yonghu">
				<span></span>
				<h4>家长信息</h4>
				<span></span>
			</div>
			<div class="data_main1" id="data_main2">
                <div class="data_main1_box" style="background:#fff; border:none;">
            
<?php
$parentlist = $mysql_class->select("users_person", "*", array("uid"=>$thisuser['id'], "is_adult"=>"1"));
if($parentlist) {
	foreach($parentlist as $list) {
?>
				<div style="line-height:30px;"><label><input type="radio" name="adultperson" value="<?php echo $list['id'];?>">&nbsp; <?php echo $list['name'];?>（<?php if($list['sex']) {?>辣妈<?php } else {?>帅爸<?php }?> - <?php echo $list['phone'];?>）</label></div>
<?php
	}
}
?>
                </div>
				<div style="text-align:center">
                    <b style=" width:25%; display:inline-block" onClick="showBg1();">添加</b>
                    <b style=" width:25%; display:inline-block" onClick="edit_adult();">修改</b>
                    <b style=" width:25%; display:inline-block" onClick="del_adult();">删除</b>
                </div>
			</div>
			<div class="a_kongge b_kongge"></div>
			<div class="t_yonghu">
				<span></span>
				<h4>宝宝信息</h4>
				<span></span>
			</div>
			<div class="data_main1" id="data_main3">
                <div class="data_main1_box" style="background:#fff; border:none;">
            
<?php
$chidlist = $mysql_class->select("users_person", "*", array("uid"=>$thisuser['id'], "is_adult"=>"0"));
if($chidlist) {
	foreach($chidlist as $list) {
?>
				<div style="line-height:30px;"><label><input type="radio" name="childperson" value="<?php echo $list['id'];?>">&nbsp; <?php echo $list['name'];?>（<?php if($list['sex']) {?>女宝<?php } else {?>男宝<?php }?> - <?php echo $list['birthday'];?>）</label></div>
<?php
	}
}
?>
				<div style="text-align:center">
                    <b style=" width:25%; display:inline-block" onClick="showBg();">添加</b>
                    <b style=" width:25%; display:inline-block" onClick="edit_child();">修改</b>
                    <b style=" width:25%; display:inline-block" onClick="del_child();">删除</b>
                </div>
			</div>
		</table>
	</form>
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
			<a href="javascript:" class="d_hide"><input type="button" onClick="post_child();" value="添加" class="k_tijiao"></a>
		</div>
	</div>
	<div class="k_tanc_bg dialog2">
		<div class="k_tanc">
			<h2>购买童趣卡享受更多优惠</h2>
			
			<a href="javascript:closeBg2();" class="d_hide"><span></span></a>
		</div>
		<div class="k_wenben">
			<div class="k_wenben_fb">
				<p><label><input type="radio" name="echild_sex" value="0" checked>男宝</label></p>
				<p><label><input type="radio" name="echild_sex" value="1" class="k_nvbao">女宝</label></p>
			</div>	
		</div>
		<div class="k_wenben1">
			<ul>
				<li>
					<span><i>*</i>宝宝姓名：</span>
					<input type="hidden" name="echild_id">
                    <input type="text" name="echild_name" placeholder="请输入宝宝姓名">
				</li>
				<li>
					<span><i></i>宝宝生日：</span>
					<input type="text" name="echild_birthday" placeholder="2010-05-01">
				</li>
				<li>
					<span><i></i>所在区域：</span>
					<input type="text" name="echild_address" placeholder="如四方坪、伍家岭等">
				</li>
				<li>
					<span><i></i>身份证号：</span>
					<input type="text" name="echild_idno" placeholder="请填写宝宝身份证号码">
				</li>
				<li>
					<p style="width:100%;">填写身份证号则购买保险（保险免费）</p>
				</li>
			</ul>
			<a href="javascript:" class="d_hide"><input type="button" onClick="doedit_child();" value="修改" class="k_tijiao"></a>
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
	<div class="k_tanc_bg dialog3">
		<div class="k_tanc">
			<h2>购买童趣卡享受更多优惠</h2>
			
			<a href="javascript:closeBg3();" class="d_hide"><span></span></a>
		</div>
		<div class="k_wenben">
			<div class="k_wenben_fb">
				<p><label><input type="radio" name="eadult_sex" value="1" checked>辣妈</label></p>
				<p><label><input type="radio" name="eadult_sex" value="0" class="k_nvbao">帅爸</label></p>
			</div>	
		</div>
		<div class="k_wenben1">
			<ul>
				<li>
					<span><i>*</i>家长姓名：</span>
					<input type="hidden" name="eadult_id">
					<input type="text" name="eadult_name" placeholder="请输入家长姓名">
				</li>
				<li>
					<span><i>*</i>联系电话：</span>
					<input type="text" maxlength="11" name="eadult_phone" placeholder="请输入家长手机号码">
				</li>
				<li>
					<span><i></i>所在区域：</span>
					<input type="text" name="eadult_address" placeholder="如四方坪、伍家岭等">
				</li>
				<li>
					<span><i></i>身份证号：</span>
					<input type="text" name="eadult_idno" placeholder="请填写大人身份证号码">
				</li>
				<li>
					<p style="width:100%;">填写身份证号则购买保险（保险免费）</p>
				</li>
			</ul>
			<a href="javascript:" class="d_hide"><input type="button" onClick="doedit_adult();" value="修改" class="k_tijiao"></a>
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
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/qiu_phone.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/phone.js"></script>
<script type="text/javascript" src="<?php echo check_dir($config['web_url']);?>js/common.js"></script>
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
function edit_child() {
	var id = get_radio_value("childperson");
	if(!id) {
		alert("请选择一名宝宝。");
		return false;	
	}
	$(".weui_loading_toast").show();
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>u?ac=profile&do=getperson",
		type:"GET",
		data:{"id":id},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$(".weui_loading_toast").hide();
				$("input[name=echild_id]").val(data['data'].id);
				init_radio("echild_sex", data['data'].sex);
				$("input[name=echild_name]").val(data['data'].name);
				$("input[name=echild_birthday]").val(data['data'].birthday);
				$("input[name=echild_address]").val(data['data'].address);
				$("input[name=echild_idno]").val(data['data'].idno);
				var bh = $("body").height(); 
				var bw = $("body").width(); 
				$(".mask").css({ 
					height:bh, 
					width:bw, 
					display:"block" 
				}); 
				$(".dialog2").show(); 
			}
		}
	});
}
function doedit_child() {
	if(empty($("input[name=echild_name]").val())) {
		alert("宝宝姓名不能为空。");
		return false;
	}
	if(!empty($("input[name=echild_birthday]").val())) {
		if(!/^20\d{2}-\d{1,2}-\d{1,2}$/.test($("input[name=echild_birthday]").val())) {
			alert("宝宝生日格式不正确：2010-05-01");
			return false;
		}
	}
	if(!empty($("input[name=echild_idno]").val())) {
		if(!/^\d{18}$/.test($("input[name=echild_idno]").val())) {
			alert("宝宝身份证号码不正确。");
			return false;
		}
	}
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>u/?ac=profile&do=edit_person&id="+$("input[name=echild_id]").val(),
		type:"POST",
		data:{"is_adult":0, name:$("input[name=echild_name]").val(), "sex":get_radio_value("echild_sex"), "birthday":$("input[name=echild_birthday]").val(), "address":$("input[name=echild_address]").val(), "idno":$("input[name=echild_idno]").val()},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$(".showmsg .weui_toast_content").html("操作成功");
				$(".showmsg").show();
				setTimeout(function(){location.reload();}, 1003);
			}
		}
	});
}
function edit_adult() {
	var id = get_radio_value("adultperson");
	if(!id) {
		alert("请选择一名家长。");
		return false;	
	}
	$(".weui_loading_toast").show();
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>u?ac=profile&do=getperson",
		type:"GET",
		data:{"id":id},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$(".weui_loading_toast").hide();
				$("input[name=eadult_id]").val(data['data'].id);
				init_radio("eadult_sex", data['data'].sex);
				$("input[name=eadult_name]").val(data['data'].name);
				$("input[name=eadult_address]").val(data['data'].address);
				$("input[name=eadult_phone]").val(data['data'].phone);
				$("input[name=eadult_idno]").val(data['data'].idno);
				var bh = Math.max($("body").height(), $(window).height()); 
				var bw = Math.max($("body").width(), $(window).width()); 
				$(".mask").css({ 
					height:bh, 
					width:bw, 
					display:"block" 
				}); 
				$(".dialog3").show(); 
			}
		}
	});
}
function doedit_adult() {
	if(empty($("input[name=eadult_name]").val())) {
		alert("家长姓名不能为空。");
		return false;
	}
	if(empty($("input[name=eadult_phone]").val())) {
		alert("家长电话不能为空。");
		return false;
	}
	if(!/^1\d{10}$/.test($("input[name=eadult_phone]").val())) {
		alert("家长电话号码不正确。");
		return false;
	}
	if(!empty($("input[name=eadult_idno]").val())) {
		if(!/^\d{18}$/.test($("input[name=eadult_idno]").val())) {
			alert("大人身份证号码不正确。");
			return false;
		}
	}
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>u/?ac=profile&do=edit_person&id="+$("input[name=eadult_id]").val(),
		type:"POST",
		data:{"is_adult":1, name:$("input[name=eadult_name]").val(), "sex":get_radio_value("eadult_sex"), "phone":$("input[name=eadult_phone]").val(), "address":$("input[name=eadult_address]").val(), "idno":$("input[name=eadult_idno]").val()},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$(".showmsg .weui_toast_content").html("操作成功");
				$(".showmsg").show();
				setTimeout(function(){location.reload();}, 1003);
			}
		}
	});
}
function del_adult() {
	var id = get_radio_value("adultperson");
	if(!id) {
		alert("请选择一名家长。");
		return false;	
	}
	del_person(id, 1);
}
function del_child() {
	var id = get_radio_value("childperson");
	if(!id) {
		alert("请选择一名宝宝。");
		return false;	
	}
	del_person(id, 0);
}
function del_person(id, is_adult) {
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>u/?ac=profile&do=del_person",
		type:"GET",
		data:{"is_adult":is_adult, "id":id},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$(".showmsg .weui_toast_content").html("操作成功");
				$(".showmsg").show();
				setTimeout(function(){location.reload();}, 1003);
			}
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
			alert("宝宝生日格式不正确：2010-05-01");
			return false;
		}
	}
	if(!empty($("input[name=child_idno]").val())) {
		if(!/^\d{18}$/.test($("input[name=child_idno]").val())) {
			alert("宝宝身份证号码不正确。");
			return false;
		}
	}
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>u/?ac=profile&do=post_person",
		type:"POST",
		data:{"is_adult":0, name:$("input[name=child_name]").val(), "sex":get_radio_value("child_sex"), "birthday":$("input[name=child_birthday]").val(), "address":$("input[name=child_address]").val(), "idno":$("input[name=child_idno]").val()},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$(".showmsg .weui_toast_content").html("操作成功");
				$(".showmsg").show();
				setTimeout(function(){location.reload();}, 1003);
			}
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
	$.ajax({
		url:"<?php echo check_dir($config['web_url']);?>u/?ac=profile&do=post_person",
		type:"POST",
		data:{"is_adult":1, name:$("input[name=adult_name]").val(), "sex":get_radio_value("adult_sex"), "phone":$("input[name=adult_phone]").val(), "address":$("input[name=adult_address]").val(), "idno":$("input[name=adult_idno]").val()},
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$(".showmsg .weui_toast_content").html("操作成功");
				$(".showmsg").show();
				setTimeout(function(){location.reload();}, 1003);
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
function closeBg2() {
	$(".mask,.dialog2").hide(); 
}
function closeBg3() {
	$(".mask,.dialog3").hide(); 
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