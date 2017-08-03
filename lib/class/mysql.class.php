<?php
//	Copyright (C) Http://www.phpstcms.com/
//	Author: me@yangddahong.cn
//	All rights reserved

defined('STCMS_ROOT') or die('Access Deined!');
class mysql_class {
	var $enable_query_counter = true;
	var $query_num = 0;
	var $query_array = array();
	var $db_selected = false;
	var $conn_id = false;
	var $memcache = false;
	var $memcache_host = 'localhost';
	var $memcache_port = 11211;
	var $memcache_time = 30;
	var $memcache_res = false;
	
	function error($errer, $errno, $sql) {
		exit("error_no:".$errno."<br>error_msg:{$errer}<br>SQL:{$sql}");
	}
	
	function init($db_host='',$db_port='',$db_user='',$db_pwd='',$db_name='',$db_prefix='',$db_charset='') {
		$this->db_host = $db_host ? $db_host : $this->db_host;
		$this->db_port = $db_port ? $db_port : $this->db_port;
		$this->db_user = $db_user ? $db_user : $this->db_user;
		$this->db_pwd  =  $db_pwd ? $db_pwd  : $this->db_pwd;
		$this->db_name = $db_name ? $db_name : $this->db_name;
		$this->db_prefix = $db_prefix ? $db_prefix : $this->db_prefix;
		$this->db_charset = $db_charset ? $db_charset : $this->db_charset;
	}
	
	function connect() {
		$func = 'mysqli_connect';
		if($conn_id = $func($this->db_host.":".$this->db_port, $this->db_user, $this->db_pwd)) {
			if(version_compare($this->version($conn_id),"4.1",">")) {
				mysqli_query($conn_id, "SET NAMES '".$this->db_charset."'");
			}
			if(version_compare($this->version($conn_id),"5.0",">")) {
				mysqli_query($conn_id, "SET sql_mode=''");
			}
			return $conn_id;
		} else {
			$this->error(mysqli_connect_errno(), mysqli_connect_error(), "connect to {$this->db_host}");
		}
	}
	
	function select_db() {
		if(!mysqli_select_db($this->conn_id, $this->db_name)) {
			$this->error(mysqli_errno($this->conn_id), mysqli_error($this->conn_id), "select db `{$this->db_name}`");			
		} else {
			$this->db_selected = true;
		}
	}
	
	function query($sql, $func=false, $is_silent=false) {
		if($this->conn_id == false) {
			$this->conn_id = $this->connect();
		}
		if($this->db_selected == false) {
			$this->select_db();
		}
		$func = $func ? 'mysqli_query' : 'mysqli_query';
		if($result = $func($this->conn_id, $sql)) {
			$this->query_num++;
			if($this->enable_query_counter)
				$this->query_array[] = $sql;
			return $result;
		} else {
			if(!$is_silent)
				$this->error(mysqli_errno($this->conn_id), mysqli_error($this->conn_id), $sql);
			else
				return false;
		}
	}
	
	function fetch($result, $type=MYSQL_ASSOC) {
		if($array = @mysqli_fetch_array($result, $type))
			return $array;
		else
			return false;
	}
	
	function fetch_all($sql, $type=MYSQL_ASSOC) {
		if(preg_match('/^SELECT/i', $sql)) {
			$key = md5($sql);
		}
		if($this->memcache && $key && class_exists("Memcache")) {
			if(empty($this->memcache_res)) {
				$this->memcache_res = new Memcache();
				$this->memcache_res->connect($this->memcache_host, $this->memcache_port);
			}
			$data = $this->memcache_res->get($key);
			if($data) return $data;
		}
		$result = $this->query($sql);
		if(!$result) {
			$array = array();
		} else {
			while($temp = mysqli_fetch_array($result, $type)) {
				$array[] = $temp;
			}
			$this->free($result);
		}
		if($this->memcahe && $key) {
			if(empty($this->memcache_res)) {
				$this->memcache_res = new Memcache();
				$this->memcache_res->connect($this->memcache_host, $this->memcache_port);
			}
			$this->memcache_res->add($key, $data, $this->memcache_time);
		}
		return $array;
	}
	
	function num($result) {
		if($num = mysqli_num_rows($result)) {
			return $num;
		} else {
			return 0;
		}
	}
	
	function num_all($sql) {
		$result = $this->query($sql);
		$nums = $this->num($result);
		$this->free($result);
		return $nums;
	}
	
	function insert($table, $data=array(), $is_replace=false) {
		$table = $this->db_prefix.$table;
		$keys = array_keys($data);
		$vals = array_values($data);
		if(!$is_replace) {
			$sql = "INSERT INTO `$table` (`";
		} else {
			$sql = "REPLACE INTO `$table` (`";
		}
		$sql .= implode("`,`",$keys)."`) VALUES ('".implode("','",$vals)."')";
		$this->query($sql, 1);
	}
	
