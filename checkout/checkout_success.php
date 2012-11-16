<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// check for set token value
$token = (!empty($_GET['token']) ? $_GET['token'] : $_POST['token']);

//	// check if customer is logged in
//	if ($_SESSION['customer_logged_in'] != 1 || empty($token)) {
//		header("Location: ".SITE_SSL_URL."checkout/");	
//	}

// if token value is not empty 	check for matching session and token values
if (!empty($token)) {
	
	// check for value existing within database
	$pp_pmts_tbl->assign_db_vars_token_an_session_id(session_id(),$token);
	
	if($pp_pmts_tbl->id > 0) {
		
		// check for existing certificates for this order and if certificates do not exist and the order has been processed created certificates
		$sql_query = "SELECT
						count(*) as rcount
					 FROM
						certificate_orders
					 WHERE 
					 	session_id = ? 
					 AND
					 	token = ? ;";

		$values = array(
						session_id(),
						$token
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
		
		$rowscount = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		$result->free();
		
		$row_count = $rowscount['rcount'];

		// if certificates are found list them as links and if not create them then list them
		if($row_count > 0) {
		// do nothing
		} else {
			
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
				
				// create certificate entry
				for($i = 1; $i <= $values['item_quantity']; $i++) {
					// reset cert orders vals
					$cert_odrs_tbl->reset_vars();
							
					// assign cert orders var vals
					$cert_odrs_tbl->advertiser_id = $values['company_id'];
					$cert_odrs_tbl->requirements = $requirement_value;
					$cert_odrs_tbl->certificate_amount_id = $values['certificate_amount_id'];
					$cert_odrs_tbl->certificate_code = $cert_odrs_tbl->generate_certificate_code();
					$cert_odrs_tbl->excludes = $adv_info_tbl->certificate_requirements[$values['certificate_amount_id']]['excludes'];
					$cert_odrs_tbl->enabled = 1;
					$cert_odrs_tbl->session_id = session_id();
					$cert_odrs_tbl->token = $token;
					$page_output .= $values['certificate_amount_id'];
					// insert new certificate
					$cert_odrs_tbl->insert();
				}
			}
					
			// clear existing cart contents
			$shopping_cart_manage->clear_contents();
					
			$pp_pmts_tbl->api_id = $api_ref_chk->api_id;	
			$pp_pmts_tbl->approved = 1;	
			$pp_pmts_tbl->update();					
		}
	
		// this section generates a new category for all cities within the site
		$sql_query = "SELECT
						id
					 FROM
						certificate_orders
					 WHERE
						session_id = ? 
					 AND
						token = ? ;";
					 
		$values = array(
						session_id(),
						$token
						);
		
		$stmt = $dbh->prepare($sql_query);					 
		$result = $stmt->execute($values);
	
		while($cur_cert = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$cert_odrs_tbl->get_db_vars($cur_cert['id']);
			$customer_order_data .= '<a class="gift_cert_lnk" target="_blank" href="'.SITE_URL.'customer_admin/view_certificate.deal?cert_id='.$cert_odrs_tbl->cert_id.'">'.SITE_URL.'customer_admin/view_certificate.deal?cert_id='.$cert_odrs_tbl->cert_id.'</a><br>';
		}
		
		$page_output .= '<table align="center">
							<tr>
								<td><strong>Thank you for your purchase from CheapLocalDeals.com!</strong></td>
							</tr>
							<tr>
								<td>Click on the link(s) below to access your Gift Certificates:<br>'.$customer_order_data.'</td>
							</tr>
						</table>';

		// draw email options
		if (empty($_POST['email_address'])) {
			$page_output .= '<br><br><form action="" method="post"><table align="center" width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
		<td align="center">Or if you prefer, enter in your email below and your new cetificates will be mailed to you.</td>
	  </tr>
	  <tr>
		<td align="center"><input name="email_address" type="text" /></td>
	  </tr>
	  <tr>
		<td align="center"><input name="Submit" type="submit" value="Submit" /></td>
	  </tr>
	</table>
	</form><br><br>';
		// if email address has been submitted send email to customer with certificate links
		} elseif(!empty($_POST['email_address'])) {
		
			// this section generates a new category for all cities within the site
			$sql_query = "SELECT
							id
						 FROM
							certificate_orders
						 WHERE
							session_id = ? 
						 AND
							token = ? ;";
						 
			$values = array(
							session_id(),
							$token
							);
			
			$stmt = $dbh->prepare($sql_query);					 
			$result = $stmt->execute($values);
		
			$customer_order_data = '';
			
			$customer_order_data .= 'You may access your Gift Certificates by clicking on the link(s) below:<br>';
		
			while($cur_cert = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				$cert_odrs_tbl->get_db_vars($cur_cert['id']);
				$customer_order_data .= '<a href="'.SITE_URL.'customer_admin/view_certificate.deal?cert_id='.$cert_odrs_tbl->cert_id.'">'.SITE_URL.'customer_admin/view_certificate.deal?cert_id='.$cert_odrs_tbl->cert_id.'</a><br>';
			}

			$email_replace_arr = array($customer_order_data);
			$email_replace_str_arr = array('CUSTOMER_ORDER_INFO');
		
			$html = str_replace($email_replace_str_arr,$email_replace_arr,CUSTOMER_PAYPAL_ORDER_EMAIL);
			
			$email_data = array();
			$email_data['content'] = $html;
			$email_data['from_address'] = SITE_FROM_ADDRESS;
			$email_data['subject'] = SITE_NAME_VAL." Order ".date("m-d-Y");
			$email_data['to_addresses'] = $_POST['email_address'];
			
			// send email
			send_email($email_data); 
			
			$email_data = array();
			$email_data['content'] = $html;
			$email_data['from_address'] = SITE_FROM_ADDRESS;
			$email_data['subject'] = SITE_NAME_VAL." Order ".date("m-d-Y");
			$email_data['to_addresses'] = SITE_FROM_ADDRESS;
			
			// send email
			send_email($email_data); 
				
		}
	}
	
} else {
  
  // page output
  $page_output = '<center><strong>Thank you for your order. You will receive an email shortly containing information about your order.</strong></center>';
	  $page_output .= '<center><a href="'.SITE_SSL_URL.'customer_admin/"><strong><font color="#0000FF">Click here to view and print your recently purchased certificates.</font></strong></a></center>';
	  
}
$page_output .= GOOGLE_CONVERSION_CODE;
	
// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Checkout Success';
$page_meta_description = 'Checkout Success';
$page_meta_keywords = 'shopping cart';

// assign header constant
$prnt_header = new prnt_header();
$prnt_header->page_header_title = $page_header_title;
$prnt_header->page_meta_description = $page_meta_description;
$prnt_header->page_meta_keywords = $page_meta_keywords;
define('PAGE_HEADER',$prnt_header->print_page_header());

// start output buffer
ob_start();
	
	// load template
	require(TEMPLATE_DIR.'blank-wobox.php');
	
	$html = ob_get_contents();
	
ob_end_clean();

print_page($html);
?>