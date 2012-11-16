<?PHP

// document used to add or modify available customeriser promo codes selection

// customer_promo_codes management class
class customer_promo_codes_frm {
  
  function delete() {
	global $dbh;
	if(is_array($_POST['delete_customer_promo_codes'])) {
	  foreach($_POST['delete_customer_promo_codes'] as $cur_promo_id) {
		$stmt = $dbh->prepare("DELETE FROM customer_promo_codes WHERE id = '".$cur_promo_id."';");
		$stmt->execute();
	  }
	}	  
  }
  
  // load add customer_promo_codes page
  function add($message = '') {
	$add_ad_payment_methods = open_table_form('Add New Promo Code','add_customer_promo_codes',SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=promocodesnewcheck','post',$message);
	$add_ad_payment_methods .= $this->form();
	$add_ad_payment_methods .= close_table_form();
  return $add_ad_payment_methods;
  }
  
  // load add customer_promo_codes page
  function edit($message = '') {
	$add_ad_payment_methods = open_table_form('Edit Promo Code','edit_customer_promo_codes',SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=promocodeseditcheck','post',$message);
	$add_ad_payment_methods .= $this->form();
	$add_ad_payment_methods .= close_table_form();
  return $add_ad_payment_methods;
  }
  
  // draw customer_promo_codes form
  function form() {
	global $cust_promo_cds_tbl;
	
	$ad_payment_methods_form = table_form_header('* indicates required field');
	$ad_payment_methods_form .= table_form_field('<span class="required">*Promo Code:</span>','<input name="promo_code" type="text" size="10" maxlength="10" value="'.$cust_promo_cds_tbl->promo_code.'">');
	$ad_payment_methods_form .= table_form_field('<span class="required">*Percentage:</span>','<input name="percentage" type="text" size="4" maxlength="4" value="'.$cust_promo_cds_tbl->percentage.'">');
	$ad_payment_methods_form .= table_span_form_field('<center><input name="id" type="hidden" value="'.$cust_promo_cds_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
	
  return $ad_payment_methods_form;
  }
	  
  // check form submission values
  function form_check() {
	global $cust_promo_cds_tbl;
	
	// required fields array
	$required_fields = array(
							 'Promo Code'=> $cust_promo_cds_tbl->promo_code,
							 'Percentage'=> $cust_promo_cds_tbl->percentage
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