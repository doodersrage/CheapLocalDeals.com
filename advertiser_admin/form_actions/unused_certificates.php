<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// disable selected certificate
if (isset($_POST['disable'])) {
	$cert_odrs_tbl->enabled = 0;
	$cert_odrs_tbl->id = $_POST['disable'];
	$cert_odrs_tbl->disable();
}

?>