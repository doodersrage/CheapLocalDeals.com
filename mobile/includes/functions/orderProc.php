<?PHP

// this document processes customers orders
$customer_info_table->reset_vars();
$customer_info_table->get_db_vars($_SESSION['customer_id']);

// get new order_id
$sql_query = "SELECT
				id
			 FROM
				orders
			 ORDER BY id DESC
			 LIMIT 1
			 ;";
$rows = $dbh->queryRow($sql_query);

$new_order_id = $rows['id']+1;

// set order total amount
$total_amount = get_order_total();

if($total_amount > 0) {
  // loads payment gateway classes
  require(LIBS_DIR.'payment/lphp.php');
  $mylphp=new lphp;
  
  // payment gateway config/processing
  $myorder['host'] = GATEWAY_SECURE_HOST;
  $myorder['port'] = GATEWAY_PORT;
  $myorder['configfile'] = GATEWAY_STORE_NUMBER;
  $myorder['keyfile'] = GATEWAY_KEYFILE;
  
  // sets payment data
  // sets type of order
  $myorder['ordertype'] = 'SALE';
  // sets process type live/test
  $myorder['result'] = (GATEWAY_LIVE_MODE == 1 ? 'LIVE' : 'GOOD');
  
  // set credit card information
  $myorder['cardnumber'] = $_POST['cc_number'];
  $myorder['cardexpmonth'] = $_POST['cc_exp_month'];
  $myorder['cardexpyear'] = substr($_POST['cc_exp_year'],-2);
  $myorder['cvmvalue'] = $_POST['cvv'];
  $myorder['cvmindicator'] = 'provided';
  
  // set billing information
  $myorder['name'] = $customer_info_table->first_name . ' ' . $customer_info_table->last_name;
  $myorder['address1'] = $customer_info_table->address_1;
  $myorder['address2'] = $customer_info_table->address_2;
  $myorder['city'] = $customer_info_table->city;
  $myorder['state'] = $customer_info_table->state;
  //$myorder['sstate'] = $customer_info_table->state;
  $myorder['zip'] = $customer_info_table->zip;
  $myorder['country'] = 'US';
  $myorder['userid'] = $customer_info_table->id;
  $myorder['email'] = $customer_info_table->email_address;
  $myorder['phone'] = $customer_info_table->phone_number;
  $myorder['fax'] = $customer_info_table->fax_number;
  
  // set transaction details
  $myorder['oid'] = $new_order_id;
  
  $myorder['terminaltype'] = 'UNSPECIFIED';
  $myorder['ip'] = $_SERVER['REMOTE_ADDR'];
  $myorder['transactionorigin'] = 'ECI';
  
  // apply order total to processing
  if(TAX_STATE_VAL == $customer_info_table->state && TAX_APPLY_VALUE == 1) {
	$myorder['taxexempt'] = 'N';
	$myorder['subtotal'] = $shopping_cart_manage->sub_total;
	$myorder['tax'] = $tax_amount;
	$myorder['chargetotal'] = $total_amount;
  } else {
	$myorder['chargetotal'] = $total_amount;
	$myorder['taxexempt'] = 'Y';
	$myorder['tax'] = 0;
  }
  
  
  // set order items
  reset($shopping_cart_manage->contents);
  
  $payment_item_id = 0;
  
  //// insert order_items
  //foreach($shopping_cart_manage->contents as $values) {
  //
  //	// update payment order id
  //	$payment_item_id++;
  //
  //	// set advertisers values
  //	$adv_info_tbl->reset_vars();
  //	$adv_info_tbl->get_db_vars($values['company_id']);
  //	
  //	$requirement_value = '';
  //	
  //	// sets item requirements
  //	$requirement_type = $adv_info_tbl->certificate_requirements[$values['certificate_amount_id']]['type'];
  //	$requirement_value = $adv_info_tbl->certificate_requirements[$values['certificate_amount_id']]['value'];
  //	
  //	switch($requirement_type) {
  //	case 1:
  //		$requirement_value = 'Valid with purchase of ' . $requirement_value;
  //	break;
  //	case 2:
  //		$requirement_value = 'Valid with minimum spend of $' . $requirement_value;
  //	break;
  //	case 3:
  //		$requirement_value = 'Valid with '.$requirement_value;
  //	break;
  //	}
  //	
  //	$odr_itms_tbl->reset_vars();
  //	$cert_amt_tbl->reset_vars();
  //	
  //	$cert_amt_tbl->get_db_vars($values['certificate_amount_id']);
  //	
  //	$myorder["items"]["item".$payment_item_id]["id"] = $values['company_id'];
  //	$myorder["items"]["item".$payment_item_id]["description"] = $adv_info_tbl->company_name . ' ' . $cert_amt_tbl->discount_amount . ' ' . $requirement_value;
  //	$myorder["items"]["item".$payment_item_id]["quantity"] = $values['item_quantity'];
  //	$myorder["items"]["item".$payment_item_id]["price"] = $values['item_price'];
  //
  //}
  
  // debugging
  //$myorder["debugging"]="true";
  
  // Send transaction. Use one of two possible methods #
  // $result = $mylphp->process($myorder); # use shared library model
  $result = $mylphp->curl_process($myorder); # use curl methods
}

