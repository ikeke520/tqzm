<?php
defined('STCMS_ROOT') or die('Access Deined!');

$enddate = trim($_GET['enddate']);
if(!preg_match("/^20\d{2}\-\d{2}\-\d{2}$/", $enddate)) {
	$enddate = '';
}
$startdate = trim($_GET['startdate']);
if(!preg_match("/^20\d{2}\-\d{2}\-\d{2}$/", $startdate) || $startdate > $enddate) {
	$startdate = '';
}

$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$where = "pid = '{$thisuser['id']}'";
if($startdate && $enddate) {
	$where .= " AND time >= '{$startdate} 00:00:00' AND time <= '{$enddate} 00:00:00'";
}

$total_records = $mysql_class->num_table("users_relation", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select("users_relation", "*", $where, "id DESC", array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>推广明细__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=user">用户管理</a>&nbsp;&raquo;&nbsp;<a href="./?ac=user&do=edit&id=<?php echo $thisuser['id'];?>">用户详情</a>&nbsp;&raquo;&nbsp;推广记录</div>
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
                        <li><a href="./?ac=user&do=edit&id=<?php echo $thisuser['id'];?>">基本信息</a></li>
                        <li><a href="./?ac=user&do=edit&step=card&id=<?php echo $thisuser['id'];?>">亲子卡</a></li>
                        <li class="on"><a href="./?ac=user&do=edit&step=extend&id=<?php echo $thisuser['id'];?>">推广明细</a></li>
                        <li><a href="./?ac=user&do=edit&step=coin&id=<?php echo $thisuser['id'];?>">积分记录</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="user" />
                    <input type="hidden" name="do" value="edit">
                    <input type="hidden" name="step" value="extend">
                    <input type="hidden" name="id" value="<?php echo $thisuser['id'];?>">
					起始日期：
					<input class="input" type="text" name="startdate" style="width:100px;" value="<?php echo $startdate; ?>" />
					&nbsp;&nbsp;结束日期：
					<input class="input" type="text" name="enddate" style="width:100px;" value="<?php echo $enddate; ?>" />
					&nbsp;&nbsp;分页：
					<select class="select" name="per_page">
						<option value="20">20条每页</option>
						<option value="50">50条每页</option>
						<option value="100">100条每页</option>
					</select>
					&nbsp;&nbsp;<a href="javascript:void(0)" onclick="$('#search-form').submit();" class="button">搜索</a>
                    <input type="hidden" name="page" value="<?php echo $page;?>">
				</form>
			</div>
        </div>
			<form id="listform">
				<table style="margin-top:-1px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table" border="0">
					<tbody>
					<tr>
						<tr>
							<th>ID</th>
							<th>推广会员</th>
							<th>现金消费</th>
                           	<th>积分消费</th>
							<th>注册时间</th>
						</tr>
					</tr>
<?php
if($records) {
	foreach($records as $list) {
		$cashpay = $mysql_class->fetch($mysql_class->query("SELECT COUNT(id) AS num, SUM(money) AS money FROM {$config['db_prefix']}users_paylog WHERE uid='{$list['id']}' AND ctype='0' AND is_payed='1' AND is_refund='0'"));
		$coinpay = $mysql_class->fetch($mysql_class->query("SELECT COUNT(id) AS num, SUM(coin) AS coin FROM {$config['db_prefix']}users_paylog WHERE uid='{$list['id']}' AND ctype='1' AND is_payed='1' AND is_refund='0'"));
?>
                    <tr>
                        <td><?php echo $list['id'];?></td>
                        <td><a href="./?ac=user&do=edit&id=<?php echo $list['uid'];?>"><?php echo $list['uname'];?></a></td>
                        <td>¥<?php echo $cashpay['money'];?>（<?php echo $cashpay['num'];?>单）</td>
                        <td><?php echo $coinpay['coin'];?>个积分（<?php echo $coinpay['num'];?>单）</td>
                        <td><?php echo $list['time'];?></td>
                    </tr>
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