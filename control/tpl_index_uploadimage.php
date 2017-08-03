<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thisactivity = $mysql_class->select_one("activity", "*", array("id"=>intval($_GET['id'])));
if(empty($thisactivity)) {
	halt("系统未找到活动。");
}
$stamp = time();
$key = md5($stamp . $config['syscode']);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>修改活动__后台管理中心__<?php echo $config['seo_title'];?></title>
<link href="../css/control.css" type="text/css" rel="stylesheet">
<link href="../lib/webuploader/webuploader.css" type="text/css" rel="stylesheet">
<link href="../js/jquery.datetimepicker.css" type="text/css" rel="stylesheet">
<style type="text/css">
.tip_process{ height:30px; width:150px; margin-top:10px; display:inline-block; float:right;}
.tip_process .parent{ width:100%; border:#999 solid 1px; border-radius:4px; height:30px; background:#ececec;}
.tip_process .sun{ float:left; height:30px; width:0; background:#dcdcdc;}</style>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=index">活动管理</a>&nbsp;&raquo;&nbsp;修改课程</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=index">活动列表</a></li>
				<li><a href="./?ac=index&do=add">新增活动</a></li>
                <li><a href="./?ac=index&do=category">活动分类</a></li>
                <li><a href="./?ac=index&do=tag">活动标签</a></li>
				<li class="on"><a href="javascript:">修改活动</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li><a href="./?ac=index&do=edit&id=<?php echo $thisactivity['id'];?>">基本信息</a></li>
						<li class="on"><a href="javascript:">活动图片</a></li>
                        <li><a href="./?ac=index&do=applylist&id=<?php echo $thisactivity['id'];?>">报名情况</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<table class="table mt10" bgcolor="#ececec" cellspacing="1" cellpadding="0">
			<tr>
				<td style="text-align:left !important;"><span style="color:gray; font-size:12px;">注明：上传图片的尺寸为 750 * 400。</span></td>
			</tr>
			<tr>
				<td class="tl" height="300" valign="top">
					<div class="imguploader">
						<div class="filelist">
<?php
if($imagelist = $mysql_class->select("activity_picture", "*", array('aid'=>$thisactivity['id']), "index_order ASC, id DESC")) {
	foreach($imagelist as $list) {
?>
							<div class="file" id="fid_<?php echo $list['id'];?>">
								<div class="index_order"><input type="text" name="index_order" onBlur="set_index_order('<?php echo $list['id'];?>', true);" value="<?php echo $list['index_order'];?>"></div>
								<a href="javascript:" onClick="remove_image('<?php echo $list['id'];?>', true);" class="del" title="删除"></a>
								<div class="preview">
									<img src="<?php echo $list['url'];?>" />
								</div>
							</div>
<?php 
	}
}
?>
							<div class="add" id="uploadadder"><a href="javascript:" id="picker"></a></div>
						</div>
					</div>
				</td>
			</tr>
		</table>
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
<script type="text/javascript" src="../lib/webuploader/webuploader.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	if ( !WebUploader.Uploader.support() ) {
        alert( 'Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
        throw new Error( 'WebUploader does not support the browser you are using.' );
    }
	uploader = new WebUploader.Uploader({
		swf: '../lib/webuploader/Uploader.swf',
		server:'./?ac=index&do=douploadimage&id=<?php echo $thisactivity['id'];?>&stamp=<?php echo $stamp;?>&key=<?php echo $key;?>',
		pick:{
			id:"#picker",
			innerHTML:"选择图片",
			multiple:true
		},
		accept:{
			title:"图片文件",
			extensions:"gif,jpg,jpeg,bmp,png",
			mimeTypes:"image/jpeg,image/png,image/gif,image/jpg"
		},
		auto:false,
		runtimeOrder:"html5,flash",
		fileVal:"file",
		method:"POST",
		sendAsBinary:false,
		chunked:false,
		fileSizeLimit:2097152
	});
	uploader.on('fileQueued', function(file) {
		uploader.makeThumb(file, function (error, src) {
			if (error || !src) {
				SU.tip("生产预览图错误。");
			} else {
				$('<div class="file" id="'+file.id+'"><div class="index_order"><input type="text" name="index_order" onBlur="set_index_order(\''+file.id+'\');" value=""></div><div class="preview"><img src="'+src+'" /></div><div class="process"><div class="parent"><div class="sun"></div></div></div><a href="javascript:" title="删除" onclick="remove_image(\''+file.id+'\')" class="del"></a></div>').insertBefore($('#uploadadder'));
			}
		}, 162, 87);
		uploader.upload();
	});
	uploader.on('uploadProgress', function(file, percent) {
		$('#'+file.id+' .process .parent').show();
		$('#'+file.id+' .process .sun').css('width', percent*100 +'%');
	});
	uploader.on('uploadSuccess', function(file, response) {
		$('#'+file.id).attr('data-fid', response.fid);
		$('#'+file.id +" input[name=index_order]").val(response.index_order);
		$('#'+file.id+' .process .parent').hide();
	});
});

function remove_image(id, f) {
	if(f) {
		var real_id = id;
		var file_id = "fid_"+id;
	} else {
		var real_id = $("#"+id).attr("data-fid");
		var file_id = id
	}
	$.ajax({
		url:"./?ac=index&do=delimage&id="+real_id,
		type:'GET',
		dataType:"json",
		success: function(data){
			if(data.error > 0) {
				alert(data.msg);
			} else {
				$("#"+file_id).remove();
			}
		}
	});
}

function set_index_order(id, f) {
	if(f) {
		var real_id = id;
		var file_id = "fid_"+id;
	} else {
		var real_id = $("#"+id).attr("data-fid");
		var file_id = id
	}
	$.ajax({
		url:"./?ac=index&do=setindexorder&id="+real_id,
		type:'POST',
		dataType:"json",
		data:"index_order="+$("#"+file_id+" input[name=index_order]").val(),
		success: function(data){
			if(data.error > 0) {
				alert(data.msg);
			}
		}
	});
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