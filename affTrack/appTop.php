<?PHP
// load config file
require_once('../includes/config.php');

if (!class_exists('DB')) {
  // connect to database server
  require_once(CLASSES_DIR.'database.php');
}

// include settings
require_once(INCLUDES_DIR.'settings.php');

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

// load email classes
require_once('Mail.php');
require_once('Mail/mime.php');

// include password functions
require_once(FUNCTIONS_DIR.'passwords.php');

// load table classes
require_once(CLASSES_DIR.'load_tables.php');

// load illegal char cleaner class
require_once(CLASSES_DIR.'illegal_char_man.php');
$man_ill_char = new man_ill_char;

// load common functions 
require('procs/common.php');

// check user session status
require_once('procs/sessChk.php');
?>
