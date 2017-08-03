<?php
defined('STCMS_ROOT') or die('Access Deined!');
$default_config = array(
	'seo_title'=>"童趣周末",
	'seo_keyword'=>"童趣周末，儿童周末，周末活动，儿童活动",
	'seo_desc'=>"童趣周末，专注于策划儿童的周末安排，为小朋友们的健康成长营造良好的氛围",
	"coin_switch"=>0,
	"user_coin"=>20,
	"cash_coin"=>10,
);
$config = $mysql_class->select_one("config");
if(empty($config)) {
	$mysql_class->insert("config", $default_config);
	$config = $default_config;
}
if($_GET['update'] == "true") {
	$data['coin_switch'] = intval($_POST['coin_switch']);
	$data['user_coin'] = intval($_POST['user_coin']);
	$data['cash_coin'] = intval($_POST['cash_coin']);
	$mysql_class->update("config", $data);
	header("Location: ./?ac=sale&do=salary");
	exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>激励机制__后台管理中心__<?php echo $config['seo_title'];?></title>
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
				<li><a href="./?ac=sale&do=worker">业务员管理</a></li>
                <li class="on"><a href="./?ac=sale&do=salary">激励机制</a></li>
                <li><a href="./?ac=sale&do=apply">大客户留言</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<form action="./?ac=sale&do=salary&update=true&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<table class="form" >
                <tr>
					<td height="80" align="right"><span class="red">*</span>积分说明：</td>
					<td>
						<p class="gray">积分是一种虚拟货币，每1个积分折合人民币1分。</p>
                        <p class="gray">积分可以用于购买系统内部的产品，也可以在后台兑换礼品。</p>
					</td>
				</tr>
                <tr>
					<td height="40" align="right"><span class="red">*</span>积分开关：</td>
					<td>
						<label><input type="radio" name="coin_switch" value="0">&nbsp;打开</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="coin_switch" value="1">&nbsp;关闭</label>
						&nbsp; <span class="gray">是否开启积分奖励机制，即推荐其他人成为会员及其消费时返还积分</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>推荐会员奖励：</td>
					<td>
                        <input type="text" class="input" name="user_coin" value="<?php echo $config['user_coin'];?>">
						&nbsp;<span class="gray">整数，每推荐一位会员，系统奖励的积分值</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>会员消费奖励：</td>
					<td>
						<input type="text" class="input" name="cash_coin" value="<?php echo $config['cash_coin'];?>">
						&nbsp; <span class="gray">整数，其推荐的会员每现金消费一元系统奖励的积分值</span>
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
$(document).ready(function(e) {
    init_radio("coin_switch", "<?php echo $config['coin_switch'];?>");
});
function check_form() {

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