<?php
defined('STCMS_ROOT') or die('Access Deined!');
function prepare($str) {
	if(strpos($str, ",")) {
		return "\"{$str}\"";
	} else {
		return $str;
	}
}
function utf82gbk($str) {
	return mb_convert_encoding($str, "GBK", "UTF-8");
}
$sextype = intval($_GET['sextype']);
$enddate = trim($_GET['enddate']);
if(!preg_match("/^20\d{2}\-\d{2}\-\d{2}$/", $enddate)) {
	$enddate = '';
}
$startdate = trim($_GET['startdate']);
if(!preg_match("/^20\d{2}\-\d{2}\-\d{2}$/", $startdate) || $startdate > $enddate) {
	$startdate = '';
}

$page = intval($_GET['page']);
$per_page = intval($_GET['per_page']) ? intval($_GET['per_page']) : 20;
$keyword = trim($_GET['keyword']);
$where = "1";
switch($sextype) {
	case 1:
		$where .= " AND sex='0'";
	break;
	case 2:
		$where .= " AND sex='1'";
	break;
}
if($startdate && $enddate) {
	$where .= " AND birthday >= '{$startdate}' AND birthday <= '{$enddate}'";
}
if(empty($keyword) == false) {
	$where .= " AND (name LIKE '%".$keyword."%' OR address LIKE '%".$keyword."%')";
}
$where .= " AND is_adult='0'";
if($_GET['is_output'] == "true") {
	require_once(STCMS_ROOT."class/PHPExcel.php");
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()->setCreator("程序开发：STCMS/me@yangdahong.cn")
								 ->setLastModifiedBy("程序开发：STCMS/me@yangdahong.cn")
								 ->setTitle("客户CRM信息")
								 ->setSubject("程序开发：STCMS/me@yangdahong.cn")
								 ->setDescription("程序开发：STCMS/me@yangdahong.cn")
								 ->setKeywords("客户 crm me@yangdhaong.cn");
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', "ID")
				->setCellValue('B1', "小孩姓名")
				->setCellValue('C1', "性别")
				->setCellValue('D1', "出生日期")
				->setCellValue('E1', "区域")
				->setCellValue('F1', "身份证号")
				->setCellValue('G1', "家长姓名")
				->setCellValue('H1', "家长电话");
	$query = $mysql_class->query("SELECT * FROM {$config['db_prefix']}users_person WHERE {$where}");
	$rownum = 2;
	if($query) {
		while($tmpdata = $mysql_class->fetch($query)) {
			$tmpadult = $mysql_class->fetch($mysql_class->query("SELECT * FROM {$config['db_prefix']}users_person WHERE id IN (SELECT pid FROM {$config['db_prefix']}users_auth_person WHERE aid IN (SELECT aid FROM {$config['db_prefix']}users_auth_person WHERE pid='{$tmpdata['id']}' AND is_adult='0') AND is_adult='1')"));
			if(empty($tmpadult)) {
				$tmpadult = $mysql_class->fetch($mysql_class->query("SELECT * FROM {$config['db_prefix']}users_person WHERE id IN (SELECT pid FROM {$config['db_prefix']}users_auth_person WHERE uid ='{$tmpdata['uid']}' AND is_adult='1')"));
			}
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue("A{$rownum}", $tmpdata['id'])
						->setCellValue("B{$rownum}", $tmpdata['name'])
						->setCellValue("C{$rownum}", ($tmpdata['sex'] ? "女" : "男"))
						->setCellValue("D{$rownum}", $tmpdata['birthday'])
						->setCellValue("E{$rownum}", $tmpdata['address'])
						->setCellValue("F{$rownum}", $tmpdata['idno'])
						->setCellValue("G{$rownum}", $tmpadult['name'])
						->setCellValue("H{$rownum}", $tmpadult['phone']);
			$rownum++;
		}
		
	}
	$objPHPExcel->getActiveSheet()->setTitle('客户CRM信息');
	$objPHPExcel->setActiveSheetIndex(0);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="客户CRM信息.xlsx"');
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1');
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header ('Cache-Control: cache, must-revalidate');
	header ('Pragma: public');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
}
$total_records = $mysql_class->num_table("users_person", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select("users_person", "*", $where, "id DESC", array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>客户CRM分析__后台管理中心__<?php echo $config['seo_title'];?></title>
<link href="../css/control.css" type="text/css" rel="stylesheet">
<link href="../js/jquery.datetimepicker.css" type="text/css" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
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
	<div class="position">您的位置：<a href="../" target="_blank"><?php echo $config['seo_title'];?></a>&nbsp;&raquo;&nbsp;<a href="./">后台管理中心</a>&nbsp;&raquo;&nbsp;<a href="./?ac=user">用户管理</a>&nbsp;&raquo;&nbsp;CRM管理</div>
	<div class="mainright">
		<div class="tab">
			<ul>
				<li><a href="./?ac=user">用户列表</a></li>
				<li><a href="./?ac=user&do=analysis">用户分析</a></li>
                <li class="on"><a href="./?ac=user&do=crm">CRM管理</a></li>
                <li><a href="./?ac=sale&do=index&step=rank">会员消费排行</a></li>
				<div class="clear"></div>
			</ul>
		</div>
		<div class="mt10">
			<div class="thead">
				<form id="search-form" method="get" action="./">
					<input type="hidden" name="ac" value="user" />
                    <input type="hidden" name="do" value="crm" />
					&nbsp;&nbsp;性别：
					<select class="select" name="sextype">
						<option value="0">全部</option>
						<option value="1">男</option>
						<option value="2">女</option>
					</select>
                    &nbsp;&nbsp;出生日期：<input type="text" class="input" name="startdate" style="width:80px;" value="<?php echo $startdate;?>">
                    &nbsp;-&nbsp;<input type="text" class="input" name="enddate" style="width:80px;" value="<?php echo $enddate;?>">
					&nbsp;&nbsp;关键词：
					<input class="input" type="text" name="keyword" style="width:80px;" value="<?php echo $keyword; ?>" />
					&nbsp;&nbsp;分页：
					<select class="select" name="per_page">
						<option value="20">20条每页</option>
						<option value="50">50条每页</option>
						<option value="100">100条每页</option>
					</select>
					&nbsp;&nbsp;<a href="javascript:void(0)" onclick="$('#search-form').submit();" class="button">搜索</a>
					<input type="hidden" name="page" value="1" />
				</form>
			</div>
			<form id="listform">
				<table style="margin-top:-1px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table" border="0">
					<tbody>
						<tr>
							<th>ID</th>
							<th>小孩姓名</th>
							<th>性别</th>
							<th>出生日期</th>
							<th>区域</th>
                           	<th>身份证号</th>
                            <th>联系家长</th>
							<th>电话</th>
						</tr>
<?php
if($records) {
	foreach($records as $list) {
		$tmpadult = $mysql_class->fetch($mysql_class->query("SELECT * FROM {$config['db_prefix']}users_person WHERE id IN (SELECT pid FROM {$config['db_prefix']}users_auth_person WHERE aid IN (SELECT aid FROM {$config['db_prefix']}users_auth_person WHERE pid='{$list['id']}' AND is_adult='0') AND is_adult='1')"));
		if(empty($tmpadult)) {
			$tmpadult = $mysql_class->fetch($mysql_class->query("SELECT * FROM {$config['db_prefix']}users_person WHERE id IN (SELECT pid FROM {$config['db_prefix']}users_auth_person WHERE uid ='{$list['uid']}' AND is_adult='1')"));
		}
?>
						<tr>
							<td><?php echo $list['id'];?></td>
							<td><?php echo $list['name'];?></td>
							<td><?php if($list['sex'] == 0 ){?>男<?php }else if($list['sex'] == 1){?>女<?php }?></td>
							<td><?php echo $list['birthday'];?></td>
							<td><?php echo $list['address'];?></td>
							<td><?php echo $list['idno'];?></td>
                        <td><?php echo $tmpadult['name'];?>（<?php if($tmpadult['sex'] == 0 ){?>宝爸<?php }else if($tmpadult['sex'] == 1){?>宝妈<?php }?>）</td>
                            <td><?php echo $tmpadult['phone'];?></td>
						</tr>
<?php
	}
}
?>
						<tr>
							<td colspan="11"><a href="javascript:void(0);" onclick="output();" class="submit"><span>导出为XSL</span></a></td>
						</tr>
						<tr>
							<td colspan="11"><?php echo $page_class->get_js_code('go_page'); ?></td>
						</tr>
					</tbody>
				</table>
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
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/su.js"></script>
<script type="text/javascript" src="../js/jquery.datetimepicker.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	$("select[name=per_page]").val("<?php echo $per_page;?>");
	$("select[name=sextype]").val("<?php echo $sextype;?>");
	$("input[name=startdate]").datetimepicker({timepicker:false, format:'Y-m-d'});
	$("input[name=enddate]").datetimepicker({timepicker:false, format:'Y-m-d'});
});
function go_page(n) {
	$("input[name=page]").val(n);
	$("#search-form").submit();
}
function output() {
	var url = location.href;
	window.open(url+"&is_output=true");
}
</script>
</body>
</html>