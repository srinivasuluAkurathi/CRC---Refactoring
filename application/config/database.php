<?php
#echo AWS_DB_HOST_NAME;
#exit("asdfa");
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------
  | DATABASE CONNECTIVITY SETTINGS
  | -------------------------------------------------------------------
  | This file will contain the settings needed to access your database.
  |
  | For complete instructions please consult the 'Database Connection'
  | page of the User Guide.
  |
  | -------------------------------------------------------------------
  | EXPLANATION OF VARIABLES
  | -------------------------------------------------------------------
  |
  |	['hostname'] The hostname of your database server.
  |	['username'] The username used to connect to the database
  |	['password'] The password used to connect to the database
  |	['database'] The name of the database you want to connect to
  |	['dbdriver'] The database type. ie: mysql.  Currently supported:
  mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
  |	['dbprefix'] You can add an optional prefix, which will be added
  |				 to the table name when using the  Active Record class
  |	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
  |	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
  |	['cache_on'] TRUE/FALSE - Enables/disables query caching
  |	['cachedir'] The path to the folder where cache files should be stored
  |	['char_set'] The character set used in communicating with the database
  |	['dbcollat'] The character collation used in communicating with the database
  |				 NOTE: For MySQL and MySQLi databases, this setting is only used
  | 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7.
  | 				 There is an incompatibility in PHP with mysql_real_escape_string() which
  | 				 can make your site vulnerable to SQL injection if you are using a
  | 				 multi-byte character set and are running versions lower than these.
  | 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
  |	['swap_pre'] A default table prefix that should be swapped with the dbprefix
  |	['autoinit'] Whether or not to automatically initialize the database.
  |	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
  |							- good for ensuring strict SQL while developing
  |
  | The $active_group variable lets you choose which connection group to
  | make active.  By default there is only one group (the 'default' group).
  |
  | The $active_record variables lets you determine whether or not to load
  | the active record class
 */
$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = AWS_DB_HOST_NAME;
$db['default']['username'] = AWS_DB_USERNAME;
$db['default']['password'] = AWS_DB_PASSWORD;
$db['default']['database'] = 'crcloud_cronln_63';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

# code for connection of new database @@@@@ S
$CI = &get_instance();
$CI->load->library('session');
$session = $CI->session->userdata('user_session');
$renew_session_user = $CI->session->userdata('renew_sess_user'); #added by bhavik on 02-01-2016 Start (added option to change plan while reactivate expired account)
$DB_ROOT = AWS_DB_USERNAME2;
$DB_PASSWORD = AWS_DB_PASSWORD2;
#updated by bhavik on 02-01-2016 Start
if (isset($session['uid']) && $session['uid'] > 0 || isset($renew_session_user->ireg_id) && $renew_session_user->ireg_id > 0) {
    if (!isset($session['db_name'])) {
        $session['db_name'] = $renew_session_user->ireg_id . "_crd";
    }
    # new db connection for admin db after login @@@@@ S
    $db['alternate']['hostname'] = AWS_DB_HOST_NAME;
    $db['alternate']['username'] = $DB_ROOT;
    $db['alternate']['password'] = $DB_PASSWORD;
    $db['alternate']['database'] = 'crcloud_' . $session['db_name'];
    $db['alternate']['dbdriver'] = "mysql";
    $db['alternate']['dbprefix'] = "";
    $db['alternate']['pconnect'] = FALSE;
    $db['alternate']['db_debug'] = TRUE;
    $db['alternate']['cache_on'] = FALSE;
    $db['alternate']['cachedir'] = "";
    $db['alternate']['char_set'] = "utf8";
    $db['alternate']['dbcollat'] = "utf8_general_ci";
    # new db connection for admin db after login @@@@@ E
}
#updated by bhavik on 02-01-2016 End
# new db connection for only create new DB while user signup @@@@@ S
error_reporting(0); # bcose wizard pdf export not working S @@@@@ 08 April 2013
# code for connection of new database @@@@@ E
$cron_session = $CI->session->userdata('cron_session');
if ($cron_session['db_name'] != '') {
    $db['cron']['hostname'] = AWS_DB_HOST_NAME;
    $db['cron']['username'] = $DB_ROOT;
    $db['cron']['password'] = $DB_PASSWORD;
    $db['cron']['database'] = 'crcloud_' . $cron_session['db_name'];
    $db['cron']['dbdriver'] = "mysql";
    $db['cron']['dbprefix'] = "";
    $db['cron']['pconnect'] = FALSE;
    $db['cron']['db_debug'] = TRUE;
    $db['cron']['cache_on'] = FALSE;
    $db['cron']['cachedir'] = "";
    $db['cron']['char_set'] = "utf8";
    $db['cron']['dbcollat'] = "utf8_general_ci";
}

