<?php
include("../lib/include.php");
include(STCMS_ROOT."wxconfig.php");
$wxmessage_class = load_class("wxmessage");
$wxmessage_class->run();
exit();
function checkSignature() {
	$signature = $_GET["signature"];
	$timestamp = $_GET["timestamp"];
	$nonce = $_GET["nonce"];	
	$token = "zgtqzm";
	$tmpArr = array($token, $timestamp, $nonce);
	sort($tmpArr, SORT_STRING);
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );
	if( $tmpStr == $signature ){
		return true;
	} else {
		return false;
	}
}
if(checkSignature()) {
	exit($_GET['echostr']);
}
?>