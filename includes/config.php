<?PHP

// sitewide manual constants
define('SITE_SSL_URL','https://www.cheaplocaldeals.com/');
define('MOB_URL','http://www.cheaplocaldeals.com/mobile/');
define('SITE_ADMIN_SSL_URL',SITE_SSL_URL.'admin/');
define('SITE_AFFILIATE_SSL_URL',SITE_SSL_URL.'affiliates/');

define('STD_TEMPLATE_DIR','includes/template/');
define('STD_ADMIN_TEMPLATE_DIR','../includes/template/admin/');
define('STD_AFFILIATE_TEMPLATE_DIR','../includes/template/affiliate/');

// site directory constants
define('SITE_DIR',$_SERVER['DOCUMENT_ROOT'].'/');
define('TEMP_DIR','/var/www/temp/');
define('SITE_CACHE_DIR',TEMP_DIR.'cache/');
define('SITE_ADMIN_DIR',SITE_DIR.'admin/');
define('SITE_AFFILIATE_DIR',SITE_DIR.'affiliates/');
define('SITE_ADMIN_CSV_DIR',SITE_ADMIN_DIR.'csvs/');
define('SITE_ADMIN_CLASSES_DIR',SITE_ADMIN_DIR.'classes/');
define('SITE_AFFILIATE_CLASSES_DIR',SITE_AFFILIATE_DIR.'classes/');
define('SITE_ADMIN_FUNCTIONS_DIR',SITE_ADMIN_DIR.'functions/');
define('INCLUDES_DIR',SITE_DIR.'includes/');
define('LIBS_DIR',INCLUDES_DIR.'libs/');
define('TEMPLATE_DIR',INCLUDES_DIR.'template/');
define('TEMPLATE_ADMIN_DIR',INCLUDES_DIR.'template/admin/');
define('TEMPLATE_AFFILIATE_DIR',INCLUDES_DIR.'template/affiliate/');
define('CLASSES_DIR',INCLUDES_DIR.'classes/');
define('FUNCTIONS_DIR',INCLUDES_DIR.'functions/');

// image directories
define('IMAGES_DIRECTORY',SITE_DIR.'images/');
define('CATEGORY_IMAGES_DIRECTORY',IMAGES_DIRECTORY.'category/');
define('CUSTOMER_IMAGES_DIRECTORY',IMAGES_DIRECTORY.'customers/');
define('LOCATIONS_IMAGES_DIRECTORY',IMAGES_DIRECTORY.'locations/');
define('API_IMAGES_DIRECTORY',IMAGES_DIRECTORY.'api_users/');

// database connection constants
define('DB_TYPE','mysql');
define('DB_HOST','localhost');
define('DB_NAME','cheaploc_cld');
define('DB_USERNAME','cheaploc_cld');
define('DB_PASSWORD','cldpass');

// layout constants
define("T","\t"); // tab
define("LB","\n"); // line break

define("ADMIN_PERMISSION_DENIED","<center><strong>You have not been given access to this section.</strong></center>");

// form constants
define('PAYMENT_TYPES',serialize(array('' => '','credit_card' => 'Credit Card','check' => 'Check')));
define('CC_TYPES',serialize(array('','Visa','Master Card','American Express','Discover')));

if (empty($pear_set)) {
	// set Pear directory
	ini_set("include_path", (LIBS_DIR."PEAR/" . ini_get("include_path")));
	$pear_set = 1;
}

// disable magic quotes
if (get_magic_quotes_gpc() && !function_exists('stripslashes_deep')) {
    function stripslashes_deep($value)
    {
        $value = is_array($value) ?
                    array_map('stripslashes_deep', $value) :
                    stripslashes($value);

        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

// builds daily hours drop down
$hours_select = '';

$hours_dd = '';
$hours_dd[] = '12:00';
$hours_dd[] = '12:30';
$hours_dd[] = '1:00';
$hours_dd[] = '1:30';
$hours_dd[] = '2:00';
$hours_dd[] = '2:30';
$hours_dd[] = '3:00';
$hours_dd[] = '3:30';
$hours_dd[] = '4:00';
$hours_dd[] = '4:30';
$hours_dd[] = '5:00';
$hours_dd[] = '5:30';
$hours_dd[] = '6:00';
$hours_dd[] = '6:30';
$hours_dd[] = '7:00';
$hours_dd[] = '7:30';
$hours_dd[] = '8:00';
$hours_dd[] = '8:30';
$hours_dd[] = '9:00';
$hours_dd[] = '9:30';
$hours_dd[] = '10:00';
$hours_dd[] = '10:30';
$hours_dd[] = '11:00';
$hours_dd[] = '11:30';

reset($hours_dd);
$hours_select[] = 'CLOSED';
// build AM list
foreach($hours_dd as $value) {
	$hours_select[] = $value.' AM';
}

reset($hours_dd);
// build PM list
foreach($hours_dd as $value) {
	$hours_select[] = $value.' PM';
}
define('HOURS_SELECT',serialize($hours_select));
define('DAYS_ARRAY',serialize(array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')));

// city type dd array
$city_type = array(
				   1=>'county - other',
				   2=>'town',
				   3=>'cities',
				   4=>'capital'
				   );
?>