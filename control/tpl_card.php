<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thismodel = "card";
$do = trim($_GET['do']);
switch($do) {
	case 'edit':
		include("./tpl_card_edit.php");
		exit;
	break;
	case 'add':
		include("./tpl_card_add.php");
		exit;
	break;
	case 'base':
		include("./tpl_card_base.php");
		exit;	
	break;
	case 'del':
		$mysql_class->update("card", array("is_del"=>1), "id IN ('".implode("','", explode(",", trim($_GET['id'])))."')");
/*		$thiscard = $mysql_class->select("card", "id IN ('".implode("','", explode(",", trim($_GET['id'])))."')");
		if($thiscard) {
			foreach($thiscard as $list) {
				remove_file(check_dir(dirname(STCMS_ROOT))."attachment/card/{$list['id']}");
			}
		}
*/		adminlog("删除了亲子卡（ID：".trim($_GET['id'])."）");
		header("Location: ./?ac=card");
		exit;
	break;
	case 'upload':
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
		$path_subfix = "temp/".date('ymd').date('His').rand(100, 999).".".get_file_ext($filesrc['name']);
		@mkdirs(dirname($upload_root.$path_subfix));
		if(@move_uploaded_file($filesrc['tmp_name'], $upload_root.$path_subfix)) {
			imgresize($upload_root . $path_subfix, 704, 384);
			exit(json_encode(array(
				'filename'=>add_slashes($filesrc['name']),
				'url'=>$url_root . $path_subfix,
				'size'=>get_size($filesrc['size']),
			)));
		} else {
			exit(json_encode(array("error"=>1, "msg"=>"上传文件失败，可能是目的文件夹没有写入权限所致。")));
		}
	break;
}

$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$where = "1 AND is_del = '0'";
if(empty($keyword) == false) {
	$where .= " AND (name LIKE '%".$keyword."%' OR price LIKE '%".$keyword."%')";
}
$total_records = $mysql_class->num_table("card", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select("card", "*", $where, "index_order ASC", array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>亲子卡管理__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;亲子卡管理</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li class="on"><a href="./?ac=card">亲子卡列表</a></li>
				<li><a href="./?ac=card&do=add">新增亲子卡</a></li>
                <li><a href="./?ac=card&do=base">实体亲子卡</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="card" />
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
							<th>名称</th>
							<th>类别</th>
							<th>价格</th>
							<th>参与活动次数</th>
                            <th>显示顺序</th>
							<th>添加时间</th>
							<th>操作</th>
						</tr>
<?php
if($records) {
	foreach($records as $list) {
?>
						<tr>
							<td><input type="checkbox" name="id" value="<?php echo $list['id'];?>"></td>
							<td><?php echo $list['id'];?></td>
							<td><a href="./?ac=card&do=edit&id=<?php echo $list['id'];?>"><?php echo $list['name'];?></a></td>
							<td><?php if($list['is_trial']) {?>体验卡<?php } else {?>普通卡<?php }?></td>
							<td><span class="red">¥<?php echo $list['price'];?></span></td>
							<td><?php echo $list['num'];?></td>
							<td><?php echo $list['index_order'];?></td>
							<td><?php echo $list['time'];?></td>
							<td><a href="./?ac=card&do=edit&id=<?php echo $list['id'];?>">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;<a onClick="SU.dialog({title:'操作确认', 'msg':'你确定要删除该亲子卡吗？', cb:function(){location='./?ac=card&do=del&id=<?php echo $list['id']; ?>';}});" href="javascript:">删除</a></td>
						</tr>
<?php
	}
}
?>
						<tr>
							<td colspan="11" class="tl"><a href="javascript:void(0);" onclick="check_all('listform', 'id');" class="button"><span>全选</span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="anti_all('listform', 'id')" class="button"><span>反选</span></a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除这些亲子卡吗？', cb:function(){location='./?ac=card&do=del&id='+get_all_value('listform', 'id', ',');}});" class="button"><span>删除</span></a></td>
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