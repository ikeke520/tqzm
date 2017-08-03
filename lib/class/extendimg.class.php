<?php
class extendimg_class {
	var $query = "?t=201705051034";
	var $source = NULL;
	
	function create($headimgurl, $uid=1) {
		$des_path = "attachment/extend/{$uid}/".base64_encode($uid).".png";
		$is_create = false;
		if(filemtime(check_dir(dirname(STCMS_ROOT)).$des_path) < strtotime("2016-05-05 19:40:00")) {
			$is_create = true;
		}
		if(!file_exists(check_dir(dirname(STCMS_ROOT)).$des_path) || $is_create) {
			require_once(STCMS_ROOT."phpqrcode/phpqrcode.php");
			$tmpfile = check_dir(dirname(STCMS_ROOT))."attachment/temp/qrcode/".date("ymdhis").rand(100, 999).".png";
			mkdirs(dirname($tmpfile));
			QRcode::png(check_dir($GLOBALS['config']['web_url'])."e/?fuid={$uid}", $tmpfile, "H", 12, 2);
			$qrimg =  imagecreatefrompng(realpath($tmpfile));
			$qrx = imagesx($qrimg);
			$qry = imagesy($qrimg);
			$this->source = imagecreatetruecolor($qrx, $qry);
			imagecopyresampled($this->source, $qrimg, 0, 0, 0, 0, $qrx, $qry, $qrx, $qry);
			$avatarimg = imagecreatefromstring(http_request($headimgurl));
			imagecopyresampled($this->source, $avatarimg, $qrx*0.4, $qry*0.4, 0, 0, $qrx*0.2, $qry*0.2, imagesx($avatarimg), imagesy($avatarimg));
			mkdirs(dirname(check_dir(dirname(STCMS_ROOT)).$des_path));
			imagepng($this->source, check_dir(dirname(STCMS_ROOT)).$des_path, 9);
			remove_file($tmpfile);
		}
		return check_dir($GLOBALS['config']['web_url']).$des_path.$this->query;
	}
	
}
?>