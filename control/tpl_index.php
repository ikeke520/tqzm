<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thismodel = "activity";
$do = trim($_GET['do']);
switch($do) {
	case 'edit':
		include("./tpl_index_edit.php");
		exit;
	break;
	case 'add':
		include("./tpl_index_add.php");
		exit;
	break;
	case 'category':
		include("./tpl_index_category.php");
		exit;	
	break;
	case 'tag':
		include("./tpl_index_tag.php");
		exit;
	break;
	case 'uploadimage':
		include("./tpl_index_uploadimage.php");
		exit;	
	break;
	case 'douploadimage':
		$key = trim($_GET['key']);
		$stamp = trim($_GET['stamp']);
		if($key != md5($stamp . $config['syscode'])) {
			exit(json_encode(array("error"=>1, "msg"=>"invalide token")));
		}
		$thisactivity = $mysql_class->select_one("activity", "*", array('id'=>intval($_GET['id'])));
		if(empty($thisactivity)) {
			exit(json_encode(array("error"=>1, "msg"=>"未找到指定活动。")));
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
		$path_subfix = "activity/{$thisactivity['id']}/".date('ymd').date('His').rand(100, 999).".".get_file_ext($filesrc['name']);
		@mkdirs(dirname($upload_root.$path_subfix));
		$index_order = $mysql_class->get_field_value("activity_picture", "max(index_order)", array("aid"=>$thisactivity['id']));
		if(@move_uploaded_file($filesrc['tmp_name'], $upload_root.$path_subfix)) {
			imgresize($upload_root . $path_subfix, 750, 400);
			$mysql_class->insert("activity_picture", array("aid"=>$thisactivity['id'], "url"=>$url_root.$path_subfix, 'index_order'=>intval($index_order)+1));
			exit(json_encode(array(
				'fid'=>$mysql_class->insert_id(),
				'filename'=>add_slashes($filesrc['name']),
				'url'=>$url_root . $path_subfix,
				'size'=>get_size($filesrc['size']),
				'index_order'=>$index_order+1
			)));
		} else {
			exit(json_encode(array("error"=>1, "msg"=>"上传文件失败，可能是目的文件夹没有写入权限所致。")));
		}
	break;
	case 'delimage':
		$thisimage = $mysql_class->select_one("activity_picture", "*", array("id"=>intval($_GET['id'])));
		if(empty($thisimage)) {
			exit(json_encode(array("error"=>1, "msg"=>"系统未找到指定的图片。")));
		}
		remove_attachment($thisimage['url']);
		$mysql_class->delete("activity_picture", array("id"=>$thisimage['id']));
		adminlog("删除活动图片：".$thisimage['id']);
		exit(json_encode(array("error"=>0)));
	break;
	case 'setindexorder':
		$thisimage = $mysql_class->select_one("activity_picture", "*", array("id"=>intval($_GET['id'])));
		if(empty($thisimage)) {
			exit(json_encode(array("error"=>1, "msg"=>"系统未找到指定的图片。")));
		}
		$mysql_class->update("activity_picture", array('index_order'=>intval($_POST['index_order'])), array('id'=>$thisimage['id']));
		exit(json_encode(array("error"=>0)));
	break;
	case 'del':
		$thisactivity = $mysql_class->select("activity", "id", "id IN ('".implode("','", explode(",", trim($_GET['id'])))."')");
		if($thisactivity) {
			foreach($thisactivity as $tmp) {
				remove_file(check_dir(dirname(STCMS_ROOT))."attachment/activity/{$tmp['id']}/");
			}
		}
		$mysql_class->delete("activity_picture", "aid IN ('".implode("','", get_id_array($thisactivity))."')");
		$mysql_class->delete("users_fav", "aid IN ('".implode("','", get_id_array($thisactivity))."')");
		$mysql_class->delete("tag_list", "aid IN ('".implode("','", get_id_array($thisactivity))."')");
		$mysql_class->delete("users_auth", "aid IN ('".implode("','", get_id_array($thisactivity))."')");
		$mysql_class->delete("activity", "id IN ('".implode("','", get_id_array($thisactivity))."')");
		adminlog("删除活动：".trim($_GET['id']));
		header("Location: ./?ac=index");
		exit;
	break;
	case 'applylist':
		include("./tpl_index_applylist.php");
		exit;
	break;
}

$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$where = "1";
if(intval($_GET['cid'])) {
	$cid = intval($_GET['cid']);
	$where .= " AND cid ='{$cid}'";
}
if(empty($keyword) == false) {
	$where .= " AND title LIKE '%".$keyword."%'";
}
$total_records = $mysql_class->num_table("activity", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select("activity", "*", $where, "id DESC", array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>活动管理__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;活动管理</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li class="on"><a href="./?ac=index">活动列表</a></li>
				<li><a href="./?ac=index&do=add">新增活动</a></li>
                <li><a href="./?ac=index&do=category">活动分类</a></li>
                <li><a href="./?ac=index&do=tag">活动标签</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="index" />
					<input type="hidden" name="tid" value="<?php echo $tid;?>">
					关键词：
					<input class="input" type="text" name="keyword" style="width:80px;" value="<?php echo $keyword; ?>" />
					&nbsp;&nbsp;分类：
					<select name="cid">
                    	<option value="0">请选择分类</option>
<?php
$category = $mysql_class->select("category", "*", false, array("index_order ASC"));
if($category) {
	foreach($category as $list) {
?>
                        <option value="<?php echo $list['id'];?>"><?php echo $list['name'];?></option>
<?php
	}
}
?>
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
							<th>选择</th>
							<th>ID</th>
							<th>活动主题</th>
							<th>类别</th>
							<th>活动时间</th>
							<th>单次费用</th>
                            <th>状态</th>
							<th>评论</th>
							<th>热度</th>
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
							<td style="max-width:15%;"><a href="./?ac=index&do=edit&id=<?php echo $list['id'];?>"><?php echo $list['title'];?></a></td>
							<td><a href="./?ac=index&cid=<?php echo $list['cid'];?>"><?php echo $list['cname'];?></a></td>
							<td><?php echo $list['date'];?>（<?php echo $list['timestr'];?>）</td>
							<td><span class="red">¥<?php echo $list['price'];?></span></td>
							<td><a href="./?ac=index&do=applylist&id=<?php echo $list['id'];?>"><?php if($list['status']){?><span class="red">已关闭</span><?php } else {?><?php if($list['person_num'] - $list['in_num'] < 1) {?>报满<?php } else {?><?php if($list['date'] <= date('Y-m-d')){?>过期<?php } else {?><span class="green">正在报名</span><?php }}}?></a>（<?php echo $list['in_num'];?>）</td>
                            <td><a href="./?ac=comment&aid=<?php echo $list['id'];?>"><?php echo $mysql_class->num_table("comment", array("aid"=>$list['id']));?></a></td>
							<td><span class="red"><?php echo $list['hit'];?></span></td>
							<td><?php echo $list['pubtime'];?></td>
							<td><a href="./?ac=index&do=uploadimage&id=<?php echo $list['id'];?>">活动图片</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="./?ac=index&do=edit&id=<?php echo $list['id'];?>">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;<a onClick="SU.dialog({title:'操作确认', 'msg':'你确定要删除该活动吗？', cb:function(){location='./?ac=index&do=del&id=<?php echo $list['id']; ?>';}});" href="javascript:">删除</a></td>
						</tr>
<?php
	}
}
?>
						<tr>
							<td colspan="11" class="tl"><a href="javascript:void(0);" onclick="check_all('listform', 'id');" class="button"><span>全选</span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="anti_all('listform', 'id')" class="button"><span>反选</span></a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除这些活动吗？', cb:function(){location='./?ac=index&do=del&id='+get_all_value('listform', 'id', ',');}});" class="button"><span>删除</span></a></td>
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
	$("select[name=cid]").val("<?php echo intval($cid);?>");
});
function go_page(n) {
	$("input[name=page]").val(n);
	$("#search-form").submit();
}
</script>
</body>
</html>