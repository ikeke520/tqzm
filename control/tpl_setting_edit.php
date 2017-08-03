<?php
defined('STCMS_ROOT') or die('Access Deined!');
$type = trim($_GET['type']);
if(!in_array($type, array('intro', 'faq', 'corp', 'link'))) {
	$type = "intro";
}
$thisinfo = $mysql_class->select_one("setting_info", "*", array("type"=>$type));
if(empty($thisinfo)) {
	$thisinfo = array("type"=>$type, "content"=>"暂无内容");
	$mysql_class->insert("setting_info", $thisinfo);
	$thisinfo['id'] = $mysql_class->insert_id();
}
if($_GET['update'] == 'true') {
	$data['content'] = trim($_POST['content']);
	$mysql_class->update("setting_info", $data, array("id"=>$thisinfo['id']));
	adminlog("修改了关于我们栏目信息");
	header("Location: ./?ac=setting&do=edit&type=".$thisinfo['type']);
	exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>系统设置__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;系统设置</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li class="on"><a href="./?ac=setting">首选项</a></li>
				<li><a href="./?ac=setting&do=admin">管理员设置</a></li>
                <li><a href="./?ac=setting&do=log">安全日志</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li><a href="./?ac=setting">首选项</a></li>
						<li<?php if($thisinfo['type'] == "intro") {?> class="on"<?php }?>><a href="./?ac=setting&do=edit&type=intro">关于我们</a></li>
						<li<?php if($thisinfo['type'] == "faq") {?> class="on"<?php }?>><a href="./?ac=setting&do=edit&type=faq">常见问题</a></li>
						<li<?php if($thisinfo['type'] == "corp") {?> class="on"<?php }?>><a href="./?ac=setting&do=edit&type=corp">商务合作</a></li>
						<li<?php if($thisinfo['type'] == "link") {?> class="on"<?php }?>><a href="./?ac=setting&do=edit&type=link">联系客服</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<form action="./?ac=setting&do=edit&update=true&type=<?php echo $type;?>&sid=<?php echo SID;?>" method="post">
			<table class="form" >
				<tr>
					<td>
						<script id="content" name="content" type="text/plain" style="width:650px; height:300px;"><?php echo $thisinfo['content'];?></script>
					</td>
				</tr>
				<tr>
					<td height="80" style="padding-left:100px;"><input type="submit" value="提交" id="hiddensubmit" style="display:none;" />
						<a href="javascript:void(0);" onclick="$('#hiddensubmit').click();" class="submit"><span>确认提交</span></a></td>
				</tr>
			</table>
		</form>
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
<script type="text/javascript" src="../lib/webuploader/webuploader.js"></script>
<script type="text/javascript" src="../lib/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="../lib/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	UE.getEditor('content');
});
</script>
</body>
</html>