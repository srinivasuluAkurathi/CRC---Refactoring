<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Recurly API key
|--------------------------------------------------------------------------
|
*/
//define('PAYMENT_GATEWAY_API_KEY',		'602fa6d308494ae29528a8e4c02e7cbf');//APP

define('PAYMENT_GATEWAY_API_KEY',		'cc26851c6b75437aa9c0cb6d62428c7d');//QA

#define('PAYMENT_GATEWAY_API_KEY',		'bda753835e6846e6a75029688ea632b9');//samrat@sasainfotech.com


/*
|--------------------------------------------------------------------------
| Mandrill API key
|--------------------------------------------------------------------------
|
*/
define('MANDRILL_API_KEY',		'Z3W32gwnpCX7456BmyPydw');

define('WEBSITE_URL',		'http://dev.creditrepaircloud.com');

/*
|--------------------------------------------------------------------------
| DATABASE Connection Constants
|--------------------------------------------------------------------------
|
*/

define('AWS_DB_HOST_NAME','localhost');
define('AWS_DB_USERNAME','root');
define('AWS_DB_PASSWORD','');

/*
|--------------------------------------------------------------------------
| AWS DB Connection user 2
|--------------------------------------------------------------------------
|
| Database credentials for the AWS
|
 *
*/
define('AWS_DB_HOST_NAME2','localhost');
define('AWS_DB_USERNAME2','root');
define('AWS_DB_PASSWORD2','');



/*
|--------------------------------------------------------------------------
| Time Zone
|--------------------------------------------------------------------------
|
| Time zone is changed to PST - L.A (daniel's time)
|
*/
date_default_timezone_set('America/Los_Angeles');


/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/*
|--------------------------------------------------------------------------
| Email configuration array
|--------------------------------------------------------------------------
|
| Use this config array for initializing the email library
|
*/
define ("EMAIL_CONFIG", serialize (array('useragent' => 'creditrepaircloud',
										'charset' => 'utf-8',
										'wordwrap' => TRUE,
										'mailtype' => 'html',
										'protocol' => 'smtp',
										'smtp_host' => 'localhost',
										'smtp_user' => 'sales@creditrepaircloud.com',
										'smtp_pass' => 'Flatop63!',
										'smtp_timeout' => '10'
								 )));

define('NOREPLY_SCA', 'no-reply@secureclientaccess.com');
define('NOREPLY_CRC', 'no-reply@creditrepaircloud.com');

#validation tag
define('VALIDATION_PREFIX', '<br/><span class="redfont">');
define('VALIDATION_SUFFIX', '</span>');



/*
|--------------------------------------------------------------------------
| Database Table Names
|--------------------------------------------------------------------------
|
| Use this constants in the query to use table names.
|
*/

#=============DB HOST's========================
define('DB_DEFAULT',						'default');
define('DB_ALTERNATE',						'alternate');
define('DB_CRON',							'cron');
define('DB_CRONDB',							'crondb');
define('DB_WEBLEAD',						'weblead');
define('DB_WEBAPI',							'webapi');
define('DB_WEBAPIUSER',						'webapiuser');




/*
|--------------------------------------------------------------------------
| Database Table Names
|--------------------------------------------------------------------------
|
| Use this constants in the query to use table names.
|
*/

#=============crd_cronjob========================
define('CRD_MAILCHIMP',					'crd_mailchimp');
define('CRD_MAILCHIMP_LISTS',			'crd_mailchimp_lists');


#=============crd_api========================
define('CRD_API_CLIENTS',					'crd_api_clients');