$weblead_session = $CI->session->userdata('weblead_session');
if ($weblead_session['dalexb'] > 0) {
    $db['weblead']['hostname'] = AWS_DB_HOST_NAME;
    $db['weblead']['username'] = $DB_ROOT;
    $db['weblead']['password'] = $DB_PASSWORD;
    $db['weblead']['database'] = 'crcloud_' . $weblead_session['dalexb'] . '_crd';
    $db['weblead']['dbdriver'] = "mysql";
    $db['weblead']['dbprefix'] = "";
    $db['weblead']['pconnect'] = FALSE;
    $db['weblead']['db_debug'] = TRUE;
    $db['weblead']['cache_on'] = FALSE;
    $db['weblead']['cachedir'] = "";
    $db['weblead']['char_set'] = "utf8";
    $db['weblead']['dbcollat'] = "utf8_general_ci";
}

# S @@@@@ database connection for the web lead API 13/August/2013
$webapi_session = $CI->session->userdata('webapi_session');
if ($webapi_session['dalexb'] > 0) {
    $db['webapiuser']['hostname'] = AWS_DB_HOST_NAME;
    $db['webapiuser']['username'] = $DB_ROOT;
    $db['webapiuser']['password'] = $DB_PASSWORD;
    $db['webapiuser']['database'] = 'crcloud_' . $webapi_session['dalexb'] . '_crd';
    $db['webapiuser']['dbdriver'] = 'mysql';
    $db['webapiuser']['dbprefix'] = '';
    $db['webapiuser']['pconnect'] = FALSE;
    $db['webapiuser']['db_debug'] = TRUE;
    $db['webapiuser']['cache_on'] = FALSE;
    $db['webapiuser']['cachedir'] = '';
    $db['webapiuser']['char_set'] = 'utf8';
    $db['webapiuser']['dbcollat'] = 'utf8_general_ci';
    $db['webapiuser']['swap_pre'] = '';
    $db['webapiuser']['autoinit'] = TRUE;
    $db['webapiuser']['stricton'] = FALSE;
}

$db['webapi']['hostname'] = AWS_DB_HOST_NAME;
$db['webapi']['username'] = $DB_ROOT;
$db['webapi']['password'] = $DB_PASSWORD;
$db['webapi']['database'] = 'crcloud_crd_api';
$db['webapi']['dbdriver'] = 'mysql';
$db['webapi']['dbprefix'] = '';
$db['webapi']['pconnect'] = FALSE;
$db['webapi']['db_debug'] = TRUE;
$db['webapi']['cache_on'] = FALSE;
$db['webapi']['cachedir'] = '';
$db['webapi']['char_set'] = 'utf8';
$db['webapi']['dbcollat'] = 'utf8_general_ci';
$db['webapi']['swap_pre'] = '';
$db['webapi']['autoinit'] = TRUE;
$db['webapi']['stricton'] = FALSE;
# E @@@@@ database connection for the web lead API 13/August/2013
/*
  | -------------------------------------------------------------------
  | connection for cron job DB
  | -------------------------------------------------------------------
  |
  | */
$db['crondb']['hostname'] = AWS_DB_HOST_NAME;
$db['crondb']['username'] = $DB_ROOT;
$db['crondb']['password'] = $DB_PASSWORD;
$db['crondb']['database'] = 'crcloud_crd_cronjob';
$db['crondb']['dbdriver'] = 'mysql';
$db['crondb']['dbprefix'] = '';
$db['crondb']['pconnect'] = FALSE;
$db['crondb']['db_debug'] = TRUE;
$db['crondb']['cache_on'] = FALSE;
$db['crondb']['cachedir'] = '';
$db['crondb']['char_set'] = 'utf8';
$db['crondb']['dbcollat'] = 'utf8_general_ci';
$db['crondb']['swap_pre'] = '';
$db['crondb']['autoinit'] = TRUE;
$db['crondb']['stricton'] = FALSE;
/* End of file database.php */
/* Location: ./application/config/database.php */
