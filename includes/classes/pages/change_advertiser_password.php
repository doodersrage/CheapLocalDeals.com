<?PHP

// prints change_advertiser_password page
class change_advertiser_password_pg {
	
	// draw forget_password form
	public function draw_change_password_form() {
		
		$login_form = '<form name="login_form"><table class="frn_box" style="width: 808px;">';
		$login_form .= '<tr><th class="frn_header" align="center" colspan="2">Change Password</th></tr>';
		$login_form .= '<tr><td align="right" class="frn_conbox">Password:</td><td><input name="password" id="password" type="password" size="30" maxlength="50" value="'.$_POST['password'].'"></td></tr>';
		$login_form .= '<tr><td align="right" class="frn_conbox">Re-Enter Password:</td><td><input name="repassword" id="repassword" type="repassword" size="30" maxlength="50" value="'.$_POST['repassword'].'"></td></tr>';
		$login_form .= '<tr><td class="frn_conbox" align="center" colspan="2"><input class="submit_btn" name="submit"  type="button" onclick="change_password()" value="Change Password"></td></tr>';
		$login_form .= '</table></form>';
		
	return $login_form;
	}
	
}

?>