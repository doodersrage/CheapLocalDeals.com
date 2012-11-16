<?PHP
// load application header
require('../../includes/application_top.php');

$login_form = '<form name="login_form" method="post">
				<table align="center" action="https://www.cheaplocaldeals.com/advertiser_admin/forget_advertiser_password.deal">';
$login_form .= '<tr><td align="right">Username:</td><td><input name="username" type="text" size="30" maxlength="50" value="'.$_POST['username'].'"></td></tr>';
$login_form .= '<tr><td align="right">Contact Email Address:</td><td><input name="email" type="text" size="30" maxlength="120" value="'.$_POST['email'].'"></td></tr>';
$login_form .= '<tr><td align="center" colspan="2"><input class="submit_btn" name="submit" type="submit" value="Reset Password"></td></tr>';
$login_form .= '</table></form>';

echo create_warning_box($login_form,'Reset Advertiser Password');
?>