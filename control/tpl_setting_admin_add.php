<?php
defined('STCMS_ROOT') or die('Access Deined!');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>新增管理员__后台管理中心__<?php echo $config['seo_title'];?></title>
<link href="../css/control.css" type="text/css" rel="stylesheet">
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=setting">系统设置</a>&nbsp;&raquo;&nbsp;管理员设置</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=setting">首选项</a></li>
				<li><a href="./?ac=setting&do=admin">管理员设置</a></li>
                <li><a href="./?ac=setting&do=log">安全日志</a></li>
				<li class="on">新增管理员</li>
				<div class="clear"></div>
			</ul>
		</div>
		<form action="./?ac=setting&do=admin&step=added&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<table class="form" >
				<tr>
					<td height="40" align="right"><span class="red">*</span>管理员名称：</td>
					<td>
						<input type="text" class="input" name="name">
						&nbsp; <span class="gray"></span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>登录密码：</td>
					<td>
						<input type="password" class="input" name="password1">
						&nbsp; <span class="gray"></span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>确认密码：</td>
					<td>
						<input type="password" class="input" name="password2">
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
function check_form() {
	if(empty($('input[name=name]').val())) {
		 alert("你还未填写用户名。");
		 $('input[name=name]').focus();
		 return false;
	}
	if(empty($('input[name=password1]').val())) {
		 alert("你还未填写登录密码。");
		 $('input[name=password1]').focus();
		 return false;
	}
	if($("input[name=password1]").val().length < 6) {
		 alert("登录密码不得少于6位数。");
		 $('input[name=password1]').focus();
		 return false;
	}
	if($("input[name=password1]").val() != $("input[name=password2]").val()) {
		alert("两次输入的登录密码不一致。");
		 $('input[name=password2]').focus();
		 return false;
	}
	return true;
}
function empty(str) {
	if(typeof str =="undefined") {
		return true;
	}
	str = str.replace(/^[\t\r\n\s]*/, '').replace(/[\r\t\s\n]*$/, '');
	if(str == '' || str == '0') {
		return true;
	} else {
		return false;
	}
}
</script>
</body>
</html>