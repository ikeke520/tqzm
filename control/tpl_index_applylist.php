<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thisactivity = $mysql_class->select_one("activity", "*", array("id"=>intval($_GET['id'])));
if(empty($thisactivity)) {
	halt("系统未找到活动。");
}
if($_GET['step'] == 'show') {
	include("./tpl_index_applylist_show.php");
	exit;
}

$page = intval($_GET['page']);
$type = intval($_GET['type']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$where = "aid='{$thisactivity['id']}'";
if($type == 1) {
	$where .= " AND is_finish='1' AND is_cancel='0'";
} else if($type == 2) {
	$where .= " AND is_finish='0'";
} else if($type == 3) {
	$where .= " AND is_cancel='1'";
}
$total_records = $mysql_class->num_table("users_auth", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select("users_auth", "*", $where, "id DESC", array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>报名列表__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=index">活动管理</a>&nbsp;&raquo;&nbsp;活动分类</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=index">活动列表</a></li>
				<li><a href="./?ac=index&do=add">新增活动</a></li>
                <li><a href="./?ac=index&do=category">活动分类</a></li>
                <li><a href="./?ac=index&do=tag">活动标签</a></li>
				<li class="on"><a href="javascript:">修改活动</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li><a href="./?ac=index&do=edit&id=<?php echo $thisactivity['id'];?>">基本信息</a></li>
						<li><a href="./?ac=index&do=uploadimage&id=<?php echo $thisactivity['id'];?>">活动图片</a></li>
                        <li class="on"><a href="./?ac=index&do=applylist&id=<?php echo $thisactivity['id'];?>">报名情况</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<div class="mt10">			
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="index" />
					<input type="hidden" name="do" value="applylist" />
					<input type="hidden" name="id" value="<?php echo $thisactivity['id'];?>">
					&nbsp;&nbsp;状态：
					<select class="select" name="type">
						<option value="0">全部记录</option>
                        <option value="1">已完成报名</option>
						<option value="2">未完成报名</option>
						<option value="3">已取消报名</option>
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
						<tr>
							<th>ID</th>
							<th>小孩姓名</th>
							<th>性别</th>
							<th>出生日期</th>
							<th>区域</th>
                           	<th>身份证号</th>
                            <th>联系家长</th>
                            <th>家长身份证号</th>
							<th>电话</th>
                            <th>状态</th>
                            <th>报名时间</th>
						</tr>
					</tr>
<?php
if($records) {
	foreach($records as $list) {
		$tmpuser = $mysql_class->select_one("users", "id, nickname", array("id"=>$list['uid']));
		$tmpchild = $mysql_class->fetch($mysql_class->query("SELECT p.* FROM {$config['db_prefix']}users_person AS p RIGHT JOIN {$config['db_prefix']}users_auth_person AS a ON a.pid=p.id WHERE a.aid='{$list['id']}' AND a.is_adult='0'"));
		$tmpadult = $mysql_class->fetch($mysql_class->query("SELECT p.* FROM {$config['db_prefix']}users_person AS p RIGHT JOIN {$config['db_prefix']}users_auth_person AS a ON a.pid=p.id WHERE a.aid='{$list['id']}' AND a.is_adult='1'"));
?>
                    <tr>
                        <td><?php echo $list['id'];?></td>
                        <td><?php echo $tmpchild['name'];?></td>
                        <td><?php if($tmpchild['sex'] == 0 ){?>男<?php }else if($tmpchild['sex'] == 1){?>女<?php }?></td>
                        <td><?php echo $tmpchild['birthday'];?></td>
                        <td><?php echo $tmpchild['address'];?></td>
                        <td><?php echo $tmpchild['idno'];?></td>
                        <td><?php echo $tmpadult['name'];?>（<?php if($tmpadult['sex'] == 0 ){?>宝爸<?php }else if($tmpadult['sex'] == 1){?>宝妈<?php }?>）</td>
                        <td><?php echo $tmpadult['idno'];?></td>
                        <td><?php echo $tmpadult['phone'];?></td>
                        <td><?php if($list['is_cancel']) {?><span class="red">已取消</span><?php } else {?><?php if($list['is_finish']) {?><span class="green">已完成</span><?php } else {?><span class="gray">未完成</span><?php }}?></td>
                        <td><?php echo $list['time'];?></td>
                    </tr>
<?php
	}
}
?>
					<tr>
                        <td colspan="11" class="tl">总计有 <?php echo $total_records;?> 条记录</td>
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
function go_page(n) {
	$("input[name=page]").val(n);
	$("#search-form").submit();
}
</script>
</body>
</html>