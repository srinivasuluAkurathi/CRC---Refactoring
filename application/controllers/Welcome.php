<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct()

	{

		parent::__construct();

		$this->load->helper('url');

	}

	public function index(){
		header('Content-Type: application/json');
		$payloadjson=file_get_contents("http://localhost/CRC-CodeRefactoring/CRC---Refactoring/welcome/payloadjson");
		//$payloadjson1=json_decode(json_encode($payloadjson), true);
		print_r($payloadjson);exit;
	}
	public function payloadjson()
	{
		$payloadjson='{"id":50538562,"products":[{"id":1609585,"name":"test_aws","stripe_plan":"","amount":{"fractional":"0.0","currency":{"id":"usd","alternate_symbols":["US$"],"decimal_mark":".","disambiguate_symbol":"US$","html_entity":"$","iso_code":"USD","iso_numeric":"840","name":"United States Dollar","priority":1,"smallest_denomination":1,"subunit":"Cent","subunit_to_unit":100,"symbol":"$","symbol_first":true,"thousands_separator":","},"bank":{"store":{"index":{"EUR_TO_USD":"1.084","EUR_TO_JPY":"119.92","EUR_TO_BGN":"1.9558","EUR_TO_CZK":"25.226","EUR_TO_DKK":"7.4701","EUR_TO_GBP":"0.8363","EUR_TO_HUF":"337.41","EUR_TO_PLN":"4.3012","EUR_TO_RON":"4.8098","EUR_TO_SEK":"10.5688","EUR_TO_CHF":"1.0605","EUR_TO_ISK":"139.3","EUR_TO_NOK":"10.16","EUR_TO_HRK":"7.4567","EUR_TO_RUB":"70.7943","EUR_TO_TRY":"6.6688","EUR_TO_AUD":"1.6442","EUR_TO_BRL":"4.7569","EUR_TO_CAD":"1.4403","EUR_TO_CNY":"7.6045","EUR_TO_HKD":"8.4445","EUR_TO_IDR":"15094.7","EUR_TO_ILS":"3.7158","EUR_TO_INR":"77.724","EUR_TO_KRW":"1315.68","EUR_TO_MXN":"20.6185","EUR_TO_MYR":"4.5886","EUR_TO_NZD":"1.716","EUR_TO_PHP":"55.36","EUR_TO_SGD":"1.5166","EUR_TO_THB":"34.39","EUR_TO_ZAR":"16.4649","EUR_TO_EUR":1},"options":{},"mutex":{},"in_transaction":false},"rounding_method":null,"currency_string":null,"rates_updated_at":"2020-02-25T00:00:00.000+00:00","last_updated":"2020-02-26T07:04:06.132+00:00"}},"amount_currency":"USD","created_at":"2018-10-09T06:22:59.000Z","updated_at":"2019-06-10T12:25:40.000Z","subject":"Thank you for your purchase","html_body":"\u003cp\u003eThank you for your purchase\u003c/p\u003e\u003cp\u003eYou may access your Thank You Page here anytime:\u003c/p\u003e\u003cp\u003e#PRODUCT_THANK_YOU_PAGE#\u003c/p\u003e","thank_you_page_id":31241782,"stripe_cancel_after_payments":null,"braintree_cancel_after_payments":null,"bump":false,"cart_product_id":null,"billing_integration":"Recurly","infusionsoft_product_id":null,"braintree_plan":null,"infusionsoft_subscription_id":null,"ontraport_product_id":null,"ontraport_payment_count":null,"ontraport_payment_type":"","ontraport_unit":null,"ontraport_gateway_id":null,"ontraport_invoice_id":null,"commissionable":true,"statement_descriptor":null,"netsuite_id":null,"netsuite_tag":null,"netsuite_class":null}],"member_id":null,"contact":{"id":1099251978,"page_id":23790005,"first_name":"signup","last_name":"test3","name":"signup test3","address":"","city":"","country":"","state":"","zip":"","email":"singuptest2@yopmail.com","phone":"(738) 328-3283","webinar_at":null,"webinar_last_time":null,"webinar_ext":"hGqyHO3s","created_at":"2020-02-26T10:05:26.000Z","updated_at":"2020-02-26T10:05:26.000Z","ip":"183.82.116.204","funnel_id":6201126,"funnel_step_id":31241781,"unsubscribed_at":null,"cf_uvid":"null","cart_affiliate_id":"","shipping_address":"","shipping_city":"","shipping_country":"","shipping_state":"","shipping_zip":"","vat_number":"","affiliate_id":null,"aff_sub":"","aff_sub2":"","cf_affiliate_id":null,"contact_profile":{"id":547337631,"first_name":"signup","last_name":"test3","address":"","city":null,"country":null,"state":null,"zip":null,"email":"singuptest2@yopmail.com","phone":"(738) 328-3283","created_at":"2020-02-26T10:05:26.000Z","updated_at":"2020-02-26T10:05:26.000Z","unsubscribed_at":null,"cf_uvid":"0d9a16263d64a435184ce0d02b1722b3","shipping_address":"","shipping_country":null,"shipping_city":null,"shipping_state":null,"shipping_zip":null,"vat_number":null,"middle_name":null,"websites":null,"location_general":null,"normalized_location":null,"deduced_location":null,"age":null,"gender":null,"age_range_lower":null,"age_range_upper":null,"action_score":null,"known_ltv":"0.00","tags":[]},"additional_info":{"cf_affiliate_id":"","time_zone":null,"utm_source":"","utm_medium":"","utm_campaign":"","utm_term":"","utm_content":"","cf_uvid":"null","webinar_delay":"-63749951561809","purchase":{"product_ids":["1609585"],"credit_card_number":"############1111","credit_card_exp_date_month":"06","credit_card_exp_date_year":"2023","payment_method_nonce":"","order_saas_url":"","payment_gateway_token":"HCpkvN-NHiwwE-qoywPIDQ"},"upwd":"123$%^","custom_country":"224","accepted_terms":"true"},"time_zone":null,"upwd":"123$%^","custom_country":"224","accepted_terms":"true"},"funnel_id":6201126,"stripe_customer_token":null,"created_at":"2020-02-26T10:05:26.000Z","updated_at":"2020-02-26T10:05:26.000Z","subscription_id":"51efc096289a1728b034ab42fb979b55","charge_id":null,"ctransreceipt":null,"status":"paid","fulfillment_status":null,"fulfillment_id":null,"fulfillments":{},"payments_count":null,"infusionsoft_ccid":null,"oap_customer_id":null,"braintree_customer_id":null,"payment_instrument_type":null,"original_amount_cents":0,"original_amount":{"fractional":"0.0","currency":{"id":"usd","alternate_symbols":["US$"],"decimal_mark":".","disambiguate_symbol":"US$","html_entity":"$","iso_code":"USD","iso_numeric":"840","name":"United States Dollar","priority":1,"smallest_denomination":1,"subunit":"Cent","subunit_to_unit":100,"symbol":"$","symbol_first":true,"thousands_separator":","},"bank":{"store":{"index":{"EUR_TO_USD":"1.084","EUR_TO_JPY":"119.92","EUR_TO_BGN":"1.9558","EUR_TO_CZK":"25.226","EUR_TO_DKK":"7.4701","EUR_TO_GBP":"0.8363","EUR_TO_HUF":"337.41","EUR_TO_PLN":"4.3012","EUR_TO_RON":"4.8098","EUR_TO_SEK":"10.5688","EUR_TO_CHF":"1.0605","EUR_TO_ISK":"139.3","EUR_TO_NOK":"10.16","EUR_TO_HRK":"7.4567","EUR_TO_RUB":"70.7943","EUR_TO_TRY":"6.6688","EUR_TO_AUD":"1.6442","EUR_TO_BRL":"4.7569","EUR_TO_CAD":"1.4403","EUR_TO_CNY":"7.6045","EUR_TO_HKD":"8.4445","EUR_TO_IDR":"15094.7","EUR_TO_ILS":"3.7158","EUR_TO_INR":"77.724","EUR_TO_KRW":"1315.68","EUR_TO_MXN":"20.6185","EUR_TO_MYR":"4.5886","EUR_TO_NZD":"1.716","EUR_TO_PHP":"55.36","EUR_TO_SGD":"1.5166","EUR_TO_THB":"34.39","EUR_TO_ZAR":"16.4649","EUR_TO_EUR":1},"options":{},"mutex":{},"in_transaction":false},"rounding_method":null,"currency_string":null,"rates_updated_at":"2020-02-25T00:00:00.000+00:00","last_updated":"2020-02-26T07:04:06.132+00:00"}},"original_amount_currency":"USD","manual":false,"error_message":null,"nmi_customer_vault_id":null,"event":"created"}';
		echo $payloadjson;exit;
		//$this->load->view('welcome_message');
	}
}
