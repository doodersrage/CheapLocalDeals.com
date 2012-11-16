<?PHP
// load application header
require('../../includes/application_top.php');

if (isset($_SESSION['advertiser_logged_in'])) {
  $page_output = '<center>You appear to already be logged in.</center>';
} else {
  $page_output = '<form name="login_form" method="post" action="https://www.cheaplocaldeals.com/account_login_page.deal">
<table align="center">
  <tbody>
    <tr>
      <td align="right">Username:</td>
      <td><input name="username" size="30" maxlength="50" value="" type="text"></td>
    </tr>
    <tr>
      <td align="right">Password:</td>
      <td><input name="password" size="30" maxlength="50" value="" type="password"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><a href="javascript: void(0)" onclick="ldAdvPassFrm()">Forget Password?</a>&nbsp;&nbsp;&nbsp;
        <input class="submit_btn" name="submit" value="Advertiser Sign In" type="submit"></td>
    </tr>
  </tbody>
</table>
</form>';
}

echo create_warning_box($page_output,'Advertiser Login');
?>
