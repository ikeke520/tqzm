<?php
defined('STCMS_ROOT') or die('Access Deined!');
if($_GET['step'] == "true") {
	$is_to_all = intval($_POST['is_to_all']);
	$tagid = intval($_POST['group']);
	$openids = preg_split("/[\r\n]/", trim($_POST['openid']));
	$content = htmlspecialchars($_POST['content']);
	if(empty($content)) {
		exit(json_encode(array("error"=>1, "msg"=>"消息不能为空。")));
	}
	if($is_to_all == 1) {
		$data = array(
			"filter"=>array("is_to_all"=>true),
			"text"=>array("content"=>urlencode($content)),
			"msgtype"=>"text"
		);
	} else if($is_to_all == 2) {
		if(empty($tagid)) {
			exit(json_encode(array("error"=>1, "msg"=>"分组不能空。")));
		}
		$data = array(
			"filter"=>array("is_to_all"=>false, "tag_id"=>$tagid),
			"text"=>array("content"=>urlencode($content)),
			"msgtype"=>"text"
		);
	} else if($is_to_all == 3) {
		if(empty($openids)) {
			exit(json_encode(array("error"=>1, "msg"=>"openid不能空。")));
		}
		$data = array(
			"touser"=>$openids,
			"text"=>array("content"=>urlencode($content)),
			"msgtype"=>"text"
		);
	}
	if($is_to_all == 3) {
		$url = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=".get_access_token();
	} else {
		$url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=".get_access_token();
	}
	$response = json_decode(http_request($url, urldecode(json_encode($data))));
	if($response->errcode == "0") {
		adminlog("群发了微信消息");
		exit(json_encode(array("error"=>0)));
	} else {
		exit(json_encode(array("error"=>1, "msg"=>$response->errmsg)));
	}
}
$url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".get_access_token();
$response = json_decode(http_request($url));
$groups = $response->tags;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>群发消息__后台管理中心__<?php echo $config['seo_title'];?></title>
<link href="../css/control.css" type="text/css" rel="stylesheet">
<style>
.catlist ul{ list-style:none;}
.catlist ul { padding-left:30px !important;}
.catlist ul li { padding:10px 0;}
</style>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
</head>
<body>
<div class="top">
	<div class="wrapper">
		<div class="logo"><?php echo $config['seo_title'];?> 后台管理中心</div>
		<div class="link"> <a href="../" target="_blank">网站首页</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="./?ac=logout">退出登录</a> </div>
	</div>
</div>
<div class="nav">
	<dl>
		<dt class="activity<?php if($thismodel == "activity"){?> on<?php }?>"><a href="./">活动管理</a></dt>
		<dt class="sale<?php if($thismodel == "sale"){?> on<?php }?>"><a href="./?ac=sale">销售管理</a></dt>
		<dt class="card<?php if($thismodel == "card"){?> on<?php }?>"><a href="./?ac=card">亲子卡管理</a></dt>
		<dt class="news<?php if($thismodel == "news"){?> on<?php }?>"><a href="./?ac=news">资讯管理</a></dt>
		<dt class="order<?php if($thismodel == "order"){?> on<?php }?>"><a href="./?ac=order">财务订单</a></dt>
		<dt class="user<?php if($thismodel == "user"){?> on<?php }?>"><a href="./?ac=user">会员管理</a></dt>
		<dt class="connect<?php if($thismodel == "connect"){?> on<?php }?>"><a href="./?ac=connect">微信接口</a></dt>
		<dt class="ads<?php if($thismodel == "ads"){?> on<?php }?>"><a href="./?ac=ads">广告管理</a></dt>
		<dt class="setting<?php if($thismodel == "setting"){?> on<?php }?>"><a href="./?ac=setting">系统设置</a></dt>
	</dl>
