<?php
class Commonrepository extends CI_Model{

	public function  __construct(){
		parent::__construct();
		$this->load->dbforge();
		
	}

	/*
	| -------------------------------------------------------------------
	| Global function for select data
	| -------------------------------------------------------------------
	| Date: 25 Feb 2020
	| 
	|*/
	public function selectData($table_name,$fields='*',$where='1',$order_by='',$order_type='',$group_by='',$limit=''){
		$this->db = $this->load->database('default', TRUE);
		$this->db->select($fields);
		$this->db->from($table_name);
		$this->db->where($where);
		if($order_by != ''){
			$this->db->order_by($order_by,$order_type);
		}
		if($group_by != ''){
			$this->db->group_by($group_by);
		}
		if($limit != ''){
			$this->db->limit($limit);
		}
		$query = $this->db->get();
		return $query->result_arr_ss();
	}

	/*
	| -------------------------------------------------------------------
	| Global function for update
	| -------------------------------------------------------------------
	| Date: 25 Feb 2020
	| 
	|*/
	public function updateDataGen($db_name, $table,$data, $where)
	{
		$this->dbl = $this->load->database($db_name, TRUE);
		$this->dbl->where($where);
		if($this->dbl->update($table, $data)){
			//return $this->dbl->last_query();
			return 1;
		}else{
			return 0;
		}
	}
	/*
	| -------------------------------------------------------------------
	| Global function for select
	| -------------------------------------------------------------------
	| Date: 25 Feb 2020
	| 
	|*/
	public function selectDataGen($db_name, $table_name,$fields='*',$where='',$order_by='',$order_type='',$group_by='',$limit=''){
		$this->dbl = $this->load->database($db_name, TRUE);
		$this->dbl->select($fields, false);
		$this->dbl->from($table_name);
		#$this->dbl->_protect_identifiers = FALSE;
		if((is_array($where) && count($where) > 0) || $where != ''){
			$this->dbl->where($where);
		}
		#$this->dbl->_protect_identifiers = TRUE;
		if($order_by != ''){
			$this->dbl->order_by($order_by,$order_type);
		}
		if($group_by != ''){
			$this->dbl->group_by($group_by);
		}
		if($limit != ''){
			$this->dbl->limit($limit);
		}
		#echo "<pre>"; print_r($this->dbl); exit();
		$query = $this->dbl->get();
		#echo "<pre>"; print_r($this->dbl->queries); exit();
		/*if($_SERVER['REMOTE_ADDR'] == '117.220.213.214'){
			$a = "Time".time();
			$query->result_obj_ss();
			$a1 = "Time".time();
			$a2 = "Time".time();
			$query->result();
			$a3 = "Time".time();
			echo $a."<-------->".$a1."<br/>";
			echo $a2."<-------->".$a3."<br/>";
		}	*/
		return $query->result_obj_ss();
	}
	

}