<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

if ($_SESSION['customer_logged_in'] != 1) header("Location: ".SITE_SSL_URL."advertiser_admin/advertiser_login.deal");

// load customers information
$customer_info_table->get_db_vars($_SESSION['customer_id']);

echo '<table class="advertiser_form" cellspacing="0" cellpadding="5" align="center">
  <tr>
    <td>Your current account balance: <font color="#FF0000"><strong>$'.format_currency($customer_info_table->balance).'</strong></font></td>
  </tr>
</table>';

?>