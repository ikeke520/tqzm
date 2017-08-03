<?php
defined('STCMS_ROOT') or die('Access Deined!');
$adconfigfile = STCMS_ROOT."adconfig.php";
if(file_exists($adconfigfile)) {
	$adconfig = include($adconfigfile);
}
if(empty($adconfig)) {
	$adconfig = array(
		"is_hide"=>"0",
		"picture"=>$config['web_url']."images/a_advertising.jpg",
		"link"=>$config['web_url'],
	);
}
if($_GET['step'] == "upload") {
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
		imgresize($upload_root . $path_subfix, 711, 178);
		exit(json_encode(array(
			'filename'=>add_slashes($filesrc['name']),
			'url'=>$url_root . $path_subfix,
			'size'=>get_size($filesrc['size']),
		)));
	} else {
		exit(json_encode(array("error"=>1, "msg"=>"上传文件失败，可能是目的文件夹没有写入权限所致。")));
	}
}
if($_GET['update'] == "true") {
	$data['link'] = trim($_POST['link']);
	$data['picture'] = trim($_POST['picture']);
	$data['is_hide'] = intval($_POST['is_hide']);
	
	if(empty($data['link']) || empty($data['picture'])) {
		halt("连接地址与图片不能为空。");
	}
	if($data['picture'] != $adconfig['picture']) {
		$data['picture'] = move_attachment($data['picture'], "banner/".basename($data['picture']));
		remove_attachment($adconfig['picture']);
	}
	file_put_contents($adconfigfile, "<?php\n\rreturn ".var_export($data, true).";\r\n?>");
	adminlog("重新设置了广告");
	header("Location: ./?ac=ads&do=banner");
	exit;
}
$stamp = time();
$key = md5($stamp . $config['syscode']);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>广告设置__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=ads">广告管理</a>&nbsp;&raquo;&nbsp;广告设置</div>
	<div class="mainright">
		<div class="tab">
			<ul>
                <li><a href="./?ac=ads">图片轮换</a></li>
				<li class="on"><a href="./?ac=ads&do=banner">广告设置</a></li>
				<div class="clear"></div>
			</ul>
		</div>
        <a href="./?ac=ads" class="button mt10">返回</a>
		<form action="./?ac=ads&do=banner&update=true&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<input type="hidden" name="picture" value="<?php echo $adconfig['picture'];?>">
			<table class="form" >
                <tr>
                	<td colspan="2"><p class="gray">说明：该广告位在首页栏目导航下，是一幅全图广告，图片尺寸 711 * 178</p></td>
                </tr>
                <tr>
					<td height="40" align="right"><span class="red">*</span>广告开关：</td>
					<td>
						<label><input type="radio" name="is_hide" value="0">&nbsp;显示</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="is_hide" value="1">&nbsp;隐藏</label>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>连接地址：</td>
					<td>
						<textarea class="textarea" name="link" style="width:500px; height:50px;"><?php echo $adconfig['link'];?></textarea>
					</td>
				</tr>
                <tr>
					<td height="40" align="right"><span class="red">*</span>连接图片：</td>
					<td>
						<div class="imguploader">
							<div id="picture" class="filelist">
								<div class="file">
									<div class="preview"><img src="<?php if($adconfig['picture']) {?><?php echo $adconfig['picture'];?><?php } else {?>../images/nopreview.png<?php }?>" /></div>
									<div class="process">
										<div class="parent">
											<div class="sun"></div>
										</div>
									</div>
									<div class="comment">图像尺寸：711 * 178</div>
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
	init_radio("is_hide", "<?php echo $adconfig['is_hide'];?>");
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
		server:'./?ac=ads&do=banner&step=upload&stamp=<?php echo $stamp;?>&key=<?php echo $key;?>',
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
		}, 711, 178);
		uploader.upload();
	});
	uploader.on('uploadProgress', function(file, percent) {
		$('#picture .file .process .parent').show();
		$('#picture .file .process .sun').css('width', percent*100 +'%');
	});
	uploader.on('uploadSuccess', function(file, response) {
		$("input[name=picture]").val(response.url);
		$("#picture .file .parent").hide();
	});
});
function check_form() {
	if(empty($("textarea[name=link]").val())) {
		SU.tip("链接地址不能为空。");
		$("textarea[name=link]").focus();
		return false;
	}
	if(empty($("input[name=picture]").val())) {
		SU.tip("轮播图片未上传。");
		$("input[name=picture]").focus();
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