<?PHP
ini_set("memory_limit","-1"); 

// start output buffer
ob_start();

// load config file
require_once('config.php');

if (!class_exists('DB')) {
  // connect to database server
  require_once(CLASSES_DIR.'database.php');
}

// include settings
require_once(INCLUDES_DIR.'settings.php');

// load table classes
require_once(CLASSES_DIR.'load_tables.php');

// set user geo data
require_once(CLASSES_DIR.'geo_ip.php');
$geo_data = new page_geo_ip;

//// add session handler class
//require_once(CLASSES_DIR.'sessions.php');
require(CLASSES_DIR.'sessions.php');
$sessions = new sessions;

// include HTML functions
require_once(FUNCTIONS_DIR.'html_functions.php');

// include common functions
require_once(FUNCTIONS_DIR.'common.php');

// include memcached functions
require_once(FUNCTIONS_DIR.'memcached.php');

// include output buffer functions
require_once(FUNCTIONS_DIR.'output_buffer.php');

// load WYSIWYG editor
include_once(LIBS_DIR."fckeditor/fckeditor.php") ;

// include password functions
require_once(FUNCTIONS_DIR.'passwords.php');

// load form fields handler
require_once(CLASSES_DIR.'form_writer.php');
$form_write = new forms_write;

// load html table class
require_once(CLASSES_DIR.'table_master.php');
$table_master = new table_master;

// load browsers class
require_once(CLASSES_DIR.'browser.php');
$browser = new browser;

// load html header class
require_once(CLASSES_DIR.'headers.php');

// load email classes
require_once('Mail.php');
require_once('Mail/mime.php');

// load api referrer handler class
if (!class_exists('api_ref_chk')) {
  require_once(CLASSES_DIR.'api_ref_chk.php');
  $api_ref_chk = new api_ref_chk;
}

// load api handler class
require_once(CLASSES_DIR.'api_load.php');
$api_load = new api_load;

if(isset($_POST['api_key'])) {
  $api_load->api_key = $_POST['api_key'];
  $api_load->load_api();
}

// load config file
require_once(INCLUDES_DIR.'domain_conf.php');

// set site view status
if($_SESSION['browse'] != 'normal'){
  // Include the Tera-WURFL file
  require_once(LIBS_DIR.'/Tera-WURFL/TeraWurfl.php');
  // instantiate the Tera-WURFL object
  $wurflObj = new TeraWurfl();
   
  // Get the capabilities of the current client.
  $matched = $wurflObj->getDeviceCapabilitiesFromAgent();
   
  // see if this client is on a wireless device (or if they can't be identified)
  if($wurflObj->getDeviceCapability("is_wireless_device")){
	  header('Location: '.SITE_URL.'mobile/');
  }
}

// load illegal char cleaner class
require_once(CLASSES_DIR.'illegal_char_man.php');
$man_ill_char = new man_ill_char;

// load shoppingcart manager
require(CLASSES_DIR.'shopping_cart_manage.php');
$shopping_cart_manage = new shopping_cart_manage;

// load page output class
require_once(CLASSES_DIR.'page_output.php');
$page_output = new page_output;

// writes session expiration warning message for logged in users
if (isset($_SESSION['advertiser_logged_in'])) {
	if ($_SESSION['advertiser_logged_in'] == 1) {
		$session_timeout_secs = SESSION_TIMEOUT * 60;
		$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>
		<script type=\"text/javascript\" src=\"".CONNECTION_TYPE."includes/js/ses_timer.js\"></script>";
	}
}

// Awesome Facebook Application
require_once LIBS_DIR.'facebook-php-sdk/src/facebook.php';

// load ratings module
require(CLASSES_DIR.'sections/ratings.php');
$adv_ratings = new adv_ratings;


// clear output buffer
ob_end_clean();
?>