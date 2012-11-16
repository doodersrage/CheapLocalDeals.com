<?PHP

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
		
		$error_message = 'Coupon accepted and your balance has been updated.';
	  } else {
		$error_message = 'The entered coupon has expired or already been used.';
	  }
  } else {
	$error_message = 'Entered coupon code was not found.';
  }
}

$page_output = '<p>Your current account balance: <font color="#FF0000"><strong>$'.format_currency($customer_info_table->balance).'</strong></font></p>';

$page_output .= '<div  id="custLoginFrm">';
$page_output .= (!empty($error_message) ? '<center><strong><font color="red">'.$error_message.'</font></strong></center>' : '');
$page_output .= '<form name="login_form" method="post">
	<p>Enter a provided coupon code to increase your available balance.<p>
	<p><input name="coupon_code" id="coupon_code" type="text" size="10" maxlength="10" /></p>
    <p><input class="submit_btn" name="process_coupon" type="submit" value="Process" />
	</form></div>';
?>