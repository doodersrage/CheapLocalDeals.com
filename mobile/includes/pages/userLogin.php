<?PHP
if ($_POST['submit'] === 'Customer Sign In') {
  if (!empty($_POST['email_address']) && !empty($_POST['password'])) {
	
	$customer_info_table->user_login_check();
	if ($_SESSION['customer_logged_in'] != 1) {
	  $warning = 'Either your e-mail address or password are invalid.';
	} else {
	  if ($_GET['mode'] == 'review') {
		if(!empty($_SESSION['previous_page'])) {
		  header("Location: ".$_SESSION['previous_page']);
		} else {
		  mob_cust_log_redir();
		}
	  } else {
		mob_cust_log_redir();
	  }
	}
  } else {
	$warning = 'You did not provide either a email address or a password.';
  }
}

$login_form = '<div  id="custLoginFrm"><h1>User Login</h1>';
$login_form .= (!empty($warning) ? '<center><strong><font color="red">'.$warning.'</font></strong></center>' : '');
$login_form .= '<form name="login_form" method="post">';
$login_form .= '<p><a href="'.MOB_SSL_URL.'?action=createAcc"><font color="#0000FF"><u>Click here to create<br />
a new account</u></font></a></p><p><a href="'.MOB_SSL_URL.'?action=forgPass">Forget Password?</a></p>';
$login_form .= '<label>Email:</label><input name="email_address" type="text" size="30" maxlength="50" value="'.(isset($_POST['submit']) ? $_POST['submit'] === 'Customer Sign In' ? $_POST['email_address'] : '' : '').'">';
$login_form .= '<label>Password:</label><input name="password" type="password" size="30" maxlength="50" value="'.(isset($_POST['submit']) ? $_POST['submit'] === 'Customer Sign In' ? $_POST['password'] : '' : '').'">';
$login_form .= '<input class="submit_btn" name="submit" type="submit" value="Customer Sign In">';
$login_form .= '</form></div>';
$page_output = $login_form;

// set page header -- only assign for static header data
$page_header_title = 'Cheap Local Deals Mobile - Account Login';
$page_meta_description = 'Account Login';
$page_meta_keywords = 'Account Login';

// this script writes the content for the sites logoff page and handles search form submissions
$selTemp = 'pages.php';
$selHedMetTitle = $page_header_title;
$selHedMetDesc = $page_meta_description;
$selHedMetKW = $page_meta_keywords;
?>