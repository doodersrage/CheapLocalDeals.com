<?PHP

header("Location: https://www.cheaplocaldeals.com/advertiser-signup/");

// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Advertiser Signup Page';
$page_meta_description = 'Create an account with us today.';
$page_meta_keywords = 'Assign keywords here';

// assign header constant
$prnt_header = new prnt_header();
$prnt_header->page_header_title = $page_header_title;
$prnt_header->page_meta_description = $page_meta_description;
$prnt_header->page_meta_keywords = $page_meta_keywords;
define('PAGE_HEADER',$prnt_header->print_page_header());

require(CLASSES_DIR.'sections/category_select.php');
$category_select_pg = new category_select_pg;

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

$form_output = '';

if(!empty($error_message)) {
$form_output .= $error_message;
}

$form_output .= '<tr><td colspan="2">
						<script type="text/javascript" src="includes/libs/jquery.popupwindow.js"></script>
						<script type="text/javascript" src="includes/js/popwindow.js"></script>
						<table align="center" border="0" cellspacing="0" cellpadding="0"><tr>
                <td align="left">
				<table>
				<tr><td>'.$form_write->input_hidden('form_submit',1).'Company Name*<br/> '.$form_write->input_text('company_name',$company_name,20,120,1).'</td></tr>
				<tr>
				<td align="left"><table width="100%" align="left" border="0" cellspacing="0" cellpadding="0"><tr><td align="left">Phone Number*<br/> '.$form_write->input_text('contact_phone_left',$contact_phone_left,3,3,2).'-'.$form_write->input_text('contact_phone_center',$contact_phone_center,3,3,2).'-'.$form_write->input_text('contact_phone_right',$contact_phone_right,4,4,2).'</td>
				</tr>
				<tr>
				<td>FAX<br/> '.$form_write->input_text('contact_fax_left',$contact_fax_left,3,3,3).'-'.$form_write->input_text('contact_fax_center',$contact_fax_center,3,3,3).'-'.$form_write->input_text('contact_fax_right',$contact_fax_right,4,4,3).'</td></tr></table></td>
				</tr>
				<tr><td>Email Address*<br/> '.$form_write->input_text('email_address',$email_address,30,120,4).'</td></tr>
				<tr><td>Website URL (eg: www.cheaplocaldeals.com)<br/> '.$form_write->input_text('website',$website,30,160,5).'</td></tr>
				</table>
				</td><td align="left" valign="top">
				<table>
				<tr><td align="left">Hide Address From Listing '.$form_write->input_checkbox('hide_address',1,$hide_address).'</td></tr>
				<tr><td>Address 1*<br/> '.$form_write->input_text('address_1',$address_1,30,120,6).'</td></tr>
				<tr><td>Address 2<br/> '.$form_write->input_text('address_2',$address_2,30,120,7).'</td></tr>
				<tr><td><table border="0" cellspacing="0" cellpadding="0"><tr><td>City*<br/> '.$form_write->input_text('city',$city,30,100,8).'</td><td>State*<br/> <select tabindex="9" name="state" id="state_dd">'.gen_state_dd($state).'</select></td></tr></table></td></tr>
				<tr><td>Zip Code*<br/> '.$form_write->input_text('zip',$zip,5,5,10).'</td></tr>
				</table></td></tr>
               </table>
			   </td></tr>
			   
			   <tr><th colspan="2" align="center">Company Bio</th></tr>
				<tr><td colspan="2">
				
				<table align="center"><tr><td colspan="2" align="left">Description (Displayed on your information page.)<br/> '.$form_write->textarea('customer_description',$customer_description,4,80,11).'</td></tr>
				<tr><td colspan="2" align="left">Products and Services (Displayed within the listing and your information page.)<br/> '.$form_write->textarea('products_services',$products_services,4,80,12).'</td></tr></table>
				
				</td></tr>';
