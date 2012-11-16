<?PHP
ini_set("memory_limit","-1"); 

// start output buffer
ob_start();

// load config file
require_once($_SERVER['DOCUMENT_ROOT'].'/includes/config.php');
// load mobile specific config
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

// include password functions
require_once(FUNCTIONS_DIR.'passwords.php');

// load html table class
require_once(CLASSES_DIR.'table_master.php');
$table_master = new table_master;

// load browsers class
require_once(CLASSES_DIR.'browser.php');
$browser = new browser;

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

// load common mobile functions
require_once(MOB_FUNC.'common.php');

// load illegal char cleaner class
require_once(CLASSES_DIR.'illegal_char_man.php');
$man_ill_char = new man_ill_char;

// load shoppingcart manager
require(CLASSES_DIR.'shopping_cart_manage.php');
$shopping_cart_manage = new shopping_cart_manage;

// load mobile layout handler
require(MOB_CLASS.'layout.php');
$mobLayout = new mobLayout;

// load form fields handler
require_once(CLASSES_DIR.'form_writer.php');
$form_write = new forms_write;

// assign mobile url constant
define('CUR_MOB_URL',CONNECTION_TYPE.'mobile/');

// set site view status
if($_GET['browse']=='normal') {
	$_SESSION['browse'] = 'normal';
	header("Location: ".SITE_URL);
}

// clear output buffer
ob_end_clean();
?>