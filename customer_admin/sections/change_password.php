<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');
echo '<table class="advertiser_form"><tr><td>';

// load account login page class
if (!class_exists('change_password_pg')) {
	require(CLASSES_DIR.'pages/change_password.php');
	$change_password_pg = new change_password_pg;
}

if ($_SESSION['customer_logged_in'] != 1) {
	header("Location: ".SITE_SSL_URL."customer_admin/account_login.deal");			
} else {
	$page_output = $change_password_pg->draw_change_password_form();
}
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;
echo '</td></tr></table>';
?>