<?PHP

// load settings email class
require(SITE_ADMIN_CLASSES_DIR.'forms/email.php');
$admin_email_frm = new admin_email_frm;

// write page header
$page_content = page_header('Customers/Advertisers Contact');

// run final bcced emails through mail abstraction
function push_email($addresses) {
	
  
  $email_data = array();
  $email_data['content'] = $_POST['message_content'];
  $email_data['from_address'] = SITE_FROM_ADDRESS;
  $email_data['subject'] = $_POST['email_title'];
  $email_data['Bcc'] = $addresses;
  $email_data['to_addresses'] = 'donotreply@cheaplocaldeals.com';
  
  // send email
  send_email($email_data); 
  
}

// walks through found users sending bcced emails as it goes
function email_walk($rows) {
  global $customer_info_table;
  
  $bcc_limit = 30;
  $cust_cnt = count($rows);
  $walk_cnt = 0;
  $cur_cnt = 0;
  $emails_arr = array();
  foreach($rows as $cur_cust) {
	  $walk_cnt++;
	  $cur_cnt++;
	  
	  // pull customer data
	  $customer_info_table->get_db_vars($cur_cust['id']);
	  
	  $emails_arr[] = $customer_info_table->email_address;
	  
	  if($cur_cnt == 30) {
		  $new_address_lst = implode(',',$emails_arr);
		  $cur_cnt = 0;
		  // send email
		  push_email($new_address_lst); 
		  $emails_arr = array();
	  } elseif($walk_cnt == $cust_cnt) {
		  $new_address_lst = implode(',',$emails_arr);
		  $cur_cnt = 0;
		  // send email
		  push_email($new_address_lst); 
		  $emails_arr = array();
	  }
  
  }
}


// writes customers emails
if ($_POST['contact_customer'] == 1) {

  if ($_POST['submit'] == 'Send Email') {
	if ($_POST['customer_id'] == 'all') {
	  // pull advertiser info
	  $sql_query = "SELECT
				  id
			   FROM
				  customer_info
			   ;";
	  $rows = $dbh->queryAll($sql_query);
  
	  // send new advertiser signup mail
	  // add account signup email
	  
	  email_walk($rows);
	  
	} else {
	  // pull customer data
	  $customer_info_table->get_db_vars($_POST['customer_id']);
  
	  // send new advertiser signup mail
	  // add account signup email
	  
	  $email_data = array();
	  $email_data['content'] = $_POST['message_content'];
	  $email_data['from_address'] = SITE_FROM_ADDRESS;
	  $email_data['subject'] = $_POST['email_title'];
	  $email_data['to_addresses'] = $customer_info_table->email_address;
	  
	  // send email
	  send_email($email_data); 
	}	
	$page_content .= create_warning_box('Customer Emails Have Been Sent');
  }
  // print preview
  if ($_POST['submit'] == 'Preview Email') {
	$page_content .= '<table>';
	$page_content .= '<tr><td align="right"><strong>Email Title:</strong></td><td>'.$_POST['email_title'].'</td></tr>';
	$page_content .= '<tr><td align="right" valign="top"><strong>Email Content:</strong></td><td>'.$_POST['message_content'].'</td></tr>';
	$page_content .= '</table>';
  }
}

// writes advertisers emails
if ($_POST['contact_advertiser'] == 1) {

  if ($_POST['submit'] == 'Send Email') {
  if (empty($_POST['advertiser_select'])) {
	// pull advertiser info
	$sql_query = "SELECT
				id
			 FROM
				advertiser_info
			 ;";
	$rows = $dbh->queryAll($sql_query);
	
	// send new advertiser signup mail
	// add account signup email
	
	email_walk($rows);
  } else {
	// pull customer data
	$adv_info_tbl->get_db_vars($_POST['advertiser_select']);

	// send new advertiser signup mail
	// add account signup email
	
	$email_data = array();
	$email_data['content'] = $_POST['message_content'];
	$email_data['from_address'] = SITE_FROM_ADDRESS;
	$email_data['subject'] = $_POST['email_title'];
	$email_data['to_addresses'] = $adv_info_tbl->email_address;
	
	// send email
	send_email($email_data); 
  }	
  $page_content .= create_warning_box('Advertiser Emails Have Been Sent');
  }
  // print preview
  if ($_POST['submit'] == 'Preview Email') {
	$page_content .= '<table>';
	$page_content .= '<tr><td align="right"><strong>Email Title:</strong></td><td>'.$_POST['email_title'].'</td></tr>';
	$page_content .= '<tr><td align="right" valign="top"><strong>Email Content:</strong></td><td>'.$_POST['message_content'].'</td></tr>';
	$page_content .= '</table>';
  }
}

// send state advertisers email
if ($_POST['state_advertiser'] == 1) {

  if ($_POST['submit'] == 'Send Email') {
	// pull advertiser info
	$sql_query = "SELECT
				id
			 FROM
				advertiser_info
			 WHERE state = '".$_POST['state_select']."'
			 ;";
	$rows = $dbh->queryAll($sql_query);
		
	email_walk($rows);
	
  $page_content .= create_warning_box('Advertiser Emails Have Been Sent');
  }
  // print preview
  if ($_POST['submit'] == 'Preview Email') {
	$page_content .= '<table>';
	$page_content .= '<tr><td align="right"><strong>Email Title:</strong></td><td>'.$_POST['email_title'].'</td></tr>';
	$page_content .= '<tr><td align="right" valign="top"><strong>Email Content:</strong></td><td>'.$_POST['message_content'].'</td></tr>';
	$page_content .= '</table>';
  }

}

// send state customers email
if ($_POST['state_customer'] == 1) {

  if ($_POST['submit'] == 'Send Email') {
	// pull advertiser info
	$sql_query = "SELECT
				id
			 FROM
				customer_info
			 WHERE state = '".$_POST['state_select']."'
			 ;";
	$rows = $dbh->queryAll($sql_query);
	
	// send new advertiser signup mail
	// add account signup email
	
	email_walk($rows);
	
	$page_content .= create_warning_box('Customers Emails Have Been Sent');
  }
  // print preview
  if ($_POST['submit'] == 'Preview Email') {
	$page_content .= '<table>';
	$page_content .= '<tr><td align="right"><strong>Email Title:</strong></td><td>'.$_POST['email_title'].'</td></tr>';
	$page_content .= '<tr><td align="right" valign="top"><strong>Email Content:</strong></td><td>'.$_POST['message_content'].'</td></tr>';
	$page_content .= '</table>';
  }

}

// select page output function
switch ($_GET['mode']) {
// send customers an email
case 'customers':
  $page_content .= $admin_email_frm->customers_email();
break;
case 'statecustomers':
  $page_content .= $admin_email_frm->customers_state_email();
break;
// send advertisers an email
case 'advertisers':
  $page_content .= $admin_email_frm->advertisers_email();
break;
case 'stateadvertisers':
  $page_content .= $admin_email_frm->advertisers_state_email();
break;
}

?>