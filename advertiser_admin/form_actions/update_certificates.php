<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// process form on submit
if (isset($_POST['form_submit'])) {
	
	$_POST['requirements'] = $_POST['cert_requirements'];

	// if no errors are found start processing form
	$adv_info_tbl->reset_vars();
	// set record id to update
	$adv_info_tbl->id = $_SESSION['advertiser_id'];
	// write values to database
	$adv_info_tbl->update_certificate_settings();
	$page_output .= '<center><font color="red">Certificate Amounts have been updated.</font></center>';
}

if(!empty($page_output)) {
	$page_output = create_warning_box($page_output);
}

echo $page_output;


?>