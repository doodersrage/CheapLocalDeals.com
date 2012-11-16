<?PHP

// document used to add or modify advertiser payment method selection

// ad_payment_methods management class
class ad_payment_methods_frm {
  
  function delete() {
	global $dbh;
	if(is_array($_POST['delete_advertiser_payment_methods'])) {
	  foreach($_POST['delete_advertiser_payment_methods'] as $cur_payment_id) {
		$stmt = $dbh->prepare("DELETE FROM advertiser_payment_methods WHERE id = '".$cur_payment_id."';");
		$stmt->execute();
	  }
	}
  }
  
  // load add ad_payment_methods page
  function add($message = '') {
	$add_ad_payment_methods = open_table_form('Add New Payment Method','add_payment_method',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=paymentmethodsnewcheck','post',$message);
	$add_ad_payment_methods .= $this->form();
	$add_ad_payment_methods .= close_table_form();
  return $add_ad_payment_methods;
  }
  
  // load add ad_payment_methods page
  function edit($message = '') {
	$add_ad_payment_methods = open_table_form('Edit Payment Method','edit_ad_payment_methods',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=paymentmethodseditcheck','post',$message);
	$add_ad_payment_methods .= $this->form();
	$add_ad_payment_methods .= close_table_form();
  return $add_ad_payment_methods;
  }
  
  // draw ad_payment_methods form
  function form() {
	global $adv_pmt_mtds_tbl;
	
	$ad_payment_methods_form = table_form_header('* indicates required field');
	$ad_payment_methods_form .= table_form_field('<span class="required">*Method Name:</span>','<input name="method" type="text" size="30" maxlength="100" value="'.$adv_pmt_mtds_tbl->method.'">');
	
	// added to set image height and width settings
	$image_location = CONNECTION_TYPE.'includes/resize_image.deal?image='.urlencode('payment_logos/'.$adv_pmt_mtds_tbl->image).'&amp;new_width=50&amp;new_height=50';

	$ad_payment_methods_form .= table_form_field('Image:','<input name="image" type="file" />'.($adv_pmt_mtds_tbl->image != '' ? ' Current Image: <img src="'.$image_location.'" alt="' . htmlentities($adv_pmt_mtds_tbl->method) . '" /><input name="old_image" type="hidden" value="'.$adv_pmt_mtds_tbl->image.'">' : ''));
	$ad_payment_methods_form .= table_span_form_field('<center><input name="id" type="hidden" value="'.$adv_pmt_mtds_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
	
  return $ad_payment_methods_form;
  }
	  
  // check form submission values
  function form_check() {
	global $adv_pmt_mtds_tbl;
	
	// required fields array
	$required_fields = array('Method Name'=> $adv_pmt_mtds_tbl->method);
		
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