</div>
<div class="main">
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=connect">微信接口</a>&nbsp;&raquo;&nbsp;群发消息</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=connect">消息管理</a></li>
				<li><a href="./?ac=connect&do=menu">菜单管理</a></li>
				<li class="on"><a href="./?ac=connect&do=post">群发消息</a></li>
				<li><a href="./?ac=connect&do=tplmsg">模板消息</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<form id="postfrm" action="./?ac=connect&do=post&step=true&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<table class="form" >
				<tr>
					<td colspan="2">
						<span class="gray">说明：只支持发送文本消息，图文、图片、视频等消息需要在微信公众平台发送。服务号每月只能发送 <span class="red">4</span> 条全体消息，且每个用户最多收到 <span class="red">4</span> 条消息。</span>
					</td>
				</tr>
				<tr>
					<td align="right"><span class="red">*</span>会员筛选：</td>
					<td>
						<label><input type="radio" name="is_to_all" value="1" checked>&nbsp;所有用户</label>&nbsp;&nbsp;
						<label><input type="radio" name="is_to_all" value="2">&nbsp;分组用户</label>&nbsp;&nbsp;
						<label><input type="radio" name="is_to_all" value="3">&nbsp;单个用户</label>&nbsp;&nbsp;
					</td>
				</tr>
				<tr id="group" style="display:none;">
					<td align="right"><span class="red">*</span>选择分组：</td>
					<td>
						<select name="group">
							<option value="0">全部分组</option>
<?php
if($groups) {
	foreach($groups as $list) {
?>
							<option value="<?php echo $list->id;?>"><?php echo $list->name;?>（<?php echo $list->count;?>）</option>
<?php
	}
}
?>
						</select>
					</td>
				</tr>
				<tr id="single" style="display:none;">
					<td align="right"><span class="red">*</span>设定用户：</td>
					<td>
						<textarea class="textarea" name="openid" style="width:300px; height:100px;"></textarea>
						&nbsp;<span class="gray">输入用户openid，支持多个用户，一行一个openid</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>消息内容：</td>
					<td>
						<textarea class="textarea" style="width:400px; height:200px;" name="content"></textarea>
						&nbsp; <span class="gray"></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" height="80" style="padding-left:100px;"><input type="submit" value="提交" id="hiddensubmit" style="display:none;" />
						<a href="javascript:void(0);" onclick="$('#hiddensubmit').click();" class="submit"><span>确认提交</span></a></td>
				</tr>
			</table>
		</form>
	</div>
</div>
<div class="footer">
	<div class="copyright">
		<div class="wapper"> 
			<p>版权所有 &copy <?php echo $config['seo_title'];?>。未经允许，任何人不得使用、复制、二次开发。</p>
			<p>系统开发：<a href="http://www.phpstcms.com/" target="_blank">STCMS</a>，me@yangdahong.cn</p>
		</div>
	</div>
</div>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/su.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	$("input[name=is_to_all]").click(function() {
		init_type();
	});
});
function init_type() {
	if(get_radio_value("is_to_all") == "2") {
		$("#group").show();
		$("#single").hide();
	} else if(get_radio_value("is_to_all") == "3") {
		$("#group").hide();
		$("#single").show();
	} else {
		$("#group").hide();
		$("#single").hide();
	}
}
function check_form() {
	if(get_radio_value("is_to_all") == "2" && $("select[name=group]").val() == "0") {
		$("select[name=group]").focus();
		SU.tip("请选择一个分组。");
		return false;
	} else if(get_radio_value("is_to_all") == "3" && $("textarea[name=openid]").val() == "") {
		$("textarea[name=openid]").focus();
		SU.tip("请输入opneid。");
		return false;
	}
	if($("textarea[name=content]").val() == "") {
		$("textarea[name=content]").focus();
		SU.tip("请输入消息内容。");
		return false;
	}
	$.ajax({
		url:"./?ac=connect&do=post&step=true",
		data:{"is_to_all":get_radio_value("is_to_all"), "group":$("select[name=group]").val(), "openid":$("textarea[name=openid]").val(), "content":$("textarea[name=content]").val()},
		type:"POST",
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				SU.tip("发送成功。");
				setTimeout(function(){location.reload();}, 1003);
			}
		}
	});
	return false;
}
</script>
</body>
</html>