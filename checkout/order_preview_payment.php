<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// load categories list
if (!class_exists('order_preview_payment_pg')) {
	require(CLASSES_DIR.'pages/order_preview_payment.php');
	$order_preview_payment_pg = new order_preview_payment_pg;
}

// check if customer is logged in
if ($_SESSION['customer_logged_in'] != 1 || count($shopping_cart_manage->contents) == 0) {
	header("Location: ".SITE_SSL_URL."checkout/");
}

$error = '';
	
// check for order submission
if ($_POST['submit'] === 'Process Order') {
	
	$customer_info_table->get_db_vars($_SESSION['customer_id']);
	
	// if order total equals zero do not check payment information
	if (get_order_total() > 0) {
		  
	  // check if cc type is set
	  if (empty($_POST['credit_card_type'])) {
		  $error .= 'You have not chosen a credit card type.<br>';
	  }
	  
	  // check credit card value submission length
	  $cc_number = str_replace('-','',$_POST['cc_number']);
	  $cc_number = str_replace(' ','',$cc_number);
	  
	  if (strlen($cc_number) < 15) {
		  $error .= 'Credit Card number does not appear to be valid.<br>';
	  }
	  
	  // checks credit card date
	  if($_POST['cc_exp_month'] < date('n') && $_POST['cc_exp_year'] == date('Y')) {
		  $error .= 'Your credit card expiration date appears to be invalid.<br>';
	  }
	  
	  if(empty($_POST['cvv'])) {
		  $error .= 'Card verification value has been left blank.<br>';
	  }
	  
	  if(!empty($_POST['promo_code']) && $cust_promo_cds_tbl->promo_code_chk($_POST['promo_code']) == 0) {
		  $error .= 'The promo code entered does not appear to be valid.<br>';
	  }
	  
	  if(empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['address_1']) || empty($_POST['city']) || empty($_POST['state']) || empty($_POST['zip']) || empty($_POST['phone_number'])){
		  $error .= 'Some billing information appears to be missing.<br>';
	  } else {
		  $customer_info_table->first_name = $_POST['first_name'];
		  $customer_info_table->last_name = $_POST['last_name'];
		  $customer_info_table->address_1 = $_POST['address_1'];
		  $customer_info_table->address_2 = $_POST['address_2'];
		  $customer_info_table->city = $_POST['city'];
		  $customer_info_table->state = $_POST['state'];
		  $customer_info_table->zip = $_POST['zip'];
		  $customer_info_table->phone_number = $_POST['phone_number'];
		  $customer_info_table->update();
	  }
	}
	
//	if(empty($_POST['agree_check'])) {
//		$error .= 'You did not agree to the GIFT CERTIFICATE TERMS & CONDITIONS.<br>';
//	}
	
	// submit order if no errors were found
	if (empty($error)) {
		require(FUNCTIONS_DIR.'order_process.php');
	}
}

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Order Payment';
$page_meta_description = 'shopping cart payment';
$page_meta_keywords = 'shopping cart payment';

// assign header constant
$prnt_header = new prnt_header();
$prnt_header->page_header_title = $page_header_title;
$prnt_header->page_meta_description = $page_meta_description;
$prnt_header->page_meta_keywords = $page_meta_keywords;
define('PAGE_HEADER',$prnt_header->print_page_header());

// page output
$page_output = '<div class="shopping_cart_box">
				<div class="cart_header_border"><div class="cart_header">Order Payment</div></div>';
if (!empty($error)) $page_output .= create_warning_box($error);
$page_output .= $order_preview_payment_pg->display_shopping_cart();
$page_output .= '</div>';

// start output buffer
ob_start();
	
	// load template
	require(TEMPLATE_DIR.'blank-wobox.php');
	
	$html = ob_get_contents();
	
ob_end_clean();

print_page($html);
?>