<?PHP
// this document runs daily and processes advertiser memberships

// load application header
require('application_top.php');

// assign new email class
$message = new Mail_mime();

// reporting settings
$display_free_adverts = 0;
$display_non_paying_premium = 0;

// clear report output
$payment_data_output = '';

// loads payment gateway classes
require(LIBS_DIR.'payment/lphp.php');
$mylphp=new lphp;

// pull advertiser info
$sql_query = "SELECT
				id
			 FROM
				advertiser_info
			 WHERE
			 	customer_level > 0
			 AND
			 	approved = 1
			 ;";
	$rows = $dbh->queryAll($sql_query);

// run through each 
foreach($rows as $advertisers) {
	
	// reset payment data string
	$result = '';
	$payment_data = '';
	$payment_problem = 0;
	$myorder = array();
	
	// resets script timeout on each run
	set_time_limit(999);
	
	// pull advertiser info
	$adv_info_tbl->get_db_vars($advertisers['id']);
	
	$payment_data .= 'Processing: '.$adv_info_tbl->company_name.' ... '.$adv_info_tbl->first_name.' '.$adv_info_tbl->last_name;
	
	// assign levels data
	$adv_lvls_tbl->get_db_vars($adv_info_tbl->customer_level);

	$payment_data .= ' ... level: '.$adv_lvls_tbl->level_name.' ';

	// if payment information is filled out start processing payment
	if ($adv_info_tbl->customer_level != 3) { 
	
	// bin switch defines if order should be processed or skipped this time around
	$process_payment = 0;
	
	
	// calculate payment
	// check for upfront payment
	$payment_val = $adv_lvls_tbl->level_upfront_cost;
	
	// get past payments count
	$payment_cnt_query = "SELECT
							count(*) as rcount
						 FROM
							membership_process
						 WHERE
							advertiser_id = '".$advertisers['id']."'
						 AND 
							advertiser_level = '".$adv_info_tbl->customer_level."'
						 ;";
	$payment_cnt_rows = $dbh->queryRow($payment_cnt_query);
	// pull recent payment data
	$payment_query = "SELECT
						id,
						advertiser_id,
						advertiser_level,
						date,
						payment,
						cc_type,
						payment_approved,
						other_info
					 FROM
						membership_process
					 WHERE
						advertiser_id = '".$advertisers['id']."'
					 AND 
						advertiser_level = '".$adv_info_tbl->customer_level."'
					 ORDER BY date DESC
					 LIMIT 1
					 ;";
	$payment_rows = $dbh->queryRow($payment_query);

	if ($payment_cnt_rows['rcount'] > 0) {
	  
	  // get days since last payment
	  $days = ceil((strtotime(date("Y-m-d")) - strtotime($payment_rows['date'])) / 60 / 60 / 24);
	  
	  // set renewal days remaining
	  $current_level_cycle = ($payment_cnt_rows['rcount'] * 30) + $days;
	  
	  // set days until next renewal
	  $days_till_renewal = ceil(($adv_lvls_tbl->level_duration * 30) - $current_level_cycle);
	  	  
	  // send advertiser renewal notice if remaining days is = 7
	  if($days_till_renewal == 7) {
		$email_data = array();
		$email_data['content'] = ADVERTISER_RENEWAL_EMAIL;
		$email_data['from_address'] = SITE_FROM_ADDRESS;
		$email_data['subject'] = SITE_NAME_VAL." Advertiser Account Signup ".date("m-d-Y");
		$email_data['to_addresses'] = $adv_info_tbl->email_address;
		
		// send email
		send_email($email_data); 
	  }
	  	  
	  // set upfront payment days delay
	  $upfront_days_cnt = ceil($adv_lvls_tbl->level_duration * 30);

	  // check for upfront billing initial payment
	  if ($payment_cnt_rows['rcount'] >= 1 && $days >= $upfront_days_cnt && $adv_info_tbl->customer_level_renew == 1) {
		  $process_payment = 1;
		  $payment_val = $adv_lvls_tbl->level_renewal_cost;
		  $payment_data .= ' ... advertiser renewal charge ';
	  // switch to renewal cost if initial period is exceeded and advertiser has accepted renewal
	  } elseif($payment_cnt_rows['rcount'] >= 1 && $days == $upfront_days_cnt && $adv_info_tbl->customer_level_renew == 1) {
		  $process_payment = 0;
		  // reset level to free account
		  $adv_info_tbl->hours_operation = serialize($adv_info_tbl->hours_operation);
		  $adv_info_tbl->payment_options = serialize($adv_info_tbl->payment_options);
		  $adv_info_tbl->certificate_requirements = serialize($adv_info_tbl->certificate_requirements);
		  $adv_info_tbl->certificate_levels = serialize($adv_info_tbl->certificate_levels);
		  $adv_info_tbl->customer_level = 3;
		  $adv_info_tbl->update();
		  $payment_data .= ' ... advertiser renewal notice ';
		  
			$email_data = array();
			$email_data['content'] = PREMIUM_ACC_EXPIRE_EMAIL;
			$email_data['from_address'] = SITE_FROM_ADDRESS;
			$email_data['subject'] = SITE_NAME_VAL." Advertiser Account Expiration ".date("m-d-Y");
			$email_data['to_addresses'] = $adv_info_tbl->email_address;
			
			// send email
			send_email($email_data); 
	  // modified to reset customer level to free
	  } elseif($payment_cnt_rows['rcount'] >= 1 && $days >= $upfront_days_cnt && $adv_info_tbl->customer_level_renew != 1) {
		  $process_payment = 0;
		  // reset level to free account
		  $adv_info_tbl->hours_operation = serialize($adv_info_tbl->hours_operation);
		  $adv_info_tbl->payment_options = serialize($adv_info_tbl->payment_options);
		  $adv_info_tbl->certificate_requirements = serialize($adv_info_tbl->certificate_requirements);
		  $adv_info_tbl->certificate_levels = serialize($adv_info_tbl->certificate_levels);
		  $adv_info_tbl->customer_level = 3;
		  $adv_info_tbl->update();
		  $payment_data .= ' ... advertiser renewal notice ';
	  }
	  
	  $renewal_date = date("Y-m-d",strtotime("+30 days"));

	// if no payments have been made for this advertiser process for the first time
	} else {
		
		// 9/10/2009 added to apply promocode to first months order
		if($adv_info_tbl->promo_code != '') {
			$adv_pro_codes_tbl->assign_db_vars_procode($adv_info_tbl->promo_code);
			$payment_val = $payment_val-($payment_val*($adv_pro_codes_tbl->percentage/100));
		}
		
		// assign discounts to first payment amount
		// check for link back status
		if($adv_info_tbl->link_partner == 1) {
			$payment_val += $adv_lvls_tbl->upfront_level_link_back;
		}
		
		// check for bbb status
		if($adv_info_tbl->bbb_member == 1) {
			$payment_val += $adv_lvls_tbl->upfront_bbb_member_price;
		}
	
		$process_payment = 1;
		// set renewal date
		$renewal_days = $adv_lvls_tbl->level_duration * 30;
		$renewal_date = date("Y-m-d",strtotime("+".$renewal_days." days"));
		$expiration_date = $renewal_date;
		// sets level expiration date
		$sql_query_update = "UPDATE
								advertiser_info
							 SET
								customer_level_exp = ?
							 WHERE
								id = ?
							 ;";
		$update_vals = array(
							$expiration_date,
							$advertisers['id']
							);
		$stmt = $dbh->prepare($sql_query_update);					 
		$stmt->execute($update_vals);
		
		$payment_data .= ' ... new advertiser ';
		// begin problem checking
		$payment_problem++;

	}
	
	// assign cctype - also used for check processing
	if ($adv_info_tbl->payment_method == 'Check' && $process_payment == 1) {
		$payment_method = $adv_info_tbl->payment_method;
		
		if($adv_info_tbl->check_routing_num != '' && $adv_info_tbl->check_account_num != '' && $adv_info_tbl->bank_name != '' && $adv_info_tbl->drivers_license_num != '') {
			// process payment with payment gateway
			// payment gateway config/processing
			$myorder['host'] = GATEWAY_SECURE_HOST;
			$myorder['port'] = GATEWAY_PORT;
			$myorder['configfile'] = GATEWAY_STORE_NUMBER;
			$myorder['keyfile'] = 'libs/payment/1001202828.pem';
			
			// sets payment data
			// sets type of order
			$myorder['ordertype'] = 'SALE';
			// sets process type live/test
			$myorder['result'] = (GATEWAY_LIVE_MODE == 1 ? 'LIVE' : 'GOOD');
			
			// set cc ecpiration
			$cc_expire = explode('/',$adv_info_tbl->cc_exp);
			
			// set checking information
			$myorder['accounttype'] = $adv_info_tbl->check_account_type;
			$myorder['account'] = $adv_info_tbl->check_account_num;
			$myorder['routing'] = $adv_info_tbl->check_routing_num;
			$myorder['bankname'] = $adv_info_tbl->bank_name;
			$myorder['bankstate'] = $adv_info_tbl->bank_state;
			$myorder['dl'] = $adv_info_tbl->drivers_license_num;
			$myorder['dlstate'] = $adv_info_tbl->drivers_license_state;
		
			$myorder['name'] = $adv_info_tbl->first_name . ' ' . $adv_info_tbl->last_name;
			$myorder['address1'] = $adv_info_tbl->address_1;
			$myorder['address2'] = $adv_info_tbl->address_2;
			$myorder['city'] = $adv_info_tbl->city;
			$myorder['state'] = $adv_info_tbl->state;
			$myorder['zip'] = $adv_info_tbl->zip;
			$myorder['country'] = 'US';
			$myorder['userid'] = $adv_info_tbl->id;
			$myorder['email'] = $adv_info_tbl->email_address;
			$myorder['phone'] = $adv_info_tbl->phone_number;
			$myorder['fax'] = $adv_info_tbl->fax_number;
		
			// set transaction details
			$myorder['oid'] = $new_order_id;
			$myorder['taxexempt'] = 'Y';
			$myorder['terminaltype'] = 'UNSPECIFIED';
			$myorder['transactionorigin'] = 'ECI';
			
			// set payment data
			$myorder['chargetotal'] = $payment_val;
			$myorder['tax'] = 0;
			
			// Send transaction. Use one of two possible methods #
			// $result = $mylphp->process($myorder); # use shared library model
			$result = $mylphp->curl_process($myorder); # use curl methods
	
	//		// print payment info for testing
	//		print_r($result);
	
			if ($result["r_approved"] == "APPROVED") // transaction failed, print the reason
			{
				// payment information accepted
				$process_payment = 1;
				
				// sends payment email to advertiser
				$html = ADVERT_PAY_PROCESS_PASS;
				$html .= '<br/><br/>Payment Info:'."<br/>";
				$html .= 'Amount: $'.$payment_val."<br/>";
				
				//$message->setTXTBody($text);
				$message->setHTMLBody($html);
				$body = $message->get();
				$extraheaders = array("From"=>SITE_FROM_ADDRESS, "Subject"=>SITE_NAME_VAL." Advertiser Account Payment Approved ".date("m-d-Y"));
				$headers = $message->headers($extraheaders);
				
				$mail = Mail::factory("mail");
				$mail->send($adv_info_tbl->email_address, $headers, $body);
				
				$payment_data .= ' ... checking payment approved $'.$payment_val.' ';
			} else {
				// payment information failed to process
				$process_payment = 0;
				// assign problem value
				$payment_problem += 2;
				
				// email advertiser and site admin to warn of payment method failure
				// sends warning email to advertiser
				$html = ADVERT_PAY_PROCESS_FAIL;
								
				$email_data = array();
				$email_data['content'] = $html;
				$email_data['from_address'] = SITE_FROM_ADDRESS;
				$email_data['subject'] = SITE_NAME_VAL." Advertiser Account Payment Failure ".date("m-d-Y");
				$email_data['to_addresses'] = $adv_info_tbl->email_address;
				
				// send email
				send_email($email_data); 
						
//				// send email to site admin
//				$html = ADVERT_PAY_PROCESS_FAIL;
				
				$html = '<br/><br/>Advertiser Info:'."<br/>";
				$html .= 'Company Name: '.$adv_info_tbl->company_name."<br/>";
				$html .= 'Company Description: '.nl2br($adv_info_tbl->customer_description)."<br/>";
				$html .= 'Name: '.$adv_info_tbl->first_name.' '.$adv_info_tbl->last_name."<br/>";
				$html .= 'Address 1: '.$adv_info_tbl->address_1."<br/>";
				$html .= 'Address 2: '.$adv_info_tbl->address_2."<br/>";
				$html .= 'City: '.$adv_info_tbl->city."<br/>";
				$html .= 'State: '.$adv_info_tbl->state."<br/>";
				$html .= 'Zip: '.$adv_info_tbl->zip."<br/>";
				$html .= 'Phone number: '.$adv_info_tbl->phone_number."<br/>";
				$html .= 'Email Address: '.$adv_info_tbl->email_address."<br/>";
				$html .= 'Affiliate Code: '.$adv_info_tbl->affiliate_code."<br/>";
				$html .= '<br/><br/>Payment Info:'."<br/>";
				$html .= 'Checking Account Number: ' . $adv_info_tbl->check_account_num."<br/>";
				$html .= 'Routing Number: ' . $adv_info_tbl->check_routing_num."<br/>";
				$html .= 'Bank Name: ' . $adv_info_tbl->bank_name."<br/>";
				$html .= 'Bank State: ' . $adv_info_tbl->bank_state."<br/>";
				$html .= 'Drivers License: ' . $adv_info_tbl->drivers_license_num."<br/>";
				$html .= 'Drivers License State: ' . $adv_info_tbl->drivers_license_state."<br/>";
				$html .= '<br/><br/><a href="https://www.cheaplocaldeals.com/admin/?sect=retcustomer&mode=edit&cid='.$adv_info_tbl->id.'">Update Advertiser Information</a>'."<br/>";
			
				$email_string = str_replace('ADVERT_INFO_STRING',$html,ADVERT_PAY_PROCESS_FAIL);
			
				$email_data = array();
				$email_data['content'] = $email_string;
				$email_data['from_address'] = SITE_FROM_ADDRESS;
				$email_data['subject'] = SITE_NAME_VAL." Advertiser Account Payment Failure ".date("m-d-Y");
				$email_data['to_addresses'] = SITE_CONTACT_EMAILS;
				
				// send email
				send_email($email_data); 
				
				// disable account until an admin approves changes
				$sql_query_update = "UPDATE
										advertiser_info
									 SET
										approved = ?
									 WHERE
										id = ?
									 ;";
						 
				$update_vals = array(
									0,
									$advertisers['id']
									);
				
				$stmt = $dbh->prepare($sql_query_update);					 
				$stmt->execute($update_vals);
				
				// save failure data for later review
				$memb_proc_fld_tbl->advertiser_id = $advertisers['id'];
				$memb_proc_fld_tbl->advertiser_level = $adv_info_tbl->customer_level;
				$memb_proc_fld_tbl->date = date("Y-m-d");
				$memb_proc_fld_tbl->payment = $payment_val;
				$memb_proc_fld_tbl->payment_method = $adv_info_tbl->payment_method;
				$memb_proc_fld_tbl->cc_type = $adv_info_tbl->credit_card_type;
				$memb_proc_fld_tbl->payment_approved = 1;
				$memb_proc_fld_tbl->other_info = (!empty($result) ? serialize($result) : '');
				$memb_proc_fld_tbl->insert();
						
			  $payment_data .= ' ... checking payment declined ';
			}
		} else {
			// sends payment email to advertiser
//			$html = ADVERT_PAY_PROCESS_PASS;
			$html = '<br/><br/>Payment Info:'."<br/>";
			$html .= 'Amount: $'.$payment_val."<br/>";
			
			$email_string = str_replace('ADVERT_INFO_STRING',$html,ADVERT_PAY_PROCESS_PASS);

			$email_data = array();
			$email_data['content'] = $email_string;
			$email_data['from_address'] = SITE_FROM_ADDRESS;
			$email_data['subject'] = SITE_NAME_VAL." Advertiser Account Payment Approved ".date("m-d-Y");
			$email_data['to_addresses'] = $adv_info_tbl->email_address;
			
			// send email
			send_email($email_data); 
		
			 $payment_data .= ' ... checking payment accepted $'.$payment_val.' ';
		 }
		// process credit card payments
	} elseif($adv_info_tbl->payment_method == 'Credit Card' && !empty($adv_info_tbl->credit_card_type) && !empty($adv_info_tbl->cc_number) && !empty($adv_info_tbl->cvv) && !empty($adv_info_tbl->cc_exp) && $process_payment == 1) {
		$payment_method = $adv_info_tbl->payment_method;
		
		// process payment with payment gateway
		// payment gateway config/processing
		$myorder['host'] = GATEWAY_SECURE_HOST;
		$myorder['port'] = GATEWAY_PORT;
		$myorder['configfile'] = GATEWAY_STORE_NUMBER;
		$myorder['keyfile'] = 'libs/payment/1001202828.pem';
		
		// sets payment data
		// sets type of order
		$myorder['ordertype'] = 'SALE';
		// sets process type live/test
		$myorder['result'] = (GATEWAY_LIVE_MODE == 1 ? 'LIVE' : 'GOOD');
		
		// set cc ecpiration
		$cc_expire = explode('/',$adv_info_tbl->cc_exp);
		
		// set credit card information
		$myorder['cardnumber'] = $adv_info_tbl->cc_number;
		$myorder['cardexpmonth'] = $cc_expire[0];
		$myorder['cardexpyear'] = substr($cc_expire[1],-2);
		$myorder['cvmvalue'] = $adv_info_tbl->cvv;
		$myorder['cvmindicator'] = 'provided';
	
		$myorder['name'] = $adv_info_tbl->first_name . ' ' . $adv_info_tbl->last_name;
		$myorder['address1'] = $adv_info_tbl->address_1;
		$myorder['address2'] = $adv_info_tbl->address_2;
		$myorder['city'] = $adv_info_tbl->city;
		$myorder['state'] = $adv_info_tbl->state;
		$myorder['zip'] = $adv_info_tbl->zip;
		$myorder['country'] = 'US';
		$myorder['userid'] = $adv_info_tbl->id;
		$myorder['email'] = $adv_info_tbl->email_address;
		$myorder['phone'] = $adv_info_tbl->phone_number;
		$myorder['fax'] = $adv_info_tbl->fax_number;
	
		// set transaction details
		$myorder['oid'] = $new_order_id;
		$myorder['taxexempt'] = 'Y';
		$myorder['terminaltype'] = 'UNSPECIFIED';
		$myorder['transactionorigin'] = 'ECI';
		
		// set payment data
		$myorder['chargetotal'] = $payment_val;
		$myorder['tax'] = 0;
		
		// Send transaction. Use one of two possible methods #
		// $result = $mylphp->process($myorder); # use shared library model
		$result = $mylphp->curl_process($myorder); # use curl methods

//		// print payment info for testing
//		print_r($result);

		if ($result["r_approved"] == "APPROVED") // transaction failed, print the reason
		{
			// payment information accepted
			$process_payment = 1;
			
			// sends payment email to advertiser
//			$html = ADVERT_PAY_PROCESS_PASS;
			$html = '<br/><br/>Payment Info:'."<br/>";
			$html .= 'Amount: $'.$payment_val."<br/>";

			$email_string = str_replace('ADVERT_INFO_STRING',$html,ADVERT_PAY_PROCESS_PASS);
			
			$email_data = array();
			$email_data['content'] = $email_string;
			$email_data['from_address'] = SITE_FROM_ADDRESS;
			$email_data['subject'] = SITE_NAME_VAL." Advertiser Account Payment Approved ".date("m-d-Y");
			$email_data['to_addresses'] = $adv_info_tbl->email_address;
			
			// send email
			send_email($email_data); 
		  	
			$payment_data .= ' ... credit card payment approved $'.$payment_val.' ';
		} else {
			// payment information failed to process
			$process_payment = 0;
			// assign problem value
		  	$payment_problem += 2;
			
			// email advertiser and site admin to warn of payment method failure
			// sends warning email to advertiser
			$html = ADVERT_PAY_PROCESS_FAIL;
			
			$email_data = array();
			$email_data['content'] = $html;
			$email_data['from_address'] = SITE_FROM_ADDRESS;
			$email_data['subject'] = SITE_NAME_VAL." Advertiser Account Payment Failure ".date("m-d-Y");
			$email_data['to_addresses'] = $adv_info_tbl->email_address;
			
			// send email
			send_email($email_data); 
			
			// send email to site admin
//			$html = ADVERT_PAY_PROCESS_FAIL;
			
			$html = '<br/><br/>Advertiser Info:'."<br/>";
			$html .= 'Company Name: '.$adv_info_tbl->company_name."<br/>";
			$html .= 'Company Description: '.nl2br($adv_info_tbl->customer_description)."<br/>";
			$html .= 'Name: '.$adv_info_tbl->first_name.' '.$adv_info_tbl->last_name."<br/>";
			$html .= 'Address 1: '.$adv_info_tbl->address_1."<br/>";
			$html .= 'Address 2: '.$adv_info_tbl->address_2."<br/>";
			$html .= 'City: '.$adv_info_tbl->city."<br/>";
			$html .= 'State: '.$adv_info_tbl->state."<br/>";
			$html .= 'Zip: '.$adv_info_tbl->zip."<br/>";
			$html .= 'Phone number: '.$adv_info_tbl->phone_number."<br/>";
			$html .= 'Email Address: '.$adv_info_tbl->email_address."<br/>";
			$html .= 'Affiliate Code: '.$adv_info_tbl->affiliate_code."<br/>";
			$html .= '<br/><br/>Payment Info:'."<br/>";
			$html .= 'Credit Card Number: ' . $adv_info_tbl->cc_number."<br/>";
			$html .= 'Expiration Month: ' . $cc_expire[0]."<br/>";
			$html .= 'Expiration Year: ' . substr($cc_expire[1],-2)."<br/>";
			$html .= 'CVV Number: ' . $adv_info_tbl->cvv."<br/>";
			$html .= '<br/><br/><a href="https://www.cheaplocaldeals.com/admin/?sect=retcustomer&mode=edit&cid='.$adv_info_tbl->id.'">Update Advertiser Information</a>'."<br/>";

			$email_string = str_replace('ADVERT_INFO_STRING',$html,ADVERT_PAY_PROCESS_FAIL);
			
			$email_data = array();
			$email_data['content'] = $email_string;
			$email_data['from_address'] = SITE_FROM_ADDRESS;
			$email_data['subject'] = SITE_NAME_VAL." Advertiser Account Payment Failure ".date("m-d-Y");
			$email_data['to_addresses'] = SITE_CONTACT_EMAILS;
			
			// send email
			send_email($email_data); 
			
			// disable account until an admin approves changes
			$sql_query_update = "UPDATE
									advertiser_info
								 SET
									approved = ?
								 WHERE
									id = ?
								 ;";
					 
			$update_vals = array(
								0,
								$advertisers['id']
								);
			
			$stmt = $dbh->prepare($sql_query_update);					 
			$stmt->execute($update_vals);
	
			// save failure data for later review
			$memb_proc_fld_tbl->advertiser_id = $advertisers['id'];
			$memb_proc_fld_tbl->advertiser_level = $adv_info_tbl->customer_level;
			$memb_proc_fld_tbl->date = date("Y-m-d");
			$memb_proc_fld_tbl->payment = $payment_val;
			$memb_proc_fld_tbl->payment_method = $adv_info_tbl->payment_method;
			$memb_proc_fld_tbl->cc_type = $adv_info_tbl->credit_card_type;
			$memb_proc_fld_tbl->payment_approved = 1;
			$memb_proc_fld_tbl->other_info = (!empty($result) ? serialize($result) : '');
			$memb_proc_fld_tbl->insert();
		
		  $payment_data .= ' ... credit card payment declined ';
		}
		
	} else {
		$process_payment = 0;
		$payment_data .= ' ... payment not required at this time ';
		// check for another possible problem
		$payment_problem++;
	}
	
	if ($process_payment == 1) {
		$memb_proc_tbl->advertiser_id = $advertisers['id'];
		$memb_proc_tbl->advertiser_level = $adv_info_tbl->customer_level;
		$memb_proc_tbl->date = date("Y-m-d");
		$memb_proc_tbl->payment = $payment_val;
		$memb_proc_tbl->payment_method = $adv_info_tbl->payment_method;
		$memb_proc_tbl->cc_type = $adv_info_tbl->credit_card_type;
		$memb_proc_tbl->payment_approved = 1;
		$memb_proc_tbl->other_info = (!empty($result) ? serialize($result) : '');
		$memb_proc_tbl->insert();
		// update advertiser info
		$sql_query_update = "UPDATE
								advertiser_info
							 SET
								customer_level_renewal_date = ?
							 WHERE
								id = ?
							 ;";
				 
		$update_vals = array(
							$renewal_date,
							$advertisers['id']
							);
		
		$stmt = $dbh->prepare($sql_query_update);					 
		$stmt->execute($update_vals);
	}
	
	}
	
	$payment_data .= ' ... process complete <br/>';
	
	if ($display_free_adverts != 1 && $adv_info_tbl->customer_level == 3) {
		// do nothing
	} elseif ($display_non_paying_premium != 1 && $process_payment == 0 && $payment_problem < 2) {
		// do nothing
	} else {
		$payment_data_output .= $payment_data;
	}
}

//echo $payment_data_output;

// emails report to site admins
if (!empty($payment_data_output)) {
	$html = $payment_data_output;
} else {
	$html = 'No new payments processed.';
}

$email_data = array();
$email_data['content'] = $html;
$email_data['from_address'] = SITE_FROM_ADDRESS;
$email_data['subject'] = SITE_NAME_VAL." Advertisers Payment Process Report ".date("m-d-Y");
$email_data['to_addresses'] = SITE_CONTACT_EMAILS;

// send email
send_email($email_data); 
?>