<?PHP
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

// if advertiser not logged in redirect to login page
if ($_SESSION['advertiser_logged_in'] != 1) header("Location: ".SITE_SSL_URL."advertiser_admin/advertiser_login.deal");

require(CLASSES_DIR.'sections/category_select.php');
$category_select_pg = new category_select_pg;

// pull existing advertiser info
$adv_info_tbl->get_db_vars($_SESSION['advertiser_id']);

$page_output = '';

// draw page output
$page_output .= '<form name="account_signup_form" id="account_signup_form">
<table width="700" align="center" class="advertiser_form">';
$page_output .= '<tr><th align="center" colspan="2">Business Information</th></tr>';
$page_output .= '<tr><td align="left" colspan="2"><table width="100%">
				<tr><td colspan="2"><input name="form_submit" type="hidden" value="1" />Hide Address From Listing <input id="hide_address" name="hide_address" type="checkbox" value="1" '.($adv_info_tbl->hide_address == 1 ? 'checked' : '').' /> </td></tr>
				<tr><td colspan="2"><input name="form_submit" type="hidden" value="1" />Allow Multiple Logins <input name="allow_multiple_logins" id="allow_multiple_logins" type="checkbox" value="1" '.($adv_info_tbl->allow_multiple_logins == 1 ? 'checked' : '').' /> (If selected you will be able to login from multiple locations at once.)</td></tr>
				<tr><td colspan="2">Company Name*<br> <input type="text" name="company_name" id="company_name" size="40" maxlength="120" value="'.$adv_info_tbl->company_name.'"></td></tr>
				<tr><td align="left" colspan="2">Website URL* (eg: www.cheaplocaldeals.com)<br> <input type="text" name="website" id="website" size="40" maxlength="160" value="'.$adv_info_tbl->website.'"></td></tr>';
$page_output .= '<tr><td>Affiliate Code<br> <input type="text" name="link_affiliate_code" id="link_affiliate_code" size="30" maxlength="120" value="'.$adv_info_tbl->link_affiliate_code.'"></td></tr>';
$page_output .= '<tr><th colspan="2" align="center">&nbsp;</th></tr>
				<tr><td colspan="2" align="left">Description (Displayed on your information page.)<br/> <textarea tabindex="11" name="customer_description" id="customer_description" rows="4" cols="80">'.$adv_info_tbl->customer_description.'</textarea></td></tr>
				<tr><td colspan="2" align="left">Products and Services (Displayed within the listing and your information page.)<br/> <textarea tabindex="12" name="products_services" id="products_services" rows="4" cols="80">'.$adv_info_tbl->products_services.'</textarea></td></tr>';
$page_output .= '</table></td></tr>';
$page_output .= '<tr id="slidebox1head"><th align="center">Categories</th><th align="right" width="30"><a href="javascript:void(0);" id="1" class="slide1" name="1">Collapse</a></th></tr>
 				<tr><td align="left" colspan="2"><script type="text/javascript">
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
</script><table id="slidebox1"><tr><td>';

$sql_query = "SELECT
				id,
				category_id
			 FROM
				advertiser_categories
			WHERE
			advertiser_id = '".$adv_info_tbl->id."';";

$rows = $dbh->queryAll($sql_query);
foreach($rows as $cur_sel) {
	$category_select_array[$cur_sel['category_id']] = 1;
}

$category_select_pg->selected_array = $category_select_array;

