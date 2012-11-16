<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if advertiser not logged in redirect to login page
if ($_SESSION['advertiser_logged_in'] != 1) header("Location: ".SITE_SSL_URL."advertiser_admin/advertiser_login.deal");

// load customers information
$adv_info_tbl->get_db_vars($_SESSION['advertiser_id']);

$page_output = '';

$states_dd = '';
$sql_query = "SELECT
				state,
				acn
			 FROM
				states
			 ;";
$rows = $dbh->queryAll($sql_query);
foreach($rows as $cur_state) {
	$states_dd .= '<option value="'.$cur_state['acn'].'" '.($cur_state['acn'] == $adv_info_tbl->state ? 'selected="selected" ' : '').'>'.$cur_state['state'].'</option>';
}

$page_output .= '
<form name="address_update_frm" id="address_update_frm">
<table class="advertiser_form" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <th align="center" class="newuser_required">* required </th>
    </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top">Contact First Name:<span class="newuser_required">*</span><br />
          
          <input id="first_name" name="first_name" value="'.$adv_info_tbl->first_name.'" type="text" id="first_name" size="30" maxlength="100" />
          <br />
          Contact Last Name:<span class="newuser_required">*</span><br />
          <input id="last_name" name="last_name" value="'.$adv_info_tbl->last_name.'" type="text" id="last_name" size="30" maxlength="100" />          </td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Contact Address 1:<span class="newuser_required">*</span><br />
            <input id="address_1" name="address_1" value="'.$adv_info_tbl->address_1.'" type="text" id="address_1" size="30" maxlength="120" />        </td>
        <td>Contact Address 2:<br />
          <input id="address_2" name="address_2" value="'.$adv_info_tbl->address_2.'" type="text" id="address_2" size="30" maxlength="120" /></td>
        </tr>
    </table></td>
    </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>Contact City:<span class="newuser_required">*</span><br />
            <input id="city" name="city" value="'.$adv_info_tbl->city.'" type="text" id="city" size="30" maxlength="100" />        </td>
        <td>Contact State:<span class="newuser_required">*</span><br />
            <select name="state" id="state">
						'.$states_dd.'
                        </select>        </td>
        <td>Contact Zip:<span class="newuser_required">*</span><br />
            <input id="zip" name="zip" value="'.$adv_info_tbl->zip.'" type="text" id="zip" size="5" maxlength="5" />        </td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top">Contact Phone Number:<span class="newuser_required">*</span><br />
          
          <input id="phone_number" name="phone_number" value="'.$adv_info_tbl->phone_number.'" type="text" id="phone_number" size="15" maxlength="15" />          </td>
        <td valign="top">Contact Fax Number:<br />
          
          <input id="fax_number" name="fax_number" value="'.$adv_info_tbl->fax_number.'" type="text" id="fax_number" size="15" maxlength="15" />          </td>
        <td valign="top">Contact Email Address:<span class="newuser_required">*</span><br />
            
            <input id="email_address" name="email_address" value="'.$adv_info_tbl->email_address.'" type="text" id="email_address" size="30" maxlength="160" />
            
        </span></td>
      </tr>
    </table></td>
    </tr>
  <tr>
    <th>&nbsp;</th>
    </tr>';
	  
$page_output .= '<tr>
    <td align="center">
      <input class="submit_btn" type="button" onclick="update_address()" name="update_account" value="Update Account" />    </td>
    </tr>
</table>
</form>
';
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;
?>