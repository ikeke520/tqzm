<?php
defined('STCMS_ROOT') or die('Access Deined!');
if($_GET['insert'] == "true") {
	$utf8 = intval($_POST['utf8']);
	$filepath = preg_replace("/^".preg_quote($config['web_url'], "/")."/i", check_dir(dirname(STCMS_ROOT)), $_POST['url']);
	if(!@file_exists($filepath)) {
		halt("未找到指定的导入文件。");
	}
	$counter = 0;
	$fp = fopen($filepath, "r");
	function gbk2utf8($str) {
		return mb_convert_encoding($str, "UTF-8", "GBK");
	}
	if($fp) {
		while($data = @fgetcsv($fp)) {
			if(is_array($data) && $counter > 0) {
				if($utf8 == 0) {
					$data = array_map("gbk2utf8", $data);
				}
				list($name, $cardno, $hash, $price, $num) = $data;
				$mysql_class->insert("cardbase", array("name"=>addslashes($name), "cardno"=>addslashes($cardno), "hash"=>addslashes($hash), "price"=>round(floatval($price), 2), "num"=>intval($num), "time"=>NOW));
			}
			$counter++;
		}
	}
	fclose($fp);
	adminlog("导入了实体卡（".($counter - 1)."张）");
	halt("已成功录入 <font color=\"red\">".($counter - 1)."</font> 张实体卡。", "./?ac=card&do=base");
}
$stamp = time();
$key = md5($stamp . $config['syscode']);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>批量录入实体卡__后台管理中心__<?php echo $config['seo_title'];?></title>
<link href="../css/control.css" type="text/css" rel="stylesheet">
<link href="../lib/webuploader/webuploader.css" type="text/css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0"/>
<style type="text/css">
.tip_process{ height:30px; width:150px; margin-top:10px; display:inline-block; float:right;}
.tip_process .parent{ width:100%; border:#999 solid 1px; border-radius:4px; height:30px; background:#ececec;}
.tip_process .sun{ float:left; height:30px; width:0; background:#dcdcdc;}</style>

</style>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=card">亲子卡管理</a>&nbsp;&raquo;&nbsp;批量录入实体卡</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=card">亲子卡列表</a></li>
				<li><a href="./?ac=card&do=add">新增亲子卡</a></li>
                <li class="on"><a href="./?ac=card&do=base">实体亲子卡</a></li>
				<div class="clear"></div>
			</ul>
		</div>
            <div class="tabcard">
                <div class="menu">
                    <ul>
                        <li><a href="./?ac=card&do=base">实体卡列表</a></li>
						<li><a href="./?ac=card&do=base&step=add">录入实体卡</a></li>
						<li class="on"><a href="./?ac=card&do=base&step=import">批量导入</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
		<form action="./?ac=card&do=base&step=import&insert=true&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<table class="form" >
				<input type="hidden" name="url">
                <tr>
					<td height="40" align="right"><span class="red">*</span>导入说明：</td>
					<td>
						<p class="gray">1、录入实体卡，每次最好不要超过1万张，文件大小不超过2M；</p>
						<p class="gray">2、导入的文件应当符合导入模板的格式，模板文件可以Excel编辑，<a href="./import.csv">下载模板</a>；</p>
						<p class="gray">3、系统默认第一行是表头，不实际录入；</p>
						<p class="gray">4、请注意导入文件的编码，一般为GB2312，如出现乱码，请选择UTF-8；</p>
					</td>
				</tr>
				<tr>
					<td height="60" align="right"><span class="red">*</span>导入的文件：</td>
					<td id="info">
						<a href="javascript:" id="picker">选择CSV</a>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>文件编码：</td>
					<td>
						<label><input type="radio" name="utf8" value="0" checked>&nbsp;GB2312</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="utf8" value="1">&nbsp;UTF-8</label>
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
	uploader = new WebUploader.Uploader({
		swf: '../lib/webuploader/Uploader.swf',
		server:'./?ac=card&do=base&step=upload&stamp=<?php echo $stamp;?>&key=<?php echo $key;?>',
		pick:{
			id:"#picker",
			innerHTML:"选择CSV",
			multiple:true
		},
		accept:{
			title:"上传CSV",
			extensions:"csv",
			mimeTypes:"text/csv"
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
		uploader.upload();
	});
	uploader.on('uploadProgress', function(file, percent) {
		SU.tip('正在上传：<div class="tip_process"><div class="parent"><div class="sun"></div></div><div>', false)
		$('.tip_process .parent .sun').css('width', percent*100 +'%');
	});
	uploader.on('uploadSuccess', function(file, response) {
		$("input[name=url]").val(response.url);
		$("#info").html("<span class=\"gray\">已上传文件："+response.filename+"，文件大小："+response.size+"</span>");
		SU.hide_tip();
	});
});
function check_form() {
	if(empty($("input[name=url]").val())) {
		$("input[name=url]").focus();
		SU.tip("还未上传CSV文件。");
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