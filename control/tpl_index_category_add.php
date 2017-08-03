<?php
defined('STCMS_ROOT') or die('Access Deined!');
if($_GET['insert'] == "true") {
	$data['name'] = htmlspecialchars(trim($_POST['name']));
	$data['index_order'] = intval($_POST['index_order']);
	$data['url'] = trim($_POST['url']);
	if(empty($data['name']) || empty($data['url'])) {
		halt("分类名称、分类图片必须填写。");
	}
	$mysql_class->insert("category", $data);
	$thisid = $mysql_class->insert_id();
	$url = move_attachment($data['url'], "category/{$thisid}/".basename($data['url']));
	if($url) {
		$mysql_class->update("category", array("url"=>$url), array("id"=>$thisid));
	}
	adminlog("添加活动分类：".$thisid);
	header("Location: ./?ac=index&do=category");
	exit;
}
$stamp = time();
$key = md5($stamp . $config['syscode']);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>添加活动分类__后台管理中心__<?php echo $config['seo_title'];?></title>
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
        <a href="javascript:" onClick="history.go(-1);" class="button mt10"><span>返回</span></a>
		<form action="./?ac=index&do=category&step=add&insert=true&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<input type="hidden" name="url" value="">
            <table class="form" >
				<tr>
					<td height="40" align="right"><span class="red">*</span>分类名称：</td>
					<td>
						<input type="text" class="input" name="name" value="">
						&nbsp; <span class="gray"></span>
					</td>
				</tr>
				<tr class="urlpicker">
					<td height="40" align="right"><span class="red">*</span>分类排序：</td>
					<td>
						<input class="input" name="index_order" style="width:100px;" value="<?php echo $thiscategory['index_order'];?>">
						&nbsp; <span class="gray">数字，按照数字顺序排列分类</span>
					</td>
				</tr>
				<tr class="urlpicker">
					<td height="40" align="right"><span class="red">*</span>分类图标：</td>
					<td>
						<div class="headimguploader">
							<div id="picture" class="filelist">
								<div class="file">
									<div class="preview"><img src="<?php if($setting['banner']) {?><?php echo $setting['banner'];?><?php } else {?>../images/nophoto.jpg<?php }?>" /></div>
									<div class="process">
										<div class="parent">
											<div class="sun"></div>
										</div>
									</div>
									<div class="comment">图像尺寸：97 * 91</div>
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
<script type="text/javascript">
$(document).ready(function(e) {
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
		server:'./?ac=index&do=category&step=upload&stamp=<?php echo $stamp;?>&key=<?php echo $key;?>',
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
		}, 150, 150);
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
	if(empty($("input[name=name]").val())) {
		SU.tip("分类名称不能为空。");
		$("input[name=name]").focus();
		return false;
	}
	if(empty($("input[name=url]").val())) {
		SU.tip("还未上传分类图片。");
		return false;
	}
	return true;
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