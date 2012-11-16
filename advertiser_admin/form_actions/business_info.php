<?PHP

// this document on submission will disable active certificates
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// process form on submit
if (isset($_POST['form_submit'])) {

	// if no errors are found start processing form
	$adv_info_tbl->reset_vars();
	// set posted vars
	$adv_info_tbl->get_post_vars();
	$adv_info_tbl->id = $_SESSION['advertiser_id'];
	// write values to database
	$adv_info_tbl->update_business();
	$adv_info_tbl->insert_selected_categories($_POST['category_select'],$adv_info_tbl->id);
	$page_output = 'Business Information Updated';
}

if(!empty($page_output)) {
	$page_output = create_warning_box($page_output);
}

echo $page_output;

?>