#=============cronln_63========================
define('CRO_CITIES',						'cro_cities');
define('CRO_COMPANY_HALF_RGSTRTN',			'cro_company_half_rgstrtn');
define('CRO_COMPANY_PAYMENTS',				'cro_company_payments');
define('CRO_COMPANY_RGSTRTN',				'cro_company_rgstrtn');
define('CRO_COUNTRIES',						'cro_countries');
define('CRO_STATES',						'cro_states');
define('CRO_USER_ACCESS',					'cro_user_access');
define('CRO_PACKAGE',						'cro_package');
define('CRD_INSTRUCTION',  				'crd_instruction');
define('CRO_SALES_PERSON',					'cro_sales_person');
define('CRO_CONTROLS',						'cro_controls');
define('CRO_AGENDA_SETTINGS',				'cro_agenda_settings');
define('CRO_RECURLY_INVOICE_DETAILS',		'cro_recurly_invoice_details'); #By: aniket 15 oct 2013
define('CRO_EMAIL_TEMPLATES',				'cro_email_templates'); #By: aniket 4 April 2014
define('CRO_RECURLY_TRANS_INFO',			'cro_recurly_trans_info'); #By: aniket 9 May 2014
define('CRO_RECURLY_SUBSCRIPTION_INFO',		'cro_recurly_subscription_info'); #By: aniket 9 May 2014
define('CRO_SALES_COMMISSION',				'cro_sales_commission'); #By: aniket 9 May 2014
define('CRO_SALES_SUB_COMMISSION',			'cro_sales_sub_commission'); #By: aniket 9 May 2014
define('CRO_MAILCHIMP_NEWSLETTER_LIST',		'cro_mailchimp_newsletter_list'); #By: aniket 26 May 2014
define('CRO_SUPPORT_USERS_APPOINTMENTS',	'cro_support_users_appointments'); #By: ashok 04 August 2014
define('CRO_SUCCESS_SPECIALIST_AVAILABILITIES',	'cro_success_specialist_availabilities');
define('CRO_SUPPORT_USERS_APPOINTMENTS_LOGS', 'cro_support_users_appointments_logs'); #By: aniket 22 Aug 2014

#=============$1_crd===============================
#define('CRD_BUREAUS',						'crd_agency');
define('CRD_BUREAUS',						'crd_bureaus');
define('CRD_AGREEMENTS',					'crd_agreements');
#define('CRD_BUREAUS',						'crd_bureaus');
define('CRD_CITIES',						'crd_cities');
define('CRD_CLIENTSTATUS',					'crd_clientstatus');
define('CRD_CITIES_BAK',					'crd_cities_bak');
define('CRD_CLIENTLETTERS',					'crd_clientletters');
define('CRD_CLIENTS',						'crd_clients');
define('CRD_CLIENTS_ASSIGNEDTO',			'crd_clients_assignedto');
define('CRD_CLIENT_NOTES',					'crd_client_notes');
define('CRD_COMPANY',						'crd_company');
define('CRD_COMPANY_CONTACTS',				'crd_company_contacts');
define('CRD_CONTACT_CATG',					'crd_contact_catg');
define('CRD_COUNTRIES',						'crd_countries');
define('CRD_DISPUTEITEMS',					'crd_disputeitems');
define('CRD_DISPUTE_BUREAUS_FURNI',			'crd_dispute_bureaus_furni');
define('CRD_DISPUTE_REASONS',				'crd_dispute_reasons');
define('CRD_LETTERS_DISPUTE_RELATION',		'crd_letters_dispute_relation');
define('CRD_DISPUTE_STATUSTYPES',			'crd_dispute_statustypes');
define('CRD_FURNISHERS',					'crd_furnishers');
define('CRD_ITEMTYPES_NOUSE',				'crd_itemtypes_nouse');
define('CRD_LETTER_STATUSTYPES',			'crd_letter_statustypes');
define('CRD_LETTER_TEMPL',					'crd_letter_templ');
define('CRD_LETTER_TEMPLCATG',				'crd_letter_templcatg');
define('CRD_MODULE_LIST',					'crd_module_list');
define('CRD_PAPERWORK',						'crd_paperwork');
define('CRD_PAPERWORK_CLIENT',				'crd_paperwork_client');
define('CRD_PERMISSION_LIST',				'crd_permission_list');
define('CRD_ROLE_LIST',						'crd_role_list');
define('CRD_ROLE_MODULE_PERMISSION_LIST',	'crd_role_module_permission_list');
define('CRD_SCHEDULER',						'crd_scheduler');
define('CRD_SCORE',							'crd_score');
define('CRD_STATES',						'crd_states');
define('CRD_AFFILIATE',						'crd_affiliate');
define('CRD_TEAM',							'crd_team');
define('CRD_CLIENTS_REFEREDBY',				'crd_clients_referedby');
define('CRD_TEAM_LOGIN_SENT',				'crd_team_login_sent');
define('CRD_AFFILIATE_LOGIN_SENT',			'crd_affiliate_login_sent');
define('CRD_CLIENT_LOGIN_SENT',				'crd_client_login_sent');
define('CRD_MASTER_CONTACT_LIST',			'crd_master_contact_list');
define('CRD_MODULE_PERMISSION_LIST',			'crd_module_permission_list');
define('CRD_CLIENT_STATUSES',				'crd_client_statuses');
define('CRD_CLIENT_STATUS_LOGS',			'crd_client_status_logs');

