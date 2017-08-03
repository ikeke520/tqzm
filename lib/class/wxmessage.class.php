<?php
class wxmessage_class {
	var $wxid = "gh_fe6883a911aa";
	var $responseTxt = "您的留言已收到，暂无客服在线，我们将在24小时内与您联系。";
	var $responseKf = "<xml>
     <ToUserName><![CDATA[%s]]></ToUserName>
     <FromUserName><![CDATA[%s]]></FromUserName>
     <CreateTime>%d</CreateTime>
     <MsgType><![CDATA[transfer_customer_service]]></MsgType>
 </xml>";
	var $responseGz = '<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%d</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>1</ArticleCount>
<Articles>
<item>
<Title><![CDATA[美好的一周从“童趣”开始]]></Title> 
<Description><![CDATA[孩子成长过程中的每分每秒，对每位父母而言，都是生命中独一无二的宝贵时光，一旦错过，永不再来。天底下的每个孩子，都有被爱的权利。]]></Description>
<PicUrl><![CDATA[http://www.zgtqzm.com/images/welcome.jpg]]></PicUrl>
<Url><![CDATA[http://www.zgtqzm.com/]]></Url>
</item>
</Articles>
</xml>';
	function run() {
		global $mysql_class;
		$data = $this->get_post();
		if(empty($data)) {
			return ;
		}
		$data = $this->init_user($data);
		if($data['nickname'] || ($data['type'] == "event" && $data['content'] == "subscribe")) {
			if($data['type'] != "event") {
				$mysql_class->insert("message", $data);
				$data['id'] = $mysql_class->insert_id();
				$this->download_attachment($data);
			}
			$this->response($data);
		}
	}
	function get_post() {
		$xmldata = file_get_contents("php://input");
		if(empty($xmldata)) {
			$xmldata = $GLOBALS['HTTP_RAW_POST_DATA'];
		}
		$xmldoc = simplexml_load_string($xmldata, 'simpleXmlElement', LIBXML_NOCDATA);
		$retarray = array();
		if(in_array($xmldoc->MsgType, array("text", "event", "image", "video", "shortvideo", "voice", "link", "location"))) {
			switch($xmldoc->MsgType) {
				case 'text':
					$retarray['type'] = 'text';
					$retarray['openid'] = (string)$xmldoc->FromUserName;
					$retarray['msgid'] = (string)$xmldoc->MsgId;
					$retarray['content'] = (string)$xmldoc->Content;
					$retarray['time'] = (string)$xmldoc->CreateTime;
				break;
				case 'event':
					$retarray['type'] = 'event';
					$retarray['openid'] = (string)$xmldoc->FromUserName;
					$retarray['msgid'] = (string)$xmldoc->MsgId;
					$retarray['content'] = (string)$xmldoc->Event;
					$retarray['time'] = (string)$xmldoc->CreateTime;
				break;
				case 'image':
					$retarray['type'] = 'image';
					$retarray['openid'] = (string)$xmldoc->FromUserName;
					$retarray['msgid'] = (string)$xmldoc->MsgId;
					$retarray['content'] = (string)$xmldoc->PicUrl;
					$retarray['comment'] = (string)$xmldoc->MediaId;
					$retarray['time'] = (string)$xmldoc->CreateTime;
				break;
				case 'video':
				case 'shortvideo':
					$retarray['type'] = 'video';
					$retarray['openid'] = (string)$xmldoc->FromUserName;
					$retarray['msgid'] = (string)$xmldoc->MsgId;
					$retarray['content'] = (string)$xmldoc->ThumbMediaId;
					$retarray['comment'] = (string)$xmldoc->MediaId;
					$retarray['time'] = (string)$xmldoc->CreateTime;
				break;
				case 'voice':
					$retarray['type'] = 'voice';
					$retarray['openid'] = (string)$xmldoc->FromUserName;
					$retarray['msgid'] = (string)$xmldoc->MsgId;
					$retarray['content'] = (string)$xmldoc->Recognition;
					$retarray['comment'] = (string)$xmldoc->MediaId;
					$retarray['time'] = (string)$xmldoc->CreateTime;
				break;
				default:
					$retarray['type'] = 'other';
					$retarray['openid'] = (string)$xmldoc->FromUserName;
					$retarray['msgid'] = (string)$xmldoc->MsgId;
					$retarray['time'] = (string)$xmldoc->CreateTime;
				break;			
			}
			$retarray['time'] = date("Y-m-d H:i:s", (int)$retarray['time']);
		}
		return $retarray;
	}
	
	function init_user($data) {
		global $mysql_class;
		if(empty($data['openid'])) {
			return false;
		}
		if($thisuser = $mysql_class->select_one("users", "id, nickname, is_reg", array("openid"=>$data['openid']))) {
			// 20161029 更新监听取消用户订阅时间
			if($data['type'] == "event" && $data['content'] == "unsubscribe") {
				if($thisuser['is_reg'] == "1") {
					$mysql_class->update("users", array("is_reg"=>"0"), array("id"=>$thisuser['id']));
				}
			} else {
				if($thisuser['is_reg'] == "0") {
					$mysql_class->update("users", array("is_reg"=>"1"), array("id"=>$thisuser['id']));
				}
			}
		} else {
			$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".get_access_token()."&openid=".$data['openid']."&lang=zh_CN";
			$response = @json_decode(http_request($url));
			$thisuser = array(
				"openid"=>$response->openid,
				"unionid"=>$response->unionid,
				"is_reg"=>$response->subscribe,
				"nickname"=>$response->nickname,
				"sex"=>$response->sex,
				"city"=>$response->city,
				"province"=>$response->province,
				"country"=>$response->country,
				"headimgurl"=>$response->headimgurl,
				"reg_time"=>NOW,
				"ip"=>get_ip(),
				"log_time"=>NOW
			);
			if($thisuser['nickname']) {
				$mysql_class->insert("users", $thisuser);
				$thisuser['id'] = $mysql_class->insert_id();
			}
		}
		$data['uid'] = $thisuser['id'];
		$data['nickname'] = addslashes($thisuser['nickname']);
		return $data;
	}
	
	function download_attachment($data) {
		global $mysql_class, $config;
		if(in_array($data['type'], array("image", "video", "voice"))) {
			$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".get_access_token()."&media_id=".$data['comment'];
			$headers = get_headers($url, 1);
			if(preg_match('/filename="(.+?)"/i', $headers['Content-disposition'], $matches)) {
				$filename = $matches[1];
			}
			if(empty($filename)) {
				return;
			}
			$filefix = "message/{$data['id']}/".date("ymdhis").rand(100, 999).".".get_file_ext($filename);
			$attfile = check_dir(dirname(STCMS_ROOT))."attachment/".$filefix;
			mkdirs(dirname($attfile));
			file_put_contents($attfile, http_request($url));
			if($data['type'] == "voice" && get_file_ext($filename) != "mp3") {
				$mp3file = ffmpeg2mp3($attfile);
				@unlink($attfile);
				$filefix = preg_replace("/\.".get_file_ext($filename)."$/i", ".mp3", $filefix);
				move_attachment($mp3file, $filefix);
			}
			$mysql_class->update("message", array("content"=>check_dir($config['web_url'])."attachment/".$filefix), array("id"=>$data['id']));
		}
	}
	
	function response($data) {
		if($data['type'] == "event" && $data['content'] == "subscribe") {
			printf($this->responseGz, $data['openid'], $this->wxid, time());
			exit;
		}
		$xml = '<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%d</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>';
		$url = "https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token=".get_access_token();
		$response = json_decode(http_request($url));
		if($response->kf_online_list) {
			printf($this->responseKf, $data['openid'], $this->wxid, time());
		} else {
			printf($xml, $data['openid'], $this->wxid, time(), $this->responseTxt);
		}
	}
}
?>