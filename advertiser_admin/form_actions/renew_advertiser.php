<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// renew or do not renew advertiser level
if(isset($_POST['renew'])) {
	$adv_info_tbl->customer_level_renew = (int)$_POST['renew'];
	$adv_info_tbl->update_renewal_status();
}

?>