<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thiscard = $mysql_class->select_one("card", "*", array("id"=>intval($_GET['id'])));
if(empty($thiscard)) {
	halt("系统未找到指定的亲子卡。");
}
if($_GET['step'] == "true") {
	$data['name'] = htmlspecialchars(trim($_POST['name']));
	$data['is_trial'] = intval($_POST['is_trial']);
	$data['price'] = round(floatval($_POST['price']), 2);
	$data['org_price'] = round(floatval($_POST['org_price']), 2);
	$data['num'] = intval($_POST['num']);
	$data['url'] = trim($_POST['url']);
	$data['intro'] = htmlspecialchars(trim($_POST['intro']));
	$data['index_order'] = intval($_POST['index_order']);
	if(empty($data['name']) || empty($data['price']) || empty($data['url']) || empty($data['num']) || empty($data['intro'])) {
		halt("名称、价格、次数、封面、使用规则必须填写。");
	}
	if($data['url'] != $thiscard['url']) {
		$data['url'] = move_attachment($data['url'], "card/{$thiscard['id']}/".basename($data['url']));
		remove_attachment($thiscard['url']);
	}
	$mysql_class->update("card", $data, array("id"=>$thiscard['id']));
	adminlog("修改了亲子卡（".$thiscard['id']."张）");
	header("Location: ./?ac=card");
	exit;
}
$stamp = time();
$key = md5($stamp . $config['syscode']);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>编辑亲子卡__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=card">亲子卡管理</a>&nbsp;&raquo;&nbsp;编辑亲子卡</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=card">亲子卡列表</a></li>
				<li class="on"><a href="./?ac=card&do=add">新增亲子卡</a></li>
                <li><a href="./?ac=card&do=base">实体亲子卡</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<form action="./?ac=card&do=edit&step=true&id=<?php echo $thiscard['id'];?>&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<input type="hidden" name="url" value="<?php echo $thiscard['url'];?>">
			<table class="form" >
                <tr>
					<td height="40" align="right"><span class="red">*</span>亲子卡名称：</td>
					<td>
						<input type="text" class="input" name="name" value="<?php echo $thiscard['name'];?>">
						&nbsp; <span class="gray"></span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>亲子卡类型：</td>
					<td>
						<label><input type="radio" name="is_trial" value="0" checked>&nbsp;普通卡</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="is_trial" value="1">&nbsp;体验卡</label>
						&nbsp;<span class="gray">体验卡每个用户最多只能购买一次，目的是以较低的价格让用户尝试</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>亲子卡价格：</td>
					<td>
						<input type="text" class="input" name="price" value="<?php echo $thiscard['price'];?>" style="width:100px;">
						&nbsp; <span class="gray">单位：元，支持两位小数，实际成交价。</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right">亲子卡原价：</td>
					<td>
						<input type="text" class="input" name="org_price" value="<?php echo $thiscard['org_price'];?>" style="width:100px;">
						&nbsp; <span class="gray">单位：元，支持两位小数。设置后，前端会显示优惠信息。</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>参加活动次数：</td>
					<td>
						<input type="number" min="1" class="input" name="num" value="<?php echo $thiscard['num'];?>" style="width:100px;">
						&nbsp; <span class="gray">单位：次，仅支持整数。</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>前端显示顺序：</td>
					<td>
						<input type="number" min="1" class="input" name="index_order" value="<?php echo $thiscard['index_order'];?>" style="width:100px;">
						&nbsp; <span class="gray">整数，数值越小越靠前。</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>亲子卡封面：</td>
					<td>
						<div class="imguploader">
							<div id="picture" class="filelist">
								<div class="file">
									<div class="preview"><img src="<?php if($thiscard['url']) {?><?php echo $thiscard['url'];?><?php } else {?>../images/nopreview.png<?php }?>" /></div>
									<div class="process">
										<div class="parent">
											<div class="sun"></div>
										</div>
									</div>
									<div class="comment">图像尺寸：704 * 384</div>
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
					<td height="40" align="right"><span class="red">*</span>使用规则：</td>
					<td>
						<textarea class="textarea" name="intro" style="width:500px; height:200px;"><?php echo $thiscard['intro'];?></textarea>
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
	init_radio("is_trial", "<?php echo $thiscard['is_trial'];?>");
	
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
		server:'./?ac=card&do=upload&stamp=<?php echo $stamp;?>&key=<?php echo $key;?>',
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
		}, 704, 384);
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
		$("input[name=name]").focus();
		SU.tip("名称不能为空。");
		return false;
	}
	if(empty($("input[name=price]").val(), true)) {
		$("input[name=price]").focus();
		SU.tip("价格不能为空。");
		return false;
	}
	var reg = new RegExp(/^(([1-9]+\d*)|0)(\.\d{1,2})?$/);
	if(!reg.test($("input[name=price]").val())) {
		$("input[name=price]").focus();
		SU.tip("价格格式不正确。");
		return false;
	}
	if(empty($("input[name=num]").val())) {
		$("input[name=num]").focus();
		SU.tip("参与活动次数不能为空。");
		return false;
	}
	var reg = new RegExp(/^\d+$/);
	if(!reg.test($("input[name=num]").val())) {
		SU.tip("参与活动次数的格式不正确。");
		$("input[name=num]").focus();
		return false;
	}
	if(empty($("input[name=url]").val())) {
		SU.tip("封面还未上传。");
		$("input[name=url]").focus();
		return false;
	}
	if(empty($("textarea[name=intro]").val())) {
		SU.tip("使用规则不能为空。");
		$("textarea[name=intro]").focus();
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