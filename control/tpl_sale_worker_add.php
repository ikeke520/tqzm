<?php
defined('STCMS_ROOT') or die('Access Deined!');
if($_GET['insert'] == "true") {
	$data['uid'] = intval($_POST['uid']);
	$data['realname'] = htmlspecialchars(trim($_POST['realname']));
	$data['idno'] = htmlspecialchars($_POST['idno']);
	$data['phone'] = htmlspecialchars(trim($_POST['phone']));
	$data['address'] = htmlspecialchars(trim($_POST['address']));
	
	if(empty($data['realname']) || empty($data['idno']) || empty($data['uid']) || empty($data['phone']) || empty($data['address'])) {
		halt("业务员姓名、身份证号码、微信ID、手机号码、现住地址必须填写。");
	}
	$mysql_class->insert("worker", $data);
	adminlog("添加业务员：".$mysql_class->insert_id());
	header("Location: ./?ac=sale&do=worker");
	exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>新增业务员__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=sale">销售管理</a>&nbsp;&raquo;&nbsp;业务员管理</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=sale">销售概览</a></li>
				<li class="on"><a href="./?ac=sale&do=worker">业务员管理</a></li>
                <li><a href="./?ac=sale&do=salary">激励机制</a></li>
                <li><a href="./?ac=sale&do=apply">大客户留言</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li><a href="./?ac=sale&do=worker">业务员列表</a></li>
						<li class="on"><a href="./?ac=sale&do=worker&step=add">新增业务员</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<form action="./?ac=sale&do=worker&step=add&insert=true&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<input type="hidden" name="uid" value="">
			<table class="form" >
				<tr>
					<td height="40" align="right"><span class="red">*</span>业务员姓名：</td>
					<td>
						<input type="text" class="input" name="realname" value="">
						&nbsp; <span class="gray">真实姓名</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>身份证号码：</td>
					<td>
                        <input type="text" class="input" name="idno" value="">
						&nbsp;<span class="gray">18位数字</span>
					</td>
				</tr>
				<tr>
					<td height="170" align="right"><span class="red">*</span>绑定微信：</td>
					<td>
						<div class="teacher_preview">
							<img src="../images/nophoto.jpg" />
							<div class="comment"></div>
							<a class="picker button" href="javascript:" onClick="SU.dialog({title:'选择用户', url:'./?ac=sale&do=getuser&func=select_user'});"><span>选择用户</span></a>
						</div>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>手机号码：</td>
					<td>
						<input type="text" class="input" name="phone">
						&nbsp; <span class="gray">11位数字</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>现住地址：</td>
					<td>
						<textarea class="textarea" name="address" style="width:400px; height:50px;"></textarea>
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
<script type="text/javascript" src="../lib/webuploader/webuploader.js"></script>
<script type="text/javascript">
function check_form() {
	if(empty($("input[name=realname]").val())) {
		$("input[name=realname]").focus();
		SU.tip("姓名不能为空。");
		return false;
	}
	if(empty($("input[name=idno]").val())) {
		$("input[name=idno]").focus();
		SU.tip("身份证号码不能为空。");
		return false;
	}
	if(empty($("input[name=uid]").val())) {
		$("input[name=uid]").focus();
		SU.tip("请选择一个微信用户。");
		return false;
	}
	if(empty($("input[name=phone]").val(), true)) {
		$("input[name=phone]").focus();
		SU.tip("手机号码不能为空。");
		return false;
	}
	if(empty($("textarea[name=address]").val())) {
		$("textarea[name=address]").focus();
		SU.tip("现住地址不能为空。");
		return false;
	}
	return true;
}
function select_user(data) {
	$("input[name=uid]").val(data['id']);
	$(".teacher_preview img").attr("src", data['picture']);
	$(".teacher_preview .comment").html(data['name']);
	return SU.rd();
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