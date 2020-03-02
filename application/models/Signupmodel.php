<?php 
class Signupmodel extends CI_Model{
	
 	public function  __construct(){
		parent::__construct();
		$this->load->dbforge();
	}

	public function updateStatusClickFunnel($status,$email)
	{
		$this->db = $this->load->database('default', TRUE);
		$data= Array('signup_status' => $status);
		$where= Array('email' => $email);
		$this->db->where($where);
		if($this->db->update('half_regiestered_clickfunnel', $data)){
			return 1;
		}else{
			return 0;
		}
	}

	public function selectDataClickFunnel($email){
		$this->db = $this->load->database('default', TRUE);
		$this->db->select('signup_status');
		$this->db->from('half_regiestered_clickfunnel');
		$this->db->where('email',$email);
		$query = $this->db->get();
		return $query->result_array();
		//return $query->result_arr_ss();
	}

	public function updateLastCompleteStep($status,$regId)
	{
		$this->db = $this->load->database('default', TRUE);
		$data= Array('last_completed_step' => $status);
		$where= Array('ireg_id' => $regId);
		$this->db->where($where);
		if($this->db->update(CRO_COMPANY_RGSTRTN, $data)){
			return 1;
		}else{
			return 0;
		}
	}

	public function getTimezone($timezone){
		$this->dbl = $this->load->database(DB_DEFAULT, TRUE);
		$this->dbl->select('vtimezone_abbr', false);
		$this->dbl->from('cro_timezone');
		$this->dbl->where('vtimezone_name',$timezone);
		$query = $this->dbl->get();
		return $query->result_array();
	}
	public function setCompanyRegistrationData($CroCompanyRegstrationData){
		$result=$this->insertData(CRO_COMPANY_RGSTRTN,$CroCompanyRegstrationData);
		return $result;
	}
	public function getCroCountries($country){
		$result=$this->selectDataGen(DB_DEFAULT, CRO_COUNTRIES ,'country_code, currency_code, currency_symbol', "icountry_id = ".$country);
		return $result;
	}
	public function setCroUserAccess($CroUserAccessData){
		$result=$this->insertData(CRO_USER_ACCESS,$CroUserAccessData);
		return $result;
	}
	public function getAgendaSettings($uid){
		$reslut = $this->selectData(CRO_AGENDA_SETTINGS,'*',"iuser_id = ".$uid);
		return $reslut;
	}
	public function getCroLeadHistory($adminEmail){
		$reslut = $this->selectDataGen(DB_DEFAULT, 'cro_lead_history','lead_id', "email = '".trim($adminEmail)."'");
		return $reslut;
	}
	public function setLeadHistory($ins_data){
		$reslut = $this->insertGenData(DB_DEFAULT, CRO_LEAD_HISTORY, $ins_data);
		return $reslut;
	}
	public function setAgendaSettings($dataCASTmp){
		$result=$this->insertData(CRO_AGENDA_SETTINGS,$dataCASTmp);
		return $result;
	}
	public function setCroControlData($insertControlData){
		$result=$this->insertData(CRO_CONTROLS,$insertControlData);
		return $result;
	}
	public function setSampleClient($dataSampleClient){
		$result=$this->insertData(CRO_USER_ACCESS,$dataSampleClient);
		return $result;
	}
	public function setSampleAffiliate($dataSampleAffiliate){
		$result = $this->insertData(CRO_USER_ACCESS,$dataSampleAffiliate);
		return $result;
	}
	public function updateCompanyData($updateRecurlyAccountCodeandSubsciptionId,$registrationId){
		$result=$this->updateData(CRO_COMPANY_RGSTRTN,$updateRecurlyAccountCodeandSubsciptionId, "ireg_id = ".$dataSession['registrationId']);
		return $result;
	}
	public function updateGrowsumoInCompany($data_grsm_management,$registrationId){
		$result=$this->updateData(CRO_COMPANY_RGSTRTN,$data_grsm_management, "ireg_id = ".$registrationId);
		return $result;
	}
	public function updateClickFunnelStatus($update_CF_signup_status,$txtEmail){
		$result=$this->updateData('half_regiestered_clickfunnel',$update_CF_signup_status, "email = '".$txtEmail."'");
		return $result;
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
		return $query->result_array();
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
	public function updateData($table,$data, $where)
	{
		$this->db = $this->load->database('default', TRUE);
		$this->db->where($where);
		if($this->db->update($table, $data)){
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
		return $query->result_array();
		//return $query->result_obj_ss();
	}

	public function  getCountyNamebyId($countryId){
		$this->db = $this->load->database('default', TRUE);
		$query = $this->db->get_where('cro_countries');
		$this->db->select('icountry_id,vcountry_name');
		$this->db->where('icountry_id',$countryId);
		$query = $this->db->get('cro_countries');
		$country_nameArr=$query->result();
		return $country_nameArr[0]->vcountry_name;
	}

	public function getSalesPerson($salesPersonId)
	{
		$this->db = $this->load->database('default', TRUE);
		$this->db->select('*');
		$this->db->from(CRO_SALES_PERSON);
		$this->db->where('isales_person_id',$salesPersonId);
		$query = $this->db->get();
		$data = $query->result();
		return $data;
	}
	public function getUserNameAndPassword($userId){
		$this->db = $this->load->database('default', TRUE);
		$this->db->select('vuser_name,vpasswd');
		$this->db->from(CRO_USER_ACCESS);
		$this->db->where('iuser_id',$userId);
		$query = $this->db->get();
		return $query->row();	
	}
	public function emailTemplateForNewSignup()
	{
		$this->db = $this->load->database('default', TRUE);
		$this->db->select('*');
		$this->db->from(CRO_EMAIL_TEMPLATES);
		$this->db->where('id','1'); #2 = welcome signup template
		$query = $this->db->get();
		return  $query->row();			
	}

	public function emailTemplateForSalesPerson($sp_name, $adminArr)
	{
		$frequency = 'Monthly';
		
		$emailTpl = "";
		$emailTpl .= "Congratulations ".ucwords($sp_name)."! <br/><br/>";
		$emailTpl .= "Your customer ".ucwords($adminArr['name'])." signed up for Credit Repair Cloud (Free Trial)!<br/>
						Login details have already been sent to ".$adminArr['email']."<br/><br/>
						If ".ucwords($adminArr['name'])." cannot find the welcome letter, instruct them to look in spam/bulk/junk or confirm that this email is correct: ".$adminArr['email']."<br/><br/>
						Customers can also visit <a href='https://www.creditrepaircloud.com'>www.creditrepaircloud.com</a> to login with the username and password they created...or click \"Forgot Password\" and the login details will be resent instantly to ".$adminArr['email'].".<br/><br/><br/>
						Package details:<br/>
						Credit Repair Cloud<br/>
						Start Plan<br/>
						$179 <br/>
						".$frequency."<br/>
						Amount paid: $0 (Free Trial)<br/>
						We will notify you when he converts to Paid/Full<br/><br/><br/>
						Customer name: <br/>
						".ucwords($adminArr['name'])."<br/>
						".$adminArr['zip']."<br/>	
						".$adminArr['country']."	<br/>
						".$adminArr['phone']."	<br/>
						".$adminArr['email']."	<br/><br/>
					";
		
		$emailTpl .=  "xo<br/>
						Your friend,<br/> 
						Credit-Aid Server<br/><br/>
						(survey)
						";
		
		
		return $emailTpl;
	}

	public function templateForTermsConditions($adminArr)
	{
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
                    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                }

                $frequency = 'Monthly';
                $price = '$179';
		$emailTpl = "";
		$emailTpl .= '<div style="font-family:Calibri, Arial; color:#000000; background:#f6f6f6;">
		              <table width="800" border="0" align="center" cellpadding="5" cellspacing="0" style="border:#ccc solid 1px; background:#FFF;">
		                <tr>
		                  <td height="5" bgcolor="#00CCFF"></td>
		                <tr>
		                  <td align="center" valign="top"><img src="http://www.credit-aid.com/images/cloud-logo-black.png"  /></td>
		                </tr>
		                <tr>
		                  <td align="left" valign="top" style="font-family:Calibri, Arial; color:#000000;"><p><strong>Agreement of Terms, Refund Policy and Credit Card Authorization for Credit Repair Cloud Service from '.ucwords($adminArr['name']).', </strong></p>
		                    <p>IP Address:   '.$ip_address.'<br />
		                      Time: '.date("M d, Y h:i A").'<br />
		                      *Digital Signature   below</p>
		                    <p><strong>About   this Agreement:</strong></p>
		                    <ol type="1">
		                      <li>AGREEMENT. This   agreement is for Credit Repair Cloud Service <a href="http://www.creditrepaircloud.com">www.creditrepaircloud.com</a>, a   business service with a monthly fee. Pricing is here: <a href="https://www.creditrepaircloud.com/pricing">https://www.creditrepaircloud.com/pricing</a>.   Signup, agreement to terms and credit card authorization are here: <a href="https://www.creditrepaircloud.com/account/register">https://www.creditrepaircloud.com/account/register</a>.   Pricing details at the bottom are dynamic and will show the exact plan (from the   pricing page) chosen by the customer. Customers can upgrade, downgrade or cancel   at any time self-service all from within their account. All actions are logged   with user ID and IP. </li>
		                      <li>TERMS. Customers   signing up are required to read, understand and click &ldquo;I AGREE&rdquo; to accept terms   of service, refund policy and credit card charge authorization displayed during   signup or the account is not created. Once the customer has clicked &ldquo;I AGREE&rdquo; to   all terms and given &ldquo;Credit Card Charge Authorization,&rdquo; the account is created,   the customer receives login information and we are sent this form with the   customers authorization and digital signature. </li>
		                      <li>FREE TRIAL. All   users start with a Free Trial (with no charge to their card) and the ability to   use our service for 30 days with no charge, They are free to add their company   info, their clients and to begin running their business from our   application.  No customers are charged   during &ldquo;Free Trial.&rdquo; </li>
		                      <li>CHARGES. Charges   only begin after the following has occurred:<br/><br/>
		                      		<ol type="a">
				                        <li>The customer has used   the service for more than 30 days without cancelling.</li>
				                        <li>Or the customer has   intentionally chosen to &ldquo;convert&rdquo; from a &ldquo;Free Trial&rdquo; to a &ldquo;Paid Full   Account.&rdquo;  At this time, the customer   agrees to charge their own card to pay in advance for the monthly or yearly plan   they have chosen. </li>
				                    </ol>
		                      </li>
		                      <li>ABSOLUTE   CONFIRMATION. When the customer clicks to charge their card, we are sent an   second email  absolute confirmation that   the customer has successfully upgraded their account and clicked &ldquo;I AGREE&rdquo; to   the terms of service and refund policy and given authorization to immediately   charge their credit card. </li>
		                      <li>CANCELLATION.   Customers read, understand and agree that they cannot cancel by telephone or   email and that they must cancel themselves with one click within their account.   Cancellation is instant.  At   cancellation, we receive a confirmation email with timestamp of cancellation and   so does the account holder.  No new   billing can ever occur after cancellation. </li>
		                    </ol>
		                    <p>&nbsp;</p></td>
		                </tr>
		                <tr>
		                  <td bgcolor="#FFFF66" ><table border="0" cellspacing="0" cellpadding="0" width="100%">
		                      <tbody>
		                        <tr>
		                          <td valign="top" width="619" colspan="2"><p><strong>SIGNUP, <U>AGREEMENT TO TERMS</U>, REFUND POLICY AND CREDIT CARD   AUTHORIZATION:</strong></p>
		                            <p><strong>I, '.ucwords($adminArr['name']).' have read, fully understand and fully agree to the Credit   Repair Cloud Terms of Service and Refund Policy.  I have also given authorization to charge my   credit card for the service plan I have chosen. </strong><strong>I   agree to the following:</strong></p>
		                            <p><strong>Please cancel your account by '.date('m/d/Y', strtotime('+29 days')).'.</strong> If you don\'t want to continue using   Credit Repair Cloud, just cancel before the trial ends and you won\'t be charged   (we\'ll email you 5 days before the trial ends to remind you). Otherwise, you\'ll   pay just <strong>$'.$price.'/'.$frequency.'</strong> for the &ldquo;<strong>Start</strong>&rdquo; service as long as your account is open. You can upgrade, downgrade or cancel any time.</p>
		                            <p>By clicking   &quot;Start my free trial&quot; I agree to the <a href="https://www.creditrepaircloud.com/termsofservice">Terms of Service,</a> <a href="https://www.creditrepaircloud.com/privacy">Privacy</a>, and <a href="https://www.creditrepaircloud.com/termsofservice#refund">Refund   policy</a></p>
		                            <p><strong>My Digital   Signature</strong></p></td>
		                        </tr>
		                        <tr>
		                          <td valign="top" width="308">Name:</td>
		                          <td valign="top" width="311"><strong>'.ucwords($adminArr['name']).'</strong></td>
		                        </tr>
		                        <tr>
		                          <td valign="top" width="308">Email:</td>
		                          <td valign="top" width="311"><a href="mailto:'.ucwords($adminArr['email']).'">'.ucwords($adminArr['email']).'</a></td>
		                        </tr>
		                        <tr>
		                          <td valign="top" width="308">Product:</td>
		                          <td valign="top" width="311">Credit Repair Cloud Subscription</td>
		                        </tr>
		                        <tr>
		                          <td valign="top" width="308">Phone:</td>
		                          <td valign="top" width="311">'.$adminArr['phone'].'</td>
		                        </tr>
		                        <tr>
		                          <td valign="top" width="308">Clicked &ldquo;I AGREE&rdquo; to terms   and refund policy:</td>
		                          <td valign="top" width="311">Yes</td>
		                        </tr>
		                        <tr>
		                          <td valign="top" width="308">Gave Credit Card Authorization</td>
		                          <td valign="top" width="311">Yes</td>
		                        </tr>
		                        <tr>
		                          <td valign="top" width="308">Timestamp of   Agreement:</td>
		                          <td valign="top" width="311">'.date("M d, Y h:i A").'</td>
		                        </tr>
		                        <tr>
		                          <td valign="top" width="308">IP Address:</td>
		                          <td valign="top" width="311">'.$ip_address.'</td>
		                        </tr>
		                        <tr>
		                          <td valign="top" width="619" colspan="2"><strong>*Digital   Signatures:</strong> In 2000, the U.S.   Electronic Signatures in Global and National Commerce (ESIGN) Act established   electronic records and signatures as legally binding, having the same legal   effects as traditional paper documents and handwritten signatures. Read more at   the FTC web site: <a href="http://www.ftc.gov/os/2001/06/esign7.htm">http://www.ftc.gov/os/2001/06/esign7.htm</a><br />
		                          </td>
		                        </tr>
		                      </tbody>
		                    </table></td>
		                </tr>
		                <tr>
		                  <td height="5" ></td>
		                </tr>
		                <tr>
		                  <td height="5" bgcolor="#00CCFF"></td>
		                </tr>
		              </table>
		            </div>';

		    return $emailTpl;        
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