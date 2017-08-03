<?php
// This is not a free software
// Copyright (c) Http://www.phpstcms.com
// Author: Dahongy <me@yangdahong.cn>

defined('STCMS_ROOT') or die('Access Denied!');
class page_class {
	var $display_num = 9;
	function init($page, $total, $per_num=40, $url='', $display_num=9) {
		$this->total = intval($total);
		$this->per_num = intval($per_num);
		$this->max_page = ceil($this->total / $this->per_num);
		$this->max_page = max($this->max_page, 1);
		$this->page = $this->verify_page(intval($page)); 
		$this->next_page = $this->verify_page($page+1);
		$this->pre_page = $this->verify_page($page-1);
		$this->isfull = (strpos($url, '?') or strpos($url, '&')); 
		$this->url = $url;
		$this->display_num = $display_num;
	}
	
	function verify_page($page) {
		$page = $page>=$this->max_page?$this->max_page:$page;
		$page = $page<=1?1:$page;
		return $page;
	}
	
	function get_code() {
		$step = intval($this->display_num/2);
		$min = $this->verify_page($this->page-$step);
		$max = $this->verify_page($this->page+$step);
		$next_step = $this->verify_page($this->page+$this->display_num);
		$pre_step = $this->verify_page($this->page-$this->display_num);
		$html = "<div class=\"page-list\">";
		if($min > 1) {
			$html .= $this->set_page(1);
			$html .= $this->set_page($pre_step, '&lt;&lt;');
		}
		for($n=$min; $n<=$max; $n++) {
			$html .= $this->set_page($n);
		}
		if($max < $this->max_page) {
			$html .= $this->set_page($next_step, '&gt;&gt;');
			$html .= $this->set_page($this->max_page);
		}
		//$html .= $this->info();
		$html .= "</div>";
		return $html;
	}
	
	function set_page($page, $name='') {
		$html = "<a".($page==$this->page?" class=\"focus\"":"")." href=\"".($this->isfull?"{$this->url}&page={$page}":"{$this->url}?page={$page}")."\"".($this->page==$page?" onclick=\"return false\"":false)."><span>".($name?$name:$page)."</span></a> ";
		return $html;
	}
	
	function set_js_page($page, $name=false) {
		$html = "<a".($page==$this->page?" class=\"focus\"":"")." href=\"javascript:\" ".($this->page==$page?" onclick=\"return false\"":"onclick=\"{$this->func}(".($this->arg?"'{$this->arg}',":"")."'{$page}');\"")."><span>".($name?$name:$page)."</span></a> ";
		return $html;
	}
	
	function get_js_code($func, $arg=array()) {
		$this->func = trim($func);
		$this->arg = implode("','", (array)$arg);
		$step = intval($this->display_num/2);
		$min = $this->verify_page($this->page-$step);
		$max = $this->verify_page($this->page+$step);
		$next_step = $this->verify_page($this->page+$this->display_num);
		$pre_step = $this->verify_page($this->page-$this->display_num);
		$html = "<div class=\"page-list\">";
		if($min > 1) {
			$html .= $this->set_js_page(1);
			$html .= $this->set_js_page($pre_step, '&lt;&lt;');
		}
		for($n=$min; $n<=$max; $n++) {
			$html .= $this->set_js_page($n);
		}
		if($max < $this->max_page) {
			$html .= $this->set_js_page($next_step, '&gt;&gt;');
			$html .= $this->set_js_page($this->max_page);
		}
		//$html .= $this->info();
		$html .= "</div>";
		return $html;
	}
	
	function set_html_page($page, $name='') {
		$html = "<a".($page == $this->page ? " class=\"focus\"":"")." href=\"".$this->url.(strpos($this->url, "?") ? "&page=" : "?page=").$page."\"".($this->page == $page ? " onclick=\"return false\"" : '')."><span>".($name ? $name : $page)."</span></a>";
		return $html;
	}
	
	function get_html_code() {
		$step = intval($this->display_num/2);
		$min = $this->verify_page($this->page-$step);
		$max = $this->verify_page($this->page+$step);
		$next_step = $this->verify_page($this->page+$this->display_num);
		$pre_step = $this->verify_page($this->page-$this->display_num);
		$html = "<div class=\"page-list\">";
		if($min > 1) {
			$html .= $this->set_html_page(1);
			$html .= $this->set_html_page($pre_step, '&lt;&lt;');
		}
		for($n=$min; $n<=$max; $n++) {
			$html .= $this->set_html_page($n);
		}
		if($max < $this->max_page) {
			$html .= $this->set_html_page($next_step, '&gt;&gt;');
			$html .= $this->set_html_page($this->max_page);
		}
		//$html .= $this->info();
		$html .= "</div>";
		return $html;
	}
	
	function info() {
		return "<span href=\"javascript:void(0)\">共{$this->max_page}页{$this->total}条数据，每页{$this->per_num}条</span>";
	}
	
	function get_a() {
		$step = intval($this->display_num/2);
		$min = $this->verify_page($this->page-$step);
		$max = $this->verify_page($this->page+$step);
		$next_step = $this->verify_page($this->page+$this->display_num);
		$pre_step = $this->verify_page($this->page-$this->display_num);
		$html .= $this->set_html_page(1, '首页');
		$html .= $this->set_html_page($pre_step, '上一页');
		for($n=$min; $n<=$max; $n++) {
			$html .= $this->set_html_page($n);
		}
		$html .= $this->set_html_page($next_step, '下一页');
		$html .= $this->set_html_page($this->max_page, '尾页');
		return $html;
	}
}
?>