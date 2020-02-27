<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class RecurlyAuthenticationWrapper { 

	public function __construct()
    {
    	$CI =& get_instance();
        $CI->load->library('recurlyclass');
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