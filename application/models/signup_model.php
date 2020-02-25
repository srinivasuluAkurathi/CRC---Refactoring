<?php 
class signup_model extends CI_Model{
	
 public function setWebhookResponseParams($webhook_response,$plan_code){

        $respone=array(
        'txtFirstName'    => trim(addslashes(ucfirst(strtolower($webhook_response->contact->first_name))));
        'txtLastName'     => trim(addslashes(ucfirst(strtolower($webhook_response->contact->last_name))));
        'txtCompanyName'  => trim(addslashes($webhook_response->contact->name));
        'txtEmail'        => trim(strtolower($webhook_response->contact->email));
        'txtPhone'        => trim($webhook_response->contact->phone);
        'country'         => trim($webhook_response->contact->additional_info->custom_country);
        'txtZip'          => trim($webhook_response->contact->zip);
        'txtPass'         => trim($webhook_response->contact->additional_info->upwd);
        'recurlyAc_code'  => trim($webhook_response->contact->contact_profile->cf_uvid);
        'subid'           => trim($webhook_response->subscription_id);
        'CF_error_message'=> trim($webhook_response->error_message);
        'sales_person_id' => 0;
        'ip_address'      => trim($webhook_response->contact->ip);
        /*--assigning variable for the growsumo partner key--*/
        'txtgs_id'        => trim($webhook_response->contact->additional_info->growsumo_pid);
        'plan_code'       => $plan_code;
        );
 	return $respone;
 }

}
?>