	function select($table,$fields = "*",$wheres = false,$orders = false, $limit=false) {
		$table = $this->db_prefix.$table;
		$sql ="SELECT ";
		if(is_array($fields) && $fields) {
			$sql .= "`".implode("`,`",$fields)."`";
		}
		elseif(is_string($fields)) {
			$sql .= $fields;
		}
		$sql .= " FROM `$table`";
		if($wheres) {
			$sql .= " WHERE ";
			if(is_array($wheres)) {
				foreach($wheres as $key => $val) {
					$whr[] = "`$key`='".$val."'";
				}
				$sql .= implode(" AND ",$whr);
			} elseif(is_string($wheres)) {
				$sql .= $wheres;
			}
		}
		if($orders) {
			$sql .= " ORDER BY ";
			if(is_array($orders)) {	
				$sql .= implode(",",$orders);
			} elseif(is_string($orders) && $orders) {
				$sql .= $orders;
			}
		}
		if($limit) {
			$sql .= " LIMIT ";
			if(is_array($limit)) $sql .= $limit[0].",".$limit[1];
			if(is_string($limit)) $sql .= $limit;
		}
		return $this->fetch_all($sql);
	}
	
	function update($table, $data, $wheres=false,$limit=false) {
		$table = $this->db_prefix.$table;
		$sql ="UPDATE `$table` SET ";
		if(is_array($data)) {
			foreach($data as $key => $val) {
				$fields[] = "`$key`='".$val."'";
			}
			$sql .= implode(",",$fields);
		} else {
			$sql .= $data;
		}
		if($wheres) {
			$sql .= " WHERE ";
			if(is_array($wheres)) {
				foreach($wheres as $key => $val) {
					$whr[] = "`$key`='".$val."'";
				}
				$sql .= implode(" AND ",$whr);
			} elseif(is_string($wheres)) {
				$sql .= $wheres;
			}
		}
		if($limit) {
			$sql .= " LIMIT ";
			if(is_array($limit)) $sql .= $limit[0].",".$limit[1];
			if(is_string($limit)) $sql .= $limit;
		}
		return $this->query($sql, 1);
	}
	
	function delete($table,$wheres=false,$limit=false) {
		$table = $this->db_prefix.$table;
		$sql = "DELETE FROM `$table`";
		if($wheres) {
			$sql .= " WHERE ";
			if(is_array($wheres)) {
				foreach($wheres as $key => $val) {
					$whr[] = "`$key`='".$val."'";
				}
				$sql .= implode(" AND ",$whr);
			} elseif(is_string($wheres)) {
				$sql .= $wheres;
			}
		}
		if($limit) {
			$sql .= " LIMIT ";
			if(is_array($limit)) $sql .= $limit[0].",".$limit[1];
			if(is_string($limit)) $sql .= $limit;
		}
		return $this->query($sql, 1);
	}
	
	function num_table($table='',$wheres=false) {
		$table = $this->db_prefix.$table;
		$sql = "SELECT COUNT(*) AS num FROM `$table`";
		if($wheres) {
			$sql .= " WHERE ";
			if(is_array($wheres)) {
				foreach($wheres as $key => $val) {
					$whr[] = "`$key`='".$val."'";
				}
				$sql .= implode(" AND ",$whr);
			} elseif(is_string($wheres)) {
				$sql .= $wheres;
			}
		}
		$result = $this->fetch($this->query($sql));
		return $result['num'];
	}
	
	function select_one($table, $fields = "*", $wheres = false, $orders=false, $limit=false) {
		$table = $this->db_prefix.$table;
		$sql ="SELECT ";
		if(is_array($fields) && $fields) {
			$sql .= "".implode(",",$fields)."";
		} elseif(is_string($fields)) {
			$sql .= $fields;
		}
		$sql .= " FROM `$table`";
		if($wheres) {
			$sql .= " WHERE ";
			if(is_array($wheres)) {
				foreach($wheres as $key => $val) {
					$whr[] = "`$key`='".$val."'";
				}
				$sql .= implode(" AND ",$whr);
			} elseif(is_string($wheres)) {
				$sql .= $wheres;
			}
		}
		if($orders) {
			$sql .= " ORDER BY ";
			if(is_array($orders)) {	
				$sql .= implode(",", $orders);
			} elseif(is_string($orders) && $orders) {
				$sql .= $orders;
			}
		}
		if($limit) {
			$sql .= " LIMIT ";
			if(is_array($limit)) $sql .= $limit[0].",".$limit[1];
			if(is_string($limit)) $sql .= $limit;
		}
		return $this->fetch($this->query($sql));
	}
	
	function get_field_value($table, $field, $where=false, $order=false) {
		$tmp = $this->select_one($table, $field, $where, $order);
		return $tmp[$field];
	}
		
	function free($result) {
		mysqli_free_result($result);
	}
	
	function close() {
		mysqli_close($this->conn_id);
	}
	
	function insert_id() {
		return mysqli_insert_id($this->conn_id);
	}
	
	function version($conn_id=false) {
		return mysqli_get_server_info($conn_id);
	}
	
	function __destruct() {
		$this->close();
	}
}
?>
