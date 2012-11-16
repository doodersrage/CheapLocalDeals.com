<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');


// check if customer is logged in
if (!empty($_POST['email_address']) && !empty($_POST['password'])) {
	
	$customer_info_table->user_login_check();
	if ($_SESSION['customer_logged_in'] != 1) {
	  echo create_warning_box('Either your email address or password are invalid.');
	  require('signin_frm.php');	
	} else {
	  echo '<script type="text/javascript">window.location.reload(true);</script>';	
	}
	
} else {
	echo create_warning_box('You did not provide either a email address or a password.');
	require('signin_frm.php');
}

?>