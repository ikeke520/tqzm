<?php
//	Copyright (C) Http://www.phpstcms.com/
//	Author: me@yangdahong.cn
//	All rights reserved

class category_class {
	function __construct() {
		global $mysql_class;
		$this->db = $mysql_class;
		$this->cats = false;
	}
	
	function get_all_cats() {
		if(!$this->cats) {
			$cats = $this->db->select('category', '*');
			$cats[] = array('id'=>0, 'pid'=>'-1', 'name'=>'Root','adder'=>'system');
			$this->cats = $cats;
		}
		return $this->cats;
	}
	
	function filter_cats($cats, $condition=array()) {
		$cats = $cats ? $cats : $this->get_all_cats();
		return filter_array($cats, $condition);
	}
	
	function get_id_array($cat_array) {
		if($cat_array && is_array($cat_array)) {
			while($cat_tmp = current($cat_array)) {
				$id_array[] = $cat_tmp['id'];
				array_shift($cat_array);
			}
		}
		return $id_array;
	}
	
	function get_child_cats($id, $self_inc=false) {
		$children = $this->filter_cats(false, array('pid'=>$id), $model);
		if($self_inc) $children_id_str = $id.',';
		if($children) {
			$children_id_str .= implode(',', $this->get_id_array($children));
			while($child_tmp = current($children)) {
				$next_child_id_str = $this->get_child_cats($child_tmp['id'], $model);
				$children_id_str .= $next_child_id_str?(','.$next_child_id_str):'';
				array_shift($children);
			}
		}
		return implode(',', array_unique(explode(',', $children_id_str)));
	}
	
	function get_top_cat($id) {
		$thiscat = $this->filter_cats(false, array("id"=>$id));
		if($thiscat[0]['pid'] > 0) {
			$top_cat = $this->filter_cats(false, array("id"=>$thiscat[0]['pid']));
			return $this->get_top_cat($top_cat[0]['id']);
		} else {
			return $thiscat[0];
		}
	}
	
	function get_code($data=false, $style='list', $tpl=false) {
		$this->get_all_cats();
		return $this->print_list($data, true, $style, $tpl);
	}
	
	function get_level($data, $level=0) {
		$pcat = $this->filter_cats(false, array('id'=>$data['pid']));
		if($pcat[0]['id']>0){
			$level++;
			$level = $this->get_level($pcat[0], $level);
		} elseif($pcat[0]['id']==0 && $pcat) {
			$level++;
		}
		return $level;	
	}
	
	function print_list($data, $is_end=true, $style='list', $list_tpl=false) {
		if(!$data) {
			$data = $this->filter_cats(false, array('pid'=>-1), $this->model);
			$data = $data[0];
		}
		if(empty($list_tpl)) {
			switch($style) {
				case 'list':
					$list_tpl = "<tr>
	<td style=\"width:30px;\"><input name=\"id\" type=\"checkbox\" value=\"{id}\"></td>
	<td style=\"width:30px;\">{id}</td>
	<td style=\"text-align:left\">{level_icon}<a href=\"./?ac=category&do=edit&id={id}\">{name}</a></td>
	<td><a href=\"./?ac=category&do=edit&id={id}\">编辑</a> | <a href=\"javascript:\" onclick=\"SU.dialog({title:'操作确认', 'msg':'你确定要删除该分类吗？', cb:function(){location='./?ac=category&do=del&id={id}';}});\">删除</a></td>
</tr>
";
				break;
				case 'checkbox':
					$list_tpl = "<label><input type=\"checkbox\" value=\"{id}\" name=\"catid[]\">{level_icon}{name}<br /></label>";
				break;
				case 'select':
					$list_tpl = "<option value=\"{id}\">{level_icon}{name}</option>";
				break;
			}
		}
		$level = $this->get_level($data);
		$children = $this->filter_cats(false, array('pid'=>$data['id']), $this->model);
		if($children) {
			$lever_icon = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level).($is_end ? '└':'├').'┬';
		} else {
			$lever_icon = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level).($is_end ? '└':'├').'─';
		}
		$code = str_replace(array('{level_icon}','{id}','{name}','{time}','{adder}', '{path}', '{url}'), array($lever_icon, $data['id'], $data['name'], $data['time'], $data['adder'], $data['path'], $data['url']), $list_tpl);
		if($children) {
			$end_key=0;
			while($child_tmp = current($children)) {
				if($end_key == count($children)-1) {
					$code .= $this->print_list($child_tmp, true, $style, $list_tpl);
				} else {
					$code .= $this->print_list($child_tmp, false, $style, $list_tpl);
				}
				next($children); $end_key++;
			}
		}
		return $code;
	}
}
?>