<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class RecurlyAuthenticationWrapper { 

	public function __construct()
    {
    	$CI =& get_instance();
        $CI->load->library('recurlyclass');
       // echo '<pre>';print_r(PAYMENT_GATEWAY_API_KEY);exit;
        Recurly_Client::$apiKey = PAYMENT_GATEWAY_API_KEY;
    }

    public function getRecurlySubscriptionsbyAccountCode($recurlyAccountCode){
        $reslut = Recurly_SubscriptionList::getForAccount($recurlyAccountCode);
        return $reslut;
    }
    public function getRecurlySubscriptionbySubscriptionId($subscriptionId){
    	$reslut = Recurly_Subscription::get($subscriptionId);
    	return $reslut;
    }
    


}

?>