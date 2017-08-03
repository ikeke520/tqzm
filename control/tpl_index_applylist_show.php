<?php
$thisperson = $mysql_class->select_one("users_person", "*", array("id"=>intval($_GET['pid'])));


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
<table style="margin-top:10px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table mt10" border="0">
    <tbody>
    <tr>
        <td>类型</td>
        <td><?php if($thisperson['is_adult']) {?> 大人 <?php } else {?> 小孩 <?php }?></td>
    </tr>
    <tr>
        <td>姓名</td>
        <td><?php echo $thisperson['name'];?></td>
    </tr>
    <tr>
        <td>生日</td>
        <td><?php echo $thisperson['birthday'];?></td>
    </tr>
    <tr>
        <td>地址</td>
        <td><?php echo $thisperson['address'];?></td>
    </tr>
    <tr>
        <td>身份证号</td>
        <td><?php echo $thisperson['idno'];?></td>
    </tr>
    <tr>
        <td>手机号码</td>
        <td><?php echo $thisperson['phone'];?></td>
    </tr>
    
    </tbody>
</table>
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