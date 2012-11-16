<?PHP
// this document on submission will disable active certificates
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

if ($_POST['submit'] === 'Disable Certificate') {
	
	if (!empty($_POST['certificate_id'])) {
		
		// disable selected certificate
		$cert_odrs_tbl->lookup_by_cert_code($_POST['certificate_id'],$_SESSION['advertiser_id']);
		if ($cert_odrs_tbl->id > 0) {
			$cert_odrs_tbl->password = $_POST['certificate_id'];
			$cert_odrs_tbl->enabled = 0;
			$cert_odrs_tbl->disable();
			$page_output = '<center><font color="red"><strong>Entered certificate has been disabled.</strong></font></center>';
		} else {
			$page_output = '<center><font color="red"><strong>The Certificate ID entered does not appear to exist.</strong></font></center>';
		}
	} else {
		$page_output = '<center><font color="red"><strong>You did not enter a Certificate ID.</strong></font></center>';
	}
	
}

if(!empty($page_output)) {
	$page_output = create_warning_box($page_output);
}

echo $page_output;

?>