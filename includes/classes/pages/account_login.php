<?PHP

// prints account login page
class account_login_pg {
	
	// draw login form
	public function draw_login_form() {
		
		$login_form = '<form name="login_form" method="post">';
		$login_form .= '<table class="advertiser_login" align="center"><tr><td class="create_acc_text">';
		$login_form .= '<table class="cust_account_signup_bx">';
		$login_form .= '<tr><th align="center" colspan="2">It\'s Quick and Easy!</th></tr>';
		$login_form .= '<tr><td align="center"><p><a href="'.SITE_SSL_URL.'customer_admin/create_account.deal"><font color="#0000FF"><u>Click here to create<br />
 a new account</u></font></a></p></td></tr>';
		$login_form .= '</table>';
		$login_form .= '</td><td class="no_account">';
		$login_form .= '<table class="login_form" align="center">';
		$login_form .= '<tr><th align="center" colspan="2">Existing Customers</th></tr>';
		$login_form .= '<tr><td align="right">Email:</td><td><input name="email_address" type="text" size="30" maxlength="50" value="'.(isset($_POST['submit']) ? $_POST['submit'] === 'Customer Sign In' ? $_POST['email_address'] : '' : '').'"></td></tr>';
		$login_form .= '<tr><td align="right">Password:</td><td><input name="password" type="password" size="30" maxlength="50" value="'.(isset($_POST['submit']) ? $_POST['submit'] === 'Customer Sign In' ? $_POST['password'] : '' : '').'"></td></tr>';
		$login_form .= '<tr><td align="right" colspan="2"><a href="'.SITE_SSL_URL.'customer_admin/forget_password.deal">Forget Password?</a>&nbsp;&nbsp;&nbsp;<input class="submit_btn" name="submit" type="submit" value="Customer Sign In"></td></tr>';
		$login_form .= '</table>';
		$login_form .= '</td></tr></table>';
		$login_form .= '</form>';
		
	return $login_form;
	}
	
}

?>