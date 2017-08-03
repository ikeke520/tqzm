<?php
defined('STCMS_ROOT') or die('Access Deined!');
if($_GET['step'] == "grow") {
	include("./tpl_user_analysis_grow.php");
	exit;
}
$province = trim($_GET['provice']);
if($province) {
	$total_num = $mysql_class->num_table("users", array("province"=>$province));
	$records = $mysql_class->fetch_all("SELECT COUNT(*) AS total_num, city FROM {$config['db_prefix']}users WHERE province='{$province}' GROUP BY city ORDER BY total_num DESC");
} else {
	$total_num = $mysql_class->num_table("users");
	$records = $mysql_class->fetch_all("SELECT COUNT(*) AS total_num, province FROM {$config['db_prefix']}users GROUP BY province ORDER BY total_num DESC");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>用户分析__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=user">用户管理</a>&nbsp;&raquo;&nbsp;用户分析</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=user">用户列表</a></li>
				<li class="on"><a href="./?ac=user&do=analysis">用户分析</a></li>
                <li><a href="./?ac=user&do=crm">CRM管理</a></li>
                <li><a href="./?ac=sale&do=index&step=rank">会员消费排行</a></li>
			</ul>
		</div>
		<div class="mt10">
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li class="on"><a href="./?ac=user&do=analysis">地区分析</a></li>
                        <li><a href="./?ac=user&do=analysis&step=grow">增长分析</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<div class="mt10">
			<form action="./?ac=user&do=edit&step=true&id=<?php echo $thisuser['id'];?>&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
				<table class="form" >
					<tr style="background:#ececec;" class="tc">
						<td height="40" style="width:150px;">地区</td>
						<td>图表	</td>
                        <td style="width:150px;">人数（比例）</td>
					</tr>
<?php
if($records) {
	foreach($records as $list) {
?>
					<tr class="tc">
						<td height="40"><?php if(empty($province)) {?><?php if($list['province']) {?><a href="./?ac=user&do=analysis&provice=<?php echo urlencode($list['province']);?>"><?php echo $list['province'];?></a><?php } else {?>其他<?php }?><?php } else {?><?php if($list['city']) {?><?php echo $list['city'];?><?php } else {?>其他<?php }?><?php }?></td>
						<td><div style="height:30px; background:#f1f1f1; width:<?php echo round($list['total_num']/$total_num, 4) *100;?>%;"></div></td>
                        <td><?php echo $list['total_num'];?>（<?php echo round($list['total_num']/$total_num, 4) *100;?>%）</td>
					</tr>
<?php
	}
}
?>
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
<script type="text/javascript" src="../js/jquery.datetimepicker.js"></script>
</body>
</html>