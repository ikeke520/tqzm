<?php
defined('STCMS_ROOT') or die('Access Deined!');
switch($_GET['step']) {
	case 'added':
		$name = htmlspecialchars(trim($_POST['name']));
		if(empty($name)) {
			exit(json_encode(array('error'=>1, 'msg'=>"分类名称不能为空。")));
		}
		$mysql_class->insert("newscat", array('name'=>$name, "time"=>NOW));
		exit(json_encode(array('error'=>0)));
	break;
	case 'del':
		$mysql_class->delete("newscat", "id IN ('".implode("','", explode(",", trim($_GET['id'])))."')");
		adminlog("删除资讯分类：".trim($_GET['id']));
		header("Location: ./?ac=news&do=category");
	exit;
	break;
	case 'edited':
		$name = htmlspecialchars(trim($_POST['name']));
		$id = intval($_POST['id']);
		if(empty($name)) {
			exit(json_encode(array('error'=>1, 'msg'=>"分类名称不能为空。")));
		}
		$mysql_class->update("newscat", array('name'=>$name), array('id'=>$id));
		exit(json_encode(array('error'=>0)));
	break;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>资讯分类__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=news">资讯管理</a>&nbsp;&raquo;&nbsp;资讯分类</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=news">资讯列表</a></li>
				<li><a href="./?ac=news&do=add">发布资讯</a></li>
                <li class="on"><a href="./?ac=news&do=category">资讯分类</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">			
			<div class="thead">
				<form id="search-form" method="get" action="./">
                	<a href="javascript:" class="button" onClick="add();"><span>添加分类</span></a>
				</form>
			</div>
            <form id="listform">
				<table style="margin-top:-1px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table" border="0">
					<tbody>
					<tr>
						<th>选择</th>
						<th>ID</th>
						<th>分类名称</th>
                        <th>添加时间</th>
                        <th>操作</th>
					</tr>
<?php
$category = $mysql_class->select("newscat");
if($category) {
	foreach($category as $list) {
?>
					<tr>
						<td><input type="checkbox" name="id" value="<?php echo $list['id'];?>"></td>
						<td><?php echo $list['id'];?></td>
						<td><a href="javascript:" onClick="edit('<?php echo $list['id'];?>', '<?php echo $list['name'];?>');"><?php echo $list['name'];?></a></td>
						<td><?php echo $list['time'];?></td>
                        <td><a onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除该分类吗？', cb:function(){location='./?ac=news&do=category&step=del&id=<?php echo $list['id'];?>';}});" href="javascript:">删除</a></td>
					</tr>
<?php
	}
}
?>
                    <tr>
                        <td colspan="11" class="tl"><a href="javascript:void(0);" onclick="check_all('listform', 'id');" class="button"><span>全选</span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="anti_all('listform', 'id')" class="button"><span>反选</span></a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除这些分类吗？', cb:function(){location='./?ac=news&do=category&step=del&id='+get_all_value('listform', 'id', ',');}});" class="button"><span>删除</span></a></td>
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
	SU.dialog({title:"添加分类", msg:"<div style='padding:20px 0;'>分类名称：<input name='catname' type='text' class='input'>&nbsp;&nbsp;<a href='javascript:' onclick='doadd();' class='button'><span>添加</span></a></div>"});
}

function doadd(pid) {
	var catname = $('input[name=catname]').val();
	if(empty(catname)) {
		alert("分类名称不能为空。");
		$('input[name=catname]').focus();
		return false;
	} else {
		$.ajax({
			url:"./?ac=news&do=category&step=added",
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
	SU.dialog({title:"修改分类", msg:"<div style='padding:20px 0;'>分类名称：<input name='catname' type='text' class='input' value='"+name+"'>&nbsp;&nbsp;<a href='javascript:' onclick='doedit("+id+");' class='button'><span>修改</span></a></div>"});
}

function doedit(id) {
	var catname = $('input[name=catname]').val();
	if(empty(catname)) {
		alert("分类名称不能为空。");
		$('input[name=catname]').focus();
		return false;
	} else {
		$.ajax({
			url:"./?ac=news&do=category&step=edited",
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