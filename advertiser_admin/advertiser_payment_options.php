<?PHP
// load application header
require('../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if advertiser not logged in redirect to login page
if ($_SESSION['advertiser_logged_in'] != 1) header("Location: ".SITE_SSL_URL."advertiser_admin/advertiser_login.deal");

// set page header -- only assign for static header data
$page_header_title = 'CheapLocalDeals.com - Advertiser Payment Options';
$page_meta_description = '';
$page_meta_keywords = '';

// assign header constant
$prnt_header = new prnt_header();
$prnt_header->page_header_title = $page_header_title;
$prnt_header->page_meta_description = $page_meta_description;
$prnt_header->page_meta_keywords = $page_meta_keywords;
define('PAGE_HEADER',$prnt_header->print_page_header());

// process form on submit
if (isset($_POST['form_submit'])) {
	// check for errors
	// check if cc type is set
	if (empty($_POST['payment_method'])) {
		$error .= 'You have not chosen a payment method.<br>';
	}
	
	if ($_POST['payment_method'] == 'Credit Card') {
		// check if cc type is set
		if (empty($_POST['credit_card_type'])) {
			$error .= 'You have not chosen a credit card type.<br>';
		}
		
		// check credit card value submission length
		$cc_number = str_replace('-','',$_POST['cc_number']);
		$cc_number = str_replace(' ','',$cc_number);
		
		if (strlen($cc_number) < 16) {
			$error .= 'Credit Card number does not appear to be correct. Please review and resubmit.<br>';
		}
		
		if (empty($_POST['cvv'])) {
			$error .= 'CVV code was not entered.<br>';
		}
		
		// checks credit card date
		if($_POST['cc_exp_month'] < date('n') && $_POST['cc_exp_year'] == date('Y')) {
			$error .= 'Your credit card expiration date appears to be invalid. Please adjust it and resubmit.';
		}
	}

	if ($_POST['payment_method'] == 'Check') {
//
//		// check if cc type is set
//		if (empty($_POST['check_routing_num'])) {
//			$error .= 'A routing number must be assigned.<br>';
//		}
//		
//		// check if cc type is set
//		if (empty($_POST['check_account_num'])) {
//			$error .= 'A checking account number must be assigned.<br>';
//		}
//
	}

	if (empty($error)) {
		// process changes	
		$adv_info_tbl->get_post_vars();
	
		$adv_info_tbl->id = $_SESSION['advertiser_id'];
		// update existing data
		$adv_info_tbl->update_payment_info();
		header("Location: ".SITE_SSL_URL."advertiser_admin/");
	} else {
		$page_output = '<center>'.$error.'</center>';
	}
}

// get current level data
$adv_info_tbl->get_db_vars($_SESSION['advertiser_id']);
$adv_lvls_tbl->get_db_vars($adv_info_tbl->customer_level);

if ($adv_lvls_tbl->level_renewal_cost > 0) {
	// draw page output
	$page_output .= '<center><strong><a href="'.SITE_SSL_URL.'advertiser_admin/"><-- BACK</a></strong></center>
<form action="" method="post" name="account_signup_form"><input name="form_submit" type="hidden" value="1">
	<table align="center" class="frn_box" width="80%">
	<tr><th class="frn_header" colspan="2">Payment Options
	<script type="text/javascript">
jQuery(function(){
jQuery("#slideboxhead2").css("cursor","pointer");
jQuery(\'#slidebox2\').hide();
jQuery(\'a.slide2\').text(\'Expand\');
			
jQuery(\'#slideboxhead2\').click(function() {
        var id = jQuery(this).attr(\'id\');
		 
		if (jQuery(\'a.slide2\').text() == \'Expand\') {
			jQuery(\'a.slide2\').text(\'Collapse\');
		} else {
			jQuery(\'a.slide2\').text(\'Expand\');
		}
		
     jQuery(\'#slidebox2\').slideToggle("medium");
        // alert(id);
     return false;
});


jQuery(\'#payment_method\').change(function() {
		 
		if (jQuery(\'#payment_method\').val() == \'Check\') {
			jQuery(\'#check_payment\').css(\'display\',\'\');
			jQuery(\'#credit_card_payment\').css(\'display\',\'none\');
		}
		if (jQuery(\'#payment_method\').val() == \'Credit Card\') {
			jQuery(\'#credit_card_payment\').css(\'display\',\'\');
			jQuery(\'#check_payment\').css(\'display\',\'none\');
		}
		if (jQuery(\'#payment_method\').val() == \'\') {
			jQuery(\'#credit_card_payment\').css(\'display\',\'none\');
			jQuery(\'#check_payment\').css(\'display\',\'none\');
		}
});

';
if ($adv_info_tbl->payment_method != '') {
	if($adv_info_tbl->payment_method == 'Check') {
		$page_output .= 'jQuery(\'#check_payment\').css(\'display\',\'\');'.LB;
	} elseif ($adv_info_tbl->payment_method == 'Credit Card') {
		$page_output .= 'jQuery(\'#credit_card_payment\').css(\'display\',\'\');'.LB;
	}
}
		$page_output .= '}); 
</script></th></tr>';
	
	$field_type_array = unserialize(PAYMENT_TYPES);
		
	$type_dd = '';
	foreach ($field_type_array as $id => $title) {
		$type_dd .= '<option value="'.$title.'" '.($title == $adv_info_tbl->payment_method ? 'selected="selected"' : '').'>'.$title.'</option>'.LB; 
	}
	
	$page_output .= '<tr><td class="frn_conbox" align="right">Payment Method:</td><td class="frn_conbox" align="left"><select id="payment_method" name="payment_method">'.$type_dd.'</select></td></tr>';
	$page_output .= '</table>';
	
	$cctype_dd = '';
	$field_type_array = unserialize(CC_TYPES);
	foreach ($field_type_array as $title) {
		$cctype_dd .= '<option '.($title == $adv_info_tbl->credit_card_type ? 'selected="selected"' : '').'>'.$title.'</option>'.LB; 
	}
	
	$page_output .= '<table style="display:none" id="credit_card_payment" align="center" class="frn_box" width="80%">';
	$page_output .= '<tr><td class="frn_conbox" align="right">Credit Card Type:</td><td class="frn_conbox" align="left"><select name="credit_card_type">'.$cctype_dd.'</select></td></tr>';
	$page_output .= '<tr><td class="frn_conbox" align="right">Credit Card Number:</td><td class="frn_conbox" align="left"><input name="cc_number" type="text" size="20" value="'.$adv_info_tbl->cc_number.'"></td></tr>';
	$page_output .= '<tr><td class="frn_conbox" align="right">CVV:</td><td class="frn_conbox" align="left"><input name="cvv" type="text" size="4" value="'.$adv_info_tbl->cvv.'"></td></tr>';
	
	$set_expiration = explode("/",$adv_info_tbl->cc_exp);
	
	// print exp dd
	$years_dd = '';
	$cur_year = date("Y");
	$fut_year = $cur_year+10;
	while($cur_year <= $fut_year) {
		$years_dd .= '<option '.($set_expiration[1] == $cur_year ? 'selected' : '').'>'.$cur_year.'</option>';
		$cur_year++;
	}
	
	$month = 1;
	$months_dd = '';
	while($month <= 12) {
		$months_dd .= '<option '.($set_expiration[0] == $month ? 'selected' : '').'>'.$month.'</option>';
		$month++;
	}
	
	$page_output .= '<tr><td class="frn_conbox" align="right">Credit Card Expiration:</td><td class="frn_conbox" align="left">Month:<select name="cc_exp_month">'.$months_dd.'</select> / Year: <select name="cc_exp_year">'.$years_dd.'</select></td></tr>';
	$page_output .= '
					</table>';
					
	$page_output .= '<table style="display:none" id="check_payment" align="center" class="frn_box" width="80%">';
	$page_output .= '<tr><td class="frn_conbox" align="center" colspan="2"> All fields below are <strong>required</strong> for check payment. If you do not feel comfortable entering this data here give us a call at 1-866-283-6809 or we will contact you during the review process of your listing.</td></tr>';
	
	// build account type drop down
	$account_type_array = array(
								'pc' => 'personal',
								'bc' => 'business'
								);
	
	$accnt_type_opt = '';
	
	foreach($account_type_array as $id => $value) {
		$accnt_type_opt .= '<option value="'.$id.'"'.($id == $adv_info_tbl->check_account_type ? ' selected ' : '').'>'.$value.'</option>';
	}
	
	$page_output .= '<tr><td class="frn_conbox" align="right">Account Type:</td><td class="frn_conbox" align="left"><select name="check_account_type">'.$accnt_type_opt.'</select></td></tr>';

	$page_output .= '<tr><td class="frn_conbox" align="right">Routing Number:</td><td class="frn_conbox" align="left"><input name="check_routing_num" type="text" size="20" value="'.$adv_info_tbl->check_routing_num.'"></td></tr>';
	$page_output .= '<tr><td class="frn_conbox" align="right">Account Number:</td><td class="frn_conbox" align="left"><input name="check_account_num" type="text" size="20" value="'.$adv_info_tbl->check_account_num.'"></td></tr>';
	$page_output .= '<tr><td class="frn_conbox" align="right">Bank Name:</td><td class="frn_conbox" align="left"><input name="bank_name" type="text" size="20" value="'.$adv_info_tbl->bank_name.'"></td></tr>';

	$page_output .= '<tr><td class="frn_conbox" align="right">Bank State:</td><td class="frn_conbox" align="left"><select name="bank_state">'.gen_state_dd($adv_info_tbl->bank_state).'</select></td></tr>';
	$page_output .= '<tr><td class="frn_conbox" align="right">Drivers License Number:</td><td class="frn_conbox" align="left"><input name="drivers_license_num" type="text" size="20" value="'.$adv_info_tbl->drivers_license_num.'"></td></tr>';

	$page_output .= '<tr><td class="frn_conbox" align="right">Drivers License State:</td><td class="frn_conbox" align="left"><select name="drivers_license_state">'.gen_state_dd($adv_info_tbl->drivers_license_state).'</select></td></tr>';
	$page_output .= '</table>';
			
	$page_output .= '<table align="center" class="frn_box" width="80%"><tr><td class="frn_conbox" align="center" colspan="2"><input class="submit_btn" name="Submit" type="submit" value="Submit"></td></tr></table></form>';
} else {
	$page_output .= '<table align="center" class="frn_box" width="80%">
					<tr><th class="frn_header" colspan="2">Payment Options</th></tr>
					<tr><td class="frn_conbox" colspan="2"><center>The advertisement level you have selected does not require payment.</center></td></tr>
					</table>';
}
$page_output .= '<center><strong><a href="'.SITE_SSL_URL.'advertiser_admin/"><-- BACK</a></strong></center>';

// start output buffer
ob_start();
	
	// load template
	require(TEMPLATE_DIR.'create_account_user_level_select.php');
	
	$html = ob_get_contents();
	
ob_end_clean();

print_page($html);
?>