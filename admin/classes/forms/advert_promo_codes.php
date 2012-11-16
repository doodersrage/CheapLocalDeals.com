<?PHP

// document used to add or modify available advertiser promo codes selection

// advert_promo_codes management class
class advert_promo_codes_frm {
  
  function delete() {
	global $dbh;
	if(is_array($_POST['delete_advert_promo_codes'])) {
	  foreach($_POST['delete_advert_promo_codes'] as $cur_promo_id) {
		$stmt = $dbh->prepare("DELETE FROM advert_promo_codes WHERE id = '".$cur_promo_id."';");
		$stmt->execute();
	  }
	}
  }
  
  // load add advert_promo_codes page
  function add($message = '') {
	$add_ad_payment_methods = open_table_form('Add New Promo Code','add_advert_promo_codes',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=promocodesnewcheck','post',$message);
	$add_ad_payment_methods .= $this->form();
	$add_ad_payment_methods .= close_table_form();
  return $add_ad_payment_methods;
  }
  
  // load add advert_promo_codes page
  function edit($message = '') {
	$add_ad_payment_methods = open_table_form('Edit Promo Code','edit_advert_promo_codes',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=promocodeseditcheck','post',$message);
	$add_ad_payment_methods .= $this->form();
	$add_ad_payment_methods .= close_table_form();
  return $add_ad_payment_methods;
  }
  
  // draw advert_promo_codes form
  function form() {
	global $adv_pro_codes_tbl;
	
	$ad_payment_methods_form = table_form_header('* indicates required field');
	$ad_payment_methods_form .= table_form_field('<span class="required">*Promo Code:</span>','<input name="promo_code" type="text" size="10" maxlength="10" value="'.$adv_pro_codes_tbl->promo_code.'">');
	$ad_payment_methods_form .= table_form_field('<span class="required">*Percentage:</span>','<input name="percentage" type="text" size="4" maxlength="4" value="'.$adv_pro_codes_tbl->percentage.'">');
	$ad_payment_methods_form .= table_span_form_field('<center><input name="id" type="hidden" value="'.$adv_pro_codes_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
	
  return $ad_payment_methods_form;
  }
	  
  // check form submission values
  function form_check() {
	global $adv_pro_codes_tbl;
	
	// required fields array
	$required_fields = array(
							 'Promo Code'=> $adv_pro_codes_tbl->promo_code,
							 'Percentage'=> $adv_pro_codes_tbl->percentage
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
	
  return $error_message;
  }

}

?>