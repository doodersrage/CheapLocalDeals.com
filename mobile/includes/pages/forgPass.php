<?PHP
$page_output = '';
if ($_SESSION['customer_logged_in'] == 1) {
	$page_output = '<center>You appear to already be logged in.</center>';
} else {
	if ($_POST['submit'] === 'Generate Password') {
		
		// check if customer is logged in
		if (!empty($_POST['email'])) {
			
			if ($customer_info_table->user_forget_password_check() > 0) {				
				$page_output = '<center><font color="red"><strong>Your new password has been generated and emailed to you.</strong></font></center>';
			} else {
			}
			
		} else {
			$page_output = '<center><font color="red"><strong>In order to assign a new password you must first supply the email address assigned to the account.</strong></font></center>';
		}
	}
}

$login_form = '<div  id="custLoginFrm"><h1>Forget Password Form</h1>';
$login_form .= (!empty($warning) ? '<center><strong><font color="red">'.$warning.'</font></strong></center>' : '');
$login_form .= '<form name="login_form" method="post">';
$login_form .= '<p><a href="'.MOB_SSL_URL.'?action=createAcc"><font color="#0000FF"><u>Click here to create<br />
a new account</u></font></a></p>';
$login_form .= '<label>Email:</label<input name="email" type="text" size="30" maxlength="120" value="'.$_POST['email'].'">';
$login_form .= '<input class="submit_btn" name="submit" type="submit" value="Generate Password">';
$login_form .= '</form></div>';
$page_output .= $login_form;

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Forget Password Form';
$page_meta_description = 'Forget Password';
$page_meta_keywords = 'Forget Password';

// this script writes the content for the sites logoff page and handles search form submissions
$selTemp = 'pages.php';
$selHedMetTitle = $page_header_title;
$selHedMetDesc = $page_meta_description;
$selHedMetKW = $page_meta_keywords;
?>