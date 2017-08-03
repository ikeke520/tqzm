<?php
defined('STCMS_ROOT') or die('Access Deined!');
if($_GET['step'] == 'edit') {
	include("./tpl_index_category_edit.php");
	exit;
} else if($_GET['step'] == "add") {
	include("./tpl_index_category_add.php");
	exit;
} else if($_GET['step'] == "del") {
	$thiscategory = $mysql_class->select("category", "*", "id IN ('".implode("','", explode(",", trim($_GET['id'])))."')");
	if($thiscategory) {
		foreach($thiscategory as $list) {
			remove_file(check_dir(dirname(STCMS_ROOT))."attachment/category/{$list['id']}");
		}
	}
	$mysql_class->delete("category", "id IN ('".implode("','", explode(",", trim($_GET['id'])))."')");
	adminlog("删除活动分类：".trim($_GET['id']));
	header("Location: ./?ac=index&do=category");
	exit;
} else if($_GET['step'] == "upload") {
	$key = trim($_GET['key']);
	$stamp = trim($_GET['stamp']);
	if($key != md5($stamp . $config['syscode'])) {
		exit(json_encode(array("error"=>1, "msg"=>"invalide token")));
	}
	$upload_root = check_dir(check_dir(dirname(STCMS_ROOT)). "attachment/");
	$url_root = check_dir($config['web_url'] ."attachment/");
	
	$filesrc = $_FILES['file'];
	if(!is_uploaded_file($filesrc['tmp_name'])) {
		exit(json_encode(array("error"=>1, "msg"=>"未找到可以上传的文件。")));
	}
	if(in_array(get_file_ext($filesrc['name']), array("exe", "php", "jsp", "asp", "dll", "aspx", "bat"))) {
		exit(json_encode(array("error"=>1, "msg"=>"上传的文件不在允许的类型中。")));
	}
	
	$path_subfix = "temp/".date('ymd').date('His').rand(1000, 9999).".".get_file_ext($filesrc['name']);
	@mkdirs(dirname($upload_root.$path_subfix));
	if(@move_uploaded_file($filesrc['tmp_name'], $upload_root.$path_subfix)) {
		imgresize($upload_root . $path_subfix, 97, 91);
		exit(json_encode(array(
			'filename'=>add_slashes($filesrc['name']),
			'url'=>$url_root . $path_subfix,
			'size'=>get_size($filesrc['size']),
		)));
	} else {
		exit(json_encode(array("error"=>1, "msg"=>"上传文件失败，可能是目的文件夹没有写入权限所致。")));
	}
}
$category = $mysql_class->select("category", "*", false, "index_order ASC");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>活动分类__后台管理中心__<?php echo $config['seo_title'];?></title>
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
                <li class="on"><a href="./?ac=index&do=category">活动分类</a></li>
                <li><a href="./?ac=index&do=tag">活动标签</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">			
			<div class="thead">
				<a href="./?ac=index&do=category&step=add" class="button"><span>添加分类</span></a>
			</div>
			<form id="listform">
				<table style="margin-top:-1px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table" border="0">
					<tbody>
					<tr>
						<th>选择</th>
						<th>ID</th>
						<th>分类名称</th>
                        <th>排列顺序</th>
                        <th>操作</th>
					</tr>
<?php
if($category) {
	foreach($category as $list) {
?>
					<tr>
						<td><input type="checkbox" name="id" value="<?php echo $list['id'];?>"></td>
						<td><?php echo $list['id'];?></td>
						<td><a href="./?ac=index&do=category&step=edit&id=<?php echo $list['id'];?>"><?php echo $list['name'];?></a></td>
						<td><?php echo $list['index_order'];?></td>
                        <td><a href="./?ac=index&do=category&step=edit&id=<?php echo $list['id'];?>">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除该分类吗？', cb:function(){location='./?ac=index&do=category&step=del&id=<?php echo $list['id'];?>';}});" href="javascript:">删除</a></td>
					</tr>
<?php
	}
}
?>
					<tr>
                        <td colspan="11" class="tl"><a href="javascript:void(0);" onclick="check_all('listform', 'id');" class="button"><span>全选</span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="anti_all('listform', 'id')" class="button"><span>反选</span></a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除这些分类吗？', cb:function(){location='./?ac=index&do=category&step=del&id='+get_all_value('listform', 'id', ',');}});" class="button"><span>删除</span></a></td>
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