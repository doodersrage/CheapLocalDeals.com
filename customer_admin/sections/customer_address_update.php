<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

if ($_SESSION['customer_logged_in'] != 1) header("Location: ".SITE_SSL_URL."advertiser_admin/advertiser_login.deal");

// load customers information
$customer_info_table->get_db_vars($_SESSION['customer_id']);
	
$page_output = '';

$states_dd = gen_state_dd($customer_info_table->state);

$page_output .= '<center><strong>'.$error_message.'</strong></center>
<form id="update_address_frm" name="form1" method="post" action="">
<table class="advertiser_form" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <th align="center" class="newuser_required">Update Billing Address</th>
    </tr>
  <tr>
	<td align="center" colspan="2">You\'re Almost done. So that you send you special coupons please enter your contact information below</td>
  </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top">First Name:<span class="newuser_required">*</span><br />
          
          <input name="first_name" value="'.$customer_info_table->first_name.'" type="text" id="first_name" size="30" maxlength="100" />
          <br />
          Last Name:<span class="newuser_required">*</span><br />
          <input name="last_name" value="'.$customer_info_table->last_name.'" type="text" id="last_name" size="30" maxlength="100" />          </td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Address 1:<span class="newuser_required">*</span><br />
            <input name="address_1" value="'.$customer_info_table->address_1.'" type="text" id="address_1" size="30" maxlength="120" />        </td>
        <td>Address 2:<br />
          <input name="address_2" value="'.$customer_info_table->address_2.'" type="text" id="address_2" size="30" maxlength="120" /></td>
        </tr>
    </table></td>
    </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>City:<span class="newuser_required">*</span><br />
            <input name="city" value="'.$customer_info_table->city.'" type="text" id="city" size="30" maxlength="100" />        </td>
        <td>State:<span class="newuser_required">*</span><br />
            <select name="state" id="state">
						'.$states_dd.'
                        </select>        </td>
        <td>Zip:<span class="newuser_required">*</span><br />
            <input name="zip" value="'.$customer_info_table->zip.'" type="text" id="zip" size="5" maxlength="5" />        </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top">Phone Number:<span class="newuser_required">*</span><br />
          
          <input name="phone_number" value="'.$customer_info_table->phone_number.'" type="text" id="phone_number" size="15" maxlength="15" />          </td>
        <td valign="top">Email Address:<span class="newuser_required">*</span><br />
            
            <input name="email_address" value="'.$customer_info_table->email_address.'" type="text" id="email_address" size="30" maxlength="160" />
            
        </span></td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td align="center">
      <input class="submit_btn" type="button" name="submit" value="Update Billing Address" onclick="update_address_proc()" />    </td>
    </tr>
</table>
</form>';
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;
?>