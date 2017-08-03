<?php
@set_time_limit(0);
defined('STCMS_ROOT') or die('Access Deined!');

if($_GET['step'] == "bat") {
	if(md5_file(STCMS_ROOT."~tplmsg.php") != $_GET['md5']) {
		halt("模板消息配置文件不正确。");
	}
	$conf = include(STCMS_ROOT."~tplmsg.php");
	$postdata = $conf['data'];
	adminlog("发了微信模板消息");
	if($conf['is_to_all'] == 3) {
		$thisopenids = preg_split("/[\r\n]/", $conf['openid']);
		if($thisopenids) {
			foreach($thisopenids as $tmpopenid) {
				if($tmpopenid != "") {
					$query = urldecode(json_encode(array("touser"=>$tmpopenid, "template_id"=>$conf['template_id'], "url"=>$conf['url'], "data"=>$conf['data'])));
					$response = json_decode(http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $query));
					if($response->errcode == "0") {
						unlink(STCMS_ROOT."~tplmsg.php");
						halt("模板消息已成功发送。");
					} else {
						halt("模板消息发送失败。");
					}
				} else {
					halt("指定的openid无效。");
				}
			}
		} else {
			halt("未指定要发给的而用户。");
		}
		exit("模板消息已发送。");
	} else if($conf['is_to_all'] == 1) {
		$next_openid = trim($_GET['next_openid']);
		$success_num = intval($_GET['success_num']);
		$fail_num = intval($_GET['fail_num']);
		$total_num = intval($_GET['total_num']);
		$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".get_access_token()."&next_openid=".$next_openid;
		$response = json_decode(http_request($url));
		if($response->data->openid) {
			foreach($response->data->openid as $tmpopenid) {
				$query = urldecode(json_encode(array("touser"=>$tmpopenid, "template_id"=>$conf['template_id'], "url"=>$conf['url'], "data"=>$conf['data'])));
				$response = json_decode(http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $query));
				if($response->errcode == "0") {
					$success_num++;
				} else {
					$fail_num++;
				}
				$total_num++;
			}
		}
		if((int)$response->total <= $total_num) {
			unlink(STCMS_ROOT."~tplmsg.php");
			$href = './?ac=connect&do=tplmsg';
		} else {
			$href = './?ac=connect&do=tplmsg&do=bat&md5='.$_GET['md5'].'&success_num='.$success_num.'&fail_num='.$fail_num.'&total_num='.$total_num.'&next_openid='.$response->next_openid;
		}
		echo '<script type="text/javascript">setTimeout(function(){location.href="'.$href.'"}, 1000);</script>';
		exit('已发送 <b>'.$total_num.'</b> 条模板消息，其中成功 <b>'.$success_num.'</b> 条成功， 失败 <b>'.$fail_num.'</b> 条，已完成<b>'. 100*round($total_num /(int)$response->total, 4).'%</b>。');
	} else if($conf['is_to_all'] == 2) {
		$next_openid = trim($_GET['next_openid']);
		$success_num = intval($_GET['success_num']);
		$fail_num = intval($_GET['fail_num']);
		$total_num = intval($_GET['total_num']);
		$url = "https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=".get_access_token();
		$response = json_decode(http_request($url, '{"tagid":"'.$conf['group'].'", "next_openid":"'.$next_openid.'"}'));
		if($response->data->openid) {
			foreach($response->data->openid as $tmpopenid) {
				$query = urldecode(json_encode(array("touser"=>$tmpopenid, "template_id"=>$conf['template_id'], "url"=>$conf['url'], "data"=>$conf['data'])));
				$response = json_decode(http_request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".get_access_token(), $query));
				if($response->errcode == "0") {
					$success_num++;
				} else {
					$fail_num++;
				}
				$total_num++;
			}
		}
		$tagurl = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".get_access_token();
		$tagres = json_decode(http_request($tagurl));
		$tagusertotal = 0;
		if($tagres->tags) {
			foreach($tagres->tags as $tmptag) {
				if($tmptag->id == $conf['group']) {
					$tagusertotal = $tmptag->count;
				}
			}
		}
		if($tagusertotal <= $total_num) {
			unlink(STCMS_ROOT."~tplmsg.php");
			$href = './?ac=connect&do=tplmsg';
		} else {
			$href = './?ac=connect&do=tplmsg&do=bat&md5='.$_GET['md5'].'&success_num='.$success_num.'&fail_num='.$fail_num.'&total_num='.$total_num.'&next_openid='.$response->next_openid;
		}
		echo '<script type="text/javascript">setTimeout(function(){location.href="'.$href.'"}, 1000);</script>';
		exit('已发送 <b>'.$total_num.'</b> 条模板消息，其中成功 <b>'.$success_num.'</b> 条成功， 失败 <b>'.$fail_num.'</b> 条，已完成<b>'. 100*round($total_num / $tagusertotal, 4).'%</b>。');
	}
	exit;
}
$url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=".get_access_token();
$response = json_decode(http_request($url));
$tpls = $response->template_list;
if($_GET['step'] == "send") {
	if($tpls) {
		foreach($tpls as $tmp) {
			if($tmp->template_id == trim($_POST['tpl'])) {
				if(preg_match_all("/\{\{(.+?)\.DATA\}\}/i", $tmp->content, $matches)) {
					$params = $matches[1];
					foreach($params as $var) {
						$result[$var] = array("value"=>urlencode(trim($_POST['param_'.$var])), "color"=>"#173177");
					}
				}
				$is_to_all = intval($_POST['is_to_all']);
				$group = trim($_POST['group']);
				$openid = trim($_POST['openid']);
				$link = trim($_POST['param_url']);
				file_put_contents(STCMS_ROOT."~tplmsg.php", "<?php\r\nreturn ".var_export(array("template_id"=>$tmp->template_id, "is_to_all"=>$is_to_all, "group"=>$group, "openid"=>$openid, "url"=>$link, "data"=>$result), true).";\r\n?>");
				header("Location: ./?ac=connect&do=tplmsg&step=bat&md5=".md5_file(STCMS_ROOT."~tplmsg.php"));
			}
		}
	}
	exit();
}
if($_GET['step'] == "gettpl") {
	if($tpls) {
		foreach($tpls as $tmp) {
			if($tmp->template_id == trim($_GET['id'])) {
				preg_match_all("/\{\{(.+?)\.DATA\}\}/i", $tmp->content, $matches);
				exit(json_encode(array("error"=>0, "content"=>nl2br($tmp->content), "example"=>nl2br($tmp->example), "paramnum"=>count($matches[1]), "param"=>$matches[1])));
			}
		}
	}
	exit(json_encode(array("error"=>1, "msg"=>"未找到指定的模板消息。")));
}
$url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token=".get_access_token();
$response = json_decode(http_request($url));
$groups = $response->tags;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>模板消息__后台管理中心__<?php echo $config['seo_title'];?></title>
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=connect">微信接口</a>&nbsp;&raquo;&nbsp;群发消息</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=connect">消息管理</a></li>
				<li><a href="./?ac=connect&do=menu">菜单管理</a></li>
				<li><a href="./?ac=connect&do=post">群发消息</a></li>
				<li class="on"><a href="./?ac=connect&do=tplmsg">模板消息</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<form id="postfrm" action="./?ac=connect&do=tplmsg&step=send&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<table class="form" >
				<tr>
					<td align="right">使用说明：</td>
					<td height="60">
						<span class="gray" style="font-size:12px;">1、模板消息的添加、删除，请在微信公众平台添加。<br>2、其他自行添加的模板可以主动发出，但请遵守模板消息的规定，否则有封号风险。</span>
					</td>
				</tr>
				<tr>
					<td align="right"><span class="red">*</span>选择模板：</td>
					<td>
						<select name="tpl" onChange="get_tpl(this.value);">
							<option value="0">请选择一个模板</option>
