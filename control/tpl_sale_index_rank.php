<?php
defined('STCMS_ROOT') or die('Access Deined!');
$type = intval($_GET['type']);
$where = "1";
switch($type) {
	case 0:
		$where = "is_use = '1' AND sell_time >= '{$startdate} 00:00:00' AND sell_time <= '{$enddate} 23:59:59'";
		$records = $mysql_class->fetch_all("SELECT SUM(price) AS total_price, COUNT(*) AS total_num, uid, uname FROM {$config['db_prefix']}cardbase WHERE {$where} GROUP BY uid LIMIT 0, 50");
	break;
	case 1:
		$where = "ctype = '0' AND time >= '{$startdate} 00:00:00' AND time <= '{$enddate} 23:59:59' AND is_payed = '1' AND is_refund='0'";
		$records = $mysql_class->fetch_all("SELECT SUM(money) AS total_price, COUNT(*) AS total_num, uid, uname FROM {$config['db_prefix']}users_paylog WHERE {$where} GROUP BY uid LIMIT 0, 50");
	break;
	case 2:
		$where = "ctype = '1' AND time >= '{$startdate} 00:00:00' AND time <= '{$enddate} 23:59:59' AND is_payed = '1' AND is_refund='0'";
		$records = $mysql_class->fetch_all("SELECT SUM(coin) AS total_coin, COUNT(*) AS total_num, uid, uname FROM {$config['db_prefix']}users_paylog WHERE {$where} GROUP BY uid LIMIT 0, 50");
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>消费排行__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;销售管理</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li class="on"><a href="./?ac=sale">销售概览</a></li>
				<li><a href="./?ac=sale&do=worker">业务员管理</a></li>
                <li><a href="./?ac=sale&do=salary">激励机制</a></li>
                <li><a href="./?ac=sale&do=apply">大客户留言</a></li>
				<div class="clear"></div>
			</ul>
		</div>
        <div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="sale" />
                    <input type="hidden" name="do" value="<?php echo $_GET['do'];?>">
                    <input type="hidden" name="step" value="<?php echo $_GET['step'];?>">
                    <input type="hidden" name="type" value="<?php echo $type;?>">
					起始日期：
					<input class="input" type="text" name="startdate" style="width:100px;" value="<?php echo $startdate; ?>" />
					&nbsp;&nbsp;结束日期：
					<input class="input" type="text" name="enddate" style="width:100px;" value="<?php echo $enddate; ?>" />
					&nbsp;&nbsp;<a href="javascript:void(0)" onclick="$('#search-form').submit();" class="button">搜索</a>
                    <input type="hidden" name="page" value="<?php echo $page;?>">
				</form>
			</div>
        </div>
		<div class="mt10">
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li><a href="./?ac=sale&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>">总括</a></li>
						<li><a href="./?ac=sale&step=list&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>">明细</a></li>
						<li class="on"><a href="./?ac=sale&step=rank&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>">排行</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<div class="mt10 tc">
            <a class="button" href="./?ac=sale&step=rank&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>&type=0">实体卡订单</a>
            &nbsp;&nbsp;<a class="button" href="./?ac=sale&step=rank&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>&type=1">线上现金订单</a>
            &nbsp;&nbsp;<a class="button" href="./?ac=sale&step=rank&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>&type=2">线上积分订单</a>
		</div>
				<table style="margin-top:10px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table" border="0">
                    <tbody>
						<tr>
							<th>排名</th>
                            <th>用户</th>
							<th>消费金额</th>
							<th>订单数量</th>
						</tr>
<?php
if($records) {
	foreach($records as $key => $list) {
?>
<?php
if($type == 0 || $type == 1) {
?>
						<tr>
                            <td><?php echo $key + 1;?></td>
                            <td><a href="./?ac=user&do=edit&id=<?php echo $list['uid'];?>"><?php echo $list['uname'];?></a></td>
							<td><?php echo $list['total_price'];?></td>
                            <td><?php echo $list['total_num'];?></td>
						</tr>
<?php
} else if($type ==2) {
?>
						<tr>
                            <td><?php echo $key + 1;?></td>
                            <td><a href="./?ac=user&do=edit&id=<?php echo $list['uid'];?>"><?php echo $list['uname'];?></a></td>
							<td><?php echo $list['total_coin'];?></td>
                            <td><?php echo $list['total_num'];?></td>
						</tr>
<?php
}
?>
<?php
	}
}
?>
					</tbody>
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
<script type="text/javascript" src="../js/jquery.datetimepicker.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	$("select[name=per_page]").val("<?php echo $per_page;?>");
	$("input[name=startdate]").datetimepicker({timepicker:false, format:'Y-m-d'});
	$("input[name=enddate]").datetimepicker({timepicker:false, format:'Y-m-d'});
});
function go_page(n) {
	$("input[name=page]").val(n);
	$("#search-form").submit();
}
</script>
</body>
</html>