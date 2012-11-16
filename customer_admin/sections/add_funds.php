<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

if ($_SESSION['customer_logged_in'] != 1) header("Location: ".SITE_SSL_URL."advertiser_admin/advertiser_login.deal");

echo '<table class="advertiser_form" border="0" cellspacing="0" cellpadding="5" align="center">
  <tr>
    <td>Enter a provided coupon code to increase your available balance.</td>
  </tr>
  <tr>
    <td><input name="coupon_code" id="coupon_code" type="text" size="10" maxlength="10" /></td>
  </tr>
  <tr>
    <td><input name="process_coupon" type="button" value="Process" onclick="proc_coupon()" /></td>
  </tr>
</table>
';

?>