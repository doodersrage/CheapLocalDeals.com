<?PHP

// prints forget_advertiser_password page
class forget_advertiser_password_pg {
	
	// draw forget_password form
	public function draw_forget_password_form() {
		
		$login_form = '<form name="login_form" method="post"><table class="frn_box" class="login_form" align="center">';
		$login_form .= '<tr><th class="frn_header" align="center" colspan="2">Forget Advertiser Password Form</th></tr>';
		$login_form .= '<tr><td class="frn_conbox" align="right">Username:</td><td><input name="username" type="text" size="30" maxlength="50" value="'.$_POST['username'].'"></td></tr>';
		$login_form .= '<tr><td class="frn_conbox" align="right">Contact Email Address:</td><td><input name="email" type="text" size="30" maxlength="120" value="'.$_POST['email'].'"></td></tr>';
		$login_form .= '<tr><td class="frn_conbox" align="center" colspan="2"><input class="submit_btn" name="submit" type="submit" value="Generate Password"></td></tr>';
		$login_form .= '</table></form>';
		
	return $login_form;
	}
	
}

?>