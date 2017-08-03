<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thismodel = "activity";
$thisactivity = $mysql_class->select_one("activity", "*", array("id"=>intval($_GET['aid'])));
if(empty($thisactivity)) {
	halt("系统未找到活动。");
}
if($_GET['do'] == "del") {
	$mysql_class->delete("comment", "id IN ('".implode("','", explode(",", trim($_GET['id'])))."')");
	adminlog("删除了评论（".trim($_GET['id'])."）");
	header("Location: ./?ac=comment&aid=".$thisactivity['id']);
	exit;
} else if($_GET['do'] == "sethide") {
	$value = intval($_POST['value']);
	$mysql_class->update("comment", "is_hide='{$value}'", "id IN ('".implode("','", explode(",", trim($_POST['id'])))."')");
	adminlog("设置显示或隐藏评论（ID：".$_GET['id'].", 值：{$value}）");
	exit(json_encode(array("error"=>0)));
}
$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$where = "aid='{$thisactivity['id']}'";
if(intval($_GET['uid'])) {
	$uid = intval($_GET['uid']);
	$where .= " AND uid = '{$uid}'";
}
if(empty($keyword) == false) {
	$where .= " AND (uname LIKE '%".$keyword."%' OR content LIKE '%".$keyword."%')";
}
$total_records = $mysql_class->num_table("comment", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select("comment", "*", $where, "id DESC", array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>评论管理__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;评论管理</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=index">活动列表</a></li>
				<li><a href="./?ac=index&do=add">新增活动</a></li>
                <li><a href="./?ac=index&do=category">活动分类</a></li>
                <li><a href="./?ac=index&do=tag">活动标签</a></li>
				<li class="on"><a href="javascript:">活动评论</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="comment" />
					<input type="hidden" name="aid" value="<?php echo $thisactivity['id'];?>">
					<input type="hidden" name="uid" value="<?php echo $uid;?>">
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
							<th>用户昵称</th>
							<th>内容</th>
                            <th>状态</th>
							<th>发表时间</th>
							<th>操作</th>
						</tr>
<?php
if($records) {
	foreach($records as $list) {
?>
						<tr>
							<td><input type="checkbox" name="id" value="<?php echo $list['id'];?>"></td>
							<td><?php echo $list['id'];?></td>
							<td><a href="./?ac=comment&aid=<?php echo $thisactivity['id'];?>&uid=<?php echo $list['uid'];?>"><?php echo $list['uname'];?></a></td>
							<td class="gray" style="max-width:400px;"><?php echo $list['content'];?></td>
							<td><?php if($list['is_hide']) {?><span class="red">隐藏</span> / <a href="javascript:" onClick="sethide('<?php echo $list['id'];?>', '0')">显示</a><?php } else {?><span class="green">显示</span> / <a href="javascript:" onClick="sethide('<?php echo $list['id'];?>', '1')">隐藏</a><?php }?></td>
							<td><?php echo $list['time'];?></td>
							<td><a onClick="SU.dialog({title:'操作确认', 'msg':'你确定要删除该评论吗？', cb:function(){location='./?ac=comment&do=del&aid=<?php echo $thisactivity['id'];?>&id=<?php echo $list['id']; ?>';}});" href="javascript:">删除</a></td>
						</tr>
<?php
	}
}
?>
						<tr>
							<td colspan="11" class="tl"><a href="javascript:void(0);" onclick="check_all('listform', 'id');" class="button"><span>全选</span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="anti_all('listform', 'id')" class="button"><span>反选</span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a class="button" href="javascript:" onClick="sethide(get_all_value('listform', 'id', ','), '0')"><span>显示</span></a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:" class="button" onClick="sethide(get_all_value('listform', 'id', ','), '1')"><span>隐藏</span></a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除这些评论吗？', cb:function(){location='./?ac=comment&do=del&aid=<?php echo $thisactivity['id'];?>&id='+get_all_value('listform', 'id', ',');}});" class="button"><span>删除</span></a></td>
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
function sethide(id, value) {
	$.ajax({
		url:"./?ac=comment&do=sethide&aid=<?php echo $thisactivity['id'];?>",
		type:"POST",
		data:{"id":id, "value":value},
		dataType:"json",
		success: function(data) {
			if(data.error < 1) {
				SU.tip("设置成功。");
				setTimeout(function(){location.reload();}, 1003);
			} else {
				alert(data.msg);	
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