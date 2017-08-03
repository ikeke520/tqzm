<?php
defined('STCMS_ROOT') or die('Access Deined!');
switch($_GET['step']) {
	case 'edit':
		include("./tpl_sale_worker_edit.php");
		exit;
	break;
	case 'add':
		include("./tpl_sale_worker_add.php");
		exit;
	break;
	case 'del':
		$mysql_class->delete("worker", "id IN ('".implode("','", explode(",", trim($_GET['id'])))."')");
		if($idarray = explode(",", $_GET['id'])) {
			$upload_root = check_dir(check_dir(dirname(STCMS_ROOT)). "attachment/");
			foreach($idarray as $tmp) {
				if(!empty($tmp)) {
					remove_file($upload_root."worker/{$tmp}/");
				}
			}
		}
		adminlog("删除业务员：".trim($_GET['id']));
		header("Location: ./?ac=sale&do=worker");
		exit;
	break;
	case 'overview':
		include("./tpl_sale_worker_overview.php");
		exit;
	break;
	case 'list':
		include("./tpl_sale_worker_list.php");
		exit;
	break;
}

$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$where = "1";
if(empty($keyword) == false) {
	$where .= " AND (realname LIKE '%".$keyword."%' OR address LIKE '%".$keyword."%' OR idno LIKE '%".$keyword."%')";
}
$total_records = $mysql_class->num_table("worker", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->fetch_all("SELECT u.*, w.* FROM {$config['db_prefix']}worker AS w LEFT JOIN {$config['db_prefix']}users AS u ON u.id = w.uid".($where ? " WHERE {$where}" :"")." ORDER BY w.id DESC LIMIT ".($page_class->page-1)*$per_page.", ".$per_page);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>业务员管理__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=sale">销售管理</a>&nbsp;&raquo;&nbsp;业务员管理</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=sale">销售概览</a></li>
				<li class="on"><a href="./?ac=sale&do=worker">业务员管理</a></li>
                <li><a href="./?ac=sale&do=salary">激励机制</a></li>
                <li><a href="./?ac=sale&do=apply">大客户留言</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li class="on"><a href="./?ac=sale&do=worker">业务员列表</a></li>
						<li><a href="./?ac=sale&do=worker&step=add">新增业务员</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="sale" />
                    <input type="hidden" name="do" value="worker" />
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
							<th>选择</th>
							<th>ID</th>
							<th>业务员姓名</th>
							<th>微信昵称</th>
							<th>身份证号码</th>
							<th>手机号码</th>
							<th>操作</th>
						</tr>
<?php
if($records) {
	foreach($records as $list) {
?>
						<tr>
							<td><input type="checkbox" name="id" value="<?php echo $list['id'];?>"></td>
							<td><?php echo $list['id'];?></td>
							<td><a href="./?ac=sale&do=worker&step=edit&id=<?php echo $list['id'];?>"><?php echo $list['realname'];?></a></td>
							<td><a href="./?ac=user&do=edit&id=<?php echo $list['uid'];?>"><?php echo $list['nickname'];?></a></td>
							<td><?php echo $list['idno'];?></td>
							<td><?php echo $list['phone'];?></td>
							<td><a href="./?ac=sale&do=worker&step=overview&id=<?php echo $list['id'];?>">销售业绩</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="./?ac=sale&do=worker&step=edit&id=<?php echo $list['id'];?>">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;<a onClick="SU.dialog({title:'操作确认', 'msg':'你确定要删除该业务员吗？', cb:function(){location='./?ac=sale&do=worker&step=del&id=<?php echo $list['id']; ?>';}});" href="javascript:">删除</a></td>
						</tr>
<?php
	}
}
?>
						<tr>
							<td colspan="11" class="tl"><a href="javascript:void(0);" onclick="check_all('listform', 'id');" class="button"><span>全选</span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="anti_all('listform', 'id')" class="button"><span>反选</span></a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除这些业务员吗？', cb:function(){location='./?ac=sale&do=worker&step=del&id='+get_all_value('listform', 'id', ',');}});" class="button"><span>删除</span></a></td>
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
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/su.js"></script>
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