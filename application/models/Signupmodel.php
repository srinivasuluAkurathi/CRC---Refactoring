<?php 
class Signupmodel extends CI_Model{
	
 	public function  __construct(){
		parent::__construct();
		$this->load->dbforge();
	}

	public function updateStatusClickFunnel($table,$status, $email)
	{
		$this->db = $this->load->database('default', TRUE);
		$data= Array('signup_status' => $status);
		$where= Array('email' => $email);
		$this->db->where($where);
		if($this->db->update($table, $data)){
			return 1;
		}else{
			return 0;
		}
	}

	public function selectDataClickFunnel($table_name,$email){
		$this->db = $this->load->database('default', TRUE);
		$this->db->select('signup_status');
		$this->db->from($table_name);
		$this->db->where('email',$email);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->result_arr_ss();
	}

	public function updateLastCompleteStep($table,$status,$regId)
	{
		$this->db = $this->load->database('default', TRUE);
		$data= Array('last_completed_step' => $status);
		$where= Array('ireg_id' => $regId);
		$this->db->where($where);
		if($this->db->update($table, $data)){
			return 1;
		}else{
			return 0;
		}
	}

	public function insertData($table,$data)
	{
		$this->db = $this->load->database('default', TRUE);
		if($this->db->insert($table,$data)){
			return $this->db->insert_id();
		}else{
			return 0;
		}
	}
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

	public function  getCountyNamebyId($cid){
		$this->db = $this->load->database('default', TRUE);
		$query = $this->db->get_where('cro_countries');
		$this->db->select('icountry_id,vcountry_name');
		$this->db->where('icountry_id',$cid);
		$query = $this->db->get('cro_countries');
		$country_nameArr=$query->result();
		return $country_nameArr[0]->vcountry_name;
	}

	public function updateLeadStatus($lead_id,$status_id)
        {
           $URL='https://app.close.io/api/v1/lead/'.$lead_id.'/';
           $lead_status = array("status_id"=>$status_id);
           $lead_status = json_encode($lead_status);
           $username = '815bbc2c61167dbcc083b8ea093ba891c757132dfc06fb23f4bbd235';
           $password = '';
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL,$URL);
           curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
           curl_setopt($ch, CURLOPT_POSTFIELDS, $lead_status);
           curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
           curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                   'Accept: application/json',
                   'Content-Type: application/json'));
           curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
           $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
           $result=curl_exec($ch);
           curl_close ($ch);
           return json_decode($result);
        }
    //Close.io integration start
	public function closeioAddLead($lead)
	{
		$URL='https://app.close.io/api/v1/lead/';
		$lead = json_encode($lead);
		$username = '815bbc2c61167dbcc083b8ea093ba891c757132dfc06fb23f4bbd235';
		$password = '';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $lead);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		$result=curl_exec ($ch);
		curl_close ($ch);
		return json_decode($result);
	}
	
}
?>