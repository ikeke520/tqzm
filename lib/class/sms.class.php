<?php
class sms_class {
	public $data;
	public $timeout = 30;
	private $apiUrl = "http://www.ztsms.cn/sendSms.do";
	private $username = "zhth";
	private $password = "acaZs1Md";

	function __construct() {
		
	}

	private function httpPost(){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->apiUrl);  
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);   
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS,  http_build_query($this->data));
		curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);    
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);
		if (curl_errno($curl)) {      
			echo 'Error POST'.curl_error($curl);      
		}      
		curl_close($curl);
		// 2017-05-18 添加短信记录
		$logfile = STCMS_ROOT."sms.log";
		$fp = fopen($logfile, "a");
		$log = sprintf("[%s] Phone:%s productid:%s\r\ncontent:%s\r\nreturn:%s\r\n\r\n", NOW, $this->data['mobile'], $this->data['productid'], $this->data['content'], $result);
		fwrite($fp, $log);
		fclose($fp);
		return $result;
	}

	public function sendSMS($phone, $msg, $pid) {
		$this->data = array(
			"content"=>$msg,
			"username"=>$this->username,
			"password"=>md5($this->password),
			"mobile"=>$phone,
			"productid"=>$pid,
		);
		
		$this->data['content'] 	= $isTranscoding === true ? mb_convert_encoding($this->data['content'], "UTF-8") : $this->data['content'];
		$this->data['username'] = $this->username;
		$this->data['password'] = md5($this->password);
		return $this->httpPost();
	}

}
?>