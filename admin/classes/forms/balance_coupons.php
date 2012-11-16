<?PHP

// add or modify static pages
// handles and displays pages admin page
class balance_coupons_frm {
  var $bc_data = '';
  
  // delete categroies
  function delete() {
	global $dbh;
	foreach($_POST['delete_coupons'] as $selected_pages) {
	  $stmt = $dbh->prepare("DELETE FROM customer_coupons WHERE id = '".$selected_pages."';");
	  $stmt->execute();
	}
  }
  
  // load add page page
  function add($message = '') {
	$add_pages = open_table_form('Add New Page','add_page',SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=balancecouponsaddcheck','post',$message);
	$add_pages .= $this->form();
	$add_pages .= close_table_form();
  return $add_pages;
  }
  
  // load add page page
  function edit($message = '') {
	$add_pages = open_table_form('Edit Page','edit_page',SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=balancecouponseditcheck','post',$message);
	$add_pages .= $this->form();
	$add_pages .= close_table_form();
  return $add_pages;
  }
  
  // draw form
  function form() {
	global $cust_cpns_tbl;

	$pages_form = table_form_header('* indicates required field');
	$pages_form .= table_form_field('<span class="required">*Code:</span>','<input name="code" type="text" size="10" maxlength="10" value="'.$cust_cpns_tbl->code.'">');
	$pages_form .= table_form_field('<span class="required">*value:</span>','<input name="value" type="text" size="13" maxlength="13" value="'.$cust_cpns_tbl->value.'">');
	$pages_form .= table_form_field('<span class="required">*Expires:</span>','<script>DateInput(\'expires\', true, \'YYYY-MM-DD\',\''.(!empty($cust_cpns_tbl->expires) ? date("Y-m-d",strtotime($cust_cpns_tbl->expires)) : date("Y-m-d")).'\')</script>');
	$pages_form .= table_span_form_field('<center><input type="hidden" name="id" value="'.$cust_cpns_tbl->id.'"><input name="submit" type="submit" value="Submit"></center>');
	
  return $pages_form;
  }
  
  // check form submission values
  function form_check() {
	global $dbh, $cust_cpns_tbl;
	
	// required fields array
	$required_fields = array(
							'Code'=> $cust_cpns_tbl->code,
							'Value'=> $cust_cpns_tbl->value,
							'Expires'=> $cust_cpns_tbl->expires
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
	
	if ($cust_cpns_tbl->existing_code_check() > 0) {
	  $error_message .= '<center>Coupon code already assigned. Please enter another.</center>'.LB;
	}
	
  return $error_message;
  }
  
}

?>