<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thismodel = "user";
if($_GET['do'] == "edit") {
	include("./tpl_user_edit.php");
	exit;
} else if($_GET['do'] == "analysis") {
	include("./tpl_user_analysis.php");
	exit;
} else if($_GET['do'] == "crm") {
	include("./tpl_user_crm.php");
	exit;
}
$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$where = "1";
if(empty($keyword) == false) {
	$where .= " AND (nickname LIKE '%".$keyword."%' OR city LIKE '%".$keyword."%' OR province LIKE '%".$keyword."%' OR country LIKE '%".$keyword."%')";
}
$total_records = $mysql_class->num_table("users", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select("users", "*", $where, "id DESC", array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>会员管理__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;用户管理</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li class="on"><a href="./?ac=user">用户列表</a></li>
				<li><a href="./?ac=user&do=analysis">用户分析</a></li>
                <li><a href="./?ac=user&do=crm">CRM管理</a></li>
                <li><a href="./?ac=sale&do=index&step=rank">会员消费排行</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="user" />
					关键词：
					<input class="input" type="text" name="keyword" style="width:80px;" value="<?php echo $keyword; ?>" />
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
							<th>用户昵称</th>
							<th>性别</th>
							<th>地区</th>
                            <th>积分</th>
							<th>最后IP</th>
							<th>最后时间</th>
							<th>注册时间</th>
							<th>是否关注</th>
							<th>操作</th>
						</tr>
<?php
if($records) {
	foreach($records as $list) {
?>
						<tr>
							<td><?php echo $list['id'];?></td>
							<td><a href="./?ac=user&do=edit&id=<?php echo $list['id'];?>"><?php echo $list['nickname'];?></a></td>
							<td><?php if($list['sex'] == 0 ){?>未知<?php }else if($list['sex'] == 1){?>男<?php } else if($list['sex'] ==2){?>女<?php }?></td>
							<td><?php echo $list['country'];?> <?php echo $list['province'];?> <?php echo $list['city'];?></td>
							<td><?php echo $list['return_coin'];?></td>
                            <td><?php echo $list['ip'];?></td>
							<td><?php echo $list['log_time'];?></td>
							<td><?php echo $list['reg_time'];?></td>
							<td><?php if($list['is_reg'] == 0 ){?><span class="red">未关注</span><?php } else {?>已关注<?php }?></td>
							<td><a href="./?ac=user&do=edit&id=<?php echo $list['id'];?>">查看</a></td>
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
});
var thisuid = '';
function combine_teacher(uid) {
	thisuid = uid;
	SU.dialog({
		url:"./?ac=index&do=getea&func=select_teacher",
		title:"选择关联导师"
	});
}
function select_teacher(data) {
	$.ajax({
		url:"./?ac=user&do=combine&uid="+thisuid,
		type:"GET",
		data:{"tid":data['id']},
		dataType:"json",
		success:function(fd) {
			if(fd.error < 1) {
				SU.tip("关联成功。");
				SU.rd();
			} else {
				SU.tip(fd.msg);
			}
		}
	});
}
function go_page(n) {
	$("input[name=page]").val(n);
	$("#search-form").submit();
}
</script>
</body>
</html>