define('CRD_CLIENT_INVOICEITEM',			'crd_client_invoiceitem');
define('CRD_CLIENT_INVOICE',				'crd_client_invoice');
define('CRD_CLIENT_PAYMENT_RECIEVED',		'crd_client_payment_recieved');
define('CRD_REMINDER_TYPE',					'crd_reminder_type');
define('CRD_TO_DO_LIST',					'crd_to_do_list');
define('CRD_MESSAGES',						'crd_messages');
define('CRD_CLIENT_IMPORTED_PDF',			'crd_client_imported_pdf');
define('CRD_GUIDED_TOUR',					'crd_guided_tour');
define('CRD_INVOICEITEM_MASTER',			'crd_invoiceitem_master');
define('CRD_CHAT',							'crd_chat');
define('CRD_LOGIN_LOGS',					'crd_login_logs');
define('CRD_RESOURCES',						'crd_resources');
define('CRD_OPTIONS',						'crd_options');

define('CRD_CR_SOURCE',						'crd_cr_source'); # 13/aug/2013 S @@@@@
define('CRD_ABBREVIATION',						'crd_abbreviation'); # 10/June/2013 S @@@@@

if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
    define('REMOTE_ADDR',						$_SERVER['HTTP_X_FORWARDED_FOR']);
} else { 
    define('REMOTE_ADDR',						$_SERVER['REMOTE_ADDR']);
}
define('REQUEST_URI',						$_SERVER['REQUEST_URI']);
define('URLS_RIGHT',						$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
define('PROVIDERS_COUNT',					'10'); # source code provider count 28/Nov/2013 @shok.

define('CRD_CLIENT_PAYMENT_OPTION',			'crd_client_payment_option');
define('CRD_COMMON_LOG',					'crd_common_log');
define('CRD_CLIENTS_DELETED',				'crd_clients_deleted');
define('CRD_QUICK_MODE_ITEM_DESCRIPTION',	'crd_quick_mode_item_description');

define('CRD_EMAIL_SETTING',					'crd_email_setting'); #By: aniket 11 jan 2014
define('CRD_EMAIL_TEMPLATE',				'crd_email_template'); #By: aniket 12 feb 2014
define('CRD_CHARGEBEE_PLAN',				'crd_chargebee_plan'); #By: aniket 29 may 2014
define('CRD_CHARGEBEE_COUPON',				'crd_chargebee_coupon'); #By: aniket 5 july 2014


define('CRO_LEAD_HISTORY',				'cro_lead_history');

# @shok : 21/April/2014 add sipute item from Quick mode for client choice 
define('CRD_DISPUTEITEMS_QM',					'crd_disputeitems_qm');
define('CRD_DISPUTE_BUREAUS_FURNI_QM',			'crd_dispute_bureaus_furni_qm');
define('CRD_QUICKNOTE',  						'crd_quicknote'); 

#--------------------------------------------------------
# @shok : 07/July/2014
# affiliate commission module
#--------------------------------------------------------
define('CRD_AFFILIATE_COMMISSION',  			'crd_affiliate_commission');
define('CRD_CLIENT_AFFILIATE_HIST',  			'crd_client_affiliate_hist');
define('CRD_AFFILIATE_PAYMENT_HIST',  			'crd_affiliate_payment_hist');
define('CRD_DOCUMENTS',  			'crd_documents');




/*
|--------------------------------------------------------------------------
| drpanel - main admin
|--------------------------------------------------------------------------
|
*/
define('CRO_ADMIN_PANEL',					'cro_admin_panel');
define('CRO_QUICK_VIDEOS',					'cro_quick_videos');
define('RECORD_PER_PAGE_DRPANEL',			'25');
if ($_SERVER['HTTP_HOST'] == "sasa-srv-w7") {
	define('DOC_ROOT_SIGNATURE_PATH',			$_SERVER['DOCUMENT_ROOT']."creditrepaircloud/uploads/dsign/");
	define('DOC_ROOT_UPLOAD_PATH',				$_SERVER['DOCUMENT_ROOT']."creditrepaircloud/uploads/");
}else{
	define('DOC_ROOT_SIGNATURE_PATH',			$_SERVER['DOCUMENT_ROOT']."/uploads/dsign/");
	define('DOC_ROOT_UPLOAD_PATH',				$_SERVER['DOCUMENT_ROOT']."/uploads/");
}

/*
|--------------------------------------------------------------------------
| spanel - sales people login
|--------------------------------------------------------------------------
|
*/
define('RECORD_PER_PAGE_SPANEL',			'25');
define('CRO_USERS_APPOINTMENTS',			'cro_users_appointments');
define('CRO_SALES_PERSON_SCHEDULES',		'cro_sales_person_schedules');

/*
|--------------------------------------------------------------------------
| supportpanel - customer support login
|--------------------------------------------------------------------------
|
*/
define('CRO_SUPPORT_PANEL',					'cro_support_panel');
define('CRO_SUPPORT_PANEL_LOGS',			'cro_support_panel_logs');
define('RECORD_PER_PAGE_SUPPORTPANEL',		'25');



if ($_SERVER['HTTP_HOST'] == "sasa-srv-w7") {
 define('SECURECLIENTACCESS_URL',			'http://sasa-srv-w7:8080/creditweb_portal/');
 define('TEAM_LOGIN_URL',					'https://sasa-srv-w7:8080/creditweb/login');
}else {
 define('SECURECLIENTACCESS_URL',			'http://dev.secureclientaccess.com/');
 define('TEAM_LOGIN_URL',					'http://dev.creditrepaircloud.com');
}



# username  host and password for the new feture link S @@@@@  11/April/2013
define('WP_HOST',						'www.credit-aid.com');
define('WP_USER',						'wordpress_5');
define('WP_PASS',						'hWw18_xRO5');
define('WP_DB',						'wordpress_6');
# username  host and password for the new feture link E @@@@@ 11/April/2013

define('CLOUD_FILES_CHANGES_STAGGING',						'yes'); 


define('LOG_DELETED',					'deleted');
define('LOG_UPDATED',					'updated');
define('LOG_INSERTED',					'inserted');
define('LOG_ADDED',						'added');
define('LOG_CHANGED',					'changed');
define('LOG_CREATED',					'created');
define('LOG_REMOVED',					'removed');

#chargebee log text's - aniket 24 june 2014
define('CB_LOG_SETTINGS_UPDATED',			'Chargebee settings updated');
define('CB_LOG_PLAN_ADDED',					'Chargebee plan added');
define('CB_LOG_PLAN_NAME_EDITED',			'Chargebee plan name edited');
define('CB_LOG_PLAN_EDITED',				'Chargebee plan edited');
define('CB_LOG_PLAN_DELETED',				'Chargebee plan deleted');
define('CB_LOG_PLAN_ARCHIVED',				'Chargebee plan archived');
define('CB_LOG_ENABLED',					'Chargebee activated');
define('CB_LOG_DISABLED',					'Chargebee deactivated');

define('CB_LOG_SUBSCRIPTION_ADDED',			'Chargebee plan assigned to client and subscription started');
define('CB_LOG_SUBSCRIPTION_ADDED_WEBFORM',	'Chargebee plan assigned to client and subscription started via webform');
define('CB_LOG_SUBSCRIPTION_CANCELLED',		'Chargebee subscription cancelled');
define('CB_LOG_SUBSCRIPTION_REACTIVATED',	'Chargebee subscription reactivated');
define('CB_LOG_INVOICE_ADDED',				'Chargebee invoice added');
define('CB_LOG_INVOICE_STATUS_CHANGED',		'Chargebee invoice status changed');
define('CB_LOG_SUBSCRIPTION_STATUS_CHANGED','Chargebee subscription status changed');
define('CB_LOG_PLAN_CHANGED',				'Chargebee plan of client changed');
define('CB_LOG_ADHOC_CHARGE',				'Chargebee 1 time charge taken');


#-----------------------------------------------------------
# Client choice dispute status : @shok : 15/March/2014
#-----------------------------------------------------------
$client_choice_dispute_status = array('verify_item'=>'Verify Item',
										'not_mine'=>'Not Mine',
										'ignore'=>'Ignore',
										'never_late'=>'Never Late');

define('CLIENT_CHOICE_DISPUTE_STATUS',		serialize($client_choice_dispute_status));

						
$dispute_priority = array('high'=>'High',
						'medium'=>'Medium',
						'low'=>'Low');

define('DISPUTE_PRIORITY',					serialize($dispute_priority));


$allowd_client_choice_status_for_edit = array('0','2','3','4');
define('ALLOWD_CLIENT_CHOICE_STATUS_FOR_EDIT',	serialize($allowd_client_choice_status_for_edit));

define("TOKAN", "pSyvf48GOds72hNQWumrjq5OrmU4zliPl4s4qiZE");
define("EMAIL", "Support@credit-aid.com");
define("Z_URL", "https://creditaid.zendesk.com/api/v2/tickets.json");


#storage space exceed msg aniket 13 june 2014
define('STORAGE_LIMIT_MSG', 'Your storage capacity is full, you cannot upload/attach any more files until you either delete unnecessary files/attachments or contact us to add more storage to your plan.');
/* End of file constants.php */
/* Location: ./application/config/constants.php */
