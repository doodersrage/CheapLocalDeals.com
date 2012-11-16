<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

echo '<table class="advertiser_form"><tr><td>';

// load previous_orders page class
if (!class_exists('previous_orders_pg')) {
	require(CLASSES_DIR.'pages/previous_orders.php');
	$previous_orders_pg = new previous_orders_pg;
}

if ($_SESSION['customer_logged_in'] != 1) {
	header("Location: ".SITE_SSL_URL."customer_admin/account_login.deal");			
} else {
	// print previous orders page content
	$page_output = $previous_orders_pg->list_previous_orders();
}
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;
echo '</td></tr></table>';
?>