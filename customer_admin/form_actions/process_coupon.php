<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

if ($_SESSION['customer_logged_in'] != 1) header("Location: ".SITE_SSL_URL."advertiser_admin/advertiser_login.deal");

// load customers information
$customer_info_table->get_db_vars($_SESSION['customer_id']);
$cust_cpns_tbl->look_up_coupon($_POST['coupon_code']);

if(!empty($_POST['coupon_code'])) {
  if($cust_cpns_tbl->id > 0) {
	  if ((strtotime($cust_cpns_tbl->expires) >= strtotime(date("Y-m-d"))) && $cust_cpns_tbl->used == 0) {
		$cust_cpns_tbl->used = 1;
		$cust_cpns_tbl->used_by_cust_id = $customer_info_table->id;
		$cust_cpns_tbl->used_date = date("Y-m-d");
		$cust_cpns_tbl->update_coupon_cust();
		
		$new_balance = $customer_info_table->balance + $cust_cpns_tbl->value;
		
		$stmt = $dbh->prepare("UPDATE customer_info SET balance = '".$new_balance."' WHERE id = '".$customer_info_table->id."';");
		$stmt->execute();
		
		$message = 'Coupon accepted and your balance has been updated.';
	  } else {
		$message = 'The entered coupon has expired or already been used.';
	  }
  } else {
	$message = 'Entered coupon code was not found.';
  }
} else {
  $message = 'You did not enter a coupon code.';
}

echo create_warning_box($message);
?>