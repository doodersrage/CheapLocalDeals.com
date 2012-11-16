<?PHP
// load application header
require('../../includes/application_top.php');

$login_form = '<form name="login_form" method="post" action="https://www.cheaplocaldeals.com/customer_admin/forget_password.deal">
				<table align="center">';
$login_form .= '<tr><td align="right">Email Address:</td><td><input name="email" type="text" size="30" maxlength="120" value="'.$_POST['email'].'"></td></tr>';
$login_form .= '<tr><td align="center" colspan="2"><input class="submit_btn" name="submit" type="submit" value="Reset Password"></td></tr>';
$login_form .= '</table></form>';

echo create_warning_box($login_form,'Reset Customer Password');
?>