<?PHP

// document adds and modifies customers

// retail customers management class
class reg_customers_frm {
	
	function generate_csv() {
		global $customer_info_table, $dbh;

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");;
		header("Content-Disposition: attachment;filename=".strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', date("Y-m-d")))."-customers.xls "); 
		header("Content-Transfer-Encoding: binary ");

		// header for spreadsheet
		$headers = array('Signed Up','Name','Address','Address2','City','State','Zip','Phone','E-mail');
		
		// build header row
		$xls_output = implode(T,$headers).LB;

		$sql_query = "SELECT
						id
					 FROM
						customer_info
					 ORDER BY date_created desc
					 ;";
		
		$rows = $dbh->queryAll($sql_query);
		
		foreach ($rows as $pages) {
			// reset row output
			$cur_row = array();
			$customer_info_table->get_db_vars($pages['id']);
			
			$cur_row[] = $customer_info_table->date_created;
			$cur_row[] = $customer_info_table->first_name . ' ' . $customer_info_table->last_name;
			$cur_row[] = $customer_info_table->address_1;
			$cur_row[] = $customer_info_table->address_2;
			$cur_row[] = $customer_info_table->city;
			$cur_row[] = $customer_info_table->state;
			$cur_row[] = $customer_info_table->zip;
			$cur_row[] = $customer_info_table->phone_number;
			$cur_row[] = $customer_info_table->email_address;
			
			$xls_output .= implode(T,$cur_row).LB;
		}
		
		echo $xls_output;
		
		die();

	}
	
	// delete categroies
	function delete() {
		global $dbh;
	  foreach($_POST['delete_regcustomer'] as $selected_customers) {
		$stmt = $dbh->prepare("DELETE FROM customer_info WHERE id = '".$selected_customers."';");
		$stmt->execute();
	  }
	}
	
	// load add retail customers page
	function add($message = '') {
	  $add_reg_customer = open_table_form('Add New Regular Customer','add_reg_customer',SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=addcheck','post',$message);
	  $add_reg_customer .= $this->form();
	  $add_reg_customer .= close_table_form();
	return $add_reg_customer;
	}
	
	// load add retail customers page
	function edit($message = '') {
	  $add_reg_customer = open_table_form('Edit Regular Customer','edit_reg_customer',SITE_ADMIN_SSL_URL.'?sect=regcustomer&mode=editcheck','post',$message);
	  $add_reg_customer .= $this->form();
	  $add_reg_customer .= close_table_form();
	return $add_reg_customer;
	}
	
	// draw retail customers form
	function form() {
	  global $dbh, $customer_info_table;
	  
	  $reg_customer_form = table_form_header('* indicates required field');
	  $reg_customer_form .= table_form_field('Account Enabled:','<input name="account_enabled" type="checkbox" value="1" '.(!empty($customer_info_table->account_enabled) ? 'checked' : '').'>');
	  
	  $reg_customer_form .= table_form_field('Balance:','<input name="balance" type="text" size="11" maxlength="13" value="'.$customer_info_table->balance.'">');
	  
	  // added 7/8/2010 for api use assignment
	  
	  $reg_customer_form .= table_form_field('Linked API User:','<select name="api_id">'.gen_api_user_dd($customer_info_table->api_id).'</select>');
	  
	  $reg_customer_form .= table_form_field('Reference Code:','<input name="ref_code" type="text" size="15" maxlength="25" value="'.$customer_info_table->ref_code.'">');
	  $reg_customer_form .= table_form_field('Reference Code:','<input name="usr_ref_code" type="text" size="15" maxlength="25" value="'.$customer_info_table->usr_ref_code.'">');
	  $reg_customer_form .= table_form_field('<span class="required">*Email Address:</span>','<input name="email_address" type="text" size="50" maxlength="160" value="'.$customer_info_table->email_address.'">');
	  $reg_customer_form .= table_form_field('Password:','<input name="password" type="password" size="30" maxlength="50" value="">'.(!empty($customer_info_table->password) ? ' - Password exists for customer.' : ' - Password has not been set. If you do not assign one the customer will not be allowed to login.'));
	  $reg_customer_form .= table_form_field('<span class="required">*First Name:</span>','<input name="first_name" type="text" size="50" maxlength="100" value="'.$customer_info_table->first_name.'">');
	  $reg_customer_form .= table_form_field('<span class="required">*Last Name:</span>','<input name="last_name" type="text" size="50" maxlength="100" value="'.$customer_info_table->last_name.'">');
	  $reg_customer_form .= table_form_field('<span class="required">*Address 1:</span>','<input name="address_1" type="text" size="50" maxlength="120" value="'.$customer_info_table->address_1.'">');
	  $reg_customer_form .= table_form_field('Address 2:','<input name="address_2" type="text" size="50" maxlength="120" value="'.$customer_info_table->address_2.'">');
	  $reg_customer_form .= table_form_field('<span class="required">*City:</span>','<input name="city" type="text" size="50" maxlength="100" value="'.$customer_info_table->city.'">');
			  	  
	  $reg_customer_form .= table_form_field('<span class="required">*State:</span>','<select name="state">'.gen_state_dd($customer_info_table->state).'</select>');
	  $reg_customer_form .= table_form_field('<span class="required">*Zip:</span>','<input name="zip" type="text" size="15" maxlength="15" value="'.$customer_info_table->zip.'">');
	  $reg_customer_form .= table_form_field('Phone Number:','<input name="phone_number" type="text" size="15" maxlength="15" value="'.$customer_info_table->phone_number.'">');
	  $reg_customer_form .= table_form_field('Fax Number:','<input name="fax_number" type="text" size="15" maxlength="15" value="'.$customer_info_table->fax_number.'">');
	  $userData = '<table width="100%">';
	  foreach($customer_info_table->header_data as $id => $value){
	  	$userData .= '<tr><td>'.$id.' </td><td> '.$value.'</td></td>';
	  }
	  $userData .= '</table>';
	  $reg_customer_form .= table_form_field('User Header:',$userData);
  
	  $reg_customer_form .= table_span_form_field('<center><input name="id" type="hidden" value="'.$customer_info_table->id.'"><input name="submit" type="submit" value="Submit"></center>');
	  
	return $reg_customer_form;
	}
		
	// check form submission values
	function form_check() {
	  global $customer_info_table;
	  
	  // required fields array
	  $required_fields = array(
							  'Contact Email Address' => $customer_info_table->email_address
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
	  
	  if ($customer_info_table->email_check() > 0) {
		$error_message .= '<br>E-Mail has already been assigned to another customer. Please choose another.';
	  }
	  
	return $error_message;
	}

}

?>