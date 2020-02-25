<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {


 	function __construct(){
		parent::__construct();
		$this->load->model('signup_model','mymodel'); // Wrong
		$this->load->model('common_repository','common_model'); // Wrong
	}

	public function index(){
			
            $webhook_response = json_decode(file_get_contents("php://input"));
            if(AWS_ENV_STATUS == 'LIVE' ){
            $response=$this->setWebhookResponseParams($webhook_response->purchase,'cr_start');
            }else{
            $response=$this->setWebhookResponseParams($webhook_response,'cr_start_master');
            }

	}

	public function thirtyDayFreeTrialSignupClickFunnelWebhook(){
			
            $requestJSONfromClickFunnel  = json_decode(file_get_contents("php://input"));
            if(AWS_ENV_STATUS == 'LIVE' )
            $requestArrayFromClickfunnel = $this->mymodel->setWebhookRequestParams($requestJSONfromClickFunnel->purchase,'cr_start');
            else
            $requestArrayFromClickfunnel = $this->mymodel->setWebhookRequestParams($requestJSONfromClickFunnel,'cr_start_master');            

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