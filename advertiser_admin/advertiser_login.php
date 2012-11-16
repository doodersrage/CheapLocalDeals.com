<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// load account login page class
if (!class_exists('advertiser_login_pg')) {
	require(CLASSES_DIR.'pages/advertiser_login.php');
	$advertiser_login_pg = new advertiser_login_pg;
}

$page_output = '<div class="shopping_cart_box">';
if ($_SESSION['advertiser_logged_in'] == 1) {
	$page_output .= '<center>You appear to already be logged in.</center>';
} else {
	if ($_POST['submit'] === 'Advertiser Sign In') {
		
		// check if customer is logged in
		if (!empty($_POST['username']) && !empty($_POST['password'])) {
			
			$adv_info_tbl->user_login_check();
			if ($_SESSION['advertiser_logged_in'] != 1) {
				$page_output .= create_warning_box('Either the username or password provided was invalid.');
				$page_output .= $advertiser_login_pg->draw_login_form();		
			} else {
				if($adv_info_tbl->email_authorized == 1) {
					if($adv_info_tbl->customer_level > 0) {
						$_SESSION['just_logged_in'] = 1;
						header("Location: ".SITE_SSL_URL."advertiser_admin/");		
					} else {
						header("Location: ".SITE_SSL_URL."advertiser_admin/create_account_user_level_select.deal");		
					}
				} else {
					$page_output .= create_warning_box('You have not yet authorized your account. Please authorize your account before logging in.');
					unset($_SESSION['advertiser_logged_in']);
					unset($_SESSION['advertiser_id']);
					unset($_SESSION['approved']);
					unset($_SESSION['customer_level']);
					unset($_SESSION['allow_multiple_logins']);
					$page_output .= $advertiser_login_pg->draw_login_form();
				}
			}
			
		} else {
			$page_output .= create_warning_box('You did not provide either a username or a password.');
			$page_output .= $advertiser_login_pg->draw_login_form();
		}
		
	} else {
		$page_output .= $advertiser_login_pg->draw_login_form();
	}
}
$page_output .= '</div>';

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Advertiser Account Login';
$page_meta_description = 'Advertiser Account Login';
$page_meta_keywords = 'Advertiser Account Login';

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