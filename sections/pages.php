<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// assign page script
$page_output->page_script = 'pages/pages.php';

// load page
$page_output->proc_template();

?>