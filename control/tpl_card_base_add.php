<?php
defined('STCMS_ROOT') or die('Access Deined!');
if($_GET['insert'] == "true") {
	$data['name'] = htmlspecialchars(trim($_POST['name']));
	$data['cardno'] = htmlspecialchars(trim($_POST['cardno']));
	$data['hash'] = htmlspecialchars(trim($_POST['hash']));
	$data['price'] = round(floatval($_POST['price']), 2);
	$data['num'] = intval($_POST['num']);
	$data['time'] = NOW;
		
	if(empty($data['name']) || empty($data['cardno']) || empty($data['hash']) || empty($data['num']) || empty($data['price'])) {
		halt("名称、价格、次数、卡号、卡密必须填写。");
	}
	$mysql_class->insert("cardbase", $data);
	adminlog("添加了实体卡（ID：".$mysql_class->insert_id()."）");
	header("Location: ./?ac=card&do=base");
	exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>新增实体卡__后台管理中心__<?php echo $config['seo_title'];?></title>
<link href="../css/control.css" type="text/css" rel="stylesheet">
<link href="../lib/webuploader/webuploader.css" type="text/css" rel="stylesheet">
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=card">亲子卡管理</a>&nbsp;&raquo;&nbsp;新增实体卡</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=card">亲子卡列表</a></li>
				<li><a href="./?ac=card&do=add">新增亲子卡</a></li>
                <li class="on"><a href="./?ac=card&do=base">实体亲子卡</a></li>
				<div class="clear"></div>
			</ul>
		</div>
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li><a href="./?ac=card&do=base">实体卡列表</a></li>
						<li class="on"><a href="./?ac=card&do=base&step=add">录入实体卡</a></li>
						<li><a href="./?ac=card&do=base&step=import">批量导入</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
		<form action="./?ac=card&do=base&step=add&insert=true&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<table class="form" >
				<tr>
					<td height="40" align="right"><span class="red">*</span>卡名称：</td>
					<td>
						<input type="text" class="input" name="name" value="">
						&nbsp; <span class="gray"></span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>实体卡号：</td>
					<td>
						<input type="text" class="input" name="cardno" value="">
						&nbsp; <span class="gray">最长16位数字与字母组合，不区分大小写</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>实体卡密：</td>
					<td>
						<input type="text" class="input" name="hash" value="">
						&nbsp; <span class="gray">最长16位数字与字母组合，不区分大小写</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>实体卡面值：</td>
					<td>
						<input type="text" class="input" name="price" value="" style="width:100px;">
						&nbsp; <span class="gray">单位：元，支持两位小数。</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>参与活动次数：</td>
					<td>
						<input type="text" class="input" name="num" value="" style="width:100px;">
						&nbsp; <span class="gray">单位：次，整数。</span>
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
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/su.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {

});
function check_form() {
	if(empty($("input[name=name]").val())) {
		$("input[name=name]").focus();
		SU.tip("名称不能为空。");
		return false;
	}
	if(empty($("input[name=price]").val(), true)) {
		$("input[name=price]").focus();
		SU.tip("价格不能为空。");
		return false;
	}
	if(empty($("input[name=cardno]").val())) {
		$("input[name=cardno]").focus();
		SU.tip("卡号不能为空。");
		return false;
	}
	if(empty($("input[name=hash]").val())) {
		$("input[name=hash]").focus();
		SU.tip("卡密不能为空。");
		return false;
	}
	var reg = new RegExp(/^(([1-9]+\d*)|0)(\.\d{1,2})?$/);
	if(!reg.test($("input[name=price]").val())) {
		$("input[name=price]").focus();
		SU.tip("价格格式不正确。");
		return false;
	}
	if(empty($("input[name=num]").val())) {
		$("input[name=num]").focus();
		SU.tip("参与活动次数不能为空。");
		return false;
	}
	var reg = new RegExp(/^\d+$/);
	if(!reg.test($("input[name=num]").val())) {
		SU.tip("参与活动次数的格式不正确。");
		$("input[name=num]").focus();
		return false;
	}
	return true;
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
</body>
</html>