<?php
if($_GET['update'] == "true") {
	$data['coin'] = intval($_POST['costcoin']);
	$data['msg'] = htmlspecialchars($_POST['msg']);
	if(empty($data['coin']) || empty($data['msg'])) {
		exit(json_encode(array("error"=>1, "msg"=>"兑换积分、兑换物品不能为空。")));
	}
	if($data['coin'] > $thisuser['return_coin']) {
		exit(json_encode(array("error"=>1, "msg"=>"积分不足，兑换失败。")));
	}
	$data['time'] = NOW;
	$data['type'] = '2';
	$data['uid'] = $thisuser['id'];
	$mysql_class->update("users", "return_coin=return_coin-{$data['coin']}", array("id"=>$thisuser['id']));
	$mysql_class->insert("users_coinlog", $data);
	adminlog("为用户办理积分兑换：".addslashes($thisuser['nickname']));
	exit(json_encode(array("error"=>0)));
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>积分兑换</title>
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
.tip {display:inline-block; color:#333; position:absolute; height:50px; line-height:50px; z-index:251}
.tip span.left{ position:absolute; left:0; top:1px; display:inline-block; background:url(../images/tip-bg.png) -7px -55px no-repeat; height:50px; width:45px;}
.tip span.center{ margin:0 3px 0 43px; background:url(../images/tip-bg.png) left -162px repeat-x; display:inline-block; height:50px; line-height:50px; font-size:14px; padding: 0 5px 0 15px; width:auto; font-weight:bold;}
.tip span.right{display:inline-block; height:50px; line-height:50px; width:4px; position:absolute; right:0; top:0px; background:url(../images/tip-bg.png) left top no-repeat;}
.table{ border-top:none; width:100%;}
.table td,.table th{ background:#fff; border:none; padding:9px 2px; padding:4px; font-size:12px; line-height:150%;}
.table th{ background:url(../images/user_bgx.gif) repeat-x 0 -67px;}
</style>
</head>
<body>
<form id="listform">
<table style="margin-top:10px;" width="100%" bgcolor="#c2c3c8" cellspacing="1" cellpadding="0" class="table mt10" border="0">
    <tbody>
    <tr>
        <td align="right">用户名：</td>
        <td><?php echo $thisuser['nickname'];?></td>
    </tr>
    <tr>
        <td align="right">用户积分：</td>
        <td><?php echo $thisuser['return_coin'];?></td>
    </tr>
    <tr>
        <td align="right">兑换积分：</td>
        <td><input type="text" class="input" name="costcoin"></td>
    </tr>
    <tr>
        <td align="right">兑换物品：</td>
        <td><textarea class="textarea" name="msg" style="width:280px; height:60px;"></textarea></td>
    </tr>
    <tr>
        <td colspan="11" height="50" align="center"><input type="submit" value="提交" id="hiddensubmit" style="display:none;" />
        <a href="javascript:void(0);" onclick="exchange();" class="submit"><span>确认兑换</span></a></td>
    </tr>
    </tbody>
</table>
</form>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/su.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript">
function go_page(n) {
	$("input[name=page]").val(n);
	$("#search-form").submit();
}
function exchange() {
	if(empty($("input[name=costcoin]").val())) {
		$("input[name=costcoin]").focus();
		SU.tip("兑换积分不能为空。");
		return false;
	}
	if(!/^\d+$/.test($("input[name=costcoin]").val())) {
		$("input[name=costcoin]").focus();
		SU.tip("兑换积分填写有误。");
		return false;
	}
	if(empty($("textarea[name=msg]").val())) {
		$("textarea[name=msg]").focus();
		SU.tip("兑换物品不能为空。");
		return false;
	}
	$.ajax({
		url:"./?ac=user&do=edit&step=exchange&update=true&id=<?php echo $thisuser['id'];?>",
		type:"POST",
		data:{"costcoin":$("input[name=costcoin]").val(), "msg":$("textarea[name=msg]").val()},
		dataType:"json",
		success: function(data) {
			if(data.error > 0 ) {
				alert(data.msg);
			} else {
				SU.tip("兑换成功。");
				setTimeout(function(){parent.location.reload();}, 1003);
			}
		}
	});
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