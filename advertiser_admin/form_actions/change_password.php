<?PHP

// this document on submission will disable active certificates
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

if ($_POST['submit'] === 'Change Password') {
	
	// check if customer is logged in
	if (!empty($_POST['password'])) {
		// check password length
		if (strlen($_POST['password']) < MINIMUM_PASSWORD_LENGTH)  {
			$page_output .= '<center><font color="red"><strong>Password must be atleast '.MINIMUM_PASSWORD_LENGTH.' characters in length.</strong></font></center>';
		} else {
			if ($_POST['password'] != $_POST['repassword'])  {
				$page_output = '<center><font color="red"><strong>Password fields do not match.</strong></font></center>';
			} else {
				// update password
				$adv_info_tbl->id = $_SESSION['advertiser_id'];
				$adv_info_tbl->password = $_POST['password'];
				$adv_info_tbl->change_password_check();
							
				$page_output .= '<center><strong>Password has been changed.</strong></center>';
			}
		}
	} else {
		$page_output .= '<center><font color="red"><strong>You did not enter a password.</strong></font></center>';
	}
	
}

if(!empty($page_output)) {
	$page_output = create_warning_box($page_output);
}

echo $page_output;

?>