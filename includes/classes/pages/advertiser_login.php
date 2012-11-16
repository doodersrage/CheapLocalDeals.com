<?PHP

// prints advertiser login page
class advertiser_login_pg {
	
	// draw login form
	public function draw_login_form() {
		
		$login_form = '<form name="advert_login_form" method="post">';
		$login_form .= '<table class="advertiser_login" align="center"><tr><td>';
		$login_form .= '<table class="login_form" align="center">';
		$login_form .= '<tr><th align="center" colspan="2">Advertiser Login</th></tr>';
		$login_form .= '<tr><td align="right">Username:</td><td><input name="username" type="text" size="30" maxlength="50" value="'.(isset($_POST['submit']) ? $_POST['submit'] === 'Advertiser Sign In' ? $_POST['username'] : '' : '').'"></td></tr>';
		$login_form .= '<tr><td align="right">Password:</td><td><input name="password" type="password" size="30" maxlength="50" value="'.(isset($_POST['submit']) ? $_POST['submit'] === 'Advertiser Sign In' ? $_POST['password'] : '' : '').'"></td></tr>';
		$login_form .= '<tr><td align="right" colspan="2"><a href="'.SITE_SSL_URL.'advertiser_admin/forget_advertiser_password.deal">Forget Password?</a>&nbsp;&nbsp;&nbsp;<input class="submit_btn" name="submit" type="submit" value="Advertiser Sign In"></td></tr>';
		$login_form .= '</table>';
		$login_form .= '</td><td class="no_account">';
		$login_form .= '<table valin="center" class="cust_account_signup_bx">';
		$login_form .= '<tr><th align="center" colspan="2">Don\'t have an account?</th></tr>';
		$login_form .= '<tr><td><p><a href="'.SITE_SSL_URL.'new-advertiser/"><font color="#0000FF"><u>Create an account now</u></font></a></p></td></tr>';
		$login_form .= '</table>';
		$login_form .= '</td></tr></table>';
		$login_form .= '</form>';
		
	return $login_form;
	}
	
}

?>