<?php
if($tpls) {
	foreach($tpls as $list) {
?>
							<option value="<?php echo $list->template_id;?>"><?php echo $list->title;?></option>
<?php
	}
}
?>
						</select>
					</td>
				</tr>
				<tr class="tplinfo" style="display:none;">
					<td align="right">模板内容：</td>
					<td class="tplcontent" style="padding:4px; font-size:12px; color:gray;"></td>
				</tr>
				<tr class="tplinfo" style="display:none;">
					<td align="right">模板示例：</td>
					<td class="tplexample" style="padding:4px; font-size:12px; color:gray;"></td>
				</tr>
				<tr>
					<td align="right"><span class="red">*</span>会员筛选：</td>
					<td>
						<label><input type="radio" name="is_to_all" value="1" checked>&nbsp;所有用户</label>&nbsp;&nbsp;
						<label><input type="radio" name="is_to_all" value="2">&nbsp;分组用户</label>&nbsp;&nbsp;
						<label><input type="radio" name="is_to_all" value="3">&nbsp;单个用户</label>&nbsp;&nbsp;
					</td>
				</tr>
				<tr id="group" style="display:none;">
					<td align="right"><span class="red">*</span>选择分组：</td>
					<td>
						<select name="group">
							<option value="0">全部分组</option>
