<?PHP

// retail customers management class
class retail_customers_frm {
	private $last_child_id;
	
	function apply_review_changes() {
	  if(is_array($_POST['approve_review'])) {
		foreach($_POST['approve_review'] as $approve_rev) {
		  $stmt = $dbh->prepare("UPDATE advertiser_reviews SET approved = 1 WHERE id = '".$approve_rev."';");
		  $stmt->execute();
		}
	  }
	  if(is_array($_POST['delete_review'])) {
		foreach($_POST['delete_review'] as $delete_rev) {
		  $stmt = $dbh->prepare("DELETE FROM advertiser_reviews WHERE id = '".$delete_rev."';");
		  $stmt->execute();
		}
	  }
	}
	
	function change_approval() {
		global $dbh;
	  if(is_array($_POST['change_approve_advertiser'])) {
		foreach($_POST['change_approve_advertiser'] as $approve_adv) {
		  $stmt = $dbh->prepare("UPDATE advertiser_info SET update_approval = 1 WHERE id = '".$approve_adv."';");
		  $stmt->execute();
		}
	  }
	}
	
	function approve_advertisers() {
		global $dbh;
	  if(is_array($_POST['approve_advertiser'])) {
		foreach($_POST['approve_advertiser'] as $approve_adv) {
		  $stmt = $dbh->prepare("UPDATE advertiser_info SET approved = 1, approval_date = '".date("Y-m-d")."' WHERE id = '".$approve_adv."';");
		  $stmt->execute();
		}
	  }
	}
	
	function update_staff_picks() {
		global $dbh;
	  if(is_array($_POST['staff_picks'])) {
		foreach($_POST['staff_picks'] as $staff_pick) {
		  $stmt = $dbh->prepare("UPDATE advertiser_info SET pick = 1 WHERE id = '".$staff_pick."';");
		  $stmt->execute();
		}
	  }	
	}
	
