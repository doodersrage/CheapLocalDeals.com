<?PHP

// check for form submission and run form check routines
if ($_POST['submit'] === 'Update Address') {
	
	$customer_info_table->get_post_vars();

	$error_array = array();
	
	$required_fields = array(
						'Address 1' => 'address_1',
						'City' => 'city',
						'Zip Code' => 'zip',
						'Phone Number' => 'phone_number',
						'Email Address' => 'email_address',
						'First Name' => 'first_name',
						'Last Name' => 'last_name',
						);
	
	foreach($required_fields as $id => $value) {
		if(empty($_POST[$value])) {
			$error_array[] = $id;
		}
	}
	
	// if no errors are found process form
	if (count($error_array) == 0) {
		$customer_info_table->id = $_SESSION['customer_id'];
		$customer_info_table->update_address();
		
		$error_message = '<center><strong>Address information has been updated.</strong></center>';
	} else {		
		// print error message
		$error_message = '<center><strong>Errors were found with these fields: '.implode(', ',$error_array).'</strong></center>';
	}
}

// load customers information
$customer_info_table->get_db_vars($_SESSION['customer_id']);
	
$page_output = '';

$states_dd = gen_state_dd($customer_info_table->state);

$page_output .= '<div id="custLoginFrm">
<form id="update_address_frm" name="form1" method="post" action="">
<h1>Billing Address</h1>
<p>You\'re Almost done. So that you send you special coupons please enter your contact information below</p>';
$page_output .= (!empty($error_message) ? $error_message : '');
$page_output .= '<label>First Name:*</label>
<input name="first_name" value="'.$customer_info_table->first_name.'" type="text" id="first_name" size="30" maxlength="100" />
<label>Last Name:*</label>
<input name="last_name" value="'.$customer_info_table->last_name.'" type="text" id="last_name" size="30" maxlength="100" />
<label>Address 1:*</label>
<input name="address_1" value="'.$customer_info_table->address_1.'" type="text" id="address_1" size="30" maxlength="120" />
<label>Address 2:</label>
<input name="address_2" value="'.$customer_info_table->address_2.'" type="text" id="address_2" size="30" maxlength="120" />
<label>City:*</label>
<input name="city" value="'.$customer_info_table->city.'" type="text" id="city" size="30" maxlength="100" />
<label>State:*</label>
<select name="state" id="state">
'.$states_dd.'
</select>
<label>Zip:*</label>
<input name="zip" value="'.$customer_info_table->zip.'" type="text" id="zip" size="5" maxlength="5" />
<label>Phone Number:*</label>
<input name="phone_number" value="'.$customer_info_table->phone_number.'" type="text" id="phone_number" size="15" maxlength="15" />
<label>Email Address:*</label>
<input name="email_address" value="'.$customer_info_table->email_address.'" type="text" id="email_address" size="30" maxlength="160" />
<input class="submit_btn" type="submit" name="submit" value="Update Address" />
</form>
</div>';

?>