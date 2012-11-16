<?PHP
// check if customer is logged in
if (!empty($_POST['password'])) {
	// check password length
	if (strlen($_POST['password']) < MINIMUM_PASSWORD_LENGTH)  {
		$error_message = '<center><font color="red"><strong>Password must be atleast '.MINIMUM_PASSWORD_LENGTH.' characters in length.</strong></font></center>';
	} else {			
		if ($_POST['password'] != $_POST['repassword'])  {
			$error_message = '<center><font color="red"><strong>Password fields do not match.</strong></font></center>';
		} else {
			// update password
			$customer_info_table->id = $_SESSION['customer_id'];
			$customer_info_table->password = $_POST['password'];
			$customer_info_table->change_password_check();
			$error_message = '<center><font color="red"><strong>Password Updated</strong></font></center>';
		}
	}
}

$login_form = '<div  id="custLoginFrm">
				<h1>Change Password</h1>';
$login_form .= (!empty($error_message) ? '<center><strong><font color="red">'.$error_message.'</font></strong></center>' : '');
$login_form .= '<form name="login_form" method="post">';
$login_form .= '<label>Password:</label><input name="password" id="password" type="password" size="30" maxlength="50" value="'.$_POST['password'].'">';
$login_form .= '<label>Confirm Password:</label><input name="repassword" id="repassword" type="password" size="30" maxlength="50" value="'.$_POST['repassword'].'">';
$login_form .= '<input class="submit_btn" id="" type="submit" name="Submit" value="Change Password" />';
$login_form .= '</form></div>';
$page_output = $login_form;

?>