<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

if (isset($_POST['submit'])) {
  if ($_POST['submit'] === 'Checkout') {
	  
	  // check if customer is logged in
	  if ($_SESSION['customer_logged_in'] == 1) {
		  header("Location: ".SITE_SSL_URL."checkout/order_preview_payment.deal");	
	  } else {
		  header("Location: ".SITE_SSL_URL."customer_admin/account_login.deal");
	  }
	  
  }
}

// load categories list
if (!class_exists('shopping_cart_pg')) {
	require(CLASSES_DIR.'pages/shopping_cart.php');
	$shopping_cart_pg = new shopping_cart_pg;
}

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Shopping Cart';
$page_meta_description = 'shopping cart';
$page_meta_keywords = 'shopping cart';

// assign header constant
$prnt_header = new prnt_header();
$prnt_header->page_header_title = $page_header_title;
$prnt_header->page_meta_description = $page_meta_description;
$prnt_header->page_meta_keywords = $page_meta_keywords;
define('PAGE_HEADER',$prnt_header->print_page_header());

// page output
$page_output = '<script type="text/javascript" src="'.CONNECTION_TYPE.'includes/js/shopping_cart.js"></script><div class="shopping_cart_box">
				<div class="cart_header_border"><div class="cart_header">Shopping Cart</div></div></div>';
$page_output .= $shopping_cart_pg->display_shopping_cart();
$page_output .= '';

// start output buffer
ob_start();

	// load template
	require(TEMPLATE_DIR.'blank-wobox.php');
	
	$html = ob_get_contents();
	
ob_end_clean();

print_page($html);
?>