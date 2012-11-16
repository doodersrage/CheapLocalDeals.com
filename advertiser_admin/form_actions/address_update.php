<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// load customers information
$adv_info_tbl->get_db_vars($_SESSION['advertiser_id']);

// clr page output
$page_output = '';

// check for form submission and run form check routines
if ($_POST['submit'] === 'Update Account') {
	
	$adv_info_tbl->get_post_vars();

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
		$adv_info_tbl->id = $_SESSION['advertiser_id'];
		$adv_info_tbl->update_address();
		
		$page_output .= '<center><strong>Address information has been updated.</strong></center>';
		
		// clear set vars
		$adv_info_tbl->reset_vars();
		
		// load customers information
		$adv_info_tbl->get_db_vars($_SESSION['advertiser_id']);
		
	} else {		
		// print error message
		$page_output .= '<center><strong>Errors were found with these fields: '.implode(', ',$error_array).'</strong></center>';
	}
}

if(!empty($page_output)) {
	$page_output = create_warning_box($page_output);
}

echo $page_output;

?>
