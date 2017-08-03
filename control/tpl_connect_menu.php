<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thismodel = "connect";
function format_data($cat) {
	if(is_array($cat) && !empty($cat)) {
		foreach($cat as $val) {
			if($val['sub']) {
				unset($val['url']);
				$val['sub_button'] = format_data($val['sub']);
				unset($val['sub']);
			} else {
				$val['type'] = "view";
			}
			$val['name'] = urlencode($val['name']);
			$val['url'] = urlencode($val['url']);
			$result[] = $val;
		}
		return $result;
	}
}
if($_GET['step'] == "update") {
	$cat = $_POST['cat'];
	if($cat) {
		$data = urldecode(json_encode(array("button"=>format_data($cat))));
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".get_access_token();
		$response = http_request($url, $data);
		$response = json_decode($response);
		if($response->errcode == "0") {
			adminlog("修改了微信菜单");
			header("Location: ./?ac=connect&do=menu"); exit;
		} else {
			halt("更新失败，请稍后再试。".var_export($response, true));
		}
	}
}
$url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".get_access_token();
$response = json_decode(http_request($url));
$menulist = $response->menu->button;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>菜单管理__后台管理中心__<?php echo $config['seo_title'];?></title>
<link href="../css/control.css" type="text/css" rel="stylesheet">
<style>
.catlist ul{ list-style:none;}
.catlist ul { padding-left:30px !important;}
.catlist ul li { padding:10px 0;}
</style>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=connect">微信接口</a>&nbsp;&raquo;&nbsp;菜单管理</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=connect">消息管理</a></li>
				<li class="on"><a href="./?ac=connect&do=menu">菜单管理</a></li>
				<li><a href="./?ac=connect&do=post">群发消息</a></li>
				<li><a href="./?ac=connect&do=tplmsg">模板消息</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
			<form id="catform" method="post" action="./?ac=connect&do=menu&step=update">		
				<div class="catlist">
					<ul>
<?php
if($menulist) {
	foreach($menulist as $key => $list) {
?>
						<li class="item"><input type="text" class="input" style="width:100px;" name="cat[<?php echo $key;?>][name]" value="<?php echo $list->name;?>" placeholder="菜单名称"> - <input class="input" type="text" name="cat[<?php echo $key;?>][url]" value="<?php echo $list->url;?>" placeholder="链接地址">&nbsp;&nbsp;<a href="javascript:" onclick="removecat(this);">×删除</a>
							<ul>
<?php
if($list->sub_button) {
	foreach($list->sub_button as $ord => $sub) {
?>
								<li><input type="text" class="input" style="width:100px;" name="cat[<?php echo $key;?>][sub][<?php echo $ord;?>][name]" value="<?php echo $sub->name;?>" placeholder="菜单名称"> - <input class="input" type="text" name="cat[<?php echo $key;?>][sub][<?php echo $ord;?>][url]" value="<?php echo $sub->url;?>" placeholder="链接地址">&nbsp;&nbsp;<a href="javascript:" onclick="removecat(this);">×删除</a></li>
<?php
	}
}
?>
								<li><a href="javascript:" onClick="addcat(this);">+添加二级菜单</a></li>
							</ul>
						</li>
<?php
	}
}
?>
						<li><a href="javascript:" onClick="addcat(this);">+添加一级菜单</a></li>
					</ul>
				</div>
				<div class="mt10">
					<a href="javascript:" onclick="$('#catform').submit();" class="submit"><span>确认提交</span></a>
				</div>
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
function addcat(t) {
	var dom = $(t);
	if($(dom).parentsUntil(".catlist").length > 2) {
		if($(dom).parent().siblings().length > 4) {
			SU.tip("二级菜单不能超过5个。");
			return;
		}
		$('<li class="item"><input type="text" class="input" style="width:100px;" name="cat['+($(dom).parent().parent().parent().index())+'][sub]['+($(dom).parent().parent().children().length - 1)+'][name]" placeholder="菜单名称"> - <input class="input" type="text" name="cat['+($(dom).parent().parent().parent().index())+'][sub]['+($(dom).parent().parent().children().length - 1)+'][url]" placeholder="链接地址">&nbsp;&nbsp;<a href="javascript:" onclick="removecat(this);">×删除</a></li>').insertBefore($(dom).parent());
	} else {
		if($(dom).parent().siblings().length > 2) {
			SU.tip("一级菜单不能超过3个。");
			return;
		}
		$('<li class="item"><input type="text" class="input" style="width:100px;" name="cat['+($(".catlist>ul>li.item").length)+'][name]" placeholder="菜单名称"> - <input class="input" type="text" name="cat['+($(".catlist>ul>li.item").length)+'][url]" placeholder="链接地址">&nbsp;&nbsp;<a href="javascript:" onclick="removecat(this);">×删除</a><ul><li><a href="javascript:" onClick="addcat(this);">+添加子菜单</a></li></ul></li>').insertBefore($(dom).parent());
	}
}
function removecat(t) {
	$(t).parent().remove();
}
</script>
</body>
</html>