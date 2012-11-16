<?PHP

// prints change_password page
class change_password_pg {
	
	// draw forget_password form
	public function draw_change_password_form() {
		
		$login_form = '<form id="update_password_frm" name="login_form" method="post">
						<table class="frn_box" style="width: 808px;">';
		$login_form .= '<tr><th class="frn_header" align="center" colspan="2">Change Password</th></tr>';
		$login_form .= '<tr><td align="right" class="frn_conbox">Password:</td><td align="left"><input name="password" id="password" type="password" size="30" maxlength="50" value="'.$_POST['password'].'"></td></tr>';
		$login_form .= '<tr><td align="right" class="frn_conbox">Re-Enter Password:</td><td align="left"><input name="repassword" id="repassword" type="password" size="30" maxlength="50" value="'.$_POST['repassword'].'"></td></tr>';
		$login_form .= '<tr><td align="center" colspan="2" class="frn_conbox"><input class="submit_btn" type="button" name="submit" value="Change Password" onclick="update_cust_pass_proc()" /></td></tr>';
		$login_form .= '</table></form>';
		
	return $login_form;
	}
	
}

?>