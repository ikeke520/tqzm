<?php defined('STCMS_ROOT') or die('Access Deined!');
$step = trim($_GET['step']);
switch($step) {
	case 'add':
		include("./tpl_setting_admin_add.php");
		exit;
	break;
	case 'added':
		$data['name'] = htmlspecialchars($_POST['name']);
		$password1 = trim($_POST['password1']);
		$password2 = trim($_POST['password2']);
		if(empty($data['name']) || empty($password1) || empty($password2)) {
			halt("用户名、密码应填写完整。");
		}
		if(strlen($password1) < 6) {
			halt("密码长度不得少于6位。");
		}
		if($password1 != $password2) {
			halt("两次输入的密码不一致。");
		}
		if($mysql_class->num_table("admin", array('name'=>$data['name'])) > 0) {
			halt("该管理员已经存在，请使用其他用户名。");
		}
		$data['pwd'] = md5(md5($password1));
		$mysql_class->insert("admin", $data);
		adminlog("添加了管理员：".$data['name']);
		header("Location: ./?ac=setting&do=admin");
	break;
	case 'edit':
		include("./tpl_setting_admin_edit.php");
		exit;
	break;
	case 'edited':
		$id = intval($_GET['id']);
		$thisadmin = $mysql_class->select_one("admin", "*", array('id'=>$id));
		if(empty($thisadmin)) {
			halt("系统未找到指定的管理员。");
		}
		$data['name'] = htmlspecialchars($_POST['name']);
		$password1 = trim($_POST['password1']);
		$password2 = trim($_POST['password2']);
		if(empty($data['name'])) {
			halt("用户名不能为空。");
		}
		if(strlen($password1) > 0) {
			if(strlen($password1) < 6) {
				halt("密码长度不得少于6位。");
			}
			if($password1 != $password2) {
				halt("两次输入的密码不一致。");
			}
			$data['pwd'] = md5(md5($password1));
		}
		if($mysql_class->num_table("admin", "name='".$data['name']."' AND id != '".$thisadmin['id']."'") > 0) {
			halt("该管理员已经存在，请使用其他用户名。");
		}
		$mysql_class->update("admin", $data, array('id'=>$thisadmin['id']));
		adminlog("修改了管理员：".$data['name']);
		header("Location: ./?ac=setting&do=admin");
	break;
	case 'del':
		$mysql_class->delete("admin", "id IN ('".implode("','", explode(',', trim($_GET['id'])))."')");
		adminlog("删除了管理员：".trim($_GET['id']));
		header("Location: ./?ac=setting&do=admin");
		exit;
	break;
}

$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$cid = intval($_GET['cid']);
$where = "1";
if(empty($keyword) == false) {
	$where .= " AND (name LIKE '%".$keyword."%')";
}
$total_records = $mysql_class->num_table("admin", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select("admin", "*", $where, 'id DESC', array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>管理员设置__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=setting">系统设置</a>&nbsp;&raquo;&nbsp;管理员设置</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=setting">首选项</a></li>
				<li class="on"><a href="./?ac=setting&do=admin">管理员设置</a></li>
                <li><a href="./?ac=setting&do=log">安全日志</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="setting" />
					<input type="hidden" name="do" value="admin" />
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
					&nbsp;&nbsp;<a href="./?ac=setting&do=admin&step=add" class="button"><span>新增管理员</span></a>
				</form>
			</div>
			<form id="listform">
				<table style="margin-top:-1px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table" border="0">
					<tbody>
						<tr>
							<th>选择</th>
							<th>ID</th>
							<th>管理员</th>
							<th>最后登录</th>
							<th>登录次数</th>
							<th>操作</th>
						</tr>
<?php
if($records) {
	foreach($records as $list) {
?>
						<tr>
							<td><input type="checkbox" name="id" value="<?php echo $list['id'];?>"></td>
							<td><?php echo $list['id'];?></td>
							<td><a href="./?ac=setting&do=admin&step=edit&id=<?php echo $list['id'];?>"><?php echo $list['name'];?></a></td>
							<td><?php echo $list['last_login'];?></td>
							<td><span class="red"><?php echo $list['times'];?></span></td>
							<td><a href="./?ac=setting&do=admin&step=edit&id=<?php echo $list['id'];?>">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;<a onClick="SU.dialog({title:'操作确认', 'msg':'你确定要删除该管理员吗？', cb:function(){location='./?ac=setting&do=admin&step=del&id=<?php echo $list['id']; ?>';}});" href="javascript:">删除</a></td>
						</tr>
<?php
	}
}
?>
						<tr>
							<td colspan="11" class="tl"><a href="javascript:void(0);" onclick="check_all('listform', 'id');" class="button"><span>全选</span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="anti_all('listform', 'id')" class="button"><span>反选</span></a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除这些管理员吗？', cb:function(){location='./?ac=setting&do=admin&step=del&id='+get_all_value('listform', 'id', ',');}});" class="button"><span>删除</span></a></td>
						</tr>
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
<script type="text/javascript" src="../js/su.js"></script> 
<script type="text/javascript" src="../js/common.js"></script> 
<script type="text/javascript">
$(document).ready(function(e) {
	$("select[name=per_page]").val("<?php echo $per_page;?>");
});
function go_page(n) {
	$("input[name=page]").val(n);
	$("#search-form").submit();
}
</script>
</body>
</html>