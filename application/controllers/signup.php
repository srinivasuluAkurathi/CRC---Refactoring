<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {


 	function __construct(){
		parent::__construct();
		$this->load->model('Signupmodel','myModel');  
		$this->load->model('Commonrepository','commonModel');
		$this->load->library('RecurlyAuthentication'); 
	}

	public function thirtyDayFreeTrialSignupClickFunnelWebhook(){
			$requestJSONfromClickFunnel  = json_decode(file_get_contents("php://input"));
            if(AWS_ENV_STATUS == 'LIVE' )
            $requestArrayFromClickfunnel = $this->setWebhookRequestParams($requestJSONfromClickFunnel->purchase,'cr_start');
            else
            $requestArrayFromClickfunnel = $this->setWebhookRequestParams($requestJSONfromClickFunnel,'cr_start_master');
        	$validRecurlyAccountArray	 = $this->checkIsRecurlyValidAccount($requestArrayFromClickfunnel,$requestJSONfromClickFunnel);
        	$recurlyAccountCode 		 = $validRecurlyAccountArray['recurlyAccountCode'];
        	$subscriptionid 		 	 = $validRecurlyAccountArray['subscriptionid'];
                       

	}





	private function checkIsRecurlyValidAccount($requestArrayFromClickfunnel,$requestJSONfromClickFunnel){
		$validRecurlyAccountArray							= array();
		$isRecurlyValidAccount								= 0;
		$validRecurlyAccountArray['subscriptionid']			= $requestArrayFromClickfunnel['subid'];
		$validRecurlyAccountArray['recurlyAccountCode']		= $requestArrayFromClickfunnel['recurlyAc_code'];
		try {
		$subscriptions =$this->RecurlyAuthentication->getRecurlySubscriptionbyAccountCode($validRecurlyAccountArray['recurlyAccountCode']);
		foreach ($subscriptions as $subscription) {
			if ($subscription->state == 'active' && $subscription->plan->plan_code == $plan_code) {
			$isRecurlyValidAccount							= 1;
			$validRecurlyAccountArray['subscriptionid'] 	= $subscription->uuid;
			}
		  }
		} catch (Recurly_NotFoundError $e) {

		}

		if($isRecurlyValidAccount == 0 && !empty($validRecurlyAccountArray['subscriptionid'])){
		try {    
		$subscription = $this->RecurlyAuthentication->getRecurlySubscriptionbySubscriptionid($validRecurlyAccountArray['subscriptionid']);
		$validRecurlyAccountArray['recurlyAccountCode']		= explode("accounts/",array_values((array) $subscription->account)[1])[1];
		}catch (Recurly_NotFoundError $e) {
		@mail("debuglog@creditrepaircloud.com", "CF - RECURLY ACCOUNT SUBSCRIPTION NOT FOUND", print_r($requestJSONfromClickFunnel,1));
			exit;
			}
		}
        return $validRecurlyAccountArray;
	}


	private function setWebhookRequestParams($requestJSONfromClickFunnel, $planCode){
		 $requestArrayFromClickfunnel    = array(
        'txtFirstName'    => trim(addslashes(ucfirst(strtolower($requestJSONfromClickFunnel->contact->first_name))));
        'txtLastName'     => trim(addslashes(ucfirst(strtolower($requestJSONfromClickFunnel->contact->last_name))));
        'txtCompanyName'  => trim(addslashes($requestJSONfromClickFunnel->contact->name));
        'txtEmail'        => trim(strtolower($requestJSONfromClickFunnel->contact->email));
        'txtPhone'        => trim($requestJSONfromClickFunnel->contact->phone);
        'country'         => trim($requestJSONfromClickFunnel->contact->additional_info->custom_country);
        'txtZip'          => trim($requestJSONfromClickFunnel->contact->zip);
        'txtPass'         => trim($requestJSONfromClickFunnel->contact->additional_info->upwd);
        'recurlyAc_code'  => trim($requestJSONfromClickFunnel->contact->contact_profile->cf_uvid);
        'subid'           => trim($requestJSONfromClickFunnel->subscription_id);
        'CF_error_message'=> trim($requestJSONfromClickFunnel->error_message);
        'sales_person_id' => 0;
        'ip_address'      => trim($requestJSONfromClickFunnel->contact->ip);
        /*--assigning variable for the growsumo partner key--*/
        'txtgs_id'        => trim($requestJSONfromClickFunnel->contact->additional_info->growsumo_pid);
        'plan_code'       => $planCode;
        );
 	return $requestArrayFromClickfunnel;
	}
}
?>