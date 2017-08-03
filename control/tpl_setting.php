<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thismodel = "setting";
if($_GET['do'] == 'admin') {
	include("./tpl_setting_admin.php");
	exit;
} else if($_GET['do'] == "slider") {
	include("./tpl_setting_slider.php");
	exit;
} else if($_GET['do'] == "edit") {
	include("./tpl_setting_edit.php");
	exit;
} else if($_GET['do'] == "log") {
	include("./tpl_setting_log.php");
	exit;
}
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
if($_GET['step'] == 'update') {
	$data['seo_title'] = htmlspecialchars($_POST['seo_title']);
	$data['seo_keyword'] = htmlspecialchars($_POST['seo_keyword']);
	$data['seo_desc'] = htmlspecialchars($_POST['seo_desc']);
	$mysql_class->update("config", $data);
	adminlog("修改了首选项");
	header("Location: ./?ac=setting");
	exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>系统设置__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;系统设置</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li class="on"><a href="./?ac=setting">首选项</a></li>
				<li><a href="./?ac=setting&do=admin">管理员设置</a></li>
                <li><a href="./?ac=setting&do=log">安全日志</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li class="on"><a href="./?ac=setting">首选项</a></li>
						<li><a href="./?ac=setting&do=edit&type=intro">关于我们</a></li>
						<li><a href="./?ac=setting&do=edit&type=faq">常见问题</a></li>
						<li><a href="./?ac=setting&do=edit&type=crop">商务合作</a></li>
						<li><a href="./?ac=setting&do=edit&type=link">联系客服</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<form action="./?ac=setting&step=update&sid=<?php echo SID;?>" method="post">
			<table class="form" >
				<tr>
					<td height="40" align="right"><span class="red">*</span>网站标题：</td>
					<td>
						<textarea class="textarea" name="seo_title" style="width:300px; height:50px;"><?php echo $config['seo_title'];?></textarea>
						&nbsp; <span class="gray">网站首页标题</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>网站关键词：</td>
					<td><textarea class="textarea" name="seo_keyword" style="width:300px; height:80px;"><?php echo $config['seo_keyword'];?></textarea>&nbsp; <span class="gray">网页head部分中的关键词，用于搜索引擎优化</span></td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>网站描述：</td>
					<td><textarea class="textarea" name="seo_desc" style="width:300px; height:160px;"><?php echo $config['seo_desc'];?></textarea>&nbsp; <span class="gray">网页head部分中的网站描述，用于搜索引擎优化</span></td>
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
</body>
</html>