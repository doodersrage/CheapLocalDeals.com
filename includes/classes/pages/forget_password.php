<?PHP

// prints forget_password page
class forget_password_pg {
	
	// draw forget_password form
	public function draw_forget_password_form() {
		
		$login_form = '<form name="login_form" method="post"><table class="frn_box" align="center">';
		$login_form .= '<tr><th class="frn_header" align="center" colspan="2">Forget Password Form</th></tr>';
		$login_form .= '<tr><td class="frn_conbox" align="right">Email Address:</td><td><input name="email" type="text" size="30" maxlength="120" value="'.$_POST['email'].'"></td></tr>';
		$login_form .= '<tr><td align="center" colspan="2"><input class="submit_btn" name="submit" type="submit" value="Generate Password"></td></tr>';
		$login_form .= '</table></form>';
		
	return $login_form;
	}
	
}

?>