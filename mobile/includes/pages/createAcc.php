<?PHP

$customer_info_table->get_post_vars();
	
$page_output = '';

// set customer ref code if assigned
if(!empty($_GET['user_ref_code'])) $_SESSION['user_ref_code'] = $_GET['user_ref_code'];
$user_ref_code = $_SESSION['user_ref_code'];
	
// check for form submission and run form check routines
if(isset($_POST['Submit'])) {
	if ($_POST['Submit'] === 'Create Account') {
	
		// extract post vars
		extract($_POST);
		
		$errored_fields = '';
		$required_fields = array(
							'Email Address' => 'email_address',
							'Password' => 'password',
							'Confirm Password' => 'confirm_password'
							);
		
		foreach($required_fields as $id => $value) {
			if(empty($_POST[$value])) {
				$errored_fields[] = $id;
			}
		}
		// write error message
		if(is_array($errored_fields)) $error_message = "Errors were found with these fields: ".implode(', ',$errored_fields).".";
		// check if username exists
		if($customer_info_table->email_check() > 0) {
			$error_message .= 'You appear to already have an account.';
		}
		// make sure password fields match
		if($_POST['password'] != $_POST['confirm_password']) {
			$error_message .= '<br/>Password fields do not match.';
		}
		// check for valid email address
		if(strpos($_POST['email_address'],'@') <= 0) {
			$error_message .= '<br/>E-Mail address does not appear to be valid.';
		}
		// check password length
		if (strlen($_POST['password']) < MINIMUM_PASSWORD_LENGTH) $error_message .= "<br>Password must be atleast ".MINIMUM_PASSWORD_LENGTH." characters in length.";
		// if error found pring error message
		if (!empty($error_message)) {
			// clear entered passwords
			$_POST['password'] = '';
			$customer_info_table->password = '';
		} else {
			$customer_info_table->balance = CUSTOMER_DEFAULT_BALANCE;
			$customer_info_table->account_enabled = 1;
			$customer_info_table->city = $geo_data->city;
			$customer_info_table->state = $geo_data->region;
			$customer_info_table->usr_ref_code = $user_ref_code;
			if($update_usr == 1){
			  $customer_info_table->update();
			} else {
			  $customer_info_table->insert();
			}
			
			// add new account information to email message
			$account_login_info = '<br><br><font color="red">Your new account login info:</font>'."<br>";
			$account_login_info .= 'E-Mail: '.$email_address."<br>";
			$account_login_info .= 'Password: '.$_POST['password']."<br>";
			$account_login_info .= '<br><br>Get a FREE Credit towards your next $10 coupon when you refer a friend and they sign up! UNLIMITED credits. Just send your friends the Link below and when sign up via that link you instantly get a credit.'."<br>";
			$account_login_info .= 'http://www.cheaplocaldeals.com/customer_admin/create_account.deal?user_ref_code='.$customer_info_table->ref_code."<br>";
	
			$email_replace_arr = array($account_login_info);
			$email_replace_str_arr = array('NEW_USER_ACC_LOGIN_INFO');
			
			$html = str_replace($email_replace_str_arr,$email_replace_arr,CUSTOMER_SIGNUP_EMAIL);
			
			$email_data = array();
			$email_data['content'] = $html;
			$email_data['from_address'] = SITE_FROM_ADDRESS;
			$email_data['subject'] = SITE_NAME_VAL." Account Signup ".date("m-d-Y");
			$email_data['to_addresses'] = $customer_info_table->email_address;
			
			// send email
			send_email($email_data); 
			
			// write admin account created email
			$html = 'New Customer Account Created:'."<br>";
			$html .= '<br><br>Customer Info:'."<br>";
			$html .= 'Email Address: '.$customer_info_table->email_address."<br>";
			$html .= '<br><br>Login Info:'."<br>";
			$html .= 'password: '.$_POST['password']."<br>";
			
			$email_data = array();
			$email_data['content'] = $html;
			$email_data['from_address'] = SITE_FROM_ADDRESS;
			$email_data['subject'] = SITE_NAME_VAL." Customer Account Signup ".date("m-d-Y");
			$email_data['to_addresses'] = SITE_CONTACT_EMAILS;
			
			// send email
			send_email($email_data);
			
			// login user
			$customer_info_table->user_login_check();
			
			// added to check reference code and process as needed 5/4/2010
			if(!empty($user_ref_code)) {
				$ref_cust_id = $customer_info_table->ref_code_id_srch($user_ref_code);
				// if cust id found get customer data
				if(!empty($ref_cust_id)) {
					$credit_amt = CUST_REF_CRED_AMT;
					// get and update customer data
					$customer_info_table->get_db_vars($ref_cust_id);
					$customer_info_table->balance += $credit_amt;
					$customer_info_table->update_balance();
					// capture reference data
					$cust_ref_info_tbl->customer_id = $customer_info_table->id;
					$cust_ref_info_tbl->ref_code = $customer_info_table->ref_code;
					$cust_ref_info_tbl->credit_amt = $credit_amt;
					$cust_ref_info_tbl->api_id = $api_ref_chk->api_id;
					$cust_ref_info_tbl->insert();
				}
			}
	
			if ($shopping_cart_manage->contents_count > 0) {
				header("Location: ".MOB_URL."checkout");							
			} else {
				header("Location: ".MOB_URL."manageacc");			
			}
		}
	}
}

$login_form = '<div  id="custLoginFrm">';
$login_form .= (!empty($error_message) ? '<center><strong><font color="red">'.$error_message.'</font></strong></center>' : '');
$login_form .= '<form name="login_form" method="post">';
$login_form .= '<p><a href="'. MOB_URL.'?action=page&pid=2" ><font size="2">Privacy Policy</font></a></p>';
$login_form .= '<label>Email:</label>'.$form_write->input_text('email_address',$email_address,30,160,1,'email_address');
$login_form .= '<label>Password:</label>'.$form_write->input_password('password',$password,20,50,2,'password');
$login_form .= '<label>Confirm Password:</label>'.$form_write->input_password('confirm_password',$confirm_password,20,50,3,'confirm_password');
$login_form .= '<input class="submit_btn" id="" type="submit" name="Submit" value="Create Account" />';
$login_form .= '</form></div>';
$page_output = $login_form;

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Customer Signup Page';
$page_meta_description = 'Create an account with us today.';
$page_meta_keywords = 'Assign keywords here';

// this script writes the content for the sites logoff page and handles search form submissions
$selTemp = 'pages.php';
$selHedMetTitle = $page_header_title;
$selHedMetDesc = $page_meta_description;
$selHedMetKW = $page_meta_keywords;
?>