if ($result["r_approved"] == "APPROVED" || $total_amount == 0) // transaction failed, print the reason
{

	// deduct amount from customers available balance
	if($customer_info_table->balance > 0) {
		$balance_order_sum = $customer_info_table->balance-get_order_total_for_coupon();
		$balance_order_sum = ($balance_order_sum < 0 ? 0 : $balance_order_sum);
		$customer_info_table->balance = $balance_order_sum;
		$customer_info_table->update_balance();
	}

	// assign order variables
	$customer_email = $customer_info_table->email_address;

	// set order table vars
	$odrs_tbl->customer_id = $_SESSION['customer_id'];
	$odrs_tbl->order_total = $total_amount;
	$odrs_tbl->payment_method = 'credit_card';
	$odrs_tbl->credit_card_type = $_POST['credit_card_type'];
	$odrs_tbl->credit_card_number = $_POST['cc_number'];
	$odrs_tbl->cvv = $_POST['cvv'];
	$odrs_tbl->expiration_date = $_POST['cc_exp_month'].'/'.$_POST['cc_exp_year'];
	$odrs_tbl->payment_approved = 1;
	$odrs_tbl->order_notes = serialize($result);
	$odrs_tbl->promo_code = $_POST['promo_code'];
	$odrs_tbl->api_id = $api_ref_chk->api_id;
	$odrs_tbl->insert();
	
	reset($shopping_cart_manage->contents);
	
	// insert order_items
	foreach($shopping_cart_manage->contents as $values) {
	
		// set advertisers values
		$adv_info_tbl->reset_vars();
		$adv_info_tbl->get_db_vars($values['company_id']);
		
		$requirement_value = '';
		
		// sets item requirements
		$requirement_type = $adv_info_tbl->certificate_requirements[$values['certificate_amount_id']]['type'];
		$requirement_value = $adv_info_tbl->certificate_requirements[$values['certificate_amount_id']]['value'];
		
		// set cert req string
		$requirement_value = set_cert_agreement_str($requirement_type,$requirement_value);
		
		$odr_itms_tbl->reset_vars();
		
		$odr_itms_tbl->order_id = $new_order_id;
		$odr_itms_tbl->item_id = $values['company_id'];
		$odr_itms_tbl->certificate_value_id = $values['certificate_amount_id'];
		$odr_itms_tbl->item_value = $values['item_price'];
		$odr_itms_tbl->item_quantity = $values['item_quantity'];
		
		// insert new record
		$odr_itms_tbl->insert();
		
		// create certificate entry
		for($i = 1; $i <= $values['item_quantity']; $i++) {
			// reset cert orders vals
			$cert_odrs_tbl->reset_vars();
					
			// assign cert orders var vals
			$cert_odrs_tbl->order_id = $new_order_id;
			$cert_odrs_tbl->customer_id = $_SESSION['customer_id'];
			$cert_odrs_tbl->advertiser_id = $values['company_id'];
			$cert_odrs_tbl->requirements = $requirement_value;
			$cert_odrs_tbl->excludes = $adv_info_tbl->certificate_requirements[$values['certificate_amount_id']]['excludes'];
			$cert_odrs_tbl->certificate_amount_id = $values['certificate_amount_id'];
			$cert_odrs_tbl->certificate_code = $cert_odrs_tbl->generate_certificate_code();
			$cert_odrs_tbl->enabled = 1;
			
			// insert new certificate
			$cert_odrs_tbl->insert();
		}
	}
	
	// load categories list
	if (!class_exists('shopping_cart_pg')) {
		require(CLASSES_DIR.'pages/shopping_cart.php');
		$shopping_cart_pg = new shopping_cart_pg;
	}
	
	reset($shopping_cart_manage->contents);
		
	$shopping_cart_pg->shopping_cart_list();
	
	$customer_order_data = '<link rel="stylesheet" type="text/css" href="https://www.cheaplocaldeals.com/css_select.deal?css_doc=includes%2Ftemplate%2Fstyle.css" media="screen" />
	'.$shopping_cart_pg->shopping_cart_list();
	
	$customer_order_data .= '<p>Certificate Download Links: (Click the links below to download your new certificates or download them through the account management system.)</p>';

	// this section generates a new category for all cities within the site
	$sql_query = "SELECT
					id
				 FROM
					certificate_orders
				 WHERE
				 	order_id = ?
				 ;";
			 
	$values = array(
					$new_order_id
					);

	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute($values);

	while($cur_cert = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
		$cert_odrs_tbl->get_db_vars($cur_cert['id']);
		$customer_order_data .= '<a href="'.SITE_URL.'customer_admin/view_certificate.deal?cert_id='.$cert_odrs_tbl->cert_id.'">'.SITE_URL.'customer_admin/view_certificate.deal?cert_id='.$cert_odrs_tbl->cert_id.'</a><br/>';
	}

	$email_replace_arr = array($customer_order_data);
	$email_replace_str_arr = array('CUSTOMER_ORDER_INFO');

	$html = str_replace($email_replace_str_arr,$email_replace_arr,CUSTOMER_ORDER_EMAIL);
	
	$email_data = array();
	$email_data['content'] = $html;
	$email_data['from_address'] = SITE_FROM_ADDRESS;
	$email_data['subject'] = SITE_NAME_VAL." Order ".date("m-d-Y");
	$email_data['to_addresses'] = $customer_email;
	
	// send email
	send_email($email_data); 
		
	// clear existing cart contents
	$shopping_cart_manage->clear_contents();
	
	// redirect to thank you page
	header("Location: ".MOB_SSL_URL."?action=checkSuccess");
	
} else { 
// failure 
//	ob_start();
//		
//		print_r($myorder);
//		print_r($result);
//		
//		$result_str = ob_get_contents();
//		
//	ob_end_clean();
	
	$error = "<center><strong>The provided payment information does not appear to be valid.</strong></center> ".$result_str;
}

?>