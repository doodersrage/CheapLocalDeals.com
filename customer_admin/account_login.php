<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// load account login page class
if (!class_exists('account_login_pg')) {
	require(CLASSES_DIR.'pages/account_login.php');
	$account_login_pg = new account_login_pg;
}

$page_output = '<div class="shopping_cart_box">';
if ($_SESSION['customer_logged_in'] == 1) {
	$page_output .= '<center>You appear to already be logged in.</center>';
} else {
	if ($_POST['submit'] === 'Customer Sign In') {
		
		// check if customer is logged in
		if (!empty($_POST['email_address']) && !empty($_POST['password'])) {
			
			$customer_info_table->user_login_check();
			if ($_SESSION['customer_logged_in'] != 1) {
				$page_output .= create_warning_box('Either your email address or password was invalid.');
				$page_output .= $account_login_pg->draw_login_form();		
			} else {
				if (count($shopping_cart_manage->contents) > 0) {
					header("Location: ".SITE_SSL_URL."checkout/");							
				} else {
					header("Location: ".SITE_SSL_URL."customer_admin/");			
				}
			}
			
		} else {
			$page_output .= create_warning_box('You did not provide either a email address or a password.');
			$page_output .= $account_login_pg->draw_login_form();
		}
		
	} else {
		$page_output .= $account_login_pg->draw_login_form();
	}
}
$page_output .= '</div>';

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Account Login';
$page_meta_description = 'Account Login';
$page_meta_keywords = 'Account Login';

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