<?php
require("../lib/include.php");
$ac = trim($_GET['ac']);
$ad_uid = intval(_get_cookie("ad_uid"));
$ad_time = intval(_get_cookie("ad_time"));
$session_time = 900;
$thisurl = get_full_host().$_SERVER['REQUEST_URI'];
if(!in_array($ac, array('login', 'logout'))) {
	if($ad_uid > 0 && $ad_time > time() - $session_time) {
		$admin = $mysql_class->select_one("admin", "*", array('id'=>$ad_uid));
		if(empty($admin)) {
			header("Location: ./?ac=login&return=".rawurlencode($thisurl)); exit;
		} else {
			_set_cookie('ad_uid', $admin['id']); _set_cookie('ad_time', time());	
		}	
	} else {
		header("Location: ./?ac=login&return=".rawurlencode($thisurl)); exit;
	}
}
switch($ac) {
	case 'login':
		include("./tpl_login.php");
	break;
	case 'logout':
		_set_cookie('ad_uid', '', -3600); _set_cookie('ad_time', '', -3600);
		header("Location: ./?ac=login");
		exit;
	break;
	case 'setting':
		include("./tpl_setting.php");
	break;
	case 'user':
		include("./tpl_user.php");
	break;
	case 'order':
		include("./tpl_order.php");
	break;
	case 'comment':
		include("./tpl_comment.php");
	break;
	case 'connect':
		include("./tpl_connect.php");
	break;
	case 'sale':
		include("./tpl_sale.php");
	break;
	case 'card':
		include("./tpl_card.php");
		exit;
	break;
	case 'news':
		include("./tpl_news.php");
		exit;
	break;
	case 'ads':
		include("./tpl_ads.php");
		exit;
	break;
	case '':
	default:
		include("./tpl_index.php");
	break;
}
?>
