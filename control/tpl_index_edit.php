<?php
defined('STCMS_ROOT') or die('Access Deined!');
$thisactivity = $mysql_class->select_one("activity", "*", array("id"=>intval($_GET['id'])));
if(empty($thisactivity)) {
	halt("系统未找到活动。");
}
$thistaglist = get_id_array($mysql_class->select("tag_list", "tid", array("aid"=>$thisactivity['id'])), "tid");
if($_GET['update'] == "true") {
	$data['title'] = htmlspecialchars(trim($_POST['title']));
	$data['cid'] = intval($_POST['cid']);
	$data['cname'] = $mysql_class->get_field_value("category", "name", array("id"=>$data['cid']));
	$data['status'] = intval($_POST['status']);
	$data['person_num'] = intval($_POST['person_num']);
	$data['adult_along'] = intval($_POST['adult_along']);
	$data['year_duration'] = htmlspecialchars(trim($_POST['year_duration']));
	$data['activity_type'] = htmlspecialchars(trim($_POST['activity_type']));
	$data['price'] = round(floatval($_POST['price']), 2);
	$data['date'] = htmlspecialchars(trim($_POST['date']));
	$data['timestr'] = htmlspecialchars(trim($_POST['timestr']));
	$data['hit'] = intval($_POST['hit']);
	$data['address'] = htmlspecialchars(trim($_POST['address']));
	$data['nav_url'] = trim($_POST['navurl']);
	$data['notice'] = trim($_POST['notice']);
	$data['intro'] = trim($_POST['intro']);
	$data['is_rmd'] = intval($_POST['is_rmd']);
	$data['is_complete'] = intval($_POST['is_complete']);
	$data['payway'] = intval($_POST['payway']);
	
	if(empty($data['title']) || empty($data['cid']) || empty($data['intro']) || empty($data['date'])) {
		halt("活动主题、分类、介绍、日期必须填写。");
	}
	$mysql_class->update("activity", $data, array("id"=>$thisactivity['id']));
	if($_POST['tag']) {
		foreach($_POST['tag'] as $tmp) {
			if(!in_array($tmp, $thistaglist)){
				$thistag = $mysql_class->select_one("tag", "*", array("id"=>$tmp));
				if($thistag) {
					if($mysql_class->num_table("tag_list", array("aid"=>$thisactivity['id'], "tid"=>$thistag['id'])) < 1) {
						$mysql_class->insert("tag_list", array("tid"=>$thistag['id'], "tname"=>add_slashes($thistag['name']), "aid"=>$thisactivity['id']));
					}
				}
			}
		}
	}
	$mysql_class->query("DELETE FROM {$config['db_prefix']}tag_list WHERE aid='{$thisactivity['id']}' AND tid NOT IN ('".implode("','", array_map("intval", $_POST['tag']))."')");
	$mysql_class->query("UPDATE {$config['db_prefix']}tag SET child_num = (SELECT COUNT(*) FROM {$config['db_prefix']}tag_list WHERE tid={$config['db_prefix']}tag.id) WHERE id IN ('".implode("','", array_map("intval", $_POST['tag']))."') OR id IN ('".implode("','", $thistaglist)."')");
	adminlog("修改活动：".trim($thisactivity['id']));
	header("Location: ./?ac=index");
	exit;
}
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
                        <li class="on"><a href="javascript:">基本信息</a></li>
						<li><a href="./?ac=index&do=uploadimage&id=<?php echo $thisactivity['id'];?>">活动图片</a></li>
                        <li><a href="./?ac=index&do=applylist&id=<?php echo $thisactivity['id'];?>">报名情况</a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
		<form action="./?ac=index&do=edit&update=true&id=<?php echo $thisactivity['id'];?>&sid=<?php echo SID;?>" method="post" onSubmit="return check_form();">
			<table class="form" >
				<tr>
					<td height="60" align="right"><span class="red">*</span>活动主题：</td>
					<td>
						<textarea class="textarea" name="title" style="width:400px; height:50px;"><?php echo $thisactivity['title'];?></textarea>
					</td>
				</tr>
				<tr>
					<td height="60" align="right"><span class="red">*</span>活动分类：</td>
					<td>
						<select name="cid" class="select">
                        	<option value="">请选择分类</option>