<?php
if($groups) {
	foreach($groups as $list) {
?>
							<option value="<?php echo $list->id;?>"><?php echo $list->name;?>（<?php echo $list->count;?>）</option>
<?php
	}
}
?>
						</select>
					</td>
				</tr>
				<tr id="single" style="display:none;">
					<td align="right"><span class="red">*</span>设定用户：</td>
					<td>
						<textarea class="textarea" name="openid" style="width:300px; height:100px;"></textarea>
						&nbsp;<span class="gray">输入用户openid，支持多个用户，一行一个openid</span>
					</td>
				</tr>
				<tr class="pointer">
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
<script type="text/javascript" src="../js/su.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	$("input[name=is_to_all]").click(function() {
		init_type();
	});
});
function init_type() {
	if(get_radio_value("is_to_all") == "2") {
		$("#group").show();
		$("#single").hide();
	} else if(get_radio_value("is_to_all") == "3") {
		$("#group").hide();
		$("#single").show();
	} else {
		$("#group").hide();
		$("#single").hide();
	}
}
function check_form() {
	if(get_radio_value("is_to_all") == "2" && $("select[name=group]").val() == "0") {
		$("select[name=group]").focus();
		SU.tip("请选择一个分组。");
		return false;
	} else if(get_radio_value("is_to_all") == "3" && $("textarea[name=openid]").val() == "") {
		$("textarea[name=openid]").focus();
		SU.tip("请输入opneid。");
		return false;
	}
	$("textarea[name^=param_]").each(function(index, element) {
		if($(this).val() == "") {
			$(this).focus();
			SU.tip("该参数不能为空。");
			return false;
		}
	});
	return true;
}
function get_tpl(id) {
	if(id == "0") {
		$(".tplinfo").hide();
		$(".param").hide();
	} else {
		$.ajax({
			url:"./?ac=connect&do=tplmsg&step=gettpl&id="+id,
			type:"GET",
			dataType:"json",
			success: function(data) {
				if(data.error > 0) {
					alert(data.msg);
				} else {
					$(".tplcontent").html(data.content);
					$(".tplexample").html(data.example);
					$(".tplinfo").show();
					$(".param").remove();
					for(var n=0; n<data.paramnum; n++) {
						$('<tr class="param"><td height="40" align="right"><span class="red">*</span>'+data.param[n]+'：</td><td><textarea class="textarea" style="width:400px; height:50px;" name="param_'+data.param[n]+'"></textarea></td></tr>').insertBefore($(".pointer"));
					}
					$('<tr class="param"><td height="40" align="right"><span class="red">*</span>链接地址：</td><td><textarea class="textarea" style="width:400px; height:50px;" name="param_url"></textarea></td></tr>').insertBefore($(".pointer"));
				}
			}
		});
	}
}
</script>
</body>
</html>