<?php
$func = trim($_GET['func']);
if(empty($func)) {
	$func = 'select_user';
}
$keyword = trim($_GET['keyword']);
$where = "1";
if($keyword) {
	$where .= " AND nickname LIKE '%{$keyword}%' OR phone LIKE '%{$keyword}%' OR openid LIKE '%{$keyword}%' OR phone LIKE '%{$keyword}%'";
}
$page = intval($_GET['page']);
$per_page = 10;
$total_records = $mysql_class->num_table("users", $where);
$page_class = load_class('page');
$page_class->init($page, $total_records, $per_page);
$records = $mysql_class->select("users", "*", $where, 'id DESC', array(($page_class->page-1)*$per_page, $per_page));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>选择用户</title>
<style>
*{ margin:0; padding:0; font-family:"Microsoft YaHei", "SimHei", "STHeiti", "SimSun", "STSong"; outline:none;}
body{ padding:10px; font-size:12px; line-height:150%;}
a{ text-decoration:none; color:#2C3E50;}
.input{ outline:none; background:url(../images/input_bg_sd.png) no-repeat #fff; border:1px solid #CCCCCC; width:250px; height: 24px; line-height:24px;border-radius:2px; padding-left:4px;}
.input:focus{border:1px solid #0077FF; box-shadow:0 0 5px #8cddff; -moz-box-shadow:0 0 5px #8cddff; -webkit-box-shadow:0 0 5px #8cddff;}
.textarea{ outline:none; width:250px; background:url(../images/input_bg_sd.png) no-repeat #fff; border:1px solid #CCCCCC; line-height:24px;border-radius:2px; padding-left:4px; font-size:14px;}
.textarea:focus{border:1px solid #0077FF; box-shadow:0 0 5px #8cddff; -moz-box-shadow:0 0 5px #8cddff; -webkit-box-shadow:0 0 5px #8cddff;}
.select{ min-width:60px; text-align:center; height:24px; line-height:24px;}
a.button{ background:url(../images/user_bgx.gif) repeat-x 0 -164px; line-height:26px; border:1px solid #a8a8a8; -moz-border-radius:4px; -webkit-border-radius:4px; color:#333; border-radius:4px; display:inline-block; padding:0px 15px;text-shadow: 1px 1px 0 #FFFFFF; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); text-indent:0;}
a.button span{color:#333; font-size:12px; margin:0;}
a.submit{ background:url(../images/submit-bg.png); line-height:40px; display:inline-block; cursor:pointer; padding:0px 0 0 15px; text-shadow: 1px 1px 0 #FFFFFF; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);}
a.submit span{color:#333; display:inline-block; line-height:40px; height:40px; background:url(../images/submit-bg.png) right; font-size:14px; margin:0; padding-right:15px;}
.page-list{}
.page-list a{ background:url(../images/user_bgx.gif) repeat-x 0 -164px;line-height:26px; border:1px solid #a8a8a8; -moz-border-radius:4px; -webkit-border-radius:4px; color:#333; border-radius:4px; display:inline-block; padding:0px 10px;text-shadow: 1px 1px 0 #FFFFFF; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); text-indent:0;}
.page-list a span{color:#333; font-size:14px; margin:0;}
.page-list a.focus{ color:gray; background:#fff; border:1px solid #ececec;}
.page-list a.focus span{ color:gray;}
.table{ border-top:none; width:100%;}
.table td,.table th{ background:#fff; border:none; text-align:center; padding:9px 2px; font-size:12px; line-height:150%;}
.table th{ background:url(../images/user_bgx.gif) repeat-x 0 -67px;}
</style>
</head>
<body>
<div class="thead">
	<form id="search-form" method="get" action="./">
		<input type="hidden" name="ac" value="sale" />
		<input type="hidden" name="do" value="getuser" />
		<input type="hidden" name="func" value="<?php echo $func;?>">
	关键词：<input class="input" type="text" name="keyword" style="width:80px;" value="<?php echo $keyword; ?>" />
	&nbsp;&nbsp;<a href="javascript:void(0)" onclick="$('#search-form').submit();" class="button">搜索</a>
	<input type="hidden" name="page" value="1" />
	</form>
	</div>
	<form id="listform">
	<table style="margin-top:10px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table mt10" border="0">
		<tbody>
		<tr>
			<th>选择</th>
			<th>ID</th>
			<th>名称</th>
		</tr>
<?php
if($records) {
foreach($records as $list) {
?>
		<tr>
			<td><input type="radio" name="id" data-headimgurl="<?php echo $list['headimgurl'];?>" data-name="<?php echo $list['nickname'];?>" value="<?php echo $list['id'];?>"></td>
			<td><?php echo $list['id'];?></td>
			<td><?php echo $list['nickname'];?></td>
		</tr>
<?php
}
}
?>
		<tr>
			<td colspan="11"><?php echo $page_class->get_js_code('go_page'); ?></td>
		</tr>
	<tr>
		<td colspan="11" height="50" align="center"><input type="submit" value="提交" id="hiddensubmit" style="display:none;" />
			<a href="javascript:void(0);" onclick="select_user();" class="submit"><span>确认选择</span></a></td>
	</tr>
		</tbody>
	</table>
	</form>
</div>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/su.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript">
function go_page(n) {
	$("input[name=page]").val(n);
	$("#search-form").submit();
}
function select_user() {
	var tid = get_radio_value('id');
	var name = $('input[name="id"][value="'+tid+'"]').attr('data-name');
	var headimgurl = $('input[name="id"][value="'+tid+'"]').attr('data-headimgurl');
	return parent.<?php echo $func; ?>({'id':tid, 'name':name, 'picture':headimgurl});
}
function empty(str) {
	if(typeof str == "undefined") {
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