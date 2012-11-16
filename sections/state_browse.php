<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if HTTPS page load request is made redirect to HTTP
check_request_type();

// assign page script
$page_output->page_script = 'pages/state_browse.php';

// load page
$page_output->proc_template();
?>