$form_output .= '<tr id="slidebox1head" ><th align="center"><span>Categories</span> </th><th align="right"><a href="javascript:void(0);" id="a" class="slide1" name="1">Collapse</a></th></tr>';
$form_output .= '<tr><td colspan="2"><script type="text/javascript">
jQuery(function(){

jQuery("#slidebox1head").css("cursor","pointer");

jQuery(\'#slidebox1head\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		if (jQuery(\'a.slide1\').text() == \'Expand\') {
			jQuery(\'a.slide1\').text(\'Collapse\');
			jQuery(\'#slidebox1\').fadeIn("slow");
		} else {
			jQuery(\'a.slide1\').text(\'Expand\');
			jQuery(\'#slidebox1\').fadeOut("slow");
		}
		
        // alert(id);
     return false;
     });

}); 
</script><table id="slidebox1" width="100%"><tr><td>';
$form_output .= $category_select_pg->list_categories();
$form_output .= '</td></tr></table></td></tr>';
$form_output .= '<tr id="slideboxhead"><th align="center"><span>Hours of Operation </span></th><th align="right"><a href="javascript:void(0);" id="b" class="slide" name="1">Collapse</a></th></tr>
				<tr><td colspan="2"><script type="text/javascript">
jQuery(function(){

jQuery("#slideboxhead").css("cursor","pointer");

jQuery(\'#slideboxhead\').click(function() {
        var id = jQuery(this).attr(\'id\');
		
		if (jQuery(\'a.slide\').text() == \'Expand\') {
			jQuery(\'a.slide\').text(\'Collapse\');
			jQuery(\'#slidebox\').fadeIn("slow");
		} else {
			jQuery(\'a.slide\').text(\'Expand\');
			jQuery(\'#slidebox\').fadeOut("slow");
		}
		
        // alert(id);
     return false;
     });
  
  jQuery(\'#select_hours\').click(function(event) {
	if (jQuery(\'#hours_of_operation_tbl\').css("display") == \'none\') {
	  jQuery(\'#hours_of_operation_tbl\').css("display","block");
	}
  });
  
  jQuery(\'.other_oper\').click(function(event) {
	if (jQuery(\'#hours_of_operation_tbl\').css("display") == \'block\') {
	  jQuery(\'#hours_of_operation_tbl\').css("display","none");
	}
  });

  jQuery(\'#hours_of_operation_tbl\').css("display","none");

}); 
</script><table width="100%" id="slidebox"><tr>
                <td align="left">';

// builds hours of operation selection
		$hours_list = unserialize(HOURS_SELECT);
		$days_array = unserialize(DAYS_ARRAY);
		if(!isset($hours_of_operation)) {
			// if hours of operation settings do not exist print HOP form
			$hours_of_operation_frm = $form_write->input_radio('hours_of_operation[selected][type]','nohours','nohours','','other_oper').' Do Not Display Hours<br/>';
			$hours_of_operation_frm .= $form_write->input_radio('hours_of_operation[selected][type]','24hr','','','other_oper').' Open 24 Hours<br/>';
			$hours_of_operation_frm .= $form_write->input_radio('hours_of_operation[selected][type]','select','','select_hours').' Select Hours<br/>';
			$hours_of_operation_frm .= '<table class="hours_of_operation_tbl" id="hours_of_operation_tbl">';
			$hours_of_operation_frm .= '<tr>';
			reset($days_array);
			foreach($days_array as $value) {
				$hours_of_operation_frm .= '<th>'.$value.'</th>';
			}
			$hours_of_operation_frm .= '</tr><tr>';
			reset($days_array);
			reset($hours_list);
			// draw days selection
			foreach($days_array as $day_value) {
				$hours_of_operation_frm .= '<td>';
				$top_hrs_arr = array();
				foreach($hours_list as $value) {
					$top_hrs_arr[] = '<option '.('9:00 AM' == $value && $day_value != 'Saturday' && $day_value != 'Sunday' ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation_frm .= $form_write->select_dd('hours_of_operation[selected]['.$day_value.'open]',$top_hrs_arr);
				$hours_of_operation_frm .= '<br/>';
				$hours_of_operation_frm .= 'to<br/>';
				$top_hrs_arr = array();
				foreach($hours_list as $value) {
					$top_hrs_arr[] = '<option '.('5:00 PM' == $value && $day_value != 'Saturday' && $day_value != 'Sunday' ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation_frm .= $form_write->select_dd('hours_of_operation[selected]['.$day_value.'close]',$top_hrs_arr);
				$hours_of_operation_frm .= '<br/>';
				$hours_of_operation_frm .= '</td>';
			}
			$hours_of_operation_frm .= '</tr></table>';
		} else {
			// if HOP values are set load values into form
			$hours_of_operation_frm = $form_write->input_radio('hours_of_operation[selected][type]','nohours',$hours_of_operation['selected']['type'],'','other_oper').' Do Not Display Hours<br/>';
			$hours_of_operation_frm .= $form_write->input_radio('hours_of_operation[selected][type]','24hr',$hours_of_operation['selected']['type'],'','other_oper').' Open 24 Hours<br/>';
			$hours_of_operation_frm .= $form_write->input_radio('hours_of_operation[selected][type]','select',$hours_of_operation['selected']['type'],'select_hours').' Select Hours<br/>';
			$hours_of_operation_frm .= '<table class="hours_of_operation_tbl" id="hours_of_operation_tbl">';
			$hours_of_operation_frm .= '<tr>';
			reset($days_array);
			foreach($days_array as $value) {
				$hours_of_operation_frm .= '<th>'.$value.'</th>';
			}
			$hours_of_operation_frm .= '</tr><tr>';
			reset($days_array);
			reset($hours_list);
			// draw days selection
			foreach($days_array as $day_value) {
				$hours_of_operation_frm .= '<td>';
				$top_hrs_arr = array();
				foreach($hours_list as $value) {
					$top_hrs_arr[] = '<option '.($hours_of_operation['selected'][$day_value.'open'] == $value ? 'selected="selected"' : '9:00 AM' == $value && $day_value != 'Saturday' && $day_value != 'Sunday' ? 'selected="selected"' : '' ).' >'.$value.'</option>';
				}
				$hours_of_operation_frm .= $form_write->select_dd('hours_of_operation[selected]['.$day_value.'open]',$top_hrs_arr);
				$hours_of_operation_frm .= '<br/>';
				$hours_of_operation_frm .= 'to<br/>';
				$top_hrs_arr = array();
				foreach($hours_list as $value) {
					$top_hrs_arr[] = '<option '.($hours_of_operation['selected'][$day_value.'close'] == $value ? 'selected="selected"' : $value == '5:00 PM' && $day_value != 'Saturday' && $day_value != 'Sunday' ? 'selected="selected"' : '' ).' >'.$value.'</option>';
				}
				$hours_of_operation_frm .= $form_write->select_dd('hours_of_operation[selected]['.$day_value.'close]',$top_hrs_arr);
				$hours_of_operation_frm .= '<br/>';
				$hours_of_operation_frm .= '</td>';
			}
			$hours_of_operation_frm .= '</tr></table>';			
		}
				
$form_output .= $hours_of_operation_frm;
$form_output .= '</td></tr></table>
				</td></tr>';
$form_output .= '<tr id="slidebox2head"><th align="center"><span>Accepted Payment Methods</span> </th><th align="right"><a href="javascript:void(0);" id="c" class="slide2" name="1">Collapse</a></th></tr>
				<tr><td colspan="2"><script type="text/javascript">
jQuery(function(){

jQuery("#slidebox2head").css("cursor","pointer");

jQuery(\'#slidebox2head\').click(function() {
        var id = jQuery(this).attr(\'id\');
		
		if (jQuery(\'a.slide2\').text() == \'Expand\') {
			jQuery(\'a.slide2\').text(\'Collapse\');
			 jQuery(\'#slidebox2\').fadeIn("slow");
		} else {
			jQuery(\'a.slide2\').text(\'Expand\');
			 jQuery(\'#slidebox2\').fadeOut("slow");
		}
		
	// alert(id);
 return false;
 });

}); 
</script><table width="100%" id="slidebox2"><tr>
                <td align="left">';
// build payment method options
$payment_methods = $adv_pmt_mtds_tbl->get_all();
		$payment_method_sel = '';
		$payment_method_sel_op = '<script type="text/javascript">
jQuery(function(){
 jQuery(".rowclick td").css("cursor","pointer");
  jQuery(\'#rowclick2 td\').click(function(event) {
	jQuery(this).toggleClass(\'selected\');
	if (event.target.type !== \'checkbox\') {
	  jQuery(\':checkbox\', this).trigger(\'click\');
	}
  });
});
</script><table class="rowclick" id="rowclick2">';
		foreach($payment_methods as $value) {
			$payment_method_sel[] = '<td>'.$form_write->input_checkbox('payment_options['.$value['id'].']',1,$payment_options[$value['id']]).' '.$value['method'].'</td>';
			if (count($payment_method_sel) == 4) {
				$payment_method_sel_op .= '<tr>'.implode('',$payment_method_sel).'</tr>';
				$payment_method_sel = '';
			}
		}
		if (count($payment_method_sel) > 0) {
			$payment_method_sel_op .= '<tr>'.implode('',$payment_method_sel).'</tr>';
			$payment_method_sel = '';
		}
		$payment_method_sel_op .= '</table>';
		
$form_output .= $payment_method_sel_op;
$form_output .= '</td></tr></table>
				</td></tr>';
$form_output .= '<tr id="slidebox3head"><th align="center"><span>Certificate Amounts</span> </th><th align="right"><a href="javascript:void(0);" id="d" class="slide3" name="1">Collapse</a></th></tr>
				<tr><td colspan="2"><script type="text/javascript">
jQuery(function(){

jQuery("#slidebox3head").css("cursor","pointer");

jQuery(\'#slidebox3head\').click(function() {
        var id = jQuery(this).attr(\'id\');
		
		if (jQuery(\'a.slide3\').text() == \'Expand\') {
			jQuery(\'a.slide3\').text(\'Collapse\');
			jQuery(\'#slidebox3\').fadeIn("slow");
		} else {
			jQuery(\'a.slide3\').text(\'Expand\');
			jQuery(\'#slidebox3\').fadeOut("slow");
		}
		
        // alert(id);
     return false;
     });

}); 
</script>';
		$certificate_amounts = $cert_amt_tbl->get_certificate_amounts();
		
		$cert_amt = '<script type="text/javascript" src="includes/libs/jquery.form-defaults.js"></script><table width="100%" align="center" id="slidebox3" class="certificate_amounts_tbl">
		<tr><td colspan="3">
		<table width="100%" border="0" cellspacing="10" cellpadding="0">
		<tr><td align="center"><a id="cert_req_link" href="includes/popups/advert_cert_agree_popup.deal?popupwindow" class="popupwindow" rel="height:410,width:750,toolbar:0,scrollbars:1,status:0,resizable:0,left:150,top:100"><img src="images/cert-requirements.gif" border="0"></a></td><td align="center"><a id="cert_req_link" href="pdf/test_cert.pdf?popupwindow" class="popupwindow" rel="height:550,width:750,toolbar:0,scrollbars:1,status:1,resizable:0,left:150,top:100"><img src="images/example-cert.png" border="0"></a></td></table></td></tr>
		<tr><td colspan="3" align="left"><p><strong><font color="red">IMPORTANT!</font></strong>: Read this before proceeding.<br>
Please select the certificate(s) that you will offer, how much the consumer must spend to use OR what they can be used towards. Finally, List exclusions, if any. </p>

<p>NOTE: All certificates have the following requirements preprinted on them (please do not list them in the Exclusions below). Certificates are not Valid with any other offer or promotion. Certificate has no cash back value. Certificate cannot be used towards outstanding balances, tips etc.. Limit one certificate redemption per visit. To view additional preprinted restrictions and a sample certificate, please <a id="cert_req_link" href="pdf/test_cert.pdf?popupwindow" class="popupwindow" rel="height:550,width:750,toolbar:0,scrollbars:1,status:1,resizable:0,left:150,top:100">click here</a>.</p>
</td></tr>';
//		$cert_amt = '<tr><td colspan="3" align="left">
//		<table width="100%"><th align="center" colspan="2" width="155">Coupon Value</th><th width="669">Requirements for use or restrictions</th></tr>';
//		
//		foreach($certificate_amounts as $value) {
//			
//			$valid_purchase = 'Enter Products(s) required to purchase for $'.$value['discount_amount'].' discount here';
////			$valid_minimum = 'Enter minimum spend required for $'.$value['discount_amount'].' discount here';
//			$valid_with = 'Other: If neither of the above can be used, please enter requirements for use of $'.$value['discount_amount'].' Discount here';
//			$excludes_def = 'enter, IF ANY, exclusions for certificate use here';
//			
//			$requirements = '<script type="text/javascript">
//jQuery(function(){
//jQuery("#requirement_texta'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$valid_purchase.'");';
////$requirements .= 'jQuery("#requirement_textb'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$valid_minimum.'");';
//$requirements .= 'jQuery("#requirement_textd'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$excludes_def.'");';
//$requirements .= 'jQuery("#requirement_textc'.strtolower(numtoalpha($value['id'])).'").DefaultValue("'.$valid_with.'");
//});
// </script>
//<script type="text/javascript">
//jQuery(function(){
// jQuery(".certificate_amount_requirements td").css("cursor","pointer");
//  jQuery(\'#certificate_amount_requirements'.$value['id'].' td\').click(function(event) {
//	if (event.target.type !== \'radio\') {
//	  jQuery(\'.cert_requirements'.$value['id'].':radio\', this).trigger(\'click\');
//	}
//  });
//  
//  jQuery(\'#certificate_levels'.$value['id'].'\').click(function(event) {
//	if (jQuery(\'#certificate_amount_requirements'.$value['id'].'\').css("display") == \'none\') {
//	  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeIn("slow");
//	} else {
//	  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').fadeOut("slow");
//	}
//  });
//
//  jQuery(\'#certificate_amount_requirements'.$value['id'].'\').css("display","none");
//  
//});
//</script><table width="100%" class="certificate_amount_requirements" id="certificate_amount_requirements'.$value['id'].'">';
//			
//			$requirements .= '<tr><td valign="top" align="left">Opt 1:
//			'.$form_write->input_radio('cert_requirements['.$value['id'].']',2,(!empty($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] : ''),'cert_requirements'.$value['id'],'cert_requirements'.$value['id']).' <font color="red">Valid With</font> Min Spend Of:';
//			
//			$min_spend_opts = explode(',',$value['min_spend_amts']);
//			foreach($min_spend_opts as $cur_spend_val) {
//				$requirements .= ' <input onclick="set_price('.$value['id'].','.$cur_spend_val.',\'#requirement_textb'.strtolower(numtoalpha($value['id'])).'\');" name="requirement_text['.$value['id'].'][min_spend]" type="radio" value="'.$cur_spend_val.'" '.($requirement_text[$value['id']][2] == $cur_spend_val ? 'checked' : '').' /> $'.$cur_spend_val;
//			}
//			
//			$requirements .= ' Other $'.$form_write->input_text('requirement_text['.$value['id'].'][2]',$requirement_text[$value['id']][2],2,10,'','requirement_textb'.strtolower(numtoalpha($value['id']))).'
//			</td></tr>';
//
//// old min spend input
////			$requirements .= '<tr><td valign="top" align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="2" '.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 2 ? 'checked ' : '' : '').'/> <font color="red">Valid With</font> Minimum Spend Of $<input id="requirement_textb'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][2]" type="text" value="'.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 2 ? $requirement_text[$value['id']][2] : '' : '').'"  maxlength="90" size="43" /></td></tr>';
//			
//			$requirements .= '<tr><td valign="top" align="left">Opt 2: '.$form_write->input_radio('cert_requirements['.$value['id'].']',1,$cert_requirements[$value['id']],'','cert_requirements'.$value['id']).' <font color="red">Valid Towards</font> : '.$form_write->input_text('requirement_text['.$value['id'].'][1]',(!empty($requirement_text[$value['id']][1]) ? $requirement_text[$value['id']][1] : ''),40,90,'','requirement_texta'.strtolower(numtoalpha($value['id']))).'</td></tr>';
//			
////			$requirements .= '<tr><td valign="top" align="left"><input name="cert_requirements['.$value['id'].']" type="radio" value="3" '.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 3 ? 'checked ' : '' : '').'/> <font color="red">Valid With</font> <textarea id="requirement_textc'.strtolower(numtoalpha($value['id'])).'" name="requirement_text['.$value['id'].'][3]" cols="48" rows="2">'.(isset($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] == 3 ? $requirement_text[$value['id']][3] : '' : '').'</textarea></td></tr>';
//			
//			$requirements .= '<tr><td valign="top" align="left">Opt 3: '.$form_write->input_radio('cert_requirements['.$value['id'].']',4,(!empty($cert_requirements[$value['id']]) ? $cert_requirements[$value['id']] : ''),'','cert_requirements'.$value['id']).'No Requirements'.$form_write->input_hidden('requirement_text['.$value['id'].'][4]','').'</td></tr>';
//			
//			$requirements .= '<tr><td valign="top" align="center">Excludes: '.$form_write->input_text('requirement_text['.$value['id'].'][excludes]',$requirement_text[$value['id']]['excludes'],47,90,'','requirement_textd'.strtolower(numtoalpha($value['id']))).'</td></tr>';
//
//			$requirements .= '</table>';
//			
//			$cert_amt .= '<tr><td align="right" valign="top">$'.$value['discount_amount'].':</td><td valign="top"> '.$form_write->input_checkbox('certificate_levels['.$value['id'].']',1,(!empty($certificate_levels[$value['id']]) ? $certificate_levels[$value['id']] : ''),'certificate_levels'.$value['id']).'</td><td>'.$requirements.'</td></tr>'.LB; 
//			
//		}
//		$cert_amt .= '</table>
//					<script type="text/javascript">
//					  jQuery(function(){
//						jQuery(\'#certificate_levels1\').attr(\'checked\', \'checked\');
//						jQuery(\'#certificate_amount_requirements1\').toggle();
//					  });
//					</script>';
				
$form_output .= $cert_amt;
$form_output .= '</td></tr></table>';
$form_output .= '</td></tr>';
$form_output .= '<tr><th colspan="2" align="center">Account Information</th></tr>
				<tr><td colspan="2"><table align="center" width="450">';
$form_output .= '<tr><td>First Name*<br/> '.$form_write->input_text('first_name',$first_name,30,120,13,'').'</td><td>Username*<br/> '.$form_write->input_text('username',$username,30,120,17,'').'</td></tr>';	
$form_output .= '<tr><td>Last Name*<br/> '.$form_write->input_text('last_name',$last_name,30,120,15,'').'</td><td>Password* '.MINIMUM_PASSWORD_LENGTH.' character or more<br/> '.$form_write->input_password('password',$password,30,120,18,'').'</td></tr>';	
$form_output .= '<tr><td></td><td>Confirm Password* '.MINIMUM_PASSWORD_LENGTH.' character or more<br/> '.$form_write->input_password('confirm_password',$confirm_password,30,120,19,'').'</td></tr>';	
$form_output .= '<tr><td>Affiliate Code "If Applicable"<br/> '.$form_write->input_text('link_affiliate_code',$link_affiliate_code,30,120,16,'').'</td><td>Listing Image: (Please limit size to 150 x 120 and file type to jpg, gif, or png)<br/> '.$form_write->input_file('image',20,$id).'</td></tr>';	
$form_output .= '</table>
				</td></tr>';
$form_output .= '<tr><td>
				<center><strong>For questions regarding advertising please dial 1-866-283-6809</strong></center>
				<script type="text/javascript">
				function set_price(cert_id,price,target) {
					jQuery(target).val(price);
					var cert_radio = \'#cert_requirements\'+cert_id;
					jQuery(cert_radio).attr(\'checked\', \'checked\');
				}
				</script>
				</td></tr>';

// start output buffer
ob_start();
	
	// load template
	require(TEMPLATE_DIR.'create_advertiser_account.php');

//	print_r($cert_requirements);
//	if(is_array($certificate_levels)) {
//		foreach($cert_requirements as $id => $value) {
//			echo $cert_requirements[$id].' ';
//			echo $requirement_text[$id][$value].' ';
//			echo '$requirement_text['.$id.']['.$value.']'.' ';
//			echo $requirement_text[$id]['excludes'].'<br>';
//		}
//	}

	$html = ob_get_contents();
	
ob_end_clean();

print_page($html);
?>