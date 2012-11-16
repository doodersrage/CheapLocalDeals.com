<?PHP
// load application header
require('../../includes/application_top.php');

if ($_SESSION['customer_logged_in'] == 1 || isset($_SESSION['advertiser_logged_in'])) {
  $page_output = '<center>You appear to already be logged in.</center>';
} else {
  $page_output = '<script type="text/javascript" src="includes/js/login_ovr.js"></script>
  <table border="0" align="center" cellpadding="5" cellspacing="0">
	<tr>
	  <td valign="top" align="right">Returning Customer:</td>
	  <td valign="top"><a href="javascript: void(0)" onclick="ldUsrFrm()">Login</a></td>
	  <td valign="top"><a href="javascript: void(0)" onclick="ldSignUp()">Create Account</a></td>
	</tr>
	<tr>
	  <td valign="top" align="right">Returning Advertiser:</td>
	  <td valign="top"><a href="javascript: void(0)" onclick="ldAdvFrm()">Login</a></td>
	  <td valign="top"><a href="http://www.cheaplocaldeals.com/new-advertiser/">Create Account</a></td>
	</tr>
  </table>';
}

echo create_warning_box($page_output,'Select Account Action');
?>