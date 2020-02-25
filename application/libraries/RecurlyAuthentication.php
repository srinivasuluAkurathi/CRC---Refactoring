<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class RecurlyAuthentication { 

	public function __construct()
    {
    	$this->load->library('recurlyclass');
    }

    public function getRecurlySubscriptionbyAccountCode($recurlyAccountcode){
    	$reslut = Recurly_SubscriptionList::getForAccount($recurlyAccountcode);
    	return $reslut;
    }
    public function getRecurlySubscriptionbySubscriptionid($subscriptionid){
    	$reslut = Recurly_Subscription::get($subscriptionid);
    	return $reslut;
    }
    


}

?>