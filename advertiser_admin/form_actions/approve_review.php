<?PHP

// this document on submission will disable active certificates
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// enable/disable account
if(isset($_GET['review_id'])) {
	$adv_rvws_tbl->id = $_POST['review_id'];
	$adv_rvws_tbl->advertiser_id = $_SESSION['advertiser_id'];
	$adv_rvws_tbl->approved = 1;
	$adv_rvws_tbl->approve();
}

?>