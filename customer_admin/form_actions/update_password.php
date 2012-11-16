<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// check if customer is logged in
if (!empty($_POST['password'])) {
	// check password length
	if (strlen($_POST['password']) < MINIMUM_PASSWORD_LENGTH)  {
		$page_output = '<center><font color="red"><strong>Password must be atleast '.MINIMUM_PASSWORD_LENGTH.' characters in length.</strong></font></center>';
	} else {			
		if ($_POST['password'] != $_POST['repassword'])  {
			$page_output = '<center><font color="red"><strong>Password fields do not match.</strong></font></center>';
		} else {
			// update password
			$customer_info_table->id = $_SESSION['customer_id'];
			$customer_info_table->password = $_POST['password'];
			$customer_info_table->change_password_check();
			$page_output = '<center><font color="red"><strong>Password Updated</strong></font></center>';
		}
	}
} else {
	$page_output = '<center><font color="red"><strong>You did not enter a password.</strong></font></center>';
}

if(!empty($page_output)) {
	$page_output = create_warning_box($page_output);
}

echo $page_output;

?>