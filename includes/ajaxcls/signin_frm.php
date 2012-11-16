<?PHP

  $reviews_form .= '<tr><td>
  <table border="0" cellspacing="0" cellpadding="5" align="center">
  <tr>
    <td align="right">E-Mail:</td>
    <td><input type="text" name="rev_email_address" id="rev_username" /></td>
  </tr>
  <tr>
    <td align="right">Password:</td>
    <td><input type="password" name="rev_password" id="rev_password" /></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input class="submit_btn" type="submit" name="button" id="button" value="Login" onclick="user_login('.(int)$_POST['loc_id'].','.(int)$_POST['alt_loc_id'].');" /><hr />
    <a href="https://www.cheaplocaldeals.com/customer_admin/create_account.deal">Create an Account </a></td>
  </tr>
</table>
  </td></tr>';

echo $reviews_form;
?>