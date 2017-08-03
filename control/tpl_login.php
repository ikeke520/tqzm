<?php defined('STCMS_ROOT') or die('Access denied!');
$session_time = 900;
$return = rawurldecode(trim($_GET['return']));
$do = trim($_GET['do']);
if(empty($return)) {
	$return = $_SERVER['HTTP_REFERER'];
}
if(intval(_get_cookie('ad_uid') > 0  && intval(_get_cookie('ad_time') + $session_time > time()))) {
	header("Location: ".$return); exit;
}
if($do == 'true') {
	$uname = trim($_POST['uname']);
	$password = md5(md5($_POST['password']));
	if($admin = $mysql_class->select_one("admin", '*', array('name'=>$uname, 'pwd'=>$password))) {
		_set_cookie('ad_uid', $admin['id']); _set_cookie('ad_time', time());
		$mysql_class->update("admin", "last_login='".NOW."', ip='".get_ip()."', times=times+1", array('id'=>$admin['id']));
		if(preg_match("#^".preg_quote(check_dir($config['web_url'])."control/?ac=login", '#')."#i", $return) || preg_match("#^".preg_quote(check_dir($config['web_url'])."control/?ac=logout")."#i", $return)) {
			$return = check_dir($config['web_url'])."control/";
		}
		header("Location: ".$return); exit;
	} else {
		halt("管理员名称或登陆密码错误。");
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>后台管理登录</title>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE10" />
<meta name="viewport" content="width=device-width" />
<style type="text/css">
body { margin: 0; padding: 0; background: #fff; font-family: "Microsoft YaHei", "SimHei", "STHeiti";}
a{color:#000; text-decoration:none;}
#webtitle{ background:#272727; height:150px; color:#DBEEF3; text-align:center; line-height:80px; font-size:200%; }
#container{ width:500px; margin:-68px auto 0 auto;}
#form-panel{ width:390px; padding:0 0 20px 0; margin:0 auto; background:#fff; border:solid 1px #ccc; border-radius:10px;}
#title{ height:67px; font-size:18px; color:#000; text-align:center; line-height:67px;}
#line{ border-top:solid 1px #e4e4e4; margin-bottom:30px;}
#form{ padding:0 25px 0 25px; position:relative;}
.field{ width:325px; height:37px; color:#888888; border:solid 1px #ccc; border-radius:4px; padding:8px; font-size:16px; line-height:37px; outline:none;}
.text{ color:#888888; font-size:12px; margin:10px 0 4px 0;}
#comment{ color:#313131; font-size:12px; position:absolute; left:25px; top:330px;}
.btntc{ text-align:center; margin-top:30px;}
#submit{ outline:none; display:inline-block; border:none; cursor:pointer; width:118px; height:45px; line-height:45px; background:#272727; color:#DBEEF3; text-align:center; font-size:16px; border-radius:4px;}
.tip {display:inline-block; color:#333; position:absolute; height:50px; line-height:50px; z-index:251}
.tip span.left{ position:absolute; left:0; top:0; display:inline-block; background:url(../images/tip-bg.png) -7px -55px no-repeat; height:50px; width:45px;}
.tip span.center{ margin:0 3px 0 43px; background:url(../images/tip-bg.png) left -162px repeat-x; display:inline-block; height:50px; line-height:50px; font-size:14px; padding: 0 5px 0 15px; width:auto; font-weight:bold;}
.tip span.right{display:inline-block; height:50px; line-height:50px; width:4px; position:absolute; right:0; top:-1px; background:url(../images/tip-bg.png) left top no-repeat;}
</style>
</head>
<body>
<div id="webtitle"></div>
<div id="container">
	<div id="form-panel">
		<div id="title">后台管理登录</div>
		<div id="line"></div>
		<div id="form">
			<form method="post" action="./?ac=login&do=true&sid=<?php echo SID; ?>&return=<?php echo rawurlencode($return); ?>" onsubmit="return login();">
				<div class="text">管理员名称</div>
				<input type="text" class="field stinput" stdefault="请输入用户名" name="uname" value="" />
				<div class="text">登陆密码</div>
				<input type="password" class="field stinput" name="password" value="" />
				<div class="btntc"><input type="submit" value="登录后台" id="submit" /></div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/su.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	$('.stinput').stinput();
});
function login() {
	var uname = $('input[name=uname]').val();
	var password = $('input[name=password]').val();
	if(empty(uname) || uname == $('input[name=uname]').attr('stdefault')) {
		$('input[name=uname]').focus();
		SU.tip("管理员名称必须填写！");
		return false;
	}
	if(empty(password)) {
		$('input[name=password]').focus();
		SU.tip("登陆密码必须填写！");
		return false;
	}
	return true;
}

function empty(str) {
	if(typeof str == 'undefined') {
		return true;
	}
	str = str.replace(/^[\t\r\n\s]*/, '').replace(/[\r\t\s\n]*$/, '');
	if(str == '') {
		return true;
	} else {
		return false;
	}
}
</script>
</body>
</html>