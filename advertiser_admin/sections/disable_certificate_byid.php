<?PHP

// this document on submission will disable active certificates
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// load account login page class
if (!class_exists('disable_certificate_byid_pg')) {
	require(CLASSES_DIR.'pages/disable_certificate_byid.php');
	$disable_certificate_byid_pg = new disable_certificate_byid_pg;
}

if ($_SESSION['advertiser_logged_in'] != 1) {
	header("Location: ".SITE_SSL_URL."advertiser_admin/advertiser_login.deal");			
} else {
	$page_output = '<table class="advertiser_form"><tr><td>';
	$page_output .= $disable_certificate_byid_pg->draw_disable_certificate_byid_form();
	$page_output .= '</td></tr></table>';
}
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;

?>