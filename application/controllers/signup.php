<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

    private $errorMessage='';

 	function __construct(){
		parent::__construct();
		$this->load->model('Signupmodel');
		$this->load->library('recurlyauthenticationwrapper'); 
	}

	public function thirtyDayFreeTrialSignupClickFunnelWebhook(){
			#$requestJSONfromClickFunnel  = json_decode(file_get_contents("php://input"));
			$payloadjson=file_get_contents("http://localhost/CRC-CodeRefactoring/CRC---Refactoring");
			$requestJSONfromClickFunnel=json_decode($payloadjson);
			if(AWS_ENV_STATUS == 'LIVE' )
            $requestArrayFromClickfunnel = $this->setWebhookRequestParams($requestJSONfromClickFunnel->purchase,'cr_start');
            else
            $requestArrayFromClickfunnel = $this->setWebhookRequestParams($requestJSONfromClickFunnel,'cr_start_master');
        	$validRecurlyAccountArray	 = $this->checkIsRecurlyValidAccount($requestArrayFromClickfunnel['subId'],$requestArrayFromClickfunnel['recurlyAccountCode'],$requestArrayFromClickfunnel['planCode']);
        	$recurlyAccountCode 		 = $validRecurlyAccountArray['recurlyAccountCode'];
        	$subscriptionId 		 	 = $validRecurlyAccountArray['subscriptionId'];
        	$clickFunnelSignupStatus 	 = $this->Signupmodel->selectData('half_regiestered_clickfunnel','signup_status',"email = '".$requestArrayFromClickfunnel['txtEmail']."'");

        	
    }

	private function checkIsRecurlyValidAccount($subscriptionId,$recurlyAccountCode,$planCode){
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
		echo '<pre>';print_r($subscription);exit;
		$validRecurlyAccountArray['recurlyAccountCode']	= explode("accounts/",array_values((array) $subscription->account)[1])[1];
		}catch (Recurly_NotFoundError $e) {
				$this->$errorMessage="CF - RECURLY ACCOUNT SUBSCRIPTION NOT FOUND THROUGH SUBSCRIPTION ID";
			}
		}
        return $validRecurlyAccountArray;
	}
	private function setWebhookRequestParams($requestJSONfromClickFunnel, $planCode){
		if(property_exists($requestJSONfromClickFunnel->contact->additional_info, 'growsumo_pid')) 
		$GrowsumoPid 				 = trim($requestJSONfromClickFunnel->contact->additional_info->growsumo_pid);else
		$GrowsumoPid 				 = '';
		$requestArrayFromClickfunnel = array(
        'txtFirstName'    	 		 => trim(addslashes(ucfirst(strtolower($requestJSONfromClickFunnel->contact->first_name)))),
        'txtLastName'     	 		 => trim(addslashes(ucfirst(strtolower($requestJSONfromClickFunnel->contact->last_name)))),
        'txtCompanyName'  	 		 => trim(addslashes($requestJSONfromClickFunnel->contact->name)),
        'txtEmail'           		=> trim(strtolower($requestJSONfromClickFunnel->contact->email)),
        'txtPhone'        	 		=> trim($requestJSONfromClickFunnel->contact->phone),
        'country'         	 		=> trim($requestJSONfromClickFunnel->contact->additional_info->custom_country),
        'txtZip'          	 		=> trim($requestJSONfromClickFunnel->contact->zip),
        'txtPass'         	 		=> trim($requestJSONfromClickFunnel->contact->additional_info->upwd),
        'recurlyAccountCode' 		=> trim($requestJSONfromClickFunnel->contact->contact_profile->cf_uvid),
        'subId'           	 		=> trim($requestJSONfromClickFunnel->subscription_id),
        'cfErrorMessage'	 		=> trim($requestJSONfromClickFunnel->error_message),
        'salesPersonId'    	 		=> 0,
        'ipAddress'        	 		=> trim($requestJSONfromClickFunnel->contact->ip),
        /*--assigning variable for the growsumo partner key--*/
        'txtgsId'            		=> $GrowsumoPid,
    	'planCode'        	 		=> $planCode,
        );
    return $requestArrayFromClickfunnel;
	}
}
?>