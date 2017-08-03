<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thisnews = $mysql_class->select_one("news", "*", array("id"=>intval($_GET['id'])));
if(empty($thisnews)) {
	halt("系统未找到指定的资讯。");
}
if($_GET['step'] == "true") {
	$data['title'] = htmlspecialchars(trim($_POST['title']));
	$data['cid'] = intval($_POST['cid']);
	$data['hit'] = intval($_POST['hit']);
	$data['url'] = trim($_POST['url']);
	$data['content'] = trim($_POST['content']);
		
	if(empty($data['title']) || empty($data['cid']) || empty($data['url']) || empty($data['content'])) {
		halt("标题、内容、封面必须填写。");
	}
	if($data['cid'] != $thisnews['cid']) {
		$data['cname'] = add_slashes($mysql_class->get_field_value("newscat", "name", array("id"=>$data['cid'])));
	}
	if($data['url'] != $thisnews['url']) {
		$data['url'] = move_attachment($data['url'], "news/{$thisnews['id']}/".basename($data['url']));
	}
	$mysql_class->update("news", $data, array("id"=>$thisnews['id']));
	adminlog("修改资讯：".trim($_GET['id']));
	header("Location: ./?ac=news");
	exit;
}
$stamp = time();
$key = md5($stamp . $config['syscode']);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>编辑资讯__后台管理中心__<?php echo $config['seo_title'];?></title>
<link href="../css/control.css" type="text/css" rel="stylesheet">
<link href="../lib/webuploader/webuploader.css" type="text/css" rel="stylesheet">
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=news">资讯管理</a>&nbsp;&raquo;&nbsp;编辑资讯</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=news">资讯列表</a></li>
				<li><a href="./?ac=news&do=add">发布资讯</a></li>
                <li><a href="./?ac=news&do=category">资讯分类</a></li>
				<li class="on"><a href="javascript:">编辑资讯</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<form action="./?ac=news&do=edit&step=true&id=<?php echo $thisnews['id'];?>&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<input type="hidden" name="url" value="<?php echo $thisnews['url'];?>">
			<table class="form" >
				<tr>
					<td height="60" align="right"><span class="red">*</span>分类：</td>
					<td>
						<select style="min-width:200px;" name="cid" class="select">
                        	<option value="">请选择分类</option>
<?php
$category = $mysql_class->select("newscat");
if($category) {
	foreach($category as $list) {
?>
                        	<option value="<?php echo $list['id'];?>"><?php echo $list['name'];?></option>
<?php
	}
}
?>
                        </select>
					</td>
				</tr>
                <tr>
					<td height="40" align="right"><span class="red">*</span>标题：</td>
					<td>
						<textarea class="textarea" name="title" style="width:400px; height:50px;"><?php echo $thisnews['title'];?></textarea>
						&nbsp; <span class="gray"></span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>热度：</td>
					<td>
						<input type="text" class="input" name="hit" value="<?php echo $thisnews['hit'];?>" style="width:100px;">
						&nbsp; <span class="gray">整数，表示资讯的点击数</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>封面：</td>
					<td>
						<div class="imguploader">
							<div id="picture" class="filelist">
								<div class="file">
									<div class="preview"><img src="<?php if($thisnews['url']) {?><?php echo $thisnews['url'];?><?php } else {?>../images/nopreview.png<?php }?>" /></div>
									<div class="process">
										<div class="parent">
											<div class="sun"></div>
										</div>
									</div>
									<div class="comment">图像尺寸：151 * 120</div>
								</div>
								<div id="uploadadder" class="add">
									<a id="picker" href="javascript:" title="添加"></a>
								</div>
								<div class="clear"></div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>内容：</td>
					<td>
						<script id="content" name="content" type="text/plain" style="width:650px; height:300px;"><?php echo $thisnews['content'];?></script>
					</td>
				</tr>
				<tr>
					<td colspan="2" height="80" style="padding-left:100px;"><input type="submit" value="提交" id="hiddensubmit" style="display:none;" />
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
	$("select[name=cid]").val("<?php echo $thisnews['cid'];?>");
	if ( !WebUploader.Uploader.support() ) {
        alert( 'Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
        throw new Error( 'WebUploader does not support the browser you are using.' );
    }
	if ( !WebUploader.Uploader.support() ) {
        alert( 'Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
        throw new Error( 'WebUploader does not support the browser you are using.' );
    }
	uploader = new WebUploader.Uploader({
		swf: '../lib/webuploader/Uploader.swf',
		server:'./?ac=news&do=upload&stamp=<?php echo $stamp;?>&key=<?php echo $key;?>',
		pick:{
			id:"#picker",
			innerHTML:"上传图片",
			multiple:false
		},
		accept:{
			title:"图片文件",
			extensions:"jpg,png,gif,jpeg",
			mimeTypes:"image/jpeg,image/png,image/gif,image/jpg"
		},
		auto:true,
		runtimeOrder:"html5,flash",
		fileVal:"file",
		method:"POST",
		sendAsBinary:false,
		chunked:false,
		fileSizeLimit:20971520
	});
	uploader.on('fileQueued', function(file) {
		uploader.makeThumb(file, function (error, src) {
			if (error || !src) {
				SU.tip("生产预览图错误。");
			} else {
				$("#picture .file .preview img").attr('src', src);
			}
		}, 151, 120);
		uploader.upload();
	});
	uploader.on('uploadProgress', function(file, percent) {
		$('#picture .file .process .parent').show();
		$('#picture .file .process .sun').css('width', percent*100 +'%');
	});
	uploader.on('uploadSuccess', function(file, response) {
		$("input[name=url]").val(response.url);
		$("#picture .file .parent").hide();
	});
});
function check_form() {
	if(empty($("select[name=cid]").val(), true)) {
		$("select[name=cid]").focus();
		SU.tip("请选择分类。");
		return false;
	}
	if(empty($("textarea[name=title]").val())) {
		$("textarea[name=title]").focus();
		SU.tip("标题不能为空。");
		return false;
	}
	if(empty($("input[name=url]").val())) {
		$("input[name=url]").focus();
		SU.tip("封面图片还未上传。");
		return false;
	}
	if(UE.getEditor('content').hasContents() == false) {
		UE.getEditor('content').focus();
		SU.tip("内容不能为空。");
		return false;
	}
	return true;
}
function empty(str, zero) {
	if(typeof str =="undefined") {
		return true;
	}
	str = str.replace(/^[\t\r\n\s]*/, '').replace(/[\r\t\s\n]*$/, '');
	if(str == '' || (str == '0' && zero == false)) {
		return true;
	} else {
		return false;
	}
}
</script>
</body>
</html>