<?PHP
// load application header
require('../../includes/application_top.php');

if ($_SESSION['customer_logged_in'] == 1) {
  $page_output = '<center>You appear to already be logged in.</center>';
} else {
  $page_output = '<form name="login_form" method="post" action="https://www.cheaplocaldeals.com/account_login_page.deal">
  <table align="center">
    <tbody>
      <tr>
        <th colspan="2" align="center">Existing Customers</th>
      </tr>
      <tr>
        <td align="right">Email:</td>
        <td><input name="email_address" size="30" maxlength="50" value="" type="text"></td>
      </tr>
      <tr>
        <td align="right">Password:</td>
        <td><input name="password" size="30" maxlength="50" value="" type="password"></td>
      </tr>
      <tr>
        <td colspan="2" align="center"><a href="javascript: void(0)" onclick="ldCustPassFrm()">Forget Password?</a>&nbsp;&nbsp;&nbsp;
          <input class="submit_btn" name="submit" value="Customer Sign In" type="submit"></td>
      </tr>
    </tbody>
  </table>
</form>';
}

echo create_warning_box($page_output,'Customer Login');
?>
