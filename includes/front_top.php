<?PHP

// start output buffer
ob_start();

// checkes login status and will log user out if proven invalid
// check advertiser login status
if (isset($_SESSION['advertiser_logged_in'])) {
	if ($_SESSION['advertiser_logged_in'] == 1) {
		if ($adv_info_tbl->user_login_session_check() == 0) {
			unset($_SESSION['advertiser_logged_in']);
			unset($_SESSION['advertiser_id']);
			unset($_SESSION['approved']);
			unset($_SESSION['customer_level']);
			unset($_SESSION['allow_multiple_logins']);
		}
	}
}
// check customer login status
if (isset($_SESSION['customer_logged_in'])) {
	if ($_SESSION['customer_logged_in'] == 1) {
		if ($customer_info_table->user_login_session_check() == 0) {
			unset($_SESSION['customer_logged_in']);
			unset($_SESSION['customer_id']);
		}
	}
}

//// set page cache settings for non-secure pages
//if (isset($_SESSION['advertiser_logged_in']) || isset($_SESSION['customer_logged_in'])) {
//} else {
//  $expires = 60*60*24*14;
//  header("Pragma: public");
//  header("Cache-Control: maxage=".$expires);
//  header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
//}

//// update page hits table
//update_page_hits();

// check for promo code value
if(isset($_POST['promo_code'])) $_SESSION['promo_code'] = $_POST['promo_code'];

// clear output buffer
ob_end_clean();

?>