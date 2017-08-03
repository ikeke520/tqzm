<?php
defined('STCMS_ROOT') or die('Access Deined!');
switch($_GET['step']) {
	case 'added':
		$name = htmlspecialchars(trim($_POST['name']));
		if(empty($name)) {
			exit(json_encode(array('error'=>1, 'msg'=>"分类名称不能为空。")));
		}
		$mysql_class->insert("tag", array('name'=>$name));
		adminlog("添加活动标签：".$name);
		exit(json_encode(array('error'=>0)));
	break;
	case 'del':
		$mysql_class->delete("tag", "id IN ('".implode("','", explode(",", trim($_GET['id'])))."')");
		adminlog("删除活动标签：".trim($_GET['id']));
		header("Location: ./?ac=index&do=tag");
	exit;
	break;
	case 'edited':
		$name = htmlspecialchars(trim($_POST['name']));
		$id = intval($_POST['id']);
		if(empty($name)) {
			exit(json_encode(array('error'=>1, 'msg'=>"分类名称不能为空。")));
		}
		$mysql_class->update("tag", array('name'=>$name), array('id'=>$id));
		adminlog("修改活动标签：".$id);
		exit(json_encode(array('error'=>0)));
	break;
}
$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$where = "1";
if(empty($keyword) == false) {
	$where .= " AND name LIKE '%".$keyword."%'";
}
$total_records = $mysql_class->num_table("tag", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$tag = $mysql_class->select("tag", "*", $where, "id DESC", array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>活动标签__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=index">活动管理</a>&nbsp;&raquo;&nbsp;活动标签</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=index">活动列表</a></li>
				<li><a href="./?ac=index&do=add">新增活动</a></li>
                <li><a href="./?ac=index&do=category">活动分类</a></li>
                <li class="on"><a href="./?ac=index&do=tag">活动标签</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">			
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="index" />
					<input type="hidden" name="do" value="tag" />
					关键词：
					<input class="input" type="text" name="keyword" style="width:80px;" value="<?php echo $keyword; ?>" />
					&nbsp;&nbsp;分页：
					<select class="select" name="per_page">
						<option value="20">20条每页</option>
						<option value="50">50条每页</option>
						<option value="100">100条每页</option>
					</select>
					&nbsp;&nbsp;<a href="javascript:void(0)" onclick="$('#search-form').submit();" class="button">搜索</a>
					&nbsp;&nbsp;<input type="hidden" name="page" value="1" />
                	<a href="javascript:" class="button" onClick="add();"><span>添加标签</span></a>
				</form>
			</div>
            <form id="listform">
				<table style="margin-top:-1px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table" border="0">
					<tbody>
					<tr>
						<th>选择</th>
						<th>ID</th>
						<th>标签名称</th>
                        <th>活动数量</th>
                        <th>操作</th>
					</tr>
<?php
if($tag) {
	foreach($tag as $list) {
?>
					<tr>
						<td><input type="checkbox" name="id" value="<?php echo $list['id'];?>"></td>
						<td><?php echo $list['id'];?></td>
						<td><a href="javascript:" onClick="edit('<?php echo $list['id'];?>', '<?php echo $list['name'];?>');"><?php echo $list['name'];?></a></td>
						<td><?php echo $mysql_class->num_table("tag_list", array("tid"=>$list['id']));?></td>
                        <td><a onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除该标签吗？', cb:function(){location='./?ac=index&do=tag&step=del&id=<?php echo $list['id'];?>';}});" href="javascript:">删除</a></td>
					</tr>
<?php
	}
}
?>
                    <tr>
                        <td colspan="11" class="tl"><a href="javascript:void(0);" onclick="check_all('listform', 'id');" class="button"><span>全选</span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="anti_all('listform', 'id')" class="button"><span>反选</span></a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除这些标签吗？', cb:function(){location='./?ac=index&do=tag&step=del&id='+get_all_value('listform', 'id', ',');}});" class="button"><span>删除</span></a></td>
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

function add(pid) {
	SU.dialog({title:"添加标签", msg:"<div style='padding:20px 0;'>标签名称：<input name='catname' type='text' class='input'>&nbsp;&nbsp;<a href='javascript:' onclick='doadd();' class='button'><span>添加</span></a></div>"});
}

function doadd(pid) {
	var catname = $('input[name=catname]').val();
	if(empty(catname)) {
		alert("标签名称不能为空。");
		$('input[name=catname]').focus();
		return false;
	} else {
		$.ajax({
			url:"./?ac=index&do=tag&step=added",
			type:"POST",
			data:"name="+catname,
			dataType:"json",
			success: function(data) {
				if(data.error != '0') {
					alert(data.msg);
				} else {
					SU.tip("添加成功。");
					setTimeout(function(){location.reload();}, 1003);
				}
			}
		});
	}
}

function edit(id, name) {
	SU.dialog({title:"修改标签", msg:"<div style='padding:20px 0;'>标签名称：<input name='catname' type='text' class='input' value='"+name+"'>&nbsp;&nbsp;<a href='javascript:' onclick='doedit("+id+");' class='button'><span>修改</span></a></div>"});
}

function doedit(id) {
	var catname = $('input[name=catname]').val();
	if(empty(catname)) {
		alert("标签名称不能为空。");
		$('input[name=catname]').focus();
		return false;
	} else {
		$.ajax({
			url:"./?ac=index&do=tag&step=edited",
			type:"POST",
			data:"name="+catname+"&id="+id,
			dataType:"json",
			success: function(data) {
				if(data.error != '0') {
					alert("修改失败。");
				} else {
					SU.tip("修改成功。");
					setTimeout(function(){location.reload();}, 1003);
				}
			}
		});
	}
}

function empty(str) {
	if(typeof str =="undefined") {
		return true;
	}
	str = str.replace(/^[\t\r\n\s]*/, '').replace(/[\r\t\s\n]*$/, '');
	if(str == '' || str == '0') {
		return true;
	} else {
		return false;
	}
}
</script>
</body>
</html>