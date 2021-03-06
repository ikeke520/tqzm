<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thismodel = "order";

$type = intval($_GET['type']);
$uid = intval($_GET['']);
$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$where = "1";

if(empty($keyword) == false) {
	$where .= " AND (uname LIKE '%".$keyword."%' OR tradeno LIKE '%".$keyword."%' OR msg LIKE '%".$keyword."%' OR money LIKE '%".$keyword."%')";
}
switch($type) {
	case 1:
		$where .= " AND is_payed = '1' AND is_refund='0'";
	break;
	case '2':
		$where .= " AND is_payed = '0'";
	break;
	case '3':
		$where .= " AND is_refund = '1'";
	break;
}
$total_records = $mysql_class->num_table("users_paylog", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select("users_paylog", "*", $where, "id DESC", array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>订单管理__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;订单列表</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li class="on"><a href="./?ac=order">订单列表</a></li>
                <li><a href="./?ac=sale">订单统计</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="order" />
					关键词：
					<input class="input" type="text" name="keyword" style="width:80px;" value="<?php echo $keyword; ?>" />
					&nbsp;&nbsp;状态：
					<select class="select" name="type">
                        <option value="0">全部</option>
                        <option value="1">已支付</option>
						<option value="2">待支付</option>
						<option value="3">退单</option>
					</select>
                    &nbsp;&nbsp;分页：
					<select class="select" name="per_page">
						<option value="20">20条每页</option>
						<option value="50">50条每页</option>
						<option value="100">100条每页</option>
					</select>
					&nbsp;&nbsp;<a href="javascript:void(0)" onclick="$('#search-form').submit();" class="button">搜索</a>
					<input type="hidden" name="page" value="1" />
				</form>
			</div>
			<form id="listform">
				<table style="margin-top:-1px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table" border="0">
					<tbody>
						<tr>
							<th>ID</th>
							<th>订单号</th>
							<th>学员</th>
							<th>类型</th>
							<th>对象</th>
                            <th>支付方式</th>
							<th>金额</th>
							<th>状态</th>
							<th>下单时间</th>
						</tr>
<?php
if($records) {
	foreach($records as $list) {
?>
						<tr>
							<td><?php echo $list['id'];?></td>
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
							<td><?php if($list['is_refund']) {?><a href="javascript:" onclick="showrefund('<?php echo $list['refund_id'];?>')"><span class="red">退单</span></a><?php } else {?><?php if($list['is_payed']){?><span class="green">已支付</span><?php } else {?>待支付<?php }}?></td>
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
<script type="text/javascript">
$(document).ready(function(e) {
	$("select[name=per_page]").val("<?php echo $per_page;?>");
	$("select[name=type]").val("<?php echo $type;?>");
});
function showrefund(refund_id) {
	SU.dialog({"title":"退单详情", "msg":'<p>退单单号：'+refund_id+'</p>'});
}
function go_page(n) {
	$("input[name=page]").val(n);
	$("#search-form").submit();
}
</script>
</body>
</html>