$page_output .= $category_select_pg->list_categories();
$page_output .= '</td></tr></table></td></tr>';
$page_output .= '<tr id="slideboxhead"><th align="center">Hours of Operation</th><th align="right" width="30"><a href="javascript:void(0);" id="1" class="slide" name="1">Collapse</a></th></tr>
				<tr><td align="left" colspan="2"><script type="text/javascript">
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

}); 
</script><table width="100%" id="slidebox"><tr>
                <td align="left">';
				
		// builds hours of operation selection
		$hours_list = unserialize(HOURS_SELECT);
		$days_array = unserialize(DAYS_ARRAY);
		if(!is_array($adv_info_tbl->hours_operation) || empty($adv_info_tbl->hours_operation)) {
			// if hours of operation settings do not exist print HOP form
			$hours_of_operation = '<input name="hours_operation[selected][type]" type="radio" value="nohours" checked /> Do Not Display Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="24hr" /> Open 24 Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="select" /> Hours Selected Below<br>';
			$hours_of_operation .= '<table>';
			$hours_of_operation .= '<tr>';
			reset($days_array);
			foreach($days_array as $value) {
				$hours_of_operation .= '<th>'.$value.'</th>';
			}
			$hours_of_operation .= '</tr><tr>';
			reset($days_array);
			reset($hours_list);
			// draw days selection
			foreach($days_array as $day_value) {
				$hours_of_operation .= '<td>';
				$hours_of_operation .= '<select id="hours_operation_selected_'.$day_value.'open" name="hours_operation[selected]['.$day_value.'open]" >';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.('9:00 AM' == $value && $day_value != 'Saturday' && $day_value != 'Sunday' ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= 'to<br>';
				$hours_of_operation .= '<select name="hours_operation[selected]['.$day_value.'close]" id="hours_operation_selected_'.$day_value.'close">';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.('5:00 PM' == $value && $day_value != 'Saturday' && $day_value != 'Sunday' ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= '</td>';
			}
			$hours_of_operation .= '</tr></table>';
		} else {
			// if HOP values are set load values into form
			if (!is_array($adv_info_tbl->hours_operation)) {
				$hours_operation = unserialize($adv_info_tbl->hours_operation);
			} else {
				$hours_operation = $adv_info_tbl->hours_operation;
			}
			$hours_of_operation = '<input name="hours_operation[selected][type]" type="radio" value="nohours" '.($hours_operation['selected']['type'] == 'nohours' ? 'checked' : '').' /> Do Not Display Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="24hr" '.($hours_operation['selected']['type'] == '24hr' ? 'checked' : '').' /> Open 24 Hours<br>';
			$hours_of_operation .= '<input name="hours_operation[selected][type]" type="radio" value="select" '.($hours_operation['selected']['type'] == 'select' ? 'checked' : '').' /> Hours Selected Below<br>';
			$hours_of_operation .= '<table>';
			$hours_of_operation .= '<tr>';
			reset($days_array);
			foreach($days_array as $value) {
				$hours_of_operation .= '<th>'.$value.'</th>';
			}
			$hours_of_operation .= '</tr><tr>';
			reset($days_array);
			reset($hours_list);
			// draw days selection
			foreach($days_array as $day_value) {
				$hours_of_operation .= '<td>';
				$hours_of_operation .= '<select id="hours_operation_selected_'.$day_value.'open" name="hours_operation[selected]['.$day_value.'open]" >';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.($hours_operation['selected'][$day_value.'open'] == $value ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= 'to<br>';
				$hours_of_operation .= '<select name="hours_operation[selected]['.$day_value.'close]" id="hours_operation_selected_'.$day_value.'close">';
				foreach($hours_list as $value) {
					$hours_of_operation .= '<option '.($hours_operation['selected'][$day_value.'close'] == $value ? 'selected="selected"' : '').' >'.$value.'</option>';
				}
				$hours_of_operation .= '</select><br>';
				$hours_of_operation .= '</td>';
			}
			$hours_of_operation .= '</tr></table>';			
		}
				
$page_output .= $hours_of_operation;
$page_output .= '</td></tr></table>
				</td></tr>';
$page_output .= '<tr id="slidebox2head"><th align="center">Accepted Payment Methods</th><th align="right" width="30"><a href="javascript:void(0);" id="1" class="slide2" name="1">Collapse</a></th></tr>
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

$payment_options = $adv_info_tbl->payment_options;

		foreach($payment_methods as $value) {
			$cur_row++;
			$payment_method_sel[] = '<td><input name="payment_options['.$value['id'].']" type="checkbox" value="1" '.($payment_options[$value['id']] == 1 ? 'checked ' : '').'/> '.$value['method'].'</td>';
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
		
$page_output .= $payment_method_sel_op;
$page_output .= '</td></tr></table>';
$page_output .= '</td></tr><tr><td align="center" colspan="2"><input id="advert_id" name="id" type="hidden" value="'.$adv_info_tbl->id.'"><input class="submit_btn" name="Submit" type="button" onclick="update_business_info_proc()" value="Submit"></td></tr></table>
                    </form>';
$session_timeout_secs = SESSION_TIMEOUT * 60;
$session_warning = "<script type=\"text/javascript\">var num = '".$session_timeout_secs."';</script>";
$page_output .= $session_warning;

echo $page_output;
?>