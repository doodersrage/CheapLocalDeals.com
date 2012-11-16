<?PHP

// create and modify certificate amount values

// certificates management class
class certificate_amount_frm {
  
  // load add retail customers page
  function add($message = '') {
	$add_certificates = open_table_form('Add New Certificate Amount','add_certificates_amount',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=certificateamountnewcheck&lid='.$_GET['lid'],'post',$message);
	$add_certificates .= $this->form();
	$add_certificates .= close_table_form();
  return $add_certificates;
  }
  
  // load add retail customers page
  function edit($message = '') {
	$add_certificates = open_table_form('Edit Certificate Amount','edit_certificate_amount',SITE_ADMIN_SSL_URL.'?sect=retcustomer&mode=certificateamounteditcheck&lid='.$_GET['lid'],'post',$message);
	$add_certificates .= $this->form();
	$add_certificates .= close_table_form();
  return $add_certificates;
  }
  
  // draw retail customers form
  function form() {
	global $cert_amt_tbl;
	
	$certificates_form = table_form_header('* indicates required field');
	$certificates_form .= table_form_field('Sort:','<input name="crtamt_sort" type="text" size="5" maxlength="12" value="'.$cert_amt_tbl->crtamt_sort.'">');
	$certificates_form .= table_form_field('<span class="required">*Discount Amount:</span>','<input name="discount_amount" type="text" size="5" maxlength="15" value="'.$cert_amt_tbl->discount_amount.'">');
	$certificates_form .= table_form_field('Discount Cost:','<input name="cost" type="text" size="5" maxlength="12" value="'.$cert_amt_tbl->cost.'">');
	$certificates_form .= table_form_field('Min Spend Amounts:','<input name="min_spend_amts" type="text" size="30" value="'.$cert_amt_tbl->min_spend_amts.'">');
	$certificates_form .= table_span_form_field('<center><input name="id" type="hidden" value="'.$cert_amt_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
	
  return $certificates_form;
  }
	  
  // check form submission values
  function form_check() {
	global $cert_amt_tbl;
	
	// required fields array
	$required_fields = array('Discount Amount'=> $cert_amt_tbl->discount_amount);
		
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