<?php
$category = $mysql_class->select("category", "*", false, array("index_order ASC"));
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
					<td height="40" align="right"><span class="red">*</span>活动标签：</td>
					<td>
<?php
$tag = $mysql_class->select("tag");
if($tag) {
	foreach($tag as $list) {
?>
						<label><input type="checkbox" name="tag[]" value="<?php echo $list['id'];?>">&nbsp;<?php echo $list['name'];?></label>&nbsp;&nbsp;&nbsp;
<?php
	}
}
?>
						<a href="./?ac=index&do=tag">管理标签</a>
                    </td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>活动状态：</td>
					<td>
						<label><input type="radio" name="status" value="0" checked>&nbsp;正在报名</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="status" value="1">&nbsp;关闭报名</label>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>编辑完成：</td>
					<td>
						<label><input type="radio" name="is_complete" value="0" checked>&nbsp;未完成</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="is_complete" value="1">&nbsp;已编辑完成</label>&nbsp;&nbsp;&nbsp;&nbsp;<span class="gray">仅编辑已完成的才在前台显示</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>是否推荐：</td>
					<td>
						<label><input type="radio" name="is_rmd" value="0" checked>&nbsp;不推荐</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="is_rmd" value="1">&nbsp;推荐到首页</label>&nbsp;&nbsp;<span class="gray">已推荐的活动优先显示</span>
					</td>
				</tr>
				<tr>
					<td height="50" align="right"><span class="red">*</span>报名人数：</td>
					<td>
						<input type="number" name="person_num" style="width:100px;" class="input" value="<?php echo $thisactivity['person_num'];?>">&nbsp;&nbsp;<span class="gray">报名人数达到设定的人数后则不能继续报名，0表示不限制</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>大人陪同：</td>
					<td>
						<label><input type="radio" name="adult_along" value="0" checked>&nbsp;不需要大人陪同</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="adult_along" value="1">&nbsp;大人陪同方能参加</label>
					</td>
				</tr>
				<tr>
					<td height="50" align="right"><span class="red">*</span>报名年龄：</td>
					<td>
						<input type="text" name="year_duration" class="input" value="<?php echo $thisactivity['year_duration'];?>">&nbsp;&nbsp;<span class="gray">年龄要求，如1岁以内等</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>活动场地：</td>
					<td>
						<input type="text" name="activity_type" class="input" value="<?php echo $thisactivity['activity_type'];?>">&nbsp;&nbsp;<span class="gray">室内、室外、草地、森林等</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>参加费用：</td>
					<td>
						<input type="text" name="price" style="width:100px;" class="input" value="<?php echo $thisactivity['price'];?>">&nbsp;&nbsp;<span class="gray">单次购买活动的价格，0表示免费</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>缴扣方式：</td>
					<td>
						<label><input type="radio" name="payway" value="0">&nbsp;通用方式</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="payway" value="1">&nbsp;单次购买</label>&nbsp;&nbsp;&nbsp;&nbsp;<span class="gray">通用方式可单次购买也可扣卡；单次购买则不允许扣卡</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>活动日期：</td>
					<td>
						<input type="text" name="date" style="width:150px;" class="input" value="<?php echo $thisactivity['date'];?>">&nbsp;&nbsp;<span class="gray">务必填写好日期，注意时间格式</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>活动时间：</td>
					<td>
						<input type="text" name="timestr" style="width:150px;" class="input" value="<?php echo $thisactivity['timestr'];?>">&nbsp;&nbsp;<span class="gray">格式随意，如星期天下午3:00</span>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>活动人气：</td>
					<td>
						<input type="text" name="hit" style="width:150px;" class="input" value="<?php echo $thisactivity['hit'];?>">&nbsp;&nbsp;<span class="gray">活动点击数</span>
					</td>
				</tr>
				<tr>
					<td height="60" align="right"><span class="red">*</span>活动地点：</td>
					<td>
						<textarea class="textarea" name="address" style="width:400px; height:50px;"><?php echo $thisactivity['address'];?></textarea>
					</td>
				</tr>
				<tr>
					<td height="60" align="right">导航链接：</td>
					<td>
						<textarea class="textarea" name="navurl" style="width:400px; height:50px;"><?php echo $thisactivity['nav_url'];?></textarea>
					</td>
				</tr>
				<tr>
					<td height="60" align="right">报名须知：</td>
					<td>
						<textarea class="textarea" name="notice" style="width:650px; height:100px;"><?php echo $thisactivity['notice'];?></textarea>
					</td>
				</tr>
				<tr>
					<td height="40" align="right"><span class="red">*</span>活动详情：</td>
					<td>
						<script id="intro" name="intro" type="text/plain" style="width:650px;height:300px;"><?php echo $thisactivity['intro'];?></script>
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
<script type="text/javascript" src="../js/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="../lib/webuploader/webuploader.js"></script>
<script type="text/javascript" src="../lib/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="../lib/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	$("input[name=date]").datetimepicker({timepicker:false, format:'Y-m-d'});
	UE.getEditor('intro');
	init_radio("status", "<?php echo $thisactivity['status'];?>");
	init_radio("is_complete", "<?php echo $thisactivity['is_complete'];?>");
	init_radio("adult_along", "<?php echo $thisactivity['adult_along'];?>");
	init_radio("is_rmd", "<?php echo $thisactivity['is_rmd'];?>");
	init_radio("payway", "<?php echo $thisactivity['payway'];?>");
	$("select[name=cid]").val("<?php echo $thisactivity['cid'];?>");
	init_checkbox("tag[]", "<?php echo implode(',', $thistaglist);?>".split(","));
});

