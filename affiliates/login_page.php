<?PHP
// load application top
require('../includes/application_top.php');

if (!class_exists('admin_users_table')) {
	// include admin_users_table class
	require(CLASSES_DIR.'tables/admin_users.php');
	$admin_users_table = new admin_users_table;
}

// login user
if(!empty($_POST['username']) && !empty($_POST['password'])) {
	if($admin_users_table->user_login_check() > 0) {
		header("Location: ".SITE_AFFILIATE_SSL_URL);	
	} else {
		$error_message = '<div class="error">Entered login info does not appear valid.</div>';
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?PHP echo SITE_NAME_VAL; ?> Admin Login</title>
<style>
html,body {
height:100%;
}
.error {
background:#FF0000;
color:#FFFFFF;
font-weight:700;
font-size:16px;
padding:5px;
}
.login_form {
border:1px solid #999999;
}
.login_form td {
background:#F4F4F4;
}
.login_form th {
background:#333333;
font-weight:700;
font-size:14px;
color:#FFFFFF;
}
</style>
</head>

<body>
<table width="100%" height="100%">
<tr><td valign="center" height="100%">
<form name="login_form" method="post" action="">
<table align="center" class="login_form">
<tr>
  <th colspan="2" align="center"><?PHP echo $error_message; ?>Affiliate System Login </th>
  </tr>
<tr>
  <td align="right">Username:</td>
  <td><input name="username" type="text" maxlength="30" /></td>
</tr>
<tr><td align="right">Password:</td><td><input name="password" type="password" maxlength="30" /></td></tr>
<tr><td align="center" colspan="2"><input type="submit" name="Login" value="Login" /></td></tr>
</table>
</form>
</td></tr>
</table>
</body>

</html>
