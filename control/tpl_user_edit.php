<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thisuser = $mysql_class->select_one("users", "*", array("id"=>intval($_GET['id'])));
if(empty($thisuser)) {
	halt("系统未找到指定的用户。");
}
switch($_GET['step']) {
	case 'setphone':
		$phone = trim($_POST['phone']);
		if(!preg_match("/^1\d{10}$/", $phone)) {
			exit(json_encode(array("error"=>1, "msg"=>"手机号码不正确。")));
		}
		$mysql_class->update("users", array("phone"=>$phone), array("id"=>$thisuser['id']));
		adminlog("修改了用户手机：".addslashes($thisuser['nickname']));
		exit(json_encode(array("error"=>0)));
	break;
	case 'coin':
		include("./tpl_user_edit_coin.php");
		exit;
	break;
	case 'extend':
		include("./tpl_user_edit_extend.php");
		exit;
	break;
	case 'exchange':
		include("./tpl_user_edit_exchange.php");
		exit;
	break;
	case 'card':
		include("./tpl_user_edit_card.php");
		exit;
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>用户详情__后台管理中心__<?php echo $config['seo_title'];?></title>
<link href="../css/control.css" type="text/css" rel="stylesheet">
<link href="../js/jquery.datetimepicker.css" type="text/css" rel="stylesheet">
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=user">用户管理</a>&nbsp;&raquo;&nbsp;用户详情</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=user">用户列表</a></li>
				<li><a href="./?ac=user&do=analysis">用户分析</a></li>
                <li><a href="./?ac=user&do=crm">CRM管理</a></li>
                <li><a href="./?ac=sale&do=index&step=rank">会员消费排行</a></li>
				<li class="on">用户详情</li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li class="on"><a href="./?ac=user&do=edit&id=<?php echo $thisuser['id'];?>">基本信息</a></li>
                        <li><a href="./?ac=user&do=edit&step=card&id=<?php echo $thisuser['id'];?>">亲子卡</a></li>
                        <li><a href="./?ac=user&do=edit&step=extend&id=<?php echo $thisuser['id'];?>">推广明细</a></li>
                        <li><a href="./?ac=user&do=edit&step=coin&id=<?php echo $thisuser['id'];?>">积分记录</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<div class="mt10">
			<form action="./?ac=user&do=edit&step=true&id=<?php echo $thisuser['id'];?>&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
				<table class="form" >
					<tr>
						<td height="40" align="right">用户昵称：</td>
						<td>
							<?php echo $thisuser['nickname'];?>
						</td>
					</tr>
					<tr>
						<td height="40" align="right">性别：</td>
						<td>
							<?php if($thisuser['sex'] == 0 ){?>未知<?php }else if($thisuser['sex'] == 1){?>男<?php } else if($thisuser['sex'] ==2){?>女<?php }?>
						</td>
					</tr>
					<tr>
						<td height="40" align="right">绑定手机：</td>
						<td>
							<?php echo $thisuser['phone'];?>
                            &nbsp;&nbsp;<a class="button" href="javascript:" onClick="setphone();"><span>绑定</span></a>
						</td>
					</tr>
					<tr>
						<td height="40" align="right">用户积分：</td>
						<td>
							<?php echo $thisuser['return_coin'];?>
                            &nbsp;&nbsp;<a class="button" href="./?ac=user&do=edit&step=coin&id=<?php echo $thisuser['id'];?>"><span>积分记录</span></a>
                            &nbsp;&nbsp;<a class="button" href="JavaScript:" onClick="exchange();"><span>积分兑换</span></a>
						</td>
					</tr>
					<tr>
						<td height="170" align="right">头像：</td>
						<td>
							<div class="teacher_preview">
								<img src="<?php echo $thisuser['headimgurl'];?>" />
							</div>
						</td>
					</tr>
					<tr>
						<td height="40" align="right">地区：</td>
						<td>
							<?php echo $thisuser['country'];?> <?php echo $thisuser['province'];?> <?php echo $thisuser['city'];?>
						</td>
					</tr>
					<tr>
						<td height="40" align="right">本次登录IP：</td>
						<td>
							<?php echo $thisuser['ip'];?>
						</td>
					</tr>
					<tr>
						<td height="40" align="right">上次IP：</td>
						<td>
							<?php echo $thisuser['last_ip'];?>
						</td>
					</tr>
					<tr>
						<td height="40" align="right">本次登录时间：</td>
						<td>
							<?php echo $thisuser['log_time'];?>
						</td>
					</tr>
					<tr>
						<td height="40" align="right">上次登录时间：</td>
						<td>
							<?php echo $thisuser['last_time'];?>
						</td>
					</tr>
					<tr>
						<td height="40" align="right">注册时间：</td>
						<td>
							<?php echo $thisuser['reg_time'];?>
						</td>
					</tr>
					<tr>
						<td height="40" align="right">openid：</td>
						<td>
							<?php echo $thisuser['openid'];?>
						</td>
					</tr>
					<tr>
						<td height="40" align="right">unionid：</td>
						<td>
							<?php echo $thisuser['unionid'];?>
						</td>
					</tr>
				</table>
			</form>
		</div>
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
function setphone() {
	SU.dialog({"title":"绑定手机", "msg":'<div>手机号码：<input type="text" name="phone" class="input" maxlength="11">&nbsp;&nbsp;<a href="javascript:" class="button" onclick="dosetphone();"><span>绑定</span></a></div>'});
}
function dosetphone() {
	if(!/^1\d{10}$/.test($("input[name=phone]").val())) {
		alert("手机号码填写不正确。");
		return false;
	}
	$.ajax({
		url:"./?ac=user&do=edit&step=setphone&id=<?php echo $thisuser['id'];?>",
		data:"phone="+$("input[name=phone]").val(),
		type:"POST",
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				SU.tip("绑定成功。");
				setTimeout(function(){location.reload();}, 1003);
			}
		}
	});
}
function exchange() {
	SU.dialog({
		title:"积分兑换",
		url:"./?ac=user&do=edit&step=exchange&id=<?php echo $thisuser['id'];?>"
	})
}
</script>
</body>
</html>