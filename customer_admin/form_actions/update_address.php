<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');
	
// check for form submission and run form check routines
if ($_POST['submit'] === 'Update Account') {
	
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
		
		$page_output = '<center><strong>Address information has been updated.</strong></center>';
	} else {		
		// print error message
		$page_output = '<center><strong>Errors were found with these fields: '.implode(', ',$error_array).'</strong></center>';
	}
}

if(!empty($page_output)) {
	$page_output = create_warning_box($page_output);
}

echo $page_output;

?>