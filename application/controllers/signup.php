<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

    private $errorMessage='';

 	function __construct(){
		parent::__construct();
		$this->load->model('Signupmodel');
		$this->load->library('recurlyauthenticationwrapper'); 
		$this->load->helper('growsumo_helper');
        //$this->load->library('cf/cfiles');
        echo '1234';exit;

	}

    #-=-=-=-Start Steps to Signup ClickFunnel Webhook-=-=-=-=-=-=#
	public function signupClickFunnelWebhook(){
        #$requestJSONObject  = json_decode(file_get_contents("php://input"));
        $payloadjson=file_get_contents("http://localhost/CRC-CodeRefactoring/CRC---Refactoring");
        $requestJSONObject           = json_decode($payloadjson);
        if(AWS_ENV_STATUS == 'LIVE' )
        $requestArray = $this->setWebhookRequestParams($requestJSONObject->purchase->contact,$requestJSONObject->purchase->subscription_id,$requestJSONObject->purchase->error_message,'cr_start');    
        else if(AWS_ENV_STATUS == 'QA')
        $requestArray = $this->setWebhookRequestParams($requestJSONObject->contact,$requestJSONObject->subscription_id,$requestJSONObject->error_message,'cr_start_master');
        try{
            if(empty($requestArray)) {
            throw new Exception("RequestArray is not fetched form webhook");
            }
        $txtFirstName                = $requestArray['txtFirstName'];
        $txtLastName                 = $requestArray['txtLastName'];
        $txtEmail                    = $requestArray['txtEmail'];
        $ipAddress                   = $requestArray['ipAddress'];
        $txtgsId                     = $requestArray['txtgsId'];
        $requestCountry              = $requestArray['country'];
        $txtPhone                    = $requestArray['txtPhone'];
        $txtZip                      = $requestArray['txtZip'];
        $salesPersonId               = $requestArray['salesPersonId'];
        $planCode                    = $requestArray['planCode'];
        $subId                       = $requestArray['subId'];
        $validRecurlyAccountArray	 = $this->checkRecurlyAccountSubscription($subId,$requestArray['recurlyAccountCode'],$planCode);
        $recurlyAccountCode 		 = $validRecurlyAccountArray['recurlyAccountCode'];
        $subscriptionId 		 	 = $validRecurlyAccountArray['subscriptionId'];
        $clickFunnelSignupStatus 	 = $this->Signupmodel->selectDataClickFunnel($txtEmail);
        if($clickFunnelSignupStatus[0]['signup_status'] == '0' && $subscriptionId!=''){
        $dataSession =$this->submitSignupdataStep1($requestArray,$recurlyAccountCode,$subscriptionId);
        $uid                         = $dataSession['uid'];
        $registrationId              = $dataSession['registrationId'];
        $fname                       = $dataSession['fname'];
        $lname                       = $dataSession['lname'];
        $adminEmail                  = $dataSession['adminEmail'];
        $companyCountry              = $dataSession['companyCountry'];
        //$this->submitSignupdataStep2($registrationId);
        $clinentAffiliateIds=$this->submitSignupdataStep3($registrationId,$fname,$lname,$adminEmail,$companyCountry);
        $this->submitSignupdataStep4($requestCountry,$clinentAffiliateIds,$dataSession);
        $status=$this->Signupmodel->updateLastCompleteStep('entry_data_signup_step4',$registrationId);
        //$this->callingGrowsumoAPI($txtEmail,$ipAddress,$txtFirstName,$txtLastName,$txtgsId,$recurlyAccountCode,$registrationId);
        $this->sendIntercomRequest($txtCompanyName,$txtFirstName,$txtLastName,$txtPhone,$registrationId,$uid,$adminEmail);
        $this->sendDatatoProofAPI($fname,$lname,$adminEmail,$txtCompanyName,$txtPhone,$txtZip,$ipAddress,$requestCountry);
        $this->sendDatatoCloseioIntegration($adminEmail,$txtPhone,$txtFirstName,$txtCompanyName);
        //$this->sendEmailtoSalesPerson($salesPersonId,$requestCountry,$txtPhone,$fname,$lname,$adminEmail);
        //$this->sendWelcomeEmailtoNewUser($uid,$adminEmail,$txtFirstName,$txtLastName);
        $this->finalUpdateStatusClickFunnel($txtEmail,$recurlyAccountCode);
        echo '<pre>';print_r($status);exit;
        }
      }catch(Exception $e){
            $this->errorMessage=$e->getMessage();
      }
    }
    #-=-Steps to Signup ClickFunnel Webhook END-=-=-=#
    
    #-=-=-Start Insert Data into Registration and User access table in Main Database -=-=-=-=#
    private function submitSignupdataStep1($requestArray,$recurlyAccountCode,$subscriptionId){
    	$this->Signupmodel->updateStatusClickFunnel('2',$requestArray['txtEmail']);
        #submit_signup_data_step1() START
        $planType = "monthly";
        $timezone = $this->getTimeZoneFromIpAddress($requestArray['ipAddress']);
        if($timezone == '' || $timezone == 'UTC') { $timezone = 'America/Los_Angeles'; }
        if($timezone != '') { date_default_timezone_set($timezone); }
        $vtimezoneAbbr = $this->Signupmodel->getTimezone($timezone);
        $CroCompanyRegstrationData                  = array(
					"dreg_date"						=> date("Y-m-d H:i:s"),
					"vcompany_name"					=> $requestArray['txtCompanyName'],
					"vfirst_name"					=> $requestArray['txtFirstName'],
					"vlast_name"					=> $requestArray['txtLastName'],
					"vemail"						=> $requestArray['txtEmail'],
					"vphone"						=> $requestArray['txtPhone'],
					"vcompany_country" 				=> $requestArray['country'],
					"icompany_timezone" 			=> $timezone,
					"vtimezone_abbr" 				=> $vtimezoneAbbr[0]['vtimezone_abbr'],
					"vpostcode" 					=> trim($requestArray['txtZip']),
					"vaccount_status"				=> 'active',
					"ipackage_id" 					=> 7,
					"isales_person_id" 				=> $requestArray['salesPersonId'],
					"vplan_type" 					=> $planType,
					"vRecurly_email" 				=> $requestArray['txtEmail'],
					"vRecurlyAc_code" 				=> $recurlyAccountCode,
					"lead_notification_email" 		=> "1-",
					"affiliate_notification_email" 	=> "1-",
					"trial_period_end" 				=> date('Y-m-d', strtotime('+30 days')),
					'vIP_address' 					=> $requestArray['ipAddress'],
					'lastlogin' 					=> date("Y-m-d H:i:s"),
					'growsumo_pk' 					=> $requestArray['txtgsId']
                        );
        $lastInsertId      = $this->Signupmodel->setCompanyRegistrationData($CroCompanyRegstrationData);
        $countryDetailArr  = $this->Signupmodel->getCroCountries($requestArray['country']);
                $CC        = $countryDetailArr[0]['country_code'];
        $CroUserAccessData = array(
    				"ireg_id" 						=> $lastInsertId,
                    "vuser_name" 					=> addslashes($requestArray['txtEmail']),
                    "vpasswd" 						=> base64_encode(addslashes($requestArray['txtPass'])),
                    "vuser_type" 					=> '1',
                    "vFirst_Name" 					=> stripslashes($requestArray['txtFirstName']),
                    "vLast_Name" 					=> stripslashes($requestArray['txtLastName']),
                    "vemail" 						=> $requestArray['txtEmail'],
                    "vCountryCode" 					=> $CC,
                    "db_name" 						=> $lastInsertId . "_crd"
                );
            $dataSession['uid'] = $this->Signupmodel->setCroUserAccess($CroUserAccessData);
     		$dataSession['registrationId'] 		    = $lastInsertId;
            $dataSession['fname'] 					= stripslashes($requestArray['txtFirstName']);
            $dataSession['lname'] 					= stripslashes($requestArray['txtLastName']);
            $dataSession['companyName'] 			= stripslashes($requestArray['txtCompanyName']);
            $dataSession['phone'] 					= $requestArray['txtPhone'];
            $dataSession['adminEmail'] 				= $requestArray['txtEmail'];
            $dataSession['companyCountry'] 			= $requestArray['country'];
            $dataSession['recurlyPaymentStatus'] 	= 'trial';
            $dataSession['planType'] 				= $planType;
            $dataSession['userName'] 				= $requestArray['txtEmail'];
            $dataSession['userType'] 				= 'admin';
            $dataSession['dbName'] 					= $dataSession['registrationId'] . '_crd';
            $dataSession['countryCode'] 			= $CC;
            $this->Signupmodel->updateLastCompleteStep('submit_signup_data_step1',$dataSession['registrationId']);
            $getCAS = $this->Signupmodel->getAgendaSettings($dataSession['uid']);
            if(count($getCAS) <= 0){
            $dataCASTmp = array("iid" => '',"iuser_id" => $dataSession['uid'],"esend_email" => 'off');
            $this->Signupmodel->setAgendaSettings($dataCASTmp);
            }
            $this->createFolderAndMoveFilesInServer($dataSession['registrationId']);
            $updateRecurlyAccountCodeandSubsciptionId = Array('vRecurlyAc_code' => $recurlyAccountCode, 'vRecurlySubscriptionId' => $subscriptionId);
            $sta=$this->Signupmodel->updateCompanyData($updateRecurlyAccountCodeandSubsciptionId,$dataSession['registrationId']);
             #-=-START Create Container, upload Bureau Logos & other IMG In Rackspace=-=-=-#
            //$this->createContainerAndMoveFiles($dataSession['registrationId'],$dataSession['companyCountry'],$subscriptionId);
            try{
                if(empty($dataSession)){
                    throw new Exception("DataSession array is not prepared");
                }
            }catch(Exception $e){
            $this->errorMessage=$e->getMessage();
            }
            return $dataSession;
    }
    #-=-=Insert Data into Registration and User access table in Main Database End-=-=-=#
    
    #-=-=After Creating Container, upload Bureau Logos & other IMG In Rackspace Start=-=-=-=#
    private function submitSignupdataStep2($registrationId){
		#entry_data_signup_step2_first_3_cf() START
        $Imgarray1=array('acra.png','acrs.png','ck.png','crsk.png');
        foreach($Imgarray1 as $value1){
          $response = $this->cfiles->copy_obj($value1,'default_cmpny_misc',$registrationId.'_cmpny_misc');
        }
        $this->Signupmodel->updateLastCompleteStep('entry_data_signup_step2_first_3_cf',$registrationId);
        #entry_data_signup_step2_first_3_cf() END
        #entry_data_signup_step2_first_6_cf() START
        $Imgarray2=array('k8.gif','mcrs.png','pg.png');
        foreach($Imgarray2 as $value2){
          $response = $this->cfiles->copy_obj($value2,'default_cmpny_misc',$registrationId.'_cmpny_misc');
        }
        $this->Signupmodel->updateLastCompleteStep('entry_data_signup_step2_first_6_cf',$registrationId);
        #entry_data_signup_step2_first_6_cf() END
        #entry_data_signup_step2_another_6_cf() START
        $response = $this->cfiles->copy_obj('cct.jpg','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $response = $this->cfiles->copy_obj('wf.png','default_cmpny_misc',$registrationId.'_cmpny_misc');
        $Imgarray3=array('sample_photo_id_copy.png','sample_power_of_attorney.pdf','sample_utility_bill_copy.png');
        foreach($Imgarray3 as $value3){
        $response = $this->cfiles->copy_obj($value3, 'default_cmpny_documents', $registrationId. '_cmpny_documents');
        }
        $this->Signupmodel->updateLastCompleteStep('entry_data_signup_step2_another_6_cf',$registrationId);
        #entry_data_signup_step2_another_6_cf() END
     }
     #-=-=-=Start After Creating Container, upload Bureau Logos & other IMG In Rackspace -=-=-=#

     #-=-=-=-Start Insert Sample Client And Sample Affiliate In main DB And creating Database-=-=-=#
     private function submitSignupdataStep3($registrationId,$fname,$lname,$adminEmail,$companyCountry){
      		#entry_data_signup_step3() START
			$insertControlData 					= array();
			$insertControlData['ireg_id']   	= $registrationId;
			$insertControlData['sender_name'] 	= ucfirst(strtolower($fname)) . " " . ucfirst(strtolower($lname));
			$insertControlData['sender_email'] 	= $adminEmail;
			$this->Signupmodel->setCroControlData($insertControlData);
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
            $clientUserId           = $this->Signupmodel->setSampleClient($dataSampleClient);
			$affiiatePassword 		= str_shuffle('abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
			$affiiatePassword 		= base64_encode(substr($affiiatePassword, 0, 8));
			$dataSampleAffiliate    = array(
					"vuser_name" 	=> "sample_affiliate_" . $registrationId,
					"vpasswd" 		=> $affiiatePassword,
					"ireg_id" 		=> "0",
					"vuser_type" 	=> "affiliate",
					"vemail" 		=> "sample_" . $registrationId . "@affiliate.com",
					"db_name" 		=> $registrationId . "_crd"
							);
            $affiliateUserId = $this->Signupmodel->setSampleAffiliate($dataSampleAffiliate);
			$this->Signupmodel->updateLastCompleteStep('entry_data_signup_step3',$registrationId);
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
        try{
            if(empty($clinentAffiliateIds)){
                throw new Exception("ClinentAffiliateIds is not Set");
            }
        }catch(Exception $e){
        $this->errorMessage=$e->getMessage();
        }
        return $clinentAffiliateIds;
    }
    #-=-=-End Insert Sample Client And Sample Affiliate In main DB And creating Database-=-=-=#

    #-=-=-Start Insert and Update Sample data into Company Database-=-=-#
    private function submitSignupdataStep4($requestArrayCountry,$clinentAffiliateIds,$dataSession){
    	$adminUid 			= $dataSession['uid'];
    	$fname 				= $dataSession['fname'];
    	$lname 				= $dataSession['lname'];
    	$adminEmail 	 	= $dataSession['adminEmail'];
    	$registrationId 	= $dataSession['registrationId'];
    	$clientUserId 		= $clinentAffiliateIds['clientUserId'];
    	$affiliateUserId 	= $clinentAffiliateIds['affiliateUserId'];
		$countryDetailArr   = $this->Signupmodel->getCroCountries($requestArrayCountry);
        $CC                 = $countryDetailArr[0]['country_code'];
        $currencySymbol     = $countryDetailArr[0]['currency_symbol'];
    	if($_SERVER["HTTP_HOST"] == "localhost") {
                    $con 		= mysql_connect("localhost", "root", "");
                } else {
                    $con 		= mysql_connect(AWS_DB_HOST_NAME2,AWS_DB_USERNAME2,AWS_DB_PASSWORD2);
                }
                $db_selected 	= mysql_select_db('crcloud_'.$registrationId.'_crd', $con);
                $current_date 	= date('Y-m-d H:i:s');
                $current_Date_plus_2_hours = date("Y-m-d H:i:s", strtotime("+2 hour"));
            mysql_query('INSERT INTO `crd_team` (`iTeam_id`, `iUser_id`, `iRole_id`, `vFirst_Name`, `vLast_Name`, `vEmail`, `vPhone`, `vMobile`, `vFax`, `vPhoto`, `vAddress`, `gender`,`created_date`) VALUES (1,'.$adminUid.', 1, "' .$fname . '", "' . $lname . '", "' . $adminEmail . '", "", "", "", "", NULL, "Male", now())');

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
                mysql_query("UPDATE crcloud_" . $registrationId . "_crd.crd_agreements SET `tagreement` = REPLACE(`tagreement`, '$', '" . $currencySymbol . "')");
                mysql_query("UPDATE crcloud_" . $registrationId . "_crd.crd_dispute_reasons SET `vdispute_reason` = REPLACE(`vdispute_reason`, '$', '" . $currencySymbol . "')");
                mysql_query("UPDATE crcloud_" . $registrationId . "_crd.crd_letter_templ SET `vtempt_text` = REPLACE(`vtempt_text`, '$', '" . $currencySymbol . "')");
                mysql_query("UPDATE crcloud_" . $registrationId . "_crd.crd_options SET `vOptionValue` = REPLACE(`vOptionValue`, '$', '" . $currencySymbol. "')");
            }
            mysql_close($con);
    }
    #-=-=-End Insert and Update Sample data into Company Database-=-=-=#

    #-=-=-Start Create RackSpace Container And moving Sample Files in to Container-=-=#
    private function createContainerAndMoveFiles($registrationId,$companyCountry,$subscriptionId){
    	$dataSession['RecurlySubscriptionId'] 	= $subscriptionId;
        $dataSession['acc_status'] 				= 'active';
        $containerArr = array();
        $this->cfiles->cf_container 			= $registrationId.'_cmpny_attachment';
        $container_url = $this->cfiles->do_container('a');
        $containerArr['attachment'] 			= $container_url;
        $this->cfiles->cf_container 			= $registrationId.'_cmpny_documents';
        $container_url = $this->cfiles->do_container('a');
        $containerArr['documents'] 				= $container_url;
        $this->cfiles->cf_container 			= $registrationId.'_cmpny_misc';
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
        $this->Signupmodel->updateLastCompleteStep('entry_data_signup_step1_recurly',$registrationId);
    }
    #-=-=-=End Create RackSpace Container And moving Sample Files in to Container-=-=#

    #-=-=-Start Create Folder In Server And moving Index.php File in to root folders-=-=-=#
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
       $sta= $this->Signupmodel->updateLastCompleteStep('entry_data_signup_step1_useraccess',$registrationId);
    }
    #-=-=-End Create Folder In Server And moving Index.php File in to root folders-=-=-=#
    
    #-=-=-Start Checking Recurly Account Subscription with Account Code and SubscriptionId-=-=-=#
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
    #-=-=-End Checking Recurly Account Subscription with Account Code and SubscriptionId-=-=-=#

    #-=-=-=-Start Set webhook Request Params into an array-=-=-=-=#
	private function setWebhookRequestParams($requestJSONObject,$subscriptionId,$errorMessage,$planCode){
		if(property_exists($requestJSONObject->additional_info, 'growsumo_pid')) 
		$GrowsumoPid 				 = trim($requestJSONObject->additional_info->growsumo_pid);
        else
		$GrowsumoPid 				 =  '';
		$requestArray                =  array(
        'txtFirstName'    	 		 => trim(addslashes(ucfirst(strtolower($requestJSONObject->first_name)))),
        'txtLastName'     	 		 => trim(addslashes(ucfirst(strtolower($requestJSONObject->last_name)))),
        'txtCompanyName'  	 		 => trim(addslashes($requestJSONObject->name)),
        'txtEmail'           		 => trim(strtolower($requestJSONObject->email)),
        'txtPhone'        	 		 => trim($requestJSONObject->phone),
        'country'         	 		 => trim($requestJSONObject->additional_info->custom_country),
        'txtZip'          	 		 => trim($requestJSONObject->zip),
        'txtPass'         	 		 => trim($requestJSONObject->additional_info->upwd),
        'recurlyAccountCode' 		 => trim($requestJSONObject->contact_profile->cf_uvid),
        'subId'           	 		 => trim($subscriptionId),
        'cfErrorMessage'	 		 => trim($errorMessage),
        'salesPersonId'    	 		 => 0,
        'ipAddress'        	 		 => trim($requestJSONObject->ip),
        /*--assigning variable for the growsumo partner key--*/
        'txtgsId'            		 => $GrowsumoPid,
    	'planCode'        	 		 => $planCode,
        );
    return $requestArray;
	}
    #-=-=-=-End Set webhook Request Params into an array=-=-=#

    #-=-=-Start Calling Growsumo API-=-=-=#
	private function callingGrowsumoAPI($txtEmail,$ipAddress,$txtFirstName,$txtLastName,$txtgsId,$recurlyAccountCode,$registrationId){
				/*
                check if customer alredy created by free training or not
                else Calling growsumo customer create operation to create a customer
                */
                $grsm_cust_detail_call = get_growsumo_customer($txtEmail);
                if(trim($grsm_cust_detail_call->message)   == 'Customer retrieved'){
                    $update_grsm_cust_arr['old_customer_key'] 	= $txtEmail;
                    $update_grsm_cust_arr['new_customer_key'] 	= $recurlyAccountCode;
                    $update_grsm_cust_arr['ip_address'] 		= $ipAddress;
                    $update_grsm_cust_arr['sent_from'] 			= 'noemal_signup';
                    $update_grsm_cust_arr['full_name'] 			= ucwords(trim($txtFirstName) . ' ' . trim($txtLastName));
                    $update_grsm_cust_result = $this->growsumo->update_growsumo_customer($update_grsm_cust_arr);
                    $data_grsm_management = Array('growsumo_pk' => trim($grsm_cust_detail_call->rdata->partner_key));
                    updateGrowsumoInCompany($data_grsm_management,$registrationId);
                    }else{
                    if($txtgsId != '' && !empty($txtgsId)){
                        $grsm_cust_data['cust_key'] 	= $recurlyAccountCode;
                        $grsm_cust_data['cust_name'] 	= ucwords(trim($txtFirstName) . ' ' . trim($txtLastName));
                        $grsm_cust_data['cust_email'] 	= $txtEmail;
                        $grsm_cust_data['cust_ref'] 	= $txtgsId;
                        $grsm_cust_data['ip_address'] 	= $ipAddress;

                        create_growsumo_customer($grsm_cust_data);    
                        }
                }
        }
        #-=-=-End Calling Growsumo API-=-=-=-#

        #-=-=-Code for INTERCOM INTEGRATION START-=-=-=-#
        private function sendIntercomRequest($txtCompanyName,$txtFirstName,$txtLastName,$txtPhone,$registrationId,$uid,$adminEmail){
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
    #-=-=-Code for INTERCOM INTEGRATION END-=-=-#

    #-=-=-Code for send data to proof API START-=-=-#
	private function sendDatatoProofAPI($fname,$lname,$adminEmail,$txtCompanyName,$txtPhone,$txtZip,$ipAddress,$country){
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
    }
    #-=-=-=-Code for send data to proof API END-=-=-=-#

    #-=-=-=-Code for close.io integration START=-=-=-#
	private function sendDatatoCloseioIntegration($adminEmail,$txtPhone,$txtFirstName,$txtLastName,$txtCompanyName){
        $lead_result = $this->Signupmodel->getCroLeadHistory($adminEmail);
	      if ($lead_result[0]['lead_id'] != "") {
	        $responses = $this->Signupmodel->updateLeadStatus($lead_result[0]['lead_id'], 'stat_RsQTHYHWS7tzAl8kSsVJIPANCBH14iVAzwJCCCDSVRT');
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
	             $ret_ins   = $this->Signupmodel->setLeadHistory($ins_data);
	          }
	      }
	}
    #-=-=-=-Code for close.io integration END-=-=-=-=-#

    #-=-=-=-START Code Update Final Step Status into Clink Funnel-=-=-=-#
    private function finalUpdateStatusClickFunnel($txtEmail,$recurlyAccountCode){
        $check_reg_id_entry = $this->Signupmodel->selectDataGen(DB_DEFAULT, CRO_COMPANY_RGSTRTN ,'ireg_id', "vemail = '".$txtEmail."'");
            if (isset($check_reg_id_entry[0]->ireg_id) && $check_reg_id_entry[0]->ireg_id > 0) {
                $update_CF_signup_status = Array('signup_status' => '1','recurly_account_code' => $recurlyAccountCode);
                $this->Signupmodel->updateClickFunnelStatus($update_CF_signup_status,$txtEmail);
               @mail("debuglog@creditrepaircloud.com", $this->errorMessage, print_r($txtEmail,1));
  
            } else {
                @mail("debuglog@creditrepaircloud.com", "CF - ACCOUNT NOT CREATED", print_r($txtEmail,1));
                @mail("debuglog@creditrepaircloud.com", "CF - B. recurlyAc_code", 'recurlyAc_code - '.$recurlyAc_code);
               $this->Signupmodel->updateStatusClickFunnel('0', $txtEmail);
            }
    }
    #-=-=-=-End Code Update Final Step Status into Clink Funnel -=-=-=-#

    #-=-=-=-START Code for send email to sales person -=-=-=-=#
     private function sendEmailtoSalesPerson($salesPersonId,$country,$txtPhone,$fname,$lname,$adminEmail){
        $country_name       = $this->Signupmodel->getCountyNamebyId($country);
        $adminArray         = array(
                'name'      => $fname . ' ' . $lname,
                'email'     => $adminEmail,
                'phone'     => $txtPhone,
                'country'   => $country
            );
        if ($salesPersonId > 0) {
            $salesPersonDetails         = $this->Signupmodel->getSalesPerson($salesPersonId);
            if ($salesPersonDetails[0]->vsales_person_email != "") {
                $emailTplSalesPerson    = $this->Signupmodel->emailTemplateForSalesPerson(ucfirst($salesPersonDetails[0]->vsales_person_name), $adminArray);
                $to         = $salesPersonDetails[0]->vsales_person_email;
                $subject    = "Your customer " . ucwords($adminArray['name']) . " signed up for Credit Repair Cloud Free Trial! (survey)";
                $from       = "support@creditrepaircloud.com";
                $fromName   = "Credit Repair Cloud";
                $email_Sent = $this->sendSalesPersonEmail($to, $subject, $emailTplSalesPerson, $from, $fromName);
            }
        }
        #-=-=-START Code for send terms & conditions agreement mail to Daniel=-=-=-#
        $to_daniel         = "sales@credit-aid.com";
        $subject_terms     = "Agreement of terms, refund policy, charge authorization for Credit Repair Cloud " . $adminArray['name'];
        $from_terms        = "support@creditrepaircloud.com";
        $emailTplTerms     = $this->Signupmodel->TemplateForTermsConditions($adminArray);
        $email_Sent_terms  = $this->sendEmail($to_daniel, $subject_terms, $emailTplTerms, $from_terms, $adminArray['name']);
        #-=-=-=-END Code for send terms & conditions agreement mail to Daniel-=-=-=#
    }
    #-=-=-=-END Code for send email to sales person-=-=-=-=-#

    #-=-=-=-START Code for sales person email function-=-=-=-#
    function sendSalesPersonEmail($to, $subject, $emailTpl, $from, $from_name){
        $this->load->library('email');
        $config = unserialize (EMAIL_CONFIG);
        $this->email->initialize($config);
        $this->email->from($from, $from_name);
        $this->email->to($to); 
        $this->email->add_custom_header('X-MC-Subaccount','crcloud');
        $this->email->subject($subject);
        $this->email->message($emailTpl); 
        $email_Sent = $this->email->send();
        return $email_Sent;
    }
    #-=-=-=-Code for sales person email function END-=-=-=-#

    #-=-=-=-Code Send Welcome Email to NewUser START=-=-=-#
    private function sendWelcomeEmailtoNewUser($uid,$adminEmail,$txtFirstName,$txtLastName){
                $userDetails = $this->Signupmodel->getUserNameAndPassword($uid);
                $to_signup = trim($adminEmail);
                $userDetails->vpasswd = base64_decode($user_details->vpasswd);
                $subject_signup = "Welcome to Credit Repair Cloud";
                $fromSignup = "support@creditrepaircloud.com";
                $fromNameSignup = "Credit Repair Cloud";
                $NAME = ucwords(trim($txtFirstName) . ' ' . trim($txtLastName));
                $image_url = "https://www.creditrepaircloud.com/application/images/mailer/";
                $date = date("Y-m-d");
                $search = array('{mailer_path}',
                    '{admin_first_name}',
                    '{admin_last_name}',
                    '{user_id}',
                    '{password}',
                    '{subscription_end_date}',
                    'SECURECLIENTACCESS_URL',
                    'TEAM_LOGIN_URL'
                );
                $replace = array($image_url,
                    ucfirst(trim(stripslashes($txtFirstName))),
                    ucfirst(trim(stripslashes($txtLastName))),
                    trim(stripslashes($userDetails->vuser_name)),
                    stripslashes($userDetails->vpasswd),
                    date('m/d/Y', strtotime('+29 days')),
                    SECURECLIENTACCESS_URL,
                    TEAM_LOGIN_URL
                );
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From: ' . $fromNameSignup . ' <' . $fromSignup . '>' . "\r\n";
                $headers .= 'Reply-To: support@creditrepaircloud.com' . "\r\n";
                $headers .= 'Bcc: sales@credit-aid.com' . "\r\n";
                $email = ucfirst(trim(ucfirst(strtolower($txtFirstName)))) . ' ' . ucfirst(trim(ucfirst(strtolower($txtLastName)))) . '<' . $to_signup . '>';
                $template = $this->Signupmodel->emailTemplateForNewSignup();
                $emailTpl = stripslashes($template->body);
                $emailTpl = '<body style="margin:0px; padding:0px; background-color: #F2F2F2" bgcolor="#F2F2F2">' . str_replace($search, $replace, $emailTpl) . '</body>';
                if($txtFirstName!="") {
                    @mail($email, $template->subject, $emailTpl, $headers);
                }
                
    }
    #-=-=-=-Code for Send Welcome Email to NewUser END-=-=-=-#

    #-=-=-=-Code for send email function Start-=-=-=-=-#
    private function sendEmail($to, $subject, $emailTpl, $from, $from_name, $cc_email='',$reply_to='', $reply_to_name='', $bcc_email=''){
        #echo $to; exit;
        $this->load->library('email');
        $config = unserialize (EMAIL_CONFIG);
        $this->email->initialize($config);
        if($from == 'no-reply@secureclientaccess.com')
           $from = $from_name.'<'.$from.'>';
        $this->email->from($from, $from_name);
        $this->email->to($to); 
        if($cc_email != ''){
            $this->email->cc($cc_email);
        }
        if($bcc_email != ''){
            $this->email->bcc($bcc_email);
        }
        if ($reply_to != '') {
            $this->email->reply_to($reply_to, $reply_to_name);
        }
        $this->email->add_custom_header('X-MC-Subaccount','crcloud');
        $this->email->add_custom_header('X-MC-SigningDomain','creditrepaircloud.com');
        $this->email->subject($subject);
        $this->email->message($emailTpl); 
        $email_Sent = $this->email->send();
        #echo $CI->email->print_debugger()."<br><hr><br>"; exit;
        return $email_Sent;
    }
    #-=-=-Code for send email function END-=-=-#

    #-=-=-Code for send Curl Resquest Start-=-=-=-#
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
     #-=-=-Code for send Curl Resquest END-=-=-=-=-#

    #-=-=-=-Start Code for Get Time Zone From IpAddress-=-=-#
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

    private function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
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
        #-=-=-Code for Get Time Zone From IpAddress END-=-=-=-#
}
?>