function check_form() {
	if(empty($("textarea[name=title]").val())) {
		$("textarea[name=title]").focus();
		SU.tip("活动主题不能为空。");
		return false;
	}
	if(empty($("select[name=cid]").val(), true)) {
		$("select[name=cid]").focus();
		SU.tip("请选择活动分类。");
		return false;
	}
	if(empty($("input[name=year_duration]").val())) {
		$("input[name=year_duration]").focus();
		SU.tip("报名年龄不能为空。");
		return false;
	}
	if(empty($("input[name=activity_type]").val())) {
		$("input[name=activity_type]").focus();
		SU.tip("活动场地不能为空。");
		return false;
	}
	if(empty($("input[name=date]").val())) {
		$("input[name=date]").focus();
		SU.tip("活动日期不能为空。");
		return false;
	}
	if(empty($("input[name=timestr]").val())) {
		$("input[name=timestr]").focus();
		SU.tip("活动时间不能为空。");
		return false;
	}
	if(empty($("textarea[name=address]").val())) {
		$("textarea[name=address]").focus();
		SU.tip("活动地点不能为空。");
		return false;
	}
	var price = $("input[name=price]").val();
	var reg	= new RegExp(/^\d*\.?\d{0,2}$/);
	if(!reg.test(price)) {
		$('input[name=price]').focus();
		SU.tip("非法的收费金额。");
		return false;
	}
	if(UE.getEditor('intro').hasContents() == false) {
		UE.getEditor('intro').focus();
		SU.tip("活动详情不能为空。");
		return false;
	}
	return true;
}
function empty(str, flag) {
	if(typeof str =="undefined") {
		return true;
	}
	str = str.replace(/^[\t\r\n\s]*/, '').replace(/[\r\t\s\n]*$/, '');
	if(str == '' || ( str == '0' && flag == false)) {
		return true;
	} else {
		return false;
	}
}
</script>
</body>
</html>