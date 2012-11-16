<?PHP
// process form on submit
if (isset($_POST['form_submit'])) {

	// write phone number
	$_POST['phone_number'] = $_POST['contact_phone_left'] . '-' . $_POST['contact_phone_center'] . '-' . $_POST['contact_phone_right'];
	// write fax number
	$_POST['fax_number'] = $_POST['contact_fax_left'] . '-' . $_POST['contact_fax_center'] . '-' . $_POST['contact_fax_right'];
	
	// clean up numbers
	if($_POST['phone_number'] == '--') $_POST['phone_number'] = '';
	if($_POST['fax_number'] == '--') $_POST['fax_number'] = '';

	// extract post vars
	extract($_POST,EXTR_OVERWRITE);
	
	// write categroy select array
	$category_select_pg->selected_array = (isset($_POST['category_select']) ? $_POST['category_select'] : '');
	// check for submission errors
	$errored_fields = '';
	$required_fields = array(
						'Company Name' => 'company_name',
						'Address 1' => 'address_1',
						'City' => 'city',
						'Zip Code' => 'zip',
						'Phone Number' => 'phone_number',
						'Email Address' => 'email_address',
						'First Name' => 'first_name',
						'Last Name' => 'last_name',
						'Username' => 'username',
						'Password' => 'password',
						'Confirm Password' => 'confirm_password'
						);
	foreach($required_fields as $id => $value) {
		if(empty($_POST[$value])) {
			$errored_fields[] = $id;
		}
	}
	
	// write error message
	if(is_array($errored_fields)) $error_message = "Errors were found with these fields: ".implode(', ',$errored_fields).'.';
	// check if username exists
	$error_message = '';
	if($adv_info_tbl->username_check() > 0) {
		$error_message .= '<br/>Username already exists, please choose another.';
	}
	// make sure password fields match
	if($_POST['password'] != $_POST['confirm_password']) {
		$error_message .= '<br/>Password fields do not match.';
	}
	
	// check for valid email address
	if(strpos($_POST['email_address'],'@') <= 0) {
		$error_message .= '<br/>Email address does not appear to be valid.';
	}
	
	// check password length
	if (strlen($_POST['password']) < MINIMUM_PASSWORD_LENGTH) $error_message .= "<br>Password must be atleast ".MINIMUM_PASSWORD_LENGTH." characters in length.";
	// if error found pring error message
	if (!empty($error_message)) {
		// do nothing
		$error_message = create_warning_box($error_message);
	} else {
		// if no errors are found start processing form
		$adv_info_tbl->reset_vars();
		// set posted vars
		$adv_info_tbl->company_name = (isset($company_name) ? $company_name : '');
		$adv_info_tbl->customer_description = (isset($customer_description) ? $customer_description : '');
		$adv_info_tbl->username = $username;
		$adv_info_tbl->password = encrypt_password($password);
		$adv_info_tbl->website = (isset($website) ? $website : '');
		$adv_info_tbl->link_affiliate_code = (isset($affiliate_code) ? $affiliate_code : '');
		$adv_info_tbl->affiliate_code = $adv_info_tbl->generate_affiliate_code();
		$adv_info_tbl->hours_operation = (isset($hours_of_operation) ? serialize($hours_of_operation) : '');
		$adv_info_tbl->link_affiliate_code = (isset($link_affiliate_code) ? $link_affiliate_code : '');
		$adv_info_tbl->products_services = (isset($products_services) ? $products_services : '');
		$adv_info_tbl->payment_options = (isset($payment_options) ? serialize($payment_options) : '');
		$adv_info_tbl->certificate_levels = (isset($certificate_levels) ? serialize($certificate_levels) : '');
		if(is_array($certificate_levels)) {
			foreach($cert_requirements as $id => $value) {
				$certificate_requirements[$id]['type'] = $cert_requirements[$id];
				$certificate_requirements[$id]['value'] = $requirement_text[$id][$value];
				$certificate_requirements[$id]['excludes'] = ($requirement_text[$id]['excludes'] != 'enter, IF ANY, exclusions for certificate use here' ? $requirement_text[$id]['excludes'] : '');
			}
		}
		
		$adv_info_tbl->certificate_requirements = (isset($certificate_requirements) ? serialize($certificate_requirements) : '');
		$adv_info_tbl->first_name = $first_name;
		$adv_info_tbl->last_name = $last_name;
		$adv_info_tbl->address_1 = $address_1;
		$adv_info_tbl->address_2 = $address_2;
		$adv_info_tbl->city = $city;
		$adv_info_tbl->state = $state;
		$adv_info_tbl->zip = $zip;
		$adv_info_tbl->phone_number = $phone_number;
		$adv_info_tbl->fax_number = (isset($fax_number) ? $fax_number : '');
		$adv_info_tbl->email_address = $email_address;
		$adv_info_tbl->hide_address = (isset($hide_address) ? $hide_address : '');
		$adv_info_tbl->account_enabled = 1;
		$adv_info_tbl->update_approval = 1;
		// upload new image if exists
		if (!empty($_FILES['image']['name'])) {
		  $target_path = CUSTOMER_IMAGES_DIRECTORY . md5($_POST['username']) . "-" . basename( $_FILES['image']['name']); 
		  $adv_info_tbl->image = md5($_POST['username']) . "-" . basename( $_FILES['image']['name']);
		  move_uploaded_file($_FILES['image']['tmp_name'], $target_path);
		}
		
		// write values to database
		$adv_info_tbl->insert();
		
		// get new order_id
		$sql_query = "SELECT
					id
				 FROM
					advertiser_info
				 ORDER BY id DESC
				 LIMIT 1
				 ;";
		$rows = $dbh->queryRow($sql_query);
		
		$new_advertiser_id = $rows['id'];

		// send twitter tweet
		if (ENABLE_TWITTER_POSTS == 1) {
			include_once(LIBS_DIR.'twitter.php');

			// connect to bitly to get a short url for posting
			require(LIBS_DIR."bitly.class.php");
			$bitly = new Bitly(BITLY_USERNAME, BITLY_API_KEY);
			$short = $bitly->shortenSingle(SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$new_advertiser_id.'/');
			
			$curTwitter = new twitter(TWITTER_USERNAME, TWITTER_PASSWORD);
			
			$twit_text = $adv_info_tbl->company_name.' '.$short.' #deals #coupons #restaurants #cheaplocaldeals';
		
		if (strlen($twit_text) > 0) {
	
			if( $curTwitter->setStatus($twit_text) == true)
				$twitter_status = "<p>Twitter Updated Successfully</p>";
			else
				$twitter_status = "<p>Twitter is unavailable at this time</p>";
		} else
			$twitter_status = "<p>Error: I won't send blank messages!</p>";
	
		}

		// send new link to delicious
		if (ENABLE_DELICIOUS == 1) {
			require(LIBS_DIR."DeliciousBrownies.php");
			
			$url   = SITE_URL.'advertiser/'.strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $adv_info_tbl->company_name)).'/'.$new_advertiser_id.'/';
			$desc  = $adv_info_tbl->company_name;
			$tags  = $adv_info_tbl->products_services;
			$notes = $adv_info_tbl->customer_description;
		
			$d = new DeliciousBrownies;
			$d->setUsername(DELICIOUS_USERNAME);
			$d->setPassword(DELICIOUS_PASSWORD);
			$d->addPost($url, $desc, $tags, $notes);
			$delicious_status = '<p>Delicious post added.</p>';
		}
				
		$adv_info_tbl->insert_selected_categories($_POST['category_select'],$new_advertiser_id);
		// send new advertiser signup mail
		// add account signup email

		// authorization link
		$authorization_text = '<p><font color="red">IMPORTANT:</font> <font color="blue">Please confirm your account by clicking this link:</font>'."<br/>";
		$authorization_text .= '<a href="'.SITE_URL.'advertiser_admin/advertiser_email_authorize.deal?authcode='.$adv_info_tbl->authorization_code.'"><font color="red">'.SITE_URL.'advertiser_admin/advertiser_email_authorize.deal?authcode='.$adv_info_tbl->authorization_code.'</font></a></p>';
		
		// add new account information to email message
		$account_text = '<br/><br/><font color="red">Your new account login info:</font>'."<br/>";
		$account_text .= 'username: '.$username."<br/>";
		$account_text .= 'password: '.$password."<br/>";
		
		// write admin account created email
		$html = 'New Advertiser Account Created:'."<br/>";
		$html .= '<br/><br/>Advertiser Info:'."<br/>";
		$html .= 'Company Name: '.$company_name."<br/>";
		$html .= 'Company Description: '.nl2br($customer_description)."<br/>";
		$html .= 'Name: '.$first_name.' '.$last_name."<br/>";
		$html .= 'Address 1: '.$address_1."<br/>";
		$html .= 'Address 2: '.$address_2."<br/>";
		$html .= 'City: '.$city."<br/>";
		$html .= 'State: '.$state."<br/>";
		$html .= 'Zip: '.$zip."<br/>";
		$html .= 'Phone number: '.$phone_number."<br/>";
		$html .= 'Email Address: '.$email_address."<br/>";
		$html .= 'Affiliate Code: '.$adv_info_tbl->affiliate_code."<br/>";
		$html .= $account_text;
		$html .= '<br>'.$twitter_status;
		$html .= $delicious_status;
		$html .= $authorization_text;

		$email_data = array();
		$email_data['content'] = $html;
		$email_data['from_address'] = SITE_FROM_ADDRESS;
		$email_data['subject'] = SITE_NAME_VAL." Advertiser Account Signup ".date("m-d-Y");
		$email_data['to_addresses'] = SITE_CONTACT_EMAILS;
		
		// send email
		send_email($email_data); 

		$email_replace_arr = array($account_text,$authorization_text);
		$email_replace_str_arr = array('ACCOUNT_TEXT','AUTHORIZATION_TEXT');
		
		$html = str_replace($email_replace_str_arr,$email_replace_arr,ADVERTISER_SIGNUP_EMAIL);

		$email_data = array();
		$email_data['content'] = $html;
		$email_data['from_address'] = SITE_FROM_ADDRESS;
		$email_data['subject'] = SITE_NAME_VAL." Advertiser Account Signup ".date("m-d-Y");
		$email_data['to_addresses'] = $adv_info_tbl->email_address;
	
		// load pdf file for attachement
		$file = SITE_DIR."pdf/Advertiser-Tools.pdf";
		$fh = fopen($file, 'r+');
		$output = fread($fh, filesize($file));
		fclose($fh);
		
		// attache pdf file to email
		$email_data['file']['file'] = $output;
		$email_data['file']['file_name'] = "Advertiser-Tools.pdf";
		$email_data['file']['content_type'] = "Application/pdf";

		// send email
		send_email($email_data); 
		
		// login new user
		$adv_info_tbl->user_login_check();
		
		// set new page message
		$_SESSION['new_advert_mess'] = '<center><strong><font color="red">Your account has been created and information emailed.</font></strong></center>';
		
		// redirect to account level set page
		header("Location: ".SITE_SSL_URL."advertiser-account-setup-confirm/");
	}
}

$page_output = '';

if(!empty($error_message)) {
$page_output .= $error_message;
}

?>