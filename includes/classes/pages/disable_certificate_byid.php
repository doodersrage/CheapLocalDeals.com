<?PHP

// prints disable_certificate_byid page
class disable_certificate_byid_pg {
	
	// draw forget_password form
	public function draw_disable_certificate_byid_form() {
		
		$login_form = '<form name="login_form"><table class="frn_box" style="width: 100%;" align="center">';
		$login_form .= '<tr><th class="frn_header" align="center" colspan="2">Disable certificate by id</th></tr>';
		$login_form .= '<tr><td colspan="2" align="center">Certificate ID: <input name="certificate_id" id="disable_certificate_id" type="text" size="30" maxlength="50" value="'.$_POST['certificate_id'].'"></td></tr>';
		$login_form .= '<tr><td class="frn_conbox" align="center" colspan="2"><input class="submit_btn" name="submit"  type="button" onclick="disable_cert_by_id()" value="Disable Certificate"></td></tr>';
		$login_form .= '</table></form>';
		
	return $login_form;
	}
	
}

?>