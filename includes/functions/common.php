<?PHP

// this document stores common functions used throughout the site

// checks for secure connection then redirects to standard HTTP protocol page
function check_request_type() {
	
	if ($_SERVER['SERVER_PORT'] == 443) {
		$pageURL = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		header("Location: ".$pageURL);
	}
	
}

// get current page url
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER['SERVER_PORT'] == 443) {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

// converts a number to an alpha value
function numtoalpha($number) { 

  $anum = "";
  while($number >= 1) {
    $number = $number - 1;
    $anum = chr(($number % 26)+65).$anum;
    $number = $number / 26;
  }

  return $anum;
}

// gets count of days between two dates
function days_between($start, $end)
{
    $diffInSeconds = abs($end - $start);
    $diffInDays = ceil($diffInSeconds / 86400);
    return $diffInDays;
}

// formats decimals to us currency
function format_currency($value) {
	$set_currency = number_format($value, 2, ".", ",");
return $set_currency;
}

// set previous url value
function assign_previous_url_val() {
	$pageURL = curPageURL(); 
	$_SESSION['previous_page'] = $pageURL;
}

// get category results page hit count
function cat_result_cnt() {
	global $dbh, $cities_tbl, $zip_cds_tbl;

	// get page hit counts
	if(!empty($_GET['city'])) {
		// build zip codes array 
		$cities_tbl->get_db_vars($_GET['city']);
		$zip_cds_tbl->city_id = $cities_tbl->id;
		$zip_array = $zip_cds_tbl->get_list();
	} else {
		// get surrounding zips
		$zip_array = $zip_cds_tbl->fetchZipsInRadiusByZip($_SESSION['cur_zip'], $_SESSION['set_radius'], 100 );
	}
	$zip_string = implode(', ',$zip_array);

	// set zip search data
	if(!empty($_GET['city'])) {
		$sel_cnt_zip = $zip_array[0];		
	} else {
		$sel_cnt_zip = $_SESSION['cur_zip'];
	}

	// pull current category count info
	$sql_query = "SELECT
					count
				 FROM
					category_counts
				 WHERE
					category_id = ? AND
					zip_code = ?
				 ;";

	$update_vals = array(
						(isset($_GET['cat']) ? (int)$_GET['cat'] : ''),
						$sel_cnt_zip
						);

	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute($update_vals);
	$rows = $result->fetchRow();
	
	$update_cat_cnt = $rows['count'];
	$update_cat_cnt++;
	
	// if cat count == 1 insert new count value
	if ($update_cat_cnt == 1) {
		$sql_query = "INSERT INTO
						category_counts
					 (
						category_id,
						zip_code,
						count
					 )
					 VALUES
					 (?,?,?);
					 ";
				 
		$update_vals = array(
							(isset($_GET['cat']) ? (int)$_GET['cat'] : ''),
							$sel_cnt_zip,
							$update_cat_cnt
							);
							
		$stmt = $dbh->prepare($sql_query);					 
		$stmt->execute($update_vals);
	} else {
		$sql_query = "UPDATE
						category_counts
					 SET
						count = ?
					 WHERE
						category_id = ? 
					 AND
						zip_code = ?
					 ;";
				 
		$update_vals = array(
							$update_cat_cnt,
							(isset($_GET['cat']) ? (int)$_GET['cat'] : ''),
							$sel_cnt_zip
							);
							
		$stmt = $dbh->prepare($sql_query);					 
		$stmt->execute($update_vals);
	}
	
	// check for existing category count								
	$sql_query = "SELECT
					sum(count) as rcount
				 FROM
					category_counts
				 WHERE
					category_id = ? 
				 AND
					zip_code in (".$zip_string.")
				 ;";

	$update_vals = array(
						(isset($_GET['cat']) ? (int)$_GET['cat'] : '')
						);

	$stmt = $dbh->prepare($sql_query);					 
	$result = $stmt->execute($update_vals);
	$rows = $result->fetchRow();

return $rows['rcount'];
}

// updates and gets results page hit counts
function get_results_page_hit_cnt() {
	global $zip_cds_tbl, $cities_tbl;

	// get views count for cached pages
	if(empty($_SESSION['city'])) {
		// set zip code values
		$zip_cds_tbl->search($_SESSION['cur_zip']);
	// pull city information if set
	} elseif(!empty($_GET['city'])) {
		// set city values
		$cities_tbl->get_db_vars($_GET['city']);

		// pull city zipcode list
		$zip_cds_tbl->city_id = $cities_tbl->id;
		$zip_array = $zip_cds_tbl->get_list();
		
		// set zip code views
		$zip_cds_tbl->search($zip_array[0]);
	}
	$page_count = $zip_cds_tbl->get_views_cnt();
	$zip_cds_tbl->update_zip_views();
	
return $page_count;	
}

// sends emails
function send_email($email_data) {

	$message = new Mail_mime();
	
	$message->setHTMLBody($email_data['content']);
	
	if (!empty($email_data['file']['file_name'])) {
		// Add an attachment
        $file = $email_data['file']['file'];                                      // Content of the file
        $file_name = $email_data['file']['file_name'];                               // Name of the Attachment
        $content_type = $email_data['file']['content_type'];                                // Content type of the file
        $message->addAttachment ($file, $content_type, $file_name, 1);  // Add the attachment to the email		
	}
	
	$body = $message->get();
	$extraheaders = array("From"=>$email_data['from_address'], "Subject"=>$email_data['subject']);
	if(!empty($email_data['Bcc'])) {
	  $extraheaders["Bcc"] = $email_data['Bcc'];
	}
	$headers = $message->headers($extraheaders);

	// SMTP params
	$smtp_params["host"] = "smtp.gmail.com"; // SMTP host
	$smtp_params["port"] = "25";               // SMTP Port
	$smtp_params["auth"]     = true;
	$smtp_params["username"] = "donotreply@cheaplocaldeals.com";
	$smtp_params["password"] = "P54459";
	$mail = Mail::factory("smtp", $smtp_params);
		
//	$mail = Mail::factory("mail");
	$mail->send($email_data['to_addresses'], $headers, $body);

}

// generate random string
function randgen($len) {
	$rand = "";
	
	if($len > 5 && $len < 17) {
		srand((double) microtime() * 1000000);
		for($i=0;$i<12;$i++) {
			$rand .= chr(rand(0,255));
		}
		$rand = substr(sha1($rand), 0, $len);
	}
return $rand;
}

// updates current page hits
function update_page_hits() {
	global $dbh;

	// do not capture views of these pages
	$disable_view_caching = array(
								'/advertiser_admin/',
								'/customer_admin/',
								'advertiser_admin/advertiser_email_authorize.deal'
								);
	
	$current_page = curPageURL();
	
	$link_check = 0;
	
	// cycle through capture disable pages
	foreach($disable_view_caching as $cur_dis_app) {
		$link_check += strpos($current_page,$cur_dis_app);
	}
	
	// if page is not in disabled list add page hit to table
	if ($link_check == 0) {
		// add page view to views table
		$sql_query = "INSERT INTO
						page_hits
					 (
						link
					 )
					 VALUES
					 (
						?
					 );";
				 
		$update_vals = array(
							$current_page
							);
		
		$stmt = $dbh->prepare($sql_query);
		$stmt->execute($update_vals);
	}
}


// draws the order total section when checking out or processing a customers order
function draw_order_totals() {
  global $shopping_cart_manage, $cust_promo_cds_tbl, $customer_info_table;
  
  // if customer is logged in get customer info
  if(!empty($_SESSION['customer_logged_in'])) {
	$customer_info_table->get_db_vars($_SESSION['customer_id']);
  }
  
  // set order total starting amount
  $total_amount = $shopping_cart_manage->sub_total+$tax_amount;

  // set order sub total amount
  $page_val = 'Sub Total: $'.format_currency($shopping_cart_manage->sub_total).'<br/>';

  // applies quantity discount if enabled
  if(ENABLE_QUANTITY_DISCOUNT == 1) {
	  if($shopping_cart_manage->contents_count >= REQUIRED_DISCOUNT_QUANTITY) {
		  if($total_amount > QUANTITY_DISCOUNT_PRICE){
			$quant_discount_amt = $total_amount - QUANTITY_DISCOUNT_PRICE;
			$page_val .= 'Quantity Discount: $'.format_currency($quant_discount_amt).'<br/>';
		  }
		 $total_amount = format_currency(QUANTITY_DISCOUNT_PRICE);
	  }
  }

  // deduct promo code amount if entered
  if(!empty($_SESSION['promo_code']) && $cust_promo_cds_tbl->promo_code_chk($_SESSION['promo_code']) > 0) {
	  $cust_promo_cds_tbl->assign_db_vars_procode($_SESSION['promo_code']);
	  $promo_discount_amt = $total_amount*($cust_promo_cds_tbl->percentage/100);
	  $total_amount = $total_amount-$promo_discount_amt;
	  $page_val .= 'Promo Discount: $'.format_currency($promo_discount_amt).'<br/>';
  }
  
  // deduct amount from customers available balance
  if(!empty($_SESSION['customer_logged_in'])) {
	if($customer_info_table->balance > 0) {
		$total_amount = $total_amount-$customer_info_table->balance;
		$total_amount = ($total_amount < 0 ? 0 : $total_amount);
		$page_val .= 'Available Balance: $'.format_currency($customer_info_table->balance).'<br/>';
	}
  }

  // added to check for matching state tax assignment
  if(TAX_STATE_VAL == $customer_info_table->state && TAX_APPLY_VALUE == 1) {
	$tax_amount = $shopping_cart_manage->sub_total*(TAX_AMOUNT_VAL/100);
	$page_val .= 'Taxes: $'.format_currency($tax_amount).'<br/>';
  }
  
  $page_val .= 'Total: $'.format_currency($total_amount).'</div></td></tr>';

return $page_val;
}

function get_order_total() {
  global $shopping_cart_manage, $cust_promo_cds_tbl, $customer_info_table;
  
  // set payment data
  // added to check for matching state tax assignment
  $total_amount = $shopping_cart_manage->sub_total;
  
  // enable quantity discounts
  if(ENABLE_QUANTITY_DISCOUNT == 1) {
	  if($shopping_cart_manage->contents_count >= REQUIRED_DISCOUNT_QUANTITY) {
		 $total_amount = format_currency(QUANTITY_DISCOUNT_PRICE);
	  }
  }
  
  // deduct amount applied to promo code
  if(!empty($_SESSION['promo_code']) && $cust_promo_cds_tbl->promo_code_chk($_SESSION['promo_code']) > 0) {
	  $cust_promo_cds_tbl->assign_db_vars_procode($_SESSION['promo_code']);
	  $promo_discount_amt = $total_amount*($cust_promo_cds_tbl->percentage/100);
	  $total_amount = $total_amount-$promo_discount_amt;
  }
  
  // deduct amount from customers available balance
  if($customer_info_table->balance > 0) {
	$total_amount = $total_amount-$customer_info_table->balance;
	$total_amount = ($total_amount < 0 ? 0 : $total_amount);
  }
  
  // apply set tax amounts if required
  if(TAX_STATE_VAL == $customer_info_table->state && TAX_APPLY_VALUE == 1) {
	$tax_amount = $total_amount*(TAX_AMOUNT_VAL/100);
	$total_amount = $total_amount+$tax_amount;
  }

return $total_amount;
}


function get_order_total_for_coupon() {
  global $shopping_cart_manage, $cust_promo_cds_tbl, $customer_info_table;
  
  // set payment data
  // added to check for matching state tax assignment
  $total_amount = $shopping_cart_manage->sub_total;
  
  // enable quantity discounts
  if(ENABLE_QUANTITY_DISCOUNT == 1) {
	  if($shopping_cart_manage->contents_count >= REQUIRED_DISCOUNT_QUANTITY) {
		 $total_amount = format_currency(QUANTITY_DISCOUNT_PRICE);
	  }
  }
  
  // deduct amount applied to promo code
  if(!empty($_SESSION['promo_code']) && $cust_promo_cds_tbl->promo_code_chk($_SESSION['promo_code']) > 0) {
	  $cust_promo_cds_tbl->assign_db_vars_procode($_SESSION['promo_code']);
	  $promo_discount_amt = $total_amount*($cust_promo_cds_tbl->percentage/100);
	  $total_amount = $total_amount-$promo_discount_amt;
  }

return $total_amount;
}

// generates state drop down options
function gen_state_dd($selected = '') {
	global $dbh;

	$sql_query = "SELECT
					state,
					acn
				 FROM
					states
				 ;";
						
	$rows = db_memc_str($sql_query);
	
	$states_dd = '<option value="" ></option>';
	foreach($rows as $cur_state) {
		$states_dd .= '<option value="'.$cur_state['acn'].'" '.($selected == $cur_state['acn'] ? 'selected="selected"' : '').' >'.$cur_state['state'].'</option>';
	}

return $states_dd;
}

// generates a city drop down selector based on selected state
function gen_city_dd($state,$selected = '') {
	global $dbh;

	$sql_query = "SELECT
					city
				 FROM
					cities
				 WHERE
				 	state = '".$state."';";
						
	$rows = db_memc_str($sql_query);
	
	$city_dd = '<option value="" ></option>';
	foreach($rows as $cur_state) {
		$city_dd .= '<option value="'.$cur_state['city'].'" '.($selected == $cur_state['city'] ? 'selected="selected"' : '').' >'.$cur_state['city'].'</option>';
	}

return $city_dd;
}

function gen_zips_sel($id = '') {
	global $dbh;

	$sql_query = "SELECT
					id,
					zip
				 FROM
					zip_codes
				 WHERE city_id = '".$id."'
				 ORDER BY zip ASC;";
	
	$rows = db_memc_str($sql_query);
	
	$states_dd = '';
	foreach($rows as $cur_state) {
		$states_dd .= '<option value="'.$cur_state['id'].'" title="'.$cur_state['zip'].'">'.$cur_state['zip'].'</option>';
	}

return $states_dd;
}

function gen_all_zips_sel($id = '') {
	global $dbh;

	$sql_query = "SELECT
					id,
					zip
				 FROM
					zip_codes
				 WHERE city_id != '".$id."'
				 ORDER BY zip ASC;";
	
	$rows = db_memc_str($sql_query);
	
	$states_dd = '';
	foreach($rows as $cur_state) {
		$states_dd .= '<option value="'.$cur_state['id'].'" title="'.$cur_state['zip'].'">'.$cur_state['zip'].'</option>';
	}

return $states_dd;
}

function gen_api_user_dd($selected = '') {
	global $dbh;

	$sql_query = "SELECT
					id,
					name
				 FROM
					api_access
				 ;";
						
	$rows = db_memc_str($sql_query);
	
	$states_dd = '<option value="" ></option>';
	foreach($rows as $cur_state) {
		$states_dd .= '<option value="'.$cur_state['id'].'" '.($selected == $cur_state['id'] ? 'selected="selected"' : '').' >'.$cur_state['name'].'</option>';
	}

return $states_dd;
}

function getUniqueCode($length = "")
{	
	$code = md5(uniqid(rand(), true));
	if ($length != "") return substr($code, 0, $length);
	else return $code;
}

function findexts($filename) { 
  $filename = strtolower($filename); 
  $exts = split("[/\\.]", $filename); 
  $n = count($exts)-1; 
  $exts = $exts[$n]; 
return $exts; 
} 
?>