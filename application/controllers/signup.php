<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

    private $errorMessage='';

 	function __construct(){
		parent::__construct();
		$this->load->model('Signupmodel');
		$this->load->library('recurlyauthenticationwrapper'); 
		$this->load->library('growsumo');
	}

	public function thirtyDayFreeTrialSignupClickFunnelWebhook(){
			#$requestJSONfromClickFunnel  = json_decode(file_get_contents("php://input"));
			$payloadjson=file_get_contents("http://localhost/CRC-CodeRefactoring/CRC---Refactoring");
			$requestJSONfromClickFunnel=json_decode($payloadjson);
			if(AWS_ENV_STATUS == 'LIVE' )
            $requestArrayFromClickfunnel = $this->setWebhookRequestParams($requestJSONfromClickFunnel->purchase->contact,$requestJSONfromClickFunnel->purchase->subscription_id,$requestJSONfromClickFunnel->purchase->error_message,'cr_start');
            else
            $requestArrayFromClickfunnel = $this->setWebhookRequestParams($requestJSONfromClickFunnel->contact,$requestJSONfromClickFunnel->subscription_id,$requestJSONfromClickFunnel->error_message,'cr_start_master');
        	echo '<pre>';print_r($requestArrayFromClickfunnel);exit;
        	$validRecurlyAccountArray	 = $this->checkRecurlyAccountSubscription($requestArrayFromClickfunnel['subId'],$requestArrayFromClickfunnel['recurlyAccountCode'],$requestArrayFromClickfunnel['planCode']);
        	$recurlyAccountCode 		 = $validRecurlyAccountArray['recurlyAccountCode'];
        	$subscriptionId 		 	 = $validRecurlyAccountArray['subscriptionId'];
        	$clickFunnelSignupStatus 	 = $this->Signupmodel->selectDataClickFunnel('half_regiestered_clickfunnel',$requestArrayFromClickfunnel['txtEmail']);
        	if($clickFunnelSignupStatus[0]['signup_status'] == '0' && $subscriptionId!=''){
        	 $dataSession =$this->submitSignupdataStep1($requestArrayFromClickfunnel,$recurlyAccountCode,$subscriptionId);
        	 $this->submitSignupdataStep2($dataSession['registrationId']);
        	 $clinentAffiliateIds=$this->submitSignupdataStep3($dataSession['registrationId'],$dataSession['fname'],$dataSession['lname'],$dataSession['adminEmail'],$dataSession['companyCountry']);
        	  }
        	 $this->submitSignupdataStep4($requestArrayFromClickfunnel['country'],$clinentAffiliateIds,$dataSession);
        	 $this->Signupmodel->updateLastCompleteStep(CRO_COMPANY_RGSTRTN,'entry_data_signup_step4',$dataSession['registrationId']);

        	 $this->callingGrowsumoAPI($requestArrayFromClickfunnel['txtEmail'],$requestArrayFromClickfunnel['ipAddress'],$requestArrayFromClickfunnel['txtFirstName'],$requestArrayFromClickfunnel['txtLastName'],$requestArrayFromClickfunnel['txtgsId'],$recurlyAccountCode,$dataSession['registrationId']);
        	 
        	 $this->sendIntercomRequest($requestArrayFromClickfunnel['txtCompanyName'],$requestArrayFromClickfunnel['txtFirstName'],$requestArrayFromClickfunnel['txtLastName'],$requestArrayFromClickfunnel['txtPhone'],$dataSession['registrationId'],$dataSession['uid'],$dataSession['adminEmail']);

        	 $this->sendDatatoProofAPI($dataSession['fname'],$dataSession['lname'],$dataSession['adminEmail'],$requestArrayFromClickfunnel['txtCompanyName'],$requestArrayFromClickfunnel['txtPhone'],$requestArrayFromClickfunnel['txtZip'],$requestArrayFromClickfunnel['ipAddress'],$requestArrayFromClickfunnel['country']);

        	 $this->sendDatatoCloseioIntegration($dataSession['adminEmail'],$requestArrayFromClickfunnel['txtPhone'],$requestArrayFromClickfunnel['txtFirstName'],$requestArrayFromClickfunnel['txtCompanyName']);

        	
        	
    }

    



    private function submitSignupdataStep1($requestArrayFromClickfunnel,$recurlyAccountCode,$subscriptionId){
    	$this->Signupmodel->updateStatusClickFunnel('half_regiestered_clickfunnel','2',$requestArrayFromClickfunnel['email']);
        $updateClickFunnelSignupStatus 			= '';
        #submit_signup_data_step1() START
        $planType = "monthly";
        $timezone = $this->getTimeZoneFromIpAddress($ip_address);
        if($timezone == '' || $timezone == 'UTC') { $timezone = 'America/Los_Angeles'; }
        if($timezone != '') { date_default_timezone_set($timezone); }
        $vtimezoneAbbr = $this->Signupmodel->selectDataGen(DB_DEFAULT,'cro_timezone','vtimezone_abbr',"vtimezone_name = '".$timezone."' ");
        $insertCroCompanyRegstrationData = array(
					"dreg_date"						=> date("Y-m-d H:i:s"),
					"vcompany_name"					=> $requestArrayFromClickfunnel['txtCompanyName'],
					"vfirst_name"					=> $requestArrayFromClickfunnel['txtFirstName'],
					"vlast_name"					=> $requestArrayFromClickfunnel['txtLastName'],
					"vemail"						=> $requestArrayFromClickfunnel['txtEmail'],
					"vphone"						=> $requestArrayFromClickfunnel['txtPhone'],
					"vcompany_country" 				=> $requestArrayFromClickfunnel['country'],
					"icompany_timezone" 			=> $timezone,
					"vtimezone_abbr" 				=> $vtimezoneAbbr[0]->vtimezoneAbbr,
					"vpostcode" 					=> trim($requestArrayFromClickfunnel['txtZip']),
					"vaccount_status"				=> 'active',
					"ipackage_id" 					=> 7,
					"isales_person_id" 				=> $requestArrayFromClickfunnel['salesPersonId'],
					"vplan_type" 					=> $planType,
					"vRecurly_email" 				=> $requestArrayFromClickfunnel['txtEmail'],
					"vRecurlyAc_code" 				=> $recurlyAccountCode,
					"lead_notification_email" 		=> "1-",
					"affiliate_notification_email" 	=> "1-",
					"trial_period_end" 				=> date('Y-m-d', strtotime('+30 days')),
					'vIP_address' 					=> $requestArrayFromClickfunnel['ipAddress'],
					'lastlogin' 					=> date("Y-m-d H:i:s"),
					'growsumo_pk' 					=> $requestArrayFromClickfunnel['txtgsId']
                        );
        $lastInsertId = $this->Signupmodel->insertData(CRO_COMPANY_RGSTRTN,$insertCroCompanyRegstrationData);
        $countryDetailArr = $this->Signupmodel->selectDataGen(DB_DEFAULT, CRO_COUNTRIES ,'country_code, currency_code, currency_symbol', "icountry_id = ".$requestArrayFromClickfunnel['country']);
                $CC = $countryDetailArr[0]->country_code;
        $insertCroUserAccessData = array(
    				"ireg_id" 						=> $lastInsertId,
                    "vuser_name" 					=> addslashes($requestArrayFromClickfunnel['txtEmail']),
                    "vpasswd" 						=> base64_encode(addslashes($requestArrayFromClickfunnel['txtPass'])),
                    "vuser_type" 					=> '1',
                    "vFirst_Name" 					=> stripslashes($requestArrayFromClickfunnel['txtFirstName']),
                    "vLast_Name" 					=> stripslashes($requestArrayFromClickfunnel['txtLastName']),
                    "vemail" 						=> $requestArrayFromClickfunnel['txtEmail'],
                    "vCountryCode" 					=> $CC,
                    "db_name" 						=> $lastInsertId . "_crd"
                );
			$dataSession['uid'] = $this->Signupmodel->insertData(CRO_USER_ACCESS,$insertCroUserAccessData);
     		$dataSession['registrationId'] 		    = $lastInsertId;
            $dataSession['fname'] 					= stripslashes($requestArrayFromClickfunnel['txtFirstName']);
            $dataSession['lname'] 					= stripslashes($requestArrayFromClickfunnel['txtLastName']);
            $dataSession['companyName'] 			= stripslashes($requestArrayFromClickfunnel['txtCompanyName']);
            $dataSession['phone'] 					= $requestArrayFromClickfunnel['txtPhone'];
            $dataSession['adminEmail'] 				= $requestArrayFromClickfunnel['txtEmail'];
            $dataSession['companyCountry'] 			= $requestArrayFromClickfunnel['country'];
            $dataSession['recurlyPaymentStatus'] 	= 'trial';
            $dataSession['planType'] 				= $planType;
            $dataSession['userName'] 				= $requestArrayFromClickfunnel['txtEmail'];
            $dataSession['userType'] 				= 'admin';
            $dataSession['dbName'] 					= $dataSession['registrationId'] . '_crd';
            $dataSession['countryCode'] 			= $CC;
            $this->Signupmodel->updateLastCompleteStep(CRO_COMPANY_RGSTRTN,'submit_signup_data_step1',$dataSession['registrationId']);
            $getCAS = $this->Signupmodel->selectData(CRO_AGENDA_SETTINGS,'*',"iuser_id = ".$dataSession['uid']);
            if(count($getCAS) <= 0){
             $dataCASTmp = array("iid" => '',"iuser_id" => $dataSession['uid'],"esend_email" => 'off');
             $this->Signupmodel->insertData(CRO_AGENDA_SETTINGS,$dataCASTmp);
            }
            $this->createFolderAndMoveFilesInServer($dataSession['registrationId']);
            $updateRecurlyAccountCodeandSubsciptionId = Array('vRecurlyAc_code' => $recurlyAccountCode, 'vRecurlySubscriptionId' => $subscriptionId);
            $this->Signupmodel->updateData(CRO_COMPANY_RGSTRTN,$updateRecurlyAccountCodeandSubsciptionId, "ireg_id = ".$dataSession['registrationId']);
             #-=-=-=-=-=-=-=-=-=-=-=-=-Create Container, upload Bureau Logos & other IMG In Rackspace START-=-=-=-=-=-=-=-=-=-=-=-=-#
            $this->createContainerAndMoveFiles($dataSession['registrationId'],$dataSession['companyCountry'],$subscriptionId);

            return $dataSession;
            
    }
    private function submitSignupdataStep2($registrationId){
		#entry_data_signup_step2_first_3_cf() START
        $response = $this->cfiles->copy_obj('acra.png','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $response = $this->cfiles->copy_obj('acrs.png','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $response = $this->cfiles->copy_obj('ck.png','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $response = $this->cfiles->copy_obj('crsk.png','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $this->Signupmodel->updateLastCompleteStep(CRO_COMPANY_RGSTRTN,'entry_data_signup_step2_first_3_cf',$registrationId);
        #entry_data_signup_step2_first_3_cf() END
        #entry_data_signup_step2_first_6_cf() START
        $response = $this->cfiles->copy_obj('k8.gif','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $response = $this->cfiles->copy_obj('mcrs.png','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $response = $this->cfiles->copy_obj('pg.png','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $this->Signupmodel->updateLastCompleteStep(CRO_COMPANY_RGSTRTN,'entry_data_signup_step2_first_6_cf',$registrationId);
        #entry_data_signup_step2_first_6_cf() END
        #entry_data_signup_step2_another_6_cf() START
        $response = $this->cfiles->copy_obj('cct.jpg','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $response = $this->cfiles->copy_obj('wf.png','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $response = $this->cfiles->copy_obj('sample_photo_id_copy.png', 'default_cmpny_documents', $registrationId. '_cmpny_documents');
        $response = $this->cfiles->copy_obj('sample_power_of_attorney.pdf', 'default_cmpny_documents', $registrationId . '_cmpny_documents');
        $response = $this->cfiles->copy_obj('sample_utility_bill_copy.png', 'default_cmpny_documents', $registrationId . '_cmpny_documents');
        $this->Signupmodel->updateLastCompleteStep(CRO_COMPANY_RGSTRTN,'entry_data_signup_step2_another_6_cf',$registrationId);
        #entry_data_signup_step2_another_6_cf() END
        #-=-=-=-=-=-=-=-=-=-=-=-=-=Create Container, upload Bureau Logos & other IMG In Rackspace END-=-=-=-=-=-=-=-=-=-=-=-=-=#
     }

     private function submitSignupdataStep3($registrationId,$fname,$lname,$adminEmail,$companyCountry){
      		#entry_data_signup_step3() START
			$insertControlData 					= array();
			$insertControlData['ireg_id']   	= $registrationId;
			$insertControlData['sender_name'] 	= ucfirst(strtolower($fname)) . " " . ucfirst(strtolower($lname));
			$insertControlData['sender_email'] 	= $adminEmail;
			$this->Signupmodel->insertData(CRO_CONTROLS,$insertControlData);
			$clientPassword 		= str_shuffle('abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
			$clientPassword 		= base64_encode(substr($clientPassword, 0, 8));
			$dataSampleClient 		= array(
					"vuser_name"  	=> "sample_" . $registrationId,
					"vpasswd"  		=> $clientPassword, 
					"ireg_id" 		=> "0",
					"vuser_type" 	=> "client",
					"vemail" 		=> "sample_" . $registrationId . "@client.com",
					"db_name" 		=> $registrationId . "_crd"
									);
			$clientUserId 		= $this->Signupmodel->insertData(CRO_USER_ACCESS,$dataSampleClient);
			$affiiatePassword 		= str_shuffle('abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
			$affiiatePassword 		= base64_encode(substr($AffiiatePassword, 0, 8));
			$dataSampleAffiliate    = array(
					"vuser_name" 	=> "sample_affiliate_" . $registrationId,
					"vpasswd" 		=> $affiiatePassword,
					"ireg_id" 		=> "0",
					"vuser_type" 	=> "affiliate",
					"vemail" 		=> "sample_" . $registrationId . "@affiliate.com",
					"db_name" 		=> $registrationId . "_crd"
							);
			$affiliateUserId = $this->Signupmodel->insertData(CRO_USER_ACCESS,$dataSampleAffiliate);
			$this->Signupmodel->updateLastCompleteStep(CRO_COMPANY_RGSTRTN,'entry_data_signup_step3',$registrationId);
			#entry_data_signup_step3() END
			if ($_SERVER["HTTP_HOST"] == "localhost") {
                    $con = mysql_connect("localhost", "root", "");
                } else {
                    $con = mysql_connect(AWS_DB_HOST_NAME,AWS_DB_USERNAME,AWS_DB_PASSWORD);
                }
                if (!$con) { die('Could not connect: ' . mysql_error()); }
                if (mysql_query("CREATE DATABASE crcloud_" . $registrationId . "_crd", $con) && mysql_select_db("crcloud_" . $registrationId . "_crd", $con)) {


 				if($companyCountry==14) { $filename = "skeleton_aus.sql"; }
                    else if($companyCountry==151) { $filename = "skeleton_nz.sql"; }
                    else { $filename = "crcloud_skeleton_crd.sql"; }
                    $templine	 = '';
                    $lines 		 = file($filename);
                    foreach ($lines as $line) {
                        if (substr($line, 0, 2) == '--' || $line == '')
                            continue;
                        $templine .= $line;
                        if (substr(trim($line), -1, 1) == ';') {
                            mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
                            $templine = '';
                        }
             }
        }
        $clinentAffiliateIds=array();
        $clinentAffiliateIds['clientUserId'] 	= $clientUserId;
        $clinentAffiliateIds['affiliateUserId']	= $affiliateUserId;
        return $clinentAffiliateIds;
    }
    private function submitSignupdataStep4($requestArrayFromClickfunnelCountry,$clinentAffiliateIds,$dataSession){
    	$adminUid 			= $dataSession['uid'];
    	$fname 				= $dataSession['fname'];
    	$lname 				= $dataSession['lname'];
    	$adminEmail 	 	= $dataSession['adminEmail'];
    	$registrationId 	= $dataSession['registrationId'];
    	$clientUserId 		= $clinentAffiliateIds['clientUserId'];
    	$affiliateUserId 	= $clinentAffiliateIds['affiliateUserId'];
		$countryDetailArr 	= $this->Signupmodel->selectDataGen(DB_DEFAULT, CRO_COUNTRIES ,'country_code, currency_code, currency_symbol', "icountry_id = ".$requestArrayFromClickfunnelCountry);
                $CC = $countryDetailArr[0]->country_code;
    	if($_SERVER["HTTP_HOST"] == "localhost") {
                    $con 		= mysql_connect("localhost", "root", "");
                } else {
                    $con 		= mysql_connect(AWS_DB_HOST_NAME2,AWS_DB_USERNAME2,AWS_DB_PASSWORD2);
                }
                $db_selected 	= mysql_select_db('crcloud_'.$registrationId.'_crd', $con);
                $current_date 	= date('Y-m-d H:i:s');
                $current_Date_plus_2_hours = date("Y-m-d H:i:s", strtotime("+2 hour"));
            mysql_query('INSERT INTO `crd_team` (`iTeam_id`, `iUser_id`, `iRole_id`, `vFirst_Name`, `vLast_Name`, `vEmail`, `vPhone`, `vMobile`, `vFax`, `vPhoto`, `vAddress`, `gender`,`created_date`) VALUES (1,'.$admin_uid.', 1, "' .$fname . '", "' . $lname . '", "' . $adminEmail . '", "", "", "", "", NULL, "Male", now())');

            mysql_query("INSERT INTO `crd_scheduler` (`Id`, `Subject`, `Location`, `Description`, `StartTime`, `EndTime`, `IsAllDayEvent`, `Color`, `RecurringRule`, `iclient_id`, `iTeam_id`, `reminder_type`) VALUES 
                        (1, 'Remember to complete your \'To-Do\' Items on your Home page!', NULL, NULL, '{$current_date}', '{$current_Date_plus_2_hours}', 0, NULL, NULL, NULL, NULL, NULL)");
            mysql_query("INSERT INTO `crd_scheduler` (`Id`, `Subject`, `Location`, `Description`, `StartTime`, `EndTime`, `IsAllDayEvent`, `Color`, `RecurringRule`, `iclient_id`, `iTeam_id`,`created_by`, `reminder_type`,`estatus`) VALUES 
	                    (2, 'Complete my Company Profile', NULL, NULL, '{$current_date}', '{$current_Date_plus_2_hours}', 0, NULL, NULL,NULL, '1', '1', 'Follow Up', 'pending'),        
	                    (3, 'Set my default Client Agreement', NULL, NULL, '{$current_date}', '{$current_Date_plus_2_hours}', 0, NULL, NULL,NULL,  '1', '1', 'Follow Up', 'pending'),
	                    (4, 'Add my Team Members', NULL, NULL, '{$current_date}', '{$current_Date_plus_2_hours}', 0, NULL, NULL,NULL,  '1', '1', 'Follow Up', 'pending'),
	                    (5, 'Log into my Sample Client and Run Wizard 1-2-3', NULL, NULL, '{$current_date}', '{$current_Date_plus_2_hours}', 0, NULL, NULL,NULL,  '1', '1', 'Follow Up', 'pending')");
                
            mysql_query("INSERT INTO `crd_client_status_logs` (`iid`, `iold_status_id`, `inew_status_id`, `iclient_id`, `ddate`, `iteam_id`) VALUES (NULL, '0', '1', '1', CURRENT_DATE(), '0'), (NULL, '0', '2', '2', CURRENT_DATE(), '0')");
            mysql_query("UPDATE `crd_clients` SET `iuser_id` = '" . $clientUserId . "',`ePortalAccessClient` = 'on' WHERE `iclient_id`=1");
            mysql_query("UPDATE `crd_clients` SET `dreg_date` = CURRENT_DATE() WHERE `iclient_id` IN('1','2')");
            mysql_query("UPDATE `crd_affiliate` SET `iUser_id` = '" . $affiliateUserId . "',`ePortalAccess` = 'on' WHERE `iAffilate_id`= 1");
            mysql_query("UPDATE `crd_affiliate` SET `dreg_date` = CURRENT_DATE() WHERE `iAffilate_id` =1");
            if ($dataSession['companyCountry'] != 224) {
                mysql_query("UPDATE crcloud_" . $registrationId . "_crd.crd_agreements SET `tagreement` = REPLACE(`tagreement`, '$', '" . $countryDetailArr[0]->currency_symbol . "')");
                mysql_query("UPDATE crcloud_" . $registrationId . "_crd.crd_dispute_reasons SET `vdispute_reason` = REPLACE(`vdispute_reason`, '$', '" . $countryDetailArr[0]->currency_symbol . "')");
                mysql_query("UPDATE crcloud_" . $registrationId . "_crd.crd_letter_templ SET `vtempt_text` = REPLACE(`vtempt_text`, '$', '" . $countryDetailArr[0]->currency_symbol . "')");
                mysql_query("UPDATE crcloud_" . $registrationId . "_crd.crd_options SET `vOptionValue` = REPLACE(`vOptionValue`, '$', '" . $countryDetailArr[0]->currency_symbol . "')");
            }
            mysql_close($con);
    }

    private function createContainerAndMoveFiles($registrationId,$companyCountry,$subscriptionId){
    	$dataSession['RecurlySubscriptionId'] 	= $subscriptionId;
        $dataSession['acc_status'] 				= 'active';
        $containerArr = array();
        $this->cfiles->cf_container 			= $data_session['reg_id'].'_cmpny_attachment';
        $container_url = $this->cfiles->do_container('a');
        $containerArr['attachment'] 			= $container_url;
        $this->cfiles->cf_container 			= $data_session['reg_id'].'_cmpny_documents';
        $container_url = $this->cfiles->do_container('a');
        $containerArr['documents'] 				= $container_url;
        $this->cfiles->cf_container 			= $data_session['reg_id'].'_cmpny_misc';
        $container_url 							= $this->cfiles->do_container('a');
        $containerArr['misc'] 					= $container_url;
        
        if($companyCountry==14) {
        $response     = $this->cfiles->copy_obj('veda.png', 'default_cmpny_misc', $registrationId . '_cmpny_misc');
        $response     = $this->cfiles->copy_obj('dnb.png', 'default_cmpny_misc', $registrationId . '_cmpny_misc');
        } else {
        $response     = $this->cfiles->copy_obj('equifax.png', 'default_cmpny_misc', $registrationId . '_cmpny_misc');
        $response     = $this->cfiles->copy_obj('experian.png', 'default_cmpny_misc', $registrationId. '_cmpny_misc');
        $response 	  = $this->cfiles->copy_obj('trans_union.png', 'default_cmpny_misc', $registrationId. '_cmpny_misc');
        }
        $response 	  = $this->cfiles->copy_obj('cas.gif','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $response 	  = $this->cfiles->copy_obj('ccp.png','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $data_state_management = Array('last_completed_step' => 'entry_data_signup_step1_recurly');
        $this->Signupmodel->updateLastCompleteStep(CRO_COMPANY_RGSTRTN,'entry_data_signup_step1_recurly',$registrationId);

    }
    private function createFolderAndMoveFilesInServer($registrationId){

    	$errorFlag = 0;
        if(!mkdir($_SERVER['DOCUMENT_ROOT'].str_replace("index.php",'',$_SERVER['SCRIPT_NAME'])."uploads/".$registrationId.'_cmpny', 0777, true)){ $errorFlag = 1; }
        if(!mkdir($_SERVER['DOCUMENT_ROOT'].str_replace("index.php",'',$_SERVER['SCRIPT_NAME'])."uploads/".$registrationId.'_cmpny/logos', 0777, true) || $errorFlag != 0){ $errorFlag = 1; }
        if(!mkdir($_SERVER['DOCUMENT_ROOT'].str_replace("index.php",'',$_SERVER['SCRIPT_NAME'])."uploads/".$registrationId.'_cmpny/contacts', 0777, true) || $errorFlag != 0){ $errorFlag = 1; }
        if(!mkdir($_SERVER['DOCUMENT_ROOT'].str_replace("index.php",'',$_SERVER['SCRIPT_NAME'])."uploads/".$registrationId.'_cmpny/attachment', 0777, true) || $errorFlag != 0){ $errorFlag = 1; }
        $myFile 	= array();
        $myFile[] 	=  $_SERVER['DOCUMENT_ROOT'].str_replace("index.php",'',$_SERVER['SCRIPT_NAME'])."uploads/".$registrationId."_cmpny/index.php";
        $myFile[] 	=  $_SERVER['DOCUMENT_ROOT'].str_replace("index.php",'',$_SERVER['SCRIPT_NAME'])."uploads/".$registrationId."_cmpny/logos/index.php";
        $myFile[] 	=  $_SERVER['DOCUMENT_ROOT'].str_replace("index.php",'',$_SERVER['SCRIPT_NAME'])."uploads/".$registrationId."_cmpny/contacts/index.php";
        $myFile[] 	=  $_SERVER['DOCUMENT_ROOT'].str_replace("index.php",'',$_SERVER['SCRIPT_NAME'])."uploads/".$registrationId."_cmpny/attachment/index.php";
        $myFile[] 	=  $_SERVER['DOCUMENT_ROOT'].str_replace("index.php",'',$_SERVER['SCRIPT_NAME'])."uploads/".$registrationId."_cmpny/documents/index.php";
        $stringData = 'No direct access allowed';
        foreach($myFile as $filename){
            $fh = fopen($filename, 'w');
            fwrite($fh, $stringData);
            fclose($fh);
        }
        $this->Signupmodel->updateLastCompleteStep(CRO_COMPANY_RGSTRTN,'entry_data_signup_step1_useraccess',$registrationId);
    }
	private function checkRecurlyAccountSubscription($subscriptionId,$recurlyAccountCode,$planCode){
		$validRecurlyAccountArray						= array();
		$isRecurlyValidAccount							= 0;
		$validRecurlyAccountArray['subscriptionId']		= $subscriptionId;
		$validRecurlyAccountArray['recurlyAccountCode']	= $recurlyAccountCode;
		try {
		$subscriptions =$this->recurlyauthenticationwrapper->getRecurlySubscriptionsbyAccountCode($recurlyAccountCode);
		foreach ($subscriptions as $subscription) {
			if ($subscription->state == 'active' && $subscription->plan->plan_code == $planCode) {
			$isRecurlyValidAccount						= 1;
			$validRecurlyAccountArray['subscriptionId'] = $subscription->uuid;
			}
		  }
		} catch (Recurly_NotFoundError $e) {
 				$this->$errorMessage="CF - RECURLY ACCOUNT SUBSCRIPTION NOT FOUND THROUGH RECURLY ACCOUNT CODE";
		}

		if($isRecurlyValidAccount == 0 && !empty($subscriptionid)){
		try {    
		$subscription = $this->recurlyauthenticationwrapper->getRecurlySubscriptionbySubscriptionId($subscriptionId);
		$validRecurlyAccountArray['recurlyAccountCode']	= explode("accounts/",array_values((array) $subscription->account)[1])[1];
		}catch (Recurly_NotFoundError $e) {
				$this->$errorMessage="CF - RECURLY ACCOUNT SUBSCRIPTION NOT FOUND THROUGH SUBSCRIPTION ID";
			}
		}
        return $validRecurlyAccountArray;
	}
	private function setWebhookRequestParams($requestJSONfromClickFunnel,$subscriptionId,$errorMessage,$planCode){
		if(property_exists($requestJSONfromClickFunnel->additional_info, 'growsumo_pid')) 
		$GrowsumoPid 				 = trim($requestJSONfromClickFunnel->additional_info->growsumo_pid);else
		$GrowsumoPid 				 = '';
		$requestArrayFromClickfunnel = array(
        'txtFirstName'    	 		 => trim(addslashes(ucfirst(strtolower($requestJSONfromClickFunnel->first_name)))),
        'txtLastName'     	 		 => trim(addslashes(ucfirst(strtolower($requestJSONfromClickFunnel->last_name)))),
        'txtCompanyName'  	 		 => trim(addslashes($requestJSONfromClickFunnel->name)),
        'txtEmail'           		 => trim(strtolower($requestJSONfromClickFunnel->email)),
        'txtPhone'        	 		 => trim($requestJSONfromClickFunnel->phone),
        'country'         	 		 => trim($requestJSONfromClickFunnel->additional_info->custom_country),
        'txtZip'          	 		 => trim($requestJSONfromClickFunnel->zip),
        'txtPass'         	 		 => trim($requestJSONfromClickFunnel->additional_info->upwd),
        'recurlyAccountCode' 		 => trim($requestJSONfromClickFunnel->contact_profile->cf_uvid),
        'subId'           	 		 => trim($subscriptionId),
        'cfErrorMessage'	 		 => trim($errorMessage),
        'salesPersonId'    	 		 => 0,
        'ipAddress'        	 		 => trim($requestJSONfromClickFunnel->ip),
        /*--assigning variable for the growsumo partner key--*/
        'txtgsId'            		 => $GrowsumoPid,
    	'planCode'        	 		 => $planCode,
        );
    return $requestArrayFromClickfunnel;
	}

	private function callingGrowsumoAPI($txtEmail,$ipAddress,$txtFirstName,$txtLastName,$txtgsId,$recurlyAccountCode,$registrationId){
				/*
                check if customer alredy created by free training or not
                else Calling growsumo customer create operation to create a customer
                */
                $grsm_cust_detail_call = $this->growsumo->get_growsumo_customer($txtEmail);
                if(trim($grsm_cust_detail_call->message)   == 'Customer retrieved'){
                    $update_grsm_cust_arr['old_customer_key'] 	= $txtEmail;
                    $update_grsm_cust_arr['new_customer_key'] 	= $recurlyAccountCode;
                    $update_grsm_cust_arr['ip_address'] 		= $ipAddress;
                    $update_grsm_cust_arr['sent_from'] 			= 'noemal_signup';
                    $update_grsm_cust_arr['full_name'] 			= ucwords(trim($txtFirstName) . ' ' . trim($txtLastName));
                    
                    $update_grsm_cust_result = $this->growsumo->update_growsumo_customer($update_grsm_cust_arr);
                    $data_grsm_management = Array('growsumo_pk' => trim($grsm_cust_detail_call->rdata->partner_key));
                    $this->Signupmodel->updateData(CRO_COMPANY_RGSTRTN,$data_grsm_management, "ireg_id = ".$registrationId);
                    }else{
                    if($txtgsId != '' && !empty($txtgsId)){
                        $grsm_cust_data['cust_key'] 	= $recurlyAccountCode;
                        $grsm_cust_data['cust_name'] 	= ucwords(trim($txtFirstName) . ' ' . trim($txtLastName));
                        $grsm_cust_data['cust_email'] 	= $txtEmail;
                        $grsm_cust_data['cust_ref'] 	= $txtgsId;
                        $grsm_cust_data['ip_address'] 	= $ipAddress;

                        $this->growsumo->create_growsumo_customer($grsm_cust_data);    
                        }
                }
        }

        private function sendIntercomRequest($txtCompanyName,$txtFirstName,$txtLastName,$txtPhone,$registrationId,$uid,$adminEmail){
    	 #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-Code for INTERCOM INTEGRATION START-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
                $cmpnm = $txtCompanyName == '' ? $txtFirstName . ' ' . $txtLastName : $txtCompanyName;
                $company_arr = array(
                    'remote_created_at' 		=> time(),
                    'company_id'	 			=> $registrationId,
                    'name' 						=> trim(stripslashes($cmpnm)),
                    "plan" 						=> 'Start',
                    "custom_attributes" 		=> array(
                    "phone" 					=> trim($txtPhone),
                    'Account Status' 			=> 'Active',
                    'Account Type' 				=> 'Trial',
                    'Mandrill Active' 			=> 'No',
                    'Mailchimp Active' 			=> 'No',
                    "Chargebee Active" 			=> 'No',
                    'Number Of Leads' 			=> 0,
                    'Number Of Active Clients' 	=> 0,
                    'Number Of Affiliates' 		=> 0,
                    'Number Of Team Members' 	=> 0,
                    'Batch Print' 				=> 'off',      
                    'CC Information Added' 		=> 'No',
                    'Signup Flow' 				=> 1
                    )
                );
                $companyResponse = $this->intercom_request('companies', $company_arr);
                $userArray = array(
                    'user_id' 					=> $uid,
                    'email' 					=> trim($adminEmail),
                    'id' 						=> $uid,
                    'signed_up_at' 				=> time(),
                    'name' 						=> trim(ucfirst(strtolower($txtFirstName))) . ' ' . trim(ucfirst(strtolower($txtLastName))),
                    'companies' 				=> array(array(
                    	'id' 					=> $registrationId)),
                    'custom_attributes' 		=> array('Account Status' => 'Active','Role' => 'Account Holder')
                );
                $tmp_users = (array) $companyResponse;
                if (!empty($tmp_users)) {
                    $userResponse 		= $this->intercom_request('users', $userArray);
                    $tmp_users 			= new stdClass();
                    $tmp_users 			= (array) $userResponse;
                    if (!empty($tmp_users)) {
                        $arr = ( array("name" => 'Account Holder', "users" => array(array("user_id" => $uid))));
                        $tag_response 	= $this->intercom_request('tags', $arr);
                    }
                }
                #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=Code for INTERCOM INTEGRATION END=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

    }
    private function intercom_request($req, $array, $delete = false){
		$ssl = false;
		$username='ha9wuk8u';
		$password='bad99d6dc2ae485d0c0bed6ce094b44a588efdae';
		$access_token='dG9rOmUwNGM5NjA1X2YyYmRfNGRlY19hNmQ5X2Q0YmEyZjczNzZiMToxOjA=';
		$str = json_encode($array);
		$URL = 'https://api.intercom.io/'.$req;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$URL);
		if(!$ssl){
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}
		if(!$delete){
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
		}else{
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		}
		curl_setopt($ch, CURLOPT_TIMEOUT, 0); //timeout after 30 seconds
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		/*curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			   'Accept: application/json',
			   'Content-Type: application/json'));*/
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("accept: application/json","authorization: Bearer $access_token","cache-control: no-cache","content-type: application/json"));
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		$result = curl_exec($ch);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);
		return json_decode($result);
	}

	private function sendDatatoProofAPI($fname,$lname,$adminEmail,$txtCompanyName,$txtPhone,$txtZip,$ipAddress,$country){
		#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=Code for send data to proof API START=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
    	$countryName 		= $this->Signupmodel->get_county_name_by_id($country);
        $ipurl 				= "http://api.ipinfodb.com/v3/ip-city/?key=99605c097f5c09d144829554bf3ab4cf07df4c963100a575dce28468e21b1e44&ip=".$ip_address."&format=json";
        $ipresponsedata 	= $this->send_curl_request('GET',$ipurl);
        $ProofApi 			= array(
        		'type' 		=> 'custom',
            'first_name' 	=> $fname,
            'last_name' 	=> $lname,
            'email' 		=> $adminEmail,
            'company_name'	=> $txtCompanyName,
            'phone' 		=> $txtPhone,
            'country' 		=> $countryName,
            'city'			=> $ipresponsedata->cityName,
            'state'			=> $ipresponsedata->regionName,
            "zipcode" 		=> trim($txtZip),
            'IP_address' 	=> $ipAddress,
        );
        if($ipresponsedata->latitude != 0 && $ipresponsedata->longitude !=0)
        {
         $ipurl 			= "http://webhook.proofapi.com/cw/yU9lhy62a5gRRIz8eAxSXh1Tgdq1/-Kq-UXKcbk1iQCSRk4mZ";
         $ProofApi 			= json_encode($ProofApi);
         $proofapidata 		= $this->send_curl_request('POST',$ipurl,$ProofApi);
        }
        #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-Code for send data to proof API END-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
	}

	private function sendDatatoCloseioIntegration($adminEmail,$txtPhone,$txtFirstName,$txtLastName,$txtCompanyName){

	#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-Code for close.io integration START-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
	   $lead_result = $this->Signupmodel->selectDataGen(DB_DEFAULT, 'cro_lead_history','lead_id', "email = '".trim($adminEmail)."'");
	      if ($lead_result[0]->lead_id != "") {
	        $responses = $this->Signupmodel->updateLeadStatus($lead_result[0]->lead_id, 'stat_RsQTHYHWS7tzAl8kSsVJIPANCBH14iVAzwJCCCDSVRT');
	      } else {
	          $emails[] 	= array('email' => $adminEmail, 'type' => 'office');
	          $phones[] 	= array('phone' => $txtPhone, 'type' => 'office');
	          $contacts[] 	= array(
	          	'name' 		=> ucfirst(strtolower($txtFirstName)) . ' ' . ucfirst(strtolower($txtLastName)), 
	          	'emails' 	=> $emails, 
	          	'phones' 	=> $phones);
	          $custom       = array("Source" => 'Signup');
	          $lead 		= array('name' => $txtCompanyName, 'status_id' => 'stat_RsQTHYHWS7tzAl8kSsVJIPANCBH14iVAzwJCCCDSVRT', 'contacts' => $contacts, 'custom' => $custom);
	          $return_result = $this->Signupmodel->closeioAddLead($lead);
	          if ($return_result->id) {
	             $ins_data  = array('email' => $adminEmail,'lead_id' => $return_result->id);
	             $ret_ins   = $this->Signupmodel->insertGenData(DB_DEFAULT, CRO_LEAD_HISTORY, $ins_data);
	          }
	      }
	      #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=Code for close.io integration END=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
    }

	private function send_curl_request($method,$url,$post="")
	{
		$ch = curl_init($url);
		# set url to send post request 
		curl_setopt($ch, CURLOPT_URL, $url);
		# allow redirects 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		# return a response into a variable 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		# times out
		curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
                
                if($method == 'POST')
                {
                 # set POST method
		 curl_setopt($ch, CURLOPT_POST, 1);
		 # add POST fields parameters
		 curl_setopt($ch, CURLOPT_POSTFIELDS,$post);// Set the request as a POST FIELD for curl.
                 
                 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($post)));
                }
		//Execute cUrl 
		$response = curl_exec($ch);
		return json_decode($response);		
	}

	private function getTimeZoneFromIpAddress($ip_address = ""){
            if($ip_address =="")
            {
                if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
                $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                $ip_address = $_SERVER['REMOTE_ADDR'];
                }
            }
            $clientInformation = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip_address));
            $clientsLatitude = $clientInformation['geoplugin_latitude'];
            $clientsLongitude = $clientInformation['geoplugin_longitude'];
            $clientsCountryCode = $clientInformation['geoplugin_countryCode'];
            $timeZone = $this->get_nearest_timezone($clientsLatitude, $clientsLongitude, $clientsCountryCode);
            return $timeZone;
        }

    function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
            $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
                : DateTimeZone::listIdentifiers();
            
            if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

                $time_zone = '';
                $tz_distance = 0;

                //only one identifier?
                if (count($timezone_ids) == 1) {
                    $time_zone = $timezone_ids[0];
                } else {

                    foreach($timezone_ids as $timezone_id) {
                        $timezone = new DateTimeZone($timezone_id);
                        $location = $timezone->getLocation();
                        $tz_lat   = $location['latitude'];
                        $tz_long  = $location['longitude'];

                        $theta    = $cur_long - $tz_long;
                        $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
                            + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                        $distance = acos($distance);
                        $distance = abs(rad2deg($distance));
                        // echo '<br />'.$timezone_id.' '.$distance;

                        if (!$time_zone || $tz_distance > $distance) {
                            $time_zone   = $timezone_id;
                            $tz_distance = $distance;
                        }
                    }
                }
                return  $time_zone;
            }
            return 'unknown';
        }
}
?>