	function generate_csv() {
		global $adv_info_tbl, $adv_lvls_tbl, $dbh;

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");;
		header("Content-Disposition: attachment;filename=".strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', date("Y-m-d")))."-advertisers.xls "); 
		header("Content-Transfer-Encoding: binary ");

		// header for spreadsheet
		$headers = array('Signed Up','Company Name','Contact Person','Advertiser Level','Address','Address2','City','State','Zip','Phone','E-mail');
		
		// build header row
		$xls_output = implode(T,$headers).LB;

		$sql_query = "SELECT
						id
					 FROM
						advertiser_info
					 ORDER BY date_created desc
					 ;";
		
		$rows = $dbh->queryAll($sql_query);
		
		foreach ($rows as $pages) {
			// reset row output
			$cur_row = array();
			$adv_info_tbl->get_db_vars($pages['id']);
			
			$cur_row[] = $adv_info_tbl->date_created;
			$cur_row[] = $adv_info_tbl->company_name;
			$cur_row[] = $adv_info_tbl->first_name . ' ' . $adv_info_tbl->last_name;
			$adv_lvls_tbl->get_db_vars($adv_info_tbl->customer_level);
			$cur_row[] = $adv_lvls_tbl->level_name;
			$cur_row[] = $adv_info_tbl->address_1;
			$cur_row[] = $adv_info_tbl->address_2;
			$cur_row[] = $adv_info_tbl->city;
			$cur_row[] = $adv_info_tbl->state;
			$cur_row[] = $adv_info_tbl->zip;
			$cur_row[] = $adv_info_tbl->phone_number;
			$cur_row[] = $adv_info_tbl->email_address;
			
			$xls_output .= implode(T,$cur_row).LB;
		}
		
		echo $xls_output;
		
		die();

	}
	
	// delete advertiser and move them to backup table
	function delete_move_advert_backup() {
		global $dbh, $adv_info_tbl, $adv_info_bu_tbl;
	
		// deleted selected items
		if(isset($_POST['delete_advertiser'])) {
			if(is_array($_POST['delete_advertiser'])) {
				foreach($_POST['delete_advertiser'] as $id => $del_advert) {
					// pull current advertiser info
					$adv_info_tbl->get_db_vars($del_advert);
					
					// transfer advert values to advert backup table
					$adv_info_bu_tbl->id = $adv_info_tbl->id;
					$adv_info_bu_tbl->approved = $adv_info_tbl->approved;
					$adv_info_bu_tbl->approval_date = $adv_info_tbl->approval_date;
					$adv_info_bu_tbl->update_approval = $adv_info_tbl->update_approval;
					$adv_info_bu_tbl->company_name = $adv_info_tbl->company_name;
					$adv_info_bu_tbl->customer_description = $adv_info_tbl->customer_description;
					$adv_info_bu_tbl->longitude = $adv_info_tbl->longitude;
					$adv_info_bu_tbl->latitude = $adv_info_tbl->latitude;
					$adv_info_bu_tbl->username = $adv_info_tbl->username;
					$adv_info_bu_tbl->password = $adv_info_tbl->password;
					$adv_info_bu_tbl->hours_operation = serialize($adv_info_tbl->hours_operation);
					$adv_info_bu_tbl->customer_level = $adv_info_tbl->customer_level;
					$adv_info_bu_tbl->customer_level_exp = $adv_info_tbl->customer_level_exp;
					$adv_info_bu_tbl->customer_level_renewal_date = $adv_info_tbl->customer_level_renewal_date;
					$adv_info_bu_tbl->website = $adv_info_tbl->website;
					$adv_info_bu_tbl->category = $adv_info_tbl->category;
					$adv_info_bu_tbl->bbb_member = $adv_info_tbl->bbb_member;
					$adv_info_bu_tbl->link_partner = $adv_info_tbl->link_partner;
					$adv_info_bu_tbl->affiliate_code = $adv_info_tbl->affiliate_code;
					$adv_info_bu_tbl->link_affiliate_code = $adv_info_tbl->link_affiliate_code;
					$adv_info_bu_tbl->products_services = $adv_info_tbl->products_services;
					$adv_info_bu_tbl->payment_options = serialize($adv_info_tbl->payment_options);
					$adv_info_bu_tbl->payment_method = $adv_info_tbl->payment_method;
					$adv_info_bu_tbl->certificate_levels = serialize($adv_info_tbl->certificate_levels);
					$adv_info_bu_tbl->certificate_requirements = serialize($adv_info_tbl->certificate_requirements);
					$adv_info_bu_tbl->credit_card_type = $adv_info_tbl->credit_card_type;
					$adv_info_bu_tbl->cc_number = $adv_info_tbl->cc_number;
					$adv_info_bu_tbl->cvv = $adv_info_tbl->cvv;
					$adv_info_bu_tbl->cc_exp = $adv_info_tbl->cc_exp;
					$adv_info_bu_tbl->bank_name = $adv_info_tbl->bank_name;
					$adv_info_bu_tbl->bank_state = $adv_info_tbl->bank_state;
					$adv_info_bu_tbl->drivers_license_num = $adv_info_tbl->drivers_license_num;
					$adv_info_bu_tbl->drivers_license_state = $adv_info_tbl->drivers_license_state;
					$adv_info_bu_tbl->check_routing_num = $adv_info_tbl->check_routing_num;
					$adv_info_bu_tbl->check_account_num = $adv_info_tbl->check_account_num;
					$adv_info_bu_tbl->hide_address = $adv_info_tbl->hide_address;
					$adv_info_bu_tbl->first_name = $adv_info_tbl->first_name;
					$adv_info_bu_tbl->last_name = $adv_info_tbl->last_name;
					$adv_info_bu_tbl->address_1 = $adv_info_tbl->address_1;
					$adv_info_bu_tbl->address_2 = $adv_info_tbl->address_2;
					$adv_info_bu_tbl->city = $adv_info_tbl->city;
					$adv_info_bu_tbl->state = $adv_info_tbl->state;
					$adv_info_bu_tbl->zip = $adv_info_tbl->zip;
					$adv_info_bu_tbl->phone_number = $adv_info_tbl->phone_number;
					$adv_info_bu_tbl->fax_number = $adv_info_tbl->fax_number;
					$adv_info_bu_tbl->email_address = $adv_info_tbl->email_address;
					$adv_info_bu_tbl->account_enabled = $adv_info_tbl->account_enabled;
					$adv_info_bu_tbl->image = $adv_info_tbl->image;
					$adv_info_bu_tbl->last_ip = $adv_info_tbl->last_ip;
					$adv_info_bu_tbl->last_login = $adv_info_tbl->last_login;
					$adv_info_bu_tbl->last_session_id = $adv_info_tbl->last_session_id;
					$adv_info_bu_tbl->allow_multiple_logins = $adv_info_tbl->allow_multiple_logins;
					$adv_info_bu_tbl->authorization_code = $adv_info_tbl->authorization_code;
					$adv_info_bu_tbl->email_authorized = $adv_info_tbl->email_authorized;
					
					// insert new backup record
					$adv_info_bu_tbl->insert();
					
					// delete advertiser from advertiser info table
					$stmt = $dbh->prepare("DELETE FROM advertiser_info WHERE id = '".$del_advert."';");
					$stmt->execute();
					
					// transfer selected categories for deleted advertiser
					$sql_query = "SELECT
									id,
									advertiser_id,
									category_id
								 FROM
									advertiser_categories
								 WHERE
									advertiser_id = ?
								;";
					
					$values2 = array(
									$del_advert
									);
	
					$stmt2 = $dbh->prepare($sql_query);					 
					$result = $stmt2->execute($values2);
					
					while($rows_check = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
						$sql_query = "INSERT INTO advertiser_categories_backup (category_id,advertiser_id) VALUES (?,?);";
						$update_vals3 = array(
											$rows_check['category_id'],
											$rows_check['advertiser_id']
											);
											
						$stmt3 = $dbh->prepare($sql_query);
						$stmt3->execute($update_vals3);						
					}
					
					// delete advertiser categories 
					$stmt = $dbh->prepare("DELETE FROM advertiser_categories WHERE advertiser_id = '".$del_advert."';");
					$stmt->execute();
					
				}
			}
		}
		
	}
	
	// load add retail customers page
	function add($message = '') {
		
		$add_retail_customer = open_table_form('Add New Advertiser','add_retail_customer',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=addcheck','post',$message);
		$add_retail_customer .= $this->form();
		$add_retail_customer .= close_table_form();
		
	return $add_retail_customer;
	}
	
	// load add retail customers page
	function edit($message = '') {
		
		$add_retail_customer = open_table_form('Edit Advertiser','edit_retail_customer',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=editcheck','post',$message);
		$add_retail_customer .= $this->form();
		$add_retail_customer .= close_table_form();
		
	return $add_retail_customer;
	}
	
	// customer level drop down menu
	function customer_level_drop_down($selected_id = '') {
			global $dbh;
		
		$parent_drop_down = '<select name="customer_level" id="parent_cat_dd">'.LB;
		
		$sql_query = "SELECT
						id,
						level_name
					 FROM
						advertiser_levels
					 ORDER BY level_weight ASC
					 ;";
		$rows = $dbh->queryAll($sql_query);
		
		foreach ($rows as $customer_level) {
		$ind = '--';
		
		$parent_drop_down .= '<option value="'.$customer_level['id'].'" '.($selected_id == $customer_level['id'] ? 'selected="selected" ' : '').'>'.$customer_level['level_name'].'</option>'.LB;
		
		}
		
		$parent_drop_down .= '</select>'.LB;
		
	return $parent_drop_down;
	}
	
	// draw retail customers form
	function form() {
			global $adv_info_tbl, $cert_amt_tbl, $adv_pmt_mtds_tbl, $dbh;
		
		$retail_customer_form = table_form_header('* indicates required field');
		$retail_customer_form .= table_form_field('Account Approved:','<input name="approved" type="checkbox" value="1" '.(!empty($adv_info_tbl->approved) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('Date Created:',$adv_info_tbl->date_created.'<input name="date_created" type="hidden" value="'.$adv_info_tbl->date_created.'" />');
		$retail_customer_form .= table_form_field('Approval Date:',$adv_info_tbl->approval_date.'<input name="approval_date" type="hidden" value="'.$adv_info_tbl->approval_date.'" />');
		$retail_customer_form .= table_form_field('Update Approved:','<input name="update_approval" type="checkbox" value="1" '.(!empty($adv_info_tbl->update_approval) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('Account Enabled:','<input name="account_enabled" type="checkbox" value="1" '.(!empty($adv_info_tbl->account_enabled) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('Allow Multiple Logins:','<input name="allow_multiple_logins" type="checkbox" value="1" '.(!empty($adv_info_tbl->allow_multiple_logins) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('Bypass Email Authentication:','<input name="email_authorized" type="checkbox" value="1" '.(!empty($adv_info_tbl->email_authorized) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('Allow Multiple Location Entry:','<input name="allow_mult_loc" type="checkbox" value="1" '.(!empty($adv_info_tbl->allow_mult_loc) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('Staff Pick:','<input name="pick" type="checkbox" value="1" '.(!empty($adv_info_tbl->pick) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('<span class="required">*Company Name:</span>','<input name="company_name" type="text" size="60" value="'.$adv_info_tbl->company_name.'">');
		$retail_customer_form .= table_form_field('Promo Code:','<input name="promo_code" type="text" size="10" maxlength="10" value="'.$adv_info_tbl->promo_code.'">');
		$retail_customer_form .= table_form_field('Company Image:','<input name="image" type="file">'.(!empty($adv_info_tbl->image) ? ' <script type="text/javascript"> 
				jQuery(function(){
				 jQuery("#del_image_lnk").click(function () {
					var current_advertiser_id = jQuery("#advert_id").val();
					var current_old_image = jQuery("#old_image").val();
					
					 $.ajax({
					   type: "POST",
					   url: "ajax_calls/delete_image.deal",
					   data: "advert_id="+current_advertiser_id+"&image="+current_old_image,
					   success: function(msg){
						 jQuery("#del_image_lnk").css("display","none");
						 jQuery("#old_image").val("");
						 jQuery("#image_text").html("Image Deleted");
					   }
					 });
				 });
				}); 
				</script>
				<br><strong>Current Image:</strong> <span id="image_text"><a class="thickbox" href="'.SITE_URL.'images/customers/' . $adv_info_tbl->image . '" target="blank">' . $adv_info_tbl->image . '</a></span><input id="old_image" name="old_image" type="hidden" value="'.$adv_info_tbl->image.'"> <a id="del_image_lnk" href="javascript:void;"><font color="red">Delete</font></a>' : ''));
		$retail_customer_form .= table_form_field('<span class="required">*Username:</span>','<input name="username" type="text" size="30" maxlength="50" value="'.$adv_info_tbl->username.'">');
		$retail_customer_form .= table_form_field('Password:','<input name="password" type="password" size="30" maxlength="50" value="">'.(!empty($adv_info_tbl->password) ? ' - Password exists' : ' - Password has not been set. If you do not assign one the customer will not be allowed to login.'));
		$retail_customer_form .= table_form_field('Affiliate Code:', '<input name="affiliate_code" type="text" value="'.$adv_info_tbl->affiliate_code.'" />');
		$retail_customer_form .= table_form_field('Link Affiliate Code:','<input name="link_affiliate_code" type="text" size="8" maxlength="8" value="'.$adv_info_tbl->link_affiliate_code.'">');

// added 9/17/2009 for managing alternate locations
		$retail_customer_form .= '<tr id="slideboxhead22" ><td class="expand_title_header" align="right"><a href="javascript:void(0);" id="a" class="slide22" name="1"><div></div></a></td><td class="form_title_header" align="center">Alternate Locations</td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead22").css("cursor","pointer");
jQuery(\'#slidebox22\').hide();
jQuery(\'a.slide22 div\').addClass(\'collapsed\');
			
jQuery(\'#slideboxhead22\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		 if (jQuery(\'a.slide22 div\').is(\'.collapsed\')) {
		 	jQuery(\'a.slide22 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide22 div\').toggleClass("expanded");
		 } else {
		 	jQuery(\'a.slide22 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide22 div\').toggleClass("expanded");
		 }
		 
		
     jQuery(\'#slidebox22\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox22" width="100%"><tr><td>';

$retail_customer_form .= '<script type="text/javascript" src="js/advertisers.js"></script><table border="0" cellpadding="4">
  <tr>
    <td width="150" valign="top"><div id="alt_message_area"></div><div id="select_alt_area">';
	
        $location_dd = '';
        $sql_query = "SELECT
						id,
						location_name
					 FROM
						advertiser_alt_locations
					 WHERE
                     	advertiser_id = '".$_GET['cid']."';";
		$rows = $dbh->queryAll($sql_query);
		if(count($rows) > 0) {
		  $retail_customer_form .= '<select name="alt_select_id" size="12" id="alt_select_id">';
		  foreach($rows as $cur_location) {
			  $retail_customer_form .= '<option value="'.$cur_location['id'].'">'.$cur_location['location_name'].'</option>';
		  }
		  $retail_customer_form .= '</select>';
		}
		
$retail_customer_form .= '</div>
      <input type="button" name="Edit" id="alt_select_loc" value="Edit"><input type="button" name="New" id="new_alt_loc" value="New"><input name="Delete" type="button" value="Delete" id="alt_select_delete" /><input name="alt_advert_id" id="alt_advert_id" type="hidden" value="'. $_GET['cid'] . '" /></td>
    <td id="alt_loc_form_area"></td>
  </tr>
</table>';

$retail_customer_form .= '</td></tr></table></td></tr>';		


		$retail_customer_form .= '<tr id="slideboxhead" ><td class="expand_title_header" align="right"><a href="javascript:void(0);" id="a" class="slide6" name="1"><div></div></a></td><td class="form_title_header" align="center">Information </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead").css("cursor","pointer");
jQuery(\'#slidebox6\').hide();
jQuery(\'a.slide6 div\').toggleClass("collapsed");
			
jQuery(\'#slideboxhead\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		 if (jQuery(\'a.slide6 div\').is(\'.collapsed\')) {
		 	jQuery(\'a.slide6 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide6 div\').toggleClass("expanded");
		 } else {
		 	jQuery(\'a.slide6 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide6 div\').toggleClass("expanded");
		 }
		
     jQuery(\'#slidebox6\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox6" width="100%">';
		
		$retail_customer_form .= table_form_field('Customer Description:','<textarea name="customer_description" cols="75" rows="9">'.$adv_info_tbl->customer_description.'</textarea>');
		$retail_customer_form .= table_form_field('Products And Services Mini Description:','<textarea name="products_services" cols="50" rows="6">'.$adv_info_tbl->products_services.'</textarea>');
		
		$retail_customer_form .= table_form_field('Website:','<input name="website" type="text" size="60" value="'.$adv_info_tbl->website.'">');
		$retail_customer_form .= table_form_field('BBB Member:','<input name="bbb_member" type="checkbox" value="1" '.(!empty($adv_info_tbl->bbb_member) ? 'checked' : '').'>');
		$retail_customer_form .= table_form_field('Links Back:','<input name="link_partner" type="checkbox" value="1" '.(!empty($adv_info_tbl->link_partner) ? 'checked' : '').'>');
		
$retail_customer_form .= '</table></td></tr>';		
		
		$retail_customer_form .= '<tr id="slideboxhead1"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide1" name="1"><div></div></a></td><td class="form_title_header">Certificate Amounts </td></tr>';

		$certificate_amounts = $cert_amt_tbl->get_certificate_amounts();
		
		$cert_amt = '<table>
						<tr><th>Disc Amt</th><th>&nbsp;</th><th>Certificate Values</th></tr>';
		foreach($certificate_amounts as $value) {
			
			$requirements = '<script type="text/javascript">
jQuery(function(){
 jQuery(".certificate_amount_requirements td").css("cursor","pointer");
  jQuery(\'#certificate_amount_requirements'.$value['id'].' td\').click(function(event) {
	if (event.target.type !== \'radio\') {
	  jQuery(\':radio\', this).trigger(\'click\');
	}
  });
  
  jQuery(\'#certificate_levels'.$value['id'].'\').click(function(event) {
	if (jQuery(\'#certificate_amount_requirements'.$value['id'].'\').css("display") == \'none\') {
	  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeIn("slow");
	} else {
	  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeOut("slow");
	}
  });

  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').css("display","none");

'.(is_array($adv_info_tbl->certificate_levels) ? !empty($adv_info_tbl->certificate_levels[$value['id']]) ? $adv_info_tbl->certificate_levels[$value['id']] == 1 ? 'jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeIn("slow");' : '' : '' : '').'

});
</script><script type="text/javascript">
				function set_price(price,target) {
					jQuery(target).val(price);
				}
				</script><table class="certificate_amount_requirements" id="certificate_amount_requirements'.$value['id'].'">';
				
			if($value['cost'] == 'blank') {
			  $requirements .= '<tr><td align="left">Cert Cost $'.('<input name="requirement_text['.$value['id'].'][blank_cost]" type="text" size="6" maxlength="20" value="'.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['blank_cost']) ? $adv_info_tbl->certificate_requirements[$value['id']]['blank_cost'] : '' : '').'" />').'</td></tr>';
			}

			$requirements .= '<tr><td valign="top" align="left">
			<input name="requirements['.$value['id'].']" type="radio" value="2" '.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['type']) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 2 ? 'checked ' : '' : '' : '').' />';
			
			// print valid with radios if assigned
			if(!empty($value['min_spend_amts'])) {
			  $requirements .= '<font color="red">Valid With</font> Min Spend Of:';
			  $min_spend_opts = explode(',',$value['min_spend_amts']);
			  foreach($min_spend_opts as $cur_spend_val) {
				  $requirements .= ' <input onclick="set_price('.$cur_spend_val.',\'#requirement_textb'.strtolower(numtoalpha($value['id'])).'\');" name="requirement_text['.$value['id'].'][min_spend]" type="radio" value="'.$cur_spend_val.'" /> $'.$cur_spend_val;
			  }
			}
			
			$requirements .= ' Other $<input id="requirement_textb'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][2]" type="text" value="'.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 2 ? $adv_info_tbl->certificate_requirements[$value['id']]['value'] : '' : '').'"  maxlength="90" size="8" />
			</td></tr>';

// old min spend input
//			$requirements .= '<tr><td valign="top" align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="2" '.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 2 ? 'checked ' : '' : '').'/> <font color="red">Valid With</font> Minimum Spend Of $<input id="requirement_textb'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][2]" type="text" value="'.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 2 ? $requirement_text[$value['id']][2] : '' : '').'"  maxlength="90" size="43" /></td></tr>';
			
			$requirements .= '<tr><td align="left"><input name="requirements['.$value['id'].']" type="radio" value="1" '.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['type']) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 1 ? 'checked ' : '' : '' : '').' /> <font color="red">Valid Towards</font> <br><textarea name="requirement_text['.$value['id'].'][1]" cols="40" rows="3">'.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['type']) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 1 ? $adv_info_tbl->certificate_requirements[$value['id']]['value'] : '' : '' : '').'</textarea></td></tr>';

//$requirements .= '<tr><td valign="top" align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="1" '.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 1 ? 'checked ' : '' : '').' /> <font color="red">Valid Towards</font> Purchase Of: <input id="requirement_texta'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][1]" type="text" value="'.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 1 ? $adv_info_tbl->certificate_requirements[$value['id']]['value'] : '' : '').'"  maxlength="90" size="40" /></td></tr>';
			
//			$requirements .= '<tr><td valign="top" align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="3" '.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 3 ? 'checked ' : '' : '').'/> <font color="red">Valid With</font> <textarea id="requirement_textc'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][3]" cols="48" rows="2">'.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 3 ? $requirement_text[$value['id']][3] : '' : '').'</textarea></td></tr>';
			
			$requirements .= '<tr><td valign="top" align="left"><input name="requirements['.$value['id'].']" type="radio" value="4" '.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['type']) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 4 ? 'checked ' : '' : '' : '').' />No Requirements<input name="requirement_text['.$value['id'].'][4]" type="hidden" value="" /></td></tr>';
			
			$requirements .= '<tr><td valign="top" align="left">Excludes: 
			<input id="requirement_texta'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][excludes]" type="text" value="'.(is_array($adv_info_tbl->certificate_requirements) ? $adv_info_tbl->certificate_requirements[$value['id']]['excludes'] : '').'"  maxlength="90" size="47" />
			</td></tr>';

//			$requirements .= '<tr><td align="left"><input name="requirements['.$value['id'].']" type="radio" value="1" '.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['type']) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 1 ? 'checked ' : '' : '' : '').' /> Purchase Of ... <br><textarea name="requirement_text['.$value['id'].'][1]" cols="40" rows="3">'.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['type']) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 1 ? $adv_info_tbl->certificate_requirements[$value['id']]['value'] : '' : '' : '').'</textarea></td></tr>';
//			
//			$requirements .= '<tr><td align="left"><input name="requirements['.$value['id'].']" type="radio" value="2" '.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['type']) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 2 ? 'checked ' : '' : '' : '').'/> Minimum Spend Of $<br><textarea name="requirement_text['.$value['id'].'][2]" cols="40" rows="3">'.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['type']) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 2 ? $adv_info_tbl->certificate_requirements[$value['id']]['value'] : '' : '' : '').'</textarea></td></tr>';
//			
//			$requirements .= '<tr><td align="left"><input name="requirements['.$value['id'].']" type="radio" value="3" '.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['type']) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 3 ? 'checked ' : '' : '' : '').'/> <textarea name="requirement_text['.$value['id'].'][3]" cols="40" rows="3">'.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['type']) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 3 ? $adv_info_tbl->certificate_requirements[$value['id']]['value'] : '' : '' : '').'</textarea></td></tr>';
//
//			$requirements .= '<tr><td align="left"><input name="requirements['.$value['id'].']" type="radio" value="4" '.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['type']) ? $adv_info_tbl->certificate_requirements[$value['id']]['type'] == 4 ? 'checked ' : '' : '' : '').'/>No Requirements<input name="requirement_text['.$value['id'].'][4]" type="hidden" value="" /></td></tr>';
			
			$requirements .= '</table>';
			
			$cert_amt .= '<tr><td align="right" valign="top">$'.($value['discount_amount'] == 'blank' ? '<input name="requirement_text['.$value['id'].'][blank_val]" type="text" size="6" maxlength="20" value="'.(is_array($adv_info_tbl->certificate_requirements) ? !empty($adv_info_tbl->certificate_requirements[$value['id']]['blank_val']) ? $adv_info_tbl->certificate_requirements[$value['id']]['blank_val'] : '' : '').'" />' : $value['discount_amount']).':</td><td valign="top"> <input name="certificate_levels['.$value['id'].']" id="certificate_levels'.$value['id'].'" type="checkbox" value="1" '.(is_array($adv_info_tbl->certificate_levels) ? !empty($adv_info_tbl->certificate_levels[$value['id']]) ?$adv_info_tbl->certificate_levels[$value['id']] == 1 ? 'checked ' : '' : ''  : '' ).'/></td><td>'.$requirements.'</td></tr>'.LB; 
		}
		$cert_amt .= '</table>';
		
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead1").css("cursor","pointer");
jQuery(\'#slidebox1\').hide();
jQuery(\'a.slide1 div\').toggleClass("collapsed");
			
jQuery(\'#slideboxhead1\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		 if (jQuery(\'a.slide1 div\').is(\'.collapsed\')) {
		 	jQuery(\'a.slide1 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide1 div\').toggleClass("expanded");
		 } else {
		 	jQuery(\'a.slide1 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide1 div\').toggleClass("expanded");
		 }
		
     jQuery(\'#slidebox1\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox1" width="100%"><tr><td class="form_title" valign="top">Certificate Amounts:</td><td class="form_field">'.$cert_amt.'</td></tr></table></td></tr>';
		
		$retail_customer_form .= '<tr id="slideboxhead2"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide2" name="1"><div></div></a></td><td class="form_title_header">Payment Information </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead2").css("cursor","pointer");
jQuery(\'#slidebox2\').hide();
jQuery(\'a.slide2 div\').toggleClass("collapsed");
			
jQuery(\'#slideboxhead2\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		 if (jQuery(\'a.slide2 div\').is(\'.collapsed\')) {
		 	jQuery(\'a.slide2 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide2 div\').toggleClass("expanded");
		 } else {
		 	jQuery(\'a.slide2 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide2 div\').toggleClass("expanded");
		 }
		
     jQuery(\'#slidebox2\').slideToggle("medium");
        // alert(id);
     return false;
});


jQuery(\'#payment_method\').change(function() {
		 
		if (jQuery(\'#payment_method\').val() == \'Check\') {
			jQuery(\'#check_payment\').css(\'display\',\'\');
			jQuery(\'#credit_card_payment\').css(\'display\',\'none\');
		}
		if (jQuery(\'#payment_method\').val() == \'Credit Card\') {
			jQuery(\'#credit_card_payment\').css(\'display\',\'\');
			jQuery(\'#check_payment\').css(\'display\',\'none\');
		}
		if (jQuery(\'#payment_method\').val() == \'\') {
			jQuery(\'#credit_card_payment\').css(\'display\',\'none\');
			jQuery(\'#check_payment\').css(\'display\',\'none\');
		}
});

';
if ($adv_info_tbl->payment_method != '') {
	if($adv_info_tbl->payment_method == 'Check') {
		$retail_customer_form .= 'jQuery(\'#check_payment\').css(\'display\',\'\');'.LB;
	} elseif ($adv_info_tbl->payment_method == 'Credit Card') {
		$retail_customer_form .= 'jQuery(\'#credit_card_payment\').css(\'display\',\'\');'.LB;
	}
}
		$retail_customer_form .= '}); 
</script><table id="slidebox2" width="100%">';

		$field_type_array = unserialize(PAYMENT_TYPES);
			
		$type_dd = '';
		foreach ($field_type_array as $id => $title) {
			$type_dd .= '<option '.($title == $adv_info_tbl->payment_method ? 'selected="selected"' : '').'>'.$title.'</option>'.LB; 
		}
		
		$cctype_dd = '';
		$field_type_array = unserialize(CC_TYPES);
		foreach ($field_type_array as $title) {
			$cctype_dd .= '<option '.($title == $adv_info_tbl->credit_card_type ? 'selected="selected"' : '').'>'.$title.'</option>'.LB; 
		}
		$retail_customer_form .= table_form_field('Payment Method:','<select id="payment_method" name="payment_method">'.$type_dd.'</select>');
		$retail_customer_form .= '<tr><td colspan="2">';
		
		$retail_customer_form .= '<table style="display:none" id="check_payment" width="100%">';
		
		// build account type drop down
		$account_type_array = array(
									'pc' => 'personal',
									'bc' => 'business'
									);
		
		$accnt_type_opt = '';
		
		foreach($account_type_array as $id => $value) {
			$accnt_type_opt .= '<option value="'.$id.'"'.($id == $adv_info_tbl->check_account_type ? ' selected ' : '').'>'.$value.'</option>';
		}
	
		$retail_customer_form .= table_form_field('Account Type:','<select name="check_account_type">'.$accnt_type_opt.'</select>');

		$retail_customer_form .= table_form_field('Routing Number:','<input name="check_routing_num" type="text" size="20" value="'.$adv_info_tbl->check_routing_num.'">');
		$retail_customer_form .= table_form_field('Account Number:','<input name="check_account_num" type="text" size="20" value="'.$adv_info_tbl->check_account_num.'">');
		$retail_customer_form .= table_form_field('Bank Name:','<input name="bank_name" type="text" size="20" value="'.$adv_info_tbl->bank_name.'">');
		
		$retail_customer_form .= table_form_field('Bank State:','<select name="bank_state">'.gen_state_dd($adv_info_tbl->bank_state).'</select>');
		$retail_customer_form .= table_form_field('Drivers License Number:','<input name="drivers_license_num" type="text" size="20" value="'.$adv_info_tbl->drivers_license_num.'">');
		
		$retail_customer_form .= table_form_field('Drivers License State:','<select name="drivers_license_state">'.gen_state_dd($adv_info_tbl->drivers_license_state).'</select>');
		$retail_customer_form .= '</table>';

		$retail_customer_form .= '<table style="display:none" id="credit_card_payment" width="100%">';
		$retail_customer_form .= table_form_field('Credit Card Type:','<select name="credit_card_type">'.$cctype_dd.'</select>');
		$retail_customer_form .= table_form_field('Credit Card Number:','<input name="cc_number" type="text" size="20" value="'.$adv_info_tbl->cc_number.'">');
		$retail_customer_form .= table_form_field('CVV:','<input name="cvv" type="text" size="4" value="'.$adv_info_tbl->cvv.'">');
		
		$set_expiration = explode("/",$adv_info_tbl->cc_exp);
		
		// print exp dd
		$years_dd = '';
		$cur_year = date("Y");
		$fut_year = $cur_year+10;
		while($cur_year <= $fut_year) {
			$years_dd .= '<option '.(isset($set_expiration[1]) ? $set_expiration[1] == $cur_year ? 'selected' : '' : '').'>'.$cur_year.'</option>';
			$cur_year++;
		}

		$month = 1;
		$months_dd = '';
		while($month <= 12) {
			$months_dd .= '<option '.($set_expiration[0] == $month ? 'selected' : '').'>'.$month.'</option>';
			$month++;
		}
		$retail_customer_form .= table_form_field('Credit Card Expiration:','Month:<select name="cc_exp_month">'.$months_dd.'</select> / Year: <select name="cc_exp_year">'.$years_dd.'</select>');
		$retail_customer_form .= '</table></td></tr>';

$retail_customer_form .= '</table></td></tr>';

		$retail_customer_form .= '<tr id="slideboxhead3"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide3" name="1"><div></div></a></td><td class="form_title_header">Level Settings </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead3").css("cursor","pointer");
jQuery(\'#slidebox3\').hide();
jQuery(\'a.slide3 div\').toggleClass("collapsed");
			
jQuery(\'#slideboxhead3\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		 if (jQuery(\'a.slide3 div\').is(\'.collapsed\')) {
		 	jQuery(\'a.slide3 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide3 div\').toggleClass("expanded");
		 } else {
		 	jQuery(\'a.slide3 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide3 div\').toggleClass("expanded");
		 }
		
     jQuery(\'#slidebox3\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox3" width="100%">';

		$retail_customer_form .= table_form_field('<span class="required">*Customer Level:</span>',$this->customer_level_drop_down($adv_info_tbl->customer_level));

		$retail_customer_form .= table_form_field('Renew Account After Initial Period:','<input name="customer_level_renew" type="checkbox" value="1" '.(!empty($adv_info_tbl->customer_level_renew) ? 'checked disabled' : '').'>');
				
		$retail_customer_form .= table_form_field('Customer Level Expiration:','
<script>DateInput(\'customer_level_exp\', true, \'YYYY-MM-DD\',\''.(!empty($adv_info_tbl->customer_level_exp) ? $adv_info_tbl->customer_level_exp : date("Y-m-d")).'\')</script>');
		$retail_customer_form .= table_form_field('Customer Level Renewal Date:','<script>DateInput(\'customer_level_renewal_date\', true, \'YYYY-MM-DD\',\''.(!empty($adv_info_tbl->customer_level_renewal_date) ? $adv_info_tbl->customer_level_renewal_date : date("Y-m-d")).'\')</script>');

$retail_customer_form .= '</table></td></tr>';

		$retail_customer_form .= '<tr id="slideboxhead4"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide10" name="1"><div></div></a></td><td class="form_title_header">Hours Of Operation </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead4").css("cursor","pointer");
jQuery(\'#slidebox10\').hide();
jQuery(\'a.slide10 div\').toggleClass("collapsed");
			
jQuery(\'#slideboxhead4\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		 if (jQuery(\'a.slide10 div\').is(\'.collapsed\')) {
		 	jQuery(\'a.slide10 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide10 div\').toggleClass("expanded");
		 } else {
		 	jQuery(\'a.slide10 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide10 div\').toggleClass("expanded");
		 }
		
     jQuery(\'#slidebox10\').slideToggle("medium");
        // alert(id);
     return false;
     });

});
</script><table id="slidebox10" width="100%">';
		
		// builds hours of operation selection
		$hours_list = unserialize(HOURS_SELECT);
		$days_array = unserialize(DAYS_ARRAY);
		if(!is_array($adv_info_tbl->hours_operation) || empty($adv_info_tbl->hours_operation)) {
			// if hours of operation settings do not exist print HOP form
			$hours_of_operation = '<input name="hours_operation[selected][type]" type="radio" value="nohours" /> Do Not Display Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="24hr" /> Open 24 Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="select" /> Hours Select Below<br>';
			$hours_of_operation .= '<table>';
			$hours_of_operation .= '<tr>';
			reset($days_array);
			foreach($days_array as $value) {
				$hours_of_operation .= '<th>'.$value.'</th>';
			}
			$hours_of_operation .= '</tr><tr>';
			reset($days_array);
			reset($hours_list);
			// draw days selection
			foreach($days_array as $day_value) {
				$hours_of_operation .= '<td>';
				$hours_of_operation .= '<select name="hours_operation[selected]['.$day_value.'open]">';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.('9:00 AM' == $value ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= 'to<br>';
				$hours_of_operation .= '<select name="hours_operation[selected]['.$day_value.'close]">';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.('5:00 PM' == $value ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= '</td>';
			}
			$hours_of_operation .= '</tr></table>';
		} else {
			// if HOP values are set load values into form
			if (!is_array($adv_info_tbl->hours_operation)) {
				$hours_operation = unserialize($adv_info_tbl->hours_operation);
			} else {
				$hours_operation = $adv_info_tbl->hours_operation;
			}
			$hours_of_operation = '<input name="hours_operation[selected][type]" type="radio" value="nohours" '.($hours_operation['selected']['type'] == 'nohours' ? 'checked' : '').' /> Do Not Display Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="24hr" '.($hours_operation['selected']['type'] == '24hr' ? 'checked' : '').' /> Open 24 Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="select" '.($hours_operation['selected']['type'] == 'select' ? 'checked' : '').' /> Hours Select Below<br>';
			$hours_of_operation .= '<table>';
			$hours_of_operation .= '<tr>';
			reset($days_array);
			foreach($days_array as $value) {
				$hours_of_operation .= '<th>'.$value.'</th>';
			}
			$hours_of_operation .= '</tr><tr>';
			reset($days_array);
			reset($hours_list);
			// draw days selection
			foreach($days_array as $day_value) {
				$hours_of_operation .= '<td>';
				$hours_of_operation .= '<select name="hours_operation[selected]['.$day_value.'open]">';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.($hours_operation['selected'][$day_value.'open'] == $value ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= 'to<br>';
				$hours_of_operation .= '<select name="hours_operation[selected]['.$day_value.'close]">';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.($hours_operation['selected'][$day_value.'close'] == $value ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= '</td>';
			}
			$hours_of_operation .= '</tr></table>';			
		}
		
		$retail_customer_form .= table_form_field('Hours Of Operation:',$hours_of_operation);

		// build payment method options
		$payment_methods = $adv_pmt_mtds_tbl->get_all();
				
		$payment_method_sel = '';
		foreach($payment_methods as $value) {
			$payment_method_sel .= '<input name="payment_options['.$value['id'].']" type="checkbox" value="1"'.(is_array($adv_info_tbl->payment_options) ? isset($adv_info_tbl->payment_options[$value['id']]) ? $adv_info_tbl->payment_options[$value['id']] == 1 ? 'checked' : '' : ''  : '' ).' />: '.$value['method'].'<br>';
		}
		
$retail_customer_form .= '</table></td></tr>';		

		$retail_customer_form .= '<tr id="slideboxhead5"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide9" name="1"><div></div></a></td><td class="form_title_header">Payment Options </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead5").css("cursor","pointer");
jQuery(\'#slidebox9\').hide();
jQuery(\'a.slide9 div\').toggleClass("collapsed");
			
jQuery(\'#slideboxhead5\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		 if (jQuery(\'a.slide9 div\').is(\'.collapsed\')) {
		 	jQuery(\'a.slide9 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide9 div\').toggleClass("expanded");
		 } else {
		 	jQuery(\'a.slide9 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide9 div\').toggleClass("expanded");
		 }
		
     jQuery(\'#slidebox9\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox9" width="100%">';

		$retail_customer_form .= table_form_field('Payment Options:',$payment_method_sel);

$retail_customer_form .= '</table></td></tr>';
		
		$retail_customer_form .= '<tr id="slideboxhead6"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide5" name="1"><div></div></a></td><td class="form_title_header">Categories </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead6").css("cursor","pointer");
jQuery(\'#slidebox5\').hide();
jQuery(\'a.slide5 div\').toggleClass("collapsed");
			
jQuery(\'#slideboxhead6\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		 if (jQuery(\'a.slide5 div\').is(\'.collapsed\')) {
		 	jQuery(\'a.slide5 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide5 div\').toggleClass("expanded");
		 } else {
		 	jQuery(\'a.slide5 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide5 div\').toggleClass("expanded");
		 }
		
     jQuery(\'#slidebox5\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox5" width="100%">';

		// load category list class
		require(CLASSES_DIR.'sections/category_select.php');
		$category_select_pg = new category_select_pg;
		
		$sql_query = "SELECT
						id,
						category_id
					 FROM
						advertiser_categories
					WHERE
					advertiser_id = '".$adv_info_tbl->id."';";
		
		$rows = $dbh->queryAll($sql_query);
		foreach($rows as $cur_sel) {
			$category_select_array[$cur_sel['category_id']] = 1;
		}
		
		$category_select_pg->selected_array = $category_select_array;

		$retail_customer_form .= table_form_field('<span class="required">*Category:</span>',$category_select_pg->list_categories());

$retail_customer_form .= '</table></td></tr>';		

		$retail_customer_form .= '<tr id="slideboxhead7"><td class="expand_title_header"> <a href="javascript:void(0);" id="a" class="slide4" name="1"><div></div></a></td><td class="form_title_header">Contact Address Information </td></tr>';
		$retail_customer_form .= '<tr>
		<td colspan="2"><script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead7").css("cursor","pointer");
jQuery(\'#slidebox4\').hide();
jQuery(\'a.slide4 div\').toggleClass("collapsed");
			
jQuery(\'#slideboxhead7\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		 if (jQuery(\'a.slide4 div\').is(\'.collapsed\')) {
		 	jQuery(\'a.slide4 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide4 div\').toggleClass("expanded");
		 } else {
		 	jQuery(\'a.slide4 div\').toggleClass("collapsed");
		 	jQuery(\'a.slide4 div\').toggleClass("expanded");
		 }
		
     jQuery(\'#slidebox4\').slideToggle("medium");
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox4" width="100%">';

		$retail_customer_form .= table_form_field('Hide Address From Listing:','<input name="hide_address" type="checkbox" value="1" '.($adv_info_tbl->hide_address == 1 ? 'checked' : '').' />');
		$retail_customer_form .= table_form_field('<span class="required">*Contact First Name:</span>','<input name="first_name" type="text" size="50" maxlength="100" value="'.$adv_info_tbl->first_name.'">');
		$retail_customer_form .= table_form_field('<span class="required">*Contact Last Name:</span>','<input name="last_name" type="text" size="50" maxlength="100" value="'.$adv_info_tbl->last_name.'">');
		$retail_customer_form .= table_form_field('<span class="required">*Contact Address 1:</span>','<input name="address_1" type="text" size="50" maxlength="120" value="'.$adv_info_tbl->address_1.'">');
		$retail_customer_form .= table_form_field('Contact Address 2:','<input name="address_2" type="text" size="50" maxlength="120" value="'.$adv_info_tbl->address_2.'">');
		$retail_customer_form .= table_form_field('<span class="required">*Contact City:</span>','<input name="city" type="text" size="50" maxlength="100" value="'.$adv_info_tbl->city.'">');
		
		$retail_customer_form .= table_form_field('<span class="required">*Contact State:</span>','<select name="state">'.gen_state_dd($adv_info_tbl->state).'</select>');
		
		$retail_customer_form .= table_form_field('<span class="required">*Contact Zip:</span>','<input name="zip" type="text" size="15" maxlength="15" value="'.$adv_info_tbl->zip.'">');
		$retail_customer_form .= table_form_field('Contact Phone Number:','<input name="phone_number" type="text" size="15" maxlength="15" value="'.$adv_info_tbl->phone_number.'">');
		$retail_customer_form .= table_form_field('Contact Fax Number:','<input name="fax_number" type="text" size="15" maxlength="15" value="'.$adv_info_tbl->fax_number.'">');
		$retail_customer_form .= table_form_field('<span class="required">*Contact Email Address:</span>','<input name="email_address" type="text" size="50" maxlength="160" value="'.$adv_info_tbl->email_address.'">');

$retail_customer_form .= '</table></td></tr>';		
		
		$retail_customer_form .= table_span_form_field('<center><input id="advert_id" name="id" type="hidden" value="'.$adv_info_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
		
	return $retail_customer_form;
	}
	
		
	// category drop down menu
	function category_drop_down($selected_id = '') {
			global $dbh;
		
		$parent_drop_down = '<select name="category" id="parent_cat_dd">'.LB;
		
		$sql_query = "SELECT
						id,
						category_name,
						parent_category_id
					 FROM
						categories
					 WHERE
						zip_id is NULL
					 AND
						parent_category_id = 0
					 ORDER BY sort_order ASC, category_name ASC
					 ;";
		$rows = $dbh->queryAll($sql_query);
		
		foreach ($rows as $categories) {
		$ind = '--';
		
		// reset last child id
		$this->last_child_id = '';
		
		// draw child drop down
		$parent_drop_down_child = $this->parent_dd_child_chk($categories['id'],$ind,$selected_id);
		
		// draw parent drop down
		$parent_drop_down_parent = '<option value="'.(!empty($this->last_child_id) ? $this->last_child_id : $categories['id']).'" '.($selected_id == $categories['id'] ? 'selected="selected" ' : '').'>'.$categories['category_name'].'</option>'.LB;
		
		$parent_drop_down .= $parent_drop_down_parent . $parent_drop_down_child;
		}
		
		$parent_drop_down .= '</select>'.LB;
		
	return $parent_drop_down;
	}
	
	// check for child categories
	function parent_dd_child_chk($cid,$ind,$selected_id = '') {
			global $dbh;
			
		$sql_query = "SELECT
						id,
						category_name,
						parent_category_id
					 FROM
						categories
					 WHERE
						zip_id is NULL
					 AND
						parent_category_id = '".$cid."'
					 ORDER BY sort_order ASC, category_name ASC
					 ;";
		$rows = $dbh->queryAll($sql_query);
		
		foreach ($rows as $categories) {
		$parent_drop_down .= '<option value="'.$categories['id'].'" '.($selected_id == $categories['id'] ? 'selected="selected" ' : '').'>'.$ind.' '.$categories['category_name'].'</option>'.LB;
		
		$parent_drop_down .= $this->parent_dd_child_chk($categories['id'],$ind.'--');
		
		$this->last_child_id = $categories['id'];
		}
		
	return $parent_drop_down;
	}
		
		// check form submission values
	function form_check() {
			global $adv_info_tbl;
		
		// required fields array
		$required_fields = array('Company Name'=> $adv_info_tbl->company_name,
								'Username' => $adv_info_tbl->username,
								'Contact First Name' => $adv_info_tbl->first_name,
								'Contact Last Name' => $adv_info_tbl->last_name,
								'Contact Address 1' => $adv_info_tbl->address_1,
								'Contact City' => $adv_info_tbl->city,
								'Contact State' => $adv_info_tbl->state,
								'Contact Zip' => $adv_info_tbl->zip,
								'Contact Email Address' => $adv_info_tbl->email_address
								);
			
		// check error values and write error array					
		foreach($required_fields as $field_name => $output) {

			if (empty($output)) {
				$errors_array[] = $field_name;
			}
		
		}
		
		if (!empty($errors_array)) {
			$error_message = 'You did not supply a value for these fields: ' . implode(', ',$errors_array);
		}
		
		if ($adv_info_tbl->username_check() > 0) {
			$error_message .= '<br>Username has already been assigned to another customer. Please choose another.';
		}
		
	return $error_message;
	}

}

?>