<?PHP
$page_output .= '<div class="newAdvert" id="frm1">
					<div class="regular_list_head">
					<div class="rlh_left_corner"></div>
                  <div class="rlh_right_corner"></div>
                  <div class="header_txt">Company Information (*indicates required field)</div>
                </div>
                <div class="adv_listing_mid"></div>
				<table border="0" cellspacing="0" cellpadding="0" class="rnd_advertiser_form" align="center">';
$page_output .= '<tr><td colspan="2">
						<table align="center" border="0" cellspacing="0" cellpadding="0"><tr>
                <td align="left">
				<table>
				<tr><td>Company Name*<br/> '.$form_write->input_text('company_name',$company_name,20,120,1).'</td></tr>
				<tr>
				<td align="left"><table width="100%" align="left" border="0" cellspacing="0" cellpadding="0"><tr><td align="left">Phone Number*<br/> '.$form_write->input_text('contact_phone_left',$contact_phone_left,3,3,2).'-'.$form_write->input_text('contact_phone_center',$contact_phone_center,3,3,2).'-'.$form_write->input_text('contact_phone_right',$contact_phone_right,4,4,2).'</td>
				</tr>
				<tr>
				<td>FAX<br/> '.$form_write->input_text('contact_fax_left',$contact_fax_left,3,3,3).'-'.$form_write->input_text('contact_fax_center',$contact_fax_center,3,3,3).'-'.$form_write->input_text('contact_fax_right',$contact_fax_right,4,4,3).'</td></tr></table></td>
				</tr>
				<tr><td>Email Address*<br/> '.$form_write->input_text('email_address',$email_address,30,120,4).'</td></tr>
				<tr><td>Website URL (eg: www.cheaplocaldeals.com)<br/> '.$form_write->input_text('website',$website,30,160,5).'</td></tr>
				</table>
				</td><td align="left" valign="top">
				<table>
				<tr><td align="left">Hide Address From Listing '.$form_write->input_checkbox('hide_address',1,$hide_address).'</td></tr>
				<tr><td>Address 1*<br/> '.$form_write->input_text('address_1',$address_1,30,120,6).'</td></tr>
				<tr><td>Address 2<br/> '.$form_write->input_text('address_2',$address_2,30,120,7).'</td></tr>
				<tr><td><table border="0" cellspacing="0" cellpadding="0"><tr><td>City*<br/> '.$form_write->input_text('city',$city,30,100,8).'</td><td>State*<br/> <select tabindex="9" name="state" id="state_dd">'.gen_state_dd($state).'</select></td></tr></table></td></tr>
				<tr><td>Zip Code*<br/> '.$form_write->input_text('zip',$zip,5,5,10).'</td></tr>
				</table></td></tr>
               </table>
			   </td></tr>
			   </table>
			   </div>';

?>