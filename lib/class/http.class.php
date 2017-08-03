<?php
// Author : me@yangdahong.cn
// This is a NOT FREE software

class http_class {
	var $errno = 0;
	var $errstr = '';
	var $fp = false;
	var $crlf = "\r\n";
	
	function get($url) {
		$url_param = $this->init_url($url);
		$this->init_fp($url_param['host'], $url_param['port']);
		$headers = sprintf('GET %s HTTP/1.0%s', $url_param['path'].($url_param['query'] ? '?'.$url_param['query'] : ''), $this->crlf);
		$headers .= sprintf('Accept:*/*%s', $this->crlf);
		$headers .= sprintf('HOST:%s%s', $url_param['host'], $this->crlf);
		$headers .= sprintf('Cache-Control:no-cache%s', $this->crlf);
		$headers .= sprintf('Connection:Close%s', str_repeat($this->crlf, 2));
		if(!fwrite($this->fp, $headers)) {
			$this->halt(-2, 'Can not input data');
		}
		return true;
	}
		
	function init_url($url) {
		$url_param = parse_url($url);
		if(!isset($url_param['port'])) {
			if(strtolower($url_param['scheme']) == 'http') {
				$url_param['port'] = '80';
			} elseif(strtolower($url_param['scheme']) == 'https') {
				$url_param['port'] = '43';
			} else {
				$url_param['port'] = '80';
			}
		}
		return $url_param;
	}
	
	function init_fp($host, $port) {
		@set_time_limit(0);
		if(function_exists('fsockopen')==false) {
			$this->halt(-1, 'function <b>fsockopen</b> is not supported');
		}
		$this->fp = @fsockopen($host, $port, &$this->errno, &$this->errstr, 10);
		if($this->fp == false || !is_resource($this->fp) || $this->errno>0) {
			$this->halt($this->errno, $this->errstr);
		}
	}
			
	function halt($errno=0, $errstr='') {
		if(empty($errno) || empty($errno)) {
			$errno = $this->errno;
			$errstr = $this->errstr;
		} else {
			$this->errno = $errno;
			$this->errstr = $errstr;
			exit('ErrorNo:'.$errno.'<br>ErrorResponse:<i>'.$errstr.'</i>');
		}
	}
	
	function __destruct() {
		if($this->fp) {
			fclose($this->fp);
		}
	}
}
?>