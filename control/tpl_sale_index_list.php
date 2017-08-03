<?php
defined('STCMS_ROOT') or die('Access Deined!');
$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$type = intval($_GET['type']);
$where = "1";
switch($type) {
	case 0:
		$table = "cardbase";
		$where .= " AND is_use = '1' AND sell_time >= '{$startdate} 00:00:00' AND sell_time <= '{$enddate} 23:59:59'";
	break;
	case 1:
		$table = "users_paylog";
		$where .= " AND ctype = '0' AND time >= '{$startdate} 00:00:00' AND time <= '{$enddate} 23:59:59' AND is_payed = '1' AND is_refund='0'";
	break;
	case 2:
		$table = "users_paylog";
		$where .= " AND ctype = '1' AND time >= '{$startdate} 00:00:00' AND time <= '{$enddate} 23:59:59' AND is_payed = '1' AND is_refund='0'";
	break;
}
$total_records = $mysql_class->num_table($table, $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select($table, "*", $where, "id DESC", array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>明细浏览__后台管理中心__<?php echo $config['seo_title'];?></title>
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
						<li class="on"><a href="./?ac=sale&step=list&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>">明细</a></li>
						<li><a href="./?ac=sale&step=rank&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>">排行</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<div class="mt10 tc">
            <a class="button" href="./?ac=sale&step=list&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>&type=0">实体卡订单</a>
            &nbsp;&nbsp;<a class="button" href="./?ac=sale&step=list&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>&type=1">线上现金订单</a>
            &nbsp;&nbsp;<a class="button" href="./?ac=sale&step=list&startdate=<?php echo $startdate;?>&enddate=<?php echo $enddate;?>&type=2">线上积分订单</a>
		</div>
<?php
if($type == 0) {
?>
				<table style="margin-top:10px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table" border="0">
					<tbody>
						<tr>
							<th>ID</th>
							<th>卡号</th>
							<th>名称</th>
							<th>价格</th>
							<th>参与活动次数</th>
                            <th>出售我间</th>
						</tr>
<?php
} else if($type == 1) {
?>
				<table style="margin-top:10px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table" border="0">
                    <tbody>
						<tr>
							<th>订单号</th>
							<th>用户</th>
							<th>类型</th>
							<th>对象</th>
                            <th>支付方式</th>
							<th>金额</th>
							<th>状态</th>
							<th>下单时间</th>
						</tr>
<?php
} else if($type == 2) {
?>
				<table style="margin-top:10px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table" border="0">
                    <tbody>
						<tr>
							<th>订单号</th>
							<th>用户</th>
							<th>类型</th>
							<th>对象</th>
                            <th>支付方式</th>
							<th>金额</th>
							<th>状态</th>
							<th>下单时间</th>
						</tr>
<?php
}
if($records) {
	foreach($records as $list) {
?>
<?php
if($type == 0) {
?>
						<tr>
							<td><?php echo $list['id'];?></td>
                            <td><a href="./?ac=card&do=base&step=edit&id=<?php echo $list['id'];?>"><?php echo $list['cardno'];?></a></td>
							<td><?php echo $list['name'];?></td>
                            <td><span class="red">¥<?php echo $list['price'];?></span></td>
							<td><?php echo $list['num'];?></td>
							<td><?php echo $list['time'];?></td>
						</tr>
<?php
} else if($type ==1) {
?>
						<tr>
							<td><?php echo $list['tradeno'];?></td>
							<td><a href="./?ac=user&do=edit&id=<?php echo $list['uid'];?>"><?php echo $list['uname'];?></a></td>
							<td><?php if($list['type'] == 0) {?>购买亲子卡<?php } else if($list['type'] == 1) {?>购买活动<?php } else if($list['type'] ==2) {?>购买线上课程<?php } else if($list['type'] == 3) {?>购买定制课程<?php } else if($list['type'] == 4) {?>查看咨询<?php }?></td>
							<td>
<?php
if($list['type'] == 1) {
	$thisactivity = $mysql_class->select_one("activity", "*", array("id"=>$list['price_id']));
?>
								<a href="./?ac=index&do=edit&id=<?php echo $thisactivity['id'];?>"><?php echo $thisactivity['title'];?></a>
<?php
} else if($list['type'] == 0) {
	$thiscard = $mysql_class->select_one("card", "*", array("id"=>$list['price_id']));
?>
								<a href="./?ac=card&do=edit&id=<?php echo $thiscard['id'];?>"><?php echo $thiscard['name'];?></a>
<?php
}
?>							</td>
							<td><?php if($list['ctype']){?>积分<?php } else {?>现金<?php }?></td>
							<td><span class="red"><?php if($list['ctype']){?><?php echo $list['coin'];?> 积分<?php } else {?>¥ <?php echo $list['money'];?><?php }?></span></td>
							<td><?php if($list['is_payed']){?><span class="green">已支付</span><?php } else {?>待支付<?php }?></td>
							<td><?php echo $list['time'];?></td>
						</tr>
<?php
} else if($type == 2) {
?>
						<tr>
							<td><?php echo $list['tradeno'];?></td>
							<td><a href="./?ac=user&do=edit&id=<?php echo $list['uid'];?>"><?php echo $list['uname'];?></a></td>
							<td><?php if($list['type'] == 0) {?>购买亲子卡<?php } else if($list['type'] == 1) {?>购买活动<?php } else if($list['type'] ==2) {?>购买线上课程<?php } else if($list['type'] == 3) {?>购买定制课程<?php } else if($list['type'] == 4) {?>查看咨询<?php }?></td>
							<td>
<?php
if($list['type'] == 1) {
	$thisactivity = $mysql_class->select_one("activity", "*", array("id"=>$list['price_id']));
?>
								<a href="./?ac=index&do=edit&id=<?php echo $thisactivity['id'];?>"><?php echo $thisactivity['title'];?></a>
<?php
} else if($list['type'] == 0) {
	$thiscard = $mysql_class->select_one("card", "*", array("id"=>$list['price_id']));
?>
								<a href="./?ac=card&do=edit&id=<?php echo $thiscard['id'];?>"><?php echo $thiscard['name'];?></a>
<?php
}
?>							</td>
							<td><?php if($list['ctype']){?>积分<?php } else {?>现金<?php }?></td>
							<td><span class="red"><?php if($list['ctype']){?><?php echo $list['coin'];?> 积分<?php } else {?>¥ <?php echo $list['money'];?><?php }?></span></td>
							<td><?php if($list['is_payed']){?><span class="green">已支付</span><?php } else {?>待支付<?php }?></td>
							<td><?php echo $list['time'];?></td>
						</tr>
<?php
}
?>
<?php
	}
}
?>
						<tr>
							<td colspan="11"><?php echo $page_class->get_js_code('go_page'); ?></td>
						</tr>
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
<script type="text/javascript" src="../js/icharts.js"></script>
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