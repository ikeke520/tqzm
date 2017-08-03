<?php
defined('STCMS_ROOT') or die('Access Deined!');
include(STCMS_ROOT."wxconfig.php");
$thismodel = "connect";
switch($_GET['do']) {
	case 'menu':
		include("./tpl_connect_menu.php");
		exit;
	break;
	case 'post':
		include("./tpl_connect_post.php");
		exit;
	break;
	case 'tplmsg':
		include("./tpl_connect_tplmsg.php");
		exit;
	break;
	case 'del':
		$this_message = $mysql_class->select("message", "*", "id IN ('".implode("','", explode(",", trim($_GET['id'])))."')");
		if($this_message) {
			foreach($this_message as $tmp) {
				remove_file(check_dir(dirname(STCMS_ROOT))."attachment/message/{$tmp['id']}/");
			}
		}
		$mysql_class->delete("message", "id IN ('".implode("','", explode(",", trim($_GET['id'])))."')");
		adminlog("删除了微信消息（ID：".trim($_GET['id'])."）");
		header("Location: ./?ac=connect");
		exit;
	break;
	case 'reply':
		$thismessage = $mysql_class->select_one("message", "*", array("id"=>intval($_GET['id'])));
		if(empty($thismessage)) {
			exit(json_encode(array("error"=>1, "msg"=>"系统未找到自定的消息。")));
		}
		if(strtotime($thismessage['time']) < strtotime("-2 days")) {
			exit(json_encode(array("error"=>1, "msg"=>"系统无法回复已经超过48小时的消息。")));
		}
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".get_access_token();
		$data = array("touser"=>$thismessage['openid'], "msgtype"=>"text", "text"=>array("content"=>urlencode($_POST['reply'])));
		$response = json_decode(http_request($url, urldecode(json_encode($data))));
		adminlog("回复了微信消息（ID：".trim($_GET['id'])."）");
		if($response->errcode == "0") {
			exit(json_encode(array("error"=>0)));
		} else {
			exit(json_encode(array("error"=>1, "msg"=>$response->errmsg)));
		}
	break;
}
$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$where = "1";
if(intval($_GET['uid'])) {
	$uid = intval($_GET['uid']);
	$where .= " AND uid = '{$uid}'";
}
if(empty($keyword) == false) {
	$where .= " AND (content LIKE '%".$keyword."%' OR nickname LIKE '%".$keyword."%')";
}
$total_records = $mysql_class->num_table("message", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select("message", "*", $where, "id DESC", array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>消息管理__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;微信接口</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li class="on"><a href="./?ac=connect">消息管理</a></li>
				<li><a href="./?ac=connect&do=menu">菜单管理</a></li>
				<li><a href="./?ac=connect&do=post">群发消息</a></li>
				<li><a href="./?ac=connect&do=tplmsg">模板消息</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="connect" />
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
							<th>消息类型</th>
							<th>内容</th>
							<th>时间</th>
							<th>操作</th>
						</tr>
<?php
if($records) {
	foreach($records as $list) {
?>
						<tr>
							<td><input type="checkbox" name="id" value="<?php echo $list['id'];?>"></td>
							<td><?php echo $list['id'];?></td>
							<td><a href="./?ac=connect&uid=<?php echo $list['uid'];?>"><?php echo $list['nickname'];?></a></td>
							<td><?php echo $list['type'];?></td>
							<td style="max-width:300px;"><?php if(in_array($list['type'], array("image", "video", "voice"))){?><a href="<?php echo $list['content'];?>" target="_blank"><?php echo $list['content'];?></a><?php } else {?><?php echo $list['content'];?><?php }?></td>
							<td><?php echo $list['time'];?></td>
							<td><a href="javascript:" onClick="reply(<?php echo $list['id'];?>);">回复</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onClick="SU.dialog({title:'操作确认', 'msg':'你确定要删除该消息吗？', cb:function(){location='./?ac=connect&do=del&id=<?php echo $list['id']; ?>';}});" href="javascript:">删除</a></td>
						</tr>
<?php
	}
}
?>
						<tr>
							<td colspan="11" class="tl"><a href="javascript:void(0);" onclick="check_all('listform', 'id');" class="button"><span>全选</span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="anti_all('listform', 'id')" class="button"><span>反选</span></a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="SU.dialog({title:'操作确认', 'msg':'你确定要删除这些消息吗？', cb:function(){location='./?ac=connect&do=del&id='+get_all_value('listform', 'id', ',');}});" class="button"><span>删除</span></a></td>
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
function reply(id) {
	SU.dialog({
		title:"回复消息",
		msg:'<textarea data-id="'+id+'" class="textarea" style="width:400px; height:100px;" name="reply" placeholder="回复内容"></textarea>',
		cb:function() {
			post();
		}
	});
}
function post() {
	var id = $("textarea[name=reply]").data("id");
	if($("textarea[name=reply]").val() == "") {
		alert("请填写回复内容。");
		return;
	}
	$.ajax({
		url:"./?ac=connect&do=reply&id="+id,
		data:{"reply":$("textarea[name=reply]").val()},
		type:"POST",
		dataType:"json",
		success: function(data) {
			if(data.error > 0) {
				alert(data.msg);
			} else {
				SU.rd();
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