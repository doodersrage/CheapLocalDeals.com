<?PHP
// load application top
require('../../includes/application_top.php');

if(empty($_POST['id'])) {
	
$form_op = '<table border="0" cellpadding="3">
      <tr>
          <td align="right" valign="top">Enabled</td>
          <td><input name="alt_location_enabled" type="checkbox" id="alt_location_enabled" value="1"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Location Name</td>
          <td><input type="text" name="alt_location_name" id="alt_location_name"></td>
        </tr>
<!--        <tr>
          <td align="right" valign="top">Hours of Operation</td>
          <td>&nbsp;</td>
        </tr>-->
        <tr>
          <td align="right" valign="top">Products and Services</td>
          <td><textarea name="alt_location_prods_servs" id="alt_location_prods_servs" cols="45" rows="3"></textarea></td>
        </tr>
        <tr>
          <td align="right" valign="top">Description</td>
          <td><textarea name="alt_location_description" id="alt_location_description" cols="45" rows="6"></textarea></td>
        </tr>
        <tr>
          <td align="right" valign="top">Website</td>
          <td><input type="text" name="alt_location_website" id="alt_location_website"></td>
        </tr>
<!--        <tr>
          <td align="right" valign="top">Payment Options</td>
          <td>&nbsp;</td>
        </tr>-->
        <tr>
          <td align="right" valign="top">Hide Address</td>
          <td><input type="checkbox" name="alt_location_hide_address" id="alt_location_hide_address" value="1"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Address 1</td>
          <td><input type="text" name="alt_location_address1" id="alt_location_address1" size="50" maxlength="120"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Address 2</td>
          <td><input type="text" name="alt_location_address2" id="alt_location_address2" size="50" maxlength="120"></td>
        </tr>
        <tr>
          <td align="right" valign="top">City</td>
          <td><input type="text" name="alt_location_city" id="alt_location_city" size="50" maxlength="100"></td>
        </tr>
        <tr>
          <td align="right" valign="top">State</td>
          <td>';
		  
        $form_op .= '<select name="alt_location_state" id="alt_location_state">'.gen_state_dd('').'</select>
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">Zip</td>
          <td><input type="text" name="alt_location_zip" id="alt_location_zip" size="15" maxlength="15"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Phone Number</td>
          <td><input type="text" name="alt_location_phone" id="alt_location_phone" size="15" maxlength="15"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Fax Number</td>
          <td><input type="text" name="alt_location_fax" id="alt_location_fax" size="15" maxlength="15"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Email Address</td>
          <td><input type="text" name="alt_location_email" id="alt_location_email" size="50" maxlength="160"></td>
        </tr>
        <tr>
          <td colspan="2" align="center" valign="top"><input name="alt_loc_type" id="alt_loc_type" type="hidden" value="new"><input name="alt_id" id="alt_id" type="hidden" value=""><input type="button" name="alt_loc_save" id="alt_loc_save" value="Save" onclick="save_alt_lox()"></td>
          </tr>
      </table>';
} else {

$adv_alt_loc_tbl->get_db_vars($_POST['id']);

$form_op = '<table border="0" cellpadding="3">
      <tr>
          <td align="right" valign="top">Enabled</td>
          <td><input name="alt_location_enabled" type="checkbox" id="alt_location_enabled" value="1" '.($adv_alt_loc_tbl->enabled == 1 ? 'checked' : '').' ></td>
        </tr>
        <tr>
          <td align="right" valign="top">Location Name</td>
          <td><input type="text" name="alt_location_name" id="alt_location_name" value="'.$adv_alt_loc_tbl->location_name.'"></td>
        </tr>
<!--        <tr>
          <td align="right" valign="top">Hours of Operation</td>
          <td>&nbsp;</td>
        </tr>-->
        <tr>
          <td align="right" valign="top">Products and Services</td>
          <td><textarea name="alt_location_prods_servs" id="alt_location_prods_servs" cols="45" rows="3">'.$adv_alt_loc_tbl->products_services.'</textarea></td>
        </tr>
        <tr>
          <td align="right" valign="top">Description</td>
          <td><textarea name="alt_location_description" id="alt_location_description" cols="45" rows="6">'.$adv_alt_loc_tbl->description.'</textarea></td>
        </tr>
        <tr>
          <td align="right" valign="top">Website</td>
          <td><input type="text" name="alt_location_website" id="alt_location_website" value="'.$adv_alt_loc_tbl->website.'"></td>
        </tr>
<!--        <tr>
          <td align="right" valign="top">Payment Options</td>
          <td>&nbsp;</td>
        </tr>-->
        <tr>
          <td align="right" valign="top">Hide Address</td>
          <td><input type="checkbox" name="alt_location_hide_address" id="alt_location_hide_address" value="1" '.($adv_alt_loc_tbl->hide_address == 1 ? 'checked' : '').' ></td>
        </tr>
        <tr>
          <td align="right" valign="top">Address 1</td>
          <td><input type="text" name="alt_location_address1" id="alt_location_address1" value="'.$adv_alt_loc_tbl->address_1.'" size="50" maxlength="120"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Address 2</td>
          <td><input type="text" name="alt_location_address2" id="alt_location_address2" value="'.$adv_alt_loc_tbl->address_2.'" size="50" maxlength="120"></td>
        </tr>
        <tr>
          <td align="right" valign="top">City</td>
          <td><input type="text" name="alt_location_city" id="alt_location_city" value="'.$adv_alt_loc_tbl->city.'" size="50" maxlength="100"></td>
        </tr>
        <tr>
          <td align="right" valign="top">State</td>
          <td>';
		  
        $form_op .= '<select name="alt_location_state" id="alt_location_state">'.gen_state_dd($adv_alt_loc_tbl->state).'</select>
          </td>
        </tr>
        <tr>
          <td align="right" valign="top">Zip</td>
          <td><input type="text" name="alt_location_zip" id="alt_location_zip" size="15" maxlength="15" value="'.$adv_alt_loc_tbl->zip.'"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Phone Number</td>
          <td><input type="text" name="alt_location_phone" id="alt_location_phone" value="'.$adv_alt_loc_tbl->phone_number.'" size="15" maxlength="15"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Fax Number</td>
          <td><input type="text" name="alt_location_fax" id="alt_location_fax" value="'.$adv_alt_loc_tbl->fax_number.'" size="15" maxlength="15"></td>
        </tr>
        <tr>
          <td align="right" valign="top">Email Address</td>
          <td><input type="text" name="alt_location_email" id="alt_location_email" value="'.$adv_alt_loc_tbl->email_address.'" size="50" maxlength="160"></td>
        </tr>
        <tr>
          <td colspan="2" align="center" valign="top"><input name="alt_loc_type" id="alt_loc_type" type="hidden" value="new"><input name="alt_id" id="alt_id" type="hidden"  value="'.$adv_alt_loc_tbl->id.'"><input type="button" name="alt_loc_save" id="alt_loc_save" value="Save" onclick="save_alt_lox()"></td>
          </tr>
      </table>';

}

echo $form_op;
?>