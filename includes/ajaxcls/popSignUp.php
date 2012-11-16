<?PHP
// load application header
require('../../includes/application_top.php');

if ($_SESSION['customer_logged_in'] == 1) {
  $page_output = '<center>You appear to already be logged in.</center>';
} else {
	
  if(!empty($_COOKIE['email_address'])) $email_address = $_COOKIE['email_address'];
	
  $page_output = '<form name="cust_frm" method="post" action="https://www.cheaplocaldeals.com/customer_admin/create_account.deal">
			<table border="0" align="center" cellpadding="0" cellspacing="0">
			  <tr>
				<td align="center" ><a href="'.SITE_URL.'privacy-policy/" target="_blank" ><font size="2">Privacy Policy</font></a></td>
			  </tr>
			  <tr>
				<td align="center" ><table border="0" cellpadding="0">
				  <tr>
					<td valign="top"><table class="noborders" >
					  <tr>
						<td>Email Address:<span class="newuser_required">*</span><br />
  '.$form_write->input_text('email_address',$email_address,30,160,1,'email_address').' </td>
					  </tr>
					  <tr>
						<td>Password:<span class="newuser_required">*</span> 6 character or more<br />
						  '.$form_write->input_password('password',$password,20,50,2,'password').'</td>
					  </tr>
					  <tr>
						<td>Confirm Password:<span class="newuser_required">*</span> 6 character or more<br />
						  '.$form_write->input_password('confirm_password',$confirm_password,20,50,3,'confirm_password').'</td>
					  </tr>
					</table></td>
				  </tr>
				</table></td>
			  </tr>
			  <tr>
				<td align="center" ><input class="submit_btn" id="" type="submit" name="Submit" value="Create Account" /></td>
			  </tr>
			</table>
		  </form>
		  <hr/>
		  <table border="0" align="center" cellpadding="0" cellspacing="0">
		  	<tr>
				<td>Advertiser? <a href="http://www.cheaplocaldeals.com/new-advertiser/">Click here to sign-up!</a></td>
			</tr>
		  </table>';
}

echo create_warning_box($page_output,'Start Saving Today!');
?>