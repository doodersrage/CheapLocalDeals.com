<?PHP

// this document on submission will disable active certificates
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// enable/disable account
if(isset($_GET['disable'])) {
	$adv_info_tbl->id = $_SESSION['advertiser_id'];
	$adv_info_tbl->account_enabled = (int)$_GET['disable'];
	$adv_info_tbl->update_enable_status();
}

?>