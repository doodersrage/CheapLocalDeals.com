<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// load forget_advertiser_password_pg page class
if (!class_exists('forget_advertiser_password_pg')) {
	require(CLASSES_DIR.'pages/forget_advertiser_password.php');
	$forget_advertiser_password_pg = new forget_advertiser_password_pg;
}

if ($_SESSION['advertiser_logged_in'] == 1) {
	$page_output = '<center>You appear to already be logged in.</center>';
} else {
	if ($_POST['submit'] === 'Generate Password') {
		
		// check if customer is logged in
		if (!empty($_POST['username']) && !empty($_POST['email'])) {
			
			if ($adv_info_tbl->user_forget_password_check() > 0) {
				$page_output = '<center><font color="red"><strong>Your new password has been generated and emailed to you.</strong></font></center>';
			} else {
				$page_output .= $forget_advertiser_password_pg->draw_forget_password_form();					
			}
			
		} else {
			$page_output = '<center><font color="red"><strong>In order to assign a new password you must first supply the username and email address assigned to the account.</strong></font></center>';
			$page_output .= $forget_advertiser_password_pg->draw_forget_password_form();
		}
		
	} else {
		$page_output = $forget_advertiser_password_pg->draw_forget_password_form();
	}
}

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Forget Advertiser Account Password Form';
$page_meta_description = 'Forget Password';
$page_meta_keywords = 'Forget Password';

// assign header constant
$prnt_header = new prnt_header();
$prnt_header->page_header_title = $page_header_title;
$prnt_header->page_meta_description = $page_meta_description;
$prnt_header->page_meta_keywords = $page_meta_keywords;
define('PAGE_HEADER',$prnt_header->print_page_header());

// start output buffer
ob_start();
	
	// load template
	require(TEMPLATE_DIR.'blank.php');
	
	$html = ob_get_contents();
	
ob_end_clean